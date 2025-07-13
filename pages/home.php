<?php
// pages/home.php - Unified Homepage
session_start();

// Konfigürasyonu yükle
require_once '../config/config.php';
require_once '../config/functions.php';

// Dil al (URL'den veya query'den)
$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Sayfa bilgileri
$pageTitle = __('site_name') . ' - ' . __('meta_description_default');
$pageDescription = __('meta_description_default');
$pageKeywords = __('meta_keywords_default');

// Header'ı dahil et
include '../includes/header.php';
?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12">
                <h1 class="display-4 fw-bold mb-4">
                    <?php echo __('hero_title'); ?>
                </h1>
                <p class="lead mb-4">
                    <?php echo __('hero_subtitle'); ?>
                </p>
                <div class="d-flex flex-column flex-sm-row gap-3">
                    <a href="#tools" class="btn btn-warning btn-lg">
                        <i class="fas fa-tools"></i> <?php echo __('explore_tools'); ?>
                    </a>
                    <a href="#popular" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-star"></i> <?php echo __('popular_tools'); ?>
                    </a>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 text-center mt-4 mt-lg-0">
                <div class="hero-visual" style="background: rgba(255,255,255,0.1); border: 2px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 3rem 2rem; backdrop-filter: blur(10px);">
                    <div class="d-flex justify-content-center align-items-center mb-3" style="gap: 1rem;">
                        <i class="fas fa-calculator fa-3x text-warning"></i>
                        <i class="fas fa-qrcode fa-3x text-info"></i>
                        <i class="fas fa-exchange-alt fa-3x text-success"></i>
                    </div>
                    <h4 class="text-white mb-2">10+ <?php echo ($currentLang === 'tr') ? 'Ücretsiz Araç' : 'Free Tools'; ?></h4>
                    <p class="text-white-50 mb-0"><?php echo ($currentLang === 'tr') ? 'Hızlı • Güvenli • Kolay' : 'Fast • Secure • Easy'; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Tools -->
<div class="container my-5" id="popular">
    <h2 class="text-center mb-5">
        <i class="fas fa-fire text-warning"></i> <?php echo __('popular_tools_title'); ?>
    </h2>
    <div class="row g-4">
        <?php
        $quickTools = [
            'bmi-calculator' => ['icon' => 'fas fa-weight', 'color' => 'primary'],
            'loan-calculator' => ['icon' => 'fas fa-calculator', 'color' => 'success'],
            'qr-generator' => ['icon' => 'fas fa-qrcode', 'color' => 'info'],
            'currency-converter' => ['icon' => 'fas fa-exchange-alt', 'color' => 'warning'],
            'password-generator' => ['icon' => 'fas fa-key', 'color' => 'danger'],
            'unit-converter' => ['icon' => 'fas fa-ruler', 'color' => 'secondary']
        ];
        
        foreach ($quickTools as $toolId => $toolStyle):
            $toolInfo = getToolInfo($toolId, $currentLang);
            if ($toolInfo):
        ?>
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card h-100 shadow-sm tool-card">
                    <div class="card-body text-center">
                        <div class="tool-icon mb-3">
                            <i class="<?php echo $toolStyle['icon']; ?> fa-3x text-<?php echo $toolStyle['color']; ?>"></i>
                        </div>
                        <h5 class="card-title"><?php echo $toolInfo['name']; ?></h5>
                        <p class="card-text text-muted"><?php echo $toolInfo['description']; ?></p>
                        <a href="<?php echo $toolInfo['url']; ?>" class="btn btn-<?php echo $toolStyle['color']; ?> w-100">
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

<!-- Ad Space -->
<?php echo renderAdSpace('content', 'large'); ?>

<!-- Categories Section -->
<div class="container my-5" id="tools">
    <h2 class="text-center mb-5">
        <i class="fas fa-th-large text-primary"></i> <?php echo __('tool_categories'); ?>
    </h2>
    <div class="row g-4">
        <?php
        $categoryIcons = [
            'finance' => ['icon' => 'fas fa-chart-line', 'color' => 'success'],
            'health' => ['icon' => 'fas fa-heartbeat', 'color' => 'danger'],
            'web' => ['icon' => 'fas fa-code', 'color' => 'info'],
            'converter' => ['icon' => 'fas fa-exchange-alt', 'color' => 'warning'],
            'utility' => ['icon' => 'fas fa-tools', 'color' => 'secondary']
        ];
        
        foreach (TOOL_CATEGORIES as $categoryId => $categoryNames):
            $categoryTools = getToolsByCategory($categoryId, $currentLang);
            $iconInfo = $categoryIcons[$categoryId];
        ?>
            <div class="col-lg-6 col-12">
                <div class="card category-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="category-icon me-3">
                                <i class="<?php echo $iconInfo['icon']; ?> fa-2x text-<?php echo $iconInfo['color']; ?>"></i>
                            </div>
                            <h4 class="card-title mb-0"><?php echo $categoryNames[$currentLang]; ?></h4>
                        </div>
                        
                        <div class="row g-2">
                            <?php foreach ($categoryTools as $toolId => $tool): ?>
                                <div class="col-6">
                                    <a href="<?php echo $tool['url']; ?>" class="text-decoration-none d-block p-2 rounded bg-light">
                                        <small class="text-muted">
                                            <i class="fas fa-chevron-right me-1"></i><?php echo $tool['name']; ?>
                                        </small>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <a href="/<?php echo $currentLang; ?>/category/<?php echo $categoryId; ?>" class="btn btn-outline-<?php echo $iconInfo['color']; ?> btn-sm mt-3 w-100">
                            <i class="fas fa-eye"></i> <?php echo __('view_all'); ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Features Section -->
<div class="features-section">
    <div class="container">
        <h2 class="text-center mb-5 text-white"><?php echo __('why_choose_us'); ?></h2>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 col-12">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-bolt fa-3x text-warning"></i>
                    </div>
                    <h5 class="text-white"><?php echo __('feature_fast_title'); ?></h5>
                    <p class="text-muted"><?php echo __('feature_fast_desc'); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-shield-alt fa-3x text-success"></i>
                    </div>
                    <h5 class="text-white"><?php echo __('feature_secure_title'); ?></h5>
                    <p class="text-muted"><?php echo __('feature_secure_desc'); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-mobile-alt fa-3x text-info"></i>
                    </div>
                    <h5 class="text-white"><?php echo __('feature_mobile_title'); ?></h5>
                    <p class="text-muted"><?php echo __('feature_mobile_desc'); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-heart fa-3x text-danger"></i>
                    </div>
                    <h5 class="text-white"><?php echo __('feature_free_title'); ?></h5>
                    <p class="text-muted"><?php echo __('feature_free_desc'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>



<?php include '../includes/footer.php'; ?>