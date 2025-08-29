// Enhanced Cart functionality with AJAX support
document.addEventListener('DOMContentLoaded', function() {
    
    // Performance optimization: Use passive event listeners where possible
    const passiveOptions = { passive: true };
    
    // Enhanced mobile menu functionality
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (mobileMenuToggle && navMenu) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            navMenu.classList.toggle('active');
            this.classList.toggle('active');
            
            // Update ARIA attributes for accessibility
            const isExpanded = navMenu.classList.contains('active');
            this.setAttribute('aria-expanded', isExpanded);
            navMenu.setAttribute('aria-hidden', !isExpanded);
            
            // Prevent body scroll when menu is open on mobile
            if (isExpanded) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navMenu.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                navMenu.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
                navMenu.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
        });
        
        // Close menu on escape key
        document.addEventListener('click', function(e) {
            if (e.key === 'Escape' && navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
                navMenu.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                mobileMenuToggle.focus();
            }
        });
    }
    
    // Enhanced touch-friendly interactions
    function isTouchDevice() {
        return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    }
    
    // CART FUNCTIONALITY - THE MAIN ISSUE WAS HERE
    class CartManager {
        constructor() {
            this.cart = {};
            this.cartCount = 0;
            this.init();
        }
        
        init() {
            this.loadCartFromSession();
            this.setupCartButtons();
            this.syncWithServerCart();
        }
        
        async syncWithServerCart() {
            // Sync with server cart count on page load
            try {
                const response = await fetch('cart.php?action=get_count', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        // Update local cart with server data
                        if (result.cart_items) {
                            this.cart = result.cart_items;
                            this.saveCartToSession();
                        }
                        this.updateCartBadge(result.cart_count);
                        console.log('Cart synced with server. Count:', result.cart_count);
                    }
                } else {
                    console.warn('Failed to sync cart with server:', response.status);
                    this.updateCartBadge();
                }
            } catch (error) {
                console.warn('Could not sync cart count:', error);
                // Fallback to current implementation
                this.updateCartBadge();
            }
        }
        
        loadCartFromSession() {
            // Load cart from session storage or initialize empty
            const savedCart = sessionStorage.getItem('cart');
            if (savedCart) {
                try {
                    this.cart = JSON.parse(savedCart);
                    this.cartCount = Object.values(this.cart).reduce((sum, qty) => sum + qty, 0);
                } catch (e) {
                    console.error('Error loading cart:', e);
                    this.cart = {};
                    this.cartCount = 0;
                }
            }
        }
        
        saveCartToSession() {
            sessionStorage.setItem('cart', JSON.stringify(this.cart));
        }
        
        setupCartButtons() {
            // Setup all add to cart buttons
            const addToCartButtons = document.querySelectorAll('.btn-cosmic, .btn-deal-primary');
            addToCartButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.handleAddToCart(e.target);
                });
                
                // Touch feedback
                button.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.95)';
                }, passiveOptions);
                
                button.addEventListener('touchend', function() {
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                }, passiveOptions);
            });
            
            // Setup quick add buttons
            const quickAddButtons = document.querySelectorAll('.quick-add');
            quickAddButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const productId = button.getAttribute('data-product-id') || 
                                    button.onclick.toString().match(/\d+/)?.[0];
                    if (productId) {
                        this.addToCart(productId, 1);
                    }
                });
            });
        }
        
        handleAddToCart(button) {
            const form = button.closest('form');
            if (form) {
                const formData = new FormData(form);
                const productId = formData.get('product_id');
                const quantity = parseInt(formData.get('quantity')) || 1;
                
                if (productId) {
                    this.addToCart(productId, quantity);
                }
            }
        }
        
        async addToCart(productId, quantity = 1) {
            try {
                // Show loading state
                this.showLoadingState(productId);
                
                // Send AJAX request to add to cart
                const response = await fetch('cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `action=add&product_id=${productId}&quantity=${quantity}`
                });
                
                if (response.ok) {
                    const result = await response.json();
                    
                    if (result.success) {
                        // Update local cart
                        if (this.cart[productId]) {
                            this.cart[productId] += quantity;
                        } else {
                            this.cart[productId] = quantity;
                        }
                        
                        // Save to session storage
                        this.saveCartToSession();
                        
                        // Update cart badge with server count (most reliable)
                        this.updateCartBadge(result.cart_count);
                        
                        // Show success message
                        this.showToast(result.message || 'Product added to cart!', 'success');
                        
                        // Add success animation
                        this.showSuccessAnimation(productId);
                        
                    } else {
                        throw new Error(result.message || 'Failed to add to cart');
                    }
                } else {
                    throw new Error('Failed to add to cart');
                }
                
            } catch (error) {
                console.error('Error adding to cart:', error);
                this.showToast('Error adding to cart. Please try again.', 'error');
            } finally {
                // Hide loading state
                this.hideLoadingState(productId);
            }
        }
        
        showLoadingState(productId) {
            const button = document.querySelector(`[data-product-id="${productId}"]`) || 
                          document.querySelector(`button[onclick*="${productId}"]`);
            if (button) {
                button.disabled = true;
                button.innerHTML = '<span class="loading-spinner"></span> Adding...';
            }
        }
        
        hideLoadingState(productId) {
            const button = document.querySelector(`[data-product-id="${productId}"]`) || 
                          document.querySelector(`button[onclick*="${productId}"]`);
            if (button) {
                button.disabled = false;
                button.innerHTML = 'Add to Cart';
            }
        }
        
        showSuccessAnimation(productId) {
            const button = document.querySelector(`[data-product-id="${productId}"]`) || 
                          document.querySelector(`button[onclick*="${productId}"]`);
            if (button) {
                button.classList.add('success-animation');
                setTimeout(() => {
                    button.classList.remove('success-animation');
                }, 1000);
            }
        }
        
        updateCartBadge(serverCartCount = null) {
            // Use server cart count if provided, otherwise use local count
            const displayCount = serverCartCount !== null ? serverCartCount : this.cartCount;
            
            let cartBadge = document.querySelector('.cart-badge');
            const cartIcon = document.querySelector('.cart-icon');
            
            if (displayCount > 0) {
                if (!cartBadge && cartIcon) {
                    // Create cart badge if it doesn't exist
                    cartBadge = document.createElement('span');
                    cartBadge.className = 'cart-badge';
                    cartIcon.appendChild(cartBadge);
                }
                
                if (cartBadge) {
                    cartBadge.textContent = displayCount;
                    cartBadge.style.display = 'flex';
                    cartBadge.setAttribute('aria-label', `${displayCount} items in cart`);
                    
                    // Add animation for visual feedback
                    cartBadge.style.transform = 'scale(1.3)';
                    setTimeout(() => {
                        cartBadge.style.transform = 'scale(1)';
                    }, 200);
                }
            } else {
                // Hide badge when cart is empty
                if (cartBadge) {
                    cartBadge.style.display = 'none';
                }
            }
            
            // Update local cart count to match server
            if (serverCartCount !== null) {
                this.cartCount = serverCartCount;
            }
        }
        
        showToast(message, type = 'success') {
            // Remove existing toasts
            const existingToasts = document.querySelectorAll('.toast');
            existingToasts.forEach(toast => toast.remove());
            
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <span class="toast-message">${message}</span>
                <button class="toast-close" onclick="this.parentElement.remove()" aria-label="Close notification">×</button>
            `;
            
            // Add toast styles if not already present
            if (!document.querySelector('#toast-styles')) {
                const toastStyles = document.createElement('style');
                toastStyles.id = 'toast-styles';
                toastStyles.textContent = `
                    .toast {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background: rgba(26, 26, 46, 0.95);
                        color: var(--text-primary);
                        padding: 16px 20px;
                        border-radius: 12px;
                        border: 1px solid;
                        backdrop-filter: blur(20px);
                        z-index: 10000;
                        display: flex;
                        align-items: center;
                        gap: 12px;
                        min-width: 300px;
                        max-width: 400px;
                        transform: translateX(100%);
                        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                    }
                    .toast.success {
                        border-color: #22c55e;
                        background: rgba(34, 197, 94, 0.1);
                    }
                    .toast.error {
                        border-color: #ef4444;
                        background: rgba(239, 68, 68, 0.1);
                    }
                    .toast.show {
                        transform: translateX(0);
                    }
                    .toast-message {
                        flex: 1;
                        font-weight: 500;
                    }
                    .toast-close {
                        background: none;
                        border: none;
                        color: var(--text-secondary);
                        font-size: 1.5rem;
                        cursor: pointer;
                        padding: 0;
                        width: 24px;
                        height: 24px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 50%;
                        transition: all 0.3s ease;
                    }
                    .toast-close:hover {
                        background: rgba(255, 255, 255, 0.1);
                        color: var(--text-primary);
                    }
                    .loading-spinner {
                        display: inline-block;
                        width: 16px;
                        height: 16px;
                        border: 2px solid #ffffff;
                        border-radius: 50%;
                        border-top-color: transparent;
                        animation: spin 1s ease-in-out infinite;
                    }
                    @keyframes spin {
                        to { transform: rotate(360deg); }
                    }
                    .success-animation {
                        animation: successPulse 0.5s ease-in-out;
                    }
                    @keyframes successPulse {
                        0% { transform: scale(1); }
                        50% { transform: scale(1.1); }
                        100% { transform: scale(1); }
                    }
                    @media (max-width: 480px) {
                        .toast {
                            right: 10px;
                            left: 10px;
                            min-width: auto;
                            max-width: none;
                        }
                    }
                `;
                document.head.appendChild(toastStyles);
            }
            
            document.body.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => toast.classList.add('show'), 100);
            
            // Auto-remove after 4 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }, 4000);
        }
    }
    
    // Initialize cart manager
    const cartManager = new CartManager();
    
    // Enhanced product card interactions for mobile
    const productCards = document.querySelectorAll('.product-card, .deal-card, .category-card');
    productCards.forEach(card => {
        if (!isTouchDevice()) {
            // Desktop hover effects
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            }, passiveOptions);
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = '';
            }, passiveOptions);
        } else {
            // Touch device interactions
            card.addEventListener('touchstart', function() {
                this.style.transform = 'translateY(-4px) scale(1.01)';
            }, passiveOptions);
            
            card.addEventListener('touchend', function() {
                setTimeout(() => {
                    this.style.transform = '';
                }, 200);
            }, passiveOptions);
        }
    });
    
    // Optimized smooth scrolling with intersection observer
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const target = document.getElementById(targetId) || document.querySelector(`[name="${targetId}"]`);
            
            if (target) {
                // Close mobile menu if open
                if (navMenu && navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    mobileMenuToggle.classList.remove('active');
                    document.body.style.overflow = '';
                }
                
                // Smooth scroll with offset for fixed header
                const headerHeight = document.querySelector('.navbar')?.offsetHeight || 0;
                const targetPosition = target.offsetTop - headerHeight - 20;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Enhanced lazy loading with better performance
    const images = document.querySelectorAll('img[data-src]');
    if (images.length > 0) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.1
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
    
    // Quantity input validation with better UX
    const quantityInputs = document.querySelectorAll('input[name="quantity"]:not([type="hidden"])');
    quantityInputs.forEach(input => {
        // Skip if input is hidden or in a product card (for one-click purchasing)
        if (input.type === 'hidden' || 
            input.closest('.product-card') || 
            input.closest('.deal-card') ||
            input.style.display === 'none') {
            return;
        }
        
        // Add touch-friendly increment/decrement buttons only for visible quantity inputs
        const wrapper = document.createElement('div');
        wrapper.className = 'quantity-wrapper';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        
        const decrementBtn = document.createElement('button');
        decrementBtn.type = 'button';
        decrementBtn.className = 'quantity-btn decrement';
        decrementBtn.innerHTML = '−';
        decrementBtn.setAttribute('aria-label', 'Decrease quantity');
        
        const incrementBtn = document.createElement('button');
        incrementBtn.type = 'button';
        incrementBtn.className = 'quantity-btn increment';
        incrementBtn.innerHTML = '+';
        incrementBtn.setAttribute('aria-label', 'Increase quantity');
        
        wrapper.insertBefore(decrementBtn, input);
        wrapper.appendChild(incrementBtn);
        
        // Button event listeners
        decrementBtn.addEventListener('click', function() {
            const currentValue = parseInt(input.value) || 1;
            const minValue = parseInt(input.min) || 1;
            if (currentValue > minValue) {
                input.value = currentValue - 1;
                input.dispatchEvent(new Event('change'));
            }
        });
        
        incrementBtn.addEventListener('click', function() {
            const currentValue = parseInt(input.value) || 1;
            const maxValue = parseInt(input.max) || 999;
            if (currentValue < maxValue) {
                input.value = currentValue + 1;
                input.dispatchEvent(new Event('change'));
            }
        });
        
        // Input validation
        input.addEventListener('change', function() {
            const value = parseInt(this.value);
            const min = parseInt(this.min) || 1;
            const max = parseInt(this.max) || 999;
            
            if (isNaN(value) || value < min) {
                this.value = min;
            } else if (value > max) {
                this.value = max;
            }
        });
    });
    
    // Auto-hide success messages with better animation
    const successMessages = document.querySelectorAll('.message.success');
    successMessages.forEach(message => {
        setTimeout(() => {
            message.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            message.style.opacity = '0';
            message.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                message.remove();
            }, 300);
        }, 3000);
    });
    
    // Performance optimization: Debounced search
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = e.target.value.toLowerCase();
                const productCards = document.querySelectorAll('.product-card, .deal-card');
                
                productCards.forEach(card => {
                    const productName = card.querySelector('h3')?.textContent.toLowerCase() || '';
                    const productDesc = card.querySelector('.description, .deal-description')?.textContent.toLowerCase() || '';
                    
                    if (productName.includes(searchTerm) || productDesc.includes(searchTerm) || searchTerm === '') {
                        card.style.display = 'block';
                        card.style.animation = 'fadeIn 0.3s ease';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }, 300);
        });
    }
    
    // Intersection Observer for animations
    const animatedElements = document.querySelectorAll('.product-card, .deal-card, .category-card, .section-title');
    if (animatedElements.length > 0) {
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'smooth-slide-up 0.6s ease-out forwards';
                    animationObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        animatedElements.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            animationObserver.observe(el);
        });
    }
    
    // Price formatting helper
    function formatPrice(price) {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR'
        }).format(price);
    }
    
    // Export functions for potential use
    window.CartUtils = {
        formatPrice: formatPrice,
        cartManager: cartManager,
        isTouchDevice: isTouchDevice,
        refreshCartBadge: function() {
            if (cartManager) {
                cartManager.syncWithServerCart();
            }
        }
    };
    
    // Performance monitoring (development only)
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.log('🎉 Enhanced E-Commerce JS loaded successfully!');
        console.log('📱 Mobile optimizations active');
        console.log('🚀 Performance optimizations enabled');
        console.log('♿ Accessibility enhancements loaded');
        console.log('🛒 Cart functionality initialized');
        
        // Log performance metrics
        window.addEventListener('load', function() {
            setTimeout(() => {
                const perfData = performance.getEntriesByType('navigation')[0];
                if (perfData) {
                    console.log('📊 Page load time:', Math.round(perfData.loadEventEnd - perfData.fetchStart), 'ms');
                }
            }, 0);
        });
    }
});

// Console message for developers
console.log('🎉 E-Commerce Cart JS loaded successfully!');
console.log('📱 This site is fully responsive and mobile-friendly');
console.log('🛒 Cart functionality is now working with AJAX support!'); 
