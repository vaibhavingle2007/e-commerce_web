<?php
require_once 'inc/db.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header('Location: product.php');
    exit;
}

$page_title = $product['name'] . " - CosmicCart";
include 'inc/header.php';
?>

<div class="product-detail">
    <div class="product-image">
        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
             alt="<?php echo htmlspecialchars($product['name']); ?>"
             onerror="this.src='assets/images/placeholder.jpg'">
    </div>
    
    <div class="product-info">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
        <div class="stock">Stock: <?php echo $product['stock']; ?> available</div>
        
        <p style="margin: 20px 0; line-height: 1.6; color: var(--text-secondary);">
            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
        </p>
        
        <?php if ($product['stock'] > 0): ?>
            <form method="POST" action="cart.php" id="addToCartForm">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" 
                           max="<?php echo $product['stock']; ?>" style="width: 100px;">
                </div>
                
                <div class="btn-container">
                    <button type="submit" class="btn btn-cosmic" onclick="showAddToCartToast(event)">Add to Cart</button>
                    <a href="product.php" class="btn btn-glass">Back to Products</a>
                </div>
            </form>
        <?php else: ?>
            <div style="color: var(--error); font-weight: bold; margin: 20px 0;">
                Out of Stock
            </div>
            <div class="btn-container">
                <a href="product.php" class="btn btn-glass">Back to Products</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function showAddToCartToast(event) {
    event.preventDefault(); // Prevent immediate form submission
    
    // Show toast notification (using the function from header.php)
    showToast('Product added to cart!', 'success');
    
    // Submit form after a short delay
    setTimeout(() => {
        document.getElementById('addToCartForm').submit();
    }, 500);
}
</script>

<?php include 'inc/footer.php'; ?> 