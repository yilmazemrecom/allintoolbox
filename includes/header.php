<?php
// includes/header.php - TEMİZ VERSİYON

$currentLang = getCurrentLanguage();
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="AllInToolbox">
    
    <?php
    // Meta tags
    generateMetaTags($pageTitle ?? null, $pageDescription ?? null, $pageKeywords ?? null);
    ?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="/?lang=<?php echo $currentLang; ?>">
                    <span class="fw-bold"><?php echo SITE_NAME; ?></span>
                </a>
                
                <!-- Mobile toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Navigation -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/?lang=<?php echo $currentLang; ?>">
                                <i class="fas fa-home"></i> <?php echo __('home'); ?>
                            </a>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-tools"></i> <?php echo __('tools'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/tools/bmi-calculator.php?lang=<?php echo $currentLang; ?>"><?php echo __('bmi_title'); ?></a></li>
                                <li><a class="dropdown-item" href="/tools/currency-converter.php?lang=<?php echo $currentLang; ?>"><?php echo __('currency_title'); ?></a></li>
                                <li><a class="dropdown-item" href="/tools/qr-code-generator.php?lang=<?php echo $currentLang; ?>"><?php echo __('qr_title'); ?></a></li>
                            </ul>
                        </li>
                    </ul>
                    
                    <!-- Language Selector -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-globe"></i> <?php echo strtoupper($currentLang); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item <?php echo ($currentLang === 'tr') ? 'active' : ''; ?>" 
                                   href="<?php echo getLanguageUrl('tr'); ?>">
                                    <i class="fas fa-flag"></i> Türkçe
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($currentLang === 'en') ? 'active' : ''; ?>" 
                                   href="<?php echo getLanguageUrl('en'); ?>">
                                    <i class="fas fa-flag"></i> English
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>