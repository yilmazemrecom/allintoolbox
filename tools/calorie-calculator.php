<?php
// tools/calorie-calculator.php - KALORİ HESAPLAYICI
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
$pageTitle = ($currentLang === 'tr') ? 'Kalori Hesaplayıcı' : 'Calorie Calculator';
$pageDescription = ($currentLang === 'tr') ? 
    'Günlük kalori ihtiyacınızı hesaplayın. BMR ve aktivite seviyenize göre kalori hesaplama.' :
    'Calculate your daily calorie needs. BMR and activity level based calorie calculation.';

// Aktivite seviyeleri
$activityLevels = [
    'sedentary' => [
        'tr' => 'Hareketsiz (Ofis işi, egzersiz yok)',
        'en' => 'Sedentary (Office job, no exercise)',
        'multiplier' => 1.2
    ],
    'light' => [
        'tr' => 'Az Aktif (Hafif egzersiz, haftada 1-3 gün)',
        'en' => 'Lightly Active (Light exercise, 1-3 days/week)',
        'multiplier' => 1.375
    ],
    'moderate' => [
        'tr' => 'Orta Aktif (Orta egzersiz, haftada 3-5 gün)',
        'en' => 'Moderately Active (Moderate exercise, 3-5 days/week)',
        'multiplier' => 1.55
    ],
    'active' => [
        'tr' => 'Çok Aktif (Yoğun egzersiz, haftada 6-7 gün)',
        'en' => 'Very Active (Hard exercise, 6-7 days/week)',
        'multiplier' => 1.725
    ],
    'extra' => [
        'tr' => 'Aşırı Aktif (Çok yoğun egzersiz, fiziksel iş)',
        'en' => 'Extra Active (Very hard exercise, physical job)',
        'multiplier' => 1.9
    ]
];

// Hedefler
$goals = [
    'maintain' => [
        'tr' => 'Mevcut Kiloyu Koruma',
        'en' => 'Maintain Current Weight',
        'modifier' => 0
    ],
    'lose_slow' => [
        'tr' => 'Yavaş Kilo Verme (0.25 kg/hafta)',
        'en' => 'Slow Weight Loss (0.5 lb/week)',
        'modifier' => -250
    ],
    'lose_moderate' => [
        'tr' => 'Orta Kilo Verme (0.5 kg/hafta)',
        'en' => 'Moderate Weight Loss (1 lb/week)',
        'modifier' => -500
    ],
    'lose_fast' => [
        'tr' => 'Hızlı Kilo Verme (0.75 kg/hafta)',
        'en' => 'Fast Weight Loss (1.5 lb/week)',
        'modifier' => -750
    ],
    'gain_slow' => [
        'tr' => 'Yavaş Kilo Alma (0.25 kg/hafta)',
        'en' => 'Slow Weight Gain (0.5 lb/week)',
        'modifier' => 250
    ],
    'gain_moderate' => [
        'tr' => 'Orta Kilo Alma (0.5 kg/hafta)',
        'en' => 'Moderate Weight Gain (1 lb/week)',
        'modifier' => 500
    ]
];

// BMR hesaplama (Mifflin-St Jeor Equation)
function calculateBMR($weight, $height, $age, $gender) {
    if ($gender === 'male') {
        return (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;
    } else {
        return (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
    }
}

// TDEE hesaplama
function calculateTDEE($bmr, $activityLevel, $activityLevels) {
    return $bmr * $activityLevels[$activityLevel]['multiplier'];
}

// Form işleme
$result = null;
$error = null;

if ($_POST) {
    $weight = floatval($_POST['weight'] ?? 0);
    $height = floatval($_POST['height'] ?? 0);
    $age = intval($_POST['age'] ?? 0);
    $gender = $_POST['gender'] ?? 'male';
    $activity = $_POST['activity'] ?? 'sedentary';
    $goal = $_POST['goal'] ?? 'maintain';
    
    if ($weight > 0 && $height > 0 && $age > 0 && 
        $weight <= 500 && $height >= 50 && $height <= 250 && 
        $age >= 10 && $age <= 120) {
        
        $bmr = calculateBMR($weight, $height, $age, $gender);
        $tdee = calculateTDEE($bmr, $activity, $activityLevels);
        $targetCalories = $tdee + $goals[$goal]['modifier'];
        
        // Makro besin dağılımı (ortalama öneriler)
        $protein = ($targetCalories * 0.25) / 4; // 25% protein (4 cal/gram)
        $carbs = ($targetCalories * 0.45) / 4;   // 45% karbonhidrat (4 cal/gram)
        $fat = ($targetCalories * 0.30) / 9;     // 30% yağ (9 cal/gram)
        
        $result = [
            'bmr' => round($bmr),
            'tdee' => round($tdee),
            'target_calories' => round($targetCalories),
            'protein' => round($protein),
            'carbs' => round($carbs),
            'fat' => round($fat),
            'weight' => $weight,
            'height' => $height,
            'age' => $age,
            'gender' => $gender,
            'activity' => $activity,
            'goal' => $goal,
            'activity_name' => $activityLevels[$activity][$currentLang],
            'goal_name' => $goals[$goal][$currentLang]
        ];
    } else {
        $error = ($currentLang === 'tr') ? 
            'Lütfen geçerli değerler girin (Kilo: 1-500kg, Boy: 50-250cm, Yaş: 10-120)!' :
            'Please enter valid values (Weight: 1-500kg, Height: 50-250cm, Age: 10-120)!';
    }
}

include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">

        <!-- Breadcrumb -->
        <?php
        $breadcrumbItems = [
            ['title' => __('breadcrumb_home'), 'url' => '/' . $currentLang . '/'],
            ['title' => __('category_utility'), 'url' => function_exists('getCategoryCleanUrl') ? getCategoryCleanUrl('utility', $currentLang) : '/pages/category.php?category=utility&lang=' . $currentLang],
            ['title' => __('calorie_title')]
        ];
        echo generateBreadcrumb($breadcrumbItems);
        ?>

        <!-- Tool Container -->
        <div class="tool-container">
            <div class="tool-header">
                <h1><i class="fas fa-apple-alt text-primary"></i> <?php echo $pageTitle; ?></h1>
                <p class="lead"><?php echo $pageDescription; ?></p>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <!-- Calorie Form -->
                    <div class="tool-form">
                        <form method="POST" id="calorieForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="weight" class="form-label">
                                        <i class="fas fa-weight"></i> 
                                        <?php echo ($currentLang === 'tr') ? 'Kilo (kg)' : 'Weight (kg)'; ?>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="weight" 
                                           name="weight" 
                                           min="1" 
                                           max="500" 
                                           step="0.1" 
                                           placeholder="70"
                                           value="<?php echo isset($_POST['weight']) ? htmlspecialchars($_POST['weight']) : ''; ?>"
                                           required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="height" class="form-label">
                                        <i class="fas fa-ruler-vertical"></i> 
                                        <?php echo ($currentLang === 'tr') ? 'Boy (cm)' : 'Height (cm)'; ?>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="height" 
                                           name="height" 
                                           min="50" 
                                           max="250" 
                                           step="0.1" 
                                           placeholder="175"
                                           value="<?php echo isset($_POST['height']) ? htmlspecialchars($_POST['height']) : ''; ?>"
                                           required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="age" class="form-label">
                                        <i class="fas fa-calendar"></i> 
                                        <?php echo ($currentLang === 'tr') ? 'Yaş' : 'Age'; ?>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="age" 
                                           name="age" 
                                           min="10" 
                                           max="120" 
                                           placeholder="30"
                                           value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''; ?>"
                                           required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">
                                        <i class="fas fa-venus-mars"></i> 
                                        <?php echo ($currentLang === 'tr') ? 'Cinsiyet' : 'Gender'; ?>
                                    </label>
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="male" <?php echo (($_POST['gender'] ?? 'male') === 'male') ? 'selected' : ''; ?>>
                                            <?php echo ($currentLang === 'tr') ? 'Erkek' : 'Male'; ?>
                                        </option>
                                        <option value="female" <?php echo (($_POST['gender'] ?? '') === 'female') ? 'selected' : ''; ?>>
                                            <?php echo ($currentLang === 'tr') ? 'Kadın' : 'Female'; ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="activity" class="form-label">
                                    <i class="fas fa-running"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Aktivite Seviyesi' : 'Activity Level'; ?>
                                </label>
                                <select class="form-control" id="activity" name="activity" required>
                                    <?php foreach ($activityLevels as $level => $info): ?>
                                        <option value="<?php echo $level; ?>" 
                                                <?php echo (($_POST['activity'] ?? 'sedentary') === $level) ? 'selected' : ''; ?>>
                                            <?php echo $info[$currentLang]; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="goal" class="form-label">
                                    <i class="fas fa-target"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Hedef' : 'Goal'; ?>
                                </label>
                                <select class="form-control" id="goal" name="goal" required>
                                    <?php foreach ($goals as $goalId => $goalInfo): ?>
                                        <option value="<?php echo $goalId; ?>" 
                                                <?php echo (($_POST['goal'] ?? 'maintain') === $goalId) ? 'selected' : ''; ?>>
                                            <?php echo $goalInfo[$currentLang]; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="calculateBtn">
                                    <i class="fas fa-calculator"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Kalori Hesapla' : 'Calculate Calories'; ?>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('calorieForm').reset();">
                                    <i class="fas fa-eraser"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Temizle' : 'Clear'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Calorie Info -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Kalori Hesaplayıcı Hakkında' : 'About Calorie Calculator'; ?>
                        </h6>
                        <p class="mb-0">
                            <?php echo ($currentLang === 'tr') ? 
                                'BMR ve aktivite seviyenize göre günlük kalori ihtiyacınızı hesaplayın.' :
                                'Calculate your daily calorie needs based on BMR and activity level.'; ?>
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <?php if ($result): ?>
                    <!-- Sonuç -->
                    <div class="tool-result">
                        <h4><i class="fas fa-chart-pie"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Kalori İhtiyacınız' : 'Your Calorie Needs'; ?>
                        </h4>
                        <div class="calorie-result-display">
                            <div class="alert alert-success mb-3">
                                <h2 class="mb-2"><?php echo number_format($result['target_calories']); ?> 
                                    <?php echo ($currentLang === 'tr') ? 'kalori/gün' : 'calories/day'; ?>
                                </h2>
                                <p class="mb-0">
                                    <strong><?php echo $result['goal_name']; ?></strong>
                                </p>
                            </div>
                            
                            <!-- Detaylı Bilgiler -->
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="text-center p-2 bg-info text-white rounded">
                                        <strong><?php echo number_format($result['bmr']); ?></strong><br>
                                        <small><?php echo ($currentLang === 'tr') ? 'BMR (Kcal)' : 'BMR (Kcal)'; ?></small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-warning text-white rounded">
                                        <strong><?php echo number_format($result['tdee']); ?></strong><br>
                                        <small><?php echo ($currentLang === 'tr') ? 'TDEE (Kcal)' : 'TDEE (Kcal)'; ?></small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Makro Besinler -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6><?php echo ($currentLang === 'tr') ? 'Önerilen Makro Dağılımı' : 'Recommended Macro Distribution'; ?></h6>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="p-2 border rounded">
                                                <strong><?php echo $result['protein']; ?>g</strong><br>
                                                <small><?php echo ($currentLang === 'tr') ? 'Protein' : 'Protein'; ?></small><br>
                                                <small class="text-muted">25%</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 border rounded">
                                                <strong><?php echo $result['carbs']; ?>g</strong><br>
                                                <small><?php echo ($currentLang === 'tr') ? 'Karbonhidrat' : 'Carbs'; ?></small><br>
                                                <small class="text-muted">45%</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 border rounded">
                                                <strong><?php echo $result['fat']; ?>g</strong><br>
                                                <small><?php echo ($currentLang === 'tr') ? 'Yağ' : 'Fat'; ?></small><br>
                                                <small class="text-muted">30%</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-3">
                                <button class="btn btn-light" onclick="copyResult()">
                                    <i class="fas fa-copy"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Sonucu Kopyala' : 'Copy Result'; ?>
                                </button>
                                <button class="btn btn-outline-light" onclick="window.print()">
                                    <i class="fas fa-print"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Yazdır' : 'Print'; ?>
                                </button>
                            </div>
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
                    <!-- Placeholder -->
                    <div class="card">
                        <div class="card-body">
                            <h5><i class="fas fa-lightbulb"></i> 
                                <?php echo ($currentLang === 'tr') ? 'Hızlı Hesaplama' : 'Quick Calculation'; ?>
                            </h5>
                            <div class="row g-2">
                                <?php 
                                $quickProfiles = [
                                    ['weight' => '70', 'height' => '175', 'age' => '30', 'gender' => 'male', 'text' => ($currentLang === 'tr') ? '30 Yaş Erkek' : '30 Year Male'],
                                    ['weight' => '60', 'height' => '165', 'age' => '25', 'gender' => 'female', 'text' => ($currentLang === 'tr') ? '25 Yaş Kadın' : '25 Year Female'],
                                    ['weight' => '80', 'height' => '180', 'age' => '40', 'gender' => 'male', 'text' => ($currentLang === 'tr') ? '40 Yaş Erkek' : '40 Year Male'],
                                    ['weight' => '55', 'height' => '160', 'age' => '35', 'gender' => 'female', 'text' => ($currentLang === 'tr') ? '35 Yaş Kadın' : '35 Year Female']
                                ];
                                foreach ($quickProfiles as $profile): ?>
                                    <div class="col-6 mb-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" 
                                                onclick="setQuickProfile(<?php echo htmlspecialchars(json_encode($profile)); ?>)">
                                            <?php echo $profile['text']; ?>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Ad Space -->
        <?php echo renderAdSpace('content', 'large'); ?>

        <!-- Calorie Information -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3><i class="fas fa-book"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Kalori Hesaplaması Hakkında Detaylı Bilgi' : 'Detailed Information About Calorie Calculation'; ?>
                        </h3>
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'BMR Nedir?' : 'What is BMR?'; ?></h5>
                                <p>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'BMR (Bazal Metabolizma Hızı), vücudunuzun dinlenme halinde harcadığı minimum kalori miktarıdır. Nefes alma, kalp atışı gibi temel yaşam fonksiyonları için gereklidir.' :
                                        'BMR (Basal Metabolic Rate) is the minimum amount of calories your body burns at rest. It\'s needed for basic life functions like breathing and heart rate.'; ?>
                                </p>
                                
                                <h5><?php echo ($currentLang === 'tr') ? 'TDEE Nedir?' : 'What is TDEE?'; ?></h5>
                                <p>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'TDEE (Toplam Günlük Enerji Harcaması), BMR\'nize aktivite seviyenizi ekleyerek günlük toplam kalori ihtiyacınızı hesaplar.' :
                                        'TDEE (Total Daily Energy Expenditure) calculates your total daily calorie needs by adding your activity level to your BMR.'; ?>
                                </p>
                                
                                <h5><?php echo ($currentLang === 'tr') ? 'Aktivite Seviyeleri' : 'Activity Levels'; ?></h5>
                                <ul>
                                    <?php foreach ($activityLevels as $level => $info): ?>
                                        <li><strong><?php echo $info[$currentLang]; ?></strong> (×<?php echo $info['multiplier']; ?>)</li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Makro Besinler' : 'Macronutrients'; ?></h5>
                                <ul>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Protein:' : 'Protein:'; ?></strong> 
                                        <?php echo ($currentLang === 'tr') ? 
                                            '4 kalori/gram - Kas yapımı ve onarımı için önemli' :
                                            '4 calories/gram - Important for muscle building and repair'; ?>
                                    </li>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Karbonhidrat:' : 'Carbohydrates:'; ?></strong> 
                                        <?php echo ($currentLang === 'tr') ? 
                                            '4 kalori/gram - Ana enerji kaynağı' :
                                            '4 calories/gram - Primary energy source'; ?>
                                    </li>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Yağ:' : 'Fat:'; ?></strong> 
                                        <?php echo ($currentLang === 'tr') ? 
                                            '9 kalori/gram - Hormon üretimi ve vitamin emilimi için gerekli' :
                                            '9 calories/gram - Essential for hormone production and vitamin absorption'; ?>
                                    </li>
                                </ul>
                                
                                <h5><?php echo ($currentLang === 'tr') ? 'Kilo Verme/Alma Rehberi' : 'Weight Loss/Gain Guide'; ?></h5>
                                <ul>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        '1 kg yağ ≈ 7700 kalori' :
                                        '1 kg fat ≈ 7700 calories'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Sağlıklı kilo verme: Haftada 0.5-1 kg' :
                                        'Healthy weight loss: 0.5-1 kg per week'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Günlük 500 kalori eksik = Haftada 0.5 kg kilo verme' :
                                        '500 calorie daily deficit = 0.5 kg weight loss per week'; ?></li>
                                </ul>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong><?php echo ($currentLang === 'tr') ? 'Önemli:' : 'Important:'; ?></strong>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Bu hesaplamalar genel önerilerdir. Kişisel diyet planı için uzman desteği alın.' :
                                        'These calculations are general recommendations. Seek professional advice for personalized diet plans.'; ?>
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
                $relatedTools = ['age-calculator', 'bmi-calculator'];
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
// Calorie Calculator specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('calorieForm');
    const calculateBtn = document.getElementById('calculateBtn');
    const currentLang = '<?php echo $currentLang; ?>';
    
    // Track tool usage
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Tool', 'View', 'Calorie Calculator');
        
        // Add to recent tools
        const toolName = currentLang === 'tr' ? 'Kalori Hesaplayıcı' : 'Calorie Calculator';
        const toolUrl = '/tools/calorie-calculator.php?lang=' + currentLang;
        AllInToolbox.storage.addRecentTool('calorie-calculator', toolName, toolUrl);
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const weight = parseFloat(document.getElementById('weight').value);
        const height = parseFloat(document.getElementById('height').value);
        const age = parseInt(document.getElementById('age').value);
        
        if (weight && height && age) {
            if (typeof AllInToolbox !== 'undefined') {
                AllInToolbox.utils.showLoading(calculateBtn);
                AllInToolbox.analytics.trackToolUsage('Calorie Calculator');
            }
            
            // Simulate processing time
            setTimeout(() => {
                if (typeof AllInToolbox !== 'undefined') {
                    AllInToolbox.utils.showLoading(calculateBtn, false);
                }
            }, 1000);
        }
    });
});

// Set quick profile
function setQuickProfile(profile) {
    document.getElementById('weight').value = profile.weight;
    document.getElementById('height').value = profile.height;
    document.getElementById('age').value = profile.age;
    document.getElementById('gender').value = profile.gender;
}

// Copy result
function copyResult() {
    <?php if ($result): ?>
    const resultText = '<?php echo ($currentLang === 'tr') ? 'Günlük Kalori İhtiyacı: ' : 'Daily Calorie Needs: '; ?><?php echo $result['target_calories']; ?> kcal\n' +
                      '<?php echo ($currentLang === 'tr') ? 'BMR: ' : 'BMR: '; ?><?php echo $result['bmr']; ?> kcal\n' +
                      '<?php echo ($currentLang === 'tr') ? 'TDEE: ' : 'TDEE: '; ?><?php echo $result['tdee']; ?> kcal\n' +
                      '<?php echo ($currentLang === 'tr') ? 'Hedef: ' : 'Goal: '; ?><?php echo $result['goal_name']; ?>';
    
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.utils.copyToClipboard(resultText);
    } else {
        navigator.clipboard.writeText(resultText).then(() => {
            alert('<?php echo ($currentLang === 'tr') ? 'Sonuç kopyalandı!' : 'Result copied!'; ?>');
        });
    }
    <?php endif; ?>
}

<?php if ($result): ?>
// Track successful calculation
if (typeof AllInToolbox !== 'undefined') {
    AllInToolbox.analytics.trackEvent('Tool', 'Calculate', 'Calorie Calculator');
}
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>