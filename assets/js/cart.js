// Cart functionality enhancements
document.addEventListener('DOMContentLoaded', function() {
    
    // Add to cart animation
    const addToCartButtons = document.querySelectorAll('.btn-success');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Add a small animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
            
            // Debug: Log button click
            console.log('Add to cart button clicked:', this);
        });
    });
    
    // Quantity input validation
    const quantityInputs = document.querySelectorAll('input[name="quantity"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const value = parseInt(this.value);
            const min = parseInt(this.min) || 1;
            const max = parseInt(this.max) || 999;
            
            if (value < min) {
                this.value = min;
            } else if (value > max) {
                this.value = max;
            }
        });
    });
    
    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
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
    
    // Form validation enhancement - REMOVED to prevent interference with cart forms
    // const forms = document.querySelectorAll('form');
    // forms.forEach(form => {
    //     form.addEventListener('submit', function(e) {
    //         const requiredFields = form.querySelectorAll('[required]');
    //         let isValid = true;
    //         
    //         requiredFields.forEach(field => {
    //             if (!field.value.trim()) {
    //                 isValid = false;
    //                 field.style.borderColor = '#e74c3c';
    //             } else {
    //                 field.style.borderColor = '#e1e5e9';
    //             }
    //         });
    //         
    //         if (!isValid) {
    //             e.preventDefault();
    //             alert('Please fill in all required fields.');
    //         }
    //     });
    // });
    
    // Auto-hide success messages
    const successMessages = document.querySelectorAll('.message.success');
    successMessages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.remove();
            }, 300);
        }, 3000);
    });
    
    // Product card hover effects
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Mobile menu toggle (if needed in future)
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (mobileMenuToggle && navMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }
    
    // Lazy loading for images (basic implementation)
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
    
    // Cart count update (if using AJAX)
    function updateCartCount() {
        const cartCount = document.querySelector('.nav-menu a[href="cart.php"]');
        if (cartCount) {
            // This would be updated via AJAX in a real implementation
            // For now, it's handled by PHP session
        }
    }
    
    // Add loading states to buttons - REMOVED to prevent interference
    // const submitButtons = document.querySelectorAll('button[type="submit"]');
    // submitButtons.forEach(button => {
    //     button.addEventListener('click', function() {
    //         if (this.form && this.form.checkValidity()) {
    //             this.disabled = true;
    //             this.textContent = 'Processing...';
    //             
    //             // Re-enable after form submission
    //             setTimeout(() => {
    //                 this.disabled = false;
    //                 this.textContent = this.getAttribute('data-original-text') || 'Submit';
    //             }, 2000);
    //         }
    //     });
    // });
    
    // Store original button text
    // submitButtons.forEach(button => {
    //     button.setAttribute('data-original-text', button.textContent);
    // });
    
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
        updateCartCount: updateCartCount
    };
    
    // Debug: Log all forms on the page
    console.log('Forms found on page:', document.querySelectorAll('form').length);
    document.querySelectorAll('form').forEach((form, index) => {
        console.log(`Form ${index}:`, form.action, form.method);
    });
});

// Console message for developers
console.log('🎉 E-Commerce Cart JS loaded successfully!');
console.log('📱 This site is fully responsive and mobile-friendly');
console.log('🛒 Cart functionality is working with PHP sessions'); 
