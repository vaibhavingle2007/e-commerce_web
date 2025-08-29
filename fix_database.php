<?php
require_once 'inc/db.php';

$page_title = "Database Fix";
include 'inc/header.php';

echo "<h2>Database Repair and Fix</h2>";

// Check and fix database tables
$tables_sql = [
    'products' => "
        CREATE TABLE IF NOT EXISTS products (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            description TEXT,
            image VARCHAR(255),
            stock INT DEFAULT 0,
            category VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'order_items' => "
        CREATE TABLE IF NOT EXISTS order_items (
            id INT PRIMARY KEY AUTO_INCREMENT,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            INDEX idx_order_id (order_id),
            INDEX idx_product_id (product_id),
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "
];

echo "<h3>Creating/Verifying Tables:</h3>";

foreach ($tables_sql as $table_name => $sql) {
    try {
        if ($conn->query($sql)) {
            echo "<p style='color: green;'>✅ Table '$table_name' created/verified successfully</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create table '$table_name': " . $conn->error . "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Exception creating table '$table_name': " . $e->getMessage() . "</p>";
    }
}

// Check table structures
echo "<h3>Table Structure Verification:</h3>";

foreach (array_keys($tables_sql) as $table_name) {
    $result = $conn->query("SHOW TABLES LIKE '$table_name'");
    if ($result && $result->num_rows > 0) {
        echo "<h4>$table_name:</h4>";
        
        // Show columns
        $columns = $conn->query("SHOW COLUMNS FROM $table_name");
        if ($columns) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            while ($row = $columns->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Show row count
        $count_result = $conn->query("SELECT COUNT(*) as count FROM $table_name");
        if ($count_result) {
            $count = $count_result->fetch_assoc()['count'];
            echo "<p>Records in table: <strong>$count</strong></p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Table '$table_name' still does not exist</p>";
    }
}

// Add sample products if none exist
$product_count = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];

if ($product_count == 0) {
    echo "<h3>Adding Sample Products:</h3>";
    
    $sample_products = [
        ['iPhone 15 Pro', 999.99, 'Latest iPhone with advanced camera system and A17 Pro chip', 'iphone15.jpg', 10, 'Mobile'],
        ['MacBook Pro M3', 1999.99, 'Powerful laptop with M3 chip for professionals', 'macbook-pro.jpg', 5, 'Laptop'],
        ['AirPods Pro 2', 249.99, 'Wireless earbuds with active noise cancellation', 'airpods-pro.jpg', 15, 'Headphones'],
        ['Magic Keyboard', 179.99, 'Wireless keyboard with backlight and numeric keypad', 'magic-keyboard.jpg', 8, 'Keyboard and Mouse'],
        ['USB-C Hub', 79.99, 'Multi-port USB-C hub with HDMI, USB-A, and SD card slots', 'usb-hub.jpg', 20, 'Accessories']
    ];
    
    foreach ($sample_products as $product) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, description, image, stock, category) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sdssiss", $product[0], $product[1], $product[2], $product[3], $product[4], $product[5]);
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✅ Added sample product: " . htmlspecialchars($product[0]) . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to add sample product: " . $stmt->error . "</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Failed to prepare statement for product: " . $conn->error . "</p>";
        }
    }
} else {
    echo "<p style='color: blue;'>ℹ️ Products already exist ($product_count products found)</p>";
}

// Test database operations
echo "<h3>Testing Database Operations:</h3>";

try {
    // Test transaction
    $conn->begin_transaction();
    
    // Test order insertion
    $test_stmt = $conn->prepare("INSERT INTO orders (user_name, email, address, phone, total_amount) VALUES (?, ?, ?, ?, ?)");
    if ($test_stmt) {
        $test_name = "Test User";
        $test_email = "test@example.com";
        $test_address = "123 Test Street";
        $test_phone = "1234567890";
        $test_total = 99.99;
        
        $test_stmt->bind_param("ssssd", $test_name, $test_email, $test_address, $test_phone, $test_total);
        
        if ($test_stmt->execute()) {
            $test_order_id = $conn->insert_id;
            echo "<p style='color: green;'>✅ Test order insertion successful (ID: $test_order_id)</p>";
            
            // Test order item insertion
            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            if ($item_stmt) {
                $test_product_id = 1;
                $test_quantity = 1;
                $test_price = 99.99;
                
                $item_stmt->bind_param("iiid", $test_order_id, $test_product_id, $test_quantity, $test_price);
                
                if ($item_stmt->execute()) {
                    echo "<p style='color: green;'>✅ Test order item insertion successful</p>";
                } else {
                    echo "<p style='color: red;'>❌ Test order item insertion failed: " . $item_stmt->error . "</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ Failed to prepare order item statement: " . $conn->error . "</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Test order insertion failed: " . $test_stmt->error . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Failed to prepare order statement: " . $conn->error . "</p>";
    }
    
    // Rollback test transaction
    $conn->rollback();
    echo "<p style='color: blue;'>ℹ️ Test transaction rolled back successfully</p>";
    
} catch (Exception $e) {
    $conn->rollback();
    echo "<p style='color: red;'>❌ Test transaction failed: " . $e->getMessage() . "</p>";
}

echo "<h3>Database Status Summary:</h3>";
echo "<p><strong>Database Connection:</strong> " . ($conn->ping() ? "✅ Active" : "❌ Failed") . "</p>";
echo "<p><strong>MySQL Version:</strong> " . $conn->get_server_info() . "</p>";
echo "<p><strong>Character Set:</strong> " . $conn->character_set_name() . "</p>";

echo "<h3>Next Steps:</h3>";
echo "<ul>";
echo "<li><a href='test_checkout.php'>Test Checkout Process</a></li>";
echo "<li><a href='checkout.php'>Go to Checkout</a></li>";
echo "<li><a href='cart.php'>View Cart</a></li>";
echo "<li><a href='index.php'>Return to Store</a></li>";
echo "</ul>";

include 'inc/footer.php';
?>