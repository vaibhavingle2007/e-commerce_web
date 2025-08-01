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

$page_title = $product['name'] . " - E-Commerce Store";
include 'inc/header.php';
?>

<div class="product-detail">
    <?php if (isset($_SESSION['cart_message'])): ?>
        <div class="message <?php echo $_SESSION['cart_message_type']; ?>" style="text-align: center; margin: 20px 0; padding: 15px; border-radius: 5px; background-color: <?php echo $_SESSION['cart_message_type'] === 'success' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $_SESSION['cart_message_type'] === 'success' ? '#155724' : '#721c24'; ?>; border: 1px solid <?php echo $_SESSION['cart_message_type'] === 'success' ? '#c3e6cb' : '#f5c6cb'; ?>;">
            <?php echo $_SESSION['cart_message']; ?>
        </div>
        <?php 
        unset($_SESSION['cart_message']);
        unset($_SESSION['cart_message_type']);
        ?>
    <?php endif; ?>
    
    <div class="product-image">
        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
             alt="<?php echo htmlspecialchars($product['name']); ?>"
             style="width: 100%; max-width: 400px; border-radius: 8px;"
             onerror="this.src='assets/images/placeholder.jpg'">
    </div>
    
    <div class="product-info">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
        <div class="stock">Stock: <?php echo $product['stock']; ?> available</div>
        
        <p style="margin: 20px 0; line-height: 1.6; color: #555;">
            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
        </p>
        
        <?php if ($product['stock'] > 0): ?>
            <form method="POST" action="cart.php">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" 
                           max="<?php echo $product['stock']; ?>" style="width: 100px;">
                </div>
                
                <button type="submit" class="btn btn-success">Add to Cart</button>
                <a href="product.php" class="btn btn-secondary">Back to Products</a>
            </form>
        <?php else: ?>
            <div style="color: #e74c3c; font-weight: bold; margin: 20px 0;">
                Out of Stock
            </div>
            <a href="product.php" class="btn btn-secondary">Back to Products</a>
        <?php endif; ?>
    </div>
</div>

<?php include 'inc/footer.php'; ?> 