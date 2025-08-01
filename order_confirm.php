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

<div style="text-align: center; padding: 50px 20px;">
    <div style="background: white; max-width: 600px; margin: 0 auto; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
        <div style="font-size: 4rem; color: #27ae60; margin-bottom: 20px;">✓</div>
        <h1 style="color: #27ae60; margin-bottom: 10px;">Order Confirmed!</h1>
        <p style="font-size: 1.2rem; color: #555; margin-bottom: 30px;">
            Thank you for your order, <?php echo htmlspecialchars($order['user_name']); ?>!
        </p>
        
        <div style="background: #f8f9fa; padding: 25px; border-radius: 8px; margin: 30px 0; text-align: left;">
            <h3 style="color: #2c3e50; margin-bottom: 20px;">Order Details:</h3>
            <div style="line-height: 2;">
                <strong>Order ID:</strong> #<?php echo $order['id']; ?><br>
                <strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?><br>
                <strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?><br>
                <strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?><br>
                <strong>Shipping Address:</strong><br>
                <div style="margin-left: 20px; color: #666;">
                    <?php echo nl2br(htmlspecialchars($order['address'])); ?>
                </div>
            </div>
        </div>
        
        <p style="color: #666; margin: 30px 0;">
            We'll send you an email confirmation shortly with tracking information.
        </p>
        
        <a href="index.php" class="btn btn-success" style="font-size: 1.1rem; padding: 15px 30px;">
            Continue Shopping
        </a>
    </div>
</div>

<?php include 'inc/footer.php'; ?> 
