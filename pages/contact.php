<!-- Working Hours -->
                <div class="card border-<?php
// pages/contact.php - İLETİŞİM SAYFASI
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Sayfa meta bilgileri
$pageTitle = ($currentLang === 'tr' ? 'İletişim' : 'Contact Us') . ' | ' . SITE_NAME;
$pageDescription = ($currentLang === 'tr' ? 
    'AllInToolbox ile iletişime geçin. Sorularınız, önerileriniz ve geri bildirimleriniz için bize ulaşın.' :
    'Contact AllInToolbox. Reach us for your questions, suggestions and feedback.');
$pageKeywords = ($currentLang === 'tr' ? 
    'iletişim, destek, geri bildirim, öneri, soru, yardım' :
    'contact, support, feedback, suggestion, question, help');


include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">

        <!-- Breadcrumb -->
        <?php
        $breadcrumbItems = [
            ['title' => ($currentLang === 'tr') ? 'Ana Sayfa' : 'Home', 'url' => '/?lang=' . $currentLang],
            ['title' => ($currentLang === 'tr') ? 'İletişim' : 'Contact Us']
        ];
        echo generateBreadcrumb($breadcrumbItems);
        ?>

        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-4">
                <i class="fas fa-envelope text-primary me-3 "></i>
                <?php echo ($currentLang === 'tr') ? 'İletişim' : 'Contact Us'; ?>
            </h1>
            <p class="lead text-muted">
                <?php echo ($currentLang === 'tr') ? 
                    'Sorularınız, önerileriniz veya geri bildirimleriniz için bizimle iletişime geçin' :
                    'Contact us for your questions, suggestions or feedback'; ?>
            </p>
        </div>


        <div class="row">
            <!-- Main Contact Info -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h3 class="mb-4">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <?php echo ($currentLang === 'tr') ? 'Bizimle İletişime Geçin' : 'Get in Touch with Us'; ?>
                        </h3>

                        <div class="alert alert-info border-0 mb-4">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle fa-2x text-info me-3 mt-1"></i>
                                <div>
                                    <h5 class="alert-heading">
                                        <?php echo ($currentLang === 'tr') ? 'E-posta ile İletişim' : 'Contact via Email'; ?>
                                    </h5>
                                    <p class="mb-0">
                                        <?php echo ($currentLang === 'tr') ? 
                                            'Sorularınız, önerileriniz veya geri bildirimleriniz için aşağıdaki e-posta adreslerinden bize ulaşabilirsiniz. Mümkün olan en kısa sürede size dönüş yapacağız.' :
                                            'You can reach us at the email addresses below for your questions, suggestions or feedback. We will get back to you as soon as possible.'; ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Methods Grid -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="contact-card h-100 p-4 border rounded">
                                    <div class="text-center mb-3">
                                        <div class="contact-icon bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-envelope fa-2x"></i>
                                        </div>
                                    </div>
                                    <h5 class="text-center mb-3"><?php echo ($currentLang === 'tr') ? 'Genel İletişim' : 'General Contact'; ?></h5>
                                    <div class="text-center">
                                        <a href="mailto:contact@allintoolbox.com" class="h6 text-decoration-none">
                                            contact@allintoolbox.com
                                        </a>
                                        <p class="text-muted small mt-2">
                                            <?php echo ($currentLang === 'tr') ? 
                                                'Genel sorular, öneri ve geri bildirimler için' :
                                                'For general questions, suggestions and feedback'; ?>
                                        </p>
                                        <span class="badge bg-success">
                                            <?php echo ($currentLang === 'tr') ? '24 saat içinde yanıt' : 'Response within 24 hours'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="contact-card h-100 p-4 border rounded">
                                    <div class="text-center mb-3">
                                        <div class="contact-icon bg-danger bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-bug fa-2x"></i>
                                        </div>
                                    </div>
                                    <h5 class="text-center mb-3"><?php echo ($currentLang === 'tr') ? 'Hata Bildirimi' : 'Bug Reports'; ?></h5>
                                    <div class="text-center">
                                        <a href="mailto:bugs@allintoolbox.com" class="h6 text-decoration-none">
                                            bugs@allintoolbox.com
                                        </a>
                                        <p class="text-muted small mt-2">
                                            <?php echo ($currentLang === 'tr') ? 
                                                'Araçlarda karşılaştığınız hataları bildirin' :
                                                'Report bugs you encounter in our tools'; ?>
                                        </p>
                                        <span class="badge bg-warning">
                                            <?php echo ($currentLang === 'tr') ? '12 saat içinde yanıt' : 'Response within 12 hours'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="contact-card h-100 p-4 border rounded">
                                    <div class="text-center mb-3">
                                        <div class="contact-icon bg-info bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-lightbulb fa-2x"></i>
                                        </div>
                                    </div>
                                    <h5 class="text-center mb-3"><?php echo ($currentLang === 'tr') ? 'Özellik Önerisi' : 'Feature Requests'; ?></h5>
                                    <div class="text-center">
                                        <a href="mailto:features@allintoolbox.com" class="h6 text-decoration-none">
                                            features@allintoolbox.com
                                        </a>
                                        <p class="text-muted small mt-2">
                                            <?php echo ($currentLang === 'tr') ? 
                                                'Yeni araç ve özellik önerilerinizi paylaşın' :
                                                'Share your new tool and feature suggestions'; ?>
                                        </p>
                                        <span class="badge bg-info">
                                            <?php echo ($currentLang === 'tr') ? '48 saat içinde yanıt' : 'Response within 48 hours'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="contact-card h-100 p-4 border rounded">
                                    <div class="text-center mb-3">
                                        <div class="contact-icon bg-warning bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-handshake fa-2x"></i>
                                        </div>
                                    </div>
                                    <h5 class="text-center mb-3"><?php echo ($currentLang === 'tr') ? 'İş Ortaklığı' : 'Business Partnership'; ?></h5>
                                    <div class="text-center">
                                        <a href="mailto:business@allintoolbox.com" class="h6 text-decoration-none">
                                            business@allintoolbox.com
                                        </a>
                                        <p class="text-muted small mt-2">
                                            <?php echo ($currentLang === 'tr') ? 
                                                'İş birlikleri ve ortaklık teklifleri için' :
                                                'For collaborations and partnership proposals'; ?>
                                        </p>
                                        <span class="badge bg-warning">
                                            <?php echo ($currentLang === 'tr') ? '3 gün içinde yanıt' : 'Response within 3 days'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Email Guidelines -->
                        <div class="mt-5">
                            <h4 class="mb-3">
                                <i class="fas fa-paper-plane text-success me-2"></i>
                                <?php echo ($currentLang === 'tr') ? 'E-posta Gönderme Rehberi' : 'Email Sending Guide'; ?>
                            </h4>
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <p class="mb-3">
                                        <?php echo ($currentLang === 'tr') ? 
                                            'Daha hızlı yanıt alabilmek için e-postanızda aşağıdaki bilgileri belirtmenizi rica ederiz:' :
                                            'To get a faster response, please include the following information in your email:'; ?>
                                    </p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    <?php echo ($currentLang === 'tr') ? 'Konu başlığını net belirtin' : 'Clearly state the subject'; ?>
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    <?php echo ($currentLang === 'tr') ? 'Sorununuzu detaylı açıklayın' : 'Describe your issue in detail'; ?>
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    <?php echo ($currentLang === 'tr') ? 'Hangi aracı kullandığınızı belirtin' : 'Specify which tool you were using'; ?>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    <?php echo ($currentLang === 'tr') ? 'Tarayıcı bilgilerinizi ekleyin' : 'Include your browser information'; ?>
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    <?php echo ($currentLang === 'tr') ? 'Ekran görüntüsü ekleyebilirsiniz' : 'You can add screenshots'; ?>
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    <?php echo ($currentLang === 'tr') ? 'İletişim dilinizi belirtin' : 'Specify your preferred language'; ?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="col-lg-4">
                <!-- Response Time -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4 text-center">
                        <h5 class="mb-3">
                            <i class="fas fa-clock text-primary me-2"></i>
                            <?php echo ($currentLang === 'tr') ? 'Yanıt Süreleri' : 'Response Times'; ?>
                        </h5>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="mb-3">
                                    <div class="h2 text-success mb-1">12h</div>
                                    <small class="text-muted"><?php echo ($currentLang === 'tr') ? 'Hata Bildirimi' : 'Bug Reports'; ?></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <div class="h2 text-info mb-1">24h</div>
                                    <small class="text-muted"><?php echo ($currentLang === 'tr') ? 'Genel Sorular' : 'General Questions'; ?></small>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">
                            <?php echo ($currentLang === 'tr') ? 
                                'İş günleri içinde yanıt vermeye çalışıyoruz' :
                                'We try to respond within business days'; ?>
                        </small>
                    </div>
                </div>

                <!-- Working Hours -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3">
                            <i class="fas fa-business-time text-success me-2"></i>
                            <?php echo ($currentLang === 'tr') ? 'Çalışma Saatleri' : 'Working Hours'; ?>
                        </h5>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span><?php echo ($currentLang === 'tr') ? 'Pazartesi - Cuma' : 'Monday - Friday'; ?></span>
                                <span class="text-success">09:00 - 18:00</span>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span><?php echo ($currentLang === 'tr') ? 'Cumartesi' : 'Saturday'; ?></span>
                                <span class="text-warning">10:00 - 15:00</span>
                            </div>
                        </div>
                        <div class="mb-0">
                            <div class="d-flex justify-content-between">
                                <span><?php echo ($currentLang === 'tr') ? 'Pazar' : 'Sunday'; ?></span>
                                <span class="text-danger"><?php echo ($currentLang === 'tr') ? 'Kapalı' : 'Closed'; ?></span>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-globe me-1"></i>
                            <?php echo ($currentLang === 'tr') ? 'Türkiye saati (GMT+3)' : 'Turkey time (GMT+3)'; ?>
                        </small>
                    </div>
                </div>

                <!-- FAQ -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-4">
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            <?php echo ($currentLang === 'tr') ? 'Sık Sorulan Sorular' : 'Frequently Asked Questions'; ?>
                        </h5>

                        <div class="accordion accordion-flush" id="faqAccordion">
                            <!-- FAQ 1 -->
                            <div class="accordion-item">
                                <h6 class="accordion-header" id="faq1">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                        <?php echo ($currentLang === 'tr') ? 'Ne kadar sürede yanıt alırım?' : 'How long does it take to get a response?'; ?>
                                    </button>
                                </h6>
                                <div id="collapse1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <?php echo ($currentLang === 'tr') ? 
                                            'Hata bildirimleri için 12 saat, genel sorular için 24 saat içinde yanıt vermeye çalışıyoruz.' :
                                            'We try to respond within 12 hours for bug reports and 24 hours for general questions.'; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ 2 -->
                            <div class="accordion-item">
                                <h6 class="accordion-header" id="faq2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                        <?php echo ($currentLang === 'tr') ? 'Hangi dillerde destek veriyorsunuz?' : 'What languages do you support?'; ?>
                                    </button>
                                </h6>
                                <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <?php echo ($currentLang === 'tr') ? 
                                            'Şu anda Türkçe ve İngilizce dillerinde destek veriyoruz.' :
                                            'We currently provide support in Turkish and English.'; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ 3 -->
                            <div class="accordion-item">
                                <h6 class="accordion-header" id="faq3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                        <?php echo ($currentLang === 'tr') ? 'Yeni araç talebinde bulunabilir miyim?' : 'Can I request new tools?'; ?>
                                    </button>
                                </h6>
                                <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <?php echo ($currentLang === 'tr') ? 
                                            'Elbette! features@allintoolbox.com adresine önerilerinizi gönderebilirsiniz.' :
                                            'Absolutely! You can send your suggestions to features@allintoolbox.com.'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media & Links -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4">
                            <i class="fas fa-share-alt me-2"></i>
                            <?php echo ($currentLang === 'tr') ? 'Bizi Takip Edin' : 'Follow Us'; ?>
                        </h4>
                        <div class="row text-center">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card border-0 h-100">
                                    <div class="card-body">
                                        <i class="fab fa-github fa-3x text-dark mb-3"></i>
                                        <h6>GitHub</h6>
                                        <p class="small text-muted"><?php echo ($currentLang === 'tr') ? 'Açık kaynak projeleri' : 'Open source projects'; ?></p>
                                        <a href="mailto:contact@allintoolbox.com?subject=GitHub%20Partnership" class="btn btn-outline-dark btn-sm">
                                            <?php echo ($currentLang === 'tr') ? 'İletişim' : 'Contact'; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card border-0 h-100">
                                    <div class="card-body">
                                        <i class="fab fa-twitter fa-3x text-info mb-3"></i>
                                        <h6>Twitter</h6>
                                        <p class="small text-muted"><?php echo ($currentLang === 'tr') ? 'Güncel haberler' : 'Latest news'; ?></p>
                                        <a href="mailto:contact@allintoolbox.com?subject=Social%20Media" class="btn btn-outline-info btn-sm">
                                            <?php echo ($currentLang === 'tr') ? 'Bilgi Al' : 'Get Info'; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card border-0 h-100">
                                    <div class="card-body">
                                        <i class="fab fa-linkedin fa-3x text-primary mb-3"></i>
                                        <h6>LinkedIn</h6>
                                        <p class="small text-muted"><?php echo ($currentLang === 'tr') ? 'Profesyonel ağ' : 'Professional network'; ?></p>
                                        <a href="mailto:business@allintoolbox.com?subject=LinkedIn%20Connection" class="btn btn-outline-primary btn-sm">
                                            <?php echo ($currentLang === 'tr') ? 'İş Birliği' : 'Collaborate'; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card border-0 h-100">
                                    <div class="card-body">
                                        <i class="fas fa-rss fa-3x text-warning mb-3"></i>
                                        <h6><?php echo ($currentLang === 'tr') ? 'Güncellemeler' : 'Updates'; ?></h6>
                                        <p class="small text-muted"><?php echo ($currentLang === 'tr') ? 'Yeni araçlar ve özellikler' : 'New tools and features'; ?></p>
                                        <a href="mailto:contact@allintoolbox.com?subject=Newsletter%20Subscription" class="btn btn-outline-warning btn-sm">
                                            <?php echo ($currentLang === 'tr') ? 'Abone Ol' : 'Subscribe'; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                <?php echo ($currentLang === 'tr') ? 
                                    'Sosyal medya hesaplarımız yakında aktif olacak. Şimdilik e-posta ile iletişime geçebilirsiniz.' :
                                    'Our social media accounts will be active soon. For now, you can contact us via email.'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<style>
.contact-card {
    transition: all 0.3s ease;
    border-color: #e9ecef !important;
}

.contact-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    border-color: #007bff !important;
}

.contact-icon {
    transition: all 0.3s ease;
}

.contact-card:hover .contact-icon {
    transform: scale(1.1);
}

.accordion-button {
    background: transparent;
    border: none;
    font-weight: 500;
}

.accordion-button:focus {
    box-shadow: none;
    border: none;
}

.accordion-button:not(.collapsed) {
    background: transparent;
    color: var(--bs-primary);
}

@media (max-width: 768px) {
    .contact-card {
        margin-bottom: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Track contact page view
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Page', 'View', 'Contact');
    }
    
    // Track email clicks
    document.querySelectorAll('a[href^="mailto:"]').forEach(link => {
        link.addEventListener('click', function() {
            const emailType = this.href.includes('bugs@') ? 'Bug Report' : 
                             this.href.includes('features@') ? 'Feature Request' :
                             this.href.includes('business@') ? 'Business' : 'General';
            
            if (typeof AllInToolbox !== 'undefined') {
                AllInToolbox.analytics.trackEvent('Contact', 'Email Click', emailType);
            }
        });
    });
    
    // Copy email addresses on click
    document.querySelectorAll('.contact-card a[href^="mailto:"]').forEach(link => {
        const emailSpan = link.querySelector('.h6');
        if (emailSpan) {
            emailSpan.style.cursor = 'pointer';
            emailSpan.title = document.documentElement.lang === 'tr' ? 'E-posta adresini kopyalamak için tıklayın' : 'Click to copy email address';
            
            emailSpan.addEventListener('click', function(e) {
                e.preventDefault();
                const email = this.textContent.trim();
                
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(email).then(() => {
                        const message = document.documentElement.lang === 'tr' ? 'E-posta adresi kopyalandı!' : 'Email address copied!';
                        showNotification(message, 'success');
                    });
                } else {
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = email;
                    document.body.appendChild(textArea);
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        const message = document.documentElement.lang === 'tr' ? 'E-posta adresi kopyalandı!' : 'Email address copied!';
                        showNotification(message, 'success');
                    } catch (err) {
                        console.error('Copy failed');
                    }
                    document.body.removeChild(textArea);
                }
            });
        }
    });
    
    // Simple notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
        notification.innerHTML = `
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            ${message}
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 3000);
    }
    
    // Add hover animations to contact cards
    document.querySelectorAll('.contact-card').forEach((card, index) => {
        card.style.animationDelay = (index * 0.1) + 's';
        
        card.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.contact-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.contact-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });
    
    // Add current time display for working hours
    const updateClock = () => {
        const now = new Date();
        const turkeyTime = new Date(now.toLocaleString("en-US", {timeZone: "Europe/Istanbul"}));
        const timeString = turkeyTime.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
        
        const clockElement = document.querySelector('.working-hours-clock');
        if (clockElement) {
            clockElement.textContent = timeString;
        }
    };
    
    // Add live clock if working hours section exists
    const workingHoursCard = document.querySelector('.fa-business-time');
    if (workingHoursCard) {
        const clockDiv = document.createElement('div');
        clockDiv.className = 'text-center mt-2 text-primary working-hours-clock';
        clockDiv.style.fontWeight = 'bold';
        workingHoursCard.closest('.card-body').appendChild(clockDiv);
        
        updateClock();
        setInterval(updateClock, 1000);
    }
});
</script>

<?php include '../includes/footer.php'; ?>