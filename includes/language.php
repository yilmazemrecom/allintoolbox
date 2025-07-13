<?php
// includes/language.php

// Mevcut dili tespit et
$current_language = 'tr';
$current_path = $_SERVER['REQUEST_URI'];

if (strpos($current_path, '/en/') === 0) {
    $current_language = 'en';
} elseif (strpos($current_path, '/tr/') === 0) {
    $current_language = 'tr';
}

// Uygun dil dosyasını yükle
$lang_file = __DIR__ . '/../languages/' . $current_language . '.php';

if (file_exists($lang_file)) {
    include $lang_file;
} else {
    // Varsayılan olarak Türkçe yükle
    include __DIR__ . '/../languages/tr.php';
}

// Dil değişkeni global olarak erişilebilir yap
global $lang;
?>