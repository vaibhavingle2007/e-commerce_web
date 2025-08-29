<?php
require_once 'inc/db.php';

// Debug: Log checkout process
error_log("Checkout process started - Session cart: " . print_r($_SESSION['cart'] ?? [], true));

// Check database tables exist
$required_tables = ['products', 'orders', 'order_items'];
foreach ($required_tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows === 0) {
        error_log("Required table '$table' does not exist");
        die("Database error: Required table '$table' is missing. Please run database setup.");
    }
}

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    error_log("Checkout attempted with empty cart - redirecting to cart.php");
    header('Location: cart.php');
    exit;
}

// Calculate total
$grand_total = 0;
$cart_items = array();
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if ($product) {
        $total = $product['price'] * $quantity;
        $grand_total += $total;
        $cart_items[$product_id] = array(
            'product' => $product,
            'quantity' => $quantity,
            'total' => $total
        );
    } else {
        error_log("Product not found: ID $product_id");
    }
}

// Process checkout
if ($_POST) {
    error_log("Checkout POST data received: " . print_r($_POST, true));
    
    $user_name = trim($_POST['user_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    // Enhanced validation
    $errors = array();
    
    if (empty($user_name)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($address)) {
        $errors[] = "Shipping address is required";
    }
    
    if (empty($cart_items)) {
        $errors[] = "No valid items in cart";
    }
    
    if (empty($errors)) {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Validate database connection
            if (!$conn || $conn->connect_error) {
                throw new Exception("Database connection failed: " . ($conn->connect_error ?? 'Unknown error'));
            }
            
            // Insert order
            $stmt = $conn->prepare("INSERT INTO orders (user_name, email, address, phone, total_amount) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Failed to prepare order statement: " . $conn->error);
            }
            
            $stmt->bind_param("ssssd", $user_name, $email, $address, $phone, $grand_total);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert order: " . $stmt->error);
            }
            
            $order_id = $conn->insert_id;
            if (!$order_id) {
                throw new Exception("Failed to get order ID after insertion");
            }
            
            error_log("Order inserted successfully with ID: $order_id");
            
            // Insert order items
            foreach ($cart_items as $product_id => $item) {
                $product = $item['product'];
                $quantity = $item['quantity'];
                $price = $product['price'];
                
                // Check stock availability
                if ($product['stock'] < $quantity) {
                    throw new Exception("Insufficient stock for product: " . $product['name']);
                }
                
                // Insert order item
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                if (!$stmt) {
                    throw new Exception("Failed to prepare order item statement: " . $conn->error);
                }
                
                $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to insert order item for product ID $product_id: " . $stmt->error);
                }
                
                // Update stock
                $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                if (!$stmt) {
                    throw new Exception("Failed to prepare stock update statement: " . $conn->error);
                }
                
                $stmt->bind_param("ii", $quantity, $product_id);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update stock for product ID $product_id: " . $stmt->error);
                }
                
                // Verify stock was actually updated
                if ($stmt->affected_rows === 0) {
                    throw new Exception("No stock was updated for product ID $product_id - product may not exist");
                }
                
                error_log("Order item inserted: Product ID $product_id, Quantity $quantity, Price $price");
            }
            
            // Commit transaction
            $conn->commit();
            
            // Clear cart
            $_SESSION['cart'] = array();
            
            error_log("Checkout completed successfully - Order ID: $order_id");
            
            // Redirect to confirmation
            header("Location: order_confirm.php?order_id=$order_id");
            exit;
            
        } catch (Exception $e) {
            // Rollback transaction
            $conn->rollback();
            error_log("Checkout failed: " . $e->getMessage());
            $error = "Error processing order: " . $e->getMessage();
        }
    } else {
        $error = "Please fix the following errors:<br>" . implode("<br>", $errors);
        error_log("Checkout validation errors: " . implode(", ", $errors));
    }
}

$page_title = "Checkout";
include 'inc/header.php';
?>

<div class="container">
    <h1 style="text-align: center; margin: 50px 0 30px 0; font-size: 3rem; background: linear-gradient(135deg, var(--primary), var(--accent)); background-clip: text; -webkit-background-clip: text; color: transparent;">Checkout</h1>

    <?php if (isset($error)): ?>
        <div class="message error">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="checkout-container">
        <div class="shipping-form">
            <h2 style="margin-bottom: 30px; font-size: 2rem; color: var(--text-primary);">Shipping Information</h2>
            <form method="POST" class="checkout-form">
                <div class="form-group">
                    <label for="user_name">Full Name: *</label>
                    <input type="text" id="user_name" name="user_name" required 
                           value="<?php echo htmlspecialchars($_POST['user_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email: *</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Shipping Address: *</label>
                    <textarea id="address" name="address" rows="4" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="btn-container" style="margin-top: 30px;">
                    <button type="submit" class="btn btn-cosmic" id="checkout-btn">
                        Place Order - ₹<?php echo number_format($grand_total, 2); ?>
                    </button>
                    <a href="cart.php" class="btn btn-glass">Back to Cart</a>
                </div>
            </form>
        </div>
        
        <div class="order-summary">
            <h3>Order Summary</h3>
            <?php foreach ($cart_items as $product_id => $item): 
                $product = $item['product'];
                $quantity = $item['quantity'];
                $total = $item['total'];
            ?>
                <div class="order-item">
                    <div>
                        <strong><?php echo htmlspecialchars($product['name']); ?></strong><br>
                        <small>Quantity: <?php echo $quantity; ?> × ₹<?php echo number_format($product['price'], 2); ?></small>
                    </div>
                    <div>₹<?php echo number_format($total, 2); ?></div>
                </div>
            <?php endforeach; ?>
            
            <hr style="margin: 20px 0; border-color: var(--border);">
            <div class="order-item total">
                <strong>Total:</strong>
                <strong>₹<?php echo number_format($grand_total, 2); ?></strong>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.querySelector('.checkout-form');
    const checkoutBtn = document.getElementById('checkout-btn');
    
    if (checkoutForm && checkoutBtn) {
        checkoutForm.addEventListener('submit', function(e) {
            // Show loading state
            checkoutBtn.disabled = true;
            checkoutBtn.innerHTML = 'Processing Order...';
            
            // Basic validation
            const requiredFields = checkoutForm.querySelectorAll('input[required], textarea[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--error)';
                } else {
                    field.style.borderColor = 'var(--border)';
                }
            });
            
            // Email validation
            const emailField = checkoutForm.querySelector('input[type="email"]');
            if (emailField && emailField.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value)) {
                    isValid = false;
                    emailField.style.borderColor = 'var(--error)';
                    alert('Please enter a valid email address');
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                checkoutBtn.disabled = false;
                checkoutBtn.innerHTML = 'Place Order - ₹<?php echo number_format($grand_total, 2); ?>';
                return false;
            }
            
            // If validation passes, show success message
            setTimeout(() => {
                if (!checkoutForm.querySelector('.message.error')) {
                    checkoutBtn.innerHTML = 'Order Placed Successfully!';
                }
            }, 1000);
        });
        
        // Real-time validation
        const inputs = checkoutForm.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.style.borderColor = 'var(--error)';
                } else {
                    this.style.borderColor = 'var(--border)';
                }
            });
            
            input.addEventListener('focus', function() {
                this.style.borderColor = 'var(--primary)';
            });
        });
    }
});
</script>

<?php include 'inc/footer.php'; ?> 
