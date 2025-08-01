<?php
require_once '../inc/db.php';
require_once 'upload_helper.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// Get product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get product data
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header('Location: dashboard.php');
    exit;
}

// Handle form submission
if ($_POST) {
    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);
    $category = trim($_POST['category'] ?? '');
    $image = $product['image']; // Keep existing image by default
    
    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/';
        
        // Ensure upload directory exists
        if (!ensureUploadDirectory($upload_dir)) {
            $error = "Upload directory is not writable.";
        } else {
            // Validate upload
            $validation = validateImageUpload($_FILES['image']);
            if ($validation === true) {
                $image = generateUniqueFilename($_FILES['image']['name'], $upload_dir);
                $upload_path = $upload_dir . $image;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    // File uploaded successfully
                } else {
                    $error = "Error uploading image. Please try again.";
                }
            } else {
                $error = $validation;
            }
        }
    } elseif (!empty($_POST['image_filename'])) {
        // Use manual filename if provided
        $image = trim($_POST['image_filename']);
    }
    
    if (empty($name) || $price <= 0) {
        $error = "Please fill in all required fields.";
    } elseif (empty($error)) {
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ?, stock = ?, category = ? WHERE id = ?");
        $stmt->bind_param("sdssisi", $name, $price, $description, $image, $stock, $category, $product_id);
        
        if ($stmt->execute()) {
            $message = "Product updated successfully!";
            // Refresh product data
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
        } else {
            $error = "Error updating product: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin</title>
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
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .current-image {
            margin: 20px 0;
            text-align: center;
        }
        .current-image img {
            max-width: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav class="admin-nav">
        <div class="container">
            <h1>Edit Product</h1>
            <ul class="admin-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="add_product.php">Add Product</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <?php if ($message): ?>
                <div class="message success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>

            <h2>Edit Product: <?php echo htmlspecialchars($product['name']); ?></h2>
            
            <?php if ($product['image']): ?>
                <div class="current-image">
                    <h4>Current Image:</h4>
                    <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         onerror="this.src='../assets/images/placeholder.jpg'">
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Product Name: *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="price">Price: *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required 
                           value="<?php echo htmlspecialchars($product['price']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image">Upload New Image:</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <small style="color: #666;">Upload JPG, PNG, GIF, or WebP images (max 5MB)</small>
                </div>
                
                <div class="form-group">
                    <label for="image_filename">Or Enter Image Filename:</label>
                    <input type="text" id="image_filename" name="image_filename" 
                           value="<?php echo htmlspecialchars($product['image']); ?>"
                           placeholder="e.g., product.jpg">
                    <small style="color: #666;">If you prefer to manually place image in assets/images/ folder</small>
                </div>
                
                <div class="form-group">
                    <label for="stock">Stock Quantity:</label>
                    <input type="number" id="stock" name="stock" min="0" 
                           value="<?php echo htmlspecialchars($product['stock']); ?>">
                </div>

                <div class="form-group">
                    <label for="category">Category: *</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Mobile" <?php if($product['category']=='Mobile') echo 'selected'; ?>>Mobile</option>
                        <option value="Laptop" <?php if($product['category']=='Laptop') echo 'selected'; ?>>Laptop</option>
                        <option value="Keyboard and Mouse" <?php if($product['category']=='Keyboard and Mouse') echo 'selected'; ?>>Keyboard and Mouse</option>
                        <option value="Headphones" <?php if($product['category']=='Headphones') echo 'selected'; ?>>Headphones</option>
                        <option value="Accessories" <?php if($product['category']=='Accessories') echo 'selected'; ?>>Accessories</option>
                    </select>
                </div>
                
                <div style="margin-top: 30px;">
                    <button type="submit" class="btn btn-success">Update Product</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 
