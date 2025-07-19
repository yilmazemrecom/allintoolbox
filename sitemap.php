<?php
// sitemap.php - SEO-Optimized XML Sitemap Generator
// Generates: sitemap.xml, sitemap_tr.xml, sitemap_en.xml

// Set proper headers for XML sitemap
header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex');

// Enable gzip compression for faster loading
if (function_exists('gzencode') && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
    header('Content-Encoding: gzip');
    ob_start('ob_gzhandler');
}

require_once 'config/config.php';
require_once 'config/functions.php';

// URL helpers'ı yükle
if (file_exists('config/url-helpers.php')) {
    require_once 'config/url-helpers.php';
}

// Request URI'den sitemap türünü belirle
$requestUri = $_SERVER['REQUEST_URI'];
$sitemapType = 'index'; // default

// URL patterns to determine sitemap type
if (preg_match('/sitemap\.xml$/', $requestUri)) {
    $sitemapType = 'index';
} elseif (preg_match('/sitemap_([a-z]{2})\.xml$/', $requestUri, $matches)) {
    $sitemapType = $matches[1];
} elseif (isset($_GET['type'])) {
    // Fallback for query parameter
    $sitemapType = $_GET['type'];
}

// Validate sitemap type
$allowedTypes = array_merge(['index'], SUPPORTED_LANGUAGES);
if (!in_array($sitemapType, $allowedTypes)) {
    http_response_code(404);
    exit('Invalid sitemap type');
}

$baseUrl = 'https://allintoolbox.com';
$currentTime = time();

// Generate appropriate sitemap
if ($sitemapType === 'index') {
    generateMainSitemapIndex($baseUrl);
} else {
    // Language-specific sitemap
    generateLanguageSitemap($baseUrl, $sitemapType);
}

/**
 * Ana sitemap.xml index oluştur - Google Search Console uyumlu
 */
function generateMainSitemapIndex($baseUrl) {
    global $currentTime;
    
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<!-- Main Sitemap Index - AllInToolbox.com -->' . "\n";
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // Dil-specific sitemap dosyaları
    foreach (SUPPORTED_LANGUAGES as $lang) {
        echo "  <sitemap>\n";
        echo "    <loc>" . htmlspecialchars($baseUrl . '/sitemap_' . $lang . '.xml', ENT_XML1) . "</loc>\n";
        echo "    <lastmod>" . date('Y-m-d\TH:i:s\Z', $currentTime) . "</lastmod>\n";
        echo "  </sitemap>\n";
    }
    
    echo '</sitemapindex>' . "\n";
}

/**
 * Dil-specific sitemap oluştur (sitemap_tr.xml, sitemap_en.xml)
 */
function generateLanguageSitemap($baseUrl, $lang) {
    global $currentTime;
    
    if (!in_array($lang, SUPPORTED_LANGUAGES)) {
        http_response_code(404);
        exit('Language not supported');
    }
    
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<!-- SEO-Optimized Sitemap for ' . strtoupper($lang) . ' - AllInToolbox.com -->' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
    echo '        xmlns:xhtml="http://www.w3.org/1999/xhtml"' . "\n";
    echo '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
    
    // Ana sayfa
    $homepageImages = getHomepageImages($lang);
    generateUrlEntry($baseUrl, "/{$lang}/", $lang, 'daily', '1.0', 'homepage', $homepageImages);
    
    // Tool sayfaları
    foreach (TOOLS_LIST as $toolId => $tool) {
        $toolUrl = function_exists('getToolCleanUrl') ? 
                   getToolCleanUrl($toolId, $lang) : 
                   "/tools/{$toolId}.php?lang={$lang}";
        
        $images = getToolImages($toolId, $lang);
        generateUrlEntry($baseUrl, $toolUrl, $lang, 'weekly', '0.8', 'tool', $images);
    }
    
    // Kategori sayfaları  
    foreach (TOOL_CATEGORIES as $categoryId => $categoryNames) {
        $categoryUrl = function_exists('getCategoryCleanUrl') ? 
                       getCategoryCleanUrl($categoryId, $lang) : 
                       "/pages/category.php?category={$categoryId}&lang={$lang}";
        
        generateUrlEntry($baseUrl, $categoryUrl, $lang, 'weekly', '0.7', 'category');
    }
    
    // Statik sayfalar
    $staticPages = ['about', 'contact', 'privacy', 'terms'];
    foreach ($staticPages as $pageId) {
        $pageUrl = function_exists('getStaticCleanUrl') ? 
                   getStaticCleanUrl($pageId, $lang) : 
                   "/pages/{$pageId}.php?lang={$lang}";
        
        generateUrlEntry($baseUrl, $pageUrl, $lang, 'monthly', '0.6', 'static');
    }
    
    echo '</urlset>' . "\n";
}

/**
 * SEO-optimized URL entry oluştur (hreflang desteği ile)
 */
function generateUrlEntry($baseUrl, $url, $currentLang, $changefreq, $priority, $pageType = 'page', $images = []) {
    global $currentTime;
    
    $escapedUrl = htmlspecialchars($baseUrl . $url, ENT_XML1);
    
    echo "  <url>\n";
    echo "    <loc>{$escapedUrl}</loc>\n";
    
    $lastmod = getPageLastModified($url, $pageType);
    echo "    <lastmod>" . date('Y-m-d\TH:i:s\Z', $lastmod) . "</lastmod>\n";
    echo "    <changefreq>{$changefreq}</changefreq>\n";
    echo "    <priority>{$priority}</priority>\n";
    
    // Hreflang alternatives - Google SEO best practices
    foreach (SUPPORTED_LANGUAGES as $lang) {
        $locale = $lang === 'tr' ? 'tr-TR' : 'en-US';
        if ($lang === $currentLang) {
            // Mevcut dil için canonical URL
            echo "    <xhtml:link rel=\"alternate\" hreflang=\"{$locale}\" href=\"{$escapedUrl}\" />\n";
        } else {
            // Diğer dil için çevrilmiş URL
            $altUrl = convertUrlToLanguage($url, $currentLang, $lang);
            $escapedAltUrl = htmlspecialchars($baseUrl . $altUrl, ENT_XML1);
            echo "    <xhtml:link rel=\"alternate\" hreflang=\"{$locale}\" href=\"{$escapedAltUrl}\" />\n";
        }
    }
    
    // x-default hreflang ekle
    $defaultLang = DEFAULT_LANGUAGE;
    if ($currentLang === $defaultLang) {
        echo "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$escapedUrl}\" />\n";
    } else {
        $defaultUrl = convertUrlToLanguage($url, $currentLang, $defaultLang);
        $escapedDefaultUrl = htmlspecialchars($baseUrl . $defaultUrl, ENT_XML1);
        echo "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$escapedDefaultUrl}\" />\n";
    }
    
    // Image sitemap support
    foreach ($images as $image) {
        echo "    <image:image>\n";
        echo "      <image:loc>" . htmlspecialchars($image['url'], ENT_XML1) . "</image:loc>\n";
        if (!empty($image['title'])) {
            echo "      <image:title>" . htmlspecialchars($image['title'], ENT_XML1) . "</image:title>\n";
        }
        if (!empty($image['caption'])) {
            echo "      <image:caption>" . htmlspecialchars($image['caption'], ENT_XML1) . "</image:caption>\n";
        }
        echo "    </image:image>\n";
    }
    
    echo "  </url>\n";
}

/**
 * URL'yi başka bir dile çevir
 */
function convertUrlToLanguage($url, $fromLang, $toLang) {
    // Ana sayfa
    if ($url === "/{$fromLang}/") {
        return "/{$toLang}/";
    }
    
    // URL patterns
    $urlParts = explode('/', trim($url, '/'));
    if (count($urlParts) < 2) return $url;
    
    $urlLang = $urlParts[0];
    $slug = $urlParts[1];
    
    // Tool URL'leri
    if (defined('TOOL_URL_MAP')) {
        foreach (TOOL_URL_MAP[$fromLang] as $toolId => $fromSlug) {
            if ($fromSlug === $slug) {
                $toSlug = TOOL_URL_MAP[$toLang][$toolId] ?? $slug;
                return "/{$toLang}/{$toSlug}/";
            }
        }
    }
    
    // Category URL'leri
    if (defined('CATEGORY_URL_MAP')) {
        foreach (CATEGORY_URL_MAP[$fromLang] as $categoryId => $fromSlug) {
            if ($fromSlug === $slug) {
                $toSlug = CATEGORY_URL_MAP[$toLang][$categoryId] ?? $slug;
                return "/{$toLang}/{$toSlug}/";
            }
        }
    }
    
    // Static page URL'leri
    if (defined('STATIC_URL_MAP')) {
        foreach (STATIC_URL_MAP[$fromLang] as $pageId => $fromSlug) {
            if ($fromSlug === $slug) {
                $toSlug = STATIC_URL_MAP[$toLang][$pageId] ?? $slug;
                return "/{$toLang}/{$toSlug}/";
            }
        }
    }
    
    // Fallback: sadece dili değiştir
    return "/{$toLang}/{$slug}/";
}

/**
 * Sayfa son değiştirilme tarihini belirle
 */
function getPageLastModified($url, $pageType) {
    global $currentTime;
    
    switch ($pageType) {
        case 'homepage':
            return $currentTime - rand(0, 86400); // Son 24 saat
            
        case 'tool':
            return $currentTime - rand(0, 604800); // Son 1 hafta
            
        case 'category':
            return $currentTime - rand(0, 2592000); // Son 1 ay
            
        case 'static':
            return $currentTime - rand(2592000, 7776000); // 1-3 ay önce
            
        default:
            return $currentTime - rand(0, 86400);
    }
}

/**
 * Tool sayfası için görsel listesi
 */
function getToolImages($toolId, $lang) {
    $baseUrl = 'https://allintoolbox.com';
    $images = [];
    
    $toolInfo = TOOLS_LIST[$toolId] ?? null;
    if ($toolInfo && isset($toolInfo[$lang])) {
        $images[] = [
            'url' => $baseUrl . "/assets/images/tools/{$toolId}-preview.jpg",
            'title' => $toolInfo[$lang]['name'] . " - " . SITE_NAME,
            'caption' => $toolInfo[$lang]['description']
        ];
        
        $images[] = [
            'url' => $baseUrl . "/assets/images/tools/{$toolId}-screenshot.jpg",
            'title' => $toolInfo[$lang]['name'] . " Screenshot",
            'caption' => "How to use " . $toolInfo[$lang]['name']
        ];
    }
    
    return $images;
}

/**
 * Ana sayfa için görsel listesi
 */
function getHomepageImages($lang) {
    $baseUrl = 'https://allintoolbox.com';
    $images = [];
    
    $images[] = [
        'url' => $baseUrl . "/assets/images/logo.png",
        'title' => SITE_NAME . " Logo",
        'caption' => META_INFO[$lang]['description']
    ];
    
    $images[] = [
        'url' => $baseUrl . "/assets/images/homepage-hero.jpg",
        'title' => META_INFO[$lang]['title'],
        'caption' => META_INFO[$lang]['og_description']
    ];
    
    // Featured tools
    foreach (array_slice(TOOLS_LIST, 0, 5) as $toolId => $tool) {
        $images[] = [
            'url' => $baseUrl . "/assets/images/tools/{$toolId}-preview.jpg",
            'title' => $tool[$lang]['name'] ?? $toolId,
            'caption' => $tool[$lang]['description'] ?? ''
        ];
    }
    
    return $images;
}

/**
 * Sitemap istatistikleri ve validasyon
 */
function getSitemapStats($lang) {
    $stats = [
        'homepage' => 1,
        'tools' => count(TOOLS_LIST),
        'categories' => count(TOOL_CATEGORIES),
        'static_pages' => 4,
        'total' => 0,
        'images' => 0,
        'hreflang_pairs' => 0
    ];
    
    $stats['total'] = $stats['homepage'] + $stats['tools'] + $stats['categories'] + $stats['static_pages'];
    $stats['images'] = ($stats['tools'] * 2) + 7;
    $stats['hreflang_pairs'] = $stats['total'] * count(SUPPORTED_LANGUAGES);
    
    return $stats;
}

/**
 * XML sitemap validasyon
 */
function validateSitemapLimits($stats) {
    $maxUrls = 50000;
    $maxFileSize = 10 * 1024 * 1024; // 10MB
    
    if ($stats['total'] > $maxUrls) {
        error_log("Sitemap URL limit exceeded: {$stats['total']} > {$maxUrls}");
        return false;
    }
    
    return true;
}
?>