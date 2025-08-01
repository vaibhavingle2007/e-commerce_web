# Product Images Directory

This directory contains all product images for the e-commerce website.

## How to Add Product Images

### Method 1: Upload via Admin Panel (Recommended)
1. Login to the admin panel at `/admin/login.php`
2. Go to "Add Product" or "Edit Product"
3. Use the file upload feature to upload images directly
4. Images will be automatically saved with unique names

### Method 2: Manual File Placement
1. Place your image files directly in this folder
2. Supported formats: JPG, JPEG, PNG, GIF, WebP
3. Maximum file size: 5MB
4. When adding products via admin panel, enter the exact filename

## Expected Image Files
Based on the database setup, the system expects these specific image files:
- `laptop.jpg` - Laptop product image
- `phone.jpg` - Smartphone product image  
- `headphones.jpg` - Headphones product image
- `watch.jpg` - Watch product image
- `tablet.jpg` - Tablet product image
- `camera.jpg` - Camera product image

## Image Guidelines
- Recommended size: 400x400 pixels or larger
- Aspect ratio: Square (1:1) works best
- File format: JPG, PNG, GIF, or WebP
- File size: Keep under 5MB for optimal performance

## Fallback Image
If a product image is missing, the system will automatically show `placeholder.jpg` as a fallback. 
