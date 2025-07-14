<?php
// tools/password-generator.php - TAM VERSİYON
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Sayfa meta bilgileri
$pageTitle = ($currentLang === 'tr') ? 'Şifre Üretici' : 'Password Generator';
$pageDescription = ($currentLang === 'tr') ? 
    'Güvenli şifre üretici. Özelleştirilebilir seçeneklerle güçlü şifreler oluşturun.' :
    'Secure password generator. Create strong passwords with customizable options.';

// Şifre üretme fonksiyonu
function generatePassword($length = 12, $includeUpper = true, $includeLower = true, $includeNumbers = true, $includeSymbols = true) {
    $chars = '';
    if ($includeLower) $chars .= 'abcdefghijklmnopqrstuvwxyz';
    if ($includeUpper) $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if ($includeNumbers) $chars .= '0123456789';
    if ($includeSymbols) $chars .= '!@#$%^&*()_+-=[]{}|;:,.<>?';
    
    if (empty($chars)) {
        return '';
    }
    
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

// Şifre gücü hesaplama
function calculatePasswordStrength($password) {
    $score = 0;
    $length = strlen($password);
    
    // Uzunluk puanı
    if ($length >= 8) $score += 1;
    if ($length >= 12) $score += 1;
    if ($length >= 16) $score += 1;
    
    // Karakter çeşitliliği
    if (preg_match('/[a-z]/', $password)) $score += 1;
    if (preg_match('/[A-Z]/', $password)) $score += 1;
    if (preg_match('/[0-9]/', $password)) $score += 1;
    if (preg_match('/[^a-zA-Z0-9]/', $password)) $score += 1;
    
    return $score;
}

// Şifre gücü metni
function getPasswordStrengthText($score, $lang) {
    if ($lang === 'tr') {
        switch ($score) {
            case 0-2: return ['text' => 'Çok Zayıf', 'class' => 'danger'];
            case 3-4: return ['text' => 'Zayıf', 'class' => 'warning'];
            case 5-6: return ['text' => 'Orta', 'class' => 'info'];
            case 7: return ['text' => 'Güçlü', 'class' => 'success'];
            default: return ['text' => 'Çok Güçlü', 'class' => 'success'];
        }
    } else {
        switch ($score) {
            case 0-2: return ['text' => 'Very Weak', 'class' => 'danger'];
            case 3-4: return ['text' => 'Weak', 'class' => 'warning'];
            case 5-6: return ['text' => 'Medium', 'class' => 'info'];
            case 7: return ['text' => 'Strong', 'class' => 'success'];
            default: return ['text' => 'Very Strong', 'class' => 'success'];
        }
    }
}

// Ortak şifre türleri
$passwordTypes = [
    'strong' => [
        'tr' => 'Güçlü Şifre (16 karakter)',
        'en' => 'Strong Password (16 chars)',
        'length' => 16,
        'upper' => true,
        'lower' => true,
        'numbers' => true,
        'symbols' => true
    ],
    'medium' => [
        'tr' => 'Orta Şifre (12 karakter)',
        'en' => 'Medium Password (12 chars)',
        'length' => 12,
        'upper' => true,
        'lower' => true,
        'numbers' => true,
        'symbols' => false
    ],
    'pin' => [
        'tr' => 'PIN Kodu (6 rakam)',
        'en' => 'PIN Code (6 digits)',
        'length' => 6,
        'upper' => false,
        'lower' => false,
        'numbers' => true,
        'symbols' => false
    ],
    'easy' => [
        'tr' => 'Kolay Şifre (8 karakter)',
        'en' => 'Easy Password (8 chars)',
        'length' => 8,
        'upper' => true,
        'lower' => true,
        'numbers' => false,
        'symbols' => false
    ]
];

// Form işleme
$result = null;
$error = null;

if ($_POST || !empty($_GET['generate'])) {
    $length = intval($_POST['length'] ?? $_GET['length'] ?? 12);
    $includeUpper = isset($_POST['uppercase']) || isset($_GET['uppercase']);
    $includeLower = isset($_POST['lowercase']) || isset($_GET['lowercase']) || (!$_POST && !$_GET);
    $includeNumbers = isset($_POST['numbers']) || isset($_GET['numbers']) || (!$_POST && !$_GET);
    $includeSymbols = isset($_POST['symbols']) || isset($_GET['symbols']);
    
    // Sınırlar
    $length = max(4, min(50, $length));
    
    // En az bir karakter türü seçilmeli
    if (!$includeUpper && !$includeLower && !$includeNumbers && !$includeSymbols) {
        $error = ($currentLang === 'tr') ? 
            'En az bir karakter türü seçmelisiniz!' :
            'You must select at least one character type!';
    } else {
        $password = generatePassword($length, $includeUpper, $includeLower, $includeNumbers, $includeSymbols);
        $strength = calculatePasswordStrength($password);
        $strengthInfo = getPasswordStrengthText($strength, $currentLang);
        
        $result = [
            'password' => $password,
            'length' => $length,
            'strength_score' => $strength,
            'strength_text' => $strengthInfo['text'],
            'strength_class' => $strengthInfo['class'],
            'include_upper' => $includeUpper,
            'include_lower' => $includeLower,
            'include_numbers' => $includeNumbers,
            'include_symbols' => $includeSymbols,
            'entropy' => round(log(strlen($password > 0 ? 
                ($includeLower ? 26 : 0) + 
                ($includeUpper ? 26 : 0) + 
                ($includeNumbers ? 10 : 0) + 
                ($includeSymbols ? 32 : 0) : 1), 2) * $length, 2)
        ];
    }
}

include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">

        <!-- Breadcrumb -->
        <?php
        $breadcrumbItems = [
            ['title' => ($currentLang === 'tr') ? 'Ana Sayfa' : 'Home', 'url' => '/?lang=' . $currentLang],
            ['title' => ($currentLang === 'tr') ? 'Web Araçları' : 'Web Tools', 'url' => '/' . $currentLang . '/category/web'],
            ['title' => $pageTitle]
        ];
        echo generateBreadcrumb($breadcrumbItems);
        ?>

        <!-- Tool Container -->
        <div class="tool-container">
            <div class="tool-header">
                <h1><i class="fas fa-key text-primary"></i> <?php echo $pageTitle; ?></h1>
                <p class="lead"><?php echo $pageDescription; ?></p>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <!-- Password Form -->
                    <div class="tool-form">
                        <form method="POST" id="passwordForm">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-ruler"></i>
                                    <?php echo ($currentLang === 'tr') ? 'Şifre Uzunluğu' : 'Password Length'; ?>
                                </label>
                                <input type="range" class="form-range" name="length" id="lengthSlider" 
                                       min="4" max="50" value="<?php echo $_POST['length'] ?? 12; ?>" 
                                       oninput="updateLengthValue(this.value)">
                                <div class="text-center">
                                    <span id="lengthValue" class="badge bg-primary"><?php echo $_POST['length'] ?? 12; ?></span> 
                                    <?php echo ($currentLang === 'tr') ? 'karakter' : 'characters'; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-check-square"></i>
                                    <?php echo ($currentLang === 'tr') ? 'Karakter Türleri' : 'Character Types'; ?>
                                </label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="lowercase" id="lowercase" 
                                           <?php echo (!$_POST || isset($_POST['lowercase'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="lowercase">
                                        <code>a-z</code> <?php echo ($currentLang === 'tr') ? 'Küçük harfler' : 'Lowercase letters'; ?>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="uppercase" id="uppercase"
                                           <?php echo isset($_POST['uppercase']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="uppercase">
                                        <code>A-Z</code> <?php echo ($currentLang === 'tr') ? 'Büyük harfler' : 'Uppercase letters'; ?>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="numbers" id="numbers"
                                           <?php echo (!$_POST || isset($_POST['numbers'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="numbers">
                                        <code>0-9</code> <?php echo ($currentLang === 'tr') ? 'Sayılar' : 'Numbers'; ?>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="symbols" id="symbols"
                                           <?php echo isset($_POST['symbols']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="symbols">
                                        <code>!@#$</code> <?php echo ($currentLang === 'tr') ? 'Özel karakterler' : 'Special characters'; ?>
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="generateBtn">
                                    <i class="fas fa-sync-alt"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Şifre Üret' : 'Generate Password'; ?>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('passwordForm').reset(); updateLengthValue(12);">
                                    <i class="fas fa-eraser"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Sıfırla' : 'Reset'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Quick Presets -->
                    <div class="card">
                        <div class="card-body">
                            <h6><?php echo ($currentLang === 'tr') ? 'Hızlı Şablonlar' : 'Quick Presets'; ?></h6>
                            <div class="row g-2">
                                <?php foreach ($passwordTypes as $type => $info): ?>
                                    <div class="col-6 mb-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" 
                                                onclick="setPasswordType('<?php echo $type; ?>')">
                                            <small><?php echo $info[$currentLang]; ?></small>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Security Tips -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-shield-alt"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Güvenlik İpuçları' : 'Security Tips'; ?>
                        </h6>
                        <ul class="mb-0 small">
                            <li><?php echo ($currentLang === 'tr') ? 'Her hesap için farklı şifre kullanın' : 'Use different passwords for each account'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? 'Şifreleri güvenli bir yerde saklayın' : 'Store passwords in a secure location'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? '2FA (iki faktörlü doğrulama) etkinleştirin' : 'Enable 2FA (two-factor authentication)'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? 'Şifreleri düzenli olarak değiştirin' : 'Change passwords regularly'; ?></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <?php if ($result): ?>
                    <!-- Sonuç -->
                    <div class="tool-result">
                        <h4><i class="fas fa-key"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Oluşturulan Şifre' : 'Generated Password'; ?>
                        </h4>
                        <div class="password-result-display">
                            <!-- Password Display -->
                            <div class="password-display mb-3">
                                <div class="input-group">
                                    <input type="password" class="form-control" id="generatedPassword" 
                                           value="<?php echo htmlspecialchars($result['password']); ?>" readonly
                                           style="font-family: 'Courier New', monospace; font-size: 1.1rem;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility()" id="toggleBtn">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </button>
                                    <button class="btn btn-success" type="button" onclick="copyPassword()">
                                        <i class="fas fa-copy"></i> 
                                        <?php echo ($currentLang === 'tr') ? 'Kopyala' : 'Copy'; ?>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Password Strength -->
                            <div class="alert alert-<?php echo $result['strength_class']; ?> mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <strong><?php echo ($currentLang === 'tr') ? 'Şifre Gücü:' : 'Password Strength:'; ?></strong>
                                        <?php echo $result['strength_text']; ?>
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        <?php echo $result['strength_score']; ?>/7
                                    </span>
                                </div>
                                
                                <!-- Strength Progress Bar -->
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-<?php echo $result['strength_class']; ?>" 
                                         style="width: <?php echo ($result['strength_score'] / 7) * 100; ?>%"></div>
                                </div>
                            </div>
                            
                            <!-- Password Analysis -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6><?php echo ($currentLang === 'tr') ? 'Şifre Analizi' : 'Password Analysis'; ?></h6>
                                    <div class="row text-center">
                                        <div class="col-3">
                                            <div class="p-2 border rounded">
                                                <strong><?php echo $result['length']; ?></strong><br>
                                                <small><?php echo ($currentLang === 'tr') ? 'Karakter' : 'Characters'; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="p-2 border rounded">
                                                <strong><?php echo $result['entropy']; ?></strong><br>
                                                <small><?php echo ($currentLang === 'tr') ? 'Entropi' : 'Entropy'; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="p-2 border rounded">
                                                <strong><?php echo ($result['include_upper'] + $result['include_lower'] + $result['include_numbers'] + $result['include_symbols']); ?></strong><br>
                                                <small><?php echo ($currentLang === 'tr') ? 'Tür' : 'Types'; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="p-2 border rounded">
                                                <strong>∞</strong><br>
                                                <small><?php echo ($currentLang === 'tr') ? 'Kırma' : 'Crack'; ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Character Types Used -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6><?php echo ($currentLang === 'tr') ? 'Kullanılan Karakter Türleri' : 'Character Types Used'; ?></h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php if ($result['include_lower']): ?>
                                            <span class="badge bg-info">a-z</span>
                                        <?php endif; ?>
                                        <?php if ($result['include_upper']): ?>
                                            <span class="badge bg-primary">A-Z</span>
                                        <?php endif; ?>
                                        <?php if ($result['include_numbers']): ?>
                                            <span class="badge bg-success">0-9</span>
                                        <?php endif; ?>
                                        <?php if ($result['include_symbols']): ?>
                                            <span class="badge bg-warning">!@#$</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-3">
                                <button class="btn btn-outline-primary" onclick="generateNewPassword()">
                                    <i class="fas fa-redo"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Yeni Şifre Üret' : 'Generate New Password'; ?>
                                </button>
                                <button class="btn btn-outline-light" onclick="window.print()">
                                    <i class="fas fa-print"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Yazdır' : 'Print'; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <?php elseif ($error): ?>
                    <!-- Hata -->
                    <div class="tool-result error">
                        <h4><i class="fas fa-exclamation-triangle"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Hata' : 'Error'; ?>
                        </h4>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                    <?php else: ?>
                    <!-- Placeholder -->
                    <div class="card">
                        <div class="card-body">
                            <h5><i class="fas fa-lightbulb"></i> 
                                <?php echo ($currentLang === 'tr') ? 'Şifre Güvenliği' : 'Password Security'; ?>
                            </h5>
                            <div class="mb-3">
                                <h6><?php echo ($currentLang === 'tr') ? 'İdeal Şifre Özellikleri:' : 'Ideal Password Features:'; ?></h6>
                                <ul class="small">
                                    <li><?php echo ($currentLang === 'tr') ? 'En az 12 karakter uzunluğunda' : 'At least 12 characters long'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Büyük ve küçük harfler içerir' : 'Contains uppercase and lowercase letters'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Sayılar ve özel karakterler içerir' : 'Includes numbers and special characters'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Tahmin edilmesi zor' : 'Difficult to guess'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Kişisel bilgiler içermez' : 'No personal information'; ?></li>
                                </ul>
                            </div>
                            
                            <div class="alert alert-warning">
                                <small>
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Şifrenizi hiç kimseyle paylaşmayın ve güvenli bir şekilde saklayın.' :
                                        'Never share your password with anyone and store it securely.'; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Ad Space -->
        <?php echo renderAdSpace('content', 'large'); ?>

        <!-- Password Security Information -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3><i class="fas fa-book"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Şifre Güvenliği Hakkında Detaylı Bilgi' : 'Detailed Information About Password Security'; ?>
                        </h3>
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Güçlü Şifre Oluşturma' : 'Creating Strong Passwords'; ?></h5>
                                <p>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Güçlü bir şifre, siber saldırılara karşı en önemli savunma hattınızdır. İyi bir şifre, uzun, karmaşık ve tahmin edilmesi zor olmalıdır.' :
                                        'A strong password is your most important defense against cyber attacks. A good password should be long, complex, and difficult to guess.'; ?>
                                </p>
                                
                                <h6><?php echo ($currentLang === 'tr') ? 'Karakter Türleri:' : 'Character Types:'; ?></h6>
                                <ul>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Küçük Harfler (a-z):' : 'Lowercase (a-z):'; ?></strong> 
                                        <?php echo ($currentLang === 'tr') ? 'Temel karakter seti' : 'Basic character set'; ?></li>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Büyük Harfler (A-Z):' : 'Uppercase (A-Z):'; ?></strong> 
                                        <?php echo ($currentLang === 'tr') ? 'Karmaşıklığı artırır' : 'Increases complexity'; ?></li>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Sayılar (0-9):' : 'Numbers (0-9):'; ?></strong> 
                                        <?php echo ($currentLang === 'tr') ? 'Entropi ekler' : 'Adds entropy'; ?></li>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Özel Karakterler (!@#$):' : 'Special Characters (!@#$):'; ?></strong> 
                                        <?php echo ($currentLang === 'tr') ? 'Maksimum güvenlik' : 'Maximum security'; ?></li>
                                </ul>
                                
                                <h6><?php echo ($currentLang === 'tr') ? 'Şifre Uzunluğu:' : 'Password Length:'; ?></h6>
                                <ul>
                                    <li><?php echo ($currentLang === 'tr') ? '8-11 karakter: Temel güvenlik' : '8-11 characters: Basic security'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? '12-15 karakter: İyi güvenlik' : '12-15 characters: Good security'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? '16+ karakter: Mükemmel güvenlik' : '16+ characters: Excellent security'; ?></li>
                                </ul>
                            </div>
                            
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'En İyi Uygulamalar' : 'Best Practices'; ?></h5>
                                <ul>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Her hesap için farklı şifre kullanın' :
                                        'Use a different password for each account'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Şifre yöneticisi kullanın' :
                                        'Use a password manager'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'İki faktörlü doğrulama (2FA) etkinleştirin' :
                                        'Enable two-factor authentication (2FA)'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Şifreleri düzenli olarak güncelleyin' :
                                        'Update passwords regularly'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Şifreleri güvenli olmayan yerlerde saklamayın' :
                                        'Don\'t store passwords in insecure places'; ?></li>
                                </ul>
                                
                                <h5><?php echo ($currentLang === 'tr') ? 'Kaçınılması Gerekenler' : 'What to Avoid'; ?></h5>
                                <ul class="text-danger">
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Kişisel bilgileri şifrede kullanmak' :
                                        'Using personal information in passwords'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Yaygın kelimeler ve kalıplar' :
                                        'Common words and patterns'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Klavye sıralamaları (123456, qwerty)' :
                                        'Keyboard sequences (123456, qwerty)'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Aynı şifreyi birden fazla yerde kullanmak' :
                                        'Reusing the same password in multiple places'; ?></li>
                                </ul>
                                
                                <div class="alert alert-success">
                                    <i class="fas fa-shield-alt"></i>
                                    <strong><?php echo ($currentLang === 'tr') ? 'Güvenlik Notu:' : 'Security Note:'; ?></strong>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Bu araç tamamen tarayıcınızda çalışır. Oluşturulan şifreler hiçbir yerde saklanmaz.' :
                                        'This tool works entirely in your browser. Generated passwords are not stored anywhere.'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Tools -->
        <div class="row mt-4">
            <div class="col-12">
                <h4><i class="fas fa-link"></i> 
                    <?php echo ($currentLang === 'tr') ? 'İlgili Araçlar' : 'Related Tools'; ?>
                </h4>
                <div class="row">
                    <?php
                    $relatedTools = [
                        [
                            'name' => ($currentLang === 'tr') ? 'QR Kod Üretici' : 'QR Code Generator',
                            'description' => ($currentLang === 'tr') ? 'Farklı türlerde QR kodları oluşturun' : 'Create different types of QR codes',
                            'url' => '/tools/qr-code-generator.php?lang=' . $currentLang,
                            'icon' => 'fas fa-qrcode'
                        ],
                        [
                            'name' => ($currentLang === 'tr') ? 'Metin Analizi' : 'Text Analyzer',
                            'description' => ($currentLang === 'tr') ? 'Metin analizi ve kelime sayımı' : 'Text analysis and word count',
                            'url' => '/tools/text-analyzer.php?lang=' . $currentLang,
                            'icon' => 'fas fa-file-alt'
                        ]
                    ];
                    
                    foreach ($relatedTools as $tool): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="<?php echo $tool['icon']; ?>"></i> 
                                        <?php echo $tool['name']; ?>
                                    </h6>
                                    <p class="card-text"><?php echo $tool['description']; ?></p>
                                    <a href="<?php echo $tool['url']; ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-arrow-right"></i> 
                                        <?php echo ($currentLang === 'tr') ? 'Kullan' : 'Use'; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
// Password Generator specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('passwordForm');
    const generateBtn = document.getElementById('generateBtn');
    const currentLang = '<?php echo $currentLang; ?>';
    
    // Track tool usage
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Tool', 'View', 'Password Generator');
        
        // Add to recent tools
        const toolName = currentLang === 'tr' ? 'Şifre Üretici' : 'Password Generator';
        const toolUrl = '/tools/password-generator.php?lang=' + currentLang;
        AllInToolbox.storage.addRecentTool('password-generator', toolName, toolUrl);
    }
    
    // Auto-generate on page load if no password exists
    <?php if (!$result && !$error): ?>
    generateNewPassword();
    <?php endif; ?>
    
    // Form submission
    form.addEventListener('submit', function(e) {
        if (typeof AllInToolbox !== 'undefined') {
            AllInToolbox.utils.showLoading(generateBtn);
            AllInToolbox.analytics.trackToolUsage('Password Generator');
        }
        
        // Simulate processing time
        setTimeout(() => {
            if (typeof AllInToolbox !== 'undefined') {
                AllInToolbox.utils.showLoading(generateBtn, false);
            }
        }, 300);
    });
});

// Password type presets
const passwordPresets = <?php echo json_encode($passwordTypes); ?>;

// Update length value display
function updateLengthValue(value) {
    document.getElementById('lengthValue').textContent = value;
}

// Set password type preset
function setPasswordType(type) {
    const preset = passwordPresets[type];
    if (preset) {
        document.getElementById('lengthSlider').value = preset.length;
        document.getElementById('lengthValue').textContent = preset.length;
        document.getElementById('lowercase').checked = preset.lower;
        document.getElementById('uppercase').checked = preset.upper;
        document.getElementById('numbers').checked = preset.numbers;
        document.getElementById('symbols').checked = preset.symbols;
        
        // Auto-generate with new settings
        generateNewPassword();
    }
}

// Toggle password visibility
function togglePasswordVisibility() {
    const passwordField = document.getElementById('generatedPassword');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.className = 'fas fa-eye-slash';
    } else {
        passwordField.type = 'password';
        toggleIcon.className = 'fas fa-eye';
    }
}

// Copy password to clipboard
function copyPassword() {
    const passwordField = document.getElementById('generatedPassword');
    const password = passwordField.value;
    
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.utils.copyToClipboard(password);
    } else {
        // Fallback for older browsers
        passwordField.select();
        passwordField.setSelectionRange(0, 99999);
        
        try {
            document.execCommand('copy');
            alert('<?php echo ($currentLang === 'tr') ? 'Şifre kopyalandı!' : 'Password copied!'; ?>');
        } catch (err) {
            alert('<?php echo ($currentLang === 'tr') ? 'Kopyalama başarısız!' : 'Copy failed!'; ?>');
        }
    }
    
    // Track copy event
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Tool', 'Copy', 'Password Generator');
    }
}

// Generate new password with current settings
function generateNewPassword() {
    document.getElementById('passwordForm').submit();
}

// Real-time strength checking
function checkPasswordStrength() {
    const length = parseInt(document.getElementById('lengthSlider').value);
    const hasLower = document.getElementById('lowercase').checked;
    const hasUpper = document.getElementById('uppercase').checked;
    const hasNumbers = document.getElementById('numbers').checked;
    const hasSymbols = document.getElementById('symbols').checked;
    
    let score = 0;
    
    // Length scoring
    if (length >= 8) score += 1;
    if (length >= 12) score += 1;
    if (length >= 16) score += 1;
    
    // Character type scoring
    if (hasLower) score += 1;
    if (hasUpper) score += 1;
    if (hasNumbers) score += 1;
    if (hasSymbols) score += 1;
    
    return score;
}

// Add event listeners for real-time updates
document.addEventListener('DOMContentLoaded', function() {
    const inputs = ['lengthSlider', 'lowercase', 'uppercase', 'numbers', 'symbols'];
    inputs.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', function() {
                // Optional: Real-time strength indication
                const score = checkPasswordStrength();
                // You could update a strength indicator here
            });
        }
    });
});

<?php if ($result): ?>
// Track successful generation
if (typeof AllInToolbox !== 'undefined') {
    AllInToolbox.analytics.trackEvent('Tool', 'Generate', 'Password Generator');
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + Enter to generate new password
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        generateNewPassword();
    }
    
    // Ctrl/Cmd + C when password field is focused to copy
    if ((e.ctrlKey || e.metaKey) && e.key === 'c' && 
        document.activeElement === document.getElementById('generatedPassword')) {
        copyPassword();
    }
});
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>