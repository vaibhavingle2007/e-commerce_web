<?php
// Simple test file to check if cart functionality is working
require_once 'inc/db.php';

echo "<h1>Cart Test</h1>";

// Test database connection
echo "<h2>Database Connection Test</h2>";
if ($conn->connect_error) {
    echo "❌ Database connection failed: " . $conn->connect_error;
} else {
    echo "✅ Database connected successfully<br>";
}

// Test session
echo "<h2>Session Test</h2>";
if (isset($_SESSION)) {
    echo "✅ Session is working<br>";
    echo "Session ID: " . session_id() . "<br>";
} else {
    echo "❌ Session not working<br>";
}

// Test products query
echo "<h2>Products Query Test</h2>";
$result = $conn->query("SELECT COUNT(*) as count FROM products WHERE stock > 0");
if ($result) {
    $row = $result->fetch_assoc();
    echo "✅ Found " . $row['count'] . " products with stock > 0<br>";
} else {
    echo "❌ Products query failed: " . $conn->error . "<br>";
}

// Test cart functionality
echo "<h2>Cart Functionality Test</h2>";
if ($_POST) {
    echo "<h3>POST Data Received:</h3>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    $action = $_POST['action'] ?? '';
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    
    if ($action === 'add' && $product_id > 0) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        $_SESSION['cart'][$product_id] = $quantity;
        echo "✅ Product $product_id added to cart with quantity $quantity<br>";
    }
}

echo "<h3>Current Cart Contents:</h3>";
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    echo "<pre>" . print_r($_SESSION['cart'], true) . "</pre>";
} else {
    echo "Cart is empty<br>";
}

// Simple test form
echo "<h2>Test Add to Cart Form</h2>";
echo '<form method="POST" action="test_cart.php">';
echo '<input type="hidden" name="action" value="add">';
echo '<input type="hidden" name="product_id" value="1">';
echo '<input type="hidden" name="quantity" value="1">';
echo '<button type="submit">Test Add Product 1 to Cart</button>';
echo '</form>';

echo '<br><a href="index.php">← Back to Home</a>';
?>