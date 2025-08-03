<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'CosmicCart - Discover the Universe'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/cart.js" defer></script>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⭐</text></svg>">
</head>
<body>
    <!-- Floating Cosmic Orbs -->
    <div class="cosmic-orb orb-1"></div>
    <div class="cosmic-orb orb-2"></div>
    <div class="cosmic-orb orb-3"></div>

    <nav class="navbar">
        <div class="nav-container">
            <h1><a href="index.php">CosmicCart</a></h1>
            
            <div class="search-container">
                <input type="text" placeholder="Search cosmic products..." id="searchInput">
            </div>
            
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="product.php">Products</a></li>
                <li>
                    <a href="cart.php" class="cart-icon">
                        🛒
                        <?php if (isset($_SESSION['cart']) && array_sum($_SESSION['cart']) > 0): ?>
                            <span class="cart-badge"><?php echo array_sum($_SESSION['cart']); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li><a href="admin/login.php">Admin</a></li>
            </ul>
            
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
        </div>
    </nav>

    <script>
        function toggleMobileMenu() {
            const navMenu = document.querySelector('.nav-menu');
            navMenu.classList.toggle('active');
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                const productName = card.querySelector('h3').textContent.toLowerCase();
                const productDesc = card.querySelector('.description')?.textContent.toLowerCase() || '';
                
                if (productName.includes(searchTerm) || productDesc.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Toast notification system
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => document.body.removeChild(toast), 300);
            }, 3000);
        }
    </script>

    <div class="container"> 
