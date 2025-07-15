<?php
// tools/color-converter.php - RENK KODU ÇEVİRİCİ
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

// URL helpers'ı yükle
if (file_exists('../config/url-helpers.php')) {
    require_once '../config/url-helpers.php';
}

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Sayfa meta bilgileri
$pageTitle = ($currentLang === 'tr') ? 'Renk Kodu Çevirici' : 'Color Code Converter';
$pageDescription = ($currentLang === 'tr') ? 
    'HEX, RGB, HSL ve HSV renk kodlarını çevirin. Renk paleti oluşturucu ve renk seçici.' :
    'Convert HEX, RGB, HSL and HSV color codes. Color palette generator and color picker.';

// Renk dönüşüm fonksiyonları
function hexToRgb($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    return [
        'r' => hexdec(substr($hex, 0, 2)),
        'g' => hexdec(substr($hex, 2, 2)),
        'b' => hexdec(substr($hex, 4, 2))
    ];
}

function rgbToHex($r, $g, $b) {
    return sprintf("#%02x%02x%02x", $r, $g, $b);
}

function rgbToHsl($r, $g, $b) {
    $r /= 255;
    $g /= 255;
    $b /= 255;
    
    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $diff = $max - $min;
    
    // Hue
    if ($diff == 0) {
        $h = 0;
    } elseif ($max == $r) {
        $h = 60 * (($g - $b) / $diff);
    } elseif ($max == $g) {
        $h = 60 * (2 + ($b - $r) / $diff);
    } else {
        $h = 60 * (4 + ($r - $g) / $diff);
    }
    
    if ($h < 0) $h += 360;
    
    // Lightness
    $l = ($max + $min) / 2;
    
    // Saturation
    if ($diff == 0) {
        $s = 0;
    } else {
        $s = $diff / (1 - abs(2 * $l - 1));
    }
    
    return [
        'h' => round($h),
        's' => round($s * 100),
        'l' => round($l * 100)
    ];
}

function hslToRgb($h, $s, $l) {
    $h /= 360;
    $s /= 100;
    $l /= 100;
    
    if ($s == 0) {
        $r = $g = $b = $l;
    } else {
        $hue2rgb = function($p, $q, $t) {
            if ($t < 0) $t += 1;
            if ($t > 1) $t -= 1;
            if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
            if ($t < 1/2) return $q;
            if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
            return $p;
        };
        
        $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
        $p = 2 * $l - $q;
        
        $r = $hue2rgb($p, $q, $h + 1/3);
        $g = $hue2rgb($p, $q, $h);
        $b = $hue2rgb($p, $q, $h - 1/3);
    }
    
    return [
        'r' => round($r * 255),
        'g' => round($g * 255),
        'b' => round($b * 255)
    ];
}

function rgbToHsv($r, $g, $b) {
    $r /= 255;
    $g /= 255;
    $b /= 255;
    
    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $diff = $max - $min;
    
    // Value
    $v = $max;
    
    // Saturation
    $s = $max == 0 ? 0 : $diff / $max;
    
    // Hue
    if ($diff == 0) {
        $h = 0;
    } elseif ($max == $r) {
        $h = 60 * (($g - $b) / $diff);
    } elseif ($max == $g) {
        $h = 60 * (2 + ($b - $r) / $diff);
    } else {
        $h = 60 * (4 + ($r - $g) / $diff);
    }
    
    if ($h < 0) $h += 360;
    
    return [
        'h' => round($h),
        's' => round($s * 100),
        'v' => round($v * 100)
    ];
}

// Popüler renkler
$popularColors = [
    '#FF0000' => ['tr' => 'Kırmızı', 'en' => 'Red'],
    '#00FF00' => ['tr' => 'Yeşil', 'en' => 'Green'],
    '#0000FF' => ['tr' => 'Mavi', 'en' => 'Blue'],
    '#FFFF00' => ['tr' => 'Sarı', 'en' => 'Yellow'],
    '#FF00FF' => ['tr' => 'Magenta', 'en' => 'Magenta'],
    '#00FFFF' => ['tr' => 'Cyan', 'en' => 'Cyan'],
    '#000000' => ['tr' => 'Siyah', 'en' => 'Black'],
    '#FFFFFF' => ['tr' => 'Beyaz', 'en' => 'White'],
    '#808080' => ['tr' => 'Gri', 'en' => 'Gray'],
    '#FFA500' => ['tr' => 'Turuncu', 'en' => 'Orange'],
    '#800080' => ['tr' => 'Mor', 'en' => 'Purple'],
    '#A52A2A' => ['tr' => 'Kahverengi', 'en' => 'Brown']
];

// Form işleme
$result = null;
$error = null;

if ($_POST) {
    $inputType = $_POST['input_type'] ?? 'hex';
    $colorValue = '';
    
    try {
        switch ($inputType) {
            case 'hex':
                $hex = $_POST['hex_value'] ?? '';
                if (preg_match('/^#?[0-9A-Fa-f]{3,6}$/', $hex)) {
                    $colorValue = ltrim($hex, '#');
                    if (strlen($colorValue) === 3) {
                        $colorValue = $colorValue[0].$colorValue[0].$colorValue[1].$colorValue[1].$colorValue[2].$colorValue[2];
                    }
                    $rgb = hexToRgb('#' . $colorValue);
                } else {
                    throw new Exception('Invalid HEX color');
                }
                break;
                
            case 'rgb':
                $r = intval($_POST['rgb_r'] ?? 0);
                $g = intval($_POST['rgb_g'] ?? 0);
                $b = intval($_POST['rgb_b'] ?? 0);
                
                if ($r >= 0 && $r <= 255 && $g >= 0 && $g <= 255 && $b >= 0 && $b <= 255) {
                    $rgb = ['r' => $r, 'g' => $g, 'b' => $b];
                } else {
                    throw new Exception('Invalid RGB values');
                }
                break;
                
            case 'hsl':
                $h = intval($_POST['hsl_h'] ?? 0);
                $s = intval($_POST['hsl_s'] ?? 0);
                $l = intval($_POST['hsl_l'] ?? 0);
                
                if ($h >= 0 && $h <= 360 && $s >= 0 && $s <= 100 && $l >= 0 && $l <= 100) {
                    $rgb = hslToRgb($h, $s, $l);
                } else {
                    throw new Exception('Invalid HSL values');
                }
                break;
        }
        
        if (isset($rgb)) {
            $hex = rgbToHex($rgb['r'], $rgb['g'], $rgb['b']);
            $hsl = rgbToHsl($rgb['r'], $rgb['g'], $rgb['b']);
            $hsv = rgbToHsv($rgb['r'], $rgb['g'], $rgb['b']);
            
            $result = [
                'hex' => $hex,
                'rgb' => $rgb,
                'hsl' => $hsl,
                'hsv' => $hsv,
                'css_hex' => $hex,
                'css_rgb' => "rgb({$rgb['r']}, {$rgb['g']}, {$rgb['b']})",
                'css_hsl' => "hsl({$hsl['h']}, {$hsl['s']}%, {$hsl['l']}%)"
            ];
        }
        
    } catch (Exception $e) {
        $error = ($currentLang === 'tr') ? 'Geçersiz renk değeri!' : 'Invalid color value!';
    }
}

include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">

        <!-- Breadcrumb -->
        <?php
        $breadcrumbItems = [
            ['title' => __('breadcrumb_home'), 'url' => '/' . $currentLang . '/'],
            ['title' => __('category_converter'), 'url' => function_exists('getCategoryCleanUrl') ? getCategoryCleanUrl('converter', $currentLang) : '/pages/category.php?category=converter&lang=' . $currentLang],
            ['title' => __('color_title')]
        ];
        echo generateBreadcrumb($breadcrumbItems);
        ?>

        <!-- Tool Container -->
        <div class="tool-container">
            <div class="tool-header">
                <h1><i class="fas fa-palette text-primary"></i> <?php echo $pageTitle; ?></h1>
                <p class="lead"><?php echo $pageDescription; ?></p>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <!-- Color Form -->
                    <div class="tool-form">
                        <form method="POST" id="colorForm">
                            <!-- Input Type Selection -->
                            <div class="mb-3">
                                <label for="input_type" class="form-label">
                                    <i class="fas fa-list"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Giriş Türü' : 'Input Type'; ?>
                                </label>
                                <select class="form-control" id="input_type" name="input_type" onchange="showColorFields()" required>
                                    <option value="hex" <?php echo (($_POST['input_type'] ?? 'hex') === 'hex') ? 'selected' : ''; ?>>
                                        HEX
                                    </option>
                                    <option value="rgb" <?php echo (($_POST['input_type'] ?? '') === 'rgb') ? 'selected' : ''; ?>>
                                        RGB
                                    </option>
                                    <option value="hsl" <?php echo (($_POST['input_type'] ?? '') === 'hsl') ? 'selected' : ''; ?>>
                                        HSL
                                    </option>
                                </select>
                            </div>

                            <!-- HEX Input -->
                            <div id="hex_fields" class="color-fields" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo ($currentLang === 'tr') ? 'HEX Renk Kodu' : 'HEX Color Code'; ?>
                                    </label>
                                    <input type="text" name="hex_value" class="form-control" 
                                           placeholder="#FF5733"
                                           value="<?php echo $_POST['hex_value'] ?? ''; ?>">
                                </div>
                            </div>

                            <!-- RGB Input -->
                            <div id="rgb_fields" class="color-fields" style="display: none;">
                                <div class="row">
                                    <div class="col-4 mb-3">
                                        <label class="form-label">R (Red)</label>
                                        <input type="number" name="rgb_r" class="form-control" 
                                               min="0" max="255" placeholder="255"
                                               value="<?php echo $_POST['rgb_r'] ?? ''; ?>">
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label class="form-label">G (Green)</label>
                                        <input type="number" name="rgb_g" class="form-control" 
                                               min="0" max="255" placeholder="87"
                                               value="<?php echo $_POST['rgb_g'] ?? ''; ?>">
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label class="form-label">B (Blue)</label>
                                        <input type="number" name="rgb_b" class="form-control" 
                                               min="0" max="255" placeholder="51"
                                               value="<?php echo $_POST['rgb_b'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- HSL Input -->
                            <div id="hsl_fields" class="color-fields" style="display: none;">
                                <div class="row">
                                    <div class="col-4 mb-3">
                                        <label class="form-label">H (Hue)</label>
                                        <input type="number" name="hsl_h" class="form-control" 
                                               min="0" max="360" placeholder="14"
                                               value="<?php echo $_POST['hsl_h'] ?? ''; ?>">
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label class="form-label">S (Saturation %)</label>
                                        <input type="number" name="hsl_s" class="form-control" 
                                               min="0" max="100" placeholder="100"
                                               value="<?php echo $_POST['hsl_s'] ?? ''; ?>">
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label class="form-label">L (Lightness %)</label>
                                        <input type="number" name="hsl_l" class="form-control" 
                                               min="0" max="100" placeholder="60"
                                               value="<?php echo $_POST['hsl_l'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="convertBtn">
                                    <i class="fas fa-sync-alt"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Rengi Çevir' : 'Convert Color'; ?>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('colorForm').reset(); showColorFields();">
                                    <i class="fas fa-eraser"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Temizle' : 'Clear'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Popular Colors -->
                    <div class="card">
                        <div class="card-body">
                            <h6><?php echo ($currentLang === 'tr') ? 'Popüler Renkler' : 'Popular Colors'; ?></h6>
                            <div class="row g-2">
                                <?php foreach ($popularColors as $hex => $names): ?>
                                    <div class="col-3 col-md-2">
                                        <div class="color-swatch" 
                                             style="background-color: <?php echo $hex; ?>; height: 40px; border-radius: 8px; cursor: pointer; border: 2px solid #ddd;"
                                             onclick="setColor('<?php echo $hex; ?>')"
                                             title="<?php echo $names[$currentLang]; ?> (<?php echo $hex; ?>)">
                                        </div>
                                        <small class="d-block text-center mt-1"><?php echo $names[$currentLang]; ?></small>
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
                        <h4><i class="fas fa-palette"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Renk Çevirimi' : 'Color Conversion'; ?>
                        </h4>
                        <div class="color-result-display">
                            <!-- Color Preview -->
                            <div class="color-preview mb-3" 
                                 style="background-color: <?php echo $result['hex']; ?>; height: 100px; border-radius: var(--border-radius); border: 2px solid var(--border-color);">
                            </div>
                            
                            <!-- Color Codes -->
                            <div class="color-codes">
                                <div class="mb-3">
                                    <label class="form-label"><strong>HEX:</strong></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="hex_result" 
                                               value="<?php echo $result['hex']; ?>" readonly>
                                        <button class="btn btn-outline-secondary" onclick="copyColorCode('hex_result')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><strong>RGB:</strong></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="rgb_result" 
                                               value="<?php echo $result['css_rgb']; ?>" readonly>
                                        <button class="btn btn-outline-secondary" onclick="copyColorCode('rgb_result')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><strong>HSL:</strong></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="hsl_result" 
                                               value="<?php echo $result['css_hsl']; ?>" readonly>
                                        <button class="btn btn-outline-secondary" onclick="copyColorCode('hsl_result')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><strong>HSV:</strong></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="hsv_result" 
                                               value="hsv(<?php echo $result['hsv']['h']; ?>, <?php echo $result['hsv']['s']; ?>%, <?php echo $result['hsv']['v']; ?>%)" readonly>
                                        <button class="btn btn-outline-secondary" onclick="copyColorCode('hsv_result')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-3">
                                <button class="btn btn-light" onclick="copyAllCodes()">
                                    <i class="fas fa-copy"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Tüm Kodları Kopyala' : 'Copy All Codes'; ?>
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
                                <?php echo ($currentLang === 'tr') ? 'Renk Formatları' : 'Color Formats'; ?>
                            </h5>
                            <div class="mb-3">
                                <strong>HEX:</strong> #FF5733<br>
                                <small class="text-muted">
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Hexadecimal renk kodu (# ile başlar)' :
                                        'Hexadecimal color code (starts with #)'; ?>
                                </small>
                            </div>
                            <div class="mb-3">
                                <strong>RGB:</strong> rgb(255, 87, 51)<br>
                                <small class="text-muted">
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Kırmızı, Yeşil, Mavi (0-255)' :
                                        'Red, Green, Blue (0-255)'; ?>
                                </small>
                            </div>
                            <div class="mb-3">
                                <strong>HSL:</strong> hsl(14, 100%, 60%)<br>
                                <small class="text-muted">
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Ton, Doygunluk, Parlaklık' :
                                        'Hue, Saturation, Lightness'; ?>
                                </small>
                            </div>
                            <div class="mb-3">
                                <strong>HSV:</strong> hsv(14, 80%, 100%)<br>
                                <small class="text-muted">
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Ton, Doygunluk, Değer' :
                                        'Hue, Saturation, Value'; ?>
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

        <!-- Color Information -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3><i class="fas fa-book"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Renk Kodları Hakkında Detaylı Bilgi' : 'Detailed Information About Color Codes'; ?>
                        </h3>
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Renk Formatları' : 'Color Formats'; ?></h5>
                                
                                <h6>HEX (Hexadecimal)</h6>
                                <p>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Web\'de en yaygın kullanılan format. # işareti ile başlar ve 6 karakterden oluşur. Her iki karakter RGB değerlerini temsil eder.' :
                                        'Most commonly used format on the web. Starts with # and consists of 6 characters. Every two characters represent RGB values.'; ?>
                                </p>
                                
                                <h6>RGB (Red, Green, Blue)</h6>
                                <p>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Kırmızı, yeşil ve mavi ışık değerlerini 0-255 arasında belirtir. Ekran teknolojilerinde kullanılır.' :
                                        'Specifies red, green and blue light values between 0-255. Used in screen technologies.'; ?>
                                </p>
                                
                                <h6>HSL (Hue, Saturation, Lightness)</h6>
                                <p>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Ton (0-360°), doygunluk (0-100%) ve parlaklık (0-100%) değerleri. Daha sezgisel renk seçimi sağlar.' :
                                        'Hue (0-360°), saturation (0-100%) and lightness (0-100%) values. Provides more intuitive color selection.'; ?>
                                </p>
                            </div>
                            
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Kullanım Alanları' : 'Usage Areas'; ?></h5>
                                <ul>
                                    <li><strong>Web Tasarım:</strong> <?php echo ($currentLang === 'tr') ? 'CSS, HTML' : 'CSS, HTML'; ?></li>
                                    <li><strong>Grafik Tasarım:</strong> <?php echo ($currentLang === 'tr') ? 'Photoshop, Illustrator' : 'Photoshop, Illustrator'; ?></li>
                                    <li><strong>Programlama:</strong> <?php echo ($currentLang === 'tr') ? 'UI/UX geliştirme' : 'UI/UX development'; ?></li>
                                    <li><strong>Yazdırma:</strong> <?php echo ($currentLang === 'tr') ? 'CMYK dönüşümü' : 'CMYK conversion'; ?></li>
                                </ul>
                                
                                <h5><?php echo ($currentLang === 'tr') ? 'İpuçları' : 'Tips'; ?></h5>
                                <ul>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'HEX kodları büyük/küçük harf duyarsızdır' :
                                        'HEX codes are case-insensitive'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        '3 karakterli HEX (#F5A) otomatik olarak 6 karaktere genişletilir' :
                                        '3-character HEX (#F5A) is automatically expanded to 6 characters'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'HSL renk harmonileri oluşturmak için idealdir' :
                                        'HSL is ideal for creating color harmonies'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'RGB değerleri doğrudan piksel renklerini temsil eder' :
                                        'RGB values directly represent pixel colors'; ?></li>
                                </ul>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-lightbulb"></i>
                                    <strong><?php echo ($currentLang === 'tr') ? 'Profesyonel İpucu:' : 'Pro Tip:'; ?></strong>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Renk paleti oluştururken HSL kullanın - aynı tonu farklı doygunluk ve parlaklıkta kullanarak uyumlu renkler elde edebilirsiniz.' :
                                        'Use HSL when creating color palettes - you can achieve harmonious colors by using the same hue with different saturation and lightness.'; ?>
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
                <h4><i class="fas fa-link"></i> <?php echo __('related_tools'); ?></h4>
                <div class="row">

                <?php
                $relatedTools = ['age-calculator', 'bmi-calculator'];
                foreach ($relatedTools as $toolId):
                    // Clean URLs kullan varsa
                    if (function_exists('getToolInfoWithCleanUrl')) {
                        $toolInfo = getToolInfoWithCleanUrl($toolId, $currentLang);
                    } else {
                        $toolInfo = getToolInfo($toolId, $currentLang);
                    }
                    
                    if ($toolInfo):
                ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo $toolInfo['name']; ?></h6>
                                    <p class="card-text"><?php echo $toolInfo['description']; ?></p>
                                    <a href="<?php echo $toolInfo['url']; ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-arrow-right"></i> <?php echo __('use_tool'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
// Color Converter specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('colorForm');
    const convertBtn = document.getElementById('convertBtn');
    const currentLang = '<?php echo $currentLang; ?>';
    
    // Track tool usage
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Tool', 'View', 'Color Converter');
        
        // Add to recent tools
        const toolName = currentLang === 'tr' ? 'Renk Kodu Çevirici' : 'Color Converter';
        const toolUrl = '/tools/color-converter.php?lang=' + currentLang;
        AllInToolbox.storage.addRecentTool('color-converter', toolName, toolUrl);
    }
    
    // Show correct fields on page load
    showColorFields();
    
    // Form submission
    form.addEventListener('submit', function(e) {
        if (typeof AllInToolbox !== 'undefined') {
            AllInToolbox.utils.showLoading(convertBtn);
            AllInToolbox.analytics.trackToolUsage('Color Converter');
        }
        
        // Simulate processing time
        setTimeout(() => {
            if (typeof AllInToolbox !== 'undefined') {
                AllInToolbox.utils.showLoading(convertBtn, false);
            }
        }, 500);
    });
});

// Show appropriate fields based on input type
function showColorFields() {
    const inputType = document.getElementById('input_type').value;
    const allFields = document.querySelectorAll('.color-fields');
    
    // Hide all fields
    allFields.forEach(field => field.style.display = 'none');
    
    // Show selected field
    const selectedField = document.getElementById(inputType + '_fields');
    if (selectedField) {
        selectedField.style.display = 'block';
    }
}

// Set color from popular colors
function setColor(hex) {
    document.getElementById('input_type').value = 'hex';
    showColorFields();
    document.querySelector('input[name="hex_value"]').value = hex;
}

// Copy individual color code
function copyColorCode(elementId) {
    const element = document.getElementById(elementId);
    const text = element.value;
    
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.utils.copyToClipboard(text);
    } else {
        navigator.clipboard.writeText(text).then(() => {
            alert('<?php echo ($currentLang === 'tr') ? 'Renk kodu kopyalandı!' : 'Color code copied!'; ?>');
        });
    }
}

// Copy all color codes
function copyAllCodes() {
    <?php if ($result): ?>
    const allCodes = 'HEX: <?php echo $result['hex']; ?>\n' +
                    'RGB: <?php echo $result['css_rgb']; ?>\n' +
                    'HSL: <?php echo $result['css_hsl']; ?>\n' +
                    'HSV: hsv(<?php echo $result['hsv']['h']; ?>, <?php echo $result['hsv']['s']; ?>%, <?php echo $result['hsv']['v']; ?>%)';
    
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.utils.copyToClipboard(allCodes);
    } else {
        navigator.clipboard.writeText(allCodes).then(() => {
            alert('<?php echo ($currentLang === 'tr') ? 'Tüm renk kodları kopyalandı!' : 'All color codes copied!'; ?>');
        });
    }
    <?php endif; ?>
}

<?php if ($result): ?>
// Track successful conversion
if (typeof AllInToolbox !== 'undefined') {
    AllInToolbox.analytics.trackEvent('Tool', 'Convert', 'Color Converter');
}
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>