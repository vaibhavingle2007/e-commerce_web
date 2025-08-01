<?php
// Helper function to ensure upload directory exists
function ensureUploadDirectory($upload_dir) {
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Check if directory is writable
    if (!is_writable($upload_dir)) {
        chmod($upload_dir, 0755);
    }
    
    return is_writable($upload_dir);
}

// Helper function to validate uploaded image
function validateImageUpload($file) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "Upload error: " . $file['error'];
    }
    
    if ($file['size'] > $max_size) {
        return "File too large. Maximum size is 5MB.";
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return "Invalid file type. Please upload JPG, PNG, GIF, or WebP images only.";
    }
    
    return true;
}

// Helper function to generate unique filename
function generateUniqueFilename($original_name, $upload_dir) {
    $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $filename = uniqid() . '.' . $extension;
    
    // Ensure filename is unique
    while (file_exists($upload_dir . $filename)) {
        $filename = uniqid() . '.' . $extension;
    }
    
    return $filename;
}
?> 
