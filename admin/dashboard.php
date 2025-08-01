<?php
require_once '../inc/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

$message = '';

// Handle product deletion
if (isset($_GET['delete_product'])) {
    $product_id = (int)$_GET['delete_product'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        $message = "Product deleted successfully!";
    } else {
        $message = "Error deleting product: " . $conn->error;
    }
}

// Get statistics
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_sales = $conn->query("SELECT SUM(total_amount) as total FROM orders")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-nav {
            background: #2c3e50;
            padding: 1px 0;
            margin-bottom: 30px;
        }
        .admin-nav .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
            height: 60px;
        }
        .admin-nav h1 {
            color: white;
            margin: 0;
            font-size: 1.5rem;
        }
        .admin-menu {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .admin-menu li {
            margin-left: 15px;
        }
        .admin-menu a {
            color: white;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 4px;
            transition: background 0.3s;
            font-size: 0.9rem;
        }
        .admin-menu a:hover {
            background: rgba(255,255,255,0.1);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .stat-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .stat-card .number {
            font-size: 2rem;
            font-weight: bold;
            color: #3498db;
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
        .admin-table img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <nav class="admin-nav">
        <div class="container">
            <h1>Admin Dashboard</h1>
            <ul class="admin-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="add_product.php">Add Product</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <?php if ($message): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Products</h3>
                <div class="number"><?php echo $total_products; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Orders</h3>
                <div class="number"><?php echo $total_orders; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Sales</h3>
                <div class="number">₹<?php echo number_format($total_sales, 2); ?></div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Manage Products</h2>
            <a href="add_product.php" class="btn btn-success">Add New Product</a>
        </div>

        <div class="admin-table">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = $products->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td>
                                <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     onerror="this.src='../assets/images/placeholder.jpg'">
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>₹<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['stock']; ?></td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn">Edit</a>
                                <a href="?delete_product=<?php echo $product['id']; ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html> 
