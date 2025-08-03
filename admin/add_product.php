<?php
require_once '../inc/db.php';
require_once 'upload_helper.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_POST) {
    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);
    $category = trim($_POST['category'] ?? '');
    $image = '';
    
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
    } else {
        // Use manual filename if provided
        $image = trim($_POST['image_filename'] ?? '');
    }
    
    if (empty($name) || $price <= 0) {
        $error = "Please fill in all required fields.";
    } elseif (!isset($error)) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, description, image, stock, category) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssis", $name, $price, $description, $image, $stock, $category);
        
        if ($stmt->execute()) {
            $message = "Product added successfully!";
            // Clear form
            $_POST = array();
        } else {
            $error = "Error adding product. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            line-height: 1.6;
            color: #333;
        }
        
        .admin-nav {
            background: #2c3e50;
            padding: 15px 0;
            margin-bottom: 30px;
        }
        
        .admin-nav .container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
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
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .btn {
            background: #3498db;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .btn-success {
            background: #27ae60;
        }
        
        .btn-success:hover {
            background: #229954;
        }
        
        .btn-secondary {
            background: #95a5a6;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .message {
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <nav class="admin-nav">
        <div class="container">
            <h1>Add Product</h1>
            <ul class="admin-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
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
            
            <?php if (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>

            <h2>Add New Product</h2>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Product Name: *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="price">Price: *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required 
                           value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="category">Category: *</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Mobile" <?php echo ($_POST['category'] ?? '') === 'Mobile' ? 'selected' : ''; ?>>Mobile</option>
                        <option value="Laptop" <?php echo ($_POST['category'] ?? '') === 'Laptop' ? 'selected' : ''; ?>>Laptop</option>
                        <option value="Keyboard and Mouse" <?php echo ($_POST['category'] ?? '') === 'Keyboard and Mouse' ? 'selected' : ''; ?>>Keyboard and Mouse</option>
                        <option value="Headphones" <?php echo ($_POST['category'] ?? '') === 'Headphones' ? 'selected' : ''; ?>>Headphones</option>
                        <option value="Accessories" <?php echo ($_POST['category'] ?? '') === 'Accessories' ? 'selected' : ''; ?>>Accessories</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image">Upload Image:</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <small style="color: #666;">Upload JPG, PNG, GIF, or WebP images (max 5MB)</small>
                </div>
                
                <div class="form-group">
                    <label for="image_filename">Or Enter Image Filename:</label>
                    <input type="text" id="image_filename" name="image_filename" 
                           value="<?php echo htmlspecialchars($_POST['image_filename'] ?? ''); ?>"
                           placeholder="e.g., product.jpg">
                    <small style="color: #666;">If you prefer to manually place image in assets/images/ folder</small>
                </div>
                
                <div class="form-group">
                    <label for="stock">Stock Quantity:</label>
                    <input type="number" id="stock" name="stock" min="0" 
                           value="<?php echo htmlspecialchars($_POST['stock'] ?? '0'); ?>">
                </div>
                
                <div style="margin-top: 30px;">
                    <button type="submit" class="btn btn-success">Add Product</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 
