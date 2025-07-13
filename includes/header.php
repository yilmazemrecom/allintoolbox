<?php
// includes/header.php
$currentLang = getCurrentLanguage();
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="author" content="AllInToolbox">
    
    <?php
    // Sayfa özelinde meta bilgileri varsa kullan, yoksa varsayılanları kullan
    $pageTitle = $pageTitle ?? null;
    $pageDescription = $pageDescription ?? null;
    $pageKeywords = $pageKeywords ?? null;
    $pageCanonical = $pageCanonical ?? null;
    
    generateMetaTags($pageTitle, $pageDescription, $pageKeywords, $pageCanonical);
    ?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo safeOutput($pageTitle ?? META_INFO[$currentLang]['title']); ?>">
    <meta property="og:description" content="<?php echo safeOutput($pageDescription ?? META_INFO[$currentLang]['description']); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo getCurrentUrl(); ?>">
    <meta property="og:image" content="<?php echo BASE_URL; ?>/assets/images/og-image.jpg">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo safeOutput($pageTitle ?? META_INFO[$currentLang]['title']); ?>">
    <meta name="twitter:description" content="<?php echo safeOutput($pageDescription ?? META_INFO[$currentLang]['description']); ?>">
    <meta name="twitter:image" content="<?php echo BASE_URL; ?>/assets/images/og-image.jpg">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "<?php echo SITE_NAME; ?>",
        "url": "<?php echo BASE_URL; ?>",
        "description": "<?php echo safeOutput(META_INFO[$currentLang]['description']); ?>",
        "inLanguage": "<?php echo $currentLang; ?>",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "<?php echo BASE_URL; ?>/<?php echo $currentLang; ?>/?search={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    
    <?php if (EZOIC_ENABLED): ?>
    <!-- Ezoic Script -->
    <script data-ezscrex="false" data-cfasync="false">
    (function(){var a=document.createElement("script");a.type="text/javascript";a.async=!0;a.src="https://go.ezoic.net/ezoic/ezoic.js";var b=document.getElementsByTagName("script")[0];b.parentNode.insertBefore(a,b)})();
    </script>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="/<?php echo $currentLang; ?>/">
                    <span class="fw-bold"><?php echo SITE_NAME; ?></span>
                </a>
                
                <!-- Mobile toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Navigation -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/<?php echo $currentLang; ?>/">
                                <i class="fas fa-home"></i> <?php echo __('home'); ?>
                            </a>
                        </li>
                        
                        <!-- Tools Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                                <i class="fas fa-tools"></i> <?php echo __('tools'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php foreach (TOOL_CATEGORIES as $categoryId => $categoryNames): ?>
                                    <li><h6 class="dropdown-header"><?php echo $categoryNames[$currentLang]; ?></h6></li>
                                    <?php
                                    $categoryTools = getToolsByCategory($categoryId, $currentLang);
                                    $toolCount = 0;
                                    foreach ($categoryTools as $toolId => $tool):
                                        if ($toolCount >= 3) break; // Limit to 3 tools per category in dropdown
                                        $toolCount++;
                                    ?>
                                        <li><a class="dropdown-item" href="<?php echo $tool['url']; ?>"><?php echo $tool['name']; ?></a></li>
                                    <?php endforeach; ?>
                                    <?php if (count($categoryTools) > 3): ?>
                                        <li><a class="dropdown-item text-muted" href="/<?php echo $currentLang; ?>/category/<?php echo $categoryId; ?>.php">
                                            <i class="fas fa-plus"></i> <?php echo __('more'); ?>
                                        </a></li>
                                    <?php endif; ?>
                                    <?php if (array_key_last(TOOL_CATEGORIES) !== $categoryId): ?>
                                        <li><hr class="dropdown-divider"></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="/<?php echo $currentLang; ?>/about.php">
                                <i class="fas fa-info-circle"></i> <?php echo __('about'); ?>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="/<?php echo $currentLang; ?>/contact.php">
                                <i class="fas fa-envelope"></i> <?php echo __('contact'); ?>
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Search -->
                    <div class="d-flex me-3">
                        <form class="d-flex" method="GET" action="/<?php echo $currentLang; ?>/search.php">
                            <input class="form-control me-2" type="search" name="q" placeholder="<?php echo __('search'); ?>..." aria-label="Search" style="min-width: 200px;">
                            <button class="btn btn-outline-success" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Language Selector -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-globe"></i> <?php echo strtoupper($currentLang); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php foreach (SUPPORTED_LANGUAGES as $lang): ?>
                                <li>
                                    <a class="dropdown-item <?php echo ($lang === $currentLang) ? 'active' : ''; ?>" 
                                       href="<?php echo getLanguageUrl($lang); ?>">
                                        <i class="fas fa-flag"></i> 
                                        <?php echo ($lang === 'tr') ? 'Türkçe' : 'English'; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Top Ad Space -->
    <?php echo renderAdSpace('header', 'banner'); ?>

