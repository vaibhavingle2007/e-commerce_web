<?php
// Installation script for E-Commerce Website
session_start();

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$message = '';
$error = '';

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ecommerce';

// Check system requirements
function checkRequirements() {
    $requirements = [
        'PHP Version (>= 7.4)' => version_compare(PHP_VERSION, '7.4.0', '>='),
        'MySQL Extension' => extension_loaded('mysqli'),
        'File Upload Support' => ini_get('file_uploads'),
        'Upload Directory Writable' => is_writable('assets/images/') || is_writable('assets/images'),
        'Session Support' => function_exists('session_start'),
    ];
    
    return $requirements;
}

// Test database connection
function testDatabase($host, $username, $password, $database) {
    try {
        $conn = new mysqli($host, $username, $password);
        if ($conn->connect_error) {
            return "Connection failed: " . $conn->connect_error;
        }
        
        // Check if database exists
        $result = $conn->query("SHOW DATABASES LIKE '$database'");
        if ($result->num_rows === 0) {
            return "Database '$database' does not exist. Please create it first.";
        }
        
        // Test connection to specific database
        $conn->select_db($database);
        
        // Check if tables exist
        $tables = ['products', 'orders', 'order_items'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows === 0) {
                return "Table '$table' does not exist. Please run database_setup.sql first.";
            }
        }
        
        $conn->close();
        return true;
    } catch (Exception $e) {
        return "Database error: " . $e->getMessage();
    }
}

// Handle form submission
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'test_connection') {
        $test_result = testDatabase($host, $username, $password, $database);
        if ($test_result === true) {
            $message = "Database connection successful! All tables exist.";
        } else {
            $error = $test_result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Installation</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .install-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .step {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #e1e5e9;
            border-radius: 8px;
        }
        .step h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .requirement {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        .requirement:last-child {
            border-bottom: none;
        }
        .status {
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status.success {
            background: #d4edda;
            color: #155724;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
        }
        .code-block {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            font-family: monospace;
            margin: 15px 0;
            border-left: 4px solid #3498db;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <h1 style="text-align: center; color: #2c3e50; margin-bottom: 30px;">
            E-Commerce Website Installation
        </h1>
        
        <?php if ($message): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="step">
            <h3>Step 1: System Requirements Check</h3>
            <?php
            $requirements = checkRequirements();
            $all_passed = true;
            foreach ($requirements as $requirement => $passed):
                if (!$passed) $all_passed = false;
            ?>
                <div class="requirement">
                    <span><?php echo $requirement; ?></span>
                    <span class="status <?php echo $passed ? 'success' : 'error'; ?>">
                        <?php echo $passed ? '✓ PASS' : '✗ FAIL'; ?>
                    </span>
                </div>
            <?php endforeach; ?>
            
            <?php if ($all_passed): ?>
                <div style="margin-top: 15px; color: #27ae60; font-weight: bold;">
                    ✓ All system requirements are met!
                </div>
            <?php else: ?>
                <div style="margin-top: 15px; color: #e74c3c; font-weight: bold;">
                    ✗ Please fix the failed requirements before proceeding.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="step">
            <h3>Step 2: Database Setup</h3>
            <p>Before proceeding, please ensure you have:</p>
            <ol>
                <li>Created a MySQL database named <strong>ecommerce</strong></li>
                <li>Imported the database schema from <code>database_setup.sql</code></li>
            </ol>
            
            <div class="code-block">
                # Create database<br>
                CREATE DATABASE ecommerce;<br><br>
                # Import schema<br>
                mysql -u root -p ecommerce &lt; database_setup.sql
            </div>
            
            <form method="POST" style="margin-top: 20px;">
                <input type="hidden" name="action" value="test_connection">
                <button type="submit" class="btn btn-success">Test Database Connection</button>
            </form>
        </div>
        
        <div class="step">
            <h3>Step 3: Configuration</h3>
            <p>Update database credentials in <code>inc/db.php</code> if needed:</p>
            <div class="code-block">
                $host = 'localhost';<br>
                $username = 'root';<br>
                $password = '';<br>
                $database = 'ecommerce';
            </div>
        </div>
        
        <div class="step">
            <h3>Step 4: Product Images</h3>
            <p>Add product images to <code>assets/images/</code> folder:</p>
            <ul>
                <li>laptop.jpg</li>
                <li>phone.jpg</li>
                <li>headphones.jpg</li>
                <li>watch.jpg</li>
                <li>tablet.jpg</li>
                <li>camera.jpg</li>
            </ul>
            <p><strong>Or</strong> use the admin panel to upload images when adding products.</p>
        </div>
        
        <div class="step">
            <h3>Step 5: Admin Access</h3>
            <p>Default admin credentials:</p>
            <div class="code-block">
                Username: admin<br>
                Password: admin123
            </div>
            <p><strong>Important:</strong> Change these credentials in <code>admin/login.php</code> for security.</p>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="btn btn-success" style="font-size: 1.1rem; padding: 15px 30px;">
                Go to Website
            </a>
            <a href="admin/login.php" class="btn" style="font-size: 1.1rem; padding: 15px 30px; margin-left: 15px;">
                Admin Panel
            </a>
        </div>
    </div>
</body>
</html> 
