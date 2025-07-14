<?php
// pages/privacy.php - GİZLİLİK POLİTİKASI SAYFASI
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Sayfa meta bilgileri
$pageTitle = ($currentLang === 'tr' ? 'Gizlilik Politikası' : 'Privacy Policy') . ' | ' . SITE_NAME;
$pageDescription = ($currentLang === 'tr' ? 
    'AllInToolbox gizlilik politikası. Kişisel verilerinizin nasıl korunduğu ve işlendiği hakkında bilgi.' :
    'AllInToolbox privacy policy. Information about how your personal data is protected and processed.');
$pageKeywords = ($currentLang === 'tr' ? 
    'gizlilik politikası, kişisel veri koruması, çerez politikası, KVKK' :
    'privacy policy, personal data protection, cookie policy, GDPR');

include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">

        <!-- Breadcrumb -->
        <?php
        $breadcrumbItems = [
            ['title' => ($currentLang === 'tr') ? 'Ana Sayfa' : 'Home', 'url' => '/?lang=' . $currentLang],
            ['title' => ($currentLang === 'tr') ? 'Gizlilik Politikası' : 'Privacy Policy']
        ];
        echo generateBreadcrumb($breadcrumbItems);
        ?>

        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-4">
                <i class="fas fa-shield-alt text-primary me-3"></i>
                <?php echo ($currentLang === 'tr') ? 'Gizlilik Politikası' : 'Privacy Policy'; ?>
            </h1>
            <p class="lead text-muted">
                <?php echo ($currentLang === 'tr') ? 
                    'Kişisel verilerinizin nasıl toplandığı, kullanıldığı ve korunduğu hakkında bilgi' :
                    'Information about how your personal data is collected, used and protected'; ?>
            </p>
            <p class="text-muted">
                <i class="fas fa-calendar-alt me-2"></i>
                <?php echo ($currentLang === 'tr') ? 'Son güncelleme: 01 Ocak 2025' : 'Last updated: January 1, 2025'; ?>
            </p>
        </div>

        <!-- Content -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">

                        <?php if ($currentLang === 'tr'): ?>
                            <!-- Türkçe İçerik -->
                            <section class="mb-5">
                                <h2><i class="fas fa-info-circle text-primary me-2"></i>Giriş</h2>
                                <p>AllInToolbox olarak, kullanıcılarımızın gizliliğini ve kişisel verilerinin korunmasını en üst düzeyde önemsiyoruz. Bu Gizlilik Politikası, web sitemizi ziyaret ettiğinizde ve hizmetlerimizi kullandığınızda kişisel bilgilerinizin nasıl toplandığı, kullanıldığı, saklandığı ve korunduğu hakkında bilgi vermektedir.</p>
                                <p>Bu politika, 6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK) ve ilgili mevzuat uyarınca hazırlanmıştır.</p>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-database text-success me-2"></i>Toplanan Veriler</h2>
                                <h4>1. Otomatik Olarak Toplanan Veriler</h4>
                                <ul>
                                    <li><strong>Teknik Veriler:</strong> IP adresi, tarayıcı türü, işletim sistemi, ziyaret edilen sayfalar</li>
                                    <li><strong>Kullanım Verileri:</strong> Site kullanım istatistikleri, tıklama verileri, oturum süresi</li>
                                    <li><strong>Çerez Verileri:</strong> Kullanıcı tercihlerini hatırlamak için çerezler</li>
                                </ul>

                                <h4>2. Kullanıcı Tarafından Sağlanan Veriler</h4>
                                <ul>
                                    <li><strong>İletişim Bilgileri:</strong> İletişim formlarında paylaştığınız e-posta adresi ve mesajlar</li>
                                    <li><strong>Hesaplama Verileri:</strong> Araçlarımızda girdiğiniz veriler (sadece hesaplama için kullanılır, saklanmaz)</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-cogs text-info me-2"></i>Veri Kullanım Amaçları</h2>
                                <p>Toplanan veriler aşağıdaki amaçlarla kullanılmaktadır:</p>
                                <ul>
                                    <li>Web sitesinin işleyişini sağlamak ve iyileştirmek</li>
                                    <li>Kullanıcı deneyimini geliştirmek ve kişiselleştirmek</li>
                                    <li>Site güvenliğini sağlamak ve kötüye kullanımı önlemek</li>
                                    <li>İstatistiksel analizler yapmak ve site performansını ölçmek</li>
                                    <li>Yasal yükümlülükleri yerine getirmek</li>
                                    <li>Kullanıcı desteği sağlamak ve sorularınızı yanıtlamak</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-shield-alt text-warning me-2"></i>Veri Güvenliği</h2>
                                <h4>Güvenlik Önlemlerimiz:</h4>
                                <ul>
                                    <li><strong>SSL Şifreleme:</strong> Tüm veri transferleri SSL/TLS ile şifrelenir</li>
                                    <li><strong>Güvenli Sunucular:</strong> Verileriniz güvenli sunucularda barındırılır</li>
                                    <li><strong>Erişim Kontrolü:</strong> Verilere erişim sınırlıdır ve izlenir</li>
                                    <li><strong>Düzenli Güvenlik Testleri:</strong> Sistemlerimiz düzenli olarak güvenlik testlerinden geçer</li>
                                    <li><strong>Veri Minimizasyonu:</strong> Sadece gerekli veriler toplanır</li>
                                </ul>

                                <div class="alert alert-success">
                                    <strong><i class="fas fa-check-circle me-2"></i>Önemli Not:</strong>
                                    Hesaplama araçlarımızda girdiğiniz veriler (kilo, boy, yaş vb.) sadece tarayıcınızda işlenir ve sunucularımızda saklanmaz.
                                </div>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-cookie-bite text-danger me-2"></i>Çerez Politikası</h2>
                                <h4>Kullandığımız Çerez Türleri:</h4>
                                <ul>
                                    <li><strong>Zorunlu Çerezler:</strong> Sitenin temel işlevlerini sağlar</li>
                                    <li><strong>Performans Çerezleri:</strong> Site performansını ölçer ve iyileştirir</li>
                                    <li><strong>İşlevsel Çerezler:</strong> Kullanıcı tercihlerini hatırlar (dil seçimi gibi)</li>
                                    <li><strong>Analitik Çerezler:</strong> Google Analytics ve benzer hizmetler için</li>
                                </ul>
                                <p>Çerezleri tarayıcı ayarlarınızdan kontrol edebilir ve silebilirsiniz.</p>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-share-alt text-secondary me-2"></i>Veri Paylaşımı</h2>
                                <p>Kişisel verileriniz aşağıdaki durumlarda üçüncü taraflarla paylaşılabilir:</p>
                                <ul>
                                    <li><strong>Yasal Zorunluluk:</strong> Kanuni yükümlülükler gereği</li>
                                    <li><strong>Hizmet Sağlayıcılar:</strong> Google Analytics, hosting hizmetleri gibi teknik hizmet sağlayıcılar</li>
                                    <li><strong>Güvenlik:</strong> Güvenlik ihlallerini önlemek için</li>
                                </ul>
                                <p><strong>Ticari amaçlarla kişisel verileriniz satılmaz veya kiralanmaz.</strong></p>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-user-shield text-primary me-2"></i>Kullanıcı Hakları</h2>
                                <p>KVKK kapsamında aşağıdaki haklara sahipsiniz:</p>
                                <ul>
                                    <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
                                    <li>İşlenen kişisel verileriniz hakkında bilgi talep etme</li>
                                    <li>Kişisel verilerinizin işlenme amacını öğrenme</li>
                                    <li>Verilerin eksik veya yanlış işlenmiş olması halinde düzeltilmesini isteme</li>
                                    <li>Belirli şartlar altında verilerin silinmesini isteme</li>
                                    <li>Otomatik sistemler ile analiz edilme konusunda bilgi alma</li>
                                </ul>
                                <p>Bu haklarınızı kullanmak için bizimle iletişime geçebilirsiniz.</p>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-clock text-info me-2"></i>Veri Saklama Süreleri</h2>
                                <ul>
                                    <li><strong>Log Verileri:</strong> 6 ay süreyle saklanır</li>
                                    <li><strong>İletişim Verileri:</strong> 3 yıl süreyle saklanır</li>
                                    <li><strong>Analitik Veriler:</strong> Google Analytics politikası gereği</li>
                                    <li><strong>Çerez Verileri:</strong> Çerez türüne göre değişir (1 ay - 2 yıl)</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-child text-warning me-2"></i>Çocukların Gizliliği</h2>
                                <p>Web sitemiz 13 yaşın altındaki çocuklardan bilerek kişisel bilgi toplamaz. Eğer 13 yaşın altındaki bir çocuğun kişisel bilgilerini topladığımızı fark edersek, bu bilgileri derhal sileriz.</p>
                            </section>

                        <?php else: ?>
                            <!-- English Content -->
                            <section class="mb-5">
                                <h2><i class="fas fa-info-circle text-primary me-2"></i>Introduction</h2>
                                <p>At AllInToolbox, we highly value our users' privacy and the protection of their personal data. This Privacy Policy provides information about how personal information is collected, used, stored, and protected when you visit our website and use our services.</p>
                                <p>This policy has been prepared in accordance with applicable data protection laws including GDPR and other relevant legislation.</p>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-database text-success me-2"></i>Data Collection</h2>
                                <h4>1. Automatically Collected Data</h4>
                                <ul>
                                    <li><strong>Technical Data:</strong> IP address, browser type, operating system, pages visited</li>
                                    <li><strong>Usage Data:</strong> Site usage statistics, click data, session duration</li>
                                    <li><strong>Cookie Data:</strong> Cookies to remember user preferences</li>
                                </ul>

                                <h4>2. User-Provided Data</h4>
                                <ul>
                                    <li><strong>Contact Information:</strong> Email address and messages shared in contact forms</li>
                                    <li><strong>Calculation Data:</strong> Data entered in our tools (used only for calculations, not stored)</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-cogs text-info me-2"></i>Data Usage Purposes</h2>
                                <p>Collected data is used for the following purposes:</p>
                                <ul>
                                    <li>To provide and improve website functionality</li>
                                    <li>To enhance and personalize user experience</li>
                                    <li>To ensure site security and prevent abuse</li>
                                    <li>To conduct statistical analysis and measure site performance</li>
                                    <li>To comply with legal obligations</li>
                                    <li>To provide user support and answer your questions</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-shield-alt text-warning me-2"></i>Data Security</h2>
                                <h4>Our Security Measures:</h4>
                                <ul>
                                    <li><strong>SSL Encryption:</strong> All data transfers are encrypted with SSL/TLS</li>
                                    <li><strong>Secure Servers:</strong> Your data is hosted on secure servers</li>
                                    <li><strong>Access Control:</strong> Data access is limited and monitored</li>
                                    <li><strong>Regular Security Testing:</strong> Our systems undergo regular security testing</li>
                                    <li><strong>Data Minimization:</strong> Only necessary data is collected</li>
                                </ul>

                                <div class="alert alert-success">
                                    <strong><i class="fas fa-check-circle me-2"></i>Important Note:</strong>
                                    Data entered in our calculation tools (weight, height, age, etc.) is processed only in your browser and is not stored on our servers.
                                </div>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-cookie-bite text-danger me-2"></i>Cookie Policy</h2>
                                <h4>Types of Cookies We Use:</h4>
                                <ul>
                                    <li><strong>Essential Cookies:</strong> Provide basic website functionality</li>
                                    <li><strong>Performance Cookies:</strong> Measure and improve site performance</li>
                                    <li><strong>Functional Cookies:</strong> Remember user preferences (like language selection)</li>
                                    <li><strong>Analytics Cookies:</strong> For Google Analytics and similar services</li>
                                </ul>
                                <p>You can control and delete cookies through your browser settings.</p>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-share-alt text-secondary me-2"></i>Data Sharing</h2>
                                <p>Your personal data may be shared with third parties in the following situations:</p>
                                <ul>
                                    <li><strong>Legal Requirement:</strong> Due to legal obligations</li>
                                    <li><strong>Service Providers:</strong> Technical service providers like Google Analytics, hosting services</li>
                                    <li><strong>Security:</strong> To prevent security breaches</li>
                                </ul>
                                <p><strong>Your personal data is never sold or rented for commercial purposes.</strong></p>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-user-shield text-primary me-2"></i>User Rights</h2>
                                <p>Under data protection laws, you have the following rights:</p>
                                <ul>
                                    <li>To learn whether your personal data is being processed</li>
                                    <li>To request information about your processed personal data</li>
                                    <li>To learn the purpose of processing your personal data</li>
                                    <li>To request correction if data is processed incompletely or incorrectly</li>
                                    <li>To request deletion of data under certain conditions</li>
                                    <li>To obtain information about automated decision-making</li>
                                </ul>
                                <p>You can contact us to exercise these rights.</p>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-clock text-info me-2"></i>Data Retention Periods</h2>
                                <ul>
                                    <li><strong>Log Data:</strong> Stored for 6 months</li>
                                    <li><strong>Contact Data:</strong> Stored for 3 years</li>
                                    <li><strong>Analytics Data:</strong> According to Google Analytics policy</li>
                                    <li><strong>Cookie Data:</strong> Varies by cookie type (1 month - 2 years)</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-child text-warning me-2"></i>Children's Privacy</h2>
                                <p>Our website does not knowingly collect personal information from children under 13. If we become aware that we have collected personal information from a child under 13, we will immediately delete such information.</p>
                            </section>
                        <?php endif; ?>

                        <!-- Contact Section -->
                        <section class="mb-5">
                            <h2><i class="fas fa-envelope text-primary me-2"></i><?php echo ($currentLang === 'tr') ? 'İletişim' : 'Contact'; ?></h2>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-3">
                                        <?php echo ($currentLang === 'tr') ? 
                                            'Gizlilik politikamız hakkında sorularınız varsa veya haklarınızı kullanmak istiyorsanız bizimle iletişime geçebilirsiniz:' :
                                            'If you have questions about our privacy policy or want to exercise your rights, you can contact us:'; ?>
                                    </p>
                                    <ul class="list-unstyled mb-0">
                                        <li><i class="fas fa-envelope me-2"></i><strong>Email:</strong> privacy@allintoolbox.com</li>
                                        <li><i class="fas fa-globe me-2"></i><strong>Website:</strong> <a href="/pages/contact.php?lang=<?php echo $currentLang; ?>">
                                            <?php echo ($currentLang === 'tr') ? 'İletişim Formu' : 'Contact Form'; ?>
                                        </a></li>
                                        <li><i class="fas fa-clock me-2"></i><strong><?php echo ($currentLang === 'tr') ? 'Yanıt süresi:' : 'Response time:'; ?></strong> 
                                            <?php echo ($currentLang === 'tr') ? '72 saat içinde' : 'Within 72 hours'; ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </section>

                        <!-- Updates Section -->
                        <section>
                            <h2><i class="fas fa-sync text-secondary me-2"></i><?php echo ($currentLang === 'tr') ? 'Güncellemeler' : 'Updates'; ?></h2>
                            <p>
                                <?php echo ($currentLang === 'tr') ? 
                                    'Bu gizlilik politikası gerektiğinde güncellenebilir. Önemli değişiklikleri web sitemizdeki duyuru ile size bildireceğiz. Politikayı düzenli olarak gözden geçirmenizi öneririz.' :
                                    'This privacy policy may be updated when necessary. We will notify you of significant changes through announcements on our website. We recommend reviewing the policy regularly.'; ?>
                            </p>
                            <div class="alert alert-info">
                                <strong><?php echo ($currentLang === 'tr') ? 'Son güncelleme tarihi:' : 'Last update date:'; ?></strong> 01.01.2025
                            </div>
                        </section>

                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Track privacy policy page view
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Legal', 'View', 'Privacy Policy');
    }
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    
    // Add reading progress indicator
    const progressBar = document.createElement('div');
    progressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: var(--primary-color);
        z-index: 9999;
        transition: width 0.3s ease;
    `;
    document.body.appendChild(progressBar);
    
    window.addEventListener('scroll', function() {
        const scrolled = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
        progressBar.style.width = scrolled + '%';
    });
});
</script>

<?php include '../includes/footer.php'; ?>