# E-Commerce Website

A complete mini e-commerce website built with PHP, MySQL, and modern CSS. This project includes a customer-facing store and an admin dashboard for product management.

## 🚀 Features

### Customer Features
- **Product Catalog**: Browse all available products
- **Product Details**: View detailed product information
- **Shopping Cart**: Add, update, and remove items
- **Checkout Process**: Complete orders with customer information
- **Order Confirmation**: Receive order confirmation with details
- **Responsive Design**: Works on desktop, tablet, and mobile

### Admin Features
- **Admin Dashboard**: Overview of products, orders, and sales
- **Product Management**: Add, edit, and delete products
- **Order Management**: View all customer orders
- **Sales Analytics**: Track total sales and order statistics

## 📁 Project Structure

```
/ecommerce/
├── /assets/
│   ├── /css/
│   │   └── style.css          # Main stylesheet
│   ├── /js/
│   │   └── cart.js            # Cart functionality (future)
│   └── /images/               # Product images
├── /admin/
│   ├── login.php              # Admin login
│   ├── dashboard.php          # Admin dashboard
│   ├── add_product.php        # Add new products
│   ├── orders.php             # View all orders
│   └── logout.php             # Admin logout
├── /inc/
│   ├── db.php                 # Database connection
│   ├── header.php             # Site header
│   └── footer.php             # Site footer
├── index.php                  # Homepage with products
├── product.php                # Individual product page
├── cart.php                   # Shopping cart
├── checkout.php               # Checkout process
├── order_confirm.php          # Order confirmation
├── database_setup.sql         # Database schema
└── README.md                  # This file
```

## 🛠️ Installation & Setup

### Quick Start
1. **Run the installation script**: Visit `install.php` in your browser
2. **Follow the setup wizard**: It will check requirements and guide you through setup
3. **Test your installation**: Use the provided links to verify everything works

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server

### Manual Setup (Alternative)

#### Step 1: Database Setup
1. Create a MySQL database named `ecommerce`
2. Import the database schema:
   ```bash
   mysql -u root -p ecommerce < database_setup.sql
   ```
   Or run the SQL commands in `database_setup.sql` manually

#### Step 2: Configure Database Connection
Edit `inc/db.php` and update the database credentials:
```php
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'ecommerce';
```

#### Step 3: Web Server Setup
1. **Option A: PHP Built-in Server**
   ```bash
   cd /path/to/ecommerce
   php -S localhost:8000
   ```
   Then visit `http://localhost:8000`

2. **Option B: Apache/Nginx**
   - Place the project in your web server's document root
   - Ensure PHP and MySQL are properly configured
   - Visit your domain or localhost

#### Step 4: Add Product Images
**Method 1: Upload via Admin Panel (Recommended)**
1. Login to admin panel at `/admin/login.php`
2. Go to "Add Product" or "Edit Product"
3. Use the file upload feature to upload images directly

**Method 2: Manual File Placement**
Place product images in the `assets/images/` folder:
- laptop.jpg
- phone.jpg
- headphones.jpg
- watch.jpg
- tablet.jpg
- camera.jpg

## 🔐 Admin Access

**Default Admin Credentials:**
- Username: `admin`
- Password: `admin123`

**To change credentials:**
Edit `admin/login.php` and update the variables:
```php
$admin_username = 'your_admin_username';
$admin_password = 'your_admin_password';
```

## 📱 Usage

### For Customers
1. Browse products on the homepage
2. Click "View Details" to see product information
3. Add items to cart
4. Manage cart quantities
5. Proceed to checkout
6. Fill in shipping information
7. Complete order

### For Admins
1. Login at `/admin/login.php`
2. View dashboard statistics
3. Add new products via "Add Product"
4. Manage existing products
5. View all customer orders
6. Monitor sales analytics

## 🎨 Customization

### Styling
- Edit `assets/css/style.css` to customize the design
- The site uses a modern, responsive design with CSS Grid and Flexbox
- Color scheme can be easily modified in the CSS variables

### Adding Features
- **Payment Integration**: Add payment gateway integration in `checkout.php`
- **User Registration**: Create user accounts and login system
- **Email Notifications**: Send order confirmations via email
- **Product Categories**: Add category management
- **Search Functionality**: Implement product search
- **Reviews & Ratings**: Add customer review system

## 🔧 Database Schema

### Products Table
- `id`: Primary key
- `name`: Product name
- `price`: Product price
- `description`: Product description
- `image`: Image filename
- `stock`: Available quantity
- `created_at`: Creation timestamp

### Orders Table
- `id`: Primary key
- `user_name`: Customer name
- `email`: Customer email
- `address`: Shipping address
- `phone`: Customer phone
- `total_amount`: Order total
- `order_date`: Order timestamp

### Order Items Table
- `id`: Primary key
- `order_id`: Foreign key to orders
- `product_id`: Foreign key to products
- `quantity`: Item quantity
- `price`: Item price at time of order

## 🔧 Recent Fixes & Improvements

### Security Fixes
- ✅ **Fixed SQL Injection vulnerabilities** - All database queries now use prepared statements
- ✅ **Added input validation** - File upload validation and sanitization
- ✅ **Enhanced error handling** - Better error messages and logging
- ✅ **File upload security** - Restricted file types and size limits

### New Features
- ✅ **File upload functionality** - Admin can upload product images directly
- ✅ **Missing edit_product.php** - Created the missing edit product page
- ✅ **Installation wizard** - Added `install.php` for easy setup
- ✅ **Upload helper functions** - Centralized image upload handling
- ✅ **Better error handling** - Improved database connection and query error handling

### Code Quality
- ✅ **Prepared statements** - All SQL queries use parameterized statements
- ✅ **Input sanitization** - All user inputs are properly sanitized
- ✅ **File validation** - Image upload validation with type and size checks
- ✅ **Error logging** - Custom error handler for debugging

## 🚨 Security Notes

For production deployment:
1. **Change default admin credentials**
2. **Use HTTPS** for secure data transmission
3. **Implement proper password hashing** (bcrypt/Argon2)
4. **Add input validation and sanitization** ✅ (Implemented)
5. **Use prepared statements** ✅ (Implemented)
6. **Set up proper file permissions**
7. **Regular security updates**

## 📞 Support

This is a learning project demonstrating:
- PHP backend development
- MySQL database design
- Modern CSS and responsive design
- E-commerce functionality
- Admin panel development

## 📄 License

This project is for educational purposes. Feel free to modify and use for learning or personal projects.

---

**Happy Coding! 🎉** 
