<?php
// tools/bmi-calculator.php - Unified BMI Calculator
session_start();

// Konfigürasyonu yükle
require_once '../config/config.php';
require_once '../config/functions.php';

// URL'den dil al veya algıla
$currentLang = detectLanguage();

// URL yeniden yönlendirme için kontrol
$requestUri = $_SERVER['REQUEST_URI'];
if (!preg_match('/^\/(tr|en)\//', $requestUri)) {
    header('Location: /' . $currentLang . '/tools/bmi-calculator', true, 301);
    exit;
}

// Dil ayarla
setLanguage($currentLang);

// Dil'e göre URL'leri ayarla
$toolUrls = [
    'tr' => '/tr/araclar/bmi-hesaplayici',
    'en' => '/en/tools/bmi-calculator'
];

// Sayfa bilgileri - dile göre
$pageInfo = [
    'tr' => [
        'title' => 'BMI Hesaplayıcı - Vücut Kitle İndeksi Hesaplama | AllInToolbox',
        'description' => 'Ücretsiz BMI hesaplayıcı ile vücut kitle indeksinizi hesaplayın. Boy ve kilo bilgilerinizi girin, BMI değerinizi öğrenin.',
        'keywords' => 'BMI hesaplayıcı, vücut kitle indeksi, BMI hesaplama, ideal kilo, sağlık hesaplayıcı'
    ],
    'en' => [
        'title' => 'BMI Calculator - Body Mass Index Calculator | AllInToolbox',
        'description' => 'Free BMI calculator to calculate your body mass index. Enter your height and weight to get your BMI value.',
        'keywords' => 'BMI calculator, body mass index, BMI calculation, ideal weight, health calculator'
    ]
];

// Sayfa meta bilgilerini ayarla
$pageTitle = $pageInfo[$currentLang]['title'];
$pageDescription = $pageInfo[$currentLang]['description'];
$pageKeywords = $pageInfo[$currentLang]['keywords'];

// İçerik metinleri
$content = [
    'tr' => [
        'page_title' => 'BMI Hesaplayıcı',
        'page_subtitle' => 'Vücut kitle indeksinizi hesaplayın ve sağlık durumunuzu değerlendirin',
        'weight_label' => 'Kilo (kg)',
        'weight_placeholder' => 'Kilonuzu girin',
        'height_label' => 'Boy (cm)',
        'height_placeholder' => 'Boyunuzu girin',
        'calculate_btn' => 'BMI Hesapla',
        'clear_btn' => 'Temizle',
        'result_title' => 'BMI Sonucunuz',
        'copy_result' => 'Sonucu Kopyala',
        'print_result' => 'Yazdır',
        'what_is_bmi' => 'BMI Nedir?',
        'bmi_description' => 'Vücut Kitle İndeksi (BMI), boy ve kiloya göre hesaplanan ve genel sağlık durumunu değerlendirmek için kullanılan bir ölçüttür.',
        'bmi_categories' => 'BMI Kategorileri',
        'categories' => [
            'underweight' => 'Zayıf',
            'normal' => 'Normal',
            'overweight' => 'Fazla Kilolu',
            'obese' => 'Obez'
        ],
        'category_descriptions' => [
            'underweight' => 'Normal kilodan düşük',
            'normal' => 'İdeal kilo aralığında',
            'overweight' => 'Normal kilodan yüksek',
            'obese' => 'Obezite riski'
        ],
        'detailed_info' => 'BMI Hakkında Detaylı Bilgi',
        'how_calculated' => 'BMI Nasıl Hesaplanır?',
        'formula_text' => 'BMI, aşağıdaki formül kullanılarak hesaplanır:',
        'formula' => 'BMI = Kilo (kg) / (Boy (m) × Boy (m))',
        'bmi_ranges' => 'BMI Sınırları',
        'important_notes' => 'Önemli Notlar',
        'notes' => [
            'BMI genel bir göstergedir ve her birey için uygun olmayabilir',
            'Yaş, cinsiyet ve kas kütlesi BMI\'yi etkileyebilir',
            'Sporcular için BMI yanıltıcı olabilir',
            'Sağlık durumunuz için doktorunuza danışın'
        ],
        'warning' => 'Bu hesaplayıcı sadece bilgi amaçlıdır. Sağlık konularında mutlaka doktorunuza danışın.',
        'related_tools' => 'İlgili Araçlar',
        'breadcrumb' => [
            'home' => 'Ana Sayfa',
            'health_tools' => 'Sağlık Araçları',
            'bmi_calculator' => 'BMI Hesaplayıcı'
        ],
        'error_message' => 'Lütfen geçerli boy (50-250 cm) ve kilo (1-500 kg) değerleri girin.'
    ],
    'en' => [
        'page_title' => 'BMI Calculator',
        'page_subtitle' => 'Calculate your body mass index and assess your health status',
        'weight_label' => 'Weight (kg)',
        'weight_placeholder' => 'Enter your weight',
        'height_label' => 'Height (cm)',
        'height_placeholder' => 'Enter your height',
        'calculate_btn' => 'Calculate BMI',
        'clear_btn' => 'Clear',
        'result_title' => 'Your BMI Result',
        'copy_result' => 'Copy Result',
        'print_result' => 'Print',
        'what_is_bmi' => 'What is BMI?',
        'bmi_description' => 'Body Mass Index (BMI) is a measure calculated from height and weight used to assess general health status.',
        'bmi_categories' => 'BMI Categories',
        'categories' => [
            'underweight' => 'Underweight',
            'normal' => 'Normal',
            'overweight' => 'Overweight',
            'obese' => 'Obese'
        ],
        'category_descriptions' => [
            'underweight' => 'Below normal weight',
            'normal' => 'Ideal weight range',
            'overweight' => 'Above normal weight',
            'obese' => 'Obesity risk'
        ],
        'detailed_info' => 'Detailed Information About BMI',
        'how_calculated' => 'How is BMI Calculated?',
        'formula_text' => 'BMI is calculated using the following formula:',
        'formula' => 'BMI = Weight (kg) / (Height (m) × Height (m))',
        'bmi_ranges' => 'BMI Ranges',
        'important_notes' => 'Important Notes',
        'notes' => [
            'BMI is a general indicator and may not be suitable for every individual',
            'Age, gender and muscle mass can affect BMI',
            'BMI can be misleading for athletes',
            'Consult your doctor about your health condition'
        ],
        'warning' => 'This calculator is for informational purposes only. Always consult your doctor for health matters.',
        'related_tools' => 'Related Tools',
        'breadcrumb' => [
            'home' => 'Home',
            'health_tools' => 'Health Tools',
            'bmi_calculator' => 'BMI Calculator'
        ],
        'error_message' => 'Please enter valid height (50-250 cm) and weight (1-500 kg) values.'
    ]
];

// Mevcut dil içeriğini al
$text = $content[$currentLang];

// BMI hesaplama fonksiyonu
function calculateBMI($weight, $height) {
    $heightInMeters = $height / 100;
    $bmi = $weight / ($heightInMeters * $heightInMeters);
    return round($bmi, 1);
}

function getBMICategory($bmi, $lang) {
    $categories = [
        'tr' => [
            'underweight' => ['category' => 'Zayıf', 'class' => 'info', 'description' => 'Normal kilodan düşük'],
            'normal' => ['category' => 'Normal', 'class' => 'success', 'description' => 'İdeal kilo aralığında'],
            'overweight' => ['category' => 'Fazla Kilolu', 'class' => 'warning', 'description' => 'Normal kilodan yüksek'],
            'obese' => ['category' => 'Obez', 'class' => 'danger', 'description' => 'Obezite riski']
        ],
        'en' => [
            'underweight' => ['category' => 'Underweight', 'class' => 'info', 'description' => 'Below normal weight'],
            'normal' => ['category' => 'Normal', 'class' => 'success', 'description' => 'Ideal weight range'],
            'overweight' => ['category' => 'Overweight', 'class' => 'warning', 'description' => 'Above normal weight'],
            'obese' => ['category' => 'Obese', 'class' => 'danger', 'description' => 'Obesity risk']
        ]
    ];
    
    if ($bmi < 18.5) {
        return $categories[$lang]['underweight'];
    } elseif ($bmi < 25) {
        return $categories[$lang]['normal'];
    } elseif ($bmi < 30) {
        return $categories[$lang]['overweight'];
    } else {
        return $categories[$lang]['obese'];
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
        $category = getBMICategory($bmi, $currentLang);
        
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
        $error = $text['error_message'];
    }
}

// Header'ı dahil et
include '../includes/header.php';
?>

<!-- Breadcrumb -->
<?php
$breadcrumbItems = [
    ['title' => $text['breadcrumb']['home'], 'url' => '/' . $currentLang . '/'],
    ['title' => $text['breadcrumb']['health_tools'], 'url' => '/' . $currentLang . '/category/health.php'],
    ['title' => $text['breadcrumb']['bmi_calculator']]
];
echo generateBreadcrumb($breadcrumbItems);
?>

<!-- Tool Container -->
<div class="tool-container">
    <div class="tool-header">
        <h1><i class="fas fa-weight text-primary"></i> <?php echo $text['page_title']; ?></h1>
        <p class="lead"><?php echo $text['page_subtitle']; ?></p>
    </div>
    
    <div class="row">
        <div class="col-lg-6">
            <!-- BMI Form -->
            <div class="tool-form">
                <form method="POST" id="bmiForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="weight" class="form-label">
                                <i class="fas fa-weight-hanging"></i> <?php echo $text['weight_label']; ?>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="weight" 
                                   name="weight" 
                                   min="1" 
                                   max="500" 
                                   step="0.1" 
                                   placeholder="<?php echo $text['weight_placeholder']; ?>"
                                   value="<?php echo isset($_POST['weight']) ? htmlspecialchars($_POST['weight']) : ''; ?>"
                                   required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="height" class="form-label">
                                <i class="fas fa-ruler-vertical"></i> <?php echo $text['height_label']; ?>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="height" 
                                   name="height" 
                                   min="50" 
                                   max="250" 
                                   step="0.1" 
                                   placeholder="<?php echo $text['height_placeholder']; ?>"
                                   value="<?php echo isset($_POST['height']) ? htmlspecialchars($_POST['height']) : ''; ?>"
                                   required>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="calculateBtn">
                            <i class="fas fa-calculator"></i> <?php echo $text['calculate_btn']; ?>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('bmiForm').reset();">
                            <i class="fas fa-eraser"></i> <?php echo $text['clear_btn']; ?>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- BMI Bilgi -->
            <div class="alert alert-info alert-custom">
                <h6><i class="fas fa-info-circle"></i> <?php echo $text['what_is_bmi']; ?></h6>
                <p class="mb-0"><?php echo $text['bmi_description']; ?></p>
            </div>
        </div>
        
        <div class="col-lg-6">
            <?php if ($result): ?>
            <!-- Sonuç -->
            <div class="tool-result">
                <h4><i class="fas fa-chart-line"></i> <?php echo $text['result_title']; ?></h4>
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
                        <p><strong><?php echo $text['weight_label']; ?>:</strong> <?php echo $result['weight']; ?> kg</p>
                        <p><strong><?php echo $text['height_label']; ?>:</strong> <?php echo $result['height']; ?> cm</p>
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-light" onclick="AllInToolbox.utils.copyToClipboard('BMI: <?php echo $result['bmi']; ?> (<?php echo $result['category']; ?>)')">
                            <i class="fas fa-copy"></i> <?php echo $text['copy_result']; ?>
                        </button>
                        <button class="btn btn-outline-light" onclick="window.print()">
                            <i class="fas fa-print"></i> <?php echo $text['print_result']; ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php elseif ($error): ?>
            <!-- Hata -->
            <div class="tool-result error">
                <h4><i class="fas fa-exclamation-triangle"></i> Error</h4>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
            <?php else: ?>
            <!-- Placeholder -->
            <div class="bmi-chart">
                <h5><i class="fas fa-chart-bar"></i> <?php echo $text['bmi_categories']; ?></h5>
                <div class="bmi-categories">
                    <div class="bmi-category mb-2">
                        <span class="badge bg-info me-2"><?php echo $text['categories']['underweight']; ?></span>
                        <span>< 18.5</span>
                    </div>
                    <div class="bmi-category mb-2">
                        <span class="badge bg-success me-2"><?php echo $text['categories']['normal']; ?></span>
                        <span>18.5 - 24.9</span>
                    </div>
                    <div class="bmi-category mb-2">
                        <span class="badge bg-warning me-2"><?php echo $text['categories']['overweight']; ?></span>
                        <span>25.0 - 29.9</span>
                    </div>
                    <div class="bmi-category mb-2">
                        <span class="badge bg-danger me-2"><?php echo $text['categories']['obese']; ?></span>
                        <span>≥ 30.0</span>
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
                <h3><i class="fas fa-book"></i> <?php echo $text['detailed_info']; ?></h3>
                
                <div class="row">
                    <div class="col-lg-6">
                        <h5><?php echo $text['how_calculated']; ?></h5>
                        <p><?php echo $text['formula_text']; ?></p>
                        <div class="alert alert-light">
                            <code><?php echo $text['formula']; ?></code>
                        </div>
                        
                        <h5><?php echo $text['bmi_ranges']; ?></h5>
                        <ul>
                            <li><strong><?php echo $text['categories']['underweight']; ?>:</strong> BMI < 18.5</li>
                            <li><strong><?php echo $text['categories']['normal']; ?>:</strong> 18.5 ≤ BMI < 25</li>
                            <li><strong><?php echo $text['categories']['overweight']; ?>:</strong> 25 ≤ BMI < 30</li>
                            <li><strong><?php echo $text['categories']['obese']; ?>:</strong> BMI ≥ 30</li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-6">
                        <h5><?php echo $text['important_notes']; ?></h5>
                        <ul>
                            <?php foreach ($text['notes'] as $note): ?>
                                <li><?php echo $note; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong><?php echo ($currentLang === 'tr') ? 'Uyarı:' : 'Warning:'; ?></strong> <?php echo $text['warning']; ?>
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
        <h4><i class="fas fa-link"></i> <?php echo $text['related_tools']; ?></h4>
        <div class="row">
            <?php
            $relatedTools = ['calorie-calculator', 'age-calculator'];
            foreach ($relatedTools as $toolId):
                $toolInfo = getToolInfo($toolId, $currentLang);
                if ($toolInfo):
            ?>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo $toolInfo['name']; ?></h6>
                            <p class="card-text"><?php echo $toolInfo['description']; ?></p>
                            <a href="<?php echo $toolInfo['url']; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-arrow-right"></i> <?php echo ($currentLang === 'tr') ? 'Kullan' : 'Use'; ?>
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
    const currentLang = '<?php echo $currentLang; ?>';
    
    // Track tool usage
    AllInToolbox.analytics.trackEvent('Tool', 'View', 'BMI Calculator');
    
    // Add to recent tools
    const toolName = currentLang === 'tr' ? 'BMI Hesaplayıcı' : 'BMI Calculator';
    const toolUrl = '<?php echo $toolUrls[$currentLang]; ?>';
    AllInToolbox.storage.addRecentTool('bmi-calculator', toolName, toolUrl);
    
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

<?php include '../includes/footer.php'; ?>