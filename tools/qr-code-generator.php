<?php
// tools/qr-code-generator.php - DÜZELTİLMİŞ VERSİYON
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Sayfa meta bilgileri
$pageTitle = ($currentLang === 'tr') ? 'QR Kod Üretici' : 'QR Code Generator';
$pageDescription = ($currentLang === 'tr') ? 
    'Ücretsiz QR kod üretici. Metin, URL, e-posta, telefon, WiFi ve kartvizit için QR kod oluşturun.' :
    'Free QR code generator. Create QR codes for text, URL, email, phone, WiFi and business cards.';

// QR Kod türleri
$qrTypes = [
    'text' => [
        'tr' => 'Metin',
        'en' => 'Text',
        'icon' => 'fas fa-font'
    ],
    'url' => [
        'tr' => 'Web Sitesi URL',
        'en' => 'Website URL',
        'icon' => 'fas fa-link'
    ],
    'email' => [
        'tr' => 'E-posta',
        'en' => 'Email',
        'icon' => 'fas fa-envelope'
    ],
    'phone' => [
        'tr' => 'Telefon',
        'en' => 'Phone',
        'icon' => 'fas fa-phone'
    ],
    'sms' => [
        'tr' => 'SMS',
        'en' => 'SMS',
        'icon' => 'fas fa-sms'
    ],
    'wifi' => [
        'tr' => 'WiFi',
        'en' => 'WiFi',
        'icon' => 'fas fa-wifi'
    ],
    'vcard' => [
        'tr' => 'Kartvizit',
        'en' => 'Business Card',
        'icon' => 'fas fa-address-card'
    ]
];

// Basit QR kod oluşturma fonksiyonu (Google Charts API kullanarak)
function generateQRCode($data, $size = 300) {
    $encodedData = urlencode($data);
    $qrUrl = "https://chart.googleapis.com/chart?chs={$size}x{$size}&cht=qr&chl={$encodedData}&choe=UTF-8";
    return $qrUrl;
}

// Alternatif: QR Server API
function generateQRCodeAlt($data, $size = 300) {
    $encodedData = urlencode($data);
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encodedData}";
    return $qrUrl;
}

// Form işleme
$result = null;
$error = null;

if ($_POST) {
    $qrType = $_POST['qr_type'] ?? 'text';
    $qrData = '';
    $size = intval($_POST['qr_size'] ?? 300);
    
    // Size validation
    $size = max(150, min(500, $size));
    
    switch ($qrType) {
        case 'text':
            $qrData = $_POST['text_content'] ?? '';
            break;
            
        case 'url':
            $url = $_POST['url_content'] ?? '';
            // URL doğrulama
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                if (!preg_match('/^https?:\/\//', $url)) {
                    $url = 'https://' . $url;
                }
            }
            $qrData = $url;
            break;
            
        case 'email':
            $email = $_POST['email_address'] ?? '';
            $subject = $_POST['email_subject'] ?? '';
            $body = $_POST['email_body'] ?? '';
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $qrData = "mailto:$email";
                if ($subject) $qrData .= "?subject=" . urlencode($subject);
                if ($body) $qrData .= ($subject ? "&" : "?") . "body=" . urlencode($body);
            } else {
                $error = ($currentLang === 'tr') ? 'Geçerli bir e-posta adresi girin!' : 'Enter a valid email address!';
            }
            break;
            
        case 'phone':
            $phone = $_POST['phone_number'] ?? '';
            $qrData = "tel:$phone";
            break;
            
        case 'sms':
            $phone = $_POST['sms_phone'] ?? '';
            $message = $_POST['sms_message'] ?? '';
            $qrData = "sms:$phone";
            if ($message) $qrData .= "?body=" . urlencode($message);
            break;
            
        case 'wifi':
            $ssid = $_POST['wifi_ssid'] ?? '';
            $password = $_POST['wifi_password'] ?? '';
            $security = $_POST['wifi_security'] ?? 'WPA';
            $hidden = isset($_POST['wifi_hidden']) ? 'true' : 'false';
            
            $qrData = "WIFI:T:$security;S:$ssid;P:$password;H:$hidden;;";
            break;
            
        case 'vcard':
            $name = $_POST['vcard_name'] ?? '';
            $phone = $_POST['vcard_phone'] ?? '';
            $email = $_POST['vcard_email'] ?? '';
            $org = $_POST['vcard_org'] ?? '';
            
            $qrData = "BEGIN:VCARD\nVERSION:3.0\nFN:$name\n";
            if ($phone) $qrData .= "TEL:$phone\n";
            if ($email) $qrData .= "EMAIL:$email\n";
            if ($org) $qrData .= "ORG:$org\n";
            $qrData .= "END:VCARD";
            break;
    }
    
    if (!$error && !empty($qrData)) {
        // QR kodunu oluştur
        $qrImageUrl = generateQRCodeAlt($qrData, $size);
        
        $result = [
            'type' => $qrType,
            'data' => $qrData,
            'display_data' => $qrData,
            'qr_image_url' => $qrImageUrl,
            'size' => $size,
            'download_url' => $qrImageUrl . '&download=1'
        ];
    } elseif (!$error) {
        $error = ($currentLang === 'tr') ? 'Lütfen gerekli alanları doldurun!' : 'Please fill in the required fields!';
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
                <h1><i class="fas fa-qrcode text-primary"></i> <?php echo $pageTitle; ?></h1>
                <p class="lead"><?php echo $pageDescription; ?></p>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <!-- QR Form -->
                    <div class="tool-form">
                        <form method="POST" id="qrForm">
                            <!-- QR Type Selection -->
                            <div class="mb-3">
                                <label for="qr_type" class="form-label">
                                    <i class="fas fa-list"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'QR Kod Türü' : 'QR Code Type'; ?>
                                </label>
                                <select class="form-control" id="qr_type" name="qr_type" onchange="showQRFields()" required>
                                    <?php foreach ($qrTypes as $type => $info): ?>
                                        <option value="<?php echo $type; ?>" 
                                                <?php echo (($_POST['qr_type'] ?? 'text') === $type) ? 'selected' : ''; ?>>
                                            <?php echo $info[$currentLang]; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- QR Size -->
                            <div class="mb-3">
                                <label for="qr_size" class="form-label">
                                    <i class="fas fa-expand"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'QR Kod Boyutu' : 'QR Code Size'; ?>
                                </label>
                                <select class="form-control" id="qr_size" name="qr_size">
                                    <option value="150" <?php echo (($_POST['qr_size'] ?? '300') === '150') ? 'selected' : ''; ?>>150x150 px</option>
                                    <option value="200" <?php echo (($_POST['qr_size'] ?? '300') === '200') ? 'selected' : ''; ?>>200x200 px</option>
                                    <option value="300" <?php echo (($_POST['qr_size'] ?? '300') === '300') ? 'selected' : ''; ?>>300x300 px</option>
                                    <option value="400" <?php echo (($_POST['qr_size'] ?? '300') === '400') ? 'selected' : ''; ?>>400x400 px</option>
                                    <option value="500" <?php echo (($_POST['qr_size'] ?? '300') === '500') ? 'selected' : ''; ?>>500x500 px</option>
                                </select>
                            </div>

                            <!-- Text QR -->
                            <div id="text_fields" class="qr-fields" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'Metin İçeriği' : 'Text Content'; ?>
                                    </label>
                                    <textarea name="text_content" class="form-control" rows="4" 
                                              placeholder="<?php echo ($currentLang === 'tr') ? 'QR koda dönüştürülecek metni girin...' : 'Enter text to convert to QR code...'; ?>"><?php echo $_POST['text_content'] ?? ''; ?></textarea>
                                </div>
                            </div>

                            <!-- URL QR -->
                            <div id="url_fields" class="qr-fields" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'Web Sitesi URL' : 'Website URL'; ?>
                                    </label>
                                    <input type="url" name="url_content" class="form-control" 
                                           placeholder="https://example.com"
                                           value="<?php echo $_POST['url_content'] ?? ''; ?>">
                                </div>
                            </div>

                            <!-- Email QR -->
                            <div id="email_fields" class="qr-fields" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'E-posta Adresi' : 'Email Address'; ?>
                                    </label>
                                    <input type="email" name="email_address" class="form-control" 
                                           placeholder="ornek@email.com"
                                           value="<?php echo $_POST['email_address'] ?? ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'Konu (Opsiyonel)' : 'Subject (Optional)'; ?>
                                    </label>
                                    <input type="text" name="email_subject" class="form-control" 
                                           value="<?php echo $_POST['email_subject'] ?? ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'Mesaj (Opsiyonel)' : 'Message (Optional)'; ?>
                                    </label>
                                    <textarea name="email_body" class="form-control" rows="3"><?php echo $_POST['email_body'] ?? ''; ?></textarea>
                                </div>
                            </div>

                            <!-- Phone QR -->
                            <div id="phone_fields" class="qr-fields" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'Telefon Numarası' : 'Phone Number'; ?>
                                    </label>
                                    <input type="tel" name="phone_number" class="form-control" 
                                           placeholder="+90 555 123 45 67"
                                           value="<?php echo $_POST['phone_number'] ?? ''; ?>">
                                </div>
                            </div>

                            <!-- SMS QR -->
                            <div id="sms_fields" class="qr-fields" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'Telefon Numarası' : 'Phone Number'; ?>
                                    </label>
                                    <input type="tel" name="sms_phone" class="form-control" 
                                           placeholder="+90 555 123 45 67"
                                           value="<?php echo $_POST['sms_phone'] ?? ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'SMS Mesajı (Opsiyonel)' : 'SMS Message (Optional)'; ?>
                                    </label>
                                    <textarea name="sms_message" class="form-control" rows="3"><?php echo $_POST['sms_message'] ?? ''; ?></textarea>
                                </div>
                            </div>

                            <!-- WiFi QR -->
                            <div id="wifi_fields" class="qr-fields" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'Ağ Adı (SSID)' : 'Network Name (SSID)'; ?>
                                    </label>
                                    <input type="text" name="wifi_ssid" class="form-control" 
                                           value="<?php echo $_POST['wifi_ssid'] ?? ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'Şifre' : 'Password'; ?>
                                    </label>
                                    <input type="text" name="wifi_password" class="form-control" 
                                           value="<?php echo $_POST['wifi_password'] ?? ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'Güvenlik Türü' : 'Security Type'; ?>
                                    </label>
                                    <select name="wifi_security" class="form-control">
                                        <option value="WPA" <?php echo (($_POST['wifi_security'] ?? 'WPA') === 'WPA') ? 'selected' : ''; ?>>WPA/WPA2</option>
                                        <option value="WEP" <?php echo (($_POST['wifi_security'] ?? '') === 'WEP') ? 'selected' : ''; ?>>WEP</option>
                                        <option value="nopass" <?php echo (($_POST['wifi_security'] ?? '') === 'nopass') ? 'selected' : ''; ?>><?php echo ($currentLang === 'tr') ? 'Şifresiz' : 'No Password'; ?></option>
                                    </select>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="wifi_hidden" id="wifi_hidden"
                                           <?php echo isset($_POST['wifi_hidden']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="wifi_hidden">
                                        <?php echo ($currentLang === 'tr') ? 'Gizli ağ' : 'Hidden network'; ?>
                                    </label>
                                </div>
                            </div>

                            <!-- vCard QR -->
                            <div id="vcard_fields" class="qr-fields" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'Ad Soyad' : 'Full Name'; ?>
                                    </label>
                                    <input type="text" name="vcard_name" class="form-control" 
                                           value="<?php echo $_POST['vcard_name'] ?? ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'Telefon' : 'Phone'; ?>
                                    </label>
                                    <input type="tel" name="vcard_phone" class="form-control" 
                                           value="<?php echo $_POST['vcard_phone'] ?? ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'E-posta' : 'Email'; ?>
                                    </label>
                                    <input type="email" name="vcard_email" class="form-control" 
                                           value="<?php echo $_POST['vcard_email'] ?? ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'Şirket/Organizasyon' : 'Company/Organization'; ?>
                                    </label>
                                    <input type="text" name="vcard_org" class="form-control" 
                                           value="<?php echo $_POST['vcard_org'] ?? ''; ?>">
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="generateBtn">
                                    <i class="fas fa-qrcode"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'QR Kod Oluştur' : 'Generate QR Code'; ?>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('qrForm').reset(); showQRFields();">
                                    <i class="fas fa-eraser"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Temizle' : 'Clear'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- QR Info -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> 
                            <?php echo ($currentLang === 'tr') ? 'QR Kod Üretici Hakkında' : 'About QR Code Generator'; ?>
                        </h6>
                        <p class="mb-0">
                            <?php echo ($currentLang === 'tr') ? 
                                'Farklı türlerde QR kodları ücretsiz oluşturun. Güvenilir API kullanılır.' :
                                'Create different types of QR codes for free. Uses reliable API.'; ?>
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <?php if ($result): ?>
                    <!-- Sonuç -->
                    <div class="tool-result">
                        <h4><i class="fas fa-qrcode"></i> 
                            <?php echo ($currentLang === 'tr') ? 'QR Kodunuz' : 'Your QR Code'; ?>
                        </h4>
                        <div class="qr-code-container text-center">
                            <!-- QR Code Image -->
                            <div class="qr-image mb-3">
                                <img src="<?php echo $result['qr_image_url']; ?>" 
                                     alt="QR Code" 
                                     class="img-fluid border rounded"
                                     style="max-width: <?php echo $result['size']; ?>px;">
                            </div>
                            
                            <div class="d-grid gap-2 mt-3">
                                <a href="<?php echo $result['download_url']; ?>" 
                                   class="btn btn-success" 
                                   download="qrcode.png">
                                    <i class="fas fa-download"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'QR Kodu İndir' : 'Download QR Code'; ?>
                                </a>
                                <button class="btn btn-light" onclick="copyQRData()">
                                    <i class="fas fa-copy"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Veriyi Kopyala' : 'Copy Data'; ?>
                                </button>
                                <button class="btn btn-outline-light" onclick="window.print()">
                                    <i class="fas fa-print"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Yazdır' : 'Print'; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- QR Data Display -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6><?php echo ($currentLang === 'tr') ? 'QR Kod İçeriği' : 'QR Code Content'; ?></h6>
                            <div class="alert alert-light">
                                <code id="qrData"><?php echo htmlspecialchars($result['display_data']); ?></code>
                            </div>
                            <small class="text-muted">
                                <?php echo ($currentLang === 'tr') ? 'Tür: ' : 'Type: '; ?>
                                <strong><?php echo $qrTypes[$result['type']][$currentLang]; ?></strong>
                                <br>
                                <?php echo ($currentLang === 'tr') ? 'Boyut: ' : 'Size: '; ?>
                                <strong><?php echo $result['size']; ?>x<?php echo $result['size']; ?> px</strong>
                            </small>
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
                                <?php echo ($currentLang === 'tr') ? 'QR Kod Örnekleri' : 'QR Code Examples'; ?>
                            </h5>
                            <div class="row g-2">
                                <?php 
                                $examples = [
                                    ['type' => 'url', 'text' => ($currentLang === 'tr') ? 'Web Sitesi' : 'Website'],
                                    ['type' => 'email', 'text' => ($currentLang === 'tr') ? 'E-posta' : 'Email'],
                                    ['type' => 'wifi', 'text' => 'WiFi'],
                                    ['type' => 'vcard', 'text' => ($currentLang === 'tr') ? 'Kartvizit' : 'Business Card']
                                ];
                                foreach ($examples as $example): ?>
                                    <div class="col-6 mb-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" 
                                                onclick="setQRType('<?php echo $example['type']; ?>')">
                                            <i class="<?php echo $qrTypes[$example['type']]['icon']; ?>"></i> 
                                            <?php echo $example['text']; ?>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Ad Space -->
        <?php echo renderAdSpace('content', 'large'); ?>

        <!-- QR Code Information -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3><i class="fas fa-book"></i> 
                            <?php echo ($currentLang === 'tr') ? 'QR Kod Hakkında Detaylı Bilgi' : 'Detailed Information About QR Codes'; ?>
                        </h3>
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'QR Kod Nedir?' : 'What is a QR Code?'; ?></h5>
                                <p>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'QR (Quick Response) kod, 1994 yılında Japonya\'da geliştirilen iki boyutlu barkod türüdür. Hızlı okuma ve yüksek veri kapasitesi ile bilinir.' :
                                        'QR (Quick Response) code is a two-dimensional barcode type developed in Japan in 1994. It is known for fast reading and high data capacity.'; ?>
                                </p>
                                
                                <h5><?php echo ($currentLang === 'tr') ? 'Desteklenen Türler' : 'Supported Types'; ?></h5>
                                <ul>
                                    <?php foreach ($qrTypes as $type => $info): ?>
                                        <li>
                                            <i class="<?php echo $info['icon']; ?>"></i> 
                                            <strong><?php echo $info[$currentLang]; ?></strong>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                
                                <h5><?php echo ($currentLang === 'tr') ? 'Boyut Seçenekleri' : 'Size Options'; ?></h5>
                                <p>
                                    <?php echo ($currentLang === 'tr') ? 
                                        '150x150 px ile 500x500 px arasında farklı boyutlarda QR kod oluşturabilirsiniz.' :
                                        'You can create QR codes in different sizes from 150x150 px to 500x500 px.'; ?>
                                </p>
                            </div>
                            
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Nasıl Kullanılır?' : 'How to Use?'; ?></h5>
                                <ol>
                                    <li><?php echo ($currentLang === 'tr') ? 'QR kod türünü seçin' : 'Select QR code type'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Boyutu belirleyin' : 'Choose the size'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Gerekli bilgileri doldurun' : 'Fill in the required information'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'QR kod oluştur butonuna tıklayın' : 'Click generate QR code button'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'QR kodu indirin veya yazdırın' : 'Download or print the QR code'; ?></li>
                                </ol>
                                
                                <h5><?php echo ($currentLang === 'tr') ? 'Özellikler' : 'Features'; ?></h5>
                                <ul>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Güvenilir API kullanımı' : 
                                        'Reliable API usage'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Yüksek kaliteli QR kodlar' : 
                                        'High quality QR codes'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'PNG formatında indirme' : 
                                        'Download in PNG format'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Farklı boyut seçenekleri' : 
                                        'Different size options'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Mobil uyumlu okuma' : 
                                        'Mobile compatible reading'; ?></li>
                                </ul>
                                
                                <div class="alert alert-success">
                                    <i class="fas fa-shield-alt"></i>
                                    <strong><?php echo ($currentLang === 'tr') ? 'Güvenlik:' : 'Security:'; ?></strong>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'QR kodlarınız güvenli API ile oluşturulur ve verileriniz saklanmaz.' :
                                        'Your QR codes are created with secure API and your data is not stored.'; ?>
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
                            'name' => ($currentLang === 'tr') ? 'Şifre Üretici' : 'Password Generator',
                            'description' => ($currentLang === 'tr') ? 'Güvenli şifre oluşturun' : 'Generate secure passwords',
                            'url' => '/tools/password-generator.php?lang=' . $currentLang,
                            'icon' => 'fas fa-key'
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
// QR Code Generator specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('qrForm');
    const generateBtn = document.getElementById('generateBtn');
    const currentLang = '<?php echo $currentLang; ?>';
    
    // Track tool usage
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Tool', 'View', 'QR Code Generator');
        
        // Add to recent tools
        const toolName = currentLang === 'tr' ? 'QR Kod Üretici' : 'QR Code Generator';
        const toolUrl = '/tools/qr-code-generator.php?lang=' + currentLang;
        AllInToolbox.storage.addRecentTool('qr-code-generator', toolName, toolUrl);
    }
    
    // Show correct fields on page load
    showQRFields();
    
    // Form submission
    form.addEventListener('submit', function(e) {
        if (typeof AllInToolbox !== 'undefined') {
            AllInToolbox.utils.showLoading(generateBtn);
            AllInToolbox.analytics.trackToolUsage('QR Code Generator');
        }
        
        // Simulate processing time
        setTimeout(() => {
            if (typeof AllInToolbox !== 'undefined') {
                AllInToolbox.utils.showLoading(generateBtn, false);
            }
        }, 1000);
    });
});

// Show appropriate fields based on QR type
function showQRFields() {
    const qrType = document.getElementById('qr_type').value;
    const allFields = document.querySelectorAll('.qr-fields');
    
    // Hide all fields
    allFields.forEach(field => field.style.display = 'none');
    
    // Show selected field
    const selectedField = document.getElementById(qrType + '_fields');
    if (selectedField) {
        selectedField.style.display = 'block';
    }
}

// Set QR type (for examples)
function setQRType(type) {
    document.getElementById('qr_type').value = type;
    showQRFields();
}

// Copy QR Data
function copyQRData() {
    const qrDataElement = document.getElementById('qrData');
    if (qrDataElement) {
        const text = qrDataElement.textContent;
        if (typeof AllInToolbox !== 'undefined') {
            AllInToolbox.utils.copyToClipboard(text);
        } else {
            navigator.clipboard.writeText(text).then(() => {
                alert('<?php echo ($currentLang === 'tr') ? 'Veri kopyalandı!' : 'Data copied!'; ?>');
            }).catch(() => {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    alert('<?php echo ($currentLang === 'tr') ? 'Veri kopyalandı!' : 'Data copied!'; ?>');
                } catch (err) {
                    alert('<?php echo ($currentLang === 'tr') ? 'Kopyalama başarısız!' : 'Copy failed!'; ?>');
                }
                document.body.removeChild(textArea);
            });
        }
    }
}

<?php if ($result): ?>
// Track successful generation
if (typeof AllInToolbox !== 'undefined') {
    AllInToolbox.analytics.trackEvent('Tool', 'Generate', 'QR Code');
}
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>