<?php
require_once 'inc/db.php';

$page_title = "E-Store - Home";
include 'inc/header.php';

$result = $conn->query("SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC");
?>

<h1 style="text-align: center; margin: 30px 0; color: #2c3e50; font-size: 2.5rem;">Our Products</h1>

<?php if (isset($_SESSION['cart_message'])): ?>
    <div class="message <?php echo $_SESSION['cart_message_type']; ?>" style="text-align: center; margin: 20px 0; padding: 15px; border-radius: 5px; background-color: <?php echo $_SESSION['cart_message_type'] === 'success' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $_SESSION['cart_message_type'] === 'success' ? '#155724' : '#721c24'; ?>; border: 1px solid <?php echo $_SESSION['cart_message_type'] === 'success' ? '#c3e6cb' : '#f5c6cb'; ?>;">
        <?php echo $_SESSION['cart_message']; ?>
    </div>
    <?php 
    // Clear the message after displaying
    unset($_SESSION['cart_message']);
    unset($_SESSION['cart_message_type']);
    ?>
<?php endif; ?>

<div class="product-grid">
    <?php while ($product = $result->fetch_assoc()): ?>
        <div class="product-card">
            <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                 onerror="this.src='assets/images/placeholder.jpg'">
            
            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <p class="description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
            <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
            
            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn">View Details</a>
            
            <form method="POST" action="cart.php" style="display: inline;">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-success">Add to Cart</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

<?php include 'inc/footer.php'; ?> 
