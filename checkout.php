<?php
require_once 'inc/db.php';

// Debug: Log checkout process
error_log("Checkout process started - Session cart: " . print_r($_SESSION['cart'] ?? [], true));

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
            // Insert order
            $stmt = $conn->prepare("INSERT INTO orders (user_name, email, address, phone, total_amount) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssd", $user_name, $email, $address, $phone, $grand_total);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert order: " . $stmt->error);
            }
            
            $order_id = $conn->insert_id;
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
                $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to insert order item: " . $stmt->error);
                }
                
                // Update stock
                $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stmt->bind_param("ii", $quantity, $product_id);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update stock: " . $stmt->error);
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

<h1 style="text-align: center; margin: 30px 0; color: #2c3e50;">Checkout</h1>



<?php if (isset($error)): ?>
    <div class="message error" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #f5c6cb;">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="checkout-container">
    <div>
        <h2>Shipping Information</h2>
        <form method="POST" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div class="form-group">
                <label for="user_name">Full Name: *</label>
                <input type="text" id="user_name" name="user_name" required 
                       value="<?php echo htmlspecialchars($_POST['user_name'] ?? ''); ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <div class="form-group">
                <label for="email">Email: *</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" 
                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <div class="form-group">
                <label for="address">Shipping Address: *</label>
                <textarea id="address" name="address" rows="4" required
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-success" style="font-size: 16px; padding: 12px 24px;">
                    Place Order - ₹<?php echo number_format($grand_total, 2); ?>
                </button>
                <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
            </div>
        </form>
    </div>
    
    <div class="order-summary" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3>Order Summary</h3>
        <?php foreach ($cart_items as $product_id => $item): 
            $product = $item['product'];
            $quantity = $item['quantity'];
            $total = $item['total'];
        ?>
            <div class="order-item" style="display: flex; justify-content: space-between; margin: 15px 0; padding: 10px 0; border-bottom: 1px solid #eee;">
                <div>
                    <strong><?php echo htmlspecialchars($product['name']); ?></strong><br>
                    <small>Quantity: <?php echo $quantity; ?> × ₹<?php echo number_format($product['price'], 2); ?></small>
                </div>
                <div>₹<?php echo number_format($total, 2); ?></div>
            </div>
        <?php endforeach; ?>
        
        <hr style="margin: 20px 0;">
        <div class="order-item" style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2em;">
            <strong>Total:</strong>
            <strong>₹<?php echo number_format($grand_total, 2); ?></strong>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?> 
