</div> <!-- container end -->
    </main> <!-- main-content end -->

    <!-- Bottom Ad Space -->
    <?php echo renderAdSpace('footer', 'banner'); ?>

    <!-- Footer -->
    <footer class="footer bg-dark text-light mt-5">
        <div class="container py-5">
            <div class="row">
                <!-- Site Info -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5><i class="fas fa-tools"></i> <?php echo SITE_NAME; ?></h5>
                    <p><?php echo __('footer_description'); ?></p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>

                <!-- Popular Tools -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5><?php echo __('popular_tools'); ?></h5>
                    <ul class="list-unstyled">
                        <?php 
                        $popularTools = [
                            'bmi-calculator',
                            'loan-calculator', 
                            'qr-generator',
                            'currency-converter',
                            'password-generator'
                        ];
                        
                        foreach ($popularTools as $toolId):
                            $toolInfo = getToolInfo($toolId, $currentLang);
                            if ($toolInfo):
                        ?>
                            <li class="mb-2">
                                <a href="<?php echo $toolInfo['url']; ?>" class="text-light text-decoration-none">
                                    <i class="fas fa-chevron-right me-2"></i><?php echo $toolInfo['name']; ?>
                                </a>
                            </li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </div>

                <!-- Categories -->
                <div class="col-lg-4 col-md-12 mb-4">
                    <h5><?php echo __('categories'); ?></h5>
                    <div class="row">
                        <?php foreach (TOOL_CATEGORIES as $categoryId => $categoryNames): ?>
                            <div class="col-6 mb-2">
                                <a href="/<?php echo $currentLang; ?>/category/<?php echo $categoryId; ?>.php" 
                                   class="text-light text-decoration-none">
                                    <i class="fas fa-folder me-2"></i><?php echo $categoryNames[$currentLang]; ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Bottom Footer -->
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">
                        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. <?php echo __('all_rights_reserved'); ?>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="/<?php echo $currentLang; ?>/privacy.php" class="text-light text-decoration-none me-3">
                        <?php echo __('privacy_policy'); ?>
                    </a>
                    <a href="/<?php echo $currentLang; ?>/terms.php" class="text-light text-decoration-none">
                        <?php echo __('terms_of_service'); ?>
                    </a>
                </div>
            </div>
            

        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="/assets/js/main.js"></script>
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');
    </script>

    <?php if (isset($additionalScripts)): ?>
        <!-- Page-specific scripts -->
        <?php echo $additionalScripts; ?>
    <?php endif; ?>
</body>
</html>