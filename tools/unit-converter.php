<?php
// tools/unit-converter.php - ÖLÇÜ BİRİMİ ÇEVİRİCİ
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Sayfa meta bilgileri
$pageTitle = ($currentLang === 'tr') ? 'Ölçü Birimi Çevirici' : 'Unit Converter';
$pageDescription = ($currentLang === 'tr') ? 
    'Ücretsiz ölçü birimi çevirici. Uzunluk, ağırlık, sıcaklık, hacim ve daha fazlasını çevirin.' :
    'Free unit converter. Convert length, weight, temperature, volume and more.';

// Ölçü birimi kategorileri ve dönüşüm faktörleri
$unitCategories = [
    'length' => [
        'name' => ['tr' => 'Uzunluk', 'en' => 'Length'],
        'icon' => 'fas fa-ruler',
        'units' => [
            'mm' => ['name' => ['tr' => 'Milimetre', 'en' => 'Millimeter'], 'factor' => 0.001],
            'cm' => ['name' => ['tr' => 'Santimetre', 'en' => 'Centimeter'], 'factor' => 0.01],
            'm' => ['name' => ['tr' => 'Metre', 'en' => 'Meter'], 'factor' => 1],
            'km' => ['name' => ['tr' => 'Kilometre', 'en' => 'Kilometer'], 'factor' => 1000],
            'in' => ['name' => ['tr' => 'İnç', 'en' => 'Inch'], 'factor' => 0.0254],
            'ft' => ['name' => ['tr' => 'Fit', 'en' => 'Foot'], 'factor' => 0.3048],
            'yd' => ['name' => ['tr' => 'Yarda', 'en' => 'Yard'], 'factor' => 0.9144],
            'mi' => ['name' => ['tr' => 'Mil', 'en' => 'Mile'], 'factor' => 1609.34]
        ]
    ],
    'weight' => [
        'name' => ['tr' => 'Ağırlık', 'en' => 'Weight'],
        'icon' => 'fas fa-weight',
        'units' => [
            'mg' => ['name' => ['tr' => 'Miligram', 'en' => 'Milligram'], 'factor' => 0.000001],
            'g' => ['name' => ['tr' => 'Gram', 'en' => 'Gram'], 'factor' => 0.001],
            'kg' => ['name' => ['tr' => 'Kilogram', 'en' => 'Kilogram'], 'factor' => 1],
            't' => ['name' => ['tr' => 'Ton', 'en' => 'Ton'], 'factor' => 1000],
            'oz' => ['name' => ['tr' => 'Ons', 'en' => 'Ounce'], 'factor' => 0.0283495],
            'lb' => ['name' => ['tr' => 'Pound', 'en' => 'Pound'], 'factor' => 0.453592],
            'st' => ['name' => ['tr' => 'Stone', 'en' => 'Stone'], 'factor' => 6.35029]
        ]
    ],
    'temperature' => [
        'name' => ['tr' => 'Sıcaklık', 'en' => 'Temperature'],
        'icon' => 'fas fa-thermometer-half',
        'units' => [
            'c' => ['name' => ['tr' => 'Celsius', 'en' => 'Celsius'], 'factor' => 1],
            'f' => ['name' => ['tr' => 'Fahrenheit', 'en' => 'Fahrenheit'], 'factor' => 1],
            'k' => ['name' => ['tr' => 'Kelvin', 'en' => 'Kelvin'], 'factor' => 1]
        ]
    ],
    'volume' => [
        'name' => ['tr' => 'Hacim', 'en' => 'Volume'],
        'icon' => 'fas fa-flask',
        'units' => [
            'ml' => ['name' => ['tr' => 'Mililitre', 'en' => 'Milliliter'], 'factor' => 0.001],
            'l' => ['name' => ['tr' => 'Litre', 'en' => 'Liter'], 'factor' => 1],
            'm3' => ['name' => ['tr' => 'Metreküp', 'en' => 'Cubic Meter'], 'factor' => 1000],
            'floz' => ['name' => ['tr' => 'Sıvı Ons', 'en' => 'Fluid Ounce'], 'factor' => 0.0295735],
            'cup' => ['name' => ['tr' => 'Fincan', 'en' => 'Cup'], 'factor' => 0.236588],
            'pt' => ['name' => ['tr' => 'Pint', 'en' => 'Pint'], 'factor' => 0.473176],
            'qt' => ['name' => ['tr' => 'Quart', 'en' => 'Quart'], 'factor' => 0.946353],
            'gal' => ['name' => ['tr' => 'Galon', 'en' => 'Gallon'], 'factor' => 3.78541]
        ]
    ],
    'area' => [
        'name' => ['tr' => 'Alan', 'en' => 'Area'],
        'icon' => 'fas fa-square',
        'units' => [
            'mm2' => ['name' => ['tr' => 'Milimetre²', 'en' => 'Square Millimeter'], 'factor' => 0.000001],
            'cm2' => ['name' => ['tr' => 'Santimetre²', 'en' => 'Square Centimeter'], 'factor' => 0.0001],
            'm2' => ['name' => ['tr' => 'Metre²', 'en' => 'Square Meter'], 'factor' => 1],
            'km2' => ['name' => ['tr' => 'Kilometre²', 'en' => 'Square Kilometer'], 'factor' => 1000000],
            'in2' => ['name' => ['tr' => 'İnç²', 'en' => 'Square Inch'], 'factor' => 0.00064516],
            'ft2' => ['name' => ['tr' => 'Fit²', 'en' => 'Square Foot'], 'factor' => 0.092903],
            'ac' => ['name' => ['tr' => 'Akre', 'en' => 'Acre'], 'factor' => 4046.86]
        ]
    ]
];

// Sıcaklık dönüşüm fonksiyonları
function convertTemperature($value, $from, $to) {
    // Önce Celsius'a çevir
    switch ($from) {
        case 'f':
            $celsius = ($value - 32) * 5/9;
            break;
        case 'k':
            $celsius = $value - 273.15;
            break;
        default:
            $celsius = $value;
    }
    
    // Celsius'tan hedef birime çevir
    switch ($to) {
        case 'f':
            return $celsius * 9/5 + 32;
        case 'k':
            return $celsius + 273.15;
        default:
            return $celsius;
    }
}

// Genel ölçü birimi dönüşümü
function convertUnit($value, $fromUnit, $toUnit, $category, $unitCategories) {
    if ($category === 'temperature') {
        return convertTemperature($value, $fromUnit, $toUnit);
    }
    
    $units = $unitCategories[$category]['units'];
    
    if (!isset($units[$fromUnit]) || !isset($units[$toUnit])) {
        return false;
    }
    
    // Temel birime çevir, sonra hedef birime çevir
    $baseValue = $value * $units[$fromUnit]['factor'];
    return $baseValue / $units[$toUnit]['factor'];
}

// Form işleme
$result = null;
$error = null;

if ($_POST) {
    $value = floatval($_POST['value'] ?? 0);
    $category = $_POST['category'] ?? 'length';
    $fromUnit = $_POST['from_unit'] ?? 'm';
    $toUnit = $_POST['to_unit'] ?? 'km';
    
    if ($value != 0 && isset($unitCategories[$category])) {
        $convertedValue = convertUnit($value, $fromUnit, $toUnit, $category, $unitCategories);
        
        if ($convertedValue !== false) {
            $result = [
                'value' => $value,
                'converted_value' => round($convertedValue, 6),
                'from_unit' => $fromUnit,
                'to_unit' => $toUnit,
                'category' => $category,
                'from_unit_name' => $unitCategories[$category]['units'][$fromUnit]['name'][$currentLang],
                'to_unit_name' => $unitCategories[$category]['units'][$toUnit]['name'][$currentLang]
            ];
        } else {
            $error = ($currentLang === 'tr') ? 'Dönüşüm hatası!' : 'Conversion error!';
        }
    } else {
        $error = ($currentLang === 'tr') ? 'Lütfen geçerli bir değer girin!' : 'Please enter a valid value!';
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
            ['title' => ($currentLang === 'tr') ? 'Çevirici Araçları' : 'Converter Tools', 'url' => '/' . $currentLang . '/category/converter'],
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
                    <!-- Unit Form -->
                    <div class="tool-form">
                        <form method="POST" id="unitForm">
                            <!-- Category Selection -->
                            <div class="mb-3">
                                <label for="category" class="form-label">
                                    <i class="fas fa-th-list"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Kategori' : 'Category'; ?>
                                </label>
                                <select class="form-control" id="category" name="category" onchange="updateUnits()" required>
                                    <?php foreach ($unitCategories as $catId => $catInfo): ?>
                                        <option value="<?php echo $catId; ?>" 
                                                <?php echo (($_POST['category'] ?? 'length') === $catId) ? 'selected' : ''; ?>>
                                            <?php echo $catInfo['name'][$currentLang]; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Value Input -->
                            <div class="mb-3">
                                <label for="value" class="form-label">
                                    <i class="fas fa-hashtag"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Değer' : 'Value'; ?>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="value" 
                                       name="value" 
                                       step="any" 
                                       placeholder="<?php echo ($currentLang === 'tr') ? 'Çevrilecek değeri girin' : 'Enter value to convert'; ?>"
                                       value="<?php echo isset($_POST['value']) ? htmlspecialchars($_POST['value']) : '1'; ?>"
                                       required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="from_unit" class="form-label">
                                        <i class="fas fa-arrow-right"></i> 
                                        <?php echo ($currentLang === 'tr') ? 'Çevrilecek Birim' : 'From Unit'; ?>
                                    </label>
                                    <select class="form-control" id="from_unit" name="from_unit" required>
                                        <!-- JavaScript ile doldurulacak -->
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="to_unit" class="form-label">
                                        <i class="fas fa-arrow-left"></i> 
                                        <?php echo ($currentLang === 'tr') ? 'Hedef Birim' : 'To Unit'; ?>
                                    </label>
                                    <select class="form-control" id="to_unit" name="to_unit" required>
                                        <!-- JavaScript ile doldurulacak -->
                                    </select>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="convertBtn">
                                    <i class="fas fa-sync-alt"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Çevir' : 'Convert'; ?>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="swapUnits()">
                                    <i class="fas fa-exchange-alt"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Birimleri Değiştir' : 'Swap Units'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Unit Info -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Ölçü Birimi Çevirici Hakkında' : 'About Unit Converter'; ?>
                        </h6>
                        <p class="mb-0">
                            <?php echo ($currentLang === 'tr') ? 
                                'Uzunluk, ağırlık, sıcaklık, hacim ve alan ölçülerini kolayca çevirin.' :
                                'Easily convert length, weight, temperature, volume and area measurements.'; ?>
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
                        <div class="unit-result-display">
                            <div class="currency-box mb-3">
                                <div class="row text-center">
                                    <div class="col-5">
                                        <div class="p-3">
                                            <h3><?php echo number_format($result['value'], 6); ?></h3>
                                            <strong><?php echo $result['from_unit_name']; ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-2 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-equals fa-2x"></i>
                                    </div>
                                    <div class="col-5">
                                        <div class="p-3">
                                            <h3><?php echo number_format($result['converted_value'], 6); ?></h3>
                                            <strong><?php echo $result['to_unit_name']; ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-success mb-3">
                                <p class="mb-0">
                                    <strong>1 <?php echo $result['from_unit_name']; ?> = 
                                    <?php echo number_format($result['converted_value'] / $result['value'], 6); ?> 
                                    <?php echo $result['to_unit_name']; ?></strong>
                                </p>
                                <small class="text-muted">
                                    <?php echo ($currentLang === 'tr') ? 'Dönüşüm oranı' : 'Conversion rate'; ?>
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
                                <?php echo ($currentLang === 'tr') ? 'Hızlı Çevrim' : 'Quick Conversion'; ?>
                            </h5>
                            <div class="row g-2">
                                <?php 
                                $quickConversions = [
                                    ['cat' => 'length', 'from' => 'm', 'to' => 'ft', 'val' => '100'],
                                    ['cat' => 'weight', 'from' => 'kg', 'to' => 'lb', 'val' => '70'],
                                    ['cat' => 'temperature', 'from' => 'c', 'to' => 'f', 'val' => '25'],
                                    ['cat' => 'volume', 'from' => 'l', 'to' => 'gal', 'val' => '20']
                                ];
                                foreach ($quickConversions as $quick): ?>
                                    <div class="col-6 mb-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" 
                                                onclick="setQuickConversion('<?php echo $quick['cat']; ?>', '<?php echo $quick['from']; ?>', '<?php echo $quick['to']; ?>', '<?php echo $quick['val']; ?>')">
                                            <?php echo $quick['val'] . ' ' . strtoupper($quick['from']) . ' → ' . strtoupper($quick['to']); ?>
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

        <!-- Unit Information -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3><i class="fas fa-book"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Ölçü Birimleri Hakkında Detaylı Bilgi' : 'Detailed Information About Units'; ?>
                        </h3>
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Desteklenen Kategoriler' : 'Supported Categories'; ?></h5>
                                <div class="row">
                                    <?php foreach ($unitCategories as $catId => $catInfo): ?>
                                        <div class="col-12 mb-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6>
                                                        <i class="<?php echo $catInfo['icon']; ?>"></i> 
                                                        <?php echo $catInfo['name'][$currentLang]; ?>
                                                    </h6>
                                                    <small class="text-muted">
                                                        <?php echo count($catInfo['units']); ?> 
                                                        <?php echo ($currentLang === 'tr') ? 'birim desteklenir' : 'units supported'; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Nasıl Kullanılır?' : 'How to Use?'; ?></h5>
                                <ol>
                                    <li><?php echo ($currentLang === 'tr') ? 'Kategori seçin (uzunluk, ağırlık, vb.)' : 'Select category (length, weight, etc.)'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Çevrilecek değeri girin' : 'Enter value to convert'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Kaynak ve hedef birimlerini seçin' : 'Select source and target units'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Çevir butonuna tıklayın' : 'Click convert button'; ?></li>
                                </ol>
                                
                                <h5><?php echo ($currentLang === 'tr') ? 'Özellikler' : 'Features'; ?></h5>
                                <ul>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Yüksek hassasiyetli dönüşümler' : 
                                        'High precision conversions'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        '50+ farklı ölçü birimi' : 
                                        '50+ different units'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Metrik ve imperial sistemler' : 
                                        'Metric and imperial systems'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Sıcaklık dahil özel dönüşümler' : 
                                        'Special conversions including temperature'; ?></li>
                                </ul>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-lightbulb"></i>
                                    <strong><?php echo ($currentLang === 'tr') ? 'İpucu:' : 'Tip:'; ?></strong>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Birim değiştir butonuyla kaynak ve hedef birimleri hızlıca değiştirebilirsiniz.' :
                                        'Use the swap button to quickly switch source and target units.'; ?>
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
                            'name' => ($currentLang === 'tr') ? 'Döviz Çevirici' : 'Currency Converter',
                            'description' => ($currentLang === 'tr') ? 'Para birimlerini çevirin' : 'Convert currencies',
                            'url' => '/tools/currency-converter.php?lang=' . $currentLang,
                            'icon' => 'fas fa-exchange-alt'
                        ],
                        [
                            'name' => ($currentLang === 'tr') ? 'BMI Hesaplayıcı' : 'BMI Calculator',
                            'description' => ($currentLang === 'tr') ? 'Vücut kitle indeksi hesaplama' : 'Calculate body mass index',
                            'url' => '/tools/bmi-calculator.php?lang=' . $currentLang,
                            'icon' => 'fas fa-weight'
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
// Unit data for JavaScript
const unitCategories = <?php echo json_encode($unitCategories); ?>;
const currentLang = '<?php echo $currentLang; ?>';

// Unit Converter specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('unitForm');
    const convertBtn = document.getElementById('convertBtn');
    
    // Track tool usage
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Tool', 'View', 'Unit Converter');
        
        // Add to recent tools
        const toolName = currentLang === 'tr' ? 'Ölçü Birimi Çevirici' : 'Unit Converter';
        AllInToolbox.tools.addRecentTool({
            name: toolName,
            url: window.location.href
        });
    }
});
    // Update units based on category
    updateUnits();

function updateUnits() {
    const categorySelect = document.getElementById('category');
    const fromUnitSelect = document.getElementById('from_unit');
    const toUnitSelect = document.getElementById('to_unit');
    
    const selectedCategory = categorySelect.value;
    const units = unitCategories[selectedCategory].units;
    
    // Clear existing options
    fromUnitSelect.innerHTML = '';
    toUnitSelect.innerHTML = '';
    
    // Populate from unit select
    for (const [unit, info] of Object.entries(units)) {
        const option = document.createElement('option');
        option.value = unit;
        option.textContent = info.name[currentLang];
        fromUnitSelect.appendChild(option);
    }
    
    // Populate to unit select
    for (const [unit, info] of Object.entries(units)) {
        const option = document.createElement('option');
        option.value = unit;
        option.textContent = info.name[currentLang];
        toUnitSelect.appendChild(option);
    }
}   
</script>