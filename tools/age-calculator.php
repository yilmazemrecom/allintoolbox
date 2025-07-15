<?php
// tools/age-calculator.php
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

if (file_exists('../config/url-helpers.php')) {
    require_once '../config/url-helpers.php';
}

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Sayfa meta bilgileri
$pageTitle = __('age_title') . __('meta_title_suffix');
$pageDescription = __('age_description');
$pageKeywords = 'age, ' . __('category_health') . ', ' . __('calculate');


// Yaş hesaplama fonksiyonu
function calculateAge($birthDate) {
    $birth = new DateTime($birthDate);
    $today = new DateTime();
    $interval = $birth->diff($today);
    
    $totalDays = $birth->diff($today)->days;
    $totalMonths = $interval->y * 12 + $interval->m;
    $totalWeeks = floor($totalDays / 7);
    $totalHours = $totalDays * 24;
    $totalMinutes = $totalHours * 60;
    
    return [
        'years' => $interval->y,
        'months' => $interval->m,
        'days' => $interval->d,
        'total_days' => $totalDays,
        'total_months' => $totalMonths,
        'total_weeks' => $totalWeeks,
        'total_hours' => $totalHours,
        'total_minutes' => $totalMinutes,
        'birth_date' => $birth,
        'next_birthday' => calculateNextBirthday($birth)
    ];
}

function calculateNextBirthday($birthDate) {
    $today = new DateTime();
    $thisYear = $today->format('Y');
    $nextBirthday = new DateTime($thisYear . '-' . $birthDate->format('m-d'));
    
    if ($nextBirthday < $today) {
        $nextBirthday->add(new DateInterval('P1Y'));
    }
    
    $daysUntil = $today->diff($nextBirthday)->days;
    return [
        'date' => $nextBirthday,
        'days_until' => $daysUntil
    ];
}

$result = null;
$error = null;

if ($_POST && !empty($_POST['birth_date'])) {
    $birthDate = $_POST['birth_date'];
    
    try {
        $birth = new DateTime($birthDate);
        $today = new DateTime();
        
        if ($birth > $today) {
            $error = ($currentLang === 'tr') ? 'Doğum tarihi gelecekte olamaz!' : 'Birth date cannot be in the future!';
        } else {
            $result = calculateAge($birthDate);
        }
    } catch (Exception $e) {
        $error = ($currentLang === 'tr') ? 'Geçerli bir tarih girin!' : 'Please enter a valid date!';
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
            ['title' => __('age_title')]
        ];
        echo generateBreadcrumb($breadcrumbItems);
        ?>
        <h1><i class="fas fa-birthday-cake text-primary"></i> 
            <?php echo ($currentLang === 'tr') ? 'Yaş Hesaplayıcı' : 'Age Calculator'; ?>
        </h1>
        <p class="lead">
            <?php echo ($currentLang === 'tr') ? 'Doğum tarihinizden yaşınızı hesaplayın' : 'Calculate your age from birth date'; ?>
        </p>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">
                                    <?php echo ($currentLang === 'tr') ? 'Doğum Tarihiniz' : 'Your Birth Date'; ?>
                                </label>
                                <input type="date" name="birth_date" class="form-control" 
                                       value="<?php echo $_POST['birth_date'] ?? ''; ?>" 
                                       max="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-calculator"></i> 
                                <?php echo ($currentLang === 'tr') ? 'Yaşımı Hesapla' : 'Calculate My Age'; ?>
                            </button>
                        </form>
                    </div>
                </div>

                <?php if (!$result && !$error): ?>
                <div class="card mt-3">
                    <div class="card-body">
                        <h6><?php echo ($currentLang === 'tr') ? 'Hızlı Hesaplama' : 'Quick Calculation'; ?></h6>
                        <div class="row g-2">
                            <?php 
                            $quickAges = [
                                ['year' => date('Y') - 20, 'age' => 20],
                                ['year' => date('Y') - 25, 'age' => 25],
                                ['year' => date('Y') - 30, 'age' => 30],
                                ['year' => date('Y') - 35, 'age' => 35]
                            ];
                            foreach ($quickAges as $quick): ?>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" 
                                            onclick="document.querySelector('input[name=birth_date]').value='<?php echo $quick['year']; ?>-01-01'">
                                        <?php echo $quick['age']; ?> <?php echo ($currentLang === 'tr') ? 'yaş' : 'years old'; ?>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <?php if ($result): ?>
                <div class="card border-success">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <?php echo ($currentLang === 'tr') ? 'Yaş Bilgileriniz' : 'Your Age Information'; ?>
                        </h5>
                        
                        <div class="alert alert-primary">
                            <h3><?php echo $result['years']; ?> 
                                <?php echo ($currentLang === 'tr') ? 'yaş' : 'years old'; ?>
                            </h3>
                            <p class="mb-0">
                                <?php echo $result['years']; ?> <?php echo ($currentLang === 'tr') ? 'yıl' : 'years'; ?>, 
                                <?php echo $result['months']; ?> <?php echo ($currentLang === 'tr') ? 'ay' : 'months'; ?>, 
                                <?php echo $result['days']; ?> <?php echo ($currentLang === 'tr') ? 'gün' : 'days'; ?>
                            </p>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <strong><?php echo number_format($result['total_days']); ?></strong><br>
                                    <small><?php echo ($currentLang === 'tr') ? 'Toplam Gün' : 'Total Days'; ?></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <strong><?php echo number_format($result['total_weeks']); ?></strong><br>
                                    <small><?php echo ($currentLang === 'tr') ? 'Toplam Hafta' : 'Total Weeks'; ?></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <strong><?php echo number_format($result['total_months']); ?></strong><br>
                                    <small><?php echo ($currentLang === 'tr') ? 'Toplam Ay' : 'Total Months'; ?></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <strong><?php echo number_format($result['total_hours']); ?></strong><br>
                                    <small><?php echo ($currentLang === 'tr') ? 'Toplam Saat' : 'Total Hours'; ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <h6><?php echo ($currentLang === 'tr') ? 'Bir Sonraki Doğum Günü' : 'Next Birthday'; ?></h6>
                            <p class="mb-0">
                                <strong><?php echo $result['next_birthday']['date']->format('d.m.Y'); ?></strong><br>
                                <small>
                                    <?php echo $result['next_birthday']['days_until']; ?> 
                                    <?php echo ($currentLang === 'tr') ? 'gün kaldı' : 'days remaining'; ?>
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <?php elseif ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <h6><?php echo ($currentLang === 'tr') ? 'Yaş Hesaplayıcı Hakkında' : 'About Age Calculator'; ?></h6>
                        <ul class="small">
                            <li><?php echo ($currentLang === 'tr') ? 'Doğum tarihinizi seçin' : 'Select your birth date'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? 'Tam yaşınızı öğrenin (yıl, ay, gün)' : 'Learn your exact age (years, months, days)'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? 'Toplam gün/saat sayısını görün' : 'See total days/hours lived'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? 'Bir sonraki doğum gününü öğrenin' : 'Find out your next birthday'; ?></li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
                <!-- Related Tools -->
        <div class="row mt-4">
            <div class="col-12">
                <h4><i class="fas fa-link"></i> <?php echo __('related_tools'); ?></h4>
                <div class="row">

                        <?php
                        $relatedTools = ['calorie-calculator', 'bmi-calculator']; 
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>