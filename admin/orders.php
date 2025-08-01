<?php
require_once '../inc/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

// Handle order deletion
if (isset($_GET['delete_order'])) {
    $order_id = (int)$_GET['delete_order'];
    // Delete order_items first (if not ON DELETE CASCADE)
    $conn->query("DELETE FROM order_items WHERE order_id = $order_id");
    // Delete the order
    if ($conn->query("DELETE FROM orders WHERE id = $order_id")) {
        echo '<div class="message success">Order deleted successfully!</div>';
    } else {
        echo '<div class="message error">Error deleting order: ' . $conn->error . '</div>';
    }
}

// Get all orders with order items
$orders = $conn->query("
    SELECT o.*, 
           GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ')') SEPARATOR ', ') as items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.id
    GROUP BY o.id
    ORDER BY o.order_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-nav {
            background: #2c3e50;
            padding: 15px 0;
            margin-bottom: 30px;
        }
        .admin-nav .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-nav h1 {
            color: white;
            margin: 0;
        }
        .admin-menu {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .admin-menu li {
            margin-left: 20px;
        }
        .admin-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .admin-menu a:hover {
            background: rgba(255,255,255,0.1);
        }
        .admin-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .admin-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        .admin-table td {
            padding: 15px;
            border-top: 1px solid #e1e5e9;
        }
        .order-items {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <nav class="admin-nav">
        <div class="container">
            <h1>Orders</h1>
            <ul class="admin-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="add_product.php">Add Product</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h2>All Orders</h2>

        <div class="admin-table">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $order['id']; ?></strong></td>
                            <td>
                                <?php echo htmlspecialchars($order['user_name']); ?><br>
                                <small><?php echo htmlspecialchars($order['phone']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($order['email']); ?></td>
                            <td>
                                <div class="order-items">
                                    <?php echo htmlspecialchars($order['items'] ?? 'No items'); ?>
                                </div>
                            </td>
                            <td><strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                            <td><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></td>
                            <td>
                                <div style="max-width: 200px; font-size: 0.9em;">
                                    <?php echo nl2br(htmlspecialchars($order['address'])); ?>
                                </div>
                            </td>
                            <td>
                                <a href="?delete_order=<?php echo $order['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($orders->num_rows === 0): ?>
            <div style="text-align: center; padding: 50px; color: #666;">
                <h3>No orders found.</h3>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 
