// Enhanced JavaScript for improved interactivity
// Requirements: 2.4, 3.4, 6.3

document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // ===== SMOOTH SCROLLING BETWEEN SECTIONS =====
    const SmoothScrollManager = {
        init: function() {
            this.setupSmoothScrolling();
            this.setupNavigationHighlighting();
        },
        
        setupSmoothScrolling: function() {
            // Enhanced smooth scrolling for all anchor links
            const anchorLinks = document.querySelectorAll('a[href^="#"], .btn[href^="#"]');
            
            anchorLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = link.getAttribute('href').substring(1);
                    const target = document.getElementById(targetId) || 
                                 document.querySelector(`[data-section="${targetId}"]`);
                    
                    if (target) {
                        this.scrollToSection(target);
                    }
                });
            });
            
            // Add smooth scrolling for category navigation
            const categoryLinks = document.querySelectorAll('.category-link');
            categoryLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    // Allow normal navigation but add smooth transition effect
                    link.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        link.style.transform = '';
                    }, 150);
                });
            });
        },
        
        scrollToSection: function(target) {
            const navbar = document.querySelector('.navbar');
            const headerHeight = navbar ? navbar.offsetHeight : 0;
            const targetPosition = target.offsetTop - headerHeight - 20;
            
            // Close mobile menu if open
            const navMenu = document.querySelector('.nav-menu');
            const mobileToggle = document.querySelector('.mobile-menu-toggle');
            if (navMenu && navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                mobileToggle.classList.remove('active');
                mobileToggle.setAttribute('aria-expanded', 'false');
                navMenu.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
            
            // Smooth scroll with easing
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
            
            // Add visual feedback
            target.style.transform = 'scale(1.02)';
            setTimeout(() => {
                target.style.transform = '';
            }, 300);
        },
        
        setupNavigationHighlighting: function() {
            const sections = document.querySelectorAll('section[id], section[data-section]');
            const navLinks = document.querySelectorAll('.nav-menu a[href^="#"]');
            
            if (sections.length === 0) return;
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const sectionId = entry.target.id || entry.target.dataset.section;
                        
                        // Update active navigation link
                        navLinks.forEach(link => {
                            link.classList.remove('active');
                            if (link.getAttribute('href') === `#${sectionId}`) {
                                link.classList.add('active');
                            }
                        });
                    }
                });
            }, {
                threshold: 0.3,
                rootMargin: '-100px 0px -100px 0px'
            });
            
            sections.forEach(section => observer.observe(section));
        }
    };
    
    // ===== INTERSECTION OBSERVERS FOR ANIMATION TRIGGERS =====
    const AnimationManager = {
        init: function() {
            this.setupScrollAnimations();
            this.setupParallaxEffects();
            this.setupCounterAnimations();
        },
        
        setupScrollAnimations: function() {
            // Enhanced animation triggers for various elements
            const animatedElements = document.querySelectorAll(
                '.product-card, .deal-card, .category-card, .section-title, .hero-content, .newsletter-section'
            );
            
            const animationObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        // Staggered animation delay
                        const delay = index * 100;
                        
                        setTimeout(() => {
                            entry.target.classList.add('animate-in');
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0) scale(1)';
                        }, delay);
                        
                        animationObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            // Prepare elements for animation
            animatedElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px) scale(0.95)';
                el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                animationObserver.observe(el);
            });
        },
        
        setupParallaxEffects: function() {
            const parallaxElements = document.querySelectorAll('.hero-product, .flagship-product');
            
            if (parallaxElements.length === 0) return;
            
            let ticking = false;
            
            const updateParallax = () => {
                const scrolled = window.pageYOffset;
                const rate = scrolled * -0.3;
                
                parallaxElements.forEach(el => {
                    el.style.transform = `translateY(${rate}px)`;
                });
                
                ticking = false;
            };
            
            const requestParallaxUpdate = () => {
                if (!ticking) {
                    requestAnimationFrame(updateParallax);
                    ticking = true;
                }
            };
            
            window.addEventListener('scroll', requestParallaxUpdate, { passive: true });
        },
        
        setupCounterAnimations: function() {
            const counters = document.querySelectorAll('.stock-text, .cart-badge');
            
            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.animateCounter(entry.target);
                        counterObserver.unobserve(entry.target);
                    }
                });
            });
            
            counters.forEach(counter => counterObserver.observe(counter));
        },
        
        animateCounter: function(element) {
            const text = element.textContent;
            const numbers = text.match(/\d+/);
            
            if (numbers) {
                const finalNumber = parseInt(numbers[0]);
                let currentNumber = 0;
                const increment = finalNumber / 30;
                
                const timer = setInterval(() => {
                    currentNumber += increment;
                    if (currentNumber >= finalNumber) {
                        currentNumber = finalNumber;
                        clearInterval(timer);
                    }
                    
                    element.textContent = text.replace(/\d+/, Math.floor(currentNumber));
                }, 50);
            }
        }
    };
    
    // ===== ENHANCED CART FUNCTIONALITY WITH LOADING STATES =====
    const EnhancedCartManager = {
        init: function() {
            this.setupLoadingStates();
            this.enhanceCartButtons();
            this.setupQuantityControls();
        },
        
        setupLoadingStates: function() {
            // Add loading states to all cart-related buttons
            const cartButtons = document.querySelectorAll('.btn-cosmic, .btn-deal-primary, .quick-add');
            
            cartButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    if (button.classList.contains('loading')) {
                        e.preventDefault();
                        return;
                    }
                    
                    this.showLoadingState(button);
                });
            });
        },
        
        showLoadingState: function(button) {
            const originalText = button.textContent;
            const originalHTML = button.innerHTML;
            
            button.classList.add('loading');
            button.disabled = true;
            button.innerHTML = `
                <span class="loading-spinner"></span>
                <span>Adding...</span>
            `;
            
            // Add loading spinner styles if not present
            this.addLoadingStyles();
            
            // Simulate loading (in real app, this would be the actual request duration)
            setTimeout(() => {
                this.hideLoadingState(button, originalHTML);
                this.showSuccessState(button, originalText);
            }, 1000);
        },
        
        hideLoadingState: function(button, originalHTML) {
            button.classList.remove('loading');
            button.disabled = false;
            button.innerHTML = originalHTML;
        },
        
        showSuccessState: function(button, originalText) {
            button.classList.add('success');
            button.innerHTML = `
                <span class="success-icon">✓</span>
                <span>Added!</span>
            `;
            
            setTimeout(() => {
                button.classList.remove('success');
                button.textContent = originalText;
            }, 2000);
        },
        
        addLoadingStyles: function() {
            if (document.querySelector('#cart-loading-styles')) return;
            
            const styles = document.createElement('style');
            styles.id = 'cart-loading-styles';
            styles.textContent = `
                .loading-spinner {
                    display: inline-block;
                    width: 16px;
                    height: 16px;
                    border: 2px solid rgba(255, 255, 255, 0.3);
                    border-radius: 50%;
                    border-top-color: var(--accent);
                    animation: spin 1s ease-in-out infinite;
                    margin-right: 8px;
                }
                
                @keyframes spin {
                    to { transform: rotate(360deg); }
                }
                
                .btn.loading {
                    opacity: 0.8;
                    cursor: not-allowed;
                }
                
                .btn.success {
                    background: linear-gradient(135deg, #22c55e, #16a34a);
                    transform: scale(1.05);
                }
                
                .success-icon {
                    display: inline-block;
                    margin-right: 8px;
                    animation: successPulse 0.6s ease-out;
                }
                
                @keyframes successPulse {
                    0% { transform: scale(0); }
                    50% { transform: scale(1.2); }
                    100% { transform: scale(1); }
                }
            `;
            document.head.appendChild(styles);
        },
        
        enhanceCartButtons: function() {
            // Override the existing quickAddToCart function with enhanced version
            window.quickAddToCart = (productId) => {
                const button = event.target;
                this.showLoadingState(button);
                
                fetch('cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=add&product_id=${productId}&quantity=1`
                })
                .then(response => response.text())
                .then(() => {
                    this.hideLoadingState(button, button.innerHTML);
                    this.showSuccessState(button, 'Quick Add');
                    showToast('Product added to cart!', 'success');
                    this.updateCartBadge();
                })
                .catch(error => {
                    this.hideLoadingState(button, button.innerHTML);
                    showToast('Error adding to cart', 'error');
                    console.error('Cart error:', error);
                });
            };
        },
        
        updateCartBadge: function() {
            // Enhanced cart badge update with animation
            const cartBadge = document.querySelector('.cart-badge');
            if (cartBadge) {
                cartBadge.style.transform = 'scale(1.3)';
                cartBadge.style.background = 'var(--accent)';
                
                setTimeout(() => {
                    cartBadge.style.transform = '';
                    cartBadge.style.background = '';
                }, 300);
            }
        },
        
        setupQuantityControls: function() {
            // Enhanced quantity controls with better UX - only for visible quantity inputs
            const quantityInputs = document.querySelectorAll('input[name="quantity"]:not([type="hidden"])');
            
            quantityInputs.forEach(input => {
                // Skip if input is hidden or in a product card (for one-click purchasing)
                if (input.type === 'hidden' || 
                    input.closest('.product-card') || 
                    input.closest('.deal-card') ||
                    input.style.display === 'none') {
                    return;
                }
                
                // Add visual feedback for quantity changes only for visible inputs
                input.addEventListener('change', (e) => {
                    const value = parseInt(e.target.value);
                    if (value > 1) {
                        e.target.style.borderColor = 'var(--accent)';
                        e.target.style.boxShadow = '0 0 0 2px rgba(0, 212, 255, 0.2)';
                    } else {
                        e.target.style.borderColor = '';
                        e.target.style.boxShadow = '';
                    }
                });
            });
        }
    };
    
    // ===== CATEGORY FILTERING WITH SEARCH INTEGRATION =====
    const CategoryFilterManager = {
        init: function() {
            this.setupCategoryFiltering();
            this.enhanceSearchFunctionality();
            this.setupFilterCombination();
        },
        
        setupCategoryFiltering: function() {
            const categoryCards = document.querySelectorAll('.category-card');
            const productCards = document.querySelectorAll('.product-card, .deal-card');
            
            // Add filter functionality to category cards
            categoryCards.forEach(card => {
                const categoryType = card.dataset.category;
                
                // Add filter button to each category card
                const filterButton = document.createElement('button');
                filterButton.className = 'category-filter-btn';
                filterButton.textContent = 'Filter Products';
                filterButton.setAttribute('data-category', categoryType);
                
                card.appendChild(filterButton);
                
                filterButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.filterByCategory(categoryType);
                });
            });
            
            // Add "Show All" filter button
            this.addShowAllButton();
        },
        
        addShowAllButton: function() {
            const categoriesSection = document.querySelector('.categories-section');
            if (!categoriesSection) return;
            
            const showAllButton = document.createElement('button');
            showAllButton.className = 'show-all-btn btn btn-electric-secondary';
            showAllButton.textContent = 'Show All Products';
            showAllButton.style.margin = '20px auto';
            showAllButton.style.display = 'block';
            
            showAllButton.addEventListener('click', () => {
                this.clearAllFilters();
            });
            
            categoriesSection.appendChild(showAllButton);
        },
        
        filterByCategory: function(category) {
            const productCards = document.querySelectorAll('.product-card, .deal-card');
            const searchInput = document.getElementById('searchInput');
            
            // Clear search input
            if (searchInput) {
                searchInput.value = '';
            }
            
            // Update active filter state
            this.updateFilterButtons(category);
            
            // Filter products
            productCards.forEach(card => {
                const productName = card.querySelector('h3')?.textContent.toLowerCase() || '';
                const productDesc = card.querySelector('.description, .deal-description')?.textContent.toLowerCase() || '';
                
                const matchesCategory = this.productMatchesCategory(productName, productDesc, category);
                
                if (matchesCategory) {
                    card.style.display = 'block';
                    card.style.animation = 'filterFadeIn 0.5s ease-out';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Scroll to products section
            const productsSection = document.getElementById('products');
            if (productsSection) {
                this.scrollToSection(productsSection);
            }
            
            // Show filter status
            this.showFilterStatus(category);
        },
        
        productMatchesCategory: function(name, description, category) {
            const categoryKeywords = {
                smartphones: ['phone', 'smartphone', 'mobile', 'iphone', 'android', 'galaxy'],
                laptops: ['laptop', 'macbook', 'notebook', 'computer', 'pc', 'ultrabook'],
                accessories: ['headphone', 'earphone', 'airpods', 'case', 'charger', 'cable', 'accessory'],
                smartwatches: ['watch', 'smartwatch', 'wearable', 'fitness', 'apple watch']
            };
            
            const keywords = categoryKeywords[category] || [];
            const text = `${name} ${description}`.toLowerCase();
            
            return keywords.some(keyword => text.includes(keyword));
        },
        
        updateFilterButtons: function(activeCategory) {
            const filterButtons = document.querySelectorAll('.category-filter-btn');
            
            filterButtons.forEach(button => {
                if (button.dataset.category === activeCategory) {
                    button.classList.add('active');
                    button.textContent = 'Filtering...';
                } else {
                    button.classList.remove('active');
                    button.textContent = 'Filter Products';
                }
            });
        },
        
        clearAllFilters: function() {
            const productCards = document.querySelectorAll('.product-card, .deal-card');
            const filterButtons = document.querySelectorAll('.category-filter-btn');
            const searchInput = document.getElementById('searchInput');
            
            // Show all products
            productCards.forEach(card => {
                card.style.display = 'block';
                card.style.animation = 'filterFadeIn 0.3s ease-out';
            });
            
            // Reset filter buttons
            filterButtons.forEach(button => {
                button.classList.remove('active');
                button.textContent = 'Filter Products';
            });
            
            // Clear search
            if (searchInput) {
                searchInput.value = '';
            }
            
            // Hide filter status
            this.hideFilterStatus();
        },
        
        enhanceSearchFunctionality: function() {
            const searchInput = document.getElementById('searchInput');
            if (!searchInput) return;
            
            // Override existing search with enhanced version
            let searchTimeout;
            
            const enhancedSearch = (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const searchTerm = e.target.value.toLowerCase();
                    this.performEnhancedSearch(searchTerm);
                }, 300);
            };
            
            // Remove existing event listeners and add enhanced one
            searchInput.removeEventListener('input', enhancedSearch);
            searchInput.addEventListener('input', enhancedSearch);
            
            // Add search suggestions
            this.addSearchSuggestions(searchInput);
        },
        
        performEnhancedSearch: function(searchTerm) {
            const productCards = document.querySelectorAll('.product-card, .deal-card');
            let visibleCount = 0;
            
            productCards.forEach(card => {
                const productName = card.querySelector('h3')?.textContent.toLowerCase() || '';
                const productDesc = card.querySelector('.description, .deal-description')?.textContent.toLowerCase() || '';
                
                const matchesSearch = searchTerm === '' || 
                                    productName.includes(searchTerm) || 
                                    productDesc.includes(searchTerm);
                
                if (matchesSearch) {
                    card.style.display = 'block';
                    card.style.animation = 'filterFadeIn 0.3s ease';
                    visibleCount++;
                    
                    // Highlight matching text
                    this.highlightSearchTerm(card, searchTerm);
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show search results status
            if (searchTerm) {
                this.showSearchStatus(searchTerm, visibleCount);
            } else {
                this.hideFilterStatus();
            }
        },
        
        highlightSearchTerm: function(card, term) {
            if (!term) return;
            
            const titleElement = card.querySelector('h3');
            const descElement = card.querySelector('.description, .deal-description');
            
            [titleElement, descElement].forEach(element => {
                if (element && element.textContent.toLowerCase().includes(term)) {
                    const originalText = element.dataset.originalText || element.textContent;
                    element.dataset.originalText = originalText;
                    
                    const regex = new RegExp(`(${term})`, 'gi');
                    element.innerHTML = originalText.replace(regex, '<mark>$1</mark>');
                }
            });
        },
        
        addSearchSuggestions: function(searchInput) {
            const suggestions = ['smartphone', 'laptop', 'headphones', 'smartwatch', 'accessories'];
            
            searchInput.addEventListener('focus', () => {
                if (!searchInput.value) {
                    searchInput.placeholder = `Try: ${suggestions[Math.floor(Math.random() * suggestions.length)]}`;
                }
            });
            
            searchInput.addEventListener('blur', () => {
                searchInput.placeholder = 'Search cosmic products...';
            });
        },
        
        setupFilterCombination: function() {
            // Allow combining search and category filters
            const searchInput = document.getElementById('searchInput');
            
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    const activeFilter = document.querySelector('.category-filter-btn.active');
                    if (activeFilter) {
                        // Combine search with active category filter
                        this.combineFilters(e.target.value, activeFilter.dataset.category);
                    }
                });
            }
        },
        
        combineFilters: function(searchTerm, category) {
            const productCards = document.querySelectorAll('.product-card, .deal-card');
            
            productCards.forEach(card => {
                const productName = card.querySelector('h3')?.textContent.toLowerCase() || '';
                const productDesc = card.querySelector('.description, .deal-description')?.textContent.toLowerCase() || '';
                
                const matchesSearch = !searchTerm || 
                                    productName.includes(searchTerm.toLowerCase()) || 
                                    productDesc.includes(searchTerm.toLowerCase());
                
                const matchesCategory = this.productMatchesCategory(productName, productDesc, category);
                
                if (matchesSearch && matchesCategory) {
                    card.style.display = 'block';
                    card.style.animation = 'filterFadeIn 0.3s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        },
        
        showFilterStatus: function(category) {
            this.hideFilterStatus(); // Remove existing status
            
            const statusElement = document.createElement('div');
            statusElement.className = 'filter-status';
            statusElement.innerHTML = `
                <span>Showing ${category} products</span>
                <button onclick="CategoryFilterManager.clearAllFilters()" class="clear-filter">Clear Filter ×</button>
            `;
            
            const productsSection = document.getElementById('products');
            if (productsSection) {
                productsSection.insertBefore(statusElement, productsSection.firstChild);
            }
            
            this.addFilterStatusStyles();
        },
        
        showSearchStatus: function(term, count) {
            this.hideFilterStatus();
            
            const statusElement = document.createElement('div');
            statusElement.className = 'filter-status search-status';
            statusElement.innerHTML = `
                <span>Found ${count} products for "${term}"</span>
                <button onclick="document.getElementById('searchInput').value=''; CategoryFilterManager.clearAllFilters()" class="clear-filter">Clear Search ×</button>
            `;
            
            const productsSection = document.getElementById('products');
            if (productsSection) {
                productsSection.insertBefore(statusElement, productsSection.firstChild);
            }
        },
        
        hideFilterStatus: function() {
            const existingStatus = document.querySelector('.filter-status');
            if (existingStatus) {
                existingStatus.remove();
            }
            
            // Remove highlights
            document.querySelectorAll('mark').forEach(mark => {
                mark.outerHTML = mark.innerHTML;
            });
        },
        
        addFilterStatusStyles: function() {
            if (document.querySelector('#filter-status-styles')) return;
            
            const styles = document.createElement('style');
            styles.id = 'filter-status-styles';
            styles.textContent = `
                .filter-status {
                    background: rgba(0, 212, 255, 0.1);
                    border: 1px solid var(--accent);
                    border-radius: 8px;
                    padding: 12px 16px;
                    margin: 20px 0;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    animation: slideDown 0.3s ease-out;
                }
                
                .clear-filter {
                    background: none;
                    border: none;
                    color: var(--accent);
                    cursor: pointer;
                    font-size: 14px;
                    padding: 4px 8px;
                    border-radius: 4px;
                    transition: all 0.3s ease;
                }
                
                .clear-filter:hover {
                    background: rgba(0, 212, 255, 0.2);
                }
                
                .category-filter-btn {
                    background: rgba(0, 212, 255, 0.1);
                    border: 1px solid var(--accent);
                    color: var(--accent);
                    padding: 8px 16px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 12px;
                    margin-top: 10px;
                    transition: all 0.3s ease;
                }
                
                .category-filter-btn:hover {
                    background: var(--accent);
                    color: var(--bg-primary);
                }
                
                .category-filter-btn.active {
                    background: var(--accent);
                    color: var(--bg-primary);
                }
                
                mark {
                    background: rgba(0, 212, 255, 0.3);
                    color: var(--text-primary);
                    padding: 2px 4px;
                    border-radius: 3px;
                }
                
                @keyframes slideDown {
                    from {
                        opacity: 0;
                        transform: translateY(-20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                
                @keyframes filterFadeIn {
                    from {
                        opacity: 0;
                        transform: scale(0.9);
                    }
                    to {
                        opacity: 1;
                        transform: scale(1);
                    }
                }
            `;
            document.head.appendChild(styles);
        },
        
        scrollToSection: function(target) {
            const navbar = document.querySelector('.navbar');
            const headerHeight = navbar ? navbar.offsetHeight : 0;
            const targetPosition = target.offsetTop - headerHeight - 20;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    };
    
    // ===== INITIALIZE ALL MANAGERS =====
    SmoothScrollManager.init();
    AnimationManager.init();
    EnhancedCartManager.init();
    CategoryFilterManager.init();
    
    // Make managers globally available for debugging
    window.EnhancedInteractivity = {
        SmoothScrollManager,
        AnimationManager,
        EnhancedCartManager,
        CategoryFilterManager
    };
    
    console.log('🚀 Enhanced interactivity loaded successfully!');
    console.log('✨ Features: Smooth scrolling, animations, enhanced cart, category filtering');
});