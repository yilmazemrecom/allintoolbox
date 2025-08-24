<?php
// tools/webp-to-jpeg-converter.php
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

if (file_exists('../config/url-helpers.php')) {
    require_once '../config/url-helpers.php';
}

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

$pageTitle = ($currentLang === 'tr') ? 'WebP to JPEG Dönüştürücü' : 'WebP to JPEG Converter';
$pageDescription = ($currentLang === 'tr') ? 
    'WebP görsellerini JPEG formatına ücretsiz dönüştürün. Kalite ayarlanabilir, uyumlu format.' :
    'Convert WebP images to JPEG format for free. Adjustable quality, compatible format.';

function convertWebpToJpeg($sourceFile, $targetFile, $quality = 90) {
    error_log("convertWebpToJpeg called with source: $sourceFile, target: $targetFile, quality: $quality");
    
    if (!file_exists($sourceFile)) {
        error_log("Source file does not exist: $sourceFile");
        return false;
    }
    
    if (!extension_loaded('gd')) {
        error_log("GD extension is not loaded");
        return false;
    }
    
    if (!function_exists('imagecreatefromwebp')) {
        error_log("WebP support not available in GD");
        return false;
    }
    
    $sourceImage = imagecreatefromwebp($sourceFile);
    
    if (!$sourceImage) {
        error_log("Failed to create image from WebP: $sourceFile");
        return false;
    }
    
    // WebP şeffaflığını beyaz arka planla değiştir
    $width = imagesx($sourceImage);
    $height = imagesy($sourceImage);
    $jpegImage = imagecreatetruecolor($width, $height);
    
    $white = imagecolorallocate($jpegImage, 255, 255, 255);
    imagefill($jpegImage, 0, 0, $white);
    
    imagecopy($jpegImage, $sourceImage, 0, 0, 0, 0, $width, $height);
    
    $result = imagejpeg($jpegImage, $targetFile, $quality);
    
    if ($result) {
        error_log("JPEG saved successfully to: $targetFile");
    } else {
        error_log("Failed to save JPEG to: $targetFile");
    }
    
    imagedestroy($sourceImage);
    imagedestroy($jpegImage);
    
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['webp_file'])) {
    $uploadedFile = $_FILES['webp_file'];
    $quality = intval($_POST['quality'] ?? 90);
    $quality = max(10, min(100, $quality));
    
    if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
        $error = ($currentLang === 'tr') ? 'Dosya yükleme hatası: ' . $uploadedFile['error'] : 'File upload error: ' . $uploadedFile['error'];
    } else if (empty($uploadedFile['tmp_name']) || !is_uploaded_file($uploadedFile['tmp_name'])) {
        $error = ($currentLang === 'tr') ? 'Geçersiz dosya yüklemesi!' : 'Invalid file upload!';
    } else {
        $allowedTypes = ['image/webp'];
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedType = finfo_file($finfo, $uploadedFile['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($detectedType, $allowedTypes)) {
            $error = ($currentLang === 'tr') ? 'Lütfen sadece WebP dosyası yükleyin! (Algılanan tip: ' . $detectedType . ')' : 'Please upload only WebP files! (Detected type: ' . $detectedType . ')';
        } else {
            $maxSize = 5 * 1024 * 1024;
            if ($uploadedFile['size'] > $maxSize) {
                $error = ($currentLang === 'tr') ? 'Dosya boyutu çok büyük! (Max: 5MB)' : 'File size too large! (Max: 5MB)';
            } else {
                if (!function_exists('imagecreatefromwebp')) {
                    $error = ($currentLang === 'tr') ? 'WebP desteği bu sunucuda bulunmuyor!' : 'WebP support not available on this server!';
                } else {
                    $timestamp = time();
                    $originalName = pathinfo($uploadedFile['name'], PATHINFO_FILENAME);
                    $sourceFileName = $timestamp . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName) . '.webp';
                    $targetFileName = $timestamp . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName) . '.jpg';
                    
                    $sourcePath = $uploadDir . $sourceFileName;
                    $targetPath = $uploadDir . $targetFileName;
                    
                    if (move_uploaded_file($uploadedFile['tmp_name'], $sourcePath)) {
                        if (convertWebpToJpeg($sourcePath, $targetPath, $quality)) {
                            $result = [
                                'original_name' => $uploadedFile['name'],
                                'original_size' => $uploadedFile['size'],
                                'target_name' => $targetFileName,
                                'target_size' => filesize($targetPath),
                                'download_url' => '../uploads/' . $targetFileName,
                                'quality' => $quality
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
                        $error = ($currentLang === 'tr') ? 'Dosya yükleme başarısız!' : 'File upload failed!';
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

        <div class="tool-container">
            <div class="tool-header">
                <h1><i class="fas fa-exchange-alt text-primary"></i> <?php echo $pageTitle; ?></h1>
                <p class="lead"><?php echo $pageDescription; ?></p>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <div class="tool-form">
                        <form method="POST" enctype="multipart/form-data" id="converterForm">
                            <div class="mb-3">
                                <label for="webp_file" class="form-label">
                                    <i class="fas fa-upload"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'WebP Dosyası Seçin' : 'Select WebP File'; ?>
                                </label>
                                <input type="file" class="form-control" id="webp_file" name="webp_file" 
                                       accept=".webp,image/webp" required>
                            </div>

                            <div class="mb-3">
                                <label for="quality" class="form-label">
                                    <i class="fas fa-sliders-h"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'JPEG Kalitesi (%)' : 'JPEG Quality (%)'; ?>
                                </label>
                                <input type="range" class="form-range" id="quality" name="quality" 
                                       min="10" max="100" value="90" 
                                       oninput="document.getElementById('qualityValue').textContent = this.value">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted"><?php echo ($currentLang === 'tr') ? 'Düşük' : 'Low'; ?></small>
                                    <span id="qualityValue" class="badge bg-primary">90</span>
                                    <small class="text-muted"><?php echo ($currentLang === 'tr') ? 'Yüksek' : 'High'; ?></small>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-exchange-alt"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'JPEG\'e Dönüştür' : 'Convert to JPEG'; ?>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                    <i class="fas fa-eraser"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Temizle' : 'Clear'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <?php if ($result): ?>
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
                            <div class="mt-2">
                                <small class="text-info">
                                    <?php echo ($currentLang === 'tr') ? 'Kalite: ' : 'Quality: '; ?>
                                    <strong><?php echo $result['quality']; ?>%</strong>
                                </small>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-3">
                            <a href="<?php echo $result['download_url']; ?>" 
                               class="btn btn-success" 
                               download="<?php echo $result['target_name']; ?>">
                                <i class="fas fa-download"></i> 
                                <?php echo ($currentLang === 'tr') ? 'JPEG Dosyasını İndir' : 'Download JPEG File'; ?>
                            </a>
                        </div>
                    </div>
                    
                    <?php elseif ($error): ?>
                    <div class="tool-result error">
                        <h4><i class="fas fa-exclamation-triangle"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Hata' : 'Error'; ?>
                        </h4>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                    <?php else: ?>
                    <div class="card">
                        <div class="card-body">
                            <h5><i class="fas fa-info-circle"></i> 
                                <?php echo ($currentLang === 'tr') ? 'WebP to JPEG' : 'WebP to JPEG'; ?>
                            </h5>
                            <p><?php echo ($currentLang === 'tr') ? 
                                'Modern WebP formatından evrensel JPEG formatına dönüştürün. Tüm platformlarda uyumludur.' :
                                'Convert from modern WebP format to universal JPEG format. Compatible with all platforms.'; ?>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function resetForm() {
    document.getElementById('converterForm').reset();
    document.getElementById('qualityValue').textContent = '90';
}
</script>

<?php include '../includes/footer.php'; ?>