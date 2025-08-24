<?php
// config/functions.php - ENHANCED VERSION

/**
 * Dil algılama - İyileştirilmiş
 */
function detectLanguage() {
    // 1. URL parametresinden (öncelik: manual selection)
    if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGUAGES)) {
        return $_GET['lang'];
    }
    
    // 2. Session'dan (kullanıcı tercihi)
    if (isset($_SESSION['language']) && in_array($_SESSION['language'], SUPPORTED_LANGUAGES)) {
        return $_SESSION['language'];
    }
    
    // 3. Cookie'den (persistent preference)
    if (isset($_COOKIE['language']) && in_array($_COOKIE['language'], SUPPORTED_LANGUAGES)) {
        return $_COOKIE['language'];
    }
    
    // 4. URL path'den (/tr/, /en/)
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    if (preg_match('#^/([a-z]{2})/#', $requestUri, $matches)) {
        $pathLang = $matches[1];
        if (in_array($pathLang, SUPPORTED_LANGUAGES)) {
            return $pathLang;
        }
    }
    
    // 5. Browser dilinden (Accept-Language header)
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        
        // Parse Accept-Language header (e.g., "tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7")
        preg_match_all('/([a-z]{2})(?:-[A-Z]{2})?(?:;q=([0-9\.]+))?/', 
                       $acceptLanguage, $matches, PREG_SET_ORDER);
        
        $languages = [];
        foreach ($matches as $match) {
            $lang = $match[1];
            $quality = isset($match[2]) ? (float)$match[2] : 1.0;
            
            if (in_array($lang, SUPPORTED_LANGUAGES)) {
                $languages[$lang] = $quality;
            }
        }
        
        // Sort by quality (highest first)
        arsort($languages);
        
        if (!empty($languages)) {
            return array_key_first($languages);
        }
    }
    
    // 6. Coğrafi konum bazlı (opsiyonel)
    // Bu kısım IP geolocation servisi ile geliştirilebilir
    
    // 7. Varsayılan dil
    return DEFAULT_LANGUAGE;
}

/**
 * Dil ayarla ve kaydet
 */
function setLanguage($lang) {
    if (in_array($lang, SUPPORTED_LANGUAGES)) {
        $_SESSION['language'] = $lang;
        
        // Cookie'ye de kaydet (30 gün)
        setcookie('language', $lang, time() + (30 * 24 * 60 * 60), '/', '', true, true);
        
        return true;
    }
    return false;
}

/**
 * Mevcut dili al
 */
function getCurrentLanguage() {
    static $currentLang = null;
    
    if ($currentLang === null) {
        $currentLang = $_SESSION['language'] ?? detectLanguage();
    }
    
    return $currentLang;
}

/**
 * Dil URL'si oluştur - GELİŞTİRİLMİŞ
 */
function getLanguageUrl($targetLang) {
    $currentUrl = $_SERVER['REQUEST_URI'];
    
    // Parse current URL
    $urlParts = parse_url($currentUrl);
    $path = $urlParts['path'] ?? '/';
    $query = $urlParts['query'] ?? '';
    
    // Remove existing lang parameter from query
    parse_str($query, $queryParams);
    unset($queryParams['lang']);
    
    // Handle special cases
    if ($path === '/' || $path === '/tr/' || $path === '/en/') {
        // Home page
        $newUrl = "/?lang={$targetLang}";
    } else {
        // Other pages - preserve path and add/update lang parameter
        $queryParams['lang'] = $targetLang;
        $queryString = http_build_query($queryParams);
        $newUrl = $path . ($queryString ? '?' . $queryString : '');
    }
    
    return $newUrl;
}

/**
 * Dil dosyasını yükle - Caching ile
 */
function loadLanguage($lang) {
    static $loadedLanguages = [];
    
    if (!isset($loadedLanguages[$lang])) {
        $langFile = __DIR__ . '/../languages/' . $lang . '.php';
        
        if (file_exists($langFile)) {
            $loadedLanguages[$lang] = include $langFile;
        } else {
            // Fallback to default language
            $defaultLangFile = __DIR__ . '/../languages/' . DEFAULT_LANGUAGE . '.php';
            if (file_exists($defaultLangFile)) {
                $loadedLanguages[$lang] = include $defaultLangFile;
            } else {
                $loadedLanguages[$lang] = [];
            }
        }
    }
    
    return $loadedLanguages[$lang];
}

/**
 * Çeviri fonksiyonu - Placeholder desteği ile
 */
function __($key, $placeholders = [], $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }
    
    $translations = loadLanguage($lang);
    $text = $translations[$key] ?? $key;
    
    // Replace placeholders if provided
    if (!empty($placeholders) && is_array($placeholders)) {
        foreach ($placeholders as $placeholder => $value) {
            $text = str_replace('{' . $placeholder . '}', $value, $text);
        }
    }
    
    return $text;
}

/**
 * Tool bilgilerini al - Enhanced
 */
function getToolInfo($toolId, $lang = null) {
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
        $toolInfo['url'] = "/tools/{$toolId}.php?lang={$lang}";
        
        // Add canonical URL for SEO
        $baseUrl = getBaseUrl();
        $toolInfo['canonical_url'] = $baseUrl . $toolInfo['url'];
        
        // Add meta information
        $toolInfo['meta_title'] = $toolInfo['name'] . ' - ' . SITE_NAME;
        $toolInfo['meta_description'] = $toolInfo['description'];
    }
    
    return $toolInfo;
}

/**
 * Kategoriye göre tool listesi - Enhanced
 */
function getToolsByCategory($category, $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }
    
    $categoryTools = [];
    
    foreach (TOOLS_LIST as $toolId => $tool) {
        if ($tool['category'] === $category) {
            // Clean URL varsa onu kullan, yoksa normal URL kullan
            if (function_exists('getToolInfoWithCleanUrl')) {
                $toolInfo = getToolInfoWithCleanUrl($toolId, $lang);
            } else {
                $toolInfo = getToolInfo($toolId, $lang);
            }
            
            if ($toolInfo) {
                $categoryTools[$toolId] = $toolInfo;
            }
        }
    }
    
    // Sort by name
    uasort($categoryTools, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
    
    return $categoryTools;
}

/**
 * Tüm kategorileri al
 */
function getAllCategories($lang = null) {
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }
    
    $categories = [];
    
    foreach (TOOL_CATEGORIES as $categoryId => $categoryNames) {
        $categories[$categoryId] = [
            'id' => $categoryId,
            'name' => $categoryNames[$lang] ?? $categoryNames[DEFAULT_LANGUAGE],
            'tools' => getToolsByCategory($categoryId, $lang)
        ];
    }
    
    return $categories;
}

/**
 * Base URL al
 */
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $protocol . $host;
}

/**
 * Güvenli HTML çıktısı
 */
function safeOutput($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Güvenli URL çıktısı
 */
function safeUrl($url) {
    return filter_var($url, FILTER_SANITIZE_URL);
}

/**
 * Reklam alanı oluştur
 */
function renderAdSpace($position = 'banner', $size = 'medium') {
    if (!defined('EZOIC_ENABLED') || !EZOIC_ENABLED) {
        return '';
    }
    
    $adId = 'ezoic-pub-ad-' . $position . '-' . $size;
    return '<div id="' . $adId . '" class="ad-space ad-' . $position . ' ad-' . $size . '"></div>';
}

/**
 * Meta tags oluştur - Enhanced
 */
function generateMetaTags($title = null, $description = null, $keywords = null, $canonical = null, $ogImage = null) {
    $lang = getCurrentLanguage();
    $meta = META_INFO[$lang] ?? META_INFO[DEFAULT_LANGUAGE];
    $baseUrl = getBaseUrl();
    
    // Default values
    $title = $title ?? $meta['title'];
    $description = $description ?? $meta['description'];
    $keywords = $keywords ?? $meta['keywords'];
    $canonical = $canonical ?? getCurrentUrl();
    $ogImage = $ogImage ?? ($baseUrl . '/assets/images/og-image.jpg');
    
    // Basic meta tags
    echo '<title>' . safeOutput($title) . '</title>' . "\n";
    echo '<meta name="description" content="' . safeOutput($description) . '">' . "\n";
    echo '<meta name="keywords" content="' . safeOutput($keywords) . '">' . "\n";
    echo '<link rel="canonical" href="' . safeUrl($canonical) . '">' . "\n";
    
    // Open Graph tags
    echo '<meta property="og:title" content="' . safeOutput($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . safeOutput($description) . '">' . "\n";
    echo '<meta property="og:image" content="' . safeUrl($ogImage) . '">' . "\n";
    echo '<meta property="og:url" content="' . safeUrl($canonical) . '">' . "\n";
    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:site_name" content="' . safeOutput(SITE_NAME) . '">' . "\n";
    echo '<meta property="og:locale" content="' . ($lang === 'tr' ? 'tr_TR' : 'en_US') . '">' . "\n";
    
    // Twitter Card tags
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . safeOutput($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . safeOutput($description) . '">' . "\n";
    echo '<meta name="twitter:image" content="' . safeUrl($ogImage) . '">' . "\n";
    
    // Hreflang tags for multilingual SEO
    foreach (SUPPORTED_LANGUAGES as $supportedLang) {
        $hreflangUrl = str_replace('lang=' . $lang, 'lang=' . $supportedLang, $canonical);
        $locale = $supportedLang === 'tr' ? 'tr-TR' : 'en-US';
        echo '<link rel="alternate" hreflang="' . $locale . '" href="' . safeUrl($hreflangUrl) . '">' . "\n";
    }
    
    // JSON-LD Schema
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => SITE_NAME,
        'url' => $baseUrl,
        'description' => $description,
        'inLanguage' => $lang === 'tr' ? 'tr-TR' : 'en-US',
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => $baseUrl . '/search?q={search_term_string}',
            'query-input' => 'required name=search_term_string'
        ]
    ];
    
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}

/**
 * Mevcut URL'yi al
 */
function getCurrentUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    
    return $protocol . $host . $uri;
}

/**
 * Breadcrumb oluştur - Enhanced
 */
function generateBreadcrumb($items) {
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
            $output .= '<a href="' . safeUrl($item['url']) . '" itemprop="item">';
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
 * Page load time hesapla
 */
function getLoadTime() {
    if (defined('START_TIME')) {
        return round(microtime(true) - START_TIME, 3);
    }
    return 0;
}

// Define START_TIME if not already defined
if (!defined('START_TIME')) {
    define('START_TIME', microtime(true));
}

/**
 * Random string oluştur
 */
function generateRandomString($length = 10) {
    return bin2hex(random_bytes(ceil($length / 2)));
}

/**
 * Email validation
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * URL validation
 */
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Rate limiting check
 */
function checkRateLimit($key, $maxRequests = 60, $timeWindow = 3600) {
    $storageKey = 'rate_limit_' . md5($key);
    
    if (!isset($_SESSION[$storageKey])) {
        $_SESSION[$storageKey] = [
            'count' => 1,
            'timestamp' => time()
        ];
        return true;
    }
    
    $data = $_SESSION[$storageKey];
    
    // Reset if time window passed
    if (time() - $data['timestamp'] > $timeWindow) {
        $_SESSION[$storageKey] = [
            'count' => 1,
            'timestamp' => time()
        ];
        return true;
    }
    
    // Check if limit exceeded
    if ($data['count'] >= $maxRequests) {
        return false;
    }
    
    // Increment counter
    $_SESSION[$storageKey]['count']++;
    return true;
}

/**
 * Log function
 */
function logMessage($message, $level = 'info') {
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        $logFile = __DIR__ . '/../logs/app.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}

/**
 * Error handler
 */
function handleError($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $message = "Error [{$errno}]: {$errstr} in {$errfile} on line {$errline}";
    logMessage($message, 'error');
    
    return true;
}

// Set custom error handler
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    set_error_handler('handleError');
}
?>