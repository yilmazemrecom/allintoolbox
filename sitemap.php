<?php
// sitemap.php - Otomatik Sitemap Oluşturucu  
// allintoolbox.com için mevcut URL yapısına uygun

header('Content-Type: application/xml; charset=utf-8');

// Konfigürasyon dosyalarını yükle
require_once 'config/config.php';
require_once 'config/functions.php';

// Sitemap XML başlangıcı
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

// Ana sayfalar
$staticPages = [
    'home' => [
        'file' => 'pages/home.php',
        'priority' => '1.0',
        'changefreq' => 'weekly'
    ],
    'about' => [
        'file' => 'pages/about.php',
        'priority' => '0.8',
        'changefreq' => 'monthly'
    ],
    'contact' => [
        'file' => 'pages/contact.php',
        'priority' => '0.7',
        'changefreq' => 'monthly'
    ],
    'privacy' => [
        'file' => 'pages/privacy.php',
        'priority' => '0.5',
        'changefreq' => 'yearly'
    ],
    'terms' => [
        'file' => 'pages/terms.php',
        'priority' => '0.5',
        'changefreq' => 'yearly'
    ]
];

// Desteklenen diller
$languages = ['tr', 'en'];

// Ana sayfalar için sitemap (mevcut URL yapısı: ?lang=tr)
foreach ($staticPages as $page => $config) {
    foreach ($languages as $lang) {
        $url = BASE_URL . '/' . $config['file'] . '?lang=' . $lang;
        
        echo '  <url>' . "\n";
        echo '    <loc>' . htmlspecialchars($url) . '</loc>' . "\n";
        echo '    <lastmod>' . date('c') . '</lastmod>' . "\n";
        echo '    <changefreq>' . $config['changefreq'] . '</changefreq>' . "\n";
        echo '    <priority>' . $config['priority'] . '</priority>' . "\n";
        
        // Hreflang alternatif diller
        foreach ($languages as $altLang) {
            $altUrl = BASE_URL . '/' . $config['file'] . '?lang=' . $altLang;
            echo '    <xhtml:link rel="alternate" hreflang="' . $altLang . '" href="' . htmlspecialchars($altUrl) . '" />' . "\n";
        }
        
        echo '  </url>' . "\n";
    }
}

// .htaccess URL'leri de ekle (/tr/, /en/)
foreach ($languages as $lang) {
    $url = BASE_URL . '/' . $lang . '/';
    
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars($url) . '</loc>' . "\n";
    echo '    <lastmod>' . date('c') . '</lastmod>' . "\n";
    echo '    <changefreq>weekly</changefreq>' . "\n";
    echo '    <priority>1.0</priority>' . "\n";
    
    // Hreflang alternatif diller
    foreach ($languages as $altLang) {
        $altUrl = BASE_URL . '/' . $altLang . '/';
        echo '    <xhtml:link rel="alternate" hreflang="' . $altLang . '" href="' . htmlspecialchars($altUrl) . '" />' . "\n";
    }
    
    echo '  </url>' . "\n";
}

// Tool sayfaları için sitemap (mevcut yapı: tools/tool-name.php?lang=tr)
$toolsList = TOOLS_LIST;
foreach ($toolsList as $toolId => $toolInfo) {
    foreach ($languages as $lang) {
        $url = BASE_URL . '/tools/' . $toolId . '.php?lang=' . $lang;
        
        echo '  <url>' . "\n";
        echo '    <loc>' . htmlspecialchars($url) . '</loc>' . "\n";
        echo '    <lastmod>' . date('c') . '</lastmod>' . "\n";
        echo '    <changefreq>weekly</changefreq>' . "\n";
        echo '    <priority>0.9</priority>' . "\n";
        
        // Hreflang alternatif diller
        foreach ($languages as $altLang) {
            $altUrl = BASE_URL . '/tools/' . $toolId . '.php?lang=' . $altLang;
            echo '    <xhtml:link rel="alternate" hreflang="' . $altLang . '" href="' . htmlspecialchars($altUrl) . '" />' . "\n";
        }
        
        echo '  </url>' . "\n";
    }
}

echo '</urlset>';
?>