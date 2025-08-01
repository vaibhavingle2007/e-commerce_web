<?php
require_once 'inc/db.php';

echo "<h1>Database Check</h1>";

// Check if database connection works
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
}

// Check if products table exists
$result = $conn->query("SHOW TABLES LIKE 'products'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Products table exists</p>";
} else {
    echo "<p style='color: red;'>✗ Products table does not exist</p>";
}

// Check products count
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$count = $result->fetch_assoc()['count'];
echo "<p>Total products in database: <strong>$count</strong></p>";

// Check products with stock
$result = $conn->query("SELECT COUNT(*) as count FROM products WHERE stock > 0");
$count = $result->fetch_assoc()['count'];
echo "<p>Products with stock > 0: <strong>$count</strong></p>";

// Show all products
echo "<h2>All Products</h2>";
$result = $conn->query("SELECT * FROM products");
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Image</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>$" . number_format($row['price'], 2) . "</td>";
        echo "<td>" . $row['stock'] . "</td>";
        echo "<td>" . htmlspecialchars($row['image']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No products found in database!</p>";
}

// Check if orders table exists
$result = $conn->query("SHOW TABLES LIKE 'orders'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Orders table exists</p>";
} else {
    echo "<p style='color: red;'>✗ Orders table does not exist</p>";
}

// Check if order_items table exists
$result = $conn->query("SHOW TABLES LIKE 'order_items'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Order_items table exists</p>";
} else {
    echo "<p style='color: red;'>✗ Order_items table does not exist</p>";
}
?> 
