<?php
// config/url-helpers.php - Clean URL Generator Functions

/**
 * Clean URL mapping for tools
 */
define('TOOL_URL_MAP', [
    'tr' => [
        'bmi-calculator' => 'bmi-hesaplayici',
        'loan-calculator' => 'kredi-hesaplayici',
        'currency-converter' => 'doviz-cevirici',
        'qr-code-generator' => 'qr-kod-uretici',
        'password-generator' => 'sifre-uretici',
        'text-analyzer' => 'metin-analizi',
        'age-calculator' => 'yas-hesaplayici',
        'unit-converter' => 'olcu-birimi-cevirici',
        'color-converter' => 'renk-cevirici',
        'calorie-calculator' => 'kalori-hesaplayici'
    ],
    'en' => [
        'bmi-calculator' => 'bmi-calculator',
        'loan-calculator' => 'loan-calculator',
        'currency-converter' => 'currency-converter',
        'qr-code-generator' => 'qr-code-generator',
        'password-generator' => 'password-generator',
        'text-analyzer' => 'text-analyzer',
        'age-calculator' => 'age-calculator',
        'unit-converter' => 'unit-converter',
        'color-converter' => 'color-converter',
        'calorie-calculator' => 'calorie-calculator'
    ]
]);

/**
 * Category URL mapping
 */
define('CATEGORY_URL_MAP', [
    'tr' => [
        'finance' => 'finans-araclari',
        'health' => 'saglik-araclari',
        'web' => 'web-araclari',
        'converter' => 'cevirici-araclar',
        'utility' => 'pratik-araclar'
    ],
    'en' => [
        'finance' => 'finance-tools',
        'health' => 'health-tools',
        'web' => 'web-tools',
        'converter' => 'converter-tools',
        'utility' => 'utility-tools'
    ]
]);

/**
 * Static page URL mapping
 */
define('STATIC_URL_MAP', [
    'tr' => [
        'about' => 'hakkimizda',
        'contact' => 'iletisim',
        'privacy' => 'gizlilik-politikasi',
        'terms' => 'kullanim-sartlari'
    ],
    'en' => [
        'about' => 'about',
        'contact' => 'contact', 
        'privacy' => 'privacy-policy',
        'terms' => 'terms-of-service'
    ]
]);

/**
 * Generate clean tool URL
 */
function getToolCleanUrl($toolId, $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }
    
    $cleanSlug = TOOL_URL_MAP[$lang][$toolId] ?? $toolId;
    return "/{$lang}/{$cleanSlug}/";
}

/**
 * Generate clean category URL  
 */
function getCategoryCleanUrl($categoryId, $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }
    
    $cleanSlug = CATEGORY_URL_MAP[$lang][$categoryId] ?? $categoryId;
    return "/{$lang}/{$cleanSlug}/";
}

/**
 * Generate clean static page URL
 */
function getStaticCleanUrl($pageId, $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }
    
    $cleanSlug = STATIC_URL_MAP[$lang][$pageId] ?? $pageId;
    return "/{$lang}/{$cleanSlug}/";
}

/**
 * Enhanced getToolInfo with clean URLs
 */
function getToolInfoWithCleanUrl($toolId, $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }
    
    if (!isset(TOOLS_LIST[$toolId])) {
        return null;
    }
    
    $tool = TOOLS_LIST[$toolId];
    $toolInfo = $tool[$lang] ?? null;
    
    if ($toolInfo) {
        // Add common properties
        $toolInfo['id'] = $toolId;
        $toolInfo['category'] = $tool['category'];
        
        // Clean URL
        $toolInfo['url'] = getToolCleanUrl($toolId, $lang);
        
        // Fallback URL (backward compatibility)
        $toolInfo['fallback_url'] = "/tools/{$toolId}.php?lang={$lang}";
        
        // Canonical URL for SEO
        $baseUrl = getBaseUrl();
        $toolInfo['canonical_url'] = $baseUrl . $toolInfo['url'];
        
        // Meta information
        $toolInfo['meta_title'] = $toolInfo['name'] . ' - ' . SITE_NAME;
        $toolInfo['meta_description'] = $toolInfo['description'];
    }
    
    return $toolInfo;
}

/**
 * Generate breadcrumb with clean URLs
 */
function generateCleanBreadcrumb($items, $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }
    
    if (empty($items)) {
        return '';
    }
    
    $output = '<nav aria-label="breadcrumb" class="breadcrumb-nav">';
    $output .= '<ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">';
    
    foreach ($items as $index => $item) {
        $position = $index + 1;
        $isLast = $index === count($items) - 1;
        
        $output .= '<li class="breadcrumb-item' . ($isLast ? ' active' : '') . '" 
                       itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        
        if (!$isLast && isset($item['url'])) {
            // Convert to clean URL if applicable
            $cleanUrl = $item['url'];
            if (isset($item['type'])) {
                switch ($item['type']) {
                    case 'tool':
                        $cleanUrl = getToolCleanUrl($item['id'], $lang);
                        break;
                    case 'category':
                        $cleanUrl = getCategoryCleanUrl($item['id'], $lang);
                        break;
                    case 'static':
                        $cleanUrl = getStaticCleanUrl($item['id'], $lang);
                        break;
                }
            }
            
            $output .= '<a href="' . safeUrl($cleanUrl) . '" itemprop="item">';
            $output .= '<span itemprop="name">' . safeOutput($item['title']) . '</span>';
            $output .= '</a>';
        } else {
            $output .= '<span itemprop="name">' . safeOutput($item['title']) . '</span>';
        }
        
        $output .= '<meta itemprop="position" content="' . $position . '">';
        $output .= '</li>';
    }
    
    $output .= '</ol></nav>';
    
    return $output;
}

/**
 * Generate sitemap with clean URLs
 */
function generateSitemapUrls($lang = 'tr') {
    $baseUrl = getBaseUrl();
    $urls = [];
    
    // Homepage
    $urls[] = [
        'url' => $baseUrl . "/{$lang}/",
        'changefreq' => 'daily',
        'priority' => '1.0'
    ];
    
    // Tools
    foreach (TOOLS_LIST as $toolId => $tool) {
        $urls[] = [
            'url' => $baseUrl . getToolCleanUrl($toolId, $lang),
            'changefreq' => 'weekly',
            'priority' => '0.8'
        ];
    }
    
    // Categories
    foreach (TOOL_CATEGORIES as $categoryId => $categoryNames) {
        $urls[] = [
            'url' => $baseUrl . getCategoryCleanUrl($categoryId, $lang),
            'changefreq' => 'weekly', 
            'priority' => '0.7'
        ];
    }
    
    // Static pages
    $staticPages = ['about', 'contact', 'privacy', 'terms'];
    foreach ($staticPages as $pageId) {
        $urls[] = [
            'url' => $baseUrl . getStaticCleanUrl($pageId, $lang),
            'changefreq' => 'monthly',
            'priority' => '0.6'
        ];
    }
    
    return $urls;
}

/**
 * Redirect old URLs to new clean URLs
 */
function redirectToCleanUrl() {
    $requestUri = $_SERVER['REQUEST_URI'];
    $currentLang = getCurrentLanguage();
    
    // Check if it's an old tool URL
    if (preg_match('/\/tools\/([a-z-]+)\.php/', $requestUri, $matches)) {
        $toolId = $matches[1];
        if (isset(TOOL_URL_MAP[$currentLang][$toolId])) {
            $cleanUrl = getToolCleanUrl($toolId, $currentLang);
            header("Location: {$cleanUrl}", true, 301);
            exit();
        }
    }
    
    // Check if it's an old category URL
    if (preg_match('/\/pages\/category\.php\?category=([a-z]+)/', $requestUri, $matches)) {
        $categoryId = $matches[1];
        if (isset(CATEGORY_URL_MAP[$currentLang][$categoryId])) {
            $cleanUrl = getCategoryCleanUrl($categoryId, $currentLang);
            header("Location: {$cleanUrl}", true, 301);
            exit();
        }
    }
    
    // Check if it's an old static page URL
    if (preg_match('/\/pages\/([a-z]+)\.php/', $requestUri, $matches)) {
        $pageId = $matches[1];
        if (isset(STATIC_URL_MAP[$currentLang][$pageId])) {
            $cleanUrl = getStaticCleanUrl($pageId, $currentLang);
            header("Location: {$cleanUrl}", true, 301);
            exit();
        }
    }
}

/**
 * Get current page info from clean URL
 */
function getCurrentPageInfo() {
    $requestUri = trim($_SERVER['REQUEST_URI'], '/');
    $segments = explode('/', $requestUri);
    
    if (count($segments) >= 2) {
        $lang = $segments[0];
        $slug = $segments[1];
        
        // Check if it's a tool
        foreach (TOOL_URL_MAP[$lang] ?? [] as $toolId => $toolSlug) {
            if ($toolSlug === $slug) {
                return [
                    'type' => 'tool',
                    'id' => $toolId,
                    'lang' => $lang
                ];
            }
        }
        
        // Check if it's a category
        foreach (CATEGORY_URL_MAP[$lang] ?? [] as $categoryId => $categorySlug) {
            if ($categorySlug === $slug) {
                return [
                    'type' => 'category',
                    'id' => $categoryId,
                    'lang' => $lang
                ];
            }
        }
        
        // Check if it's a static page
        foreach (STATIC_URL_MAP[$lang] ?? [] as $pageId => $pageSlug) {
            if ($pageSlug === $slug) {
                return [
                    'type' => 'static',
                    'id' => $pageId,
                    'lang' => $lang
                ];
            }
        }
    }
    
    return null;
}

/**
 * Enhanced language URL generator with clean URLs
 */
function getLanguageCleanUrl($targetLang) {
    $pageInfo = getCurrentPageInfo();
    
    if ($pageInfo) {
        switch ($pageInfo['type']) {
            case 'tool':
                return getToolCleanUrl($pageInfo['id'], $targetLang);
            case 'category':
                return getCategoryCleanUrl($pageInfo['id'], $targetLang);
            case 'static':
                return getStaticCleanUrl($pageInfo['id'], $targetLang);
        }
    }
    
    // Fallback to homepage
    return "/{$targetLang}/";
}
?>