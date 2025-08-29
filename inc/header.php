<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'CosmicCart - Discover the Universe'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/performance.js" defer></script>
    <script src="assets/js/cart.js" defer></script>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⭐</text></svg>">
    
    <!-- Performance and Mobile Optimizations -->
    <meta name="theme-color" content="#0a0a0f">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ElectroCart">
    
    <!-- Preconnect to external resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- DNS prefetch for better performance -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    
    <!-- Manifest for PWA capabilities -->
    <link rel="manifest" href="/manifest.json">
</head>
<body>
    <!-- Floating Cosmic Orbs -->
    <div class="cosmic-orb orb-1"></div>
    <div class="cosmic-orb orb-2"></div>
    <div class="cosmic-orb orb-3"></div>

    <nav class="navbar">
        <div class="nav-container">
            <h1><a href="index.php">ElectroCart</a></h1>
            
            <div class="search-container">
                <input type="text" placeholder="Search cosmic products..." id="searchInput">
            </div>
            
            <ul class="nav-menu" role="navigation" aria-hidden="true">
                <li><a href="index.php" aria-label="Go to homepage">Home</a></li>
                <li><a href="product.php" aria-label="Browse all products">Products</a></li>
                <li><a href="#deals" aria-label="View deals">Deals</a></li>
                <li>
                    <a href="cart.php" class="cart-icon" aria-label="View shopping cart">
                        🛒
                        <?php 
                        $cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                        if ($cart_count > 0): 
                        ?>
                            <span class="cart-badge" aria-label="<?php echo $cart_count; ?> items in cart"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li><a href="admin/login.php" aria-label="Admin login">Admin</a></li>
            </ul>
            
            <button class="mobile-menu-toggle" 
                    onclick="toggleMobileMenu()" 
                    aria-expanded="false" 
                    aria-controls="nav-menu" 
                    aria-label="Toggle navigation menu">
                ☰
            </button>
        </div>
    </nav>

    <script>
        // Enhanced mobile menu toggle with accessibility
        function toggleMobileMenu() {
            const navMenu = document.querySelector('.nav-menu');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            navMenu.classList.toggle('active');
            toggle.classList.toggle('active');
            
            const isExpanded = navMenu.classList.contains('active');
            toggle.setAttribute('aria-expanded', isExpanded);
            navMenu.setAttribute('aria-hidden', !isExpanded);
            
            // Prevent body scroll when menu is open
            if (isExpanded) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        // Enhanced search functionality with debouncing
        let searchTimeout;
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
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
        });

        // Enhanced toast notification system
        function showToast(message, type = 'success') {
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
            
            // Haptic feedback on supported devices
            if ('vibrate' in navigator) {
                navigator.vibrate(type === 'success' ? 50 : [50, 50, 50]);
            }
        }
    </script>

    <div class="container"> 
