<?php
// sitemap.php - Clean URLs ile Sitemap Generator
header('Content-Type: application/xml; charset=utf-8');

require_once 'config/config.php';
require_once 'config/functions.php';

// URL helpers'ı yükle
if (file_exists('config/url-helpers.php')) {
    require_once 'config/url-helpers.php';
}

// Sitemap türünü belirle
$sitemapType = $_GET['type'] ?? 'index';
$baseUrl = 'https://allintoolbox.com';

// Ana sitemap index
if ($sitemapType === 'index') {
    generateSitemapIndex($baseUrl);
} else {
    // Dil-specific sitemap
    generateLanguageSitemap($baseUrl, $sitemapType);
}

/**
 * Ana sitemap index oluştur
 */
function generateSitemapIndex($baseUrl) {
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    foreach (SUPPORTED_LANGUAGES as $lang) {
        echo "  <sitemap>\n";
        echo "    <loc>{$baseUrl}/sitemap-{$lang}.xml</loc>\n";
        echo "    <lastmod>" . date('Y-m-d\TH:i:s\Z') . "</lastmod>\n";
        echo "  </sitemap>\n";
    }
    
    echo '</sitemapindex>' . "\n";
}

/**
 * Dil-specific sitemap oluştur
 */
function generateLanguageSitemap($baseUrl, $lang) {
    if (!in_array($lang, SUPPORTED_LANGUAGES)) {
        http_response_code(404);
        exit();
    }
    
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
    
    // Ana sayfa
    generateUrlEntry($baseUrl, "/{$lang}/", $lang, 'daily', '1.0');
    
    // Tool sayfaları
    foreach (TOOLS_LIST as $toolId => $tool) {
        $toolUrl = function_exists('getToolCleanUrl') ? 
                   getToolCleanUrl($toolId, $lang) : 
                   "/tools/{$toolId}.php?lang={$lang}";
        
        generateUrlEntry($baseUrl, $toolUrl, $lang, 'weekly', '0.8');
    }
    
    // Kategori sayfaları  
    foreach (TOOL_CATEGORIES as $categoryId => $categoryNames) {
        $categoryUrl = function_exists('getCategoryCleanUrl') ? 
                       getCategoryCleanUrl($categoryId, $lang) : 
                       "/pages/category.php?category={$categoryId}&lang={$lang}";
        
        generateUrlEntry($baseUrl, $categoryUrl, $lang, 'weekly', '0.7');
    }
    
    // Statik sayfalar
    $staticPages = ['about', 'contact', 'privacy', 'terms'];
    foreach ($staticPages as $pageId) {
        $pageUrl = function_exists('getStaticCleanUrl') ? 
                   getStaticCleanUrl($pageId, $lang) : 
                   "/pages/{$pageId}.php?lang={$lang}";
        
        generateUrlEntry($baseUrl, $pageUrl, $lang, 'monthly', '0.6');
    }
    
    echo '</urlset>' . "\n";
}

/**
 * URL entry oluştur (hreflang desteği ile)
 */
function generateUrlEntry($baseUrl, $url, $currentLang, $changefreq, $priority) {
    echo "  <url>\n";
    echo "    <loc>{$baseUrl}{$url}</loc>\n";
    echo "    <lastmod>" . date('Y-m-d\TH:i:s\Z') . "</lastmod>\n";
    echo "    <changefreq>{$changefreq}</changefreq>\n";
    echo "    <priority>{$priority}</priority>\n";
    
    // Hreflang alternatives
    foreach (SUPPORTED_LANGUAGES as $lang) {
        if ($lang !== $currentLang) {
            // URL'yi diğer dile çevir
            $altUrl = convertUrlToLanguage($url, $currentLang, $lang);
            $locale = $lang === 'tr' ? 'tr-TR' : 'en-US';
            echo "    <xhtml:link rel=\"alternate\" hreflang=\"{$locale}\" href=\"{$baseUrl}{$altUrl}\" />\n";
        }
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
 * Sitemap stats (debug için)
 */
function getSitemapStats($lang) {
    $stats = [
        'homepage' => 1,
        'tools' => count(TOOLS_LIST),
        'categories' => count(TOOL_CATEGORIES),
        'static_pages' => 4, // about, contact, privacy, terms
        'total' => 0
    ];
    
    $stats['total'] = $stats['homepage'] + $stats['tools'] + $stats['categories'] + $stats['static_pages'];
    
    return $stats;
}
?>