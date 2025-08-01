<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'E-Store'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/cart.js" defer></script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1><a href="index.php">E-Store</a></h1>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="product.php">Products</a></li>
                <li><a href="cart.php">Cart (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)</a></li>
                <li><a href="admin/login.php">Admin</a></li>
            </ul>
            
        
        </div>
    </nav>
    <div class="container"> 
