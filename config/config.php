<?php
// config/config.php - MVP 10 TOOL TAMAMLANDI

define('BASE_URL', 'https://allintoolbox.com');
define('SITE_NAME', 'AllInToolbox');

// Dil ayarları
define('DEFAULT_LANGUAGE', 'tr');
define('SUPPORTED_LANGUAGES', ['tr', 'en']);

// Debug mode
define('DEBUG_MODE', true);

// Hata raporlama
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Reklam ayarları
define('EZOIC_ENABLED', true); // MVP için açık

// Tool kategorileri
define('TOOL_CATEGORIES', [
    'finance' => [
        'tr' => 'Finans',
        'en' => 'Finance'
    ],
    'health' => [
        'tr' => 'Sağlık',
        'en' => 'Health'
    ],
    'web' => [
        'tr' => 'Web Araçları',
        'en' => 'Web Tools'
    ],
    'converter' => [
        'tr' => 'Çevirici',
        'en' => 'Converter'
    ],
    'utility' => [
        'tr' => 'Pratik Araçlar',
        'en' => 'Utility Tools'
    ]
]);

// Tool listesi - MVP 10 TOOL TAMAMLANDI ✅
define('TOOLS_LIST', [
    // Finans (2) ✅
    'loan-calculator' => [
        'category' => 'finance',
        'tr' => [
            'name' => 'Kredi Hesaplayıcı',
            'description' => 'Kredi taksit ve faiz hesaplama, aylık ödeme miktarı'
        ],
        'en' => [
            'name' => 'Loan Calculator',
            'description' => 'Calculate loan payments and interest, monthly payment amount'
        ]
    ],
    'currency-converter' => [
        'category' => 'finance',
        'tr' => [
            'name' => 'Döviz Çevirici',
            'description' => 'Güncel kurlarla 150+ para birimini çevirin'
        ],
        'en' => [
            'name' => 'Currency Converter',
            'description' => 'Convert 150+ currencies with current rates'
        ]
    ],
    
    // Sağlık (2) ✅
    'bmi-calculator' => [
        'category' => 'health',
        'tr' => [
            'name' => 'BMI Hesaplayıcı',
            'description' => 'Vücut kitle indeksinizi hesaplayın ve sağlık durumunuzu değerlendirin'
        ],
        'en' => [
            'name' => 'BMI Calculator',
            'description' => 'Calculate your Body Mass Index and assess health status'
        ]
    ],
    'calorie-calculator' => [
        'category' => 'health',
        'tr' => [
            'name' => 'Kalori Hesaplayıcı',
            'description' => 'BMR ve aktivite seviyenize göre günlük kalori ihtiyacınızı hesaplayın'
        ],
        'en' => [
            'name' => 'Calorie Calculator',
            'description' => 'Calculate daily calorie needs based on BMR and activity level'
        ]
    ],
    
    // Web Araçları (2) ✅
    'qr-code-generator' => [
        'category' => 'web',
        'tr' => [
            'name' => 'QR Kod Üretici',
            'description' => 'Farklı türlerde QR kodları oluşturun (URL, email, WiFi, vCard)'
        ],
        'en' => [
            'name' => 'QR Code Generator',
            'description' => 'Create different types of QR codes (URL, email, WiFi, vCard)'
        ]
    ],
    'password-generator' => [
        'category' => 'web',
        'tr' => [
            'name' => 'Şifre Üretici',
            'description' => 'Güvenli şifre oluşturun, karakter seçenekleri ile özelleştirin'
        ],
        'en' => [
            'name' => 'Password Generator',
            'description' => 'Generate secure passwords with customizable character options'
        ]
    ],
    
    // Çevirici (2) ✅
    'unit-converter' => [
        'category' => 'converter',
        'tr' => [
            'name' => 'Ölçü Birimi Çevirici',
            'description' => 'Uzunluk, ağırlık, sıcaklık, hacim ve alan ölçülerini çevirin'
        ],
        'en' => [
            'name' => 'Unit Converter',
            'description' => 'Convert length, weight, temperature, volume and area measurements'
        ]
    ],
    'color-converter' => [
        'category' => 'converter',
        'tr' => [
            'name' => 'Renk Kodu Çevirici',
            'description' => 'HEX, RGB, HSL ve HSV renk kodlarını birbirine çevirin'
        ],
        'en' => [
            'name' => 'Color Code Converter',
            'description' => 'Convert HEX, RGB, HSL and HSV color codes'
        ]
    ],
    
    // Pratik Araçlar (2) ✅
    'text-analyzer' => [
        'category' => 'utility',
        'tr' => [
            'name' => 'Metin Analizi',
            'description' => 'Kelime, karakter sayısı analizi ve okuma süresi hesaplama'
        ],
        'en' => [
            'name' => 'Text Analyzer',
            'description' => 'Word and character count analysis with reading time calculation'
        ]
    ],
    'age-calculator' => [
        'category' => 'utility',
        'tr' => [
            'name' => 'Yaş Hesaplayıcı',
            'description' => 'Doğum tarihinizden tam yaşınızı ve bir sonraki doğum gününüzü hesaplayın'
        ],
        'en' => [
            'name' => 'Age Calculator',
            'description' => 'Calculate exact age from birth date and next birthday'
        ]
    ]
]);

// Meta bilgileri
define('META_INFO', [
    'tr' => [
        'title' => 'AllInToolbox - 10 Ücretsiz Online Araç | Hesaplayıcı ve Çevirici',
        'description' => 'BMI hesaplayıcı, kredi hesaplayıcı, döviz çevirici, QR kod üretici, şifre üretici ve daha fazlası. Ücretsiz, hızlı ve güvenilir online araçlar.',
        'keywords' => 'hesaplayıcı, çevirici, BMI, kredi, döviz, QR kod, şifre üretici, kalori hesaplayıcı, ölçü birimi çevirici, renk çevirici, metin analizi, yaş hesaplayıcı, online araçlar'
    ],
    'en' => [
        'title' => 'AllInToolbox - 10 Free Online Tools | Calculator and Converter',
        'description' => 'BMI calculator, loan calculator, currency converter, QR code generator, password generator and more. Free, fast and reliable online tools.',
        'keywords' => 'calculator, converter, BMI, loan, currency, QR code, password generator, calorie calculator, unit converter, color converter, text analyzer, age calculator, online tools'
    ]
]);

// SEO URL yapısı
define('SEO_URLS', [
    'tr' => [
        'tools_prefix' => 'araclar',
        'category_prefix' => 'kategori',
        'tools' => [
            'bmi-calculator' => 'bmi-hesaplayici',
            'loan-calculator' => 'kredi-hesaplayici',
            'currency-converter' => 'doviz-cevirici',
            'calorie-calculator' => 'kalori-hesaplayici',
            'qr-code-generator' => 'qr-kod-uretici',
            'password-generator' => 'sifre-uretici',
            'unit-converter' => 'olcu-birimi-cevirici',
            'color-converter' => 'renk-kodu-cevirici',
            'text-analyzer' => 'metin-analizi',
            'age-calculator' => 'yas-hesaplayici'
        ]
    ],
    'en' => [
        'tools_prefix' => 'tools',
        'category_prefix' => 'category',
        'tools' => [
            'bmi-calculator' => 'bmi-calculator',
            'loan-calculator' => 'loan-calculator',
            'currency-converter' => 'currency-converter',
            'calorie-calculator' => 'calorie-calculator',
            'qr-code-generator' => 'qr-code-generator',
            'password-generator' => 'password-generator',
            'unit-converter' => 'unit-converter',
            'color-converter' => 'color-converter',
            'text-analyzer' => 'text-analyzer',
            'age-calculator' => 'age-calculator'
        ]
    ]
]);

// Popüler araçlar (homepage için)
define('POPULAR_TOOLS', [
    'bmi-calculator',
    'currency-converter', 
    'qr-code-generator',
    'password-generator',
    'loan-calculator',
    'calorie-calculator'
]);

// Site istatistikleri
define('SITE_STATS', [
    'total_tools' => 10,
    'total_categories' => 5,
    'launch_date' => '2025-01-01'
]);

// Timezone
date_default_timezone_set('Europe/Istanbul');

// Tool özel ayarları
define('TOOL_SETTINGS', [
    'currency' => [
        'api_url' => 'https://latest.currency-api.pages.dev/v1/currencies',
        'cache_duration' => 3600 // 1 saat cache
    ],
    'qr' => [
        'max_size' => 500,
        'default_size' => 300,
        'supported_types' => ['text', 'url', 'email', 'phone', 'sms', 'wifi', 'vcard']
    ],
    'password' => [
        'min_length' => 4,
        'max_length' => 50,
        'default_length' => 12
    ],
    'bmi' => [
        'min_weight' => 1,
        'max_weight' => 500,
        'min_height' => 50,
        'max_height' => 250
    ],
    'calorie' => [
        'min_age' => 10,
        'max_age' => 120,
        'activity_multipliers' => [
            'sedentary' => 1.2,
            'light' => 1.375,
            'moderate' => 1.55,
            'active' => 1.725,
            'extra' => 1.9
        ]
    ]
]);

// Analytics ve tracking
define('ANALYTICS', [
    'google_analytics' => 'GA_MEASUREMENT_ID',
    'google_adsense' => 'ca-pub-XXXXXXXXX',
    'ezoic_enabled' => true
]);

// Cache ayarları
define('CACHE_SETTINGS', [
    'enabled' => true,
    'duration' => 3600,
    'currency_cache' => 1800 // 30 dakika
]);

// Güvenlik ayarları
define('SECURITY', [
    'csrf_protection' => true,
    'rate_limiting' => true,
    'max_requests_per_minute' => 60
]);

// Başarılı MVP özeti:
/*
✅ TAMAMLANAN 10 ARAÇ:

📊 FİNANS (2/2):
- Kredi Hesaplayıcı (loan-calculator.php)
- Döviz Çevirici (currency-converter.php)

🏥 SAĞLIK (2/2):
- BMI Hesaplayıcı (bmi-calculator.php) 
- Kalori Hesaplayıcı (calorie-calculator.php)

🌐 WEB ARAÇLARI (2/2):
- QR Kod Üretici (qr-code-generator.php)
- Şifre Üretici (password-generator.php)

🔄 ÇEVİRİCİ (2/2):
- Ölçü Birimi Çevirici (unit-converter.php)
- Renk Kodu Çevirici (color-converter.php)

🛠️ PRATİK ARAÇLAR (2/2):
- Metin Analizi (text-analyzer.php)
- Yaş Hesaplayıcı (age-calculator.php)

🎯 MVP ÖZELLİKLERİ:
✅ 2 dil desteği (TR/EN)
✅ Responsive dark theme
✅ Reklam alanları hazır
✅ SEO optimizasyonu
✅ Analytics entegrasyonu
✅ Breadcrumb navigasyon
✅ Related tools önerileri
✅ Detaylı açıklama sayfaları
✅ Social sharing butonları
✅ Print ve copy fonksiyonları
*/
?>