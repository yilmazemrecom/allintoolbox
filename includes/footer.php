<?php
// includes/footer.php - CLEAN URLs ile GÜNCELLENMIŞ

if (!isset($currentLang)) {
    $currentLang = getCurrentLanguage();
}

// Include URL helpers if available
if (file_exists(__DIR__ . '/../config/url-helpers.php')) {
    require_once __DIR__ . '/../config/url-helpers.php';
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
                    
                    <!-- Developer Info -->
                    <div class="mt-3">
                        <small class="text-light-50">
                            <i class="fas fa-code me-1"></i>
                            <?php echo ($currentLang === 'tr') ? 'Geliştirici:' : 'Developer:'; ?>
                            <a href="https://yilmazemre.tr" target="_blank" rel="noopener" class="text-warning text-decoration-none">
                                Emre Yılmaz
                            </a>
                        </small>
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
                            // Use clean URLs if helper functions exist
                            if (function_exists('getToolInfoWithCleanUrl')) {
                                $toolInfo = getToolInfoWithCleanUrl($toolId, $currentLang);
                            } else {
                                $toolInfo = getToolInfo($toolId, $currentLang);
                            }
                            
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
                            // Use clean URLs if helper functions exist
                            if (function_exists('getCategoryCleanUrl')) {
                                $categoryUrl = getCategoryCleanUrl($categoryId, $currentLang);
                            } else {
                                $categoryUrl = "/pages/category.php?category={$categoryId}&lang={$currentLang}";
                            }
                        ?>
                            <div class="col-12 mb-2">
                                <a href="<?php echo $categoryUrl; ?>" 
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
                        <?php
                        $staticPages = [
                            'about' => ($currentLang === 'tr') ? 'Hakkımızda' : 'About Us',
                            'contact' => ($currentLang === 'tr') ? 'İletişim' : 'Contact',
                            'privacy' => ($currentLang === 'tr') ? 'Gizlilik Politikası' : 'Privacy Policy',
                            'terms' => ($currentLang === 'tr') ? 'Kullanım Şartları' : 'Terms of Service'
                        ];
                        
                        $staticIcons = [
                            'about' => 'fas fa-info-circle',
                            'contact' => 'fas fa-envelope',
                            'privacy' => 'fas fa-shield-alt',
                            'terms' => 'fas fa-file-contract'
                        ];
                        
                        foreach ($staticPages as $pageId => $pageName):
                            // Use clean URLs if helper functions exist
                            if (function_exists('getStaticCleanUrl')) {
                                $pageUrl = getStaticCleanUrl($pageId, $currentLang);
                            } else {
                                $pageUrl = "/pages/{$pageId}.php?lang={$currentLang}";
                            }
                        ?>
                            <li class="mb-2">
                                <a href="<?php echo $pageUrl; ?>" class="text-light text-decoration-none footer-link">
                                    <i class="<?php echo $staticIcons[$pageId]; ?> me-2"></i><?php echo $pageName; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
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
                <div class="col-md-8">
                    <p class="mb-0 text-light-50">
                        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. <?php echo ($currentLang === 'tr') ? 'Tüm hakları saklıdır.' : 'All rights reserved.'; ?>
                        <span class="mx-2">•</span>
                        <?php echo ($currentLang === 'tr') ? 'Türkiye\'de yapıldı' : 'Made in Turkey'; ?> 🇹🇷
                        <span class="mx-2">•</span>
                        <a href="https://yilmazemre.tr" target="_blank" rel="noopener" class="text-warning text-decoration-none">
                            yilmazemre.tr
                        </a>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <small class="text-light-50">
                        <?php echo ($currentLang === 'tr') ? 'Sayfa yükleme süresi:' : 'Page load time:'; ?> 
                        <span id="loadTime"><?php echo getLoadTime(); ?>s</span>
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
    <?php if (defined('ANALYTICS') && !empty(ANALYTICS['google_analytics'])): ?>
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
    <?php endif; ?>
    
    <!-- Ezoic Integration -->
    <?php if (defined('ANALYTICS') && isset(ANALYTICS['ezoic_enabled']) && ANALYTICS['ezoic_enabled']): ?>
    <script data-ezoic="1">
        // Ezoic integration code
        var ezoicTestActive = true;
    </script>
    <?php endif; ?>

    <!-- Footer JavaScript -->
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
        
        // Track footer clicks for analytics
        document.querySelectorAll('.footer-link').forEach(link => {
            link.addEventListener('click', function() {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'click', {
                        event_category: 'Footer',
                        event_label: this.textContent.trim(),
                        value: 1
                    });
                }
            });
        });
        
        // Track developer link clicks
        document.querySelectorAll('a[href="https://yilmazemre.tr"]').forEach(link => {
            link.addEventListener('click', function() {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'click', {
                        event_category: 'Developer',
                        event_label: 'yilmazemre.tr',
                        value: 1
                    });
                }
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