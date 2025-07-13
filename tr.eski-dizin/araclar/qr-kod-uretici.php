<?php
// tr/araclar/qr-kod-uretici.php
session_start();

// Konfigürasyonu yükle
require_once '../../config/config.php';
require_once '../../config/functions.php';

// Dil ayarla
setLanguage('tr');

// Sayfa bilgileri
$pageTitle = 'QR Kod Üretici - Ücretsiz QR Kod Oluştur | AllInToolbox';
$pageDescription = 'Ücretsiz QR kod üreticisi ile URL, metin, telefon numarası ve e-posta için QR kod oluşturun. Hızlı ve kolay QR kod üretimi.';
$pageKeywords = 'QR kod üretici, QR kod oluştur, QR kod generator, ücretsiz QR kod';

// QR kod türleri
$qrTypes = [
    'text' => 'Metin',
    'url' => 'Web Sitesi URL',
    'email' => 'E-posta',
    'phone' => 'Telefon',
    'sms' => 'SMS',
    'wifi' => 'WiFi',
    'vcard' => 'Kartvizit'
];

// Form işleme
$result = null;
$error = null;

if ($_POST) {
    $type = $_POST['type'] ?? 'text';
    $data = '';
    
    switch ($type) {
        case 'text':
            $data = $_POST['text'] ?? '';
            break;
            
        case 'url':
            $url = $_POST['url'] ?? '';
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                if (!preg_match('/^https?:\/\//', $url)) {
                    $url = 'https://' . $url;
                }
            }
            $data = $url;
            break;
            
        case 'email':
            $email = $_POST['email'] ?? '';
            $subject = $_POST['email_subject'] ?? '';
            $body = $_POST['email_body'] ?? '';
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data = "mailto:$email";
                if ($subject) $data .= "?subject=" . urlencode($subject);
                if ($body) $data .= ($subject ? "&" : "?") . "body=" . urlencode($body);
            } else {
                $error = 'Geçerli bir e-posta adresi girin.';
            }
            break;
            
        case 'phone':
            $phone = preg_replace('/[^0-9+]/', '', $_POST['phone'] ?? '');
            $data = "tel:$phone";
            break;
            
        case 'sms':
            $phone = preg_replace('/[^0-9+]/', '', $_POST['sms_phone'] ?? '');
            $message = $_POST['sms_message'] ?? '';
            $data = "sms:$phone" . ($message ? "?body=" . urlencode($message) : '');
            break;
            
        case 'wifi':
            $ssid = $_POST['wifi_ssid'] ?? '';
            $password = $_POST['wifi_password'] ?? '';
            $security = $_POST['wifi_security'] ?? 'WPA';
            $hidden = isset($_POST['wifi_hidden']) ? 'true' : 'false';
            $data = "WIFI:T:$security;S:$ssid;P:$password;H:$hidden;;";
            break;
            
        case 'vcard':
            $name = $_POST['vcard_name'] ?? '';
            $phone = $_POST['vcard_phone'] ?? '';
            $email = $_POST['vcard_email'] ?? '';
            $organization = $_POST['vcard_org'] ?? '';
            $data = "BEGIN:VCARD\nVERSION:3.0\nFN:$name\n";
            if ($phone) $data .= "TEL:$phone\n";
            if ($email) $data .= "EMAIL:$email\n";
            if ($organization) $data .= "ORG:$organization\n";
            $data .= "END:VCARD";
            break;
    }
    
    if ($data && !$error) {
        // QR kod URL'si oluştur (Google Charts API kullanarak)
        $size = $_POST['size'] ?? '200';
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . urlencode($data);
        
        $result = [
            'type' => $type,
            'data' => $data,
            'qr_url' => $qrUrl,
            'size' => $size
        ];
    } elseif (!$error) {
        $error = 'Lütfen gerekli alanları doldurun.';
    }
}

// Header'ı dahil et
include '../../includes/header.php';
?>

<!-- Breadcrumb -->
<?php
echo generateBreadcrumb([
    ['title' => translate('home'), 'url' => '/tr/'],
    ['title' => 'Web Araçları', 'url' => '/tr/category/web.php'],
    ['title' => 'QR Kod Üretici']
]);
?>

<!-- Tool Container -->
<div class="tool-container">
    <div class="tool-header">
        <h1><i class="fas fa-qrcode text-primary"></i> QR Kod Üretici</h1>
        <p class="lead">Farklı türlerde QR kodları kolayca oluşturun</p>
    </div>
    
    <div class="row">
        <div class="col-lg-6">
            <!-- QR Form -->
            <div class="tool-form">
                <form method="POST" id="qrForm">
                    <!-- QR Türü Seçimi -->
                    <div class="mb-3">
                        <label for="type" class="form-label">
                            <i class="fas fa-list"></i> QR Kod Türü
                        </label>
                        <select class="form-control" id="type" name="type" onchange="toggleFields()" required>
                            <?php foreach ($qrTypes as $value => $label): ?>
                                <option value="<?php echo $value; ?>" <?php echo (isset($_POST['type']) && $_POST['type'] === $value) ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Metin -->
                    <div class="qr-field" id="field-text">
                        <div class="mb-3">
                            <label for="text" class="form-label">Metin</label>
                            <textarea class="form-control" id="text" name="text" rows="3" placeholder="QR koda dönüştürülecek metni girin"><?php echo htmlspecialchars($_POST['text'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- URL -->
                    <div class="qr-field" id="field-url" style="display: none;">
                        <div class="mb-3">
                            <label for="url" class="form-label">Web Sitesi URL</label>
                            <input type="url" class="form-control" id="url" name="url" placeholder="https://example.com" value="<?php echo htmlspecialchars($_POST['url'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <!-- E-posta -->
                    <div class="qr-field" id="field-email" style="display: none;">
                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta Adresi</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="ornek@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email_subject" class="form-label">Konu (Opsiyonel)</label>
                            <input type="text" class="form-control" id="email_subject" name="email_subject" placeholder="E-posta konusu" value="<?php echo htmlspecialchars($_POST['email_subject'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email_body" class="form-label">Mesaj (Opsiyonel)</label>
                            <textarea class="form-control" id="email_body" name="email_body" rows="2" placeholder="E-posta içeriği"><?php echo htmlspecialchars($_POST['email_body'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Telefon -->
                    <div class="qr-field" id="field-phone" style="display: none;">
                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefon Numarası</label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="+90 555 123 45 67" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <!-- SMS -->
                    <div class="qr-field" id="field-sms" style="display: none;">
                        <div class="mb-3">
                            <label for="sms_phone" class="form-label">Telefon Numarası</label>
                            <input type="tel" class="form-control" id="sms_phone" name="sms_phone" placeholder="+90 555 123 45 67" value="<?php echo htmlspecialchars($_POST['sms_phone'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="sms_message" class="form-label">SMS Mesajı (Opsiyonel)</label>
                            <textarea class="form-control" id="sms_message" name="sms_message" rows="2" placeholder="SMS içeriği"><?php echo htmlspecialchars($_POST['sms_message'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- WiFi -->
                    <div class="qr-field" id="field-wifi" style="display: none;">
                        <div class="mb-3">
                            <label for="wifi_ssid" class="form-label">Ağ Adı (SSID)</label>
                            <input type="text" class="form-control" id="wifi_ssid" name="wifi_ssid" placeholder="WiFi ağ adı" value="<?php echo htmlspecialchars($_POST['wifi_ssid'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="wifi_password" class="form-label">Şifre</label>
                            <input type="password" class="form-control" id="wifi_password" name="wifi_password" placeholder="WiFi şifresi" value="<?php echo htmlspecialchars($_POST['wifi_password'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="wifi_security" class="form-label">Güvenlik Türü</label>
                            <select class="form-control" id="wifi_security" name="wifi_security">
                                <option value="WPA">WPA/WPA2</option>
                                <option value="WEP">WEP</option>
                                <option value="">Açık Ağ</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="wifi_hidden" name="wifi_hidden">
                                <label class="form-check-label" for="wifi_hidden">
                                    Gizli ağ
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- vCard -->
                    <div class="qr-field" id="field-vcard" style="display: none;">
                        <div class="mb-3">
                            <label for="vcard_name" class="form-label">Ad Soyad</label>
                            <input type="text" class="form-control" id="vcard_name" name="vcard_name" placeholder="John Doe" value="<?php echo htmlspecialchars($_POST['vcard_name'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="vcard_phone" class="form-label">Telefon</label>
                            <input type="tel" class="form-control" id="vcard_phone" name="vcard_phone" placeholder="+90 555 123 45 67" value="<?php echo htmlspecialchars($_POST['vcard_phone'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="vcard_email" class="form-label">E-posta</label>
                            <input type="email" class="form-control" id="vcard_email" name="vcard_email" placeholder="john@example.com" value="<?php echo htmlspecialchars($_POST['vcard_email'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="vcard_org" class="form-label">Şirket/Organizasyon</label>
                            <input type="text" class="form-control" id="vcard_org" name="vcard_org" placeholder="Şirket Adı" value="<?php echo htmlspecialchars($_POST['vcard_org'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <!-- QR Boyutu -->
                    <div class="mb-3">
                        <label for="size" class="form-label">
                            <i class="fas fa-expand-arrows-alt"></i> QR Kod Boyutu
                        </label>
                        <select class="form-control" id="size" name="size">
                            <option value="150">150x150 px</option>
                            <option value="200" selected>200x200 px</option>
                            <option value="300">300x300 px</option>
                            <option value="400">400x400 px</option>
                            <option value="500">500x500 px</option>
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="generateBtn">
                            <i class="fas fa-magic"></i> QR Kod Oluştur
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('qrForm').reset(); toggleFields();">
                            <i class="fas fa-eraser"></i> Temizle
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-lg-6">
            <?php if ($result): ?>
            <!-- Sonuç -->
            <div class="qr-code-container">
                <h4><i class="fas fa-qrcode"></i> QR Kodunuz</h4>
                <div class="text-center mb-4">
                    <img src="<?php echo $result['qr_url']; ?>" 
                         alt="QR Kod" 
                         id="qrCodeImage"
                         class="img-fluid">
                </div>
                
                <div class="qr-info mb-3">
                    <p><strong>Tür:</strong> <?php echo $qrTypes[$result['type']]; ?></p>
                    <p><strong>Boyut:</strong> <?php echo $result['size']; ?>x<?php echo $result['size']; ?> px</p>
                    <div class="alert alert-light">
                        <small><strong>İçerik:</strong><br><?php echo nl2br(htmlspecialchars(substr($result['data'], 0, 100) . (strlen($result['data']) > 100 ? '...' : ''))); ?></small>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="<?php echo $result['qr_url']; ?>" 
                       download="qr-code.png" 
                       class="btn btn-success">
                        <i class="fas fa-download"></i> İndir
                    </a>
                    <button class="btn btn-outline-primary" onclick="copyQRUrl()">
                        <i class="fas fa-copy"></i> URL'yi Kopyala
                    </button>
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print"></i> Yazdır
                    </button>
                </div>
            </div>
            <?php elseif ($error): ?>
            <!-- Hata -->
            <div class="tool-result error">
                <h4><i class="fas fa-exclamation-triangle"></i> Hata</h4>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
            <?php else: ?>
            <!-- Bilgi -->
            <div class="alert alert-info alert-custom">
                <h5><i class="fas fa-info-circle"></i> QR Kod Türleri</h5>
                <ul class="mb-0">
                    <li><strong>Metin:</strong> Herhangi bir metin</li>
                    <li><strong>URL:</strong> Web sitesi bağlantısı</li>
                    <li><strong>E-posta:</strong> E-posta gönderme</li>
                    <li><strong>Telefon:</strong> Telefon arama</li>
                    <li><strong>SMS:</strong> SMS gönderme</li>
                    <li><strong>WiFi:</strong> WiFi ağına bağlanma</li>
                    <li><strong>Kartvizit:</strong> İletişim bilgileri</li>
                </ul>
            </div>
            
            <div class="alert alert-success alert-custom">
                <h6><i class="fas fa-lightbulb"></i> İpuçları</h6>
                <ul class="mb-0">
                    <li>QR kodunuz cep telefonu kamerası ile okunabilir</li>
                    <li>Daha büyük boyutlar daha kolay okunur</li>
                    <li>QR kodları yazdırırken yüksek kalite kullanın</li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// QR Generator specific JavaScript
function toggleFields() {
    const selectedType = document.getElementById('type').value;
    const allFields = document.querySelectorAll('.qr-field');
    
    // Hide all fields
    allFields.forEach(field => field.style.display = 'none');
    
    // Show selected field
    const targetField = document.getElementById('field-' + selectedType);
    if (targetField) {
        targetField.style.display = 'block';
    }
}

function copyQRUrl() {
    const qrImage = document.getElementById('qrCodeImage');
    if (qrImage) {
        AllInToolbox.utils.copyToClipboard(qrImage.src);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize field visibility
    toggleFields();
    
    // Track tool usage
    AllInToolbox.analytics.trackEvent('Tool', 'View', 'QR Generator');
    AllInToolbox.storage.addRecentTool('qr-generator', 'QR Kod Üretici', '/tr/araclar/qr-kod-uretici.php');
    
    const form = document.getElementById('qrForm');
    const generateBtn = document.getElementById('generateBtn');
    
    // Form submission
    form.addEventListener('submit', function(e) {
        AllInToolbox.utils.showLoading(generateBtn);
        AllInToolbox.analytics.trackToolUsage('QR Generator');
        
        // Simulate processing time
        setTimeout(() => {
            AllInToolbox.utils.showLoading(generateBtn, false);
        }, 1000);
    });
});

<?php if ($result): ?>
// Track successful generation
AllInToolbox.analytics.trackEvent('Tool', 'Generate', 'QR Code - <?php echo $result['type']; ?>');
<?php endif; ?>
</script>

<?php include '../../includes/footer.php'; ?>