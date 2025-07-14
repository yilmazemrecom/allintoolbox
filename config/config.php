<?php
// config/config.php - MVP 10 TOOL VERSİYONU

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
define('EZOIC_ENABLED', false); // Test için kapalı

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

// Tool listesi - MVP 10 TOOL
define('TOOLS_LIST', [
    // Finans (2)
    'loan-calculator' => [
        'category' => 'finance',
        'tr' => [
            'name' => 'Kredi Hesaplayıcı',
            'description' => 'Kredi taksit ve faiz hesaplama'
        ],
        'en' => [
            'name' => 'Loan Calculator',
            'description' => 'Calculate loan payments and interest'
        ]
    ],
    'currency-converter' => [
        'category' => 'finance',
        'tr' => [
            'name' => 'Döviz Çevirici',
            'description' => 'Para birimlerini çevirin'
        ],
        'en' => [
            'name' => 'Currency Converter',
            'description' => 'Convert currencies'
        ]
    ],
    
    // Sağlık (2)
    'bmi-calculator' => [
        'category' => 'health',
        'tr' => [
            'name' => 'BMI Hesaplayıcı',
            'description' => 'Vücut kitle indeksinizi hesaplayın'
        ],
        'en' => [
            'name' => 'BMI Calculator',
            'description' => 'Calculate your Body Mass Index'
        ]
    ],
    'calorie-calculator' => [
        'category' => 'health',
        'tr' => [
            'name' => 'Kalori Hesaplayıcı',
            'description' => 'Günlük kalori ihtiyacınızı hesaplayın'
        ],
        'en' => [
            'name' => 'Calorie Calculator',
            'description' => 'Calculate daily calorie needs'
        ]
    ],
    
    // Web Araçları (2)
    'qr-code-generator' => [
        'category' => 'web',
        'tr' => [
            'name' => 'QR Kod Üretici',
            'description' => 'QR kod oluşturun'
        ],
        'en' => [
            'name' => 'QR Code Generator',
            'description' => 'Generate QR codes'
        ]
    ],
    'password-generator' => [
        'category' => 'web',
        'tr' => [
            'name' => 'Şifre Üretici',
            'description' => 'Güvenli şifre oluşturun'
        ],
        'en' => [
            'name' => 'Password Generator',
            'description' => 'Generate secure passwords'
        ]
    ],
    
    // Çevirici (2)
    'unit-converter' => [
        'category' => 'converter',
        'tr' => [
            'name' => 'Ölçü Birimi Çevirici',
            'description' => 'Uzunluk, ağırlık, sıcaklık çevirme'
        ],
        'en' => [
            'name' => 'Unit Converter',
            'description' => 'Convert length, weight, temperature'
        ]
    ],
    'color-converter' => [
        'category' => 'converter',
        'tr' => [
            'name' => 'Renk Kodu Çevirici',
            'description' => 'HEX, RGB, HSL çevirme'
        ],
        'en' => [
            'name' => 'Color Code Converter',
            'description' => 'Convert HEX, RGB, HSL'
        ]
    ],
    
    // Pratik Araçlar (2)
    'text-analyzer' => [
        'category' => 'utility',
        'tr' => [
            'name' => 'Metin Analizi',
            'description' => 'Kelime, karakter sayısı analizi'
        ],
        'en' => [
            'name' => 'Text Analyzer',
            'description' => 'Word and character count analysis'
        ]
    ],
    'age-calculator' => [
        'category' => 'utility',
        'tr' => [
            'name' => 'Yaş Hesaplayıcı',
            'description' => 'Doğum tarihinizden yaşınızı hesaplayın'
        ],
        'en' => [
            'name' => 'Age Calculator',
            'description' => 'Calculate age from birth date'
        ]
    ]
]);

// Meta bilgileri
define('META_INFO', [
    'tr' => [
        'title' => 'AllInToolbox - 10+ Ücretsiz Online Araç',
        'description' => 'BMI hesaplayıcı, kredi hesaplayıcı, döviz çevirici, QR kod üretici ve daha fazlası.',
        'keywords' => 'hesaplayıcı, çevirici, BMI, kredi, döviz, QR kod, şifre üretici, online araçlar'
    ],
    'en' => [
        'title' => 'AllInToolbox - 10+ Free Online Tools',
        'description' => 'BMI calculator, loan calculator, currency converter, QR code generator and more.',
        'keywords' => 'calculator, converter, BMI, loan, currency, QR code, password generator, online tools'
    ]
]);

// Timezone
date_default_timezone_set('Europe/Istanbul');
?>