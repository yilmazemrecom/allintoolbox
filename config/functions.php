<?php
// config/functions.php - TEMİZ VERSİYON

/**
 * Dil algılama
 */
function detectLanguage() {
    // GET parametresinden
    if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGUAGES)) {
        return $_GET['lang'];
    }
    
    // Session'dan
    if (isset($_SESSION['language']) && in_array($_SESSION['language'], SUPPORTED_LANGUAGES)) {
        return $_SESSION['language'];
    }
    
    // Browser'dan
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if (in_array($browserLang, SUPPORTED_LANGUAGES)) {
            return $browserLang;
        }
    }
    
    return DEFAULT_LANGUAGE;
}

/**
 * Dil ayarla
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
 * Dil URL'si oluştur - DÜZELTİLMİŞ
 */
function getLanguageUrl($targetLang) {
    $currentUrl = $_SERVER['REQUEST_URI'];
    
    // Query parametrelerini ayrıştır
    $urlParts = parse_url($currentUrl);
    $path = $urlParts['path'] ?? '/';
    
    // Özel durumlar için kontrol
    if ($path === '/tr/' || $path === '/en/' || $path === '/') {
        // Ana sayfa için
        return "/?lang={$targetLang}";
    }
    
    // Diğer sayfalar için lang parametresini değiştir
    parse_str($urlParts['query'] ?? '', $queryParams);
    $queryParams['lang'] = $targetLang;
    
    return $path . '?' . http_build_query($queryParams);
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
            $loadedLanguages[$lang] = [];
        }
    }
    
    return $loadedLanguages[$lang];
}

/**
 * Çeviri fonksiyonu
 */
function __($key, $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }
    
    $translations = loadLanguage($lang);
    return $translations[$key] ?? $key;
}

/**
 * Tool bilgilerini al
 */
function getToolInfo($toolId, $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }
    
    $tools = TOOLS_LIST;
    $toolInfo = $tools[$toolId][$lang] ?? null;
    
    if ($toolInfo) {
        // Basit URL - sadece query parameter
        $toolInfo['url'] = "/tools/{$toolId}.php?lang={$lang}";
    }
    
    return $toolInfo;
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
            $toolInfo = getToolInfo($id, $lang);
            if ($toolInfo) {
                $categoryTools[$id] = $toolInfo;
            }
        }
    }
    
    return $categoryTools;
}

/**
 * Güvenli HTML çıktısı
 */
function safeOutput($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
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