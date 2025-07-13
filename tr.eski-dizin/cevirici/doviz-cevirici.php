<?php
// tr/cevirici/doviz-cevirici.php
session_start();

// Konfigürasyonu yükle
require_once '../../config/config.php';
require_once '../../config/functions.php';

// Dil ayarla
setLanguage('tr');

// Sayfa bilgileri
$pageTitle = 'Döviz Çevirici - Güncel Döviz Kurları | AllInToolbox';
$pageDescription = 'Güncel döviz kurları ile para birimi çevirici. TL, Dolar, Euro, Sterlin ve diğer dövizler arası çevrim yapın.';
$pageKeywords = 'döviz çevirici, döviz kurları, TL dolar, euro çevirici, para birimi çevirici';

// Döviz kurları (gerçek uygulamada API'den gelecek)
$currencies = [
    'TRY' => ['name' => 'Türk Lirası', 'symbol' => '₺', 'rate' => 1.0],
    'USD' => ['name' => 'Amerikan Doları', 'symbol' => '$', 'rate' => 0.037],
    'EUR' => ['name' => 'Euro', 'symbol' => '€', 'rate' => 0.034],
    'GBP' => ['name' => 'İngiliz Sterlini', 'symbol' => '£', 'rate' => 0.029],
    'JPY' => ['name' => 'Japon Yeni', 'symbol' => '¥', 'rate' => 5.45],
    'CHF' => ['name' => 'İsviçre Frangı', 'symbol' => 'CHF', 'rate' => 0.033],
    'CAD' => ['name' => 'Kanada Doları', 'symbol' => 'C$', 'rate' => 0.051],
    'AUD' => ['name' => 'Avustralya Doları', 'symbol' => 'A$', 'rate' => 0.057],
    'CNY' => ['name' => 'Çin Yuanı', 'symbol' => '¥', 'rate' => 0.27],
    'RUB' => ['name' => 'Rus Rublesi', 'symbol' => '₽', 'rate' => 3.42]
];

// Döviz çevirme fonksiyonu
function convertCurrency($amount, $fromCurrency, $toCurrency, $rates) {
    if (!isset($rates[$fromCurrency]) || !isset($rates[$toCurrency])) {
        return false;
    }
    
    // TRY bazında hesaplama
    $tryAmount = $amount / $rates[$fromCurrency]['rate'];
    $convertedAmount = $tryAmount * $rates[$toCurrency]['rate'];
    
    return $convertedAmount;
}

// Form işleme
$result = null;
$error = null;

if ($_POST) {
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $fromCurrency = $_POST['from_currency'] ?? 'TRY';
    $toCurrency = $_POST['to_currency'] ?? 'USD';
    
    if ($amount > 0 && $amount <= 1000000000) {
        if ($fromCurrency === $toCurrency) {
            $convertedAmount = $amount;
        } else {
            $convertedAmount = convertCurrency($amount, $fromCurrency, $toCurrency, $currencies);
        }
        
        if ($convertedAmount !== false) {
            $result = [
                'amount' => $amount,
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
                'converted_amount' => $convertedAmount,
                'rate' => $convertedAmount / $amount,
                'from_name' => $currencies[$fromCurrency]['name'],
                'to_name' => $currencies[$toCurrency]['name'],
                'from_symbol' => $currencies[$fromCurrency]['symbol'],
                'to_symbol' => $currencies[$toCurrency]['symbol']
            ];
        } else {
            $error = 'Döviz çevirimi yapılamadı. Lütfen tekrar deneyin.';
        }
    } else {
        $error = 'Lütfen geçerli bir miktar girin (1-1.000.000.000).';
    }
}

// Header'ı dahil et
include '../../includes/header.php';
?>

<!-- Breadcrumb -->
<?php
echo generateBreadcrumb([
    ['title' => translate('home'), 'url' => '/tr/'],
    ['title' => 'Çeviriciler', 'url' => '/tr/category/converter.php'],
    ['title' => 'Döviz Çevirici']
]);
?>

<!-- Tool Container -->
<div class="tool-container">
    <div class="tool-header">
        <h1><i class="fas fa-exchange-alt text-primary"></i> Döviz Çevirici</h1>
        <p class="lead">Güncel kurlarla para birimlerini çevirin</p>
        <small class="text-muted">
            <i class="fas fa-clock"></i> Son güncelleme: <?php echo date('d.m.Y H:i'); ?>
        </small>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Döviz Form -->
            <div class="tool-form">
                <form method="POST" id="currencyForm">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="amount" class="form-label">
                                <i class="fas fa-coins"></i> Miktar
                            </label>
                            <input type="number" 
                                   class="form-control form-control-lg" 
                                   id="amount" 
                                   name="amount" 
                                   min="0.01" 
                                   max="1000000000" 
                                   step="0.01" 
                                   placeholder="Çevrilecek miktarı girin"
                                   value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : '1'; ?>"
                                   required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-5">
                            <label for="from_currency" class="form-label">Çevrilecek Para Birimi</label>
                            <select class="form-control" id="from_currency" name="from_currency" required>
                                <?php foreach ($currencies as $code => $currency): ?>
                                    <option value="<?php echo $code; ?>" 
                                            <?php echo (isset($_POST['from_currency']) && $_POST['from_currency'] === $code) ? 'selected' : ($code === 'TRY' ? 'selected' : ''); ?>>
                                        <?php echo $currency['symbol'] . ' ' . $currency['name'] . ' (' . $code . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2 text-center">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex align-items-center justify-content-center" style="height: 38px;">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="swapCurrencies()" title="Para birimlerini değiştir">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-5">
                            <label for="to_currency" class="form-label">Hedef Para Birimi</label>
                            <select class="form-control" id="to_currency" name="to_currency" required>
                                <?php foreach ($currencies as $code => $currency): ?>
                                    <option value="<?php echo $code; ?>" 
                                            <?php echo (isset($_POST['to_currency']) && $_POST['to_currency'] === $code) ? 'selected' : ($code === 'USD' ? 'selected' : ''); ?>>
                                        <?php echo $currency['symbol'] . ' ' . $currency['name'] . ' (' . $code . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg" id="convertBtn">
                            <i class="fas fa-calculator"></i> Çevir
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                            <i class="fas fa-eraser"></i> Temizle
                        </button>
                    </div>
                </form>
            </div>
            
            <?php if ($result): ?>
            <!-- Sonuç -->
            <div class="tool-result mt-4">
                <h4><i class="fas fa-chart-line"></i> Çevrim Sonucu</h4>
                <div class="currency-result">
                    <div class="row text-center">
                        <div class="col-5">
                            <div class="currency-box">
                                <div class="currency-amount">
                                    <?php echo number_format($result['amount'], 2, ',', '.'); ?>
                                </div>
                                <div class="currency-info">
                                    <span class="currency-symbol"><?php echo $result['from_symbol']; ?></span>
                                    <span class="currency-name"><?php echo $result['from_name']; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-right fa-2x text-warning"></i>
                        </div>
                        
                        <div class="col-5">
                            <div class="currency-box">
                                <div class="currency-amount text-success">
                                    <?php echo number_format($result['converted_amount'], 2, ',', '.'); ?>
                                </div>
                                <div class="currency-info">
                                    <span class="currency-symbol"><?php echo $result['to_symbol']; ?></span>
                                    <span class="currency-name"><?php echo $result['to_name']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="exchange-rate mt-3 text-center">
                        <small class="text-muted">
                            1 <?php echo $result['from_currency']; ?> = <?php echo number_format($result['rate'], 4, ',', '.'); ?> <?php echo $result['to_currency']; ?>
                        </small>
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-light" onclick="copyResult()">
                            <i class="fas fa-copy"></i> Sonucu Kopyala
                        </button>
                        <button class="btn btn-outline-light" onclick="window.print()">
                            <i class="fas fa-print"></i> Yazdır
                        </button>
                    </div>
                </div>
            </div>
            <?php elseif ($error): ?>
            <!-- Hata -->
            <div class="tool-result error mt-4">
                <h4><i class="fas fa-exclamation-triangle"></i> Hata</h4>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-lg-4">
            <!-- Popüler Dövizler -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-chart-bar"></i> Popüler Döviz Kurları</h5>
                    <small class="text-muted">1 TRY = </small>
                </div>
                <div class="card-body">
                    <?php 
                    $popularCurrencies = ['USD', 'EUR', 'GBP', 'JPY'];
                    foreach ($popularCurrencies as $currency): 
                        $rate = $currencies[$currency]['rate'];
                    ?>
                        <div class="d-flex justify-content-between align-items-center mb-2 popular-currency" 
                             onclick="setQuickConversion('TRY', '<?php echo $currency; ?>')">
                            <div>
                                <span class="fw-bold"><?php echo $currencies[$currency]['symbol']; ?> <?php echo $currency; ?></span>
                                <br><small class="text-muted"><?php echo $currencies[$currency]['name']; ?></small>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold"><?php echo number_format($rate, 4, ',', '.'); ?></span>
                            </div>
                        </div>
                        <?php if ($currency !== end($popularCurrencies)): ?>
                            <hr class="my-2">
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Hesap Makinesi -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-calculator"></i> Hızlı Hesap</h5>
                </div>
                <div class="card-body">
                    <div class="calculator-display" id="calcDisplay">0</div>
                    <div class="calculator-buttons">
                        <button class="calculator-button clear" onclick="clearCalc()">C</button>
                        <button class="calculator-button operator" onclick="calcOperation('/')">÷</button>
                        <button class="calculator-button operator" onclick="calcOperation('*')">×</button>
                        <button class="calculator-button operator" onclick="calcOperation('-')">-</button>
                        
                        <button class="calculator-button number" onclick="calcNumber('7')">7</button>
                        <button class="calculator-button number" onclick="calcNumber('8')">8</button>
                        <button class="calculator-button number" onclick="calcNumber('9')">9</button>
                        <button class="calculator-button operator" onclick="calcOperation('+')">+</button>
                        
                        <button class="calculator-button number" onclick="calcNumber('4')">4</button>
                        <button class="calculator-button number" onclick="calcNumber('5')">5</button>
                        <button class="calculator-button number" onclick="calcNumber('6')">6</button>
                        <button class="calculator-button equals" onclick="calcEquals()" rowspan="2">=</button>
                        
                        <button class="calculator-button number" onclick="calcNumber('1')">1</button>
                        <button class="calculator-button number" onclick="calcNumber('2')">2</button>
                        <button class="calculator-button number" onclick="calcNumber('3')">3</button>
                        
                        <button class="calculator-button number" onclick="calcNumber('0')" style="grid-column: span 2;">0</button>
                        <button class="calculator-button number" onclick="calcNumber('.')">.</button>
                    </div>
                    <button class="btn btn-primary btn-sm mt-2 w-100" onclick="useCalcResult()">
                        <i class="fas fa-arrow-up"></i> Sonucu Kullan
                    </button>
                </div>
            </div>
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
                <h3><i class="fas fa-info-circle"></i> Döviz Çevirici Hakkında</h3>
                
                <div class="row">
                    <div class="col-lg-6">
                        <h5>Özellikler</h5>
                        <ul>
                            <li>10+ popüler para birimi desteği</li>
                            <li>Gerçek zamanlı kur bilgileri</li>
                            <li>Entegre hesap makinesi</li>
                            <li>Hızlı para birimi değişimi</li>
                            <li>Sonuç kopyalama ve yazdırma</li>
                        </ul>
                        
                        <h5>Desteklenen Para Birimleri</h5>
                        <div class="row">
                            <?php foreach ($currencies as $code => $currency): ?>
                                <div class="col-6 mb-1">
                                    <small><?php echo $currency['symbol']; ?> <?php echo $code; ?> - <?php echo $currency['name']; ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <h5>Kullanım İpuçları</h5>
                        <ul>
                            <li>Miktarı girin ve para birimlerini seçin</li>
                            <li>Popüler kurlar bölümünden hızlı seçim yapabilirsiniz</li>
                            <li>Değiştir butonu ile para birimlerini hızlıca değiştirebilirsiniz</li>
                            <li>Hesap makinesini karmaşık hesaplamalar için kullanın</li>
                        </ul>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Uyarı:</strong> Kurlar yaklaşık değerlerdir. Gerçek işlemler için bankanızı kontrol edin.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Currency Converter specific JavaScript
let calcValue = '0';
let calcOperator = '';
let calcPreviousValue = '';
let calcWaitingForOperand = false;

function swapCurrencies() {
    const fromSelect = document.getElementById('from_currency');
    const toSelect = document.getElementById('to_currency');
    
    const tempValue = fromSelect.value;
    fromSelect.value = toSelect.value;
    toSelect.value = tempValue;
}

function resetForm() {
    document.getElementById('currencyForm').reset();
    document.getElementById('amount').value = '1';
    document.getElementById('from_currency').value = 'TRY';
    document.getElementById('to_currency').value = 'USD';
}

function setQuickConversion(from, to) {
    document.getElementById('from_currency').value = from;
    document.getElementById('to_currency').value = to;
    
    // Auto submit if amount is already filled
    const amount = document.getElementById('amount').value;
    if (amount && parseFloat(amount) > 0) {
        document.getElementById('currencyForm').submit();
    }
}

function copyResult() {
    <?php if ($result): ?>
    const resultText = `<?php echo number_format($result['amount'], 2, ',', '.'); ?> <?php echo $result['from_currency']; ?> = <?php echo number_format($result['converted_amount'], 2, ',', '.'); ?> <?php echo $result['to_currency']; ?>`;
    AllInToolbox.utils.copyToClipboard(resultText);
    <?php endif; ?>
}

// Calculator functions
function updateCalcDisplay() {
    document.getElementById('calcDisplay').textContent = calcValue;
}

function calcNumber(num) {
    if (calcWaitingForOperand) {
        calcValue = num;
        calcWaitingForOperand = false;
    } else {
        calcValue = calcValue === '0' ? num : calcValue + num;
    }
    updateCalcDisplay();
}

function calcOperation(nextOperator) {
    const inputValue = parseFloat(calcValue);
    
    if (calcPreviousValue === '') {
        calcPreviousValue = inputValue;
    } else if (calcOperator) {
        const currentValue = calcPreviousValue || 0;
        const newValue = calculate(currentValue, inputValue, calcOperator);
        
        calcValue = String(newValue);
        calcPreviousValue = newValue;
        updateCalcDisplay();
    }
    
    calcWaitingForOperand = true;
    calcOperator = nextOperator;
}

function calculate(firstValue, secondValue, operator) {
    switch (operator) {
        case '+':
            return firstValue + secondValue;
        case '-':
            return firstValue - secondValue;
        case '*':
            return firstValue * secondValue;
        case '/':
            return firstValue / secondValue;
        default:
            return secondValue;
    }
}

function calcEquals() {
    const inputValue = parseFloat(calcValue);
    
    if (calcPreviousValue !== '' && calcOperator) {
        const newValue = calculate(calcPreviousValue, inputValue, calcOperator);
        calcValue = String(newValue);
        calcPreviousValue = '';
        calcOperator = '';
        calcWaitingForOperand = true;
        updateCalcDisplay();
    }
}

function clearCalc() {
    calcValue = '0';
    calcPreviousValue = '';
    calcOperator = '';
    calcWaitingForOperand = false;
    updateCalcDisplay();
}

function useCalcResult() {
    const amount = parseFloat(calcValue);
    if (!isNaN(amount) && amount > 0) {
        document.getElementById('amount').value = amount;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Track tool usage
    AllInToolbox.analytics.trackEvent('Tool', 'View', 'Currency Converter');
    AllInToolbox.storage.addRecentTool('currency-converter', 'Döviz Çevirici', '/tr/cevirici/doviz-cevirici.php');
    
    const form = document.getElementById('currencyForm');
    const convertBtn = document.getElementById('convertBtn');
    
    // Form submission
    form.addEventListener('submit', function(e) {
        AllInToolbox.utils.showLoading(convertBtn);
        AllInToolbox.analytics.trackToolUsage('Currency Converter');
        
        // Simulate processing time
        setTimeout(() => {
            AllInToolbox.utils.showLoading(convertBtn, false);
        }, 1000);
    });
    
    // Add currency change listeners for quick conversion
    document.getElementById('from_currency').addEventListener('change', function() {
        AllInToolbox.analytics.trackEvent('Currency', 'Select From', this.value);
    });
    
    document.getElementById('to_currency').addEventListener('change', function() {
        AllInToolbox.analytics.trackEvent('Currency', 'Select To', this.value);
    });
    
    // Popular currency click handlers
    document.querySelectorAll('.popular-currency').forEach(item => {
        item.style.cursor = 'pointer';
        item.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        item.addEventListener('mouseleave', function() {
            this.style.backgroundColor = 'transparent';
        });
    });
});

<?php if ($result): ?>
// Track successful conversion
AllInToolbox.analytics.trackEvent('Tool', 'Convert', 'Currency - <?php echo $result['from_currency']; ?> to <?php echo $result['to_currency']; ?>');
<?php endif; ?>
</script>

<style>
.currency-result {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 2rem;
    border-radius: 10px;
}

.currency-box {
    background: rgba(255,255,255,0.1);
    padding: 1rem;
    border-radius: 8px;
    backdrop-filter: blur(10px);
}

.currency-amount {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.currency-info {
    display: flex;
    flex-direction: column;
}

.currency-symbol {
    font-size: 1.2rem;
    font-weight: bold;
}

.currency-name {
    font-size: 0.8rem;
    opacity: 0.8;
}

.popular-currency {
    transition: background-color 0.2s ease;
    padding: 0.5rem;
    border-radius: 5px;
}

.calculator-buttons {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
    margin-top: 1rem;
}

.calculator-button {
    aspect-ratio: 1;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.2s ease;
    cursor: pointer;
}

.calculator-button:hover {
    transform: scale(0.95);
}

.calculator-button.operator {
    background: #ffc107;
    color: #000;
}

.calculator-button.number {
    background: #f8f9fa;
    color: #000;
}

.calculator-button.equals {
    background: #28a745;
    color: white;
    grid-row: span 2;
    aspect-ratio: auto;
}

.calculator-button.clear {
    background: #dc3545;
    color: white;
}
</style>

<?php include '../../includes/footer.php'; ?>