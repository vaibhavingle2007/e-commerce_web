// Performance optimizations for mobile experience
(function() {
    'use strict';
    
    // Critical performance optimizations
    const PerformanceOptimizer = {
        
        // Preload critical resources
        preloadCriticalResources: function() {
            const criticalResources = [
                { href: 'assets/css/style.css', as: 'style' },
                { href: 'assets/js/cart.js', as: 'script' }
            ];
            
            criticalResources.forEach(resource => {
                const link = document.createElement('link');
                link.rel = 'preload';
                link.href = resource.href;
                link.as = resource.as;
                if (resource.as === 'style') {
                    link.onload = function() {
                        this.onload = null;
                        this.rel = 'stylesheet';
                    };
                }
                document.head.appendChild(link);
            });
        },
        
        // Optimize images with lazy loading
        optimizeImages: function() {
            const images = document.querySelectorAll('img:not([data-src])');
            
            // Convert regular images to lazy loading
            images.forEach(img => {
                if (img.src && !img.hasAttribute('data-src')) {
                    const rect = img.getBoundingClientRect();
                    const isInViewport = rect.top < window.innerHeight && rect.bottom > 0;
                    
                    if (!isInViewport) {
                        img.setAttribute('data-src', img.src);
                        img.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMSIgaGVpZ2h0PSIxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InRyYW5zcGFyZW50Ii8+PC9zdmc+';
                        img.style.backgroundColor = 'rgba(0, 212, 255, 0.1)';
                    }
                }
            });
        },
        
        // Debounce scroll events for better performance
        debounceScrollEvents: function() {
            let scrollTimeout;
            let isScrolling = false;
            
            const handleScroll = () => {
                if (!isScrolling) {
                    requestAnimationFrame(() => {
                        // Scroll-based optimizations
                        this.updateNavbarOnScroll();
                        isScrolling = false;
                    });
                    isScrolling = true;
                }
            };
            
            window.addEventListener('scroll', handleScroll, { passive: true });
        },
        
        // Update navbar appearance on scroll
        updateNavbarOnScroll: function() {
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                const scrolled = window.scrollY > 50;
                navbar.style.background = scrolled 
                    ? 'rgba(26, 26, 46, 0.95)' 
                    : 'rgba(34, 34, 34, 0.8)';
                navbar.style.backdropFilter = scrolled 
                    ? 'blur(25px)' 
                    : 'blur(20px)';
            }
        },
        
        // Optimize animations based on device capabilities
        optimizeAnimations: function() {
            const isLowEndDevice = navigator.hardwareConcurrency <= 2 || 
                                   navigator.deviceMemory <= 2;
            
            if (isLowEndDevice) {
                document.documentElement.style.setProperty('--animation-duration', '0.2s');
                document.documentElement.style.setProperty('--transition-duration', '0.15s');
                
                // Disable complex animations
                const complexAnimations = document.querySelectorAll('.cosmic-orb');
                complexAnimations.forEach(el => el.style.display = 'none');
            }
        },
        
        // Implement connection-aware loading
        adaptToConnection: function() {
            if ('connection' in navigator) {
                const connection = navigator.connection;
                const isSlowConnection = connection.effectiveType === 'slow-2g' || 
                                       connection.effectiveType === '2g';
                
                if (isSlowConnection) {
                    // Reduce image quality and disable non-essential animations
                    document.documentElement.classList.add('slow-connection');
                    
                    // Disable background gradients and complex effects
                    const style = document.createElement('style');
                    style.textContent = `
                        .slow-connection .cosmic-orb,
                        .slow-connection .product-glow,
                        .slow-connection .electronics-hero::before {
                            display: none !important;
                        }
                        .slow-connection .product-card::before,
                        .slow-connection .category-card::before,
                        .slow-connection .deal-card::before {
                            display: none !important;
                        }
                    `;
                    document.head.appendChild(style);
                }
            }
        },
        
        // Initialize all optimizations
        init: function() {
            // Run immediately
            this.optimizeAnimations();
            this.adaptToConnection();
            
            // Run after DOM is loaded
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    this.optimizeImages();
                    this.debounceScrollEvents();
                });
            } else {
                this.optimizeImages();
                this.debounceScrollEvents();
            }
            
            // Run after page is fully loaded
            window.addEventListener('load', () => {
                this.preloadCriticalResources();
            });
        }
    };
    
    // Initialize performance optimizations
    PerformanceOptimizer.init();
    
    // Register service worker for caching (if supported)
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('SW registered: ', registration);
                })
                .catch(registrationError => {
                    console.log('SW registration failed: ', registrationError);
                });
        });
    }
    
    // Export for debugging
    window.PerformanceOptimizer = PerformanceOptimizer;
    
})();