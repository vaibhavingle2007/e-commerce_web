<?php
require_once 'inc/db.php';

$message = '';

// Debug: Check if session is working
if (!isset($_SESSION)) {
    die("Session not started!");
}

// Handle cart actions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    // Debug: Log the POST data
    error_log("Cart POST data: " . print_r($_POST, true));
    error_log("Session cart before: " . print_r($_SESSION['cart'] ?? [], true));
    
    if ($action === 'add') {
        $quantity = (int)($_POST['quantity'] ?? 1);
        if ($quantity > 0 && $product_id > 0) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = array();
            }
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            $message = "Product added to cart!";
            
            // Debug: Log the session after adding
            error_log("Session cart after adding: " . print_r($_SESSION['cart'], true));
            
            // Redirect back to the referring page with success message
            $redirect_url = $_SERVER['HTTP_REFERER'] ?? 'index.php';
            if (strpos($redirect_url, 'cart.php') !== false) {
                $redirect_url = 'index.php';
            }
            
            // Add success message to session for display on redirect
            $_SESSION['cart_message'] = "Product added to cart successfully!";
            $_SESSION['cart_message_type'] = 'success';
            
            header("Location: $redirect_url");
            exit;
        } else {
            $message = "Invalid product or quantity!";
            error_log("Invalid add to cart attempt - product_id: $product_id, quantity: $quantity");
        }
    } elseif ($action === 'update') {
        $quantity = (int)($_POST['quantity'] ?? 0);
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        $message = "Cart updated!";
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][$product_id]);
        $message = "Product removed from cart!";
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = array();
        $message = "Cart cleared!";
    }
}

$page_title = "Shopping Cart";
include 'inc/header.php';
?>

<h1 style="text-align: center; margin: 30px 0; color: #2c3e50;">Shopping Cart</h1>



<?php if ($message): ?>
    <div class="message success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if (empty($_SESSION['cart'])): ?>
    <div style="text-align: center; padding: 50px;">
        <h3>Your cart is empty.</h3>
        <a href="index.php" class="btn">Continue Shopping</a>
    </div>
<?php else: ?>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $grand_total = 0;
            foreach ($_SESSION['cart'] as $product_id => $quantity):
                $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $product = $result->fetch_assoc();
                
                if ($product) {
                    $total = $product['price'] * $quantity;
                    $grand_total += $total;
            ?>
                <tr>
                    <td>
                        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             onerror="this.src='assets/images/placeholder.jpg'">
                        <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                    </td>
                    <td>₹<?php echo number_format($product['price'], 2); ?></td>
                    <td>
                        <form method="POST" style="display: flex; align-items: center; gap: 10px;">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <input type="number" name="quantity" value="<?php echo $quantity; ?>" 
                                   min="0" max="<?php echo $product['stock']; ?>" style="width: 60px;">
                            <button type="submit" class="btn">Update</button>
                        </form>
                    </td>
                    <td>₹<?php echo number_format($total, 2); ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to remove this item?')">
                                Remove
                            </button>
                        </form>
                    </td>
                </tr>
            <?php 
                } else {
                    // Product not found, remove from cart
                    unset($_SESSION['cart'][$product_id]);
                }
            endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold;">
                    Grand Total:
                </td>
                <td style="font-weight: bold; font-size: 1.2em;">
                    ₹<?php echo number_format($grand_total, 2); ?>
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
    </div>
<?php endif; ?>

<?php include 'inc/footer.php'; ?> 
