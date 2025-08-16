<?php
require_once 'inc/db.php';

// Define the categories and their display order
$categories = [
    'Mobile' => 'Mobile',
    'Laptop' => 'Laptop',
    'Keyboard and Mouse' => 'Keyboard and Mouse',
    'Headphones' => 'Headphones',
    'Accessories' => 'Accessories',
];

// Fetch all products with stock > 0, grouped by category
$result = $conn->query("SELECT * FROM products WHERE stock > 0 ORDER BY FIELD(category, 'Mobile', 'Laptop', 'Keyboard and Mouse', 'Headphones', 'Accessories'), category, created_at DESC");

// Organize products by category
$products_by_category = [];
while ($row = $result->fetch_assoc()) {
    $cat = $row['category'] ?: 'Other';
    $products_by_category[$cat][] = $row;
}

$page_title = "All Products - CosmicCart";
include 'inc/header.php';
?>

<!-- Products Hero Section -->
<section class="hero-section" style="padding: 60px 20px;">
    <h1 class="hero-title">Cosmic Collection</h1>
    <p class="hero-subtitle">Explore our stellar selection of premium products from across the galaxy</p>
</section>

<?php if (isset($_SESSION['cart_message'])): ?>
    <div class="message <?php echo $_SESSION['cart_message_type']; ?>">
        <?php echo $_SESSION['cart_message']; ?>
    </div>
    <?php 
    unset($_SESSION['cart_message']);
    unset($_SESSION['cart_message_type']);
    ?>
<?php endif; ?>

<?php foreach ($categories as $cat_key => $cat_label): ?>
    <?php if (!empty($products_by_category[$cat_key])): ?>
        <section style="margin: 60px 0;">
            <?php
            // Map category names to anchor IDs
            $anchor_map = [
                'Mobile' => 'mobile-section',
                'Laptop' => 'laptop-section',
                'Keyboard and Mouse' => 'keyboard-mouse-section',
                'Headphones' => 'headphones-section',
                'Accessories' => 'accessories-section'
            ];
            $anchor_id = isset($anchor_map[$cat_key]) ? $anchor_map[$cat_key] : strtolower(str_replace(' ', '-', $cat_key)) . '-section';
            ?>
            <h2 id="<?php echo $anchor_id; ?>" style="margin: 40px 0 30px 0; font-size: 2.5rem; background: linear-gradient(135deg, var(--primary), var(--accent)); background-clip: text; -webkit-background-clip: text; color: transparent; text-align: center; position: relative;">
                <?php echo htmlspecialchars($cat_label); ?>
                <div style="position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 100px; height: 3px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 2px;"></div>
            </h2>
            <div class="product-grid">
                <?php foreach ($products_by_category[$cat_key] as $product): ?>
                    <div class="product-card">
                        <?php if (strtotime($product['created_at']) > strtotime('-7 days')): ?>
                            <div class="product-badge badge-new">New</div>
                        <?php endif; ?>
                        
                        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             onerror="this.src='assets/images/placeholder.jpg'">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="description"><?php echo htmlspecialchars(substr($product['description'], 0, 120)) . '...'; ?></p>
                        <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
                        
                        <div class="btn-container">
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-glass">View Details</a>
                            
                            <form method="POST" action="cart.php" style="display: inline;">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-cosmic" data-product-id="<?php echo $product['id']; ?>">Add to Cart</button>
                            </form>
                        </div>
                        
                        <button class="quick-add" data-product-id="<?php echo $product['id']; ?>">Quick Add</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
<?php endforeach; ?>

<?php if (!empty($products_by_category['Other'])): ?>
    <section style="margin: 60px 0;">
        <h2 id="other-section" style="margin: 40px 0 30px 0; font-size: 2.5rem; background: linear-gradient(135deg, var(--primary), var(--accent)); background-clip: text; -webkit-background-clip: text; color: transparent; text-align: center; position: relative;">
            Other Products
            <div style="position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 100px; height: 3px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 2px;"></div>
        </h2>
        <div class="product-grid">
            <?php foreach ($products_by_category['Other'] as $product): ?>
                <div class="product-card">
                    <?php if (strtotime($product['created_at']) > strtotime('-7 days')): ?>
                        <div class="product-badge badge-new">New</div>
                    <?php endif; ?>
                    
                    <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         onerror="this.src='assets/images/placeholder.jpg'">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="description"><?php echo htmlspecialchars(substr($product['description'], 0, 120)) . '...'; ?></p>
                    <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
                    
                    <div class="btn-container">
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-glass">View Details</a>
                        
                        <form method="POST" action="cart.php" style="display: inline;">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-cosmic" data-product-id="<?php echo $product['id']; ?>">Add to Cart</button>
                        </form>
                    </div>
                    
                    <button class="quick-add" data-product-id="<?php echo $product['id']; ?>">Quick Add</button>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>



<script>
    // Cross-page navigation support for product page
    function initProductPageNavigation() {
        // Handle cross-page navigation from sessionStorage
        function handleCrossPageNavigation() {
            const targetSection = sessionStorage.getItem('scrollToSection');
            if (targetSection) {
                // Clear the stored section
                sessionStorage.removeItem('scrollToSection');
                
                // Find and scroll to the target section
                const target = document.querySelector(`#${targetSection}`);
                if (target) {
                    // Add delay to ensure page is fully rendered
                    setTimeout(() => {
                        scrollToTarget(target, 3000); // Longer highlight for cross-page navigation
                    }, 200);
                }
            }
        }

        // Handle hash in URL on page load (for direct links)
        if (window.location.hash) {
            setTimeout(() => {
                const target = document.querySelector(window.location.hash);
                if (target) {
                    scrollToTarget(target);
                }
            }, 100);
        }

        // Initialize after page load
        window.addEventListener('load', handleCrossPageNavigation);
    }

    // Optimized smooth scroll function with better performance
    function smoothScrollTo(targetPosition, duration = 600) {
        const startPosition = window.pageYOffset;
        const distance = targetPosition - startPosition;
        
        // Skip animation if distance is very small
        if (Math.abs(distance) < 10) {
            window.scrollTo(0, targetPosition);
            return Promise.resolve();
        }
        
        return new Promise((resolve) => {
            let startTime = null;
            let animationId = null;

            function animation(currentTime) {
                if (startTime === null) startTime = currentTime;
                const timeElapsed = currentTime - startTime;
                const progress = Math.min(timeElapsed / duration, 1);
                
                // Use optimized easing function
                const easeProgress = easeOutCubic(progress);
                const currentPosition = startPosition + (distance * easeProgress);
                
                window.scrollTo(0, currentPosition);
                
                if (progress < 1) {
                    animationId = requestAnimationFrame(animation);
                } else {
                    resolve();
                }
            }

            // Optimized cubic easing function
            function easeOutCubic(t) {
                return 1 - Math.pow(1 - t, 3);
            }

            animationId = requestAnimationFrame(animation);
        });
    }

    // Optimized scroll to target function with better performance
    function scrollToTarget(target, highlightDuration = 2000) {
        // Calculate proper offset (cached for performance)
        const navbar = document.querySelector('.navbar');
        const headerHeight = navbar ? navbar.offsetHeight : 80;
        const additionalOffset = 30;
        
        const targetPosition = Math.max(0, target.offsetTop - headerHeight - additionalOffset);
        
        // Use optimized smooth scrolling
        const scrollPromise = smoothScrollTo(targetPosition, 600);
        
        // Add visual feedback immediately (non-blocking)
        requestAnimationFrame(() => {
            target.style.scrollMarginTop = `${headerHeight + additionalOffset}px`;
            
            // Add highlight effect
            target.classList.add('section-highlight');
            
            // Remove highlight after duration
            setTimeout(() => {
                target.classList.remove('section-highlight');
            }, highlightDuration);
        });
        
        return scrollPromise;
    }

    // Optimized product page scroll manager
    class ProductPageScrollManager {
        constructor() {
            this.sections = [];
            this.ticking = false;
            this.lastScrollY = 0;
            
            this.cacheSections();
            this.initScrollHandler();
        }
        
        cacheSections() {
            this.sections = Array.from(document.querySelectorAll('section h2[id]')).map(section => ({
                element: section,
                id: section.id,
                top: section.offsetTop,
                height: section.parentElement.offsetHeight,
                parent: section.parentElement
            }));
        }
        
        initScrollHandler() {
            window.addEventListener('scroll', () => {
                if (!this.ticking) {
                    requestAnimationFrame(() => {
                        this.updateActiveSection();
                        this.ticking = false;
                    });
                    this.ticking = true;
                }
            }, { passive: true });
            
            // Update cached positions on resize
            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    this.updateCachedPositions();
                }, 250);
            });
        }
        
        updateCachedPositions() {
            this.sections.forEach(section => {
                section.top = section.element.offsetTop;
                section.height = section.parent.offsetHeight;
            });
        }
        
        updateActiveSection() {
            const scrollPosition = window.scrollY + 150;
            let currentSection = null;
            
            // Find current section using cached positions
            for (const section of this.sections) {
                if (scrollPosition >= section.top && scrollPosition < section.top + section.height) {
                    currentSection = section;
                    break;
                }
            }
            
            // Update visual feedback efficiently
            this.sections.forEach(section => {
                const isActive = section === currentSection;
                const hasActive = section.element.classList.contains('active-section');
                
                if (isActive && !hasActive) {
                    section.element.classList.add('active-section');
                } else if (!isActive && hasActive) {
                    section.element.classList.remove('active-section');
                }
            });
        }
    }

    // Initialize optimized product page scroll manager
    let productScrollManager;
    function initProductPageScrollHighlighting() {
        productScrollManager = new ProductPageScrollManager();
    }

    // Initialize all product page navigation features
    document.addEventListener('DOMContentLoaded', () => {
        initProductPageNavigation();
        initProductPageScrollHighlighting();
    });
</script>

<?php include 'inc/footer.php'; ?> 
