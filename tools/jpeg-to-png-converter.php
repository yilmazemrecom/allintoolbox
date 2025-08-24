<?php
// tools/jpeg-to-png-converter.php
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

// URL helpers'ı yükle
if (file_exists('../config/url-helpers.php')) {
    require_once '../config/url-helpers.php';
}

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Sayfa meta bilgileri
$pageTitle = ($currentLang === 'tr') ? 'JPEG to PNG Dönüştürücü' : 'JPEG to PNG Converter';
$pageDescription = ($currentLang === 'tr') ? 
    'JPEG görsellerini PNG formatına ücretsiz dönüştürün. Kalite kaybı olmadan hızlı ve güvenli dönüşüm.' :
    'Convert JPEG images to PNG format for free. Fast and secure conversion without quality loss.';

// Dönüştürme fonksiyonu
function convertJpegToPng($sourceFile, $targetFile) {
    error_log("convertJpegToPng called with source: $sourceFile, target: $targetFile");
    
    // Kaynak dosyanın varlığını kontrol et
    if (!file_exists($sourceFile)) {
        error_log("Source file does not exist: $sourceFile");
        return false;
    }
    
    // GD extension kontrolü
    if (!extension_loaded('gd')) {
        error_log("GD extension is not loaded");
        return false;
    }
    
    // JPEG dosyasını yükle
    $sourceImage = imagecreatefromjpeg($sourceFile);
    
    if (!$sourceImage) {
        error_log("Failed to create image from JPEG: $sourceFile");
        return false;
    }
    
    // PNG olarak kaydet
    $result = imagepng($sourceImage, $targetFile);
    
    if ($result) {
        error_log("PNG saved successfully to: $targetFile");
    } else {
        error_log("Failed to save PNG to: $targetFile");
    }
    
    // Belleği temizle
    imagedestroy($sourceImage);
    
    return $result;
}

// Dosya yükleme ve dönüştürme işlemi
$result = null;
$error = null;
$uploadDir = '../uploads/';

// Upload dizini yoksa oluştur
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        $error = ($currentLang === 'tr') ? 'Upload dizini oluşturulamadı!' : 'Cannot create upload directory!';
    }
}

// Debug POST ve FILES
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received");
    error_log("FILES: " . print_r($_FILES, true));
    
    // Geliştirme modunda ekranda da göster
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        echo "<!-- DEBUG: POST received -->";
        echo "<!-- FILES: " . htmlspecialchars(print_r($_FILES, true)) . " -->";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['jpeg_file'])) {
    $uploadedFile = $_FILES['jpeg_file'];
    
    // Debug bilgisi ekle
    error_log("File upload attempt: " . print_r($uploadedFile, true));
    
    // Dosya yükleme hatalarını kontrol et
    if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
        $error = ($currentLang === 'tr') ? 'Dosya yükleme hatası: ' . $uploadedFile['error'] : 'File upload error: ' . $uploadedFile['error'];
    } else if (empty($uploadedFile['tmp_name']) || !is_uploaded_file($uploadedFile['tmp_name'])) {
        $error = ($currentLang === 'tr') ? 'Geçersiz dosya yüklemesi!' : 'Invalid file upload!';
    } else {
        // Dosya türünü kontrol et
        $allowedTypes = ['image/jpeg', 'image/jpg'];
        
        // MIME type kontrolü
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedType = finfo_file($finfo, $uploadedFile['tmp_name']);
        finfo_close($finfo);
        
        error_log("Detected MIME type: " . $detectedType);
        error_log("Uploaded file type: " . $uploadedFile['type']);
        
        if (!in_array($detectedType, $allowedTypes)) {
            $error = ($currentLang === 'tr') ? 'Lütfen sadece JPEG dosyası yükleyin! (Algılanan tip: ' . $detectedType . ')' : 'Please upload only JPEG files! (Detected type: ' . $detectedType . ')';
        } else {
            // Dosya boyutunu kontrol et (max 5MB)
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($uploadedFile['size'] > $maxSize) {
                $error = ($currentLang === 'tr') ? 'Dosya boyutu çok büyük! (Max: 5MB)' : 'File size too large! (Max: 5MB)';
            } else {
                // Benzersiz dosya adları oluştur
                $timestamp = time();
                $originalName = pathinfo($uploadedFile['name'], PATHINFO_FILENAME);
                $sourceFileName = $timestamp . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName) . '.jpg';
                $targetFileName = $timestamp . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName) . '.png';
                
                $sourcePath = $uploadDir . $sourceFileName;
                $targetPath = $uploadDir . $targetFileName;
                
                // Dosyayı geçici dizine taşı
                error_log("Moving file from " . $uploadedFile['tmp_name'] . " to " . $sourcePath);
                if (move_uploaded_file($uploadedFile['tmp_name'], $sourcePath)) {
                    error_log("File moved successfully");
                    // JPEG'i PNG'ye dönüştür
                    error_log("Starting conversion from $sourcePath to $targetPath");
                    if (convertJpegToPng($sourcePath, $targetPath)) {
                        $result = [
                            'original_name' => $uploadedFile['name'],
                            'original_size' => $uploadedFile['size'],
                            'source_path' => $sourcePath,
                            'target_path' => $targetPath,
                            'target_name' => $targetFileName,
                            'target_size' => filesize($targetPath),
                            'download_url' => '../uploads/' . $targetFileName
                        ];
                        
                        // Orijinal dosyayı sil (opsiyonel)
                        if (file_exists($sourcePath)) {
                            unlink($sourcePath);
                        }
                    } else {
                        $error = ($currentLang === 'tr') ? 'Dönüştürme işlemi başarısız!' : 'Conversion failed!';
                        // Hatalı dosyayı temizle
                        if (file_exists($sourcePath)) {
                            unlink($sourcePath);
                        }
                    }
                } else {
                    $error = ($currentLang === 'tr') ? 'Dosya yükleme başarısız! Hedef: ' . $sourcePath : 'File upload failed! Target: ' . $sourcePath;
                    error_log("Failed to move uploaded file to: " . $sourcePath);
                }
            }
        }
    }
}

// Otomatik temizleme
require_once '../scripts/auto-cleanup.php';

include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">

        <!-- Breadcrumb -->
        <?php
        $breadcrumbItems = [
            ['title' => __('breadcrumb_home'), 'url' => '/' . $currentLang . '/'],
            ['title' => __('category_image'), 'url' => function_exists('getCategoryCleanUrl') ? getCategoryCleanUrl('image', $currentLang) : '/pages/category.php?category=image&lang=' . $currentLang],
            ['title' => $pageTitle]
        ];
        echo generateBreadcrumb($breadcrumbItems);
        ?>

        <!-- Tool Container -->
        <div class="tool-container">
            <div class="tool-header">
                <h1><i class="fas fa-exchange-alt text-primary"></i> <?php echo $pageTitle; ?></h1>
                <p class="lead"><?php echo $pageDescription; ?></p>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <!-- Upload Form -->
                    <div class="tool-form">
                        <form method="POST" enctype="multipart/form-data" id="converterForm">
                            <div class="mb-3">
                                <label for="jpeg_file" class="form-label">
                                    <i class="fas fa-upload"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'JPEG Dosyası Seçin' : 'Select JPEG File'; ?>
                                </label>
                                <input type="file" class="form-control" id="jpeg_file" name="jpeg_file" 
                                       accept=".jpg,.jpeg,image/jpeg" required>
                                <div class="form-text">
                                    <?php echo ($currentLang === 'tr') ? 'Maksimum dosya boyutu: 5MB' : 'Maximum file size: 5MB'; ?>
                                </div>
                            </div>
                            
                            <!-- File Preview -->
                            <div id="file-preview" class="mb-3" style="display: none;">
                                <label class="form-label">
                                    <?php echo ($currentLang === 'tr') ? 'Dosya Önizleme' : 'File Preview'; ?>
                                </label>
                                <div class="border rounded p-3">
                                    <img id="preview-image" src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                                    <div id="file-info" class="mt-2 small text-muted"></div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="convertBtn">
                                    <i class="fas fa-exchange-alt"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'PNG\'ye Dönüştür' : 'Convert to PNG'; ?>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                    <i class="fas fa-eraser"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Temizle' : 'Clear'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Info -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> 
                            <?php echo ($currentLang === 'tr') ? 'JPEG to PNG Dönüştürücü Hakkında' : 'About JPEG to PNG Converter'; ?>
                        </h6>
                        <p class="mb-0">
                            <?php echo ($currentLang === 'tr') ? 
                                'JPEG dosyalarınızı PNG formatına kalite kaybı olmadan dönüştürün.' :
                                'Convert your JPEG files to PNG format without quality loss.'; ?>
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <?php if ($result): ?>
                    <!-- Başarılı Dönüşüm -->
                    <div class="tool-result">
                        <h4><i class="fas fa-check-circle text-success"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Dönüşüm Tamamlandı!' : 'Conversion Completed!'; ?>
                        </h4>
                        
                        <div class="conversion-info">
                            <div class="row">
                                <div class="col-6">
                                    <strong><?php echo ($currentLang === 'tr') ? 'Orijinal:' : 'Original:'; ?></strong><br>
                                    <small><?php echo htmlspecialchars($result['original_name']); ?></small><br>
                                    <small class="text-muted"><?php echo formatFileSize($result['original_size']); ?></small>
                                </div>
                                <div class="col-6">
                                    <strong><?php echo ($currentLang === 'tr') ? 'Dönüştürülen:' : 'Converted:'; ?></strong><br>
                                    <small><?php echo htmlspecialchars($result['target_name']); ?></small><br>
                                    <small class="text-muted"><?php echo formatFileSize($result['target_size']); ?></small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-3">
                            <a href="<?php echo $result['download_url']; ?>" 
                               class="btn btn-success" 
                               download="<?php echo $result['target_name']; ?>">
                                <i class="fas fa-download"></i> 
                                <?php echo ($currentLang === 'tr') ? 'PNG Dosyasını İndir' : 'Download PNG File'; ?>
                            </a>
                        </div>
                    </div>
                    
                    <?php elseif ($error): ?>
                    <!-- Hata -->
                    <div class="tool-result error">
                        <h4><i class="fas fa-exclamation-triangle"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Hata' : 'Error'; ?>
                        </h4>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                    <?php else: ?>
                    <!-- Format Karşılaştırma -->
                    <div class="card">
                        <div class="card-body">
                            <h5><i class="fas fa-balance-scale"></i> 
                                <?php echo ($currentLang === 'tr') ? 'JPEG vs PNG' : 'JPEG vs PNG'; ?>
                            </h5>
                            <div class="row">
                                <div class="col-6">
                                    <h6 class="text-primary">JPEG</h6>
                                    <ul class="small">
                                        <li><?php echo ($currentLang === 'tr') ? 'Küçük dosya boyutu' : 'Small file size'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 'Kayıplı sıkıştırma' : 'Lossy compression'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 'Şeffaflık yok' : 'No transparency'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 'Fotoğraflar için ideal' : 'Ideal for photos'; ?></li>
                                    </ul>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-success">PNG</h6>
                                    <ul class="small">
                                        <li><?php echo ($currentLang === 'tr') ? 'Büyük dosya boyutu' : 'Larger file size'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 'Kayıpsız sıkıştırma' : 'Lossless compression'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 'Şeffaflık desteği' : 'Transparency support'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 'Grafikler için ideal' : 'Ideal for graphics'; ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Ad Space -->
        <?php echo renderAdSpace('content', 'large'); ?>

        <!-- Detailed Information -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3><i class="fas fa-book"></i> 
                            <?php echo ($currentLang === 'tr') ? 'JPEG to PNG Dönüştürme Hakkında' : 'About JPEG to PNG Conversion'; ?>
                        </h3>
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Ne Zaman PNG Kullanmalı?' : 'When to Use PNG?'; ?></h5>
                                <ul>
                                    <li><?php echo ($currentLang === 'tr') ? 'Şeffaf arka plan gerektiğinde' : 'When transparent background is needed'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Kalite kaybı istemediğinizde' : 'When you want no quality loss'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Logo ve grafikler için' : 'For logos and graphics'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Web tasarımında' : 'In web design'; ?></li>
                                </ul>
                                
                                <h5><?php echo ($currentLang === 'tr') ? 'Dönüştürme Süreci' : 'Conversion Process'; ?></h5>
                                <ol>
                                    <li><?php echo ($currentLang === 'tr') ? 'JPEG dosyanızı seçin' : 'Select your JPEG file'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Dosya önizlemesini kontrol edin' : 'Check the file preview'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Dönüştür butonuna tıklayın' : 'Click convert button'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'PNG dosyasını indirin' : 'Download the PNG file'; ?></li>
                                </ol>
                            </div>
                            
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Özellikler' : 'Features'; ?></h5>
                                <ul>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Kalite kaybı olmadan dönüştürme' : 
                                        'Conversion without quality loss'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Hızlı işlem süresi' : 
                                        'Fast processing time'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        '5MB\'a kadar dosya desteği' : 
                                        'Support for files up to 5MB'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Güvenli dosya işleme' : 
                                        'Secure file processing'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Anında indirme' : 
                                        'Instant download'; ?></li>
                                </ul>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong><?php echo ($currentLang === 'tr') ? 'Not:' : 'Note:'; ?></strong>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'PNG dosyalar genellikle JPEG\'ten daha büyük boyutta olur.' :
                                        'PNG files are usually larger in size than JPEG.'; ?>
                                </div>
                                
                                <div class="alert alert-success">
                                    <i class="fas fa-shield-alt"></i>
                                    <strong><?php echo ($currentLang === 'tr') ? 'Güvenlik:' : 'Security:'; ?></strong>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Dosyalarınız işlem sonrası otomatik olarak silinir.' :
                                        'Your files are automatically deleted after processing.'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Tools -->
        <div class="row mt-4">
            <div class="col-12">
                <h4><i class="fas fa-link"></i> <?php echo __('related_tools'); ?></h4>
                <div class="row">
                <?php
                $relatedTools = ['color-converter', 'qr-code-generator'];
                foreach ($relatedTools as $toolId):
                    // Clean URLs kullan varsa
                    if (function_exists('getToolInfoWithCleanUrl')) {
                        $toolInfo = getToolInfoWithCleanUrl($toolId, $currentLang);
                    } else {
                        $toolInfo = getToolInfo($toolId, $currentLang);
                    }
                    
                    if ($toolInfo):
                ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo $toolInfo['name']; ?></h6>
                                    <p class="card-text"><?php echo $toolInfo['description']; ?></p>
                                    <a href="<?php echo $toolInfo['url']; ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-arrow-right"></i> <?php echo __('use_tool'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
// JPEG to PNG Converter JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('converterForm');
    const fileInput = document.getElementById('jpeg_file');
    const convertBtn = document.getElementById('convertBtn');
    const previewContainer = document.getElementById('file-preview');
    const previewImage = document.getElementById('preview-image');
    const fileInfo = document.getElementById('file-info');
    const currentLang = '<?php echo $currentLang; ?>';
    
    // Track tool usage
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Tool', 'View', 'JPEG to PNG Converter');
        
        // Add to recent tools
        const toolName = currentLang === 'tr' ? 'JPEG to PNG Dönüştürücü' : 'JPEG to PNG Converter';
        const toolUrl = '/tools/jpeg-to-png-converter.php?lang=' + currentLang;
        AllInToolbox.storage.addRecentTool('jpeg-to-png-converter', toolName, toolUrl);
    }
    
    // File input change event
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Dosya türü kontrolü
            if (!file.type.match(/^image\/jpe?g$/)) {
                alert(currentLang === 'tr' ? 'Lütfen sadece JPEG dosyası seçin!' : 'Please select only JPEG files!');
                this.value = '';
                previewContainer.style.display = 'none';
                return;
            }
            
            // Dosya boyutu kontrolü (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert(currentLang === 'tr' ? 'Dosya boyutu çok büyük! (Max: 5MB)' : 'File size too large! (Max: 5MB)');
                this.value = '';
                previewContainer.style.display = 'none';
                return;
            }
            
            // Önizleme oluştur
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                fileInfo.innerHTML = 
                    '<strong>' + file.name + '</strong><br>' +
                    (currentLang === 'tr' ? 'Boyut: ' : 'Size: ') + formatBytes(file.size) + '<br>' +
                    (currentLang === 'tr' ? 'Tür: ' : 'Type: ') + file.type;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        if (!fileInput.files[0]) {
            e.preventDefault();
            alert(currentLang === 'tr' ? 'Lütfen bir dosya seçin!' : 'Please select a file!');
            return;
        }
        
        if (typeof AllInToolbox !== 'undefined') {
            AllInToolbox.utils.showLoading(convertBtn);
            AllInToolbox.analytics.trackToolUsage('JPEG to PNG Converter');
        }
    });
    
    // Format bytes function
    window.formatBytes = function(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    };
});

// Reset form function
function resetForm() {
    document.getElementById('converterForm').reset();
    document.getElementById('file-preview').style.display = 'none';
}

<?php if ($result): ?>
// Track successful conversion
if (typeof AllInToolbox !== 'undefined') {
    AllInToolbox.analytics.trackEvent('Tool', 'Convert', 'JPEG to PNG Success');
}
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>