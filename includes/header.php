<?php
// includes/header.php - CLEAN URLs ile GÜNCELLENMİŞ VERSİYON

$currentLang = getCurrentLanguage();

// Include URL helpers if available
if (file_exists(__DIR__ . '/../config/url-helpers.php')) {
    require_once __DIR__ . '/../config/url-helpers.php';
}

// Mevcut sayfanın path'ini al
$currentPath = $_SERVER['REQUEST_URI'];
$isHomePage = ($currentPath === '/' || strpos($currentPath, '/pages/home.php') !== false);
$isToolPage = (strpos($currentPath, '/tools/') !== false);

// Base URL'i belirle - clean URLs için root
$baseUrl = '';
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="AllInToolbox">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#1a1d29">
    
    <?php
    // Meta tags
    generateMetaTags($pageTitle ?? null, $pageDescription ?? null, $pageKeywords ?? null);
    ?>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $baseUrl; ?>/assets/images/favicon.ico">

    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo $pageTitle ?? SITE_NAME; ?>">
    <meta property="og:description" content="<?php echo $pageDescription ?? __('meta_description_default'); ?>">
    <meta property="og:image" content="<?php echo $baseUrl; ?>/assets/images/og-image.png">
    <meta property="og:url" content="<?php echo getCurrentUrl(); ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo SITE_NAME; ?>">
    
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/style.css">
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "<?php echo SITE_NAME; ?>",
        "url": "<?php echo $baseUrl; ?>",
        "description": "<?php echo __('meta_description_default'); ?>",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "<?php echo $baseUrl; ?>/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="/<?php echo $currentLang; ?>/" 
                   title="<?php echo SITE_NAME; ?> - <?php echo __('meta_description_default'); ?>">
                    <i class="fas fa-tools me-2"></i>
                    <span class="fw-bold"><?php echo SITE_NAME; ?></span>
                </a>
                
                <!-- Mobile toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Navigation -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">

                        <!-- Finance Tools -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" 
                               title="<?php echo __('category_finance'); ?>">
                                <i class="fas fa-chart-line me-1"></i> <?php echo __('category_finance'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><h6 class="dropdown-header"><?php echo __('category_finance'); ?></h6></li>
                                <li><a class="dropdown-item" href="<?php echo function_exists('getToolCleanUrl') ? getToolCleanUrl('loan-calculator', $currentLang) : "/tools/loan-calculator.php?lang={$currentLang}"; ?>">
                                    <i class="fas fa-calculator me-2"></i><?php echo ($currentLang === 'tr') ? 'Kredi Hesaplayıcı' : 'Loan Calculator'; ?>
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo function_exists('getToolCleanUrl') ? getToolCleanUrl('currency-converter', $currentLang) : "/tools/currency-converter.php?lang={$currentLang}"; ?>">
                                    <i class="fas fa-exchange-alt me-2"></i><?php echo ($currentLang === 'tr') ? 'Döviz Çevirici' : 'Currency Converter'; ?>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo function_exists('getCategoryCleanUrl') ? getCategoryCleanUrl('finance', $currentLang) : "/pages/category.php?category=finance&lang={$currentLang}"; ?>">
                                    <i class="fas fa-eye me-2"></i><?php echo ($currentLang === 'tr') ? 'Tümünü Gör' : 'View All'; ?>
                                </a></li>
                            </ul>
                        </li>
                        
                        <!-- Health Tools -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" 
                               title="<?php echo __('category_health'); ?>">
                                <i class="fas fa-heartbeat me-1"></i> <?php echo __('category_health'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><h6 class="dropdown-header"><?php echo __('category_health'); ?></h6></li>
                                <li><a class="dropdown-item" href="<?php echo function_exists('getToolCleanUrl') ? getToolCleanUrl('bmi-calculator', $currentLang) : "/tools/bmi-calculator.php?lang={$currentLang}"; ?>">
                                    <i class="fas fa-weight me-2"></i><?php echo ($currentLang === 'tr') ? 'BMI Hesaplayıcı' : 'BMI Calculator'; ?>
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo function_exists('getToolCleanUrl') ? getToolCleanUrl('calorie-calculator', $currentLang) : "/tools/calorie-calculator.php?lang={$currentLang}"; ?>">
                                    <i class="fas fa-apple-alt me-2"></i><?php echo ($currentLang === 'tr') ? 'Kalori Hesaplayıcı' : 'Calorie Calculator'; ?>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo function_exists('getCategoryCleanUrl') ? getCategoryCleanUrl('health', $currentLang) : "/pages/category.php?category=health&lang={$currentLang}"; ?>">
                                    <i class="fas fa-eye me-2"></i><?php echo ($currentLang === 'tr') ? 'Tümünü Gör' : 'View All'; ?>
                                </a></li>
                            </ul>
                        </li>
                        
                        <!-- Web Tools -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" 
                               title="<?php echo __('category_web'); ?>">
                                <i class="fas fa-code me-1"></i> <?php echo __('category_web'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><h6 class="dropdown-header"><?php echo __('category_web'); ?></h6></li>
                                <li><a class="dropdown-item" href="<?php echo function_exists('getToolCleanUrl') ? getToolCleanUrl('qr-code-generator', $currentLang) : "/tools/qr-code-generator.php?lang={$currentLang}"; ?>">
                                    <i class="fas fa-qrcode me-2"></i><?php echo ($currentLang === 'tr') ? 'QR Kod Üretici' : 'QR Code Generator'; ?>
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo function_exists('getToolCleanUrl') ? getToolCleanUrl('password-generator', $currentLang) : "/tools/password-generator.php?lang={$currentLang}"; ?>">
                                    <i class="fas fa-key me-2"></i><?php echo ($currentLang === 'tr') ? 'Şifre Üretici' : 'Password Generator'; ?>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo function_exists('getCategoryCleanUrl') ? getCategoryCleanUrl('web', $currentLang) : "/pages/category.php?category=web&lang={$currentLang}"; ?>">
                                    <i class="fas fa-eye me-2"></i><?php echo ($currentLang === 'tr') ? 'Tümünü Gör' : 'View All'; ?>
                                </a></li>
                            </ul>
                        </li>
                        
                        <!-- Converter Tools -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" 
                               title="<?php echo __('category_converter'); ?>">
                                <i class="fas fa-exchange-alt me-1"></i> <?php echo __('category_converter'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><h6 class="dropdown-header"><?php echo __('category_converter'); ?></h6></li>
                                <li><a class="dropdown-item" href="<?php echo function_exists('getToolCleanUrl') ? getToolCleanUrl('unit-converter', $currentLang) : "/tools/unit-converter.php?lang={$currentLang}"; ?>">
                                    <i class="fas fa-ruler me-2"></i><?php echo ($currentLang === 'tr') ? 'Ölçü Birimi Çevirici' : 'Unit Converter'; ?>
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo function_exists('getToolCleanUrl') ? getToolCleanUrl('color-converter', $currentLang) : "/tools/color-converter.php?lang={$currentLang}"; ?>">
                                    <i class="fas fa-palette me-2"></i><?php echo ($currentLang === 'tr') ? 'Renk Çevirici' : 'Color Converter'; ?>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo function_exists('getCategoryCleanUrl') ? getCategoryCleanUrl('converter', $currentLang) : "/pages/category.php?category=converter&lang={$currentLang}"; ?>">
                                    <i class="fas fa-eye me-2"></i><?php echo ($currentLang === 'tr') ? 'Tümünü Gör' : 'View All'; ?>
                                </a></li>
                            </ul>
                        </li>
                        
                        <!-- Utility Tools -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" 
                               title="<?php echo __('category_utility'); ?>">
                                <i class="fas fa-tools me-1"></i> <?php echo __('category_utility'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><h6 class="dropdown-header"><?php echo __('category_utility'); ?></h6></li>
                                <li><a class="dropdown-item" href="<?php echo function_exists('getToolCleanUrl') ? getToolCleanUrl('text-analyzer', $currentLang) : "/tools/text-analyzer.php?lang={$currentLang}"; ?>">
                                    <i class="fas fa-file-alt me-2"></i><?php echo ($currentLang === 'tr') ? 'Metin Analizi' : 'Text Analyzer'; ?>
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo function_exists('getToolCleanUrl') ? getToolCleanUrl('age-calculator', $currentLang) : "/tools/age-calculator.php?lang={$currentLang}"; ?>">
                                    <i class="fas fa-birthday-cake me-2"></i><?php echo ($currentLang === 'tr') ? 'Yaş Hesaplayıcı' : 'Age Calculator'; ?>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo function_exists('getCategoryCleanUrl') ? getCategoryCleanUrl('utility', $currentLang) : "/pages/category.php?category=utility&lang={$currentLang}"; ?>">
                                    <i class="fas fa-eye me-2"></i><?php echo ($currentLang === 'tr') ? 'Tümünü Gör' : 'View All'; ?>
                                </a></li>
                            </ul>
                        </li>
                    </ul>
                    
                    <!-- Right Menu -->
                    <ul class="navbar-nav">
                        <!-- More Menu Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" 
                               title="<?php echo ($currentLang === 'tr') ? 'Daha Fazla' : 'More'; ?>">
                                <i class="fas fa-ellipsis-h me-1"></i>
                                <span class="d-none d-lg-inline"><?php echo ($currentLang === 'tr') ? 'Daha Fazla' : 'More'; ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <!-- About -->
                                <li>
                                    <a class="dropdown-item" href="<?php echo function_exists('getStaticCleanUrl') ? getStaticCleanUrl('about', $currentLang) : "/pages/about.php?lang={$currentLang}"; ?>" 
                                       title="<?php echo ($currentLang === 'tr') ? 'Hakkımızda' : 'About Us'; ?>">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <?php echo ($currentLang === 'tr') ? 'Hakkımızda' : 'About Us'; ?>
                                    </a>
                                </li>
                                
                                <!-- Contact -->
                                <li>
                                    <a class="dropdown-item" href="<?php echo function_exists('getStaticCleanUrl') ? getStaticCleanUrl('contact', $currentLang) : "/pages/contact.php?lang={$currentLang}"; ?>" 
                                       title="<?php echo ($currentLang === 'tr') ? 'İletişim' : 'Contact'; ?>">
                                        <i class="fas fa-envelope me-2"></i>
                                        <?php echo ($currentLang === 'tr') ? 'İletişim' : 'Contact'; ?>
                                    </a>
                                </li>
                                
                                <!-- Privacy -->
                                <li>
                                    <a class="dropdown-item" href="<?php echo function_exists('getStaticCleanUrl') ? getStaticCleanUrl('privacy', $currentLang) : "/pages/privacy.php?lang={$currentLang}"; ?>" 
                                       title="<?php echo ($currentLang === 'tr') ? 'Gizlilik Politikası' : 'Privacy Policy'; ?>">
                                        <i class="fas fa-shield-alt me-2"></i>
                                        <?php echo ($currentLang === 'tr') ? 'Gizlilik Politikası' : 'Privacy Policy'; ?>
                                    </a>
                                </li>
                                
                                <!-- Terms -->
                                <li>
                                    <a class="dropdown-item" href="<?php echo function_exists('getStaticCleanUrl') ? getStaticCleanUrl('terms', $currentLang) : "/pages/terms.php?lang={$currentLang}"; ?>" 
                                       title="<?php echo ($currentLang === 'tr') ? 'Kullanım Şartları' : 'Terms of Service'; ?>">
                                        <i class="fas fa-file-contract me-2"></i>
                                        <?php echo ($currentLang === 'tr') ? 'Kullanım Şartları' : 'Terms of Service'; ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    
                    <!-- Language Selector -->
                    <div class="dropdown ms-2">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false" 
                                title="<?php echo ($currentLang === 'tr') ? 'Dil Seçimi' : 'Language Selection'; ?>">
                            <i class="fas fa-globe me-1"></i> 
                            <span class="d-none d-md-inline"><?php echo strtoupper($currentLang); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item <?php echo ($currentLang === 'tr') ? 'active' : ''; ?>" 
                                   href="<?php echo function_exists('getLanguageCleanUrl') ? getLanguageCleanUrl('tr') : getLanguageUrl('tr'); ?>" 
                                   title="Türkçe">
                                    🇹🇷 Türkçe
                                    <?php if ($currentLang === 'tr'): ?>
                                        <i class="fas fa-check text-success ms-2"></i>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($currentLang === 'en') ? 'active' : ''; ?>" 
                                   href="<?php echo function_exists('getLanguageCleanUrl') ? getLanguageCleanUrl('en') : getLanguageUrl('en'); ?>" 
                                   title="English">
                                    🇺🇸 English
                                    <?php if ($currentLang === 'en'): ?>
                                        <i class="fas fa-check text-success ms-2"></i>
                                    <?php endif; ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Main Content Wrapper -->
    <main class="main-content">