<?php
// pages/category.php - KATEGORİ SAYFASI
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Kategori ID'sini al
$categoryId = $_GET['category'] ?? '';

// Geçerli kategori kontrolü
if (!isset(TOOL_CATEGORIES[$categoryId])) {
    header("Location: /?lang={$currentLang}");
    exit();
}

$categoryInfo = TOOL_CATEGORIES[$categoryId];
$categoryName = $categoryInfo[$currentLang];

// Kategoriye ait araçları al
$categoryTools = getToolsByCategory($categoryId, $currentLang);

// Sayfa meta bilgileri
$pageTitle = $categoryName . ' ' . ($currentLang === 'tr' ? 'Araçları' : 'Tools') . ' | ' . SITE_NAME;
$pageDescription = ($currentLang === 'tr' ? 
    $categoryName . ' kategorisindeki ücretsiz online araçlar. ' . count($categoryTools) . ' farklı araç mevcut.' :
    'Free online tools in ' . $categoryName . ' category. ' . count($categoryTools) . ' different tools available.');
$pageKeywords = $categoryName . ', ' . ($currentLang === 'tr' ? 'araçlar, hesaplayıcı, çevirici' : 'tools, calculator, converter');

// Kategori ikonları ve renkler
$categoryStyles = [
    'finance' => [
        'icon' => 'fas fa-chart-line',
        'color' => 'success',
        'gradient' => 'linear-gradient(135deg, #28a745 0%, #20c997 100%)',
        'description' => [
            'tr' => 'Finansal hesaplamalar ve para birimi çevirme araçları',
            'en' => 'Financial calculations and currency conversion tools'
        ]
    ],
    'health' => [
        'icon' => 'fas fa-heartbeat',
        'color' => 'danger',
        'gradient' => 'linear-gradient(135deg, #dc3545 0%, #fd7e14 100%)',
        'description' => [
            'tr' => 'Sağlık ve fitness hesaplama araçları',
            'en' => 'Health and fitness calculation tools'
        ]
    ],
    'web' => [
        'icon' => 'fas fa-code',
        'color' => 'info',
        'gradient' => 'linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%)',
        'description' => [
            'tr' => 'Web geliştirme ve güvenlik araçları',
            'en' => 'Web development and security tools'
        ]
    ],
    'converter' => [
        'icon' => 'fas fa-exchange-alt',
        'color' => 'warning',
        'gradient' => 'linear-gradient(135deg, #ffc107 0%, #fd7e14 100%)',
        'description' => [
            'tr' => 'Ölçü birimi ve format çevirme araçları',
            'en' => 'Unit and format conversion tools'
        ]
    ],
    'utility' => [
        'icon' => 'fas fa-tools',
        'color' => 'secondary',
        'gradient' => 'linear-gradient(135deg, #6c757d 0%, #495057 100%)',
        'description' => [
            'tr' => 'Günlük hayatı kolaylaştıran pratik araçlar',
            'en' => 'Practical tools that make daily life easier'
        ]
    ]
];

$currentStyle = $categoryStyles[$categoryId];

include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">

        <!-- Breadcrumb -->
        <?php
        $breadcrumbItems = [
            ['title' => ($currentLang === 'tr') ? 'Ana Sayfa' : 'Home', 'url' => '/?lang=' . $currentLang],
            ['title' => $categoryName ]
        ];
        echo generateBreadcrumb($breadcrumbItems);
        ?>

        <!-- Category Header -->
        <div class="category-header mb-5" style="background: <?php echo $currentStyle['gradient']; ?>; border-radius: var(--border-radius); padding: 3rem 2rem; color: white; text-align: center; position: relative; overflow: hidden;">
            <!-- Background Pattern -->
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1; background-image: url('data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 100 100&quot;><defs><pattern id=&quot;grain&quot; width=&quot;100&quot; height=&quot;100&quot; patternUnits=&quot;userSpaceOnUse&quot;><circle cx=&quot;25&quot; cy=&quot;25&quot; r=&quot;1&quot; fill=&quot;white&quot;/><circle cx=&quot;75&quot; cy=&quot;75&quot; r=&quot;1&quot; fill=&quot;white&quot;/></pattern></defs><rect width=&quot;100&quot; height=&quot;100&quot; fill=&quot;url(%23grain)&quot;/></svg>');"></div>
            
            <div style="position: relative; z-index: 1;">
                <div class="mb-3">
                    <i class="<?php echo $currentStyle['icon']; ?> fa-4x"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3"><?php echo $categoryName; ?></h1>
                <p class="lead mb-4"><?php echo $currentStyle['description'][$currentLang]; ?></p>
                <div class="d-flex justify-content-center align-items-center gap-4 flex-wrap">
                    <div class="text-center">
                        <h3 class="mb-0"><?php echo count($categoryTools); ?></h3>
                        <small><?php echo ($currentLang === 'tr') ? 'Araç' : 'Tools'; ?></small>
                    </div>
                    <div class="text-center">
                        <h3 class="mb-0">100%</h3>
                        <small><?php echo ($currentLang === 'tr') ? 'Ücretsiz' : 'Free'; ?></small>
                    </div>
                    <div class="text-center">
                        <h3 class="mb-0"><i class="fas fa-mobile-alt"></i></h3>
                        <small><?php echo ($currentLang === 'tr') ? 'Mobil Uyumlu' : 'Mobile Friendly'; ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tools Grid -->
        <div class="row g-4 mb-5">
            <?php if (!empty($categoryTools)): ?>
                <?php foreach ($categoryTools as $toolId => $toolInfo): ?>
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="card h-100 tool-card shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <div class="tool-icon mb-3" style="font-size: 3rem; color: var(--bs-<?php echo $currentStyle['color']; ?>);">
                                        <?php
                                        // Tool-specific icons
                                        $toolIcons = [
                                            'bmi-calculator' => 'fas fa-weight',
                                            'calorie-calculator' => 'fas fa-apple-alt',
                                            'loan-calculator' => 'fas fa-calculator',
                                            'currency-converter' => 'fas fa-exchange-alt',
                                            'qr-code-generator' => 'fas fa-qrcode',
                                            'password-generator' => 'fas fa-key',
                                            'unit-converter' => 'fas fa-ruler',
                                            'color-converter' => 'fas fa-palette',
                                            'text-analyzer' => 'fas fa-file-alt',
                                            'age-calculator' => 'fas fa-birthday-cake'
                                        ];
                                        $icon = $toolIcons[$toolId] ?? 'fas fa-tools';
                                        ?>
                                        <i class="<?php echo $icon; ?>"></i>
                                    </div>
                                </div>
                                
                                <h5 class="card-title text-center mb-3"><?php echo $toolInfo['name']; ?></h5>
                                <p class="card-text text-muted text-center mb-4"><?php echo $toolInfo['description']; ?></p>
                                
                                <div class="d-grid">
                                    <a href="<?php echo $toolInfo['url']; ?>" 
                                       class="btn btn-<?php echo $currentStyle['color']; ?> btn-lg">
                                        <i class="fas fa-arrow-right me-2"></i>
                                        <?php echo ($currentLang === 'tr') ? 'Kullan' : 'Use Tool'; ?>
                                    </a>
                                </div>
                                
                                <!-- Tool Features -->
                                <div class="mt-3">
                                    <div class="d-flex justify-content-center gap-3 text-muted small">
                                        <span><i class="fas fa-bolt me-1"></i><?php echo ($currentLang === 'tr') ? 'Hızlı' : 'Fast'; ?></span>
                                        <span><i class="fas fa-shield-alt me-1"></i><?php echo ($currentLang === 'tr') ? 'Güvenli' : 'Secure'; ?></span>
                                        <span><i class="fas fa-mobile-alt me-1"></i><?php echo ($currentLang === 'tr') ? 'Mobil' : 'Mobile'; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- No Tools Message -->
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-tools fa-4x text-muted mb-3"></i>
                        <h4><?php echo ($currentLang === 'tr') ? 'Henüz araç bulunmuyor' : 'No tools available yet'; ?></h4>
                        <p class="text-muted"><?php echo ($currentLang === 'tr') ? 'Bu kategoride yakında yeni araçlar eklenecek.' : 'New tools will be added to this category soon.'; ?></p>
                        <a href="/?lang=<?php echo $currentLang; ?>" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i><?php echo ($currentLang === 'tr') ? 'Ana Sayfaya Dön' : 'Back to Home'; ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Ad Space -->
        <?php echo renderAdSpace('content', 'large'); ?>

        <!-- Other Categories -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">
                    <i class="fas fa-th-large me-2"></i>
                    <?php echo ($currentLang === 'tr') ? 'Diğer Kategoriler' : 'Other Categories'; ?>
                </h3>
                <div class="row g-3">
                    <?php foreach (TOOL_CATEGORIES as $otherCategoryId => $otherCategoryNames): ?>
                        <?php if ($otherCategoryId !== $categoryId): ?>
                            <?php 
                            $otherStyle = $categoryStyles[$otherCategoryId];
                            $otherTools = getToolsByCategory($otherCategoryId, $currentLang);
                            ?>
                            <div class="col-lg-3 col-md-6 col-12">
                                <a href="/pages/category.php?category=<?php echo $otherCategoryId; ?>&lang=<?php echo $currentLang; ?>" 
                                   class="text-decoration-none">
                                    <div class="card h-100 category-preview-card border-0 shadow-sm">
                                        <div class="card-body text-center p-4">
                                            <div class="mb-3" style="color: var(--bs-<?php echo $otherStyle['color']; ?>);">
                                                <i class="<?php echo $otherStyle['icon']; ?> fa-3x"></i>
                                            </div>
                                            <h6 class="card-title"><?php echo $otherCategoryNames[$currentLang]; ?></h6>
                                            <p class="card-text small text-muted mb-3">
                                                <?php echo count($otherTools); ?> 
                                                <?php echo ($currentLang === 'tr') ? 'araç mevcut' : 'tools available'; ?>
                                            </p>
                                            <div class="btn btn-outline-<?php echo $otherStyle['color']; ?> btn-sm">
                                                <?php echo ($currentLang === 'tr') ? 'Keşfet' : 'Explore'; ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Category Features -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="mb-4">
                            <i class="<?php echo $currentStyle['icon']; ?> me-2 text-<?php echo $currentStyle['color']; ?>"></i>
                            <?php echo ($currentLang === 'tr') ? 'Neden ' . $categoryName . ' Araçlarımızı Seçmelisiniz?' : 'Why Choose Our ' . $categoryName . ' Tools?'; ?>
                        </h4>
                        
                        <div class="row g-4">
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center">
                                    <div class="mb-3 text-success">
                                        <i class="fas fa-rocket fa-2x"></i>
                                    </div>
                                    <h6><?php echo ($currentLang === 'tr') ? 'Hızlı Sonuçlar' : 'Fast Results'; ?></h6>
                                    <p class="small text-muted"><?php echo ($currentLang === 'tr') ? 'Saniyeler içinde sonuç alın' : 'Get results in seconds'; ?></p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center">
                                    <div class="mb-3 text-primary">
                                        <i class="fas fa-shield-alt fa-2x"></i>
                                    </div>
                                    <h6><?php echo ($currentLang === 'tr') ? '100% Güvenli' : '100% Secure'; ?></h6>
                                    <p class="small text-muted"><?php echo ($currentLang === 'tr') ? 'Verileriniz güvende' : 'Your data is safe'; ?></p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center">
                                    <div class="mb-3 text-info">
                                        <i class="fas fa-mobile-alt fa-2x"></i>
                                    </div>
                                    <h6><?php echo ($currentLang === 'tr') ? 'Mobil Uyumlu' : 'Mobile Friendly'; ?></h6>
                                    <p class="small text-muted"><?php echo ($currentLang === 'tr') ? 'Her cihazda çalışır' : 'Works on any device'; ?></p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center">
                                    <div class="mb-3 text-warning">
                                        <i class="fas fa-heart fa-2x"></i>
                                    </div>
                                    <h6><?php echo ($currentLang === 'tr') ? 'Tamamen Ücretsiz' : 'Completely Free'; ?></h6>
                                    <p class="small text-muted"><?php echo ($currentLang === 'tr') ? 'Kayıt gerekmez' : 'No registration required'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<style>
.category-header {
    background-attachment: fixed;
}

.tool-card {
    transition: all 0.3s ease;
    border-radius: 15px;
}

.tool-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}

.tool-icon {
    transition: all 0.3s ease;
}

.tool-card:hover .tool-icon {
    transform: scale(1.1) rotate(5deg);
}

.category-preview-card {
    transition: all 0.3s ease;
    border-radius: 15px;
}

.category-preview-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

@media (max-width: 768px) {
    .category-header {
        padding: 2rem 1rem !important;
    }
    
    .category-header .display-4 {
        font-size: 2rem !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Track category page view
    const categoryName = '<?php echo $categoryName; ?>';
    const toolCount = <?php echo count($categoryTools); ?>;
    
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Category', 'View', categoryName);
        AllInToolbox.analytics.trackEvent('Category', 'ToolCount', categoryName, toolCount);
    }
    
    // Add fade-in animation to tool cards
    const cards = document.querySelectorAll('.tool-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Track tool clicks from category page
    document.querySelectorAll('.tool-card a').forEach(link => {
        link.addEventListener('click', function() {
            const toolName = this.closest('.tool-card').querySelector('.card-title').textContent;
            if (typeof AllInToolbox !== 'undefined') {
                AllInToolbox.analytics.trackEvent('Category', 'ToolClick', toolName);
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>