<?php
// tools/password-generator.php
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Şifre üretme fonksiyonu
function generatePassword($length = 12, $includeUpper = true, $includeLower = true, $includeNumbers = true, $includeSymbols = true) {
    $chars = '';
    if ($includeLower) $chars .= 'abcdefghijklmnopqrstuvwxyz';
    if ($includeUpper) $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if ($includeNumbers) $chars .= '0123456789';
    if ($includeSymbols) $chars .= '!@#$%^&*()_+-=[]{}|;:,.<>?';
    
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

$password = '';
if ($_POST || !empty($_GET['generate'])) {
    $length = intval($_POST['length'] ?? $_GET['length'] ?? 12);
    $includeUpper = isset($_POST['uppercase']) || isset($_GET['uppercase']);
    $includeLower = isset($_POST['lowercase']) || isset($_GET['lowercase']) || (!$_POST && !$_GET);
    $includeNumbers = isset($_POST['numbers']) || isset($_GET['numbers']) || (!$_POST && !$_GET);
    $includeSymbols = isset($_POST['symbols']) || isset($_GET['symbols']);
    
    $length = max(4, min(50, $length)); // 4-50 arası
    $password = generatePassword($length, $includeUpper, $includeLower, $includeNumbers, $includeSymbols);
}

include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">
        <h1><i class="fas fa-key text-primary"></i> 
            <?php echo ($currentLang === 'tr') ? 'Şifre Üretici' : 'Password Generator'; ?>
        </h1>
        <p class="lead">
            <?php echo ($currentLang === 'tr') ? 'Güvenli şifre oluşturun' : 'Generate secure passwords'; ?>
        </p>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" id="passwordForm">
                            <div class="mb-3">
                                <label class="form-label">
                                    <?php echo ($currentLang === 'tr') ? 'Şifre Uzunluğu' : 'Password Length'; ?>
                                </label>
                                <input type="range" class="form-range" name="length" id="lengthSlider" 
                                       min="4" max="50" value="<?php echo $_POST['length'] ?? 12; ?>" 
                                       oninput="document.getElementById('lengthValue').textContent = this.value">
                                <div class="text-center">
                                    <span id="lengthValue"><?php echo $_POST['length'] ?? 12; ?></span> 
                                    <?php echo ($currentLang === 'tr') ? 'karakter' : 'characters'; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="lowercase" id="lowercase" 
                                           <?php echo (!$_POST || isset($_POST['lowercase'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="lowercase">
                                        <?php echo ($currentLang === 'tr') ? 'Küçük harfler (a-z)' : 'Lowercase letters (a-z)'; ?>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="uppercase" id="uppercase"
                                           <?php echo isset($_POST['uppercase']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="uppercase">
                                        <?php echo ($currentLang === 'tr') ? 'Büyük harfler (A-Z)' : 'Uppercase letters (A-Z)'; ?>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="numbers" id="numbers"
                                           <?php echo (!$_POST || isset($_POST['numbers'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="numbers">
                                        <?php echo ($currentLang === 'tr') ? 'Sayılar (0-9)' : 'Numbers (0-9)'; ?>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="symbols" id="symbols"
                                           <?php echo isset($_POST['symbols']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="symbols">
                                        <?php echo ($currentLang === 'tr') ? 'Semboller (!@#$...)' : 'Symbols (!@#$...)'; ?>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-sync-alt"></i> 
                                <?php echo ($currentLang === 'tr') ? 'Şifre Üret' : 'Generate Password'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <?php if ($password): ?>
                <div class="card border-success">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <?php echo ($currentLang === 'tr') ? 'Oluşturulan Şifre' : 'Generated Password'; ?>
                        </h5>
                        
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="generatedPassword" 
                                   value="<?php echo htmlspecialchars($password); ?>" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyPassword()">
                                <i class="fas fa-copy"></i> 
                                <?php echo ($currentLang === 'tr') ? 'Kopyala' : 'Copy'; ?>
                            </button>
                        </div>
                        
                        <div class="small text-muted">
                            <strong><?php echo ($currentLang === 'tr') ? 'Şifre Gücü:' : 'Password Strength:'; ?></strong>
                            <?php 
                            $strength = strlen($password);
                            if ($strength >= 16) {
                                echo '<span class="text-success">' . (($currentLang === 'tr') ? 'Çok Güçlü' : 'Very Strong') . '</span>';
                            } elseif ($strength >= 12) {
                                echo '<span class="text-info">' . (($currentLang === 'tr') ? 'Güçlü' : 'Strong') . '</span>';
                            } elseif ($strength >= 8) {
                                echo '<span class="text-warning">' . (($currentLang === 'tr') ? 'Orta' : 'Medium') . '</span>';
                            } else {
                                echo '<span class="text-danger">' . (($currentLang === 'tr') ? 'Zayıf' : 'Weak') . '</span>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <h6><?php echo ($currentLang === 'tr') ? 'Güvenli Şifre İpuçları' : 'Secure Password Tips'; ?></h6>
                        <ul class="small">
                            <li><?php echo ($currentLang === 'tr') ? 'En az 12 karakter kullanın' : 'Use at least 12 characters'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? 'Büyük-küçük harf karışımı' : 'Mix uppercase and lowercase'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? 'Sayı ve sembol ekleyin' : 'Include numbers and symbols'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? 'Tahmin edilebilir kelimeler kullanmayın' : 'Avoid predictable words'; ?></li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function copyPassword() {
    const passwordField = document.getElementById('generatedPassword');
    passwordField.select();
    passwordField.setSelectionRange(0, 99999);
    
    try {
        document.execCommand('copy');
        alert('<?php echo ($currentLang === 'tr') ? 'Şifre kopyalandı!' : 'Password copied!'; ?>');
    } catch (err) {
        alert('<?php echo ($currentLang === 'tr') ? 'Kopyalama başarısız!' : 'Copy failed!'; ?>');
    }
}

// Auto-generate on page load
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!$password): ?>
    // İlk yüklemede otomatik şifre üret
    document.getElementById('passwordForm').submit();
    <?php endif; ?>
});
</script>
</body>
</html>