<?php
// config/config.php - COMPLETE CONFIGURATION

// Performance monitoring start
define('START_TIME', microtime(true));

// Basic Settings
define('BASE_URL', 'https://allintoolbox.com');
define('SITE_NAME', 'AllInToolbox');
define('SITE_VERSION', '1.0.0');
define('SITE_LAUNCH_DATE', '2025-01-01');

// Language Settings
define('DEFAULT_LANGUAGE', 'tr');
define('SUPPORTED_LANGUAGES', ['tr', 'en']);

// Development Settings
define('DEBUG_MODE', false); // Set to false in production
define('MAINTENANCE_MODE', false);

// Error Reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
}

// Security Settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('CSRF_TOKEN_LIFETIME', 1800); // 30 minutes
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Advertisement Settings
define('EZOIC_ENABLED', true);
define('GOOGLE_ADSENSE_ID', 'ca-pub-1234567890123456'); // Replace with actual ID
define('GOOGLE_ANALYTICS_ID', 'G-XXXXXXXXXX'); // Replace with actual ID

// API Settings
define('CURRENCY_API_KEY', ''); // Add when implementing currency converter
define('CURRENCY_API_URL', 'https://api.exchangerate-api.com/v4/latest/');

// File Upload Settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt']);

// Rate Limiting
define('RATE_LIMIT_REQUESTS', 60); // requests per hour
define('RATE_LIMIT_WINDOW', 3600); // 1 hour

// Tool Categories
define('TOOL_CATEGORIES', [
    'finance' => [
        'tr' => 'Finans Araçları',
        'en' => 'Finance Tools'
    ],
    'health' => [
        'tr' => 'Sağlık Araçları', 
        'en' => 'Health Tools'
    ],
    'web' => [
        'tr' => 'Web Araçları',
        'en' => 'Web Tools'
    ],
    'converter' => [
        'tr' => 'Çevirici Araçlar',
        'en' => 'Converter Tools'
    ],
    'utility' => [
        'tr' => 'Pratik Araçlar',
        'en' => 'Utility Tools'
    ]
]);

// Complete Tools List - MVP 10 Tools
define('TOOLS_LIST', [
    // Finance Tools (2)
    'loan-calculator' => [
        'category' => 'finance',
        'tr' => [
            'name' => 'Kredi Hesaplayıcı',
            'description' => 'Kredi taksit hesaplama, faiz oranı ve ödeme planı analizi',
            'keywords' => 'kredi hesaplayıcı, taksit hesaplama, faiz hesaplama, konut kredisi'
        ],
        'en' => [
            'name' => 'Loan Calculator',
            'description' => 'Loan payment calculation, interest rate and payment plan analysis',
            'keywords' => 'loan calculator, payment calculation, interest calculation, mortgage'
        ]
    ],
    'currency-converter' => [
        'category' => 'finance',
        'tr' => [
            'name' => 'Döviz Çevirici',
            'description' => 'Güncel kurlarla 150+ para birimini anında çevirin',
            'keywords' => 'döviz çevirici, para birimi, kur hesaplama, dolar euro'
        ],
        'en' => [
            'name' => 'Currency Converter',
            'description' => 'Convert 150+ currencies with real-time exchange rates',
            'keywords' => 'currency converter, exchange rate, money converter, USD EUR'
        ]
    ],
    
    // Health Tools (2)
    'bmi-calculator' => [
        'category' => 'health',
        'tr' => [
            'name' => 'BMI Hesaplayıcı',
            'description' => 'Vücut kitle indeksi hesaplama ve ideal kilo analizi',
            'keywords' => 'BMI hesaplayıcı, vücut kitle indeksi, ideal kilo, kilo hesaplama'
        ],
        'en' => [
            'name' => 'BMI Calculator',
            'description' => 'Body mass index calculation and ideal weight analysis',
            'keywords' => 'BMI calculator, body mass index, ideal weight, weight calculator'
        ]
    ],
    'calorie-calculator' => [
        'category' => 'health',
        'tr' => [
            'name' => 'Kalori Hesaplayıcı',
            'description' => 'Günlük kalori ihtiyacı ve BMR hesaplama',
            'keywords' => 'kalori hesaplayıcı, günlük kalori, BMR hesaplama, metabolizma'
        ],
        'en' => [
            'name' => 'Calorie Calculator',
            'description' => 'Daily calorie needs and BMR calculation',
            'keywords' => 'calorie calculator, daily calories, BMR calculation, metabolism'
        ]
    ],
    
    // Web Tools (2)
    'qr-code-generator' => [
        'category' => 'web',
        'tr' => [
            'name' => 'QR Kod Üretici',
            'description' => 'URL, metin ve vCard için ücretsiz QR kod oluşturucu',
            'keywords' => 'QR kod üretici, QR kod oluşturucu, URL QR kod, metin QR kod'
        ],
        'en' => [
            'name' => 'QR Code Generator',
            'description' => 'Free QR code generator for URL, text and vCard',
            'keywords' => 'QR code generator, QR code creator, URL QR code, text QR code'
        ]
    ],
    'password-generator' => [
        'category' => 'web',
        'tr' => [
            'name' => 'Şifre Üretici',
            'description' => 'Güvenli ve güçlü şifre oluşturucu araç',
            'keywords' => 'şifre üretici, güvenli şifre, şifre oluşturucu, rastgele şifre'
        ],
        'en' => [
            'name' => 'Password Generator',
            'description' => 'Secure and strong password generator tool',
            'keywords' => 'password generator, secure password, password creator, random password'
        ]
    ],
    
    // Converter Tools (2) 
    'unit-converter' => [
        'category' => 'converter',
        'tr' => [
            'name' => 'Ölçü Birimi Çevirici',
            'description' => 'Uzunluk, ağırlık, sıcaklık ve hacim birim çevirici',
            'keywords' => 'ölçü birimi çevirici, birim dönüştürücü, metre feet, kg pound'
        ],
        'en' => [
            'name' => 'Unit Converter',
            'description' => 'Length, weight, temperature and volume unit converter',
            'keywords' => 'unit converter, measurement converter, meter feet, kg pound'
        ]
    ],
    'color-converter' => [
        'category' => 'converter',
        'tr' => [
            'name' => 'Renk Çevirici',
            'description' => 'HEX, RGB, HSL ve CMYK renk kodu çevirici',
            'keywords' => 'renk çevirici, HEX RGB, renk kodu, color picker'
        ],
        'en' => [
            'name' => 'Color Converter',
            'description' => 'HEX, RGB, HSL and CMYK color code converter',
            'keywords' => 'color converter, HEX RGB, color code, color picker'
        ]
    ],
    
    // Utility Tools (2)
    'text-analyzer' => [
        'category' => 'utility',
        'tr' => [
            'name' => 'Metin Analizi',
            'description' => 'Kelime sayısı, karakter sayısı ve metin analizi araç',
            'keywords' => 'metin analizi, kelime sayısı, karakter sayısı, yazı analizi'
        ],
        'en' => [
            'name' => 'Text Analyzer',
            'description' => 'Word count, character count and text analysis tool',
            'keywords' => 'text analyzer, word count, character count, text analysis'
        ]
    ],
    'age-calculator' => [
        'category' => 'utility',
        'tr' => [
            'name' => 'Yaş Hesaplayıcı',
            'description' => 'Doğum tarihinden yaş hesaplama ve yaşam istatistikleri',
            'keywords' => 'yaş hesaplayıcı, doğum tarihi, yaş hesaplama, yaşam süresi'
        ],
        'en' => [
            'name' => 'Age Calculator', 
            'description' => 'Calculate age from birth date and life statistics',
            'keywords' => 'age calculator, birth date, age calculation, life duration'
        ]
    ]
]);

// Meta Information for SEO
define('META_INFO', [
    'tr' => [
        'title' => 'AllInToolbox - Ücretsiz Online Araçlar ve Hesaplayıcılar',
        'description' => 'Günlük ihtiyaçlarınız için 10+ ücretsiz online araç. BMI, kredi, döviz, QR kod, şifre üretici ve daha fazlası. Hızlı, güvenli ve kolay kullanım.',
        'keywords' => 'online araçlar, hesaplayıcı, BMI hesaplayıcı, kredi hesaplayıcı, döviz çevirici, QR kod üretici, ücretsiz araçlar',
        'og_description' => 'Günlük hayatınızı kolaylaştıran ücretsiz online araçlar. BMI, kredi hesaplayıcı, döviz çevirici ve daha fazlası.'
    ],
    'en' => [
        'title' => 'AllInToolbox - Free Online Tools and Calculators',
        'description' => '10+ free online tools for your daily needs. BMI, loan, currency, QR code, password generator and more. Fast, secure and easy to use.',
        'keywords' => 'online tools, calculator, BMI calculator, loan calculator, currency converter, QR code generator, free tools',
        'og_description' => 'Free online tools that make your daily life easier. BMI, loan calculator, currency converter and more.'
    ]
]);

// Analytics Configuration
define('ANALYTICS', [
    'google_analytics' => GOOGLE_ANALYTICS_ID,
    'google_adsense' => GOOGLE_ADSENSE_ID,
    'ezoic_enabled' => EZOIC_ENABLED,
    'track_events' => true,
    'track_scroll' => true,
    'track_clicks' => true
]);

// Site Statistics (for footer)
define('SITE_STATS', [
    'launch_date' => SITE_LAUNCH_DATE,
    'total_tools' => count(TOOLS_LIST),
    'total_categories' => count(TOOL_CATEGORIES),
    'supported_languages' => count(SUPPORTED_LANGUAGES)
]);

// Contact Information
define('CONTACT_INFO', [
    'email' => 'info@allintoolbox.com',
    'support_email' => 'support@allintoolbox.com',
    'business_hours' => '09:00-18:00 UTC+3',
    'response_time' => '24 hours'
]);

// Social Media Links
define('SOCIAL_LINKS', [
    'facebook' => 'https://facebook.com/allintoolbox',
    'twitter' => 'https://twitter.com/allintoolbox', 
    'instagram' => 'https://instagram.com/allintoolbox',
    'linkedin' => 'https://linkedin.com/company/allintoolbox',
    'github' => 'https://github.com/allintoolbox'
]);

// Legal Pages
define('LEGAL_PAGES', [
    'privacy' => [
        'tr' => 'Gizlilik Politikası',
        'en' => 'Privacy Policy'
    ],
    'terms' => [
        'tr' => 'Kullanım Şartları', 
        'en' => 'Terms of Service'
    ],
    'cookies' => [
        'tr' => 'Çerez Politikası',
        'en' => 'Cookie Policy'
    ],
    'disclaimer' => [
        'tr' => 'Sorumluluk Reddi',
        'en' => 'Disclaimer'
    ]
]);

// Database Configuration (for future use)
define('DB_CONFIG', [
    'host' => 'localhost',
    'dbname' => 'allintoolbox',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
]);

// Cache Configuration
define('CACHE_CONFIG', [
    'enabled' => !DEBUG_MODE,
    'type' => 'file', // file, redis, memcached
    'ttl' => 3600, // 1 hour
    'path' => __DIR__ . '/../cache/'
]);

// Email Configuration (for contact forms, notifications)
define('EMAIL_CONFIG', [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => '',
    'smtp_password' => '',
    'smtp_encryption' => 'tls',
    'from_email' => 'noreply@allintoolbox.com',
    'from_name' => 'AllInToolbox'
]);

// Feature Flags
define('FEATURES', [
    'user_accounts' => false,
    'tool_favorites' => false,
    'tool_history' => true,
    'api_access' => false,
    'premium_tools' => false,
    'notifications' => false,
    'tool_sharing' => true,
    'offline_mode' => false
]);

// Localization
setlocale(LC_ALL, DEFAULT_LANGUAGE === 'tr' ? 'tr_TR.UTF-8' : 'en_US.UTF-8');
date_default_timezone_set('Europe/Istanbul');

// Session Configuration
if (!session_id()) {
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'domain' => '',
        'secure' => !DEBUG_MODE, // Only use secure cookies in production
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// CSRF Protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}

// Regenerate CSRF token if expired
if (isset($_SESSION['csrf_token_time']) && 
    (time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_LIFETIME) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}

// Auto-create necessary directories
$directories = [
    __DIR__ . '/../logs',
    __DIR__ . '/../cache',
    __DIR__ . '/../uploads'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Maintenance Mode Check
if (MAINTENANCE_MODE && !DEBUG_MODE) {
    if (!isset($_SESSION['admin_bypass'])) {
        http_response_code(503);
        include __DIR__ . '/../pages/maintenance.php';
        exit();
    }
}

// Load environment variables if available
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
    foreach ($env as $key => $value) {
        if (!defined($key)) {
            define($key, $value);
        }
    }
}
?>