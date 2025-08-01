<?php
require_once '../inc/db.php';

// Clear admin session
unset($_SESSION['admin_logged_in']);
session_destroy();

// Redirect to login
header('Location: login.php');
exit;
?> 
