<?php
// Ana index.php - Dil yönlendirmesi
session_start();

// Konfigürasyon dosyalarını yükle
require_once 'config/config.php';
require_once 'config/functions.php';

// Desteklenen diller
$supported_languages = ['tr', 'en'];
$default_language = 'tr';

// Manuel dil seçimi kontrol et (örn: ?lang=en)
if (isset($_GET['lang']) && in_array($_GET['lang'], $supported_languages)) {
    $selected_language = $_GET['lang'];
    $_SESSION['user_language'] = $selected_language;
} 
// Session'da kayıtlı dil var mı?
elseif (isset($_SESSION['user_language']) && in_array($_SESSION['user_language'], $supported_languages)) {
    $selected_language = $_SESSION['user_language'];
} 
// Otomatik dil algılama
else {
    $selected_language = detectLanguage();
    $_SESSION['user_language'] = $selected_language;
}

// Dil klasörünün mevcut olup olmadığını kontrol et
$target_dir = __DIR__ . '/' . $selected_language;
if (!is_dir($target_dir)) {
    // Eğer seçilen dil klasörü yoksa varsayılan dile yönlendir
    $selected_language = $default_language;
    $_SESSION['user_language'] = $selected_language;
}

// Seçilen dile yönlendir
header("Location: /{$selected_language}/");
exit();

/**
 * Otomatik dil algılama fonksiyonu
 */
function detectLanguage() {
    global $default_language, $supported_languages;
    
    // 1. Browser dil tercihi kontrol et
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        
        foreach ($browser_languages as $lang) {
            $lang_code = substr(trim($lang), 0, 2);
            
            // Desteklenen diller arasında var mı kontrol et
            if (in_array($lang_code, $supported_languages)) {
                return $lang_code;
            }
        }
    }
    
    // 2. IP bazlı basit kontrol (opsiyonel)
    $user_ip = getUserIP();
    
    // Localhost'ta varsayılan Türkçe
    if ($user_ip === '127.0.0.1' || $user_ip === '::1' || $user_ip === '0.0.0.0') {
        return 'tr';
    }
    
    // 3. Varsayılan dil
    return $default_language;
}

/**
 * Kullanıcı IP adresini al
 */
function getUserIP() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            // Virgülle ayrılmış IP'ler varsa ilkini al (proxy durumu)
            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            return trim($ip);
        }
    }
    
    return '127.0.0.1';
}
?>