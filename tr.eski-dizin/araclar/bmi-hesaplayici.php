<?php
// tr/araclar/bmi-hesaplayici.php
session_start();

// Konfigürasyonu yükle
require_once '../../config/config.php';
require_once '../../config/functions.php';

// Dil ayarla
setLanguage('tr');

// Sayfa bilgileri
$pageTitle = 'BMI Hesaplayıcı - Vücut Kitle İndeksi Hesaplama | AllInToolbox';
$pageDescription = 'Ücretsiz BMI hesaplayıcı ile vücut kitle indeksinizi hesaplayın. Boy ve kilo bilgilerinizi girin, BMI değerinizi öğrenin.';
$pageKeywords = 'BMI hesaplayıcı, vücut kitle indeksi, BMI hesaplama, ideal kilo, sağlık hesaplayıcı';

// BMI hesaplama fonksiyonu
function calculateBMI($weight, $height) {
    $heightInMeters = $height / 100;
    $bmi = $weight / ($heightInMeters * $heightInMeters);
    return round($bmi, 1);
}

function getBMICategory($bmi) {
    if ($bmi < 18.5) {
        return ['category' => 'Zayıf', 'class' => 'info', 'description' => 'Normal kilodan düşük'];
    } elseif ($bmi < 25) {
        return ['category' => 'Normal', 'class' => 'success', 'description' => 'İdeal kilo aralığında'];
    } elseif ($bmi < 30) {
        return ['category' => 'Fazla Kilolu', 'class' => 'warning', 'description' => 'Normal kilodan yüksek'];
    } else {
        return ['category' => 'Obez', 'class' => 'danger', 'description' => 'Obezite riski'];
    }
}

// Form işleme
$result = null;
$error = null;

if ($_POST) {
    $weight = isset($_POST['weight']) ? floatval($_POST['weight']) : 0;
    $height = isset($_POST['height']) ? floatval($_POST['height']) : 0;
    
    if ($weight > 0 && $height > 0 && $weight <= 500 && $height >= 50 && $height <= 250) {
        $bmi = calculateBMI($weight, $height);
        $category = getBMICategory($bmi);
        
        $result = [
            'bmi' => $bmi,
            'category' => $category['category'],
            'class' => $category['class'],
            'description' => $category['description'],
            'weight' => $weight,
            'height' => $height
        ];
        
        // Analytics için
        $analyticsData = json_encode([
            'tool' => 'bmi-calculator',
            'bmi' => $bmi,
            'category' => $category['category']
        ]);
    } else {
        $error = 'Lütfen geçerli boy (50-250 cm) ve kilo (1-500 kg) değerleri girin.';
    }
}

// Header'ı dahil et
include '../../includes/header.php';
?>

<!-- Breadcrumb -->
<?php
echo generateBreadcrumb([
    ['title' => translate('home'), 'url' => '/tr/'],
    ['title' => 'Sağlık Araçları', 'url' => '/tr/category/health.php'],
    ['title' => 'BMI Hesaplayıcı']
]);
?>

<!-- Tool Container -->
<div class="tool-container">
    <div class="tool-header">
        <h1><i class="fas fa-weight text-primary"></i> BMI Hesaplayıcı</h1>
        <p class="lead">Vücut kitle indeksinizi hesaplayın ve sağlık durumunuzu değerlendirin</p>
    </div>
    
    <div class="row">
        <div class="col-lg-6">
            <!-- BMI Form -->
            <div class="tool-form">
                <form method="POST" id="bmiForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="weight" class="form-label">
                                <i class="fas fa-weight-hanging"></i> Kilo (kg)
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="weight" 
                                   name="weight" 
                                   min="1" 
                                   max="500" 
                                   step="0.1" 
                                   placeholder="Kilonuzu girin"
                                   value="<?php echo isset($_POST['weight']) ? htmlspecialchars($_POST['weight']) : ''; ?>"
                                   required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="height" class="form-label">
                                <i class="fas fa-ruler-vertical"></i> Boy (cm)
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="height" 
                                   name="height" 
                                   min="50" 
                                   max="250" 
                                   step="0.1" 
                                   placeholder="Boyunuzu girin"
                                   value="<?php echo isset($_POST['height']) ? htmlspecialchars($_POST['height']) : ''; ?>"
                                   required>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="calculateBtn">
                            <i class="fas fa-calculator"></i> BMI Hesapla
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('bmiForm').reset();">
                            <i class="fas fa-eraser"></i> Temizle
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- BMI Bilgi -->
            <div class="alert alert-info alert-custom">
                <h6><i class="fas fa-info-circle"></i> BMI Nedir?</h6>
                <p class="mb-0">Vücut Kitle İndeksi (BMI), boy ve kiloya göre hesaplanan ve genel sağlık durumunu değerlendirmek için kullanılan bir ölçüttür.</p>
            </div>
        </div>
        
        <div class="col-lg-6">
            <?php if ($result): ?>
            <!-- Sonuç -->
            <div class="tool-result">
                <h4><i class="fas fa-chart-line"></i> BMI Sonucunuz</h4>
                <div class="bmi-result-display">
                    <div class="bmi-value mb-3">
                        <span class="display-4 fw-bold"><?php echo $result['bmi']; ?></span>
                        <small class="text-muted">BMI</small>
                    </div>
                    
                    <div class="alert alert-<?php echo $result['class']; ?> mb-3">
                        <h5><i class="fas fa-tag"></i> <?php echo $result['category']; ?></h5>
                        <p class="mb-0"><?php echo $result['description']; ?></p>
                    </div>
                    
                    <div class="bmi-details">
                        <p><strong>Kilo:</strong> <?php echo $result['weight']; ?> kg</p>
                        <p><strong>Boy:</strong> <?php echo $result['height']; ?> cm</p>
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-light" onclick="AllInToolbox.utils.copyToClipboard('BMI: <?php echo $result['bmi']; ?> (<?php echo $result['category']; ?>)')">
                            <i class="fas fa-copy"></i> Sonucu Kopyala
                        </button>
                        <button class="btn btn-outline-light" onclick="window.print()">
                            <i class="fas fa-print"></i> Yazdır
                        </button>
                    </div>
                </div>
            </div>
            <?php elseif ($error): ?>
            <!-- Hata -->
            <div class="tool-result error">
                <h4><i class="fas fa-exclamation-triangle"></i> Hata</h4>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
            <?php else: ?>
            <!-- Placeholder -->
            <div class="bmi-chart">
                <h5><i class="fas fa-chart-bar"></i> BMI Kategorileri</h5>
                <div class="bmi-categories">
                    <div class="bmi-category mb-2">
                        <span class="badge bg-info me-2">Zayıf</span>
                        <span>18.5'ten az</span>
                    </div>
                    <div class="bmi-category mb-2">
                        <span class="badge bg-success me-2">Normal</span>
                        <span>18.5 - 24.9</span>
                    </div>
                    <div class="bmi-category mb-2">
                        <span class="badge bg-warning me-2">Fazla Kilolu</span>
                        <span>25.0 - 29.9</span>
                    </div>
                    <div class="bmi-category mb-2">
                        <span class="badge bg-danger me-2">Obez</span>
                        <span>30.0 ve üzeri</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Ad Space -->
<?php echo renderAdSpace('content', 'large'); ?>

<!-- BMI Information -->
<div class="row mt-5">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h3><i class="fas fa-book"></i> BMI Hakkında Detaylı Bilgi</h3>
                
                <div class="row">
                    <div class="col-lg-6">
                        <h5>BMI Nasıl Hesaplanır?</h5>
                        <p>BMI, aşağıdaki formül kullanılarak hesaplanır:</p>
                        <div class="alert alert-light">
                            <code>BMI = Kilo (kg) / (Boy (m) × Boy (m))</code>
                        </div>
                        
                        <h5>BMI Sınırları</h5>
                        <ul>
                            <li><strong>Zayıf:</strong> BMI < 18.5</li>
                            <li><strong>Normal:</strong> 18.5 ≤ BMI < 25</li>
                            <li><strong>Fazla Kilolu:</strong> 25 ≤ BMI < 30</li>
                            <li><strong>Obez:</strong> BMI ≥ 30</li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-6">
                        <h5>Önemli Notlar</h5>
                        <ul>
                            <li>BMI genel bir göstergedir ve her birey için uygun olmayabilir</li>
                            <li>Yaş, cinsiyet ve kas kütlesi BMI'yi etkileyebilir</li>
                            <li>Sporcular için BMI yanıltıcı olabilir</li>
                            <li>Sağlık durumunuz için doktorunuza danışın</li>
                        </ul>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Uyarı:</strong> Bu hesaplayıcı sadece bilgi amaçlıdır. Sağlık konularında mutlaka doktorunuza danışın.
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
        <h4><i class="fas fa-link"></i> İlgili Araçlar</h4>
        <div class="row">
            <?php
            $relatedTools = ['calorie-calculator', 'age-calculator'];
            foreach ($relatedTools as $toolId):
                $toolInfo = getToolInfo($toolId, 'tr');
                if ($toolInfo):
            ?>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo $toolInfo['name']; ?></h6>
                            <p class="card-text"><?php echo $toolInfo['description']; ?></p>
                            <a href="<?php echo $toolInfo['url']; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-arrow-right"></i> Kullan
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

<script>
// BMI Calculator specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bmiForm');
    const calculateBtn = document.getElementById('calculateBtn');
    
    // Track tool usage
    AllInToolbox.analytics.trackEvent('Tool', 'View', 'BMI Calculator');
    
    // Add to recent tools
    AllInToolbox.storage.addRecentTool('bmi-calculator', 'BMI Hesaplayıcı', '/tr/araclar/bmi-hesaplayici.php');
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const weight = parseFloat(document.getElementById('weight').value);
        const height = parseFloat(document.getElementById('height').value);
        
        if (weight && height) {
            AllInToolbox.utils.showLoading(calculateBtn);
            AllInToolbox.analytics.trackToolUsage('BMI Calculator');
            
            // Simulate processing time
            setTimeout(() => {
                AllInToolbox.utils.showLoading(calculateBtn, false);
            }, 1000);
        }
    });
    
    // Real-time BMI preview (optional)
    const weightInput = document.getElementById('weight');
    const heightInput = document.getElementById('height');
    
    function updatePreview() {
        const weight = parseFloat(weightInput.value);
        const height = parseFloat(heightInput.value);
        
        if (weight && height && weight > 0 && height > 0) {
            const bmi = weight / Math.pow(height / 100, 2);
            console.log('Preview BMI:', bmi.toFixed(1));
        }
    }
    
    weightInput.addEventListener('input', AllInToolbox.utils.debounce(updatePreview, 500));
    heightInput.addEventListener('input', AllInToolbox.utils.debounce(updatePreview, 500));
});

<?php if ($result): ?>
// Track successful calculation
AllInToolbox.analytics.trackEvent('Tool', 'Calculate', 'BMI Calculator');
<?php endif; ?>
</script>

<?php include '../../includes/footer.php'; ?>