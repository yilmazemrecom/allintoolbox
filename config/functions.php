<?php
// config/functions.php

/**
 * Dil algılama fonksiyonu - YENİ VERSİYON
 * Query string, session ve browser'dan dil algılar
 */
function detectLanguage() {
    // Önce GET parametresinden kontrol et
    if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGUAGES)) {
        return $_GET['lang'];
    }
    
    // Session'dan kontrol et
    if (isset($_SESSION['language']) && in_array($_SESSION['language'], SUPPORTED_LANGUAGES)) {
        return $_SESSION['language'];
    }
    
    // URL'den kontrol et
    $requestUri = $_SERVER['REQUEST_URI'];
    foreach (SUPPORTED_LANGUAGES as $lang) {
        if (preg_match('/^\/' . $lang . '(\/|$)/', $requestUri)) {
            return $lang;
        }
    }
    
    // Browser dilini kontrol et
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if (in_array($browserLang, SUPPORTED_LANGUAGES)) {
            return $browserLang;
        }
    }
    
    return DEFAULT_LANGUAGE;
}

/**
 * Dil değiştirme fonksiyonu
 */
function setLanguage($lang) {
    if (in_array($lang, SUPPORTED_LANGUAGES)) {
        $_SESSION['language'] = $lang;
        return true;
    }
    return false;
}

/**
 * Mevcut dili al
 */
function getCurrentLanguage() {
    return $_SESSION['language'] ?? detectLanguage();
}

/**
 * Dil URL'si oluştur
 */
function getLanguageUrl($lang) {
    $currentUrl = $_SERVER['REQUEST_URI'];
    $currentLang = getCurrentLanguage();
    
    // Mevcut dil prefix'ini kaldır
    $cleanUrl = str_replace('/' . $currentLang . '/', '/', $currentUrl);
    if ($cleanUrl === '/' . $currentLang) {
        $cleanUrl = '/';
    }
    
    // Yeni dil prefix'ini ekle
    if ($cleanUrl === '/') {
        return '/' . $lang . '/';
    } else {
        return '/' . $lang . $cleanUrl;
    }
}

/**
 * Dil dosyasını yükle
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
 * Metin çevirisi fonksiyonu - YENİ VERSİYON
 */
function __($key, $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }
    
    $translations = loadLanguage($lang);
    
    // Nested key support (örn: 'bmi.title')
    if (strpos($key, '.') !== false) {
        $keys = explode('.', $key);
        $value = $translations;
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $key; // Key bulunamadı
            }
        }
        return $value;
    }
    
    return $translations[$key] ?? $key;
}

/**
 * Kısa çeviri fonksiyonu (alias)
 */
function t($key, $lang = null) {
    return __($key, $lang);
}

/**
 * Tool bilgilerini al
 */
function getToolInfo($toolId, $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }
    
    $tools = TOOLS_LIST;
    return $tools[$toolId][$lang] ?? null;
}

/**
 * Kategoriye göre tool listesi
 */
function getToolsByCategory($category, $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }
    
    $tools = TOOLS_LIST;
    $categoryTools = [];
    
    foreach ($tools as $id => $tool) {
        if ($tool['category'] === $category) {
            $categoryTools[$id] = $tool[$lang];
        }
    }
    
    return $categoryTools;
}

/**
 * URL slug oluştur
 */
function createSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Güvenli HTML çıktısı
 */
function safeOutput($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * JSON response
 */
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Form validation
 */
function validateInput($value, $type = 'string', $required = true) {
    if ($required && empty($value)) {
        return false;
    }
    
    switch ($type) {
        case 'email':
            return filter_var($value, FILTER_VALIDATE_EMAIL);
        case 'number':
            return is_numeric($value);
        case 'float':
            return filter_var($value, FILTER_VALIDATE_FLOAT);
        case 'int':
            return filter_var($value, FILTER_VALIDATE_INT);
        case 'url':
            return filter_var($value, FILTER_VALIDATE_URL);
        default:
            return true;
    }
}

/**
 * Reklam alanı oluştur
 */
function renderAdSpace($position = 'banner', $size = 'medium') {
    if (!EZOIC_ENABLED) {
        return '';
    }
    
    $adId = 'ezoic-pub-ad-' . $position . '-' . $size;
    return '<div id="' . $adId . '" class="ad-space ad-' . $position . ' ad-' . $size . '"></div>';
}

/**
 * Meta tags oluştur
 */
function generateMetaTags($title = null, $description = null, $keywords = null, $canonical = null) {
    $lang = getCurrentLanguage();
    $meta = META_INFO[$lang];
    
    $title = $title ?? $meta['title'];
    $description = $description ?? $meta['description'];
    $keywords = $keywords ?? $meta['keywords'];
    $canonical = $canonical ?? getCurrentUrl();
    
    echo '<title>' . safeOutput($title) . '</title>' . "\n";
    echo '<meta name="description" content="' . safeOutput($description) . '">' . "\n";
    echo '<meta name="keywords" content="' . safeOutput($keywords) . '">' . "\n";
    echo '<link rel="canonical" href="' . safeOutput($canonical) . '">' . "\n";
    
    // Hreflang tags
    foreach (SUPPORTED_LANGUAGES as $hrefLang) {
        $hrefUrl = str_replace('/' . $lang . '/', '/' . $hrefLang . '/', $canonical);
        echo '<link rel="alternate" hreflang="' . $hrefLang . '" href="' . safeOutput($hrefUrl) . '">' . "\n";
    }
}

/**
 * Mevcut URL'yi al
 */
function getCurrentUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Breadcrumb oluştur
 */
function generateBreadcrumb($items) {
    $lang = getCurrentLanguage();
    $output = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    foreach ($items as $item) {
        if (isset($item['url'])) {
            $output .= '<li class="breadcrumb-item"><a href="' . safeOutput($item['url']) . '">' . safeOutput($item['title']) . '</a></li>';
        } else {
            $output .= '<li class="breadcrumb-item active" aria-current="page">' . safeOutput($item['title']) . '</li>';
        }
    }
    
    $output .= '</ol></nav>';
    return $output;
}

/**
 * Sayfa yükleme süresi hesapla
 */
function startTimer() {
    $GLOBALS['start_time'] = microtime(true);
}

function getLoadTime() {
    return round(microtime(true) - $GLOBALS['start_time'], 4);
}

// Timer'ı başlat
startTimer();
?>