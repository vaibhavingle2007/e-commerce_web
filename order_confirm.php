<?php
require_once 'inc/db.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    header('Location: index.php');
    exit;
}

$page_title = "Order Confirmation";
include 'inc/header.php';
?>

<div class="container">
    <div class="order-confirmation">
        <div class="success-icon">✓</div>
        <h1>Order Confirmed!</h1>
        <p class="thank-you-message">
            Thank you for your order, <?php echo htmlspecialchars($order['user_name']); ?>!
        </p>
        
        <div class="order-details">
            <h3>Order Details:</h3>
            <div class="details-content">
                <div class="detail-item">
                    <strong>Order ID:</strong> #<?php echo $order['id']; ?>
                </div>
                <div class="detail-item">
                    <strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?>
                </div>
                <div class="detail-item">
                    <strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?>
                </div>
                <div class="detail-item">
                    <strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?>
                </div>
                <div class="detail-item">
                    <strong>Shipping Address:</strong>
                    <div class="address">
                        <?php echo nl2br(htmlspecialchars($order['address'])); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <p class="confirmation-message">
            We'll send you an email confirmation shortly with tracking information.
        </p>
        
        <div class="btn-container">
            <a href="index.php" class="btn btn-cosmic">
                Continue Shopping
            </a>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?> 
