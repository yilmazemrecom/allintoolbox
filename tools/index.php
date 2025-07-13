<?php
// en/index.php
session_start();

// Load configuration
require_once '../config/config.php';
require_once '../config/functions.php';

// Set language
setLanguage('en');

// Page information
$pageTitle = 'AllInToolbox - Free Online Calculator and Converter Tools';
$pageDescription = 'BMI calculator, loan calculator, QR code generator, currency converter and more. Free online tools.';
$pageKeywords = 'calculator, converter, BMI, loan, QR code, currency, online tools';

// Include header
include '../includes/header.php';
?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12">
                <h1 class="display-4 fw-bold mb-4">
                    Free Online <span class="text-warning">Calculator</span> Tools
                </h1>
                <p class="lead mb-4">
                    BMI calculator, loan calculator, QR code generator, currency converter and many more free online tools. 
                    Fast, easy and reliable results.
                </p>
                <div class="d-flex flex-column flex-sm-row gap-3">
                    <a href="#tools" class="btn btn-warning btn-lg">
                        <i class="fas fa-tools"></i> Explore Tools
                    </a>
                    <a href="#popular" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-star"></i> Popular Tools
                    </a>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 text-center mt-4 mt-lg-0">
                <div class="hero-visual" style="background: rgba(255,255,255,0.1); border: 2px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 3rem 2rem; backdrop-filter: blur(10px);">
                    <div class="d-flex justify-content-center align-items-center mb-3" style="gap: 1rem;">
                        <i class="fas fa-calculator fa-3x text-warning"></i>
                        <i class="fas fa-qrcode fa-3x text-info"></i>
                        <i class="fas fa-exchange-alt fa-3x text-success"></i>
                    </div>
                    <h4 class="text-white mb-2">10+ Free Tools</h4>
                    <p class="text-white-50 mb-0">Fast • Secure • Easy</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Tools -->
<section id="popular" class="mb-5">
    <div class="container">
        <h2 class="text-center mb-5">
            <i class="fas fa-fire text-warning"></i> Most Popular Tools
        </h2>
        <div class="row">
            <?php
            $quickTools = [
                'bmi-calculator' => ['icon' => 'fas fa-weight', 'color' => 'primary'],
                'loan-calculator' => ['icon' => 'fas fa-calculator', 'color' => 'success'],
                'qr-generator' => ['icon' => 'fas fa-qrcode', 'color' => 'info'],
                'currency-converter' => ['icon' => 'fas fa-exchange-alt', 'color' => 'warning'],
                'password-generator' => ['icon' => 'fas fa-key', 'color' => 'danger'],
                'unit-converter' => ['icon' => 'fas fa-ruler', 'color' => 'secondary']
            ];
            
            foreach ($quickTools as $toolId => $toolStyle):
                $toolInfo = getToolInfo($toolId, 'en');
                if ($toolInfo):
            ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm tool-card">
                        <div class="card-body text-center">
                            <div class="tool-icon mb-3">
                                <i class="<?php echo $toolStyle['icon']; ?> fa-3x text-<?php echo $toolStyle['color']; ?>"></i>
                            </div>
                            <h5 class="card-title"><?php echo $toolInfo['name']; ?></h5>
                            <p class="card-text text-muted"><?php echo $toolInfo['description']; ?></p>
                            <a href="<?php echo $toolInfo['url']; ?>" class="btn btn-<?php echo $toolStyle['color']; ?>">
                                <i class="fas fa-arrow-right"></i> Use Tool
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
</section>

<!-- Ad Space -->
<?php echo renderAdSpace('content', 'large'); ?>

<!-- Categories Section -->
<section id="tools" class="mb-5">
    <div class="container">
        <h2 class="text-center mb-5">
            <i class="fas fa-th-large text-primary"></i> Tool Categories
        </h2>
        <div class="row">
            <?php
            $categoryIcons = [
                'finance' => ['icon' => 'fas fa-chart-line', 'color' => 'success'],
                'health' => ['icon' => 'fas fa-heartbeat', 'color' => 'danger'],
                'web' => ['icon' => 'fas fa-code', 'color' => 'info'],
                'converter' => ['icon' => 'fas fa-exchange-alt', 'color' => 'warning'],
                'utility' => ['icon' => 'fas fa-tools', 'color' => 'secondary']
            ];
            
            foreach (TOOL_CATEGORIES as $categoryId => $categoryNames):
                $categoryTools = getToolsByCategory($categoryId, 'en');
                $iconInfo = $categoryIcons[$categoryId];
            ?>
                <div class="col-lg-6 mb-4">
                    <div class="card category-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="category-icon me-3">
                                    <i class="<?php echo $iconInfo['icon']; ?> fa-2x text-<?php echo $iconInfo['color']; ?>"></i>
                                </div>
                                <h4 class="card-title mb-0"><?php echo $categoryNames['en']; ?></h4>
                            </div>
                            
                            <div class="row">
                                <?php foreach ($categoryTools as $toolId => $tool): ?>
                                    <div class="col-6 mb-2">
                                        <a href="<?php echo $tool['url']; ?>" class="text-decoration-none">
                                            <small class="text-muted">
                                                <i class="fas fa-chevron-right me-1"></i><?php echo $tool['name']; ?>
                                            </small>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <a href="/en/category/<?php echo $categoryId; ?>.php" class="btn btn-outline-<?php echo $iconInfo['color']; ?> btn-sm mt-2">
                                <i class="fas fa-eye"></i> View All
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section bg-light py-5 mb-5">
    <div class="container">
        <h2 class="text-center mb-5">Why Choose AllInToolbox?</h2>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-bolt fa-3x text-warning"></i>
                    </div>
                    <h5>Fast & Easy</h5>
                    <p class="text-muted">All our tools load quickly and are easy to use. Perform complex calculations in seconds.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-shield-alt fa-3x text-success"></i>
                    </div>
                    <h5>Secure & Private</h5>
                    <p class="text-muted">Your data is safe. Calculations are performed only in your browser, no data is stored.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-mobile-alt fa-3x text-info"></i>
                    </div>
                    <h5>Mobile Friendly</h5>
                    <p class="text-muted">Works perfectly on all devices. Access from phone, tablet or computer.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-heart fa-3x text-danger"></i>
                    </div>
                    <h5>Completely Free</h5>
                    <p class="text-muted">All our tools are free. No registration required, unlimited usage.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Tools -->
<section class="mb-5">
    <div class="container">
        <h2 class="text-center mb-5">
            <i class="fas fa-clock text-info"></i> Recently Added Tools
        </h2>
        <div class="row">
            <?php
            $recentTools = ['text-analyzer', 'age-calculator', 'calorie-calculator', 'color-converter'];
            foreach ($recentTools as $toolId):
                $toolInfo = getToolInfo($toolId, 'en');
                if ($toolInfo):
            ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card recent-tool-card">
                        <div class="card-body text-center">
                            <span class="badge bg-success mb-2">New</span>
                            <h6 class="card-title"><?php echo $toolInfo['name']; ?></h6>
                            <p class="card-text"><small class="text-muted"><?php echo $toolInfo['description']; ?></small></p>
                            <a href="<?php echo $toolInfo['url']; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> Try It
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
</section>

<!-- Newsletter Signup -->
<section class="newsletter-section bg-primary text-white py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h3 class="mb-3">
                    <i class="fas fa-envelope"></i> Stay Updated with New Tools
                </h3>
                <p class="mb-4">Join our mailing list to receive updates about new tools and features.</p>
                <form class="row g-3 justify-content-center" id="newsletterForm">
                    <div class="col-auto">
                        <input type="email" class="form-control" placeholder="Your email address" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-paper-plane"></i> Subscribe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>