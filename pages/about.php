<?php
// pages/about.php - HAKKIMIZDA SAYFASI
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Sayfa meta bilgileri
$pageTitle = ($currentLang === 'tr' ? 'Hakkımızda' : 'About Us') . ' | ' . SITE_NAME;
$pageDescription = ($currentLang === 'tr' ? 
    'AllInToolbox hakkında bilgi edinin. Ücretsiz online araçlarımızla amacımız ve vizyonumuz.' :
    'Learn about AllInToolbox. Our mission and vision with free online tools.');
$pageKeywords = ($currentLang === 'tr' ? 
    'hakkımızda, AllInToolbox, ücretsiz araçlar, online hesaplayıcı' :
    'about us, AllInToolbox, free tools, online calculator');

include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">

        <!-- Breadcrumb -->
        <?php
        $breadcrumbItems = [
            ['title' => ($currentLang === 'tr') ? 'Ana Sayfa' : 'Home', 'url' => '/?lang=' . $currentLang],
            ['title' => ($currentLang === 'tr') ? 'Hakkımızda' : 'About Us']
        ];
        echo generateBreadcrumb($breadcrumbItems);
        ?>

        <!-- Hero Section -->
        <div class="hero-section mb-5">
            <div class="text-center">
                <h1 class="display-4 fw-bold mb-4">
                    <i class="fas fa-info-circle me-3"></i>
                    <?php echo ($currentLang === 'tr') ? 'Hakkımızda' : 'About Us'; ?>
                </h1>
                <p class="lead">
                    <?php echo ($currentLang === 'tr') ? 
                        'Günlük hayatınızı kolaylaştıran ücretsiz online araçlar geliştiriyoruz.' :
                        'We develop free online tools that make your daily life easier.'; ?>
                </p>
            </div>
        </div>

        <!-- Mission & Vision -->
        <div class="row mb-5">
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-bullseye fa-2x text-white"></i>
                            </div>
                        </div>
                        <h3 class="text-center mb-4"><?php echo ($currentLang === 'tr') ? 'Misyonumuz' : 'Our Mission'; ?></h3>
                        <p class="text-muted">
                            <?php echo ($currentLang === 'tr') ? 
                                'İnsanların günlük hesaplamalarını ve dönüşümlerini kolaylaştırmak için basit, hızlı ve güvenilir online araçlar sunmak. Her araç kullanıcı dostu olmalı ve herkesçe erişilebilir olmalıdır.' :
                                'To provide simple, fast and reliable online tools to facilitate people\'s daily calculations and conversions. Every tool should be user-friendly and accessible to everyone.'; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="bg-success bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-eye fa-2x text-white"></i>
                            </div>
                        </div>
                        <h3 class="text-center mb-4"><?php echo ($currentLang === 'tr') ? 'Vizyonumuz' : 'Our Vision'; ?></h3>
                        <p class="text-muted">
                            <?php echo ($currentLang === 'tr') ? 
                                'Dünyanın en kapsamlı ve kullanıcı dostu ücretsiz online araç platformu olmak. Sürekli yenilik yaparak kullanıcılarımızın değişen ihtiyaçlarına yanıt vermek.' :
                                'To become the world\'s most comprehensive and user-friendly free online tool platform. To respond to our users\' changing needs through continuous innovation.'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Story Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h3 class="mb-4">
                            <i class="fas fa-history me-2 text-info"></i>
                            <?php echo ($currentLang === 'tr') ? 'Hikayemiz' : 'Our Story'; ?>
                        </h3>
                        <p class="lead">
                            <?php echo ($currentLang === 'tr') ? 
                                'AllInToolbox, 2025 yılında insanların günlük yaşamlarında ihtiyaç duydukları hesaplamaları kolaylaştırmak amacıyla kuruldu.' :
                                'AllInToolbox was founded in 2025 to simplify the calculations people need in their daily lives.'; ?>
                        </p>
                        <p>
                            <?php echo ($currentLang === 'tr') ? 
                                'Başlangıçta sadece birkaç temel hesaplayıcı ile yola çıkan projemiz, kullanıcı geri bildirimlerinin ışığında hızla genişledi. Bugün 5 farklı kategoride 10 profesyonel araç sunuyoruz.' :
                                'Our project started with just a few basic calculators and quickly expanded in light of user feedback. Today we offer 10 professional tools in 5 different categories.'; ?>
                        </p>
                        <p>
                            <?php echo ($currentLang === 'tr') ? 
                                'Her aracımız, güvenlik, kullanım kolaylığı ve doğruluk prensipleri gözetilerek geliştirilir. Verilerinizin güvenliği bizim için en önemli önceliktir.' :
                                'Each of our tools is developed with principles of security, ease of use and accuracy. The security of your data is our top priority.'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body p-5 text-white">
                        <h3 class="text-center mb-5"><?php echo ($currentLang === 'tr') ? 'Sayılarla AllInToolbox' : 'AllInToolbox in Numbers'; ?></h3>
                        <div class="row text-center">
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="mb-2">
                                    <i class="fas fa-tools fa-3x"></i>
                                </div>
                                <h2 class="fw-bold"><?php echo SITE_STATS['total_tools']; ?></h2>
                                <p><?php echo ($currentLang === 'tr') ? 'Ücretsiz Araç' : 'Free Tools'; ?></p>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="mb-2">
                                    <i class="fas fa-th-large fa-3x"></i>
                                </div>
                                <h2 class="fw-bold"><?php echo SITE_STATS['total_categories']; ?></h2>
                                <p><?php echo ($currentLang === 'tr') ? 'Farklı Kategori' : 'Different Categories'; ?></p>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="mb-2">
                                    <i class="fas fa-globe fa-3x"></i>
                                </div>
                                <h2 class="fw-bold">2</h2>
                                <p><?php echo ($currentLang === 'tr') ? 'Dil Desteği' : 'Language Support'; ?></p>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="mb-2">
                                    <i class="fas fa-shield-alt fa-3x"></i>
                                </div>
                                <h2 class="fw-bold">100%</h2>
                                <p><?php echo ($currentLang === 'tr') ? 'Güvenli & Ücretsiz' : 'Secure & Free'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Values Section -->
        <div class="row mb-5">
            <div class="col-12">
                <h3 class="text-center mb-5">
                    <i class="fas fa-heart me-2 text-danger"></i>
                    <?php echo ($currentLang === 'tr') ? 'Değerlerimiz' : 'Our Values'; ?>
                </h3>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="text-center">
                            <div class="mb-3 text-primary">
                                <i class="fas fa-hand-holding-heart fa-3x"></i>
                            </div>
                            <h5><?php echo ($currentLang === 'tr') ? 'Ücretsizlik' : 'Free Access'; ?></h5>
                            <p class="text-muted">
                                <?php echo ($currentLang === 'tr') ? 
                                    'Tüm araçlarımız herkesçe erişilebilir ve tamamen ücretsizdir.' :
                                    'All our tools are accessible to everyone and completely free.'; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="text-center">
                            <div class="mb-3 text-success">
                                <i class="fas fa-user-shield fa-3x"></i>
                            </div>
                            <h5><?php echo ($currentLang === 'tr') ? 'Gizlilik' : 'Privacy'; ?></h5>
                            <p class="text-muted">
                                <?php echo ($currentLang === 'tr') ? 
                                    'Verileriniz hiçbir zaman saklanmaz, tüm hesaplamalar tarayıcınızda yapılır.' :
                                    'Your data is never stored, all calculations are done in your browser.'; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="text-center">
                            <div class="mb-3 text-warning">
                                <i class="fas fa-rocket fa-3x"></i>
                            </div>
                            <h5><?php echo ($currentLang === 'tr') ? 'Hız' : 'Speed'; ?></h5>
                            <p class="text-muted">
                                <?php echo ($currentLang === 'tr') ? 
                                    'Araçlarımız hızlı yüklenir ve anında sonuç verir.' :
                                    'Our tools load quickly and give instant results.'; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="text-center">
                            <div class="mb-3 text-info">
                                <i class="fas fa-mobile-alt fa-3x"></i>
                            </div>
                            <h5><?php echo ($currentLang === 'tr') ? 'Erişilebilirlik' : 'Accessibility'; ?></h5>
                            <p class="text-muted">
                                <?php echo ($currentLang === 'tr') ? 
                                    'Tüm cihazlarda mükemmel çalışan responsive tasarım.' :
                                    'Responsive design that works perfectly on all devices.'; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="text-center">
                            <div class="mb-3 text-danger">
                                <i class="fas fa-check-circle fa-3x"></i>
                            </div>
                            <h5><?php echo ($currentLang === 'tr') ? 'Doğruluk' : 'Accuracy'; ?></h5>
                            <p class="text-muted">
                                <?php echo ($currentLang === 'tr') ? 
                                    'Tüm hesaplamalar test edilmiş ve doğrulanmış algoritmalardır.' :
                                    'All calculations are tested and verified algorithms.'; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="text-center">
                            <div class="mb-3 text-secondary">
                                <i class="fas fa-users fa-3x"></i>
                            </div>
                            <h5><?php echo ($currentLang === 'tr') ? 'Kullanıcı Odaklılık' : 'User Focus'; ?></h5>
                            <p class="text-muted">
                                <?php echo ($currentLang === 'tr') ? 
                                    'Her karar kullanıcı deneyimini iyileştirmek için alınır.' :
                                    'Every decision is made to improve the user experience.'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technology Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h3 class="mb-4">
                            <i class="fas fa-cogs me-2 text-primary"></i>
                            <?php echo ($currentLang === 'tr') ? 'Teknoloji & Güvenlik' : 'Technology & Security'; ?>
                        </h3>
                        <div class="row">
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Kullandığımız Teknolojiler:' : 'Technologies We Use:'; ?></h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Modern PHP 8+ Backend</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Responsive Bootstrap 5 Frontend</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Vanilla JavaScript (Framework-free)</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Mobile-First Design</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>PWA Ready Infrastructure</li>
                                </ul>
                            </div>
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Güvenlik Önlemlerimiz:' : 'Our Security Measures:'; ?></h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-shield-alt text-primary me-2"></i><?php echo ($currentLang === 'tr') ? 'Client-side hesaplama' : 'Client-side calculations'; ?></li>
                                    <li class="mb-2"><i class="fas fa-shield-alt text-primary me-2"></i><?php echo ($currentLang === 'tr') ? 'Veri saklamama politikası' : 'No data storage policy'; ?></li>
                                    <li class="mb-2"><i class="fas fa-shield-alt text-primary me-2"></i>HTTPS SSL Encryption</li>
                                    <li class="mb-2"><i class="fas fa-shield-alt text-primary me-2"></i><?php echo ($currentLang === 'tr') ? 'Güvenli kod standartları' : 'Secure coding standards'; ?></li>
                                    <li class="mb-2"><i class="fas fa-shield-alt text-primary me-2"></i><?php echo ($currentLang === 'tr') ? 'Düzenli güvenlik testleri' : 'Regular security testing'; ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact CTA -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-5">
                        <h3 class="mb-4">
                            <?php echo ($currentLang === 'tr') ? 'Bizimle İletişime Geçin' : 'Get in Touch with Us'; ?>
                        </h3>
                        <p class="lead mb-4">
                            <?php echo ($currentLang === 'tr') ? 
                                'Sorularınız, önerileriniz veya yeni araç fikirleriniz mi var? Sizden haber almaktan memnuniyet duyarız!' :
                                'Do you have questions, suggestions or new tool ideas? We would love to hear from you!'; ?>
                        </p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="/pages/contact.php?lang=<?php echo $currentLang; ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-envelope me-2"></i>
                                <?php echo ($currentLang === 'tr') ? 'İletişim' : 'Contact Us'; ?>
                            </a>
                            <a href="/?lang=<?php echo $currentLang; ?>" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-tools me-2"></i>
                                <?php echo ($currentLang === 'tr') ? 'Araçları Keşfet' : 'Explore Tools'; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Track about page view
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Page', 'View', 'About');
    }
    
    // Add scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Animate cards
    document.querySelectorAll('.card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        card.style.transitionDelay = (index * 0.1) + 's';
        
        observer.observe(card);
    });
});
</script>

<?php include '../includes/footer.php'; ?>