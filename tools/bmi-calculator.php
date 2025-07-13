<?php
// tools/bmi-calculator.php - Unified BMI Calculator
session_start();

// Konfigürasyonu yükle
require_once '../config/config.php';
require_once '../config/functions.php';

// Dil al (URL'den veya query'den)
$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Sayfa meta bilgileri
$pageTitle = __('bmi_title') . __('meta_title_suffix');
$pageDescription = __('bmi_title') . ' - ' . __('bmi_subtitle');
$pageKeywords = 'BMI, ' . __('category_health') . ', ' . __('calculate');

// BMI hesaplama fonksiyonu
function calculateBMI($weight, $height) {
    $heightInMeters = $height / 100;
    $bmi = $weight / ($heightInMeters * $heightInMeters);
    return round($bmi, 1);
}

function getBMICategory($bmi) {
    if ($bmi < 18.5) {
        return ['category' => __('bmi_underweight'), 'class' => 'info', 'description' => __('bmi_underweight_desc')];
    } elseif ($bmi < 25) {
        return ['category' => __('bmi_normal'), 'class' => 'success', 'description' => __('bmi_normal_desc')];
    } elseif ($bmi < 30) {
        return ['category' => __('bmi_overweight'), 'class' => 'warning', 'description' => __('bmi_overweight_desc')];
    } else {
        return ['category' => __('bmi_obese'), 'class' => 'danger', 'description' => __('bmi_obese_desc')];
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
        $category =<?php
// tools/bmi-calculator.php - Unified BMI Calculator
session_start();

// Konfigürasyonu yükle
require_once '../config/config.php';
require_once '../config/functions.php';

// URL'den dil al
$currentLang = detectLanguage();
setLanguage($currentLang);

// Sayfa meta bilgileri
$pageTitle = __('bmi_title') . __('meta_title_suffix');
$pageDescription = __('bmi_title') . ' - ' . __('bmi_subtitle');
$pageKeywords = 'BMI, ' . __('category_health') . ', ' . __('calculate');

// BMI hesaplama fonksiyonu
function calculateBMI($weight, $height) {
    $heightInMeters = $height / 100;
    $bmi = $weight / ($heightInMeters * $heightInMeters);
    return round($bmi, 1);
}

function getBMICategory($bmi) {
    if ($bmi < 18.5) {
        return ['category' => __('bmi_underweight'), 'class' => 'info', 'description' => __('bmi_underweight_desc')];
    } elseif ($bmi < 25) {
        return ['category' => __('bmi_normal'), 'class' => 'success', 'description' => __('bmi_normal_desc')];
    } elseif ($bmi < 30) {
        return ['category' => __('bmi_overweight'), 'class' => 'warning', 'description' => __('bmi_overweight_desc')];
    } else {
        return ['category' => __('bmi_obese'), 'class' => 'danger', 'description' => __('bmi_obese_desc')];
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
    } else {
        $error = __('bmi_error');
    }
}

// Header'ı dahil et
include '../includes/header.php';
?>

<!-- Breadcrumb -->
<?php
$breadcrumbItems = [
    ['title' => __('breadcrumb_home'), 'url' => '/' . $currentLang . '/'],
    ['title' => __('breadcrumb_health_tools'), 'url' => '/' . $currentLang . '/category/health'],
    ['title' => __('bmi_title')]
];
echo generateBreadcrumb($breadcrumbItems);
?>

<!-- Tool Container -->
<div class="tool-container">
    <div class="tool-header">
        <h1><i class="fas fa-weight text-primary"></i> <?php echo __('bmi_title'); ?></h1>
        <p class="lead"><?php echo __('bmi_subtitle'); ?></p>
    </div>
    
    <div class="row">
        <div class="col-lg-6">
            <!-- BMI Form -->
            <div class="tool-form">
                <form method="POST" id="bmiForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="weight" class="form-label">
                                <i class="fas fa-weight-hanging"></i> <?php echo __('bmi_weight_label'); ?>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="weight" 
                                   name="weight" 
                                   min="1" 
                                   max="500" 
                                   step="0.1" 
                                   placeholder="<?php echo __('bmi_weight_placeholder'); ?>"
                                   value="<?php echo isset($_POST['weight']) ? htmlspecialchars($_POST['weight']) : ''; ?>"
                                   required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="height" class="form-label">
                                <i class="fas fa-ruler-vertical"></i> <?php echo __('bmi_height_label'); ?>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="height" 
                                   name="height" 
                                   min="50" 
                                   max="250" 
                                   step="0.1" 
                                   placeholder="<?php echo __('bmi_height_placeholder'); ?>"
                                   value="<?php echo isset($_POST['height']) ? htmlspecialchars($_POST['height']) : ''; ?>"
                                   required>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="calculateBtn">
                            <i class="fas fa-calculator"></i> <?php echo __('bmi_calculate'); ?>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('bmiForm').reset();">
                            <i class="fas fa-eraser"></i> <?php echo __('clear'); ?>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- BMI Bilgi -->
            <div class="alert alert-info alert-custom">
                <h6><i class="fas fa-info-circle"></i> <?php echo __('bmi_what_is'); ?></h6>
                <p class="mb-0"><?php echo __('bmi_description'); ?></p>
            </div>
        </div>
        
        <div class="col-lg-6">
            <?php if ($result): ?>
            <!-- Sonuç -->
            <div class="tool-result">
                <h4><i class="fas fa-chart-line"></i> <?php echo __('bmi_result_title'); ?></h4>
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
                        <p><strong><?php echo __('bmi_weight_label'); ?>:</strong> <?php echo $result['weight']; ?> kg</p>
                        <p><strong><?php echo __('bmi_height_label'); ?>:</strong> <?php echo $result['height']; ?> cm</p>
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-light" onclick="AllInToolbox.utils.copyToClipboard('BMI: <?php echo $result['bmi']; ?> (<?php echo $result['category']; ?>)')">
                            <i class="fas fa-copy"></i> <?php echo __('copy'); ?>
                        </button>
                        <button class="btn btn-outline-light" onclick="window.print()">
                            <i class="fas fa-print"></i> <?php echo __('print'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php elseif ($error): ?>
            <!-- Hata -->
            <div class="tool-result error">
                <h4><i class="fas fa-exclamation-triangle"></i> <?php echo __('error'); ?></h4>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
            <?php else: ?>
            <!-- Placeholder -->
            <div class="bmi-chart">
                <h5><i class="fas fa-chart-bar"></i> <?php echo __('bmi_categories'); ?></h5>
                <div class="bmi-categories">
                    <div class="bmi-category mb-2">
                        <span class="badge bg-info me-2"><?php echo __('bmi_underweight'); ?></span>
                        <span>< 18.5</span>
                    </div>
                    <div class="bmi-category mb-2">
                        <span class="badge bg-success me-2"><?php echo __('bmi_normal'); ?></span>
                        <span>18.5 - 24.9</span>
                    </div>
                    <div class="bmi-category mb-2">
                        <span class="badge bg-warning me-2"><?php echo __('bmi_overweight'); ?></span>
                        <span>25.0 - 29.9</span>
                    </div>
                    <div class="bmi-category mb-2">
                        <span class="badge bg-danger me-2"><?php echo __('bmi_obese'); ?></span>
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
                <h3><i class="fas fa-book"></i> <?php echo __('bmi_detailed_info'); ?></h3>
                
                <div class="row">
                    <div class="col-lg-6">
                        <h5><?php echo __('bmi_how_calculated'); ?></h5>
                        <p><?php echo __('bmi_formula_text'); ?></p>
                        <div class="alert alert-light">
                            <code><?php echo __('bmi_formula'); ?></code>
                        </div>
                        
                        <h5><?php echo __('bmi_ranges'); ?></h5>
                        <ul>
                            <li><strong><?php echo __('bmi_underweight'); ?>:</strong> BMI < 18.5</li>
                            <li><strong><?php echo __('bmi_normal'); ?>:</strong> 18.5 ≤ BMI < 25</li>
                            <li><strong><?php echo __('bmi_overweight'); ?>:</strong> 25 ≤ BMI < 30</li>
                            <li><strong><?php echo __('bmi_obese'); ?>:</strong> BMI ≥ 30</li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-6">
                        <h5><?php echo __('bmi_important_notes'); ?></h5>
                        <ul>
                            <li><?php echo __('bmi_note_1'); ?></li>
                            <li><?php echo __('bmi_note_2'); ?></li>
                            <li><?php echo __('bmi_note_3'); ?></li>
                            <li><?php echo __('bmi_note_4'); ?></li>
                        </ul>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong><?php echo __('warning'); ?>:</strong> <?php echo __('bmi_warning'); ?>
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

<script>
// BMI Calculator specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bmiForm');
    const calculateBtn = document.getElementById('calculateBtn');
    const currentLang = '<?php echo $currentLang; ?>';
    
    // Track tool usage
    AllInToolbox.analytics.trackEvent('Tool', 'View', 'BMI Calculator');
    
    // Add to recent tools
    const toolName = '<?php echo __('bmi_title'); ?>';
    const toolUrl = `/${currentLang}/${currentLang === 'tr' ? 'araclar/bmi-hesaplayici' : 'tools/bmi-calculator'}`;
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
});

<?php if ($result): ?>
// Track successful calculation
AllInToolbox.analytics.trackEvent('Tool', 'Calculate', 'BMI Calculator');
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>