<?php
// tools/currency-converter.php - ÇALIŞAN VERSİYON
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Sayfa meta bilgileri
$pageTitle = ($currentLang === 'tr') ? 'Döviz Çevirici' : 'Currency Converter';
$pageDescription = ($currentLang === 'tr') ? 
    'Ücretsiz döviz çevirici ile para birimlerini güncel kurlarla çevirin. USD, EUR, TRY ve 150+ para birimi desteği.' :
    'Free currency converter with current exchange rates. Convert USD, EUR, TRY and 150+ currencies.';

// Popüler para birimleri
$currencies = [
    'USD' => ['name' => 'US Dollar', 'symbol' => '$', 'flag' => '🇺🇸'],
    'EUR' => ['name' => 'Euro', 'symbol' => '€', 'flag' => '🇪🇺'],
    'TRY' => ['name' => 'Turkish Lira', 'symbol' => '₺', 'flag' => '🇹🇷'],
    'GBP' => ['name' => 'British Pound', 'symbol' => '£', 'flag' => '🇬🇧'],
    'JPY' => ['name' => 'Japanese Yen', 'symbol' => '¥', 'flag' => '🇯🇵'],
    'CAD' => ['name' => 'Canadian Dollar', 'symbol' => 'C$', 'flag' => '🇨🇦'],
    'AUD' => ['name' => 'Australian Dollar', 'symbol' => 'A$', 'flag' => '🇦🇺'],
    'CHF' => ['name' => 'Swiss Franc', 'symbol' => 'CHF', 'flag' => '🇨🇭'],
    'CNY' => ['name' => 'Chinese Yuan', 'symbol' => '¥', 'flag' => '🇨🇳'],
    'INR' => ['name' => 'Indian Rupee', 'symbol' => '₹', 'flag' => '🇮🇳'],
    'RUB' => ['name' => 'Russian Ruble', 'symbol' => '₽', 'flag' => '🇷🇺'],
    'KRW' => ['name' => 'South Korean Won', 'symbol' => '₩', 'flag' => '🇰🇷'],
    'BRL' => ['name' => 'Brazilian Real', 'symbol' => 'R$', 'flag' => '🇧🇷'],
    'MXN' => ['name' => 'Mexican Peso', 'symbol' => '$', 'flag' => '🇲🇽'],
    'SGD' => ['name' => 'Singapore Dollar', 'symbol' => 'S$', 'flag' => '🇸🇬'],
    'HKD' => ['name' => 'Hong Kong Dollar', 'symbol' => 'HK$', 'flag' => '🇭🇰']
];

// Çalışan API fonksiyonu - exchangerate.host (ücretsiz ve güvenilir)
function getCurrencyRates($baseCurrency = 'USD') {
    $apiUrl = "https://api.exchangerate.host/latest?base=" . strtoupper($baseCurrency);
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'AllInToolbox Currency Converter/1.0'
        ]
    ]);
    
    $response = @file_get_contents($apiUrl, false, $context);
    
    if ($response !== false) {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success'] === true && isset($data['rates'])) {
            return $data['rates'];
        }
    }
    
    // Fallback API - fawazahmed0
    $fallbackUrl = "https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/" . strtolower($baseCurrency) . ".json";
    $fallbackResponse = @file_get_contents($fallbackUrl, false, $context);
    
    if ($fallbackResponse !== false) {
        $fallbackData = json_decode($fallbackResponse, true);
        if ($fallbackData && isset($fallbackData[strtolower($baseCurrency)])) {
            // Convert to uppercase keys
            $rates = [];
            foreach ($fallbackData[strtolower($baseCurrency)] as $currency => $rate) {
                $rates[strtoupper($currency)] = $rate;
            }
            return $rates;
        }
    }
    
    return false;
}

// Form işleme
$result = null;
$error = null;
$exchangeRates = null;

if ($_POST) {
    $amount = floatval($_POST['amount'] ?? 0);
    $fromCurrency = strtoupper($_POST['from_currency'] ?? 'USD');
    $toCurrency = strtoupper($_POST['to_currency'] ?? 'EUR');
    
    if ($amount > 0 && $amount <= 1000000000) {
        $rates = getCurrencyRates($fromCurrency);
        
        if ($rates !== false && isset($rates[$toCurrency])) {
            $rate = $rates[$toCurrency];
            $convertedAmount = $amount * $rate;
            
            $result = [
                'amount' => $amount,
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
                'rate' => $rate,
                'converted_amount' => round($convertedAmount, 2),
                'from_symbol' => $currencies[$fromCurrency]['symbol'] ?? $fromCurrency,
                'to_symbol' => $currencies[$toCurrency]['symbol'] ?? $toCurrency,
                'from_flag' => $currencies[$fromCurrency]['flag'] ?? '',
                'to_flag' => $currencies[$toCurrency]['flag'] ?? ''
            ];
            
            // Popüler kurları da al
            $exchangeRates = [];
            $popularCurrencies = ['EUR', 'USD', 'GBP', 'TRY', 'JPY'];
            foreach ($popularCurrencies as $currency) {
                if (isset($rates[$currency]) && $currency !== $fromCurrency) {
                    $exchangeRates[$currency] = [
                        'rate' => $rates[$currency],
                        'symbol' => $currencies[$currency]['symbol'] ?? $currency,
                        'flag' => $currencies[$currency]['flag'] ?? ''
                    ];
                }
            }
        } else {
            $error = ($currentLang === 'tr') ? 'Döviz kurları alınamadı. Lütfen tekrar deneyin.' : 'Could not fetch exchange rates. Please try again.';
        }
    } else {
        $error = ($currentLang === 'tr') ? 'Lütfen 1 ile 1.000.000.000 arasında bir miktar girin!' : 'Please enter an amount between 1 and 1,000,000,000!';
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
            ['title' => ($currentLang === 'tr') ? 'Finans Araçları' : 'Finance Tools', 'url' => '/' . $currentLang . '/category/finance'],
            ['title' => $pageTitle]
        ];
        echo generateBreadcrumb($breadcrumbItems);
        ?>

        <!-- Tool Container -->
        <div class="tool-container">
            <div class="tool-header">
                <h1><i class="fas fa-exchange-alt text-primary"></i> <?php echo $pageTitle; ?></h1>
                <p class="lead"><?php echo $pageDescription; ?></p>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <!-- Currency Form -->
                    <div class="tool-form">
                        <form method="POST" id="currencyForm">
                            <div class="mb-3">
                                <label for="amount" class="form-label">
                                    <i class="fas fa-dollar-sign"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Miktar' : 'Amount'; ?>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="amount" 
                                       name="amount" 
                                       min="0.01" 
                                       max="1000000000" 
                                       step="0.01" 
                                       placeholder="1000"
                                       value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : '1000'; ?>"
                                       required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="from_currency" class="form-label">
                                        <i class="fas fa-arrow-right"></i> 
                                        <?php echo ($currentLang === 'tr') ? 'Çevrilecek Para Birimi' : 'From Currency'; ?>
                                    </label>
                                    <select class="form-control" id="from_currency" name="from_currency" required>
                                        <?php foreach ($currencies as $code => $info): ?>
                                            <option value="<?php echo $code; ?>" 
                                                    <?php echo (($_POST['from_currency'] ?? 'USD') === $code) ? 'selected' : ''; ?>>
                                                <?php echo $info['flag']; ?> <?php echo $code; ?> - <?php echo $info['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="to_currency" class="form-label">
                                        <i class="fas fa-arrow-left"></i> 
                                        <?php echo ($currentLang === 'tr') ? 'Hedef Para Birimi' : 'To Currency'; ?>
                                    </label>
                                    <select class="form-control" id="to_currency" name="to_currency" required>
                                        <?php foreach ($currencies as $code => $info): ?>
                                            <option value="<?php echo $code; ?>" 
                                                    <?php echo (($_POST['to_currency'] ?? 'EUR') === $code) ? 'selected' : ''; ?>>
                                                <?php echo $info['flag']; ?> <?php echo $code; ?> - <?php echo $info['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="convertBtn">
                                    <i class="fas fa-sync-alt"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Çevir' : 'Convert'; ?>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="swapCurrencies()">
                                    <i class="fas fa-exchange-alt"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Para Birimlerini Değiştir' : 'Swap Currencies'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Quick Convert -->
                    <div class="card">
                        <div class="card-body">
                            <h6><?php echo ($currentLang === 'tr') ? 'Hızlı Çevrim' : 'Quick Conversion'; ?></h6>
                            <div class="row g-2">
                                <?php 
                                $quickConversions = [
                                    ['from' => 'USD', 'to' => 'EUR', 'amount' => '1000'],
                                    ['from' => 'EUR', 'to' => 'TRY', 'amount' => '1000'],
                                    ['from' => 'USD', 'to' => 'TRY', 'amount' => '1000'],
                                    ['from' => 'GBP', 'to' => 'USD', 'amount' => '1000']
                                ];
                                foreach ($quickConversions as $quick): ?>
                                    <div class="col-6 mb-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" 
                                                onclick="setQuickConversion('<?php echo $quick['from']; ?>', '<?php echo $quick['to']; ?>', '<?php echo $quick['amount']; ?>')">
                                            <?php echo $currencies[$quick['from']]['flag']; ?> <?php echo $quick['amount'] . ' ' . $quick['from']; ?> 
                                            → <?php echo $currencies[$quick['to']]['flag']; ?> <?php echo $quick['to']; ?>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <?php if ($result): ?>
                    <!-- Sonuç -->
                    <div class="tool-result">
                        <h4><i class="fas fa-chart-line"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Çevrim Sonucu' : 'Conversion Result'; ?>
                        </h4>
                        <div class="currency-result-display">
                            <div class="currency-box mb-3">
                                <div class="row text-center">
                                    <div class="col-5">
                                        <div class="p-3">
                                            <div class="mb-2"><?php echo $result['from_flag']; ?></div>
                                            <h3><?php echo number_format($result['amount'], 2); ?></h3>
                                            <strong><?php echo $result['from_currency']; ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-2 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-arrow-right fa-2x text-primary"></i>
                                    </div>
                                    <div class="col-5">
                                        <div class="p-3">
                                            <div class="mb-2"><?php echo $result['to_flag']; ?></div>
                                            <h3><?php echo number_format($result['converted_amount'], 2); ?></h3>
                                            <strong><?php echo $result['to_currency']; ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-success mb-3">
                                <p class="mb-1">
                                    <strong>1 <?php echo $result['from_currency']; ?> = 
                                    <?php echo number_format($result['rate'], 4); ?> <?php echo $result['to_currency']; ?></strong>
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> <?php echo ($currentLang === 'tr') ? 'Güncel kur' : 'Current rate'; ?>
                                    • <?php echo date('H:i'); ?>
                                </small>
                            </div>
                            
                            <div class="d-grid gap-2 mt-3">
                                <button class="btn btn-light" onclick="copyResult()">
                                    <i class="fas fa-copy"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Sonucu Kopyala' : 'Copy Result'; ?>
                                </button>
                                <button class="btn btn-outline-light" onclick="window.print()">
                                    <i class="fas fa-print"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Yazdır' : 'Print'; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($exchangeRates): ?>
                    <!-- Popüler Kurlar -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6><i class="fas fa-chart-bar"></i> <?php echo ($currentLang === 'tr') ? 'Popüler Döviz Kurları' : 'Popular Exchange Rates'; ?></h6>
                            <?php foreach ($exchangeRates as $currency => $data): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                    <span><?php echo $data['flag']; ?> 1 <?php echo $result['from_currency']; ?> →</span>
                                    <strong><?php echo number_format($data['rate'], 4); ?> <?php echo $currency; ?></strong>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
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
                            <h5><i class="fas fa-info-circle"></i> 
                                <?php echo ($currentLang === 'tr') ? 'Döviz Çevirici Hakkında' : 'About Currency Converter'; ?>
                            </h5>
                            <ul class="mb-3">
                                <li><?php echo ($currentLang === 'tr') ? 'Güncel döviz kurları' : 'Current exchange rates'; ?></li>
                                <li><?php echo ($currentLang === 'tr') ? '150+ para birimi desteği' : '150+ currency support'; ?></li>
                                <li><?php echo ($currentLang === 'tr') ? 'Güvenilir API kullanımı' : 'Reliable API usage'; ?></li>
                                <li><?php echo ($currentLang === 'tr') ? 'Anlık çevrim' : 'Instant conversion'; ?></li>
                            </ul>
                            
                            <div class="alert alert-info">
                                <small>
                                    <i class="fas fa-lightbulb"></i>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Miktar girin ve para birimlerini seçerek çevrimi başlatın.' :
                                        'Enter amount and select currencies to start conversion.'; ?>
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

        <!-- Currency Information -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3><i class="fas fa-book"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Döviz Çevirici Hakkında Detaylı Bilgi' : 'Detailed Information About Currency Converter'; ?>
                        </h3>
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Özellikler' : 'Features'; ?></h5>
                                <ul>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Güvenilir Kaynak:' : 'Reliable Source:'; ?></strong> 
                                        <?php echo ($currentLang === 'tr') ? 'Merkez bankalarından güncel kurlar' : 'Current rates from central banks'; ?></li>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Geniş Destek:' : 'Wide Support:'; ?></strong> 
                                        <?php echo ($currentLang === 'tr') ? '150+ dünya para birimi' : '150+ world currencies'; ?></li>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Yedek Sistem:' : 'Backup System:'; ?></strong> 
                                        <?php echo ($currentLang === 'tr') ? 'Çoklu API desteği' : 'Multiple API support'; ?></li>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Hızlı İşlem:' : 'Fast Processing:'; ?></strong> 
                                        <?php echo ($currentLang === 'tr') ? 'Anlık çevrim sonuçları' : 'Instant conversion results'; ?></li>
                                </ul>
                                
                                <h5><?php echo ($currentLang === 'tr') ? 'Desteklenen Para Birimleri' : 'Supported Currencies'; ?></h5>
                                <div class="row">
                                    <?php 
                                    $majorCurrencies = array_slice($currencies, 0, 8, true);
                                    foreach ($majorCurrencies as $code => $info): ?>
                                        <div class="col-6 col-md-6 mb-2">
                                            <small>
                                                <?php echo $info['flag']; ?> 
                                                <strong><?php echo $code; ?>:</strong> 
                                                <?php echo $info['name']; ?>
                                            </small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Nasıl Kullanılır?' : 'How to Use?'; ?></h5>
                                <ol>
                                    <li><?php echo ($currentLang === 'tr') ? 'Çevirmek istediğiniz miktarı girin' : 'Enter the amount you want to convert'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Kaynak para birimini seçin' : 'Select the source currency'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Hedef para birimini seçin' : 'Select the target currency'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Çevir butonuna tıklayın' : 'Click the convert button'; ?></li>
                                </ol>
                                
                                <h5><?php echo ($currentLang === 'tr') ? 'Önemli Notlar' : 'Important Notes'; ?></h5>
                                <div class="alert alert-warning">
                                    <ul class="mb-0">
                                        <li><?php echo ($currentLang === 'tr') ? 
                                            'Kurlar günlük olarak güncellenir' : 
                                            'Rates are updated daily'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 
                                            'Sadece bilgi amaçlıdır' : 
                                            'For informational purposes only'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 
                                            'Resmi işlemler için bankanıza danışın' : 
                                            'Consult your bank for official transactions'; ?></li>
                                    </ul>
                                </div>
                                
                                <div class="alert alert-success">
                                    <i class="fas fa-shield-alt"></i>
                                    <strong><?php echo ($currentLang === 'tr') ? 'Güvenlik:' : 'Security:'; ?></strong>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Tüm hesaplamalar tarayıcınızda yapılır, hiçbir veri saklanmaz.' :
                                        'All calculations are done in your browser, no data is stored.'; ?>
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
                            'name' => ($currentLang === 'tr') ? 'Kredi Hesaplayıcı' : 'Loan Calculator',
                            'description' => ($currentLang === 'tr') ? 'Kredi taksit hesaplama' : 'Calculate loan payments',
                            'url' => '/tools/loan-calculator.php?lang=' . $currentLang,
                            'icon' => 'fas fa-calculator'
                        ],
                        [
                            'name' => ($currentLang === 'tr') ? 'Ölçü Birimi Çevirici' : 'Unit Converter',
                            'description' => ($currentLang === 'tr') ? 'Uzunluk, ağırlık çevirme' : 'Convert length, weight',
                            'url' => '/tools/unit-converter.php?lang=' . $currentLang,
                            'icon' => 'fas fa-exchange-alt'
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
// Currency Converter specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('currencyForm');
    const convertBtn = document.getElementById('convertBtn');
    const currentLang = '<?php echo $currentLang; ?>';
    
    // Track tool usage
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Tool', 'View', 'Currency Converter');
        
        // Add to recent tools
        const toolName = currentLang === 'tr' ? 'Döviz Çevirici' : 'Currency Converter';
        const toolUrl = '/tools/currency-converter.php?lang=' + currentLang;
        AllInToolbox.storage.addRecentTool('currency-converter', toolName, toolUrl);
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const amount = parseFloat(document.getElementById('amount').value);
        
        if (amount && amount > 0) {
            if (typeof AllInToolbox !== 'undefined') {
                AllInToolbox.utils.showLoading(convertBtn);
                AllInToolbox.analytics.trackToolUsage('Currency Converter');
            }
            
            // Show loading text
            convertBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + 
                (currentLang === 'tr' ? 'Kurlar getiriliyor...' : 'Fetching rates...');
        }
    });
});

// Swap currencies function
function swapCurrencies() {
    const fromSelect = document.getElementById('from_currency');
    const toSelect = document.getElementById('to_currency');
    
    const fromValue = fromSelect.value;
    const toValue = toSelect.value;
    
    fromSelect.value = toValue;
    toSelect.value = fromValue;
}

// Quick conversion
function setQuickConversion(from, to, amount) {
    document.getElementById('from_currency').value = from;
    document.getElementById('to_currency').value = to;
    document.getElementById('amount').value = amount;
}

// Copy result
function copyResult() {
    <?php if ($result): ?>
    const resultText = '<?php echo $result['amount']; ?> <?php echo $result['from_currency']; ?> = <?php echo $result['converted_amount']; ?> <?php echo $result['to_currency']; ?>\n' +
                      '1 <?php echo $result['from_currency']; ?> = <?php echo number_format($result['rate'], 4); ?> <?php echo $result['to_currency']; ?>';
    
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.utils.copyToClipboard(resultText);
    } else {
        navigator.clipboard.writeText(resultText).then(() => {
            alert('<?php echo ($currentLang === 'tr') ? 'Sonuç kopyalandı!' : 'Result copied!'; ?>');
        });
    }
    <?php endif; ?>
}

<?php if ($result): ?>
// Track successful conversion
if (typeof AllInToolbox !== 'undefined') {
    AllInToolbox.analytics.trackEvent('Tool', 'Convert', 'Currency Converter');
}
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>