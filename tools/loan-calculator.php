<?php
// tools/loan-calculator.php
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Kredi hesaplama
$result = null;
$error = null;

if ($_POST) {
    $principal = floatval($_POST['principal'] ?? 0);
    $rate = floatval($_POST['rate'] ?? 0);
    $months = intval($_POST['months'] ?? 0);
    
    if ($principal > 0 && $rate > 0 && $months > 0) {
        $monthlyRate = ($rate / 100) / 12;
        $monthlyPayment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $months)) / (pow(1 + $monthlyRate, $months) - 1);
        $totalPayment = $monthlyPayment * $months;
        $totalInterest = $totalPayment - $principal;
        
        $result = [
            'monthly_payment' => round($monthlyPayment, 2),
            'total_payment' => round($totalPayment, 2),
            'total_interest' => round($totalInterest, 2),
            'principal' => $principal,
            'rate' => $rate,
            'months' => $months
        ];
    } else {
        $error = ($currentLang === 'tr') ? 'Lütfen geçerli değerler girin!' : 'Please enter valid values!';
    }
}

include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">
        <h1><i class="fas fa-calculator text-primary"></i> 
            <?php echo ($currentLang === 'tr') ? 'Kredi Hesaplayıcı' : 'Loan Calculator'; ?>
        </h1>
        <p class="lead">
            <?php echo ($currentLang === 'tr') ? 'Kredi taksit ve faiz hesaplama' : 'Calculate loan payments and interest'; ?>
        </p>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">
                                    <?php echo ($currentLang === 'tr') ? 'Kredi Tutarı (₺)' : 'Loan Amount ($)'; ?>
                                </label>
                                <input type="number" name="principal" class="form-control" step="0.01" 
                                       value="<?php echo $_POST['principal'] ?? ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <?php echo ($currentLang === 'tr') ? 'Yıllık Faiz Oranı (%)' : 'Annual Interest Rate (%)'; ?>
                                </label>
                                <input type="number" name="rate" class="form-control" step="0.01" 
                                       value="<?php echo $_POST['rate'] ?? ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <?php echo ($currentLang === 'tr') ? 'Vade (Ay)' : 'Term (Months)'; ?>
                                </label>
                                <input type="number" name="months" class="form-control" 
                                       value="<?php echo $_POST['months'] ?? ''; ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-calculator"></i> 
                                <?php echo ($currentLang === 'tr') ? 'Hesapla' : 'Calculate'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <?php if ($result): ?>
                <div class="card border-success">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <?php echo ($currentLang === 'tr') ? 'Hesaplama Sonucu' : 'Calculation Result'; ?>
                        </h5>
                        
                        <div class="alert alert-primary">
                            <h4><?php echo number_format($result['monthly_payment'], 2); ?> 
                                <?php echo ($currentLang === 'tr') ? '₺' : '$'; ?>
                            </h4>
                            <small><?php echo ($currentLang === 'tr') ? 'Aylık Taksit' : 'Monthly Payment'; ?></small>
                        </div>
                        
                        <table class="table table-sm">
                            <tr>
                                <td><?php echo ($currentLang === 'tr') ? 'Toplam Ödeme:' : 'Total Payment:'; ?></td>
                                <td><strong><?php echo number_format($result['total_payment'], 2); ?> 
                                    <?php echo ($currentLang === 'tr') ? '₺' : '$'; ?></strong></td>
                            </tr>
                            <tr>
                                <td><?php echo ($currentLang === 'tr') ? 'Toplam Faiz:' : 'Total Interest:'; ?></td>
                                <td><strong><?php echo number_format($result['total_interest'], 2); ?> 
                                    <?php echo ($currentLang === 'tr') ? '₺' : '$'; ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php elseif ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <h6><?php echo ($currentLang === 'tr') ? 'Kredi Hesaplayıcı Nasıl Kullanılır?' : 'How to Use Loan Calculator?'; ?></h6>
                        <ul class="small">
                            <li><?php echo ($currentLang === 'tr') ? 'Kredi tutarını girin' : 'Enter loan amount'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? 'Yıllık faiz oranını girin' : 'Enter annual interest rate'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? 'Vadeyi ay olarak girin' : 'Enter term in months'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? 'Hesapla butonuna tıklayın' : 'Click calculate button'; ?></li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>