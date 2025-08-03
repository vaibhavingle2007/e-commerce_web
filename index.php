<?php
require_once 'inc/db.php';

$page_title = "CosmicCart - Discover the Universe";
include 'inc/header.php';

$result = $conn->query("SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC");
?>

<!-- Hero Section -->
<section class="hero-section">
    <h1 class="hero-title">Discover the Universe</h1>
    <p class="hero-subtitle">Explore our cosmic collection of premium products from across the galaxy</p>
    <div style="margin-top: 2rem;">
        <a href="product.php" class="btn btn-cosmic">Shop Collection</a>
        <a href="#products" class="btn btn-glass">Learn More</a>
    </div>
</section>

<?php if (isset($_SESSION['cart_message'])): ?>
    <div class="message <?php echo $_SESSION['cart_message_type']; ?>">
        <?php echo $_SESSION['cart_message']; ?>
    </div>
    <?php 
    unset($_SESSION['cart_message']);
    unset($_SESSION['cart_message_type']);
    ?>
<?php endif; ?>

<!-- Featured Products Section -->
<section id="products">
    <h2 style="text-align: center; margin: 50px 0 30px 0; font-size: 2.5rem; background: linear-gradient(135deg, var(--primary), var(--accent)); background-clip: text; -webkit-background-clip: text; color: transparent;">Featured Products</h2>
    
    <div class="product-grid">
        <?php while ($product = $result->fetch_assoc()): ?>
            <div class="product-card">
                <?php if (strtotime($product['created_at']) > strtotime('-7 days')): ?>
                    <div class="product-badge badge-new">New</div>
                <?php endif; ?>
                
                <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     onerror="this.src='assets/images/placeholder.jpg'">
                
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
                
                <div class="btn-container">
                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-glass">View Details</a>
                    
                    <form method="POST" action="cart.php" style="display: inline;">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-cosmic" onclick="showToast('Product added to cart!', 'success')">Add to Cart</button>
                    </form>
                </div>
                
                <button class="quick-add" onclick="quickAddToCart(<?php echo $product['id']; ?>)">Quick Add</button>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section">
    <h2>Join the Cosmic Community</h2>
    <p>Subscribe to our newsletter for exclusive cosmic deals and stellar updates</p>
    <form class="newsletter-form" onsubmit="subscribeNewsletter(event)">
        <input type="email" placeholder="Enter your email address" required>
        <button type="submit" class="btn btn-cosmic">Subscribe</button>
    </form>
</section>

<script>
    function quickAddToCart(productId) {
        fetch('cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=add&product_id=${productId}&quantity=1`
        })
        .then(response => response.text())
        .then(() => {
            showToast('Product added to cart!', 'success');
            // Update cart badge
            setTimeout(() => location.reload(), 1000);
        })
        .catch(error => {
            showToast('Error adding to cart', 'error');
        });
    }

    function subscribeNewsletter(event) {
        event.preventDefault();
        const email = event.target.querySelector('input[type="email"]').value;
        showToast('Thank you for subscribing to our cosmic newsletter!', 'success');
        event.target.reset();
    }

    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
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
</script>

<?php include 'inc/footer.php'; ?> 
