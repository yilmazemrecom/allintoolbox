<?php
// tools/loan-calculator.php - TAM VERSİYON - FIXED
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Sayfa meta bilgileri
$pageTitle = ($currentLang === 'tr') ? 'Kredi Hesaplayıcı' : 'Loan Calculator';
$pageDescription = ($currentLang === 'tr') ? 
    'Kredi taksit hesaplama, faiz oranı ve ödeme planı analizi. Konut, taşıt ve ihtiyaç kredisi hesaplaması.' :
    'Loan payment calculation, interest rate and payment plan analysis. Mortgage, auto and personal loan calculations.';

// Kredi hesaplama fonksiyonları
function calculateLoan($principal, $rate, $months) {
    $monthlyRate = ($rate / 100) / 12;
    if ($monthlyRate > 0) {
        $monthlyPayment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $months)) / (pow(1 + $monthlyRate, $months) - 1);
    } else {
        $monthlyPayment = $principal / $months; // 0% faiz durumu
    }
    
    $totalPayment = $monthlyPayment * $months;
    $totalInterest = $totalPayment - $principal;
    
    return [
        'monthly_payment' => round($monthlyPayment, 2),
        'total_payment' => round($totalPayment, 2),
        'total_interest' => round($totalInterest, 2),
        'interest_percentage' => round(($totalInterest / $principal) * 100, 2)
    ];
}

function generatePaymentSchedule($principal, $rate, $months, $maxRows = 12) {
    $monthlyRate = ($rate / 100) / 12;
    $monthlyPayment = calculateLoan($principal, $rate, $months)['monthly_payment'];
    $balance = $principal;
    $schedule = [];
    
    $showRows = min($months, $maxRows);
    
    for ($i = 1; $i <= $showRows; $i++) {
        $interestPayment = $balance * $monthlyRate;
        $principalPayment = $monthlyPayment - $interestPayment;
        $balance -= $principalPayment;
        
        $schedule[] = [
            'month' => $i,
            'payment' => round($monthlyPayment, 2),
            'principal' => round($principalPayment, 2),
            'interest' => round($interestPayment, 2),
            'balance' => round(max(0, $balance), 2)
        ];
    }
    
    return $schedule;
}

// Kredi türleri
$loanTypes = [
    'personal' => [
        'tr' => 'İhtiyaç Kredisi',
        'en' => 'Personal Loan',
        'typical_rate' => 2.5,
        'max_term' => 60
    ],
    'mortgage' => [
        'tr' => 'Konut Kredisi',
        'en' => 'Mortgage',
        'typical_rate' => 1.8,
        'max_term' => 360
    ],
    'auto' => [
        'tr' => 'Taşıt Kredisi',
        'en' => 'Auto Loan',
        'typical_rate' => 2.2,
        'max_term' => 60
    ],
    'business' => [
        'tr' => 'Ticari Kredi',
        'en' => 'Business Loan',
        'typical_rate' => 3.0,
        'max_term' => 120
    ]
];

// Form işleme
$result = null;
$paymentSchedule = null;
$error = null;

if ($_POST) {
    $principal = floatval($_POST['principal'] ?? 0);
    $rate = floatval($_POST['rate'] ?? 0);
    $months = intval($_POST['months'] ?? 0);
    $loanType = $_POST['loan_type'] ?? 'personal';
    
    if ($principal > 0 && $rate >= 0 && $months > 0 && 
        $principal <= 10000000 && $rate <= 100 && $months <= 600) {
        
        $calculation = calculateLoan($principal, $rate, $months);
        $paymentSchedule = generatePaymentSchedule($principal, $rate, $months);
        
        $result = [
            'principal' => $principal,
            'rate' => $rate,
            'months' => $months,
            'years' => round($months / 12, 1),
            'loan_type' => $loanType,
            'loan_type_name' => $loanTypes[$loanType][$currentLang],
            'monthly_payment' => $calculation['monthly_payment'],
            'total_payment' => $calculation['total_payment'],
            'total_interest' => $calculation['total_interest'],
            'interest_percentage' => $calculation['interest_percentage']
        ];
    } else {
        $error = ($currentLang === 'tr') ? 
            'Lütfen geçerli değerler girin (Tutar: 1-10M, Faiz: 0-100%, Vade: 1-600 ay)!' :
            'Please enter valid values (Amount: 1-10M, Rate: 0-100%, Term: 1-600 months)!';
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
            ['title' => ($currentLang === 'tr') ? 'Finans Araçları' : 'Finance Tools', 'url' => '/' . 'pages/category.php?category=finance'],
            ['title' => $pageTitle]
        ];
        echo generateBreadcrumb($breadcrumbItems);
        ?>

        <!-- Tool Container -->
        <div class="tool-container">
            <div class="tool-header">
                <h1><i class="fas fa-calculator text-primary"></i> <?php echo $pageTitle; ?></h1>
                <p class="lead"><?php echo $pageDescription; ?></p>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <!-- Loan Form -->
                    <div class="tool-form">
                        <form method="POST" id="loanForm">
                            <!-- Loan Type -->
                            <div class="mb-3">
                                <label for="loan_type" class="form-label">
                                    <i class="fas fa-tags"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Kredi Türü' : 'Loan Type'; ?>
                                </label>
                                <select class="form-control" id="loan_type" name="loan_type" onchange="updateLoanDefaults()" required>
                                    <?php foreach ($loanTypes as $type => $info): ?>
                                        <option value="<?php echo $type; ?>" 
                                                data-rate="<?php echo $info['typical_rate']; ?>"
                                                data-max-term="<?php echo $info['max_term']; ?>"
                                                <?php echo (($_POST['loan_type'] ?? 'personal') === $type) ? 'selected' : ''; ?>>
                                            <?php echo $info[$currentLang]; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="principal" class="form-label">
                                    <i class="fas fa-money-bill"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Kredi Tutarı (₺)' : 'Loan Amount ($)'; ?>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="principal" 
                                       name="principal" 
                                       min="1000" 
                                       max="10000000" 
                                       step="1000" 
                                       placeholder="100000"
                                       value="<?php echo isset($_POST['principal']) ? htmlspecialchars($_POST['principal']) : ''; ?>"
                                       required>
                                <small class="text-muted">
                                    <?php echo ($currentLang === 'tr') ? 'Minimum: 1.000 ₺ - Maksimum: 10.000.000 ₺' : 'Minimum: $1,000 - Maximum: $10,000,000'; ?>
                                </small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="rate" class="form-label">
                                        <i class="fas fa-percentage"></i> 
                                        <?php echo ($currentLang === 'tr') ? 'Yıllık Faiz Oranı (%)' : 'Annual Interest Rate (%)'; ?>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="rate" 
                                           name="rate" 
                                           min="0" 
                                           max="100" 
                                           step="0.1" 
                                           placeholder="2.5"
                                           value="<?php echo isset($_POST['rate']) ? htmlspecialchars($_POST['rate']) : ''; ?>"
                                           required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="months" class="form-label">
                                        <i class="fas fa-calendar-alt"></i> 
                                        <?php echo ($currentLang === 'tr') ? 'Vade (Ay)' : 'Term (Months)'; ?>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="months" 
                                           name="months" 
                                           min="1" 
                                           max="600" 
                                           placeholder="36"
                                           value="<?php echo isset($_POST['months']) ? htmlspecialchars($_POST['months']) : ''; ?>"
                                           required>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="calculateBtn">
                                    <i class="fas fa-calculator"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Hesapla' : 'Calculate'; ?>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('loanForm').reset();">
                                    <i class="fas fa-eraser"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Temizle' : 'Clear'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Quick Examples -->
                    <div class="card">
                        <div class="card-body">
                            <h6><?php echo ($currentLang === 'tr') ? 'Hızlı Örnekler' : 'Quick Examples'; ?></h6>
                            <div class="row g-2">
                                <?php 
                                $examples = [
                                    ['amount' => '100000', 'rate' => '2.5', 'months' => '36', 'type' => 'personal', 'name' => ($currentLang === 'tr') ? 'İhtiyaç Kredisi' : 'Personal Loan'],
                                    ['amount' => '500000', 'rate' => '1.8', 'months' => '240', 'type' => 'mortgage', 'name' => ($currentLang === 'tr') ? 'Konut Kredisi' : 'Mortgage'],
                                    ['amount' => '200000', 'rate' => '2.2', 'months' => '48', 'type' => 'auto', 'name' => ($currentLang === 'tr') ? 'Taşıt Kredisi' : 'Auto Loan']
                                ];
                                foreach ($examples as $example): ?>
                                    <div class="col-12 mb-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" 
                                                onclick="setExample('<?php echo $example['amount']; ?>', '<?php echo $example['rate']; ?>', '<?php echo $example['months']; ?>', '<?php echo $example['type']; ?>')">
                                            <?php echo $example['name']; ?>: <?php echo number_format($example['amount']); ?> - %<?php echo $example['rate']; ?> - <?php echo $example['months']; ?> ay
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
                        <h4><i class="fas fa-chart-pie"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Kredi Hesaplama Sonucu' : 'Loan Calculation Result'; ?>
                        </h4>
                        <div class="loan-result-display">
                            <!-- Aylık Ödeme -->
                            <div class="alert alert-primary mb-3">
                                <h2 class="mb-1"><?php echo number_format($result['monthly_payment'], 2); ?> 
                                    <?php echo ($currentLang === 'tr') ? '₺' : '$'; ?>
                                </h2>
                                <p class="mb-0">
                                    <strong><?php echo ($currentLang === 'tr') ? 'Aylık Taksit' : 'Monthly Payment'; ?></strong>
                                </p>
                            </div>
                            
                            <!-- Detaylı Bilgiler -->
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="text-center p-3 bg-success text-white rounded">
                                        <strong><?php echo number_format($result['total_payment'], 0); ?></strong><br>
                                        <small><?php echo ($currentLang === 'tr') ? 'Toplam Ödeme' : 'Total Payment'; ?></small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-3 bg-warning text-white rounded">
                                        <strong><?php echo number_format($result['total_interest'], 0); ?></strong><br>
                                        <small><?php echo ($currentLang === 'tr') ? 'Toplam Faiz' : 'Total Interest'; ?></small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Kredi Özeti -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6><?php echo ($currentLang === 'tr') ? 'Kredi Özeti' : 'Loan Summary'; ?></h6>
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td><?php echo ($currentLang === 'tr') ? 'Kredi Türü:' : 'Loan Type:'; ?></td>
                                            <td><strong><?php echo $result['loan_type_name']; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo ($currentLang === 'tr') ? 'Ana Para:' : 'Principal:'; ?></td>
                                            <td><strong><?php echo number_format($result['principal'], 0); ?> 
                                                <?php echo ($currentLang === 'tr') ? '₺' : '$'; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo ($currentLang === 'tr') ? 'Faiz Oranı:' : 'Interest Rate:'; ?></td>
                                            <td><strong>%<?php echo $result['rate']; ?> <?php echo ($currentLang === 'tr') ? 'yıllık' : 'annually'; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo ($currentLang === 'tr') ? 'Vade:' : 'Term:'; ?></td>
                                            <td><strong><?php echo $result['months']; ?> <?php echo ($currentLang === 'tr') ? 'ay' : 'months'; ?> 
                                                (<?php echo $result['years']; ?> <?php echo ($currentLang === 'tr') ? 'yıl' : 'years'; ?>)</strong></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo ($currentLang === 'tr') ? 'Toplam Faiz Oranı:' : 'Total Interest Rate:'; ?></td>
                                            <td><strong>%<?php echo $result['interest_percentage']; ?></strong></td>
                                        </tr>
                                    </table>
                                </div>
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
                                <?php echo ($currentLang === 'tr') ? 'Kredi Hesaplayıcı Hakkında' : 'About Loan Calculator'; ?>
                            </h5>
                            <ul class="mb-3">
                                <li><?php echo ($currentLang === 'tr') ? 'Aylık taksit hesaplama' : 'Monthly payment calculation'; ?></li>
                                <li><?php echo ($currentLang === 'tr') ? 'Toplam faiz analizi' : 'Total interest analysis'; ?></li>
                                <li><?php echo ($currentLang === 'tr') ? 'Farklı kredi türleri' : 'Different loan types'; ?></li>
                                <li><?php echo ($currentLang === 'tr') ? 'Ödeme planı görüntüleme' : 'Payment schedule display'; ?></li>
                            </ul>
                            
                            <div class="alert alert-info">
                                <small>
                                    <i class="fas fa-lightbulb"></i>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Kredi tutarı, faiz oranı ve vade bilgilerini girerek hesaplamayı başlatın.' :
                                        'Enter loan amount, interest rate and term information to start calculation.'; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($paymentSchedule): ?>
        <!-- Payment Schedule -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5><i class="fas fa-calendar-check"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Ödeme Planı (İlk 12 Ay)' : 'Payment Schedule (First 12 Months)'; ?>
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo ($currentLang === 'tr') ? 'Ay' : 'Month'; ?></th>
                                        <th><?php echo ($currentLang === 'tr') ? 'Ödeme' : 'Payment'; ?></th>
                                        <th><?php echo ($currentLang === 'tr') ? 'Ana Para' : 'Principal'; ?></th>
                                        <th><?php echo ($currentLang === 'tr') ? 'Faiz' : 'Interest'; ?></th>
                                        <th><?php echo ($currentLang === 'tr') ? 'Kalan Bakiye' : 'Remaining Balance'; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($paymentSchedule as $payment): ?>
                                        <tr>
                                            <td><?php echo $payment['month']; ?></td>
                                            <td><?php echo number_format($payment['payment'], 2); ?> 
                                                <?php echo ($currentLang === 'tr') ? '₺' : '$'; ?></td>
                                            <td><?php echo number_format($payment['principal'], 2); ?> 
                                                <?php echo ($currentLang === 'tr') ? '₺' : '$'; ?></td>
                                            <td><?php echo number_format($payment['interest'], 2); ?> 
                                                <?php echo ($currentLang === 'tr') ? '₺' : '$'; ?></td>
                                            <td><?php echo number_format($payment['balance'], 2); ?> 
                                                <?php echo ($currentLang === 'tr') ? '₺' : '$'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($result['months'] > 12): ?>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                <?php echo ($currentLang === 'tr') ? 
                                    'Sadece ilk 12 ay gösterilmektedir. Toplam vade: ' . $result['months'] . ' ay' :
                                    'Only first 12 months are shown. Total term: ' . $result['months'] . ' months'; ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Ad Space -->
        <?php echo renderAdSpace('content', 'large'); ?>

    </div>
</main>

<script>
// Loan Calculator specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const currentLang = '<?php echo $currentLang; ?>';
    
    // Track tool usage
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Tool', 'View', 'Loan Calculator');
        
        // Add to recent tools
        const toolName = currentLang === 'tr' ? 'Kredi Hesaplayıcı' : 'Loan Calculator';
        const toolUrl = '/tools/loan-calculator.php?lang=' + currentLang;
        AllInToolbox.storage.addRecentTool('loan-calculator', toolName, toolUrl);
    }
});

// Update loan defaults based on selected type
function updateLoanDefaults() {
    const typeSelect = document.getElementById('loan_type');
    const rateInput = document.getElementById('rate');
    const monthsInput = document.getElementById('months');
    
    const selectedOption = typeSelect.options[typeSelect.selectedIndex];
    const defaultRate = selectedOption.getAttribute('data-rate');
    const maxTerm = selectedOption.getAttribute('data-max-term');
    
    // Update placeholder values
    rateInput.placeholder = defaultRate;
    monthsInput.setAttribute('max', maxTerm);
    
    // Auto-fill if empty
    if (!rateInput.value) {
        rateInput.value = defaultRate;
    }
}

// Set example values
function setExample(amount, rate, months, type) {
    document.getElementById('principal').value = amount;
    document.getElementById('rate').value = rate;
    document.getElementById('months').value = months;
    document.getElementById('loan_type').value = type;
}

// Copy result
function copyResult() {
    <?php if ($result): ?>
    const resultText = 
        '<?php echo ($currentLang === 'tr') ? 'KREDİ HESAPLAMA SONUCU' : 'LOAN CALCULATION RESULT'; ?>\n' +
        '<?php echo str_repeat('=', 30); ?>\n\n' +
        '<?php echo ($currentLang === 'tr') ? 'Aylık Taksit: ' : 'Monthly Payment: '; ?><?php echo number_format($result['monthly_payment'], 2); ?> <?php echo ($currentLang === 'tr') ? '₺' : '$'; ?>\n' +
        '<?php echo ($currentLang === 'tr') ? 'Toplam Ödeme: ' : 'Total Payment: '; ?><?php echo number_format($result['total_payment'], 0); ?> <?php echo ($currentLang === 'tr') ? '₺' : '$'; ?>\n' +
        '<?php echo ($currentLang === 'tr') ? 'Toplam Faiz: ' : 'Total Interest: '; ?><?php echo number_format($result['total_interest'], 0); ?> <?php echo ($currentLang === 'tr') ? '₺' : '$'; ?>\n\n' +
        '<?php echo ($currentLang === 'tr') ? 'Kredi Detayları:' : 'Loan Details:'; ?>\n' +
        '<?php echo ($currentLang === 'tr') ? 'Kredi Türü: ' : 'Loan Type: '; ?><?php echo $result['loan_type_name']; ?>\n' +
        '<?php echo ($currentLang === 'tr') ? 'Ana Para: ' : 'Principal: '; ?><?php echo number_format($result['principal'], 0); ?> <?php echo ($currentLang === 'tr') ? '₺' : '$'; ?>\n' +
        '<?php echo ($currentLang === 'tr') ? 'Faiz Oranı: %' : 'Interest Rate: %'; ?><?php echo $result['rate']; ?>\n' +
        '<?php echo ($currentLang === 'tr') ? 'Vade: ' : 'Term: '; ?><?php echo $result['months']; ?> <?php echo ($currentLang === 'tr') ? 'ay' : 'months'; ?>';
    
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
// Track successful calculation
if (typeof AllInToolbox !== 'undefined') {
    AllInToolbox.analytics.trackEvent('Tool', 'Calculate', 'Loan Calculator');
}
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>