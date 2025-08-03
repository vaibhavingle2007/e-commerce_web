<?php
require_once 'inc/db.php';

// Define the categories and their display order
$categories = [
    'Mobile' => 'Mobile',
    'Laptop' => 'Laptop',
    'Keyboard and Mouse' => 'Keyboard and Mouse',
    'Headphones' => 'Headphones',
    'Accessories' => 'Accessories',
];

// Fetch all products with stock > 0, grouped by category
$result = $conn->query("SELECT * FROM products WHERE stock > 0 ORDER BY FIELD(category, 'Mobile', 'Laptop', 'Keyboard and Mouse', 'Headphones', 'Accessories'), category, created_at DESC");

// Organize products by category
$products_by_category = [];
while ($row = $result->fetch_assoc()) {
    $cat = $row['category'] ?: 'Other';
    $products_by_category[$cat][] = $row;
}

$page_title = "All Products - CosmicCart";
include 'inc/header.php';
?>

<!-- Products Hero Section -->
<section class="hero-section" style="padding: 60px 20px;">
    <h1 class="hero-title">Cosmic Collection</h1>
    <p class="hero-subtitle">Explore our stellar selection of premium products from across the galaxy</p>
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

<?php foreach ($categories as $cat_key => $cat_label): ?>
    <?php if (!empty($products_by_category[$cat_key])): ?>
        <section style="margin: 60px 0;">
            <h2 style="margin: 40px 0 30px 0; font-size: 2.5rem; background: linear-gradient(135deg, var(--primary), var(--accent)); background-clip: text; -webkit-background-clip: text; color: transparent; text-align: center; position: relative;">
                <?php echo htmlspecialchars($cat_label); ?>
                <div style="position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 100px; height: 3px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 2px;"></div>
            </h2>
            <div class="product-grid">
                <?php foreach ($products_by_category[$cat_key] as $product): ?>
                    <div class="product-card">
                        <?php if (strtotime($product['created_at']) > strtotime('-7 days')): ?>
                            <div class="product-badge badge-new">New</div>
                        <?php endif; ?>
                        
                        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             onerror="this.src='assets/images/placeholder.jpg'">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="description"><?php echo htmlspecialchars(substr($product['description'], 0, 120)) . '...'; ?></p>
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
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
<?php endforeach; ?>

<?php if (!empty($products_by_category['Other'])): ?>
    <section style="margin: 60px 0;">
        <h2 style="margin: 40px 0 30px 0; font-size: 2.5rem; background: linear-gradient(135deg, var(--primary), var(--accent)); background-clip: text; -webkit-background-clip: text; color: transparent; text-align: center; position: relative;">
            Other Products
            <div style="position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 100px; height: 3px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 2px;"></div>
        </h2>
        <div class="product-grid">
            <?php foreach ($products_by_category['Other'] as $product): ?>
                <div class="product-card">
                    <?php if (strtotime($product['created_at']) > strtotime('-7 days')): ?>
                        <div class="product-badge badge-new">New</div>
                    <?php endif; ?>
                    
                    <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         onerror="this.src='assets/images/placeholder.jpg'">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="description"><?php echo htmlspecialchars(substr($product['description'], 0, 120)) . '...'; ?></p>
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
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

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
</script>

<?php include 'inc/footer.php'; ?> 
