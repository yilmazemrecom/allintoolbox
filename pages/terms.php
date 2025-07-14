<?php
// pages/terms.php - KULLANIM ŞARTLARI SAYFASI
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Sayfa meta bilgileri
$pageTitle = ($currentLang === 'tr' ? 'Kullanım Şartları' : 'Terms of Service') . ' | ' . SITE_NAME;
$pageDescription = ($currentLang === 'tr' ? 
    'AllInToolbox kullanım şartları. Web sitesi ve araçlarımızın kullanım koşulları hakkında bilgi.' :
    'AllInToolbox terms of service. Information about the terms and conditions of using our website and tools.');
$pageKeywords = ($currentLang === 'tr' ? 
    'kullanım şartları, şartlar ve koşullar, yasal bilgiler, hizmet şartları' :
    'terms of service, terms and conditions, legal information, service terms');

include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">

        <!-- Breadcrumb -->
        <?php
        $breadcrumbItems = [
            ['title' => ($currentLang === 'tr') ? 'Ana Sayfa' : 'Home', 'url' => '/?lang=' . $currentLang],
            ['title' => ($currentLang === 'tr') ? 'Kullanım Şartları' : 'Terms of Service']
        ];
        echo generateBreadcrumb($breadcrumbItems);
        ?>

        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-4">
                <i class="fas fa-file-contract text-primary me-3"></i>
                <?php echo ($currentLang === 'tr') ? 'Kullanım Şartları' : 'Terms of Service'; ?>
            </h1>
            <p class="lead text-muted">
                <?php echo ($currentLang === 'tr') ? 
                    'AllInToolbox hizmetlerini kullanırken uymanız gereken şartlar ve koşullar' :
                    'Terms and conditions that you must comply with when using AllInToolbox services'; ?>
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
                                <h2><i class="fas fa-handshake text-primary me-2"></i>Kabul ve Onay</h2>
                                <p>AllInToolbox web sitesini ve hizmetlerini kullanarak, aşağıda belirtilen kullanım şartlarını okuduğunuzu, anladığınızı ve kabul ettiğinizi beyan etmiş olursunuz. Bu şartları kabul etmiyorsanız, lütfen web sitemizi kullanmayınız.</p>
                                <div class="alert alert-info">
                                    <strong>Önemli:</strong> Bu şartlar zaman zaman güncellenebilir. Güncellemeler web sitesinde yayınlandığı anda yürürlüğe girer.
                                </div>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-info-circle text-success me-2"></i>Hizmet Tanımı</h2>
                                <p>AllInToolbox, kullanıcılara aşağıdaki hizmetleri ücretsiz olarak sunmaktadır:</p>
                                <ul>
                                    <li><strong>Hesaplayıcı Araçları:</strong> BMI, kalori, kredi hesaplayıcısı gibi</li>
                                    <li><strong>Çevirici Araçları:</strong> Döviz, ölçü birimi, renk kodu çeviricisi gibi</li>
                                    <li><strong>Üretici Araçları:</strong> QR kod, şifre üretici gibi</li>
                                    <li><strong>Analiz Araçları:</strong> Metin analizi, yaş hesaplayıcı gibi</li>
                                    <li><strong>Pratik Araçları:</strong> Günlük hayatı kolaylaştıran çeşitli araçlar</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-user-check text-info me-2"></i>Kullanıcı Sorumlulukları</h2>
                                <h4>Kabul Edilen Kullanımlar:</h4>
                                <ul>
                                    <li>Araçları kişisel veya ticari amaçlarla kullanmak</li>
                                    <li>Doğru ve güncel bilgiler girmek</li>
                                    <li>Yasal sınırlar içinde kullanmak</li>
                                    <li>Diğer kullanıcıların haklarına saygı göstermek</li>
                                </ul>

                                <h4>Yasak Kullanımlar:</h4>
                                <ul>
                                    <li>Sistemi hacklemeye veya zarar vermeye çalışmak</li>
                                    <li>Spam veya zararlı içerik oluşturmak</li>
                                    <li>Başkalarının kişisel bilgilerini kötüye kullanmak</li>
                                    <li>Telif hakkı ihlali yapacak şekilde kullanmak</li>
                                    <li>Otomatik botlar veya scraping araçları kullanmak</li>
                                    <li>Hizmetin normal işleyişini engelleyecek şekilde aşırı yük oluşturmak</li>
                                    <li>Yanıltıcı veya sahte bilgiler yaymak</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-shield-alt text-warning me-2"></i>Hizmet Sınırlamaları</h2>
                                <h4>Teknik Sınırlamalar:</h4>
                                <ul>
                                    <li>Günlük kullanım limitlerí uygulanabilir</li>
                                    <li>Büyük hesaplama işlemleri zaman aşımına uğrayabilir</li>
                                    <li>Bazı özellikler geçici olarak kullanılamayabilir</li>
                                </ul>

                                <h4>İçerik Sınırlamaları:</h4>
                                <ul>
                                    <li>Girilen veriler belirli format ve boyut sınırlarına tabidir</li>
                                    <li>Uygunsuz içerik otomatik olarak filtrelenir</li>
                                    <li>Güvenlik nedeniyle bazı dosya türleri desteklenmez</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-exclamation-triangle text-danger me-2"></i>Sorumluluk Reddi</h2>
                                <div class="alert alert-warning">
                                    <strong>Önemli Uyarı:</strong> AllInToolbox araçları genel bilgilendirme amaçlıdır. Profesyonel tavsiye yerine geçmez.
                                </div>
                                
                                <h4>Doğruluk Garantisi:</h4>
                                <ul>
                                    <li>Hesaplama sonuçlarının %100 doğruluğunu garanti etmiyoruz</li>
                                    <li>Sonuçlar yaklaşık değerler olabilir</li>
                                    <li>Kritik kararlar için profesyonel danışmanlık alınmalıdır</li>
                                </ul>

                                <h4>Hizmet Kesintileri:</h4>
                                <ul>
                                    <li>Planlı veya plansız bakım çalışmaları olabilir</li>
                                    <li>Teknik arızalar nedeniyle geçici kesintiler yaşanabilir</li>
                                    <li>Üçüncü taraf servislere bağımlı özellikler etkilenebilir</li>
                                </ul>

                                <h4>Finansal Sorumluluk:</h4>
                                <p>AllInToolbox, hizmetlerin kullanımından doğabilecek hiçbir maddi veya manevi zarar için sorumlu değildir. Bu dahil olmak üzere:</p>
                                <ul>
                                    <li>Yanlış hesaplama sonuçlarından kaynaklanan zararlar</li>
                                    <li>Hizmet kesintilerinden dolayı oluşan kayıplar</li>
                                    <li>Veri kaybı veya güvenlik ihlalleri</li>
                                    <li>Üçüncü taraf hizmetlerden kaynaklanan sorunlar</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-copyright text-secondary me-2"></i>Fikri Mülkiyet Hakları</h2>
                                <h4>AllInToolbox'ın Hakları:</h4>
                                <ul>
                                    <li>Web sitesi tasarımı ve kodları telif hakkı ile korunmaktadır</li>
                                    <li>Marka adı ve logosu tescilli markalardır</li>
                                    <li>İçerik ve algoritmalarm tüm hakları saklıdır</li>
                                </ul>

                                <h4>Kullanıcı Hakları:</h4>
                                <ul>
                                    <li>Hesaplama sonuçlarını kişisel veya ticari amaçla kullanabilirsiniz</li>
                                    <li>Web sitesine link verebilirsiniz</li>
                                    <li>Ekran görüntüsü alıp paylaşabilirsiniz (kaynak belirterek)</li>
                                </ul>

                                <h4>Yasak İşlemler:</h4>
                                <ul>
                                    <li>Web sitesinin kopyalanması veya klonlanması</li>
                                    <li>Kaynak kod çalınması veya tersine mühendislik</li>
                                    <li>Marka ve logo kullanımı (izin alınmadıkça)</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-gavel text-info me-2"></i>Yasal Hükümler</h2>
                                <h4>Geçerli Hukuk:</h4>
                                <p>Bu kullanım şartları Türkiye Cumhuriyeti kanunlarına tabidir ve Türk hukuku uyarınca yorumlanır.</p>

                                <h4>Uyuşmazlık Çözümü:</h4>
                                <ul>
                                    <li>Öncelikle dostane çözüm aranacaktır</li>
                                    <li>Çözülemeven uyuşmazlıklar İstanbul mahkemelerinde görülecektir</li>
                                    <li>Tüketici hakları saklıdır</li>
                                </ul>

                                <h4>Uygulanabilir Kanunlar:</h4>
                                <ul>
                                    <li>6698 sayılı Kişisel Verilerin Korunması Kanunu</li>
                                    <li>5651 sayılı İnternet Ortamında Yapılan Yayınların Düzenlenmesi Hakkında Kanun</li>
                                    <li>6563 sayılı Elektronik Ticaretin Düzenlenmesi Hakkında Kanun</li>
                                    <li>Türk Ticaret Kanunu ve İlgili Mevzuat</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-ban text-danger me-2"></i>Hesap Askıya Alma ve Sonlandırma</h2>
                                <p>Aşağıdaki durumlarda hizmetlere erişiminiz kısıtlanabilir veya sonlandırılabilir:</p>
                                <ul>
                                    <li>Kullanım şartlarının ihlal edilmesi</li>
                                    <li>Sisteme zarar verici faaliyetler</li>
                                    <li>Diğer kullanıcıların haklarının çiğnenmesi</li>
                                    <li>Yasal yükümlülüklerin ihlali</li>
                                </ul>
                                <p>Askıya alma işlemi öncesinde mümkün olduğunca uyarı verilmeye çalışılır.</p>
                            </section>

                        <?php else: ?>
                            <!-- English Content -->
                            <section class="mb-5">
                                <h2><i class="fas fa-handshake text-primary me-2"></i>Acceptance and Agreement</h2>
                                <p>By using the AllInToolbox website and services, you acknowledge that you have read, understood, and agree to be bound by the terms and conditions set forth below. If you do not agree to these terms, please do not use our website.</p>
                                <div class="alert alert-info">
                                    <strong>Important:</strong> These terms may be updated from time to time. Updates take effect immediately when published on the website.
                                </div>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-info-circle text-success me-2"></i>Service Description</h2>
                                <p>AllInToolbox provides users with the following services free of charge:</p>
                                <ul>
                                    <li><strong>Calculator Tools:</strong> BMI, calorie, loan calculators, etc.</li>
                                    <li><strong>Converter Tools:</strong> Currency, unit, color code converters, etc.</li>
                                    <li><strong>Generator Tools:</strong> QR code, password generators, etc.</li>
                                    <li><strong>Analysis Tools:</strong> Text analysis, age calculator, etc.</li>
                                    <li><strong>Utility Tools:</strong> Various tools that make daily life easier</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-user-check text-info me-2"></i>User Responsibilities</h2>
                                <h4>Acceptable Use:</h4>
                                <ul>
                                    <li>Using tools for personal or commercial purposes</li>
                                    <li>Entering accurate and current information</li>
                                    <li>Using within legal boundaries</li>
                                    <li>Respecting other users' rights</li>
                                </ul>

                                <h4>Prohibited Use:</h4>
                                <ul>
                                    <li>Attempting to hack or damage the system</li>
                                    <li>Creating spam or harmful content</li>
                                    <li>Misusing others' personal information</li>
                                    <li>Using in ways that violate copyright</li>
                                    <li>Using automated bots or scraping tools</li>
                                    <li>Creating excessive load that would disrupt normal service operation</li>
                                    <li>Spreading misleading or false information</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-shield-alt text-warning me-2"></i>Service Limitations</h2>
                                <h4>Technical Limitations:</h4>
                                <ul>
                                    <li>Daily usage limits may be applied</li>
                                    <li>Large calculation operations may timeout</li>
                                    <li>Some features may be temporarily unavailable</li>
                                </ul>

                                <h4>Content Limitations:</h4>
                                <ul>
                                    <li>Input data is subject to certain format and size limits</li>
                                    <li>Inappropriate content is automatically filtered</li>
                                    <li>Some file types are not supported for security reasons</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-exclamation-triangle text-danger me-2"></i>Disclaimer</h2>
                                <div class="alert alert-warning">
                                    <strong>Important Warning:</strong> AllInToolbox tools are for general informational purposes only. They do not replace professional advice.
                                </div>
                                
                                <h4>Accuracy Guarantee:</h4>
                                <ul>
                                    <li>We do not guarantee 100% accuracy of calculation results</li>
                                    <li>Results may be approximate values</li>
                                    <li>Professional consultation should be sought for critical decisions</li>
                                </ul>

                                <h4>Service Interruptions:</h4>
                                <ul>
                                    <li>Planned or unplanned maintenance work may occur</li>
                                    <li>Temporary interruptions may occur due to technical failures</li>
                                    <li>Features dependent on third-party services may be affected</li>
                                </ul>

                                <h4>Financial Responsibility:</h4>
                                <p>AllInToolbox is not responsible for any material or moral damages that may arise from the use of services, including:</p>
                                <ul>
                                    <li>Damages caused by incorrect calculation results</li>
                                    <li>Losses due to service interruptions</li>
                                    <li>Data loss or security breaches</li>
                                    <li>Issues arising from third-party services</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-copyright text-secondary me-2"></i>Intellectual Property Rights</h2>
                                <h4>AllInToolbox Rights:</h4>
                                <ul>
                                    <li>Website design and code are protected by copyright</li>
                                    <li>Brand name and logo are registered trademarks</li>
                                    <li>All rights to content and algorithms are reserved</li>
                                </ul>

                                <h4>User Rights:</h4>
                                <ul>
                                    <li>You may use calculation results for personal or commercial purposes</li>
                                    <li>You may link to the website</li>
                                    <li>You may take and share screenshots (with source attribution)</li>
                                </ul>

                                <h4>Prohibited Actions:</h4>
                                <ul>
                                    <li>Copying or cloning the website</li>
                                    <li>Stealing source code or reverse engineering</li>
                                    <li>Using brand and logo (without permission)</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-gavel text-info me-2"></i>Legal Provisions</h2>
                                <h4>Governing Law:</h4>
                                <p>These terms of use are subject to the laws of the Republic of Turkey and shall be interpreted in accordance with Turkish law.</p>

                                <h4>Dispute Resolution:</h4>
                                <ul>
                                    <li>Amicable solution will be sought first</li>
                                    <li>Unresolved disputes will be heard in Istanbul courts</li>
                                    <li>Consumer rights are reserved</li>
                                </ul>

                                <h4>Applicable Laws:</h4>
                                <ul>
                                    <li>Personal Data Protection Law No. 6698</li>
                                    <li>Law No. 5651 on Regulation of Publications on the Internet</li>
                                    <li>Law No. 6563 on Regulation of Electronic Commerce</li>
                                    <li>Turkish Commercial Code and Related Legislation</li>
                                </ul>
                            </section>

                            <section class="mb-5">
                                <h2><i class="fas fa-ban text-danger me-2"></i>Account Suspension and Termination</h2>
                                <p>Your access to services may be restricted or terminated in the following cases:</p>
                                <ul>
                                    <li>Violation of terms of use</li>
                                    <li>Activities harmful to the system</li>
                                    <li>Violation of other users' rights</li>
                                    <li>Violation of legal obligations</li>
                                </ul>
                                <p>Warning will be attempted whenever possible before suspension.</p>
                            </section>
                        <?php endif; ?>

                        <!-- Additional Sections Common to Both Languages -->
                        <section class="mb-5">
                            <h2><i class="fas fa-globe text-primary me-2"></i><?php echo ($currentLang === 'tr') ? 'Çok Dilli Hizmet' : 'Multi-Language Service'; ?></h2>
                            <p>
                                <?php echo ($currentLang === 'tr') ? 
                                    'AllInToolbox şu anda Türkçe ve İngilizce dillerinde hizmet vermektedir. Dil çevirilerindeki küçük farklılıklar durumunda Türkçe metin esas alınır.' :
                                    'AllInToolbox currently provides services in Turkish and English. In case of minor differences in language translations, the Turkish text is taken as the basis.'; ?>
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2><i class="fas fa-mobile-alt text-info me-2"></i><?php echo ($currentLang === 'tr') ? 'Mobil Uygulama' : 'Mobile Application'; ?></h2>
                            <p>
                                <?php echo ($currentLang === 'tr') ? 
                                    'AllInToolbox şu anda web tabanlı bir hizmettir. Gelecekte mobil uygulama geliştirildiğinde, bu kullanım şartları mobil uygulama için de geçerli olacaktır.' :
                                    'AllInToolbox is currently a web-based service. When a mobile application is developed in the future, these terms of use will also apply to the mobile application.'; ?>
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2><i class="fas fa-ad text-warning me-2"></i><?php echo ($currentLang === 'tr') ? 'Reklamlar ve Üçüncü Taraf İçerikleri' : 'Advertisements and Third-Party Content'; ?></h2>
                            <p>
                                <?php echo ($currentLang === 'tr') ? 
                                    'Web sitemizde üçüncü taraf reklamları görüntülenebilir. Bu reklamların içeriğinden sorumlu değiliz. Reklam verenlerin gizlilik politikaları kendilerine aittir.' :
                                    'Third-party advertisements may be displayed on our website. We are not responsible for the content of these advertisements. Advertisers\' privacy policies are their own.'; ?>
                            </p>
                            <ul>
                                <li><?php echo ($currentLang === 'tr') ? 'Google AdSense reklamları gösterilebilir' : 'Google AdSense ads may be displayed'; ?></li>
                                <li><?php echo ($currentLang === 'tr') ? 'Ezoic reklam platformu kullanılabilir' : 'Ezoic advertising platform may be used'; ?></li>
                                <li><?php echo ($currentLang === 'tr') ? 'Reklamlara tıklamak sizin sorumluluğunuzdadır' : 'Clicking on ads is your responsibility'; ?></li>
                            </ul>
                        </section>

                        <!-- Contact Section -->
                        <section class="mb-5">
                            <h2><i class="fas fa-envelope text-primary me-2"></i><?php echo ($currentLang === 'tr') ? 'İletişim' : 'Contact'; ?></h2>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-3">
                                        <?php echo ($currentLang === 'tr') ? 
                                            'Kullanım şartları hakkında sorularınız varsa bizimle iletişime geçebilirsiniz:' :
                                            'If you have questions about our terms of service, you can contact us:'; ?>
                                    </p>
                                    <ul class="list-unstyled mb-0">
                                        <li><i class="fas fa-envelope me-2"></i><strong>Email:</strong> legal@allintoolbox.com</li>
                                        <li><i class="fas fa-globe me-2"></i><strong>Website:</strong> <a href="/pages/contact.php?lang=<?php echo $currentLang; ?>">
                                            <?php echo ($currentLang === 'tr') ? 'İletişim Formu' : 'Contact Form'; ?>
                                        </a></li>
                                        <li><i class="fas fa-clock me-2"></i><strong><?php echo ($currentLang === 'tr') ? 'Yanıt süresi:' : 'Response time:'; ?></strong> 
                                            <?php echo ($currentLang === 'tr') ? '5 iş günü içinde' : 'Within 5 business days'; ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </section>

                        <!-- Updates Section -->
                        <section>
                            <h2><i class="fas fa-sync text-secondary me-2"></i><?php echo ($currentLang === 'tr') ? 'Şartların Güncellenmesi' : 'Updates to Terms'; ?></h2>
                            <p>
                                <?php echo ($currentLang === 'tr') ? 
                                    'Bu kullanım şartları gerektiğinde güncellenebilir. Önemli değişiklikleri ana sayfada duyuru ile bildireceğiz. Güncellemeler yayınlandığı tarihte yürürlüğe girer.' :
                                    'These terms of use may be updated when necessary. We will announce significant changes on the homepage. Updates take effect on the date they are published.'; ?>
                            </p>
                            <div class="alert alert-info">
                                <strong><?php echo ($currentLang === 'tr') ? 'Son güncelleme tarihi:' : 'Last update date:'; ?></strong> 01.01.2025<br>
                                <strong><?php echo ($currentLang === 'tr') ? 'Versiyon:' : 'Version:'; ?></strong> 1.0
                            </div>
                            
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong><?php echo ($currentLang === 'tr') ? 'Teşekkür:' : 'Thank you:'; ?></strong>
                                <?php echo ($currentLang === 'tr') ? 
                                    'AllInToolbox hizmetlerini kullandığınız için teşekkür ederiz. Sorularınız için her zaman yanınızdayız.' :
                                    'Thank you for using AllInToolbox services. We are always here for your questions.'; ?>
                            </div>
                        </section>

                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="mb-4 text-center">
                            <i class="fas fa-link me-2"></i>
                            <?php echo ($currentLang === 'tr') ? 'Hızlı Bağlantılar' : 'Quick Links'; ?>
                        </h4>
                        <div class="row text-center">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="/pages/privacy.php?lang=<?php echo $currentLang; ?>" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-shield-alt me-2"></i>
                                    <?php echo ($currentLang === 'tr') ? 'Gizlilik Politikası' : 'Privacy Policy'; ?>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="/pages/contact.php?lang=<?php echo $currentLang; ?>" class="btn btn-outline-success w-100">
                                    <i class="fas fa-envelope me-2"></i>
                                    <?php echo ($currentLang === 'tr') ? 'İletişim' : 'Contact'; ?>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="/pages/about.php?lang=<?php echo $currentLang; ?>" class="btn btn-outline-info w-100">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <?php echo ($currentLang === 'tr') ? 'Hakkımızda' : 'About Us'; ?>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="/?lang=<?php echo $currentLang; ?>" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-home me-2"></i>
                                    <?php echo ($currentLang === 'tr') ? 'Ana Sayfa' : 'Home'; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Track terms page view
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Legal', 'View', 'Terms of Service');
    }
    
    // Add table of contents navigation
    const sections = document.querySelectorAll('section h2');
    if (sections.length > 0) {
        const toc = document.createElement('div');
        toc.className = 'card border-info mb-4';
        toc.innerHTML = `
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    ${document.documentElement.lang === 'tr' ? 'İçindekiler' : 'Table of Contents'}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    ${Array.from(sections).map((section, index) => {
                        const id = 'section-' + index;
                        section.id = id;
                        return `
                            <div class="col-md-6 mb-2">
                                <a href="#${id}" class="text-decoration-none d-block p-2 rounded">
                                    <small>${section.textContent}</small>
                                </a>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>
        `;
        
        const firstSection = document.querySelector('section');
        if (firstSection) {
            firstSection.parentNode.insertBefore(toc, firstSection);
        }
    }
    
    // Smooth scrolling for TOC links
    document.querySelectorAll('a[href^="#section-"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
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
        background: linear-gradient(90deg, #007bff, #28a745);
        z-index: 9999;
        transition: width 0.3s ease;
    `;
    document.body.appendChild(progressBar);
    
    window.addEventListener('scroll', function() {
        const scrolled = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
        progressBar.style.width = Math.min(100, Math.max(0, scrolled)) + '%';
    });
    
    // Print functionality
    const printBtn = document.createElement('button');
    printBtn.className = 'btn btn-secondary position-fixed';
    printBtn.style.cssText = 'bottom: 20px; right: 20px; z-index: 1000;';
    printBtn.innerHTML = '<i class="fas fa-print"></i>';
    printBtn.title = document.documentElement.lang === 'tr' ? 'Yazdır' : 'Print';
    printBtn.onclick = () => window.print();
    document.body.appendChild(printBtn);
});
</script>

<?php include '../includes/footer.php'; ?>