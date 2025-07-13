<?php
// config/config.php
define('BASE_URL', 'https://allintoolbox.com');
define('SITE_NAME', 'AllInToolbox');
define('SITE_DESCRIPTION', 'Free Online Calculators, Converters and Tools');

// Dil ayarları
define('DEFAULT_LANGUAGE', 'tr');
define('SUPPORTED_LANGUAGES', ['tr', 'en']);

// Veritabanı (gelecekte kullanım için)
define('DB_HOST', 'localhost');
define('DB_NAME', 'allintoolbox');
define('DB_USER', 'username');
define('DB_PASS', 'password');

// Reklam ayarları
define('ADSENSE_CLIENT', 'ca-pub-XXXXXXXXXXXXXXXX');
define('EZOIC_ENABLED', true);

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

// Tool listesi
define('TOOLS_LIST', [
    'bmi-calculator' => [
        'category' => 'health',
        'tr' => [
            'name' => 'BMI Hesaplayıcı',
            'description' => 'Vücut kitle indeksinizi hesaplayın',
            'url' => '/tr/araclar/bmi-hesaplayici.php'
        ],
        'en' => [
            'name' => 'BMI Calculator',
            'description' => 'Calculate your Body Mass Index',
            'url' => '/en/tools/bmi-calculator.php'
        ]
    ],
    'loan-calculator' => [
        'category' => 'finance',
        'tr' => [
            'name' => 'Kredi Hesaplayıcı',
            'description' => 'Kredi taksitlerini hesaplayın',
            'url' => '/tr/araclar/kredi-hesaplayici.php'
        ],
        'en' => [
            'name' => 'Loan Calculator',
            'description' => 'Calculate loan payments',
            'url' => '/en/tools/loan-calculator.php'
        ]
    ],
    'qr-generator' => [
        'category' => 'web',
        'tr' => [
            'name' => 'QR Kod Üretici',
            'description' => 'QR kod oluşturun',
            'url' => '/tr/araclar/qr-kod-uretici.php'
        ],
        'en' => [
            'name' => 'QR Code Generator',
            'description' => 'Generate QR codes',
            'url' => '/en/tools/qr-code-generator.php'
        ]
    ],
    'currency-converter' => [
        'category' => 'converter',
        'tr' => [
            'name' => 'Döviz Çevirici',
            'description' => 'Para birimlerini çevirin',
            'url' => '/tr/cevirici/doviz-cevirici.php'
        ],
        'en' => [
            'name' => 'Currency Converter',
            'description' => 'Convert currencies',
            'url' => '/en/converter/currency-converter.php'
        ]
    ],
    'unit-converter' => [
        'category' => 'converter',
        'tr' => [
            'name' => 'Ölçü Birimi Çevirici',
            'description' => 'Ölçü birimlerini çevirin',
            'url' => '/tr/cevirici/olcu-birimi-cevirici.php'
        ],
        'en' => [
            'name' => 'Unit Converter',
            'description' => 'Convert units',
            'url' => '/en/converter/unit-converter.php'
        ]
    ],
    'password-generator' => [
        'category' => 'web',
        'tr' => [
            'name' => 'Şifre Üretici',
            'description' => 'Güvenli şifre oluşturun',
            'url' => '/tr/araclar/sifre-uretici.php'
        ],
        'en' => [
            'name' => 'Password Generator',
            'description' => 'Generate secure passwords',
            'url' => '/en/tools/password-generator.php'
        ]
    ],
    'color-converter' => [
        'category' => 'converter',
        'tr' => [
            'name' => 'Renk Kodu Çevirici',
            'description' => 'Renk kodlarını çevirin',
            'url' => '/tr/cevirici/renk-kodu-cevirici.php'
        ],
        'en' => [
            'name' => 'Color Code Converter',
            'description' => 'Convert color codes',
            'url' => '/en/converter/color-converter.php'
        ]
    ],
    'text-analyzer' => [
        'category' => 'utility',
        'tr' => [
            'name' => 'Metin Analizi',
            'description' => 'Metin özelliklerini analiz edin',
            'url' => '/tr/araclar/metin-analizi.php'
        ],
        'en' => [
            'name' => 'Text Analyzer',
            'description' => 'Analyze text properties',
            'url' => '/en/tools/text-analyzer.php'
        ]
    ],
    'age-calculator' => [
        'category' => 'utility',
        'tr' => [
            'name' => 'Yaş Hesaplayıcı',
            'description' => 'Yaşınızı hesaplayın',
            'url' => '/tr/araclar/yas-hesaplayici.php'
        ],
        'en' => [
            'name' => 'Age Calculator',
            'description' => 'Calculate your age',
            'url' => '/en/tools/age-calculator.php'
        ]
    ],
    'calorie-calculator' => [
        'category' => 'health',
        'tr' => [
            'name' => 'Kalori Hesaplayıcı',
            'description' => 'Günlük kalori ihtiyacınızı hesaplayın',
            'url' => '/tr/araclar/kalori-hesaplayici.php'
        ],
        'en' => [
            'name' => 'Calorie Calculator',
            'description' => 'Calculate daily calorie needs',
            'url' => '/en/tools/calorie-calculator.php'
        ]
    ]
]);

// Meta bilgileri
define('META_INFO', [
    'tr' => [
        'title' => 'AllInToolbox - Ücretsiz Online Hesaplayıcı ve Çevirici Araçları',
        'description' => 'BMI hesaplayıcı, kredi hesaplayıcı, QR kod üretici, döviz çevirici ve daha fazlası. Ücretsiz online araçlar.',
        'keywords' => 'hesaplayıcı, çevirici, BMI, kredi, QR kod, döviz, online araçlar'
    ],
    'en' => [
        'title' => 'AllInToolbox - Free Online Calculators and Converter Tools',
        'description' => 'BMI calculator, loan calculator, QR code generator, currency converter and more. Free online tools.',
        'keywords' => 'calculator, converter, BMI, loan, QR code, currency, online tools'
    ]
]);

// Timezone
date_default_timezone_set('Europe/Istanbul');

// Debug mode (production'da false yapın)
define('DEBUG_MODE', true);

// Hata raporlama
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>