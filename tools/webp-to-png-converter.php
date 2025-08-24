<?php
// tools/webp-to-png-converter.php
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

if (file_exists('../config/url-helpers.php')) {
    require_once '../config/url-helpers.php';
}

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

$pageTitle = ($currentLang === 'tr') ? 'WebP to PNG Dönüştürücü' : 'WebP to PNG Converter';
$pageDescription = ($currentLang === 'tr') ? 
    'WebP görsellerini PNG formatına ücretsiz dönüştürün. Şeffaflık korunur, kalite kaybı olmaz.' :
    'Convert WebP images to PNG format for free. Transparency preserved, no quality loss.';

function convertWebpToPng($sourceFile, $targetFile) {
    error_log("convertWebpToPng called with source: $sourceFile, target: $targetFile");
    
    if (!file_exists($sourceFile)) {
        error_log("Source file does not exist: $sourceFile");
        return false;
    }
    
    if (!extension_loaded('gd')) {
        error_log("GD extension is not loaded");
        return false;
    }
    
    // WebP desteği kontrolü
    if (!function_exists('imagecreatefromwebp')) {
        error_log("WebP support not available in GD");
        return false;
    }
    
    $sourceImage = imagecreatefromwebp($sourceFile);
    
    if (!$sourceImage) {
        error_log("Failed to create image from WebP: $sourceFile");
        return false;
    }
    
    // PNG için alfa kanalını etkinleştir (şeffaflık korunur)
    imagealphablending($sourceImage, false);
    imagesavealpha($sourceImage, true);
    
    $result = imagepng($sourceImage, $targetFile);
    
    if ($result) {
        error_log("PNG saved successfully to: $targetFile");
    } else {
        error_log("Failed to save PNG to: $targetFile");
    }
    
    imagedestroy($sourceImage);
    
    return $result;
}

$result = null;
$error = null;
$uploadDir = '../uploads/';

if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        $error = ($currentLang === 'tr') ? 'Upload dizini oluşturulamadı!' : 'Cannot create upload directory!';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received");
    error_log("FILES: " . print_r($_FILES, true));
    
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        echo "<!-- DEBUG: POST received -->";
        echo "<!-- FILES: " . htmlspecialchars(print_r($_FILES, true)) . " -->";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['webp_file'])) {
    $uploadedFile = $_FILES['webp_file'];
    
    error_log("File upload attempt: " . print_r($uploadedFile, true));
    
    if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
        $error = ($currentLang === 'tr') ? 'Dosya yükleme hatası: ' . $uploadedFile['error'] : 'File upload error: ' . $uploadedFile['error'];
    } else if (empty($uploadedFile['tmp_name']) || !is_uploaded_file($uploadedFile['tmp_name'])) {
        $error = ($currentLang === 'tr') ? 'Geçersiz dosya yüklemesi!' : 'Invalid file upload!';
    } else {
        $allowedTypes = ['image/webp'];
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedType = finfo_file($finfo, $uploadedFile['tmp_name']);
        finfo_close($finfo);
        
        error_log("Detected MIME type: " . $detectedType);
        
        if (!in_array($detectedType, $allowedTypes)) {
            $error = ($currentLang === 'tr') ? 'Lütfen sadece WebP dosyası yükleyin! (Algılanan tip: ' . $detectedType . ')' : 'Please upload only WebP files! (Detected type: ' . $detectedType . ')';
        } else {
            $maxSize = 5 * 1024 * 1024;
            if ($uploadedFile['size'] > $maxSize) {
                $error = ($currentLang === 'tr') ? 'Dosya boyutu çok büyük! (Max: 5MB)' : 'File size too large! (Max: 5MB)';
            } else {
                // WebP desteği kontrolü
                if (!function_exists('imagecreatefromwebp')) {
                    $error = ($currentLang === 'tr') ? 'WebP desteği bu sunucuda bulunmuyor!' : 'WebP support not available on this server!';
                } else {
                    $timestamp = time();
                    $originalName = pathinfo($uploadedFile['name'], PATHINFO_FILENAME);
                    $sourceFileName = $timestamp . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName) . '.webp';
                    $targetFileName = $timestamp . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName) . '.png';
                    
                    $sourcePath = $uploadDir . $sourceFileName;
                    $targetPath = $uploadDir . $targetFileName;
                    
                    error_log("Moving file from " . $uploadedFile['tmp_name'] . " to " . $sourcePath);
                    if (move_uploaded_file($uploadedFile['tmp_name'], $sourcePath)) {
                        error_log("File moved successfully");
                        error_log("Starting conversion from $sourcePath to $targetPath");
                        if (convertWebpToPng($sourcePath, $targetPath)) {
                            $result = [
                                'original_name' => $uploadedFile['name'],
                                'original_size' => $uploadedFile['size'],
                                'source_path' => $sourcePath,
                                'target_path' => $targetPath,
                                'target_name' => $targetFileName,
                                'target_size' => filesize($targetPath),
                                'download_url' => '../uploads/' . $targetFileName
                            ];
                            
                            if (file_exists($sourcePath)) {
                                unlink($sourcePath);
                            }
                        } else {
                            $error = ($currentLang === 'tr') ? 'Dönüştürme işlemi başarısız!' : 'Conversion failed!';
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
                                <label for="webp_file" class="form-label">
                                    <i class="fas fa-upload"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'WebP Dosyası Seçin' : 'Select WebP File'; ?>
                                </label>
                                <input type="file" class="form-control" id="webp_file" name="webp_file" 
                                       accept=".webp,image/webp" required>
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
                            <?php echo ($currentLang === 'tr') ? 'WebP to PNG Dönüştürücü Hakkında' : 'About WebP to PNG Converter'; ?>
                        </h6>
                        <p class="mb-0">
                            <?php echo ($currentLang === 'tr') ? 
                                'WebP dosyalarınızı PNG formatına dönüştürün. Şeffaflık korunur, kayıpsız dönüşüm.' :
                                'Convert your WebP files to PNG format. Transparency is preserved, lossless conversion.'; ?>
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
                                <?php echo ($currentLang === 'tr') ? 'WebP vs PNG' : 'WebP vs PNG'; ?>
                            </h5>
                            <div class="row">
                                <div class="col-6">
                                    <h6 class="text-primary">WebP</h6>
                                    <ul class="small">
                                        <li><?php echo ($currentLang === 'tr') ? 'Çok küçük dosya boyutu' : 'Very small file size'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 'Modern format' : 'Modern format'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 'Şeffaflık desteği' : 'Transparency support'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 'Sınırlı tarayıcı desteği' : 'Limited browser support'; ?></li>
                                    </ul>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-success">PNG</h6>
                                    <ul class="small">
                                        <li><?php echo ($currentLang === 'tr') ? 'Büyük dosya boyutu' : 'Large file size'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 'Evrensel destek' : 'Universal support'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 'Şeffaflık desteği' : 'Transparency support'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 'Kayıpsız kalite' : 'Lossless quality'; ?></li>
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
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('converterForm');
    const fileInput = document.getElementById('webp_file');
    const convertBtn = document.getElementById('convertBtn');
    const previewContainer = document.getElementById('file-preview');
    const previewImage = document.getElementById('preview-image');
    const fileInfo = document.getElementById('file-info');
    const currentLang = '<?php echo $currentLang; ?>';
    
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Tool', 'View', 'WebP to PNG Converter');
        const toolName = currentLang === 'tr' ? 'WebP to PNG Dönüştürücü' : 'WebP to PNG Converter';
        const toolUrl = '/tools/webp-to-png-converter.php?lang=' + currentLang;
        AllInToolbox.storage.addRecentTool('webp-to-png-converter', toolName, toolUrl);
    }
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (!file.type.match(/^image\/webp$/)) {
                alert(currentLang === 'tr' ? 'Lütfen sadece WebP dosyası seçin!' : 'Please select only WebP files!');
                this.value = '';
                previewContainer.style.display = 'none';
                return;
            }
            
            if (file.size > 5 * 1024 * 1024) {
                alert(currentLang === 'tr' ? 'Dosya boyutu çok büyük! (Max: 5MB)' : 'File size too large! (Max: 5MB)');
                this.value = '';
                previewContainer.style.display = 'none';
                return;
            }
            
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
    
    form.addEventListener('submit', function(e) {
        if (!fileInput.files[0]) {
            e.preventDefault();
            alert(currentLang === 'tr' ? 'Lütfen bir dosya seçin!' : 'Please select a file!');
            return;
        }
        
        if (typeof AllInToolbox !== 'undefined') {
            AllInToolbox.utils.showLoading(convertBtn);
            AllInToolbox.analytics.trackToolUsage('WebP to PNG Converter');
        }
    });
    
    window.formatBytes = function(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    };
});

function resetForm() {
    document.getElementById('converterForm').reset();
    document.getElementById('file-preview').style.display = 'none';
}

<?php if ($result): ?>
if (typeof AllInToolbox !== 'undefined') {
    AllInToolbox.analytics.trackEvent('Tool', 'Convert', 'WebP to PNG Success');
}
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>