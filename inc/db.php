<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ecommerce';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Enable error reporting for debugging (remove in production)
if (!function_exists('custom_error_handler')) {
    function custom_error_handler($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        switch ($errno) {
            case E_USER_ERROR:
                echo "<b>My ERROR</b> [$errno] $errstr<br />";
                echo "Fatal error on line $errline in file $errfile";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />";
                exit(1);
                break;
            case E_USER_WARNING:
                echo "<b>My WARNING</b> [$errno] $errstr<br />";
                break;
            case E_USER_NOTICE:
                echo "<b>My NOTICE</b> [$errno] $errstr<br />";
                break;
            default:
                echo "Unknown error type: [$errno] $errstr<br />";
                break;
        }
        return true;
    }
    
    set_error_handler("custom_error_handler");
}
?> 
