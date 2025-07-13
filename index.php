<?php
// Ana index.php - Dil yönlendirmesi (mevcut yapıya göre düzeltildi)
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

// pages/home.php dosyasının mevcut olup olmadığını kontrol et
$target_file = __DIR__ . '/pages/home.php';
if (!file_exists($target_file)) {
    // Eğer home.php yoksa 404
    http_response_code(404);
    echo "Home page not found!";
    exit();
}

// Seçilen dile göre home.php'ye yönlendir
header("Location: /{$selected_language}/");
exit();

// detectLanguage() function is now in config/functions.php

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