<?php
// tools/currency-converter.php - DÜZENLI VERSİYON
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
    'USD' => ['name' => 'US Dollar', 'symbol' => '$'],
    'EUR' => ['name' => 'Euro', 'symbol' => '€'],
    'TRY' => ['name' => 'Turkish Lira', 'symbol' => '₺'],
    'GBP' => ['name' => 'British Pound', 'symbol' => '£'],
    'JPY' => ['name' => 'Japanese Yen', 'symbol' => '¥'],
    'CAD' => ['name' => 'Canadian Dollar', 'symbol' => 'C$'],
    'AUD' => ['name' => 'Australian Dollar', 'symbol' => 'A$'],
    'CHF' => ['name' => 'Swiss Franc', 'symbol' => 'CHF'],
    'CNY' => ['name' => 'Chinese Yuan', 'symbol' => '¥'],
    'SEK' => ['name' => 'Swedish Krona', 'symbol' => 'kr'],
    'NOK' => ['name' => 'Norwegian Krone', 'symbol' => 'kr'],
    'DKK' => ['name' => 'Danish Krone', 'symbol' => 'kr']
];

// Form işleme
$result = null;
$error = null;
$exchangeRates = null;

if ($_POST) {
    $amount = floatval($_POST['amount'] ?? 0);
    $fromCurrency = $_POST['from_currency'] ?? 'USD';
    $toCurrency = $_POST['to_currency'] ?? 'EUR';
    
    if ($amount > 0 && $amount <= 1000000000) {
        // Ücretsiz API kullanarak döviz kurları al
        $apiUrl = "https://latest.currency-api.pages.dev/v1/currencies/{$fromCurrency}.json";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'AllInToolbox Currency Converter'
            ]
        ]);
        
        $response = @file_get_contents($apiUrl, false, $context);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            
            if ($data && isset($data[$fromCurrency])) {
                $rates = $data[$fromCurrency];
                
                if (isset($rates[$toCurrency])) {
                    $rate = $rates[$toCurrency];
                    $convertedAmount = $amount * $rate;
                    
                    $result = [
                        'amount' => $amount,
                        'from_currency' => $fromCurrency,
                        'to_currency' => $toCurrency,
                        'rate' => $rate,
                        'converted_amount' => round($convertedAmount, 2),
                        'from_symbol' => $currencies[$fromCurrency]['symbol'] ?? $fromCurrency,
                        'to_symbol' => $currencies[$toCurrency]['symbol'] ?? $toCurrency
                    ];
                    
                    // Popüler kurları da al
                    $exchangeRates = [];
                    $popularPairs = ['EUR', 'USD', 'GBP', 'JPY'];
                    foreach ($popularPairs as $currency) {
                        if (isset($rates[$currency]) && $currency !== $fromCurrency) {
                            $exchangeRates[$currency] = [
                                'rate' => $rates[$currency],
                                'symbol' => $currencies[$currency]['symbol'] ?? $currency
                            ];
                        }
                    }
                } else {
                    $error = ($currentLang === 'tr') ? 'Döviz kuru bulunamadı!' : 'Exchange rate not found!';
                }
            } else {
                $error = ($currentLang === 'tr') ? 'Geçersiz para birimi!' : 'Invalid currency!';
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
                                       placeholder="<?php echo ($currentLang === 'tr') ? 'Çevrilecek miktarı girin' : 'Enter amount to convert'; ?>"
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
                                                <?php echo $code; ?> - <?php echo $info['name']; ?>
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
                                                <?php echo $code; ?> - <?php echo $info['name']; ?>
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
                    
                    <!-- Currency Info -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Döviz Çevirici Hakkında' : 'About Currency Converter'; ?>
                        </h6>
                        <p class="mb-0">
                            <?php echo ($currentLang === 'tr') ? 
                                'Güncel döviz kurları ile 150+ para birimini çevirin. Kurlar günlük olarak güncellenir.' :
                                'Convert 150+ currencies with current exchange rates. Rates are updated daily.'; ?>
                        </p>
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
                                            <h3><?php echo number_format($result['amount'], 2); ?></h3>
                                            <strong><?php echo $result['from_currency']; ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-2 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-arrow-right fa-2x"></i>
                                    </div>
                                    <div class="col-5">
                                        <div class="p-3">
                                            <h3><?php echo number_format($result['converted_amount'], 2); ?></h3>
                                            <strong><?php echo $result['to_currency']; ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-success mb-3">
                                <p class="mb-0">
                                    <strong>1 <?php echo $result['from_currency']; ?> = 
                                    <?php echo number_format($result['rate'], 4); ?> <?php echo $result['to_currency']; ?></strong>
                                </p>
                                <small class="text-muted">
                                    <?php echo ($currentLang === 'tr') ? 'Güncel kur' : 'Current rate'; ?>
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
                            <h6><?php echo ($currentLang === 'tr') ? 'Popüler Döviz Kurları' : 'Popular Exchange Rates'; ?></h6>
                            <?php foreach ($exchangeRates as $currency => $data): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>1 <?php echo $result['from_currency']; ?> →</span>
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
                            <h5><i class="fas fa-chart-bar"></i> 
                                <?php echo ($currentLang === 'tr') ? 'Hızlı Çevrim' : 'Quick Conversion'; ?>
                            </h5>
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
                                            <?php echo $quick['amount'] . ' ' . $quick['from'] . ' → ' . $quick['to']; ?>
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
                                <h5><?php echo ($currentLang === 'tr') ? 'Nasıl Kullanılır?' : 'How to Use?'; ?></h5>
                                <ul>
                                    <li><?php echo ($currentLang === 'tr') ? 'Çevirmek istediğiniz miktarı girin' : 'Enter the amount you want to convert'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Kaynak para birimini seçin' : 'Select the source currency'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Hedef para birimini seçin' : 'Select the target currency'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Çevir butonuna tıklayın' : 'Click the convert button'; ?></li>
                                </ul>
                                
                                <h5><?php echo ($currentLang === 'tr') ? 'Desteklenen Para Birimleri' : 'Supported Currencies'; ?></h5>
                                <p><?php echo ($currentLang === 'tr') ? 
                                    '150+ dünya para birimi desteklenir. En popüler olanları:' :
                                    '150+ world currencies are supported. Most popular ones:'; ?></p>
                                <div class="row">
                                    <?php 
                                    $popularCurrencies = array_slice($currencies, 0, 6, true);
                                    foreach ($popularCurrencies as $code => $info): ?>
                                        <div class="col-6 col-md-4 mb-1">
                                            <small><strong><?php echo $code; ?>:</strong> <?php echo $info['name']; ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Önemli Notlar' : 'Important Notes'; ?></h5>
                                <ul>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Kurlar günlük olarak güncellenir' : 
                                        'Rates are updated daily'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Sadece bilgi amaçlıdır, resmi işlemler için bankanıza danışın' : 
                                        'For informational purposes only, consult your bank for official transactions'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Gerçek zamanlı kurlar için ekstra ücret alınmaz' : 
                                        'No extra fees for real-time rates'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Tüm hesaplamalar tarayıcınızda yapılır' : 
                                        'All calculations are done in your browser'; ?></li>
                                </ul>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong><?php echo ($currentLang === 'tr') ? 'Uyarı:' : 'Warning:'; ?></strong>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Bu hesaplayıcı sadece bilgi amaçlıdır. Resmi işlemler için bankanıza danışın.' :
                                        'This calculator is for informational purposes only. Consult your bank for official transactions.'; ?>
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
                            'name' => ($currentLang === 'tr') ? 'Yüzde Hesaplayıcı' : 'Percentage Calculator',
                            'description' => ($currentLang === 'tr') ? 'Yüzde hesaplamaları' : 'Percentage calculations',
                            'url' => '/tools/percentage-calculator.php?lang=' . $currentLang,
                            'icon' => 'fas fa-percent'
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
            
            // Simulate processing time
            setTimeout(() => {
                if (typeof AllInToolbox !== 'undefined') {
                    AllInToolbox.utils.showLoading(convertBtn, false);
                }
            }, 1000);
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
    const resultText = '<?php echo $result['amount']; ?> <?php echo $result['from_currency']; ?> = <?php echo $result['converted_amount']; ?> <?php echo $result['to_currency']; ?>';
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.utils.copyToClipboard(resultText);
    } else {
        // Fallback copy
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