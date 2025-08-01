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

$page_title = "All Products - E-Commerce Store";
include 'inc/header.php';
?>

<h1 style="text-align: center; margin: 30px 0; color: #2c3e50; font-size: 2.5rem;">All Products</h1>

<?php if (isset($_SESSION['cart_message'])): ?>
    <div class="message <?php echo $_SESSION['cart_message_type']; ?>" style="text-align: center; margin: 20px 0; padding: 15px; border-radius: 5px; background-color: <?php echo $_SESSION['cart_message_type'] === 'success' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $_SESSION['cart_message_type'] === 'success' ? '#155724' : '#721c24'; ?>; border: 1px solid <?php echo $_SESSION['cart_message_type'] === 'success' ? '#c3e6cb' : '#f5c6cb'; ?>;">
        <?php echo $_SESSION['cart_message']; ?>
    </div>
    <?php 
    unset($_SESSION['cart_message']);
    unset($_SESSION['cart_message_type']);
    ?>
<?php endif; ?>

<?php foreach ($categories as $cat_key => $cat_label): ?>
    <?php if (!empty($products_by_category[$cat_key])): ?>
        <h2 style="margin: 40px 0 20px 0; color: #314151; font-size: 2rem; border-bottom: 2px solid #eee; padding-bottom: 8px;"> <?php echo htmlspecialchars($cat_label); ?> </h2>
        <div class="product-grid">
            <?php foreach ($products_by_category[$cat_key] as $product): ?>
                <div class="product-card">
                    <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         onerror="this.src='assets/images/placeholder.jpg'">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php if (!empty($products_by_category['Other'])): ?>
    <h2 style="margin: 40px 0 20px 0; color: #314151; font-size: 2rem; border-bottom: 2px solid #eee; padding-bottom: 8px;">Other</h2>
    <div class="product-grid">
        <?php foreach ($products_by_category['Other'] as $product): ?>
            <div class="product-card">
                <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     onerror="this.src='assets/images/placeholder.jpg'">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
                <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn">View Details</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'inc/footer.php'; ?> 
