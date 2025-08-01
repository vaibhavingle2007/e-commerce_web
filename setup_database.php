<?php
require_once 'inc/db.php';

$page_title = "Database Setup";
include 'inc/header.php';

echo "<h2>Database Setup and Verification</h2>";

// Check and create tables
$tables = array(
    'products' => "
        CREATE TABLE IF NOT EXISTS products (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            description TEXT,
            image VARCHAR(255),
            stock INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ",
    'orders' => "
        CREATE TABLE IF NOT EXISTS orders (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            address TEXT NOT NULL,
            phone VARCHAR(20),
            total_amount DECIMAL(10,2) NOT NULL,
            order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ",
    'order_items' => "
        CREATE TABLE IF NOT EXISTS order_items (
            id INT PRIMARY KEY AUTO_INCREMENT,
            order_id INT,
            product_id INT,
            quantity INT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        )
    "
);

foreach ($tables as $table_name => $sql) {
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>✅ Table '$table_name' created/verified successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create table '$table_name': " . $conn->error . "</p>";
    }
}

// Check if tables exist
echo "<h3>Table Status:</h3>";
foreach (array_keys($tables) as $table_name) {
    $result = $conn->query("SHOW TABLES LIKE '$table_name'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table '$table_name' exists</p>";
        
        // Show table structure
        $structure = $conn->query("DESCRIBE $table_name");
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; font-size: 12px;'>";
        echo "<tr><th colspan='5'>$table_name Structure</th></tr>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $structure->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Table '$table_name' does not exist</p>";
    }
}

// Insert sample products if none exist
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$count = $result->fetch_assoc()['count'];

if ($count == 0) {
    echo "<h3>Inserting Sample Products</h3>";
    
    $sample_products = array(
        array('name' => 'Sample Product 1', 'price' => 29.99, 'description' => 'This is a sample product for testing', 'image' => 'placeholder.jpg', 'stock' => 10),
        array('name' => 'Sample Product 2', 'price' => 49.99, 'description' => 'Another sample product for testing', 'image' => 'placeholder.jpg', 'stock' => 5),
        array('name' => 'Sample Product 3', 'price' => 19.99, 'description' => 'Third sample product for testing', 'image' => 'placeholder.jpg', 'stock' => 15)
    );
    
    foreach ($sample_products as $product) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, description, image, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssi", $product['name'], $product['price'], $product['description'], $product['image'], $product['stock']);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Sample product added: " . $product['name'] . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to add sample product: " . $stmt->error . "</p>";
        }
    }
} else {
    echo "<p style='color: blue;'>ℹ️ $count products already exist in database</p>";
}

// Test database connection and permissions
echo "<h3>Database Connection Test</h3>";
if ($conn->ping()) {
    echo "<p style='color: green;'>✅ Database connection is working</p>";
} else {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
}

// Test insert permissions
echo "<h3>Testing Insert Permissions</h3>";
$test_order = $conn->prepare("INSERT INTO orders (user_name, email, address, phone, total_amount) VALUES (?, ?, ?, ?, ?)");
$test_name = "Test User";
$test_email = "test@example.com";
$test_address = "Test Address";
$test_phone = "1234567890";
$test_amount = 99.99;

$test_order->bind_param("ssssd", $test_name, $test_email, $test_address, $test_phone, $test_amount);

if ($test_order->execute()) {
    $test_order_id = $conn->insert_id;
    echo "<p style='color: green;'>✅ Test order inserted successfully (ID: $test_order_id)</p>";
    
    // Clean up test order
    $conn->query("DELETE FROM orders WHERE id = $test_order_id");
    echo "<p style='color: blue;'>ℹ️ Test order cleaned up</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to insert test order: " . $test_order->error . "</p>";
}

echo "<h3>Setup Complete!</h3>";
echo "<p>Your database is now ready for checkout functionality.</p>";
echo "<p><a href='index.php' class='btn btn-success'>Go to Store</a></p>";
echo "<p><a href='test_checkout.php' class='btn btn-secondary'>Test Checkout</a></p>";

include 'inc/footer.php';
?> 