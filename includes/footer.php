<?php
// includes/footer.php - GÜNCELLENMIŞ VERSİYON

if (!isset($currentLang)) {
    $currentLang = getCurrentLanguage();
}
?>
    </main> <!-- main-content end -->



    <!-- Bottom Ad Space -->
    <?php echo renderAdSpace('footer', 'banner'); ?>

    <!-- Footer -->
    <footer class="footer bg-dark text-light">
        <div class="container py-5">
            <div class="row">
                <!-- Site Info -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5><i class="fas fa-tools me-2"></i><?php echo SITE_NAME; ?></h5>
                    <p class="text-light-50">
                        <?php echo ($currentLang === 'tr') ? 
                            'Ücretsiz online araçlar ile hesaplamalarınızı kolaylaştırın. Hızlı, güvenli ve kullanımı kolay.' :
                            'Simplify your calculations with free online tools. Fast, secure and easy to use.'; ?>
                    </p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3" title="Facebook" aria-label="Facebook">
                            <i class="fab fa-facebook fa-lg"></i>
                        </a>
                        <a href="#" class="text-light me-3" title="Twitter" aria-label="Twitter">
                            <i class="fab fa-twitter fa-lg"></i>
                        </a>
                        <a href="#" class="text-light me-3" title="Instagram" aria-label="Instagram">
                            <i class="fab fa-instagram fa-lg"></i>
                        </a>
                        <a href="#" class="text-light" title="LinkedIn" aria-label="LinkedIn">
                            <i class="fab fa-linkedin fa-lg"></i>
                        </a>
                    </div>
                </div>

                <!-- Popular Tools -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5><?php echo ($currentLang === 'tr') ? 'Popüler Araçlar' : 'Popular Tools'; ?></h5>
                    <ul class="list-unstyled">
                        <?php 
                        $popularToolIds = [
                            'bmi-calculator',
                            'loan-calculator', 
                            'currency-converter',
                            'qr-code-generator',
                            'password-generator',
                            'calorie-calculator'
                        ];
                        
                        foreach ($popularToolIds as $toolId):
                            $toolInfo = getToolInfo($toolId, $currentLang);
                            if ($toolInfo):
                        ?>
                            <li class="mb-2">
                                <a href="<?php echo $toolInfo['url']; ?>" 
                                   class="text-light text-decoration-none footer-link">
                                    <i class="fas fa-chevron-right me-2 small"></i><?php echo $toolInfo['name']; ?>
                                </a>
                            </li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </div>

                <!-- Categories -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5><?php echo ($currentLang === 'tr') ? 'Kategoriler' : 'Categories'; ?></h5>
                    <div class="row">
                        <?php 
                        $categoryIcons = [
                            'finance' => 'fas fa-chart-line',
                            'health' => 'fas fa-heartbeat',
                            'web' => 'fas fa-code',
                            'converter' => 'fas fa-exchange-alt',
                            'utility' => 'fas fa-tools'
                        ];
                        
                        foreach (TOOL_CATEGORIES as $categoryId => $categoryNames): 
                        ?>
                            <div class="col-12 mb-2">
                                <a href="/<?php echo $currentLang; ?>/category/<?php echo $categoryId; ?>" 
                                   class="text-light text-decoration-none footer-link">
                                    <i class="<?php echo $categoryIcons[$categoryId]; ?> me-2"></i><?php echo $categoryNames[$currentLang]; ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quick Links & Info -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5><?php echo ($currentLang === 'tr') ? 'Hızlı Bağlantılar' : 'Quick Links'; ?></h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="/<?php echo $currentLang; ?>/about" class="text-light text-decoration-none footer-link">
                                <i class="fas fa-info-circle me-2"></i><?php echo ($currentLang === 'tr') ? 'Hakkımızda' : 'About Us'; ?>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/<?php echo $currentLang; ?>/contact" class="text-light text-decoration-none footer-link">
                                <i class="fas fa-envelope me-2"></i><?php echo ($currentLang === 'tr') ? 'İletişim' : 'Contact'; ?>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/<?php echo $currentLang; ?>/privacy" class="text-light text-decoration-none footer-link">
                                <i class="fas fa-shield-alt me-2"></i><?php echo ($currentLang === 'tr') ? 'Gizlilik Politikası' : 'Privacy Policy'; ?>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/<?php echo $currentLang; ?>/terms" class="text-light text-decoration-none footer-link">
                                <i class="fas fa-file-contract me-2"></i><?php echo ($currentLang === 'tr') ? 'Kullanım Şartları' : 'Terms of Service'; ?>
                            </a>
                        </li>
                    </ul>
                    
                    <div class="mt-4">
                        <h6><?php echo ($currentLang === 'tr') ? 'İstatistikler' : 'Statistics'; ?></h6>
                        <small class="text-light-50">
                            <i class="fas fa-tools me-1"></i> <?php echo SITE_STATS['total_tools']; ?> <?php echo ($currentLang === 'tr') ? 'Araç' : 'Tools'; ?><br>
                            <i class="fas fa-folder me-1"></i> <?php echo SITE_STATS['total_categories']; ?> <?php echo ($currentLang === 'tr') ? 'Kategori' : 'Categories'; ?><br>
                            <i class="fas fa-calendar me-1"></i> <?php echo ($currentLang === 'tr') ? 'Kuruluş:' : 'Since:'; ?> <?php echo date('Y', strtotime(SITE_STATS['launch_date'])); ?>
                        </small>
                    </div>
                </div>
            </div>

            <hr class="my-4 border-secondary">

            <!-- Bottom Footer -->
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-light-50">
                        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. <?php echo ($currentLang === 'tr') ? 'Tüm hakları saklıdır.' : 'All rights reserved.'; ?>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-light-50">
                        <?php echo ($currentLang === 'tr') ? 'Sayfa yükleme süresi:' : 'Page load time:'; ?> 
                        <span id="loadTime"><?php echo getLoadTime(); ?>s</span>
                        <span class="mx-2">•</span>
                        <?php echo ($currentLang === 'tr') ? 'Türkiye\'de yapıldı' : 'Made in Turkey'; ?> 🇹🇷
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="/assets/js/main.js"></script>
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo ANALYTICS['google_analytics']; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo ANALYTICS['google_analytics']; ?>', {
            page_title: document.title,
            page_location: window.location.href,
            custom_map: {
                'custom_parameter_1': 'tool_usage'
            }
        });
    </script>
    
    <!-- Ezoic Integration -->
    <?php if (ANALYTICS['ezoic_enabled']): ?>
    <script data-ezoic="1">
        // Ezoic integration code
        var ezoicTestActive = true;
    </script>
    <?php endif; ?>

    <!-- Newsletter Form Handler -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {

        
        // Footer link hover effects
        document.querySelectorAll('.footer-link').forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
                this.style.transition = 'transform 0.2s ease';
            });
            
            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });
        
        // Scroll to top functionality
        const scrollToTopBtn = document.createElement('button');
        scrollToTopBtn.innerHTML = '<i class="fas fa-chevron-up"></i>';
        scrollToTopBtn.className = 'btn btn-primary btn-scroll-top';
        scrollToTopBtn.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        `;
        
        document.body.appendChild(scrollToTopBtn);
        
        // Show/hide scroll to top button
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.style.display = 'block';
            } else {
                scrollToTopBtn.style.display = 'none';
            }
        });
        
        // Scroll to top functionality
        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
    </script>

    <?php if (isset($additionalScripts)): ?>
        <!-- Page-specific scripts -->
        <?php echo $additionalScripts; ?>
    <?php endif; ?>
</body>
</html>