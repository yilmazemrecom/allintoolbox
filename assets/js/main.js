// AllInToolbox Main JavaScript

// Global Variables
const AllInToolbox = {
    // Configuration
    config: {
        apiTimeout: 5000,
        debounceDelay: 300,
        animationDuration: 300
    },
    
    // Utility functions
    utils: {
        // Debounce function
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },
        
        // Format number with thousands separator
        formatNumber: function(num, decimals = 2) {
            if (isNaN(num)) return '';
            return Number(num).toLocaleString('tr-TR', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        },
        
        // Validate email
        isValidEmail: function(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },
        
        // Validate number
        isValidNumber: function(value) {
            return !isNaN(value) && isFinite(value);
        },
        
        // Copy text to clipboard
        copyToClipboard: function(text) {
            navigator.clipboard.writeText(text).then(() => {
                this.showNotification('Panoya kopyalandı!', 'success');
            }).catch(() => {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    this.showNotification('Panoya kopyalandı!', 'success');
                } catch (err) {
                    this.showNotification('Kopyalama başarısız!', 'error');
                }
                document.body.removeChild(textArea);
            });
        },
        
        // Show notification
        showNotification: function(message, type = 'info') {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.notification');
            existingNotifications.forEach(notification => notification.remove());
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification alert alert-${type === 'error' ? 'danger' : type} position-fixed`;
            notification.style.cssText = `
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 250px;
                animation: slideInRight 0.3s ease-out;
            `;
            notification.innerHTML = `
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
                ${message}
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.style.animation = 'slideOutRight 0.3s ease-in';
                    setTimeout(() => notification.remove(), 300);
                }
            }, 3000);
        },
        
        // Loading state
        showLoading: function(element, show = true) {
            if (show) {
                element.disabled = true;
                const originalText = element.textContent;
                element.dataset.originalText = originalText;
                element.innerHTML = '<span class="loading"></span> Hesaplanıyor...';
            } else {
                element.disabled = false;
                element.innerHTML = element.dataset.originalText || 'Hesapla';
            }
        },
        
        // Format currency
        formatCurrency: function(amount, currency = 'TRY') {
            const currencySymbols = {
                'TRY': '₺',
                'USD': '$',
                'EUR': '€',
                'GBP': '£'
            };
            
            const symbol = currencySymbols[currency] || currency;
            return `${this.formatNumber(amount)} ${symbol}`;
        },
        
        // Get URL parameters
        getUrlParameter: function(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        },
        
        // Set URL parameter
        setUrlParameter: function(name, value) {
            const url = new URL(window.location);
            url.searchParams.set(name, value);
            window.history.replaceState({}, '', url);
        }
    },
    
    // Search functionality
    search: {
        init: function() {
            const searchInput = document.querySelector('input[name="q"]');
            if (searchInput) {
                searchInput.addEventListener('input', AllInToolbox.utils.debounce(
                    AllInToolbox.search.handleSearch, 
                    AllInToolbox.config.debounceDelay
                ));
            }
        },
        
        handleSearch: function(event) {
            const query = event.target.value.trim();
            if (query.length < 2) {
                AllInToolbox.search.hideResults();
                return;
            }
            
            // Simple client-side search (could be enhanced with server-side search)
            AllInToolbox.search.performSearch(query);
        },
        
        performSearch: function(query) {
            // This would typically make an AJAX request to a search endpoint
            // For now, we'll simulate with client-side search
            const tools = [
                {name: 'BMI Hesaplayıcı', url: '/tr/araclar/bmi-hesaplayici.php'},
                {name: 'Kredi Hesaplayıcı', url: '/tr/araclar/kredi-hesaplayici.php'},
                {name: 'QR Kod Üretici', url: '/tr/araclar/qr-kod-uretici.php'},
                {name: 'Döviz Çevirici', url: '/tr/cevirici/doviz-cevirici.php'},
                {name: 'Ölçü Birimi Çevirici', url: '/tr/cevirici/olcu-birimi-cevirici.php'}
            ];
            
            const results = tools.filter(tool => 
                tool.name.toLowerCase().includes(query.toLowerCase())
            );
            
            AllInToolbox.search.showResults(results);
        },
        
        showResults: function(results) {
            let resultsContainer = document.querySelector('.search-results');
            if (!resultsContainer) {
                resultsContainer = document.createElement('div');
                resultsContainer.className = 'search-results';
                const searchContainer = document.querySelector('.d-flex');
                searchContainer.appendChild(resultsContainer);
            }
            
            if (results.length === 0) {
                resultsContainer.innerHTML = '<div class="search-result-item">Sonuç bulunamadı</div>';
            } else {
                resultsContainer.innerHTML = results.map(result => 
                    `<div class="search-result-item" onclick="window.location.href='${result.url}'">${result.name}</div>`
                ).join('');
            }
            
            resultsContainer.style.display = 'block';
        },
        
        hideResults: function() {
            const resultsContainer = document.querySelector('.search-results');
            if (resultsContainer) {
                resultsContainer.style.display = 'none';
            }
        }
    },
    
    // Form validation
    validation: {
        validateForm: function(form) {
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!AllInToolbox.validation.validateField(input)) {
                    isValid = false;
                }
            });
            
            return isValid;
        },
        
        validateField: function(field) {
            const value = field.value.trim();
            const type = field.type;
            const required = field.hasAttribute('required');
            
            // Remove existing error styling
            field.classList.remove('is-invalid');
            
            // Check if required field is empty
            if (required && !value) {
                AllInToolbox.validation.showFieldError(field, 'Bu alan zorunludur');
                return false;
            }
            
            // Type-specific validation
            if (value) {
                switch (type) {
                    case 'email':
                        if (!AllInToolbox.utils.isValidEmail(value)) {
                            AllInToolbox.validation.showFieldError(field, 'Geçerli bir e-posta adresi girin');
                            return false;
                        }
                        break;
                    case 'number':
                        if (!AllInToolbox.utils.isValidNumber(value)) {
                            AllInToolbox.validation.showFieldError(field, 'Geçerli bir sayı girin');
                            return false;
                        }
                        break;
                    case 'tel':
                        if (!/^[\d\s\-\+\(\)]+$/.test(value)) {
                            AllInToolbox.validation.showFieldError(field, 'Geçerli bir telefon numarası girin');
                            return false;
                        }
                        break;
                }
            }
            
            // Remove error if validation passes
            AllInToolbox.validation.clearFieldError(field);
            return true;
        },
        
        showFieldError: function(field, message) {
            field.classList.add('is-invalid');
            
            // Remove existing error message
            const existingError = field.parentNode.querySelector('.invalid-feedback');
            if (existingError) {
                existingError.remove();
            }
            
            // Add new error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        },
        
        clearFieldError: function(field) {
            field.classList.remove('is-invalid');
            const errorDiv = field.parentNode.querySelector('.invalid-feedback');
            if (errorDiv) {
                errorDiv.remove();
            }
        }
    },
    
    // Analytics
    analytics: {
        trackEvent: function(category, action, label = null) {
            // Google Analytics tracking
            if (typeof gtag !== 'undefined') {
                gtag('event', action, {
                    event_category: category,
                    event_label: label
                });
            }
            
            // Custom analytics tracking can be added here
            console.log('Analytics Event:', {category, action, label});
        },
        
        trackToolUsage: function(toolName) {
            AllInToolbox.analytics.trackEvent('Tool Usage', 'Calculate', toolName);
        },
        
        trackPageView: function(page) {
            if (typeof gtag !== 'undefined') {
                gtag('config', 'GA_MEASUREMENT_ID', {
                    page_title: document.title,
                    page_location: window.location.href
                });
            }
        }
    },
    
    // Local storage management
    storage: {
        set: function(key, value) {
            try {
                localStorage.setItem(`allintoolbox_${key}`, JSON.stringify(value));
            } catch (e) {
                console.warn('LocalStorage not available');
            }
        },
        
        get: function(key, defaultValue = null) {
            try {
                const item = localStorage.getItem(`allintoolbox_${key}`);
                return item ? JSON.parse(item) : defaultValue;
            } catch (e) {
                return defaultValue;
            }
        },
        
        remove: function(key) {
            try {
                localStorage.removeItem(`allintoolbox_${key}`);
            } catch (e) {
                console.warn('LocalStorage not available');
            }
        },
        
        // Save recent tools
        addRecentTool: function(toolId, toolName, toolUrl) {
            const recentTools = this.get('recent_tools', []);
            
            // Remove if already exists
            const filtered = recentTools.filter(tool => tool.id !== toolId);
            
            // Add to beginning
            filtered.unshift({
                id: toolId,
                name: toolName,
                url: toolUrl,
                timestamp: Date.now()
            });
            
            // Keep only last 10
            const limited = filtered.slice(0, 10);
            
            this.set('recent_tools', limited);
        },
        
        getRecentTools: function() {
            return this.get('recent_tools', []);
        }
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Fix Bootstrap navbar collapse on mobile
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarToggler && navbarCollapse) {
        // Close navbar when clicking outside
        document.addEventListener('click', function(e) {
            if (!navbarCollapse.contains(e.target) && !navbarToggler.contains(e.target)) {
                if (navbarCollapse.classList.contains('show')) {
                    navbarToggler.click();
                }
            }
        });
    }
    
    // Initialize search
    AllInToolbox.search.init();
    
    // Add click handlers for copy buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('copy-btn')) {
            const textToCopy = e.target.dataset.copy || e.target.previousElementSibling.value;
            AllInToolbox.utils.copyToClipboard(textToCopy);
        }
    });
    
    // Add form validation to all forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!AllInToolbox.validation.validateForm(form)) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('blur', function() {
                AllInToolbox.validation.validateField(field);
            });
        });
    });
    
    // Newsletter form handler
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            // Simulate newsletter signup
            AllInToolbox.utils.showNotification('E-posta listemize kaydoldunuz!', 'success');
            AllInToolbox.analytics.trackEvent('Newsletter', 'Signup', email);
            
            // Reset form
            this.reset();
        });
    }
    
    // Track page view
    AllInToolbox.analytics.trackPageView(window.location.pathname);
    
    // Add smooth scrolling to anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.d-flex') && !e.target.closest('.search-results')) {
            AllInToolbox.search.hideResults();
        }
    });
    
    // Add fade-in animation to cards
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    });
    
    document.querySelectorAll('.card, .tool-container').forEach(card => {
        observer.observe(card);
    });
    
    // Fix mobile viewport height
    const setVH = () => {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    };
    
    setVH();
    window.addEventListener('resize', setVH);
});

// Export for global use
window.AllInToolbox = AllInToolbox;