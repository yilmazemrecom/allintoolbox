<?php
// includes/header.php - DÜZELTİLMİŞ VERSİYON

$currentLang = getCurrentLanguage();

// Mevcut sayfanın path'ini al
$currentPath = $_SERVER['REQUEST_URI'];
$isHomePage = ($currentPath === '/' || strpos($currentPath, '/pages/home.php') !== false);
$isToolPage = (strpos($currentPath, '/tools/') !== false);

// Base URL'i belirle - tool sayfalarında farklı olacak
$baseUrl = $isToolPage ? '..' : '';
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
    <link rel="apple-touch-icon" href="<?php echo $baseUrl; ?>/assets/images/apple-touch-icon.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/style.css">
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="<?php echo $baseUrl; ?>/?lang=<?php echo $currentLang; ?>" 
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
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/tools/loan-calculator.php?lang=<?php echo $currentLang; ?>">
                                    <i class="fas fa-calculator me-2"></i><?php echo ($currentLang === 'tr') ? 'Kredi Hesaplayıcı' : 'Loan Calculator'; ?>
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/tools/currency-converter.php?lang=<?php echo $currentLang; ?>">
                                    <i class="fas fa-exchange-alt me-2"></i><?php echo ($currentLang === 'tr') ? 'Döviz Çevirici' : 'Currency Converter'; ?>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/pages/category.php?category=finance&lang=<?php echo $currentLang; ?>">
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
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/tools/bmi-calculator.php?lang=<?php echo $currentLang; ?>">
                                    <i class="fas fa-weight me-2"></i><?php echo ($currentLang === 'tr') ? 'BMI Hesaplayıcı' : 'BMI Calculator'; ?>
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/tools/calorie-calculator.php?lang=<?php echo $currentLang; ?>">
                                    <i class="fas fa-apple-alt me-2"></i><?php echo ($currentLang === 'tr') ? 'Kalori Hesaplayıcı' : 'Calorie Calculator'; ?>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/pages/category.php?category=health&lang=<?php echo $currentLang; ?>">
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
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/tools/qr-code-generator.php?lang=<?php echo $currentLang; ?>">
                                    <i class="fas fa-qrcode me-2"></i><?php echo ($currentLang === 'tr') ? 'QR Kod Üretici' : 'QR Code Generator'; ?>
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/tools/password-generator.php?lang=<?php echo $currentLang; ?>">
                                    <i class="fas fa-key me-2"></i><?php echo ($currentLang === 'tr') ? 'Şifre Üretici' : 'Password Generator'; ?>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/pages/category.php?category=web&lang=<?php echo $currentLang; ?>">
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
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/tools/unit-converter.php?lang=<?php echo $currentLang; ?>">
                                    <i class="fas fa-ruler me-2"></i><?php echo ($currentLang === 'tr') ? 'Ölçü Birimi Çevirici' : 'Unit Converter'; ?>
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/tools/color-converter.php?lang=<?php echo $currentLang; ?>">
                                    <i class="fas fa-palette me-2"></i><?php echo ($currentLang === 'tr') ? 'Renk Çevirici' : 'Color Converter'; ?>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/pages/category.php?category=converter&lang=<?php echo $currentLang; ?>">
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
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/tools/text-analyzer.php?lang=<?php echo $currentLang; ?>">
                                    <i class="fas fa-file-alt me-2"></i><?php echo ($currentLang === 'tr') ? 'Metin Analizi' : 'Text Analyzer'; ?>
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/tools/age-calculator.php?lang=<?php echo $currentLang; ?>">
                                    <i class="fas fa-birthday-cake me-2"></i><?php echo ($currentLang === 'tr') ? 'Yaş Hesaplayıcı' : 'Age Calculator'; ?>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/pages/category.php?category=utility&lang=<?php echo $currentLang; ?>">
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
                                    <a class="dropdown-item" href="<?php echo $baseUrl; ?>/pages/about.php?lang=<?php echo $currentLang; ?>" 
                                       title="<?php echo ($currentLang === 'tr') ? 'Hakkımızda' : 'About Us'; ?>">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <?php echo ($currentLang === 'tr') ? 'Hakkımızda' : 'About Us'; ?>
                                    </a>
                                </li>
                                
                                <!-- Contact -->
                                <li>
                                    <a class="dropdown-item" href="<?php echo $baseUrl; ?>/pages/contact.php?lang=<?php echo $currentLang; ?>" 
                                       title="<?php echo ($currentLang === 'tr') ? 'İletişim' : 'Contact'; ?>">
                                        <i class="fas fa-envelope me-2"></i>
                                        <?php echo ($currentLang === 'tr') ? 'İletişim' : 'Contact'; ?>
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
                                   href="<?php echo getLanguageUrl('tr'); ?>" 
                                   title="Türkçe">
                                    🇹🇷 Türkçe
                                    <?php if ($currentLang === 'tr'): ?>
                                        <i class="fas fa-check text-success ms-2"></i>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($currentLang === 'en') ? 'active' : ''; ?>" 
                                   href="<?php echo getLanguageUrl('en'); ?>" 
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