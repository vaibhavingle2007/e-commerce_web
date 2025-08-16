<?php
require_once 'inc/db.php';

$page_title = "ElectroCart - Discover the Universe";
include 'inc/header.php';

$result = $conn->query("SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC");
?>

<!-- Hero Section -->
<section id="hero" class="hero-section electronics-hero">
    <div class="hero-content">
        <div class="hero-text">
            <h1 class="hero-title electronics-title">Premium Electronics</h1>
            <p class="hero-subtitle electronics-subtitle">Discover cutting-edge smartphones, laptops, and tech accessories with futuristic design</p>
            <div class="hero-buttons">
                <a href="product.php" class="btn btn-electric-primary">Shop Now</a>
                <a href="#categories" class="btn btn-electric-secondary">Browse Categories</a>
            </div>
        </div>
        <div class="hero-product">
            <div class="flagship-product">
                <img src="https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=500&h=500&fit=crop&crop=center&auto=format&q=80" alt="iPhone 15 Pro Max - Flagship Smartphone" class="flagship-image" onerror="this.src='assets/images/placeholder.jpg'">
                <div class="product-glow"></div>
            </div>
        </div>
    </div>
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

<!-- Featured Deals Section -->
<section id="deals" class="featured-deals-section">
    <h2 class="section-title">Featured Deals</h2>
    <p class="section-subtitle">Hurry! Limited stock items with exclusive discounts</p>
    
    <div class="deals-grid">
        <?php
        // Fetch products from database for deals section - prioritize low stock items (less than 10)
        $deals_query = "SELECT * FROM products WHERE stock > 0 AND stock < 10 ORDER BY stock ASC, RAND() LIMIT 4";
        $deals_result = $conn->query($deals_query);
        
        // If we don't have enough low stock items, fill with other products
        if ($deals_result->num_rows < 4) {
            $remaining_slots = 4 - $deals_result->num_rows;
            $additional_query = "SELECT * FROM products WHERE stock >= 10 ORDER BY RAND() LIMIT $remaining_slots";
            $additional_result = $conn->query($additional_query);
        }
        
        // Define deal types and discounts for variety
        $deal_types = ['flash', 'weekend', 'clearance', 'daily'];
        $discount_ranges = [15, 20, 25, 30, 35];

        
        // Combine low stock items with additional items if needed
        $all_deal_products = [];
        
        // Add low stock items first
        while ($product = $deals_result->fetch_assoc()) {
            $all_deal_products[] = $product;
        }
        
        // Add additional items if we have them
        if (isset($additional_result) && $additional_result->num_rows > 0) {
            while ($product = $additional_result->fetch_assoc()) {
                $all_deal_products[] = $product;
            }
        }
        
        $deal_index = 0;
        foreach ($all_deal_products as $product): 
            // Generate realistic deal data
            $discount_percent = $discount_ranges[array_rand($discount_ranges)];
            $original_price = $product['price'] * (1 + ($discount_percent / 100));
            $sale_price = $product['price'];
            $savings = $original_price - $sale_price;
            // Adjust deal type based on stock level for more urgency
            if ($product['stock'] <= 3) {
                $deal_type = 'urgent';
            } elseif ($product['stock'] <= 5) {
                $deal_type = 'limited-stock';
            } else {
                $deal_type = $deal_types[$deal_index % count($deal_types)];
            }
            
            // Extract brand from product name (first word)
            $name_parts = explode(' ', $product['name']);
            $brand = $name_parts[0];
            $product_model = implode(' ', array_slice($name_parts, 1));
            if (empty($product_model)) {
                $product_model = $product['name'];
                $brand = 'Premium';
            }
            
            // Generate specs based on category
            $specs = '';
            switch (strtolower($product['category'])) {
                case 'mobile':
                    $specs = '128GB Storage, 5G Ready, Advanced Camera';
                    break;
                case 'laptop':
                    $specs = 'Intel i7, 16GB RAM, 512GB SSD';
                    break;
                case 'headphones':
                    $specs = 'Wireless, Noise Cancelling, 30hr Battery';
                    break;
                case 'keyboard and mouse':
                    $specs = 'Mechanical Keys, RGB Backlight, Wireless';
                    break;
                case 'accessories':
                    $specs = 'Premium Quality, Universal Compatibility';
                    break;
                default:
                    $specs = 'Premium Features, Latest Technology';
            }
        ?>
            <div class="deal-card" data-deal-type="<?php echo $deal_type; ?>" <?php echo ($product['stock'] < 10) ? 'data-low-stock="true"' : ''; ?>>
                <div class="deal-badges">
                    <div class="discount-badge">
                        <?php echo $discount_percent; ?>% OFF
                    </div>
                    <div class="deal-type-badge <?php echo $deal_type; ?>">
                        <?php echo strtoupper($deal_type); ?>
                    </div>
                </div>
                

                <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     onerror="this.src='assets/images/placeholder.jpg'">
                
                <div class="deal-content">
                    <div class="product-brand"><?php echo htmlspecialchars($brand); ?></div>
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="product-specs"><?php echo htmlspecialchars($specs); ?></p>
                    <p class="deal-description"><?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?></p>
                    
                    <div class="price-section">
                        <div class="original-price">₹<?php echo number_format($original_price, 2); ?></div>
                        <div class="sale-price">₹<?php echo number_format($sale_price, 2); ?></div>
                        <div class="savings">Save ₹<?php echo number_format($savings, 2); ?></div>
                    </div>
                    
                    <div class="stock-indicator">
                        <?php 
                        // Enhanced stock urgency messaging
                        if ($product['stock'] <= 3) {
                            echo '<span class="stock-text critical">ALMOST GONE! Only ' . $product['stock'] . ' left!</span>';
                            $stock_class = 'critical';
                        } elseif ($product['stock'] <= 5) {
                            echo '<span class="stock-text low">HURRY! Only ' . $product['stock'] . ' left!</span>';
                            $stock_class = 'low';
                        } elseif ($product['stock'] < 10) {
                            echo '<span class="stock-text limited">Limited Stock: ' . $product['stock'] . ' remaining</span>';
                            $stock_class = 'limited';
                        } else {
                            echo '<span class="stock-text">Only ' . $product['stock'] . ' left!</span>';
                            $stock_class = 'normal';
                        }
                        ?>
                        <div class="stock-bar <?php echo $stock_class; ?>">
                            <div class="stock-fill" style="width: <?php echo min(100, ($product['stock'] / 20) * 100); ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="deal-actions">
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-deal-secondary">View Details</a>
                        <form method="POST" action="cart.php" style="display: inline;">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-deal-primary">
                                Grab Deal
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php 
            $deal_index++;
        endforeach; 
        ?>
        
        <?php if (empty($all_deal_products)): ?>
            <div class="no-deals-message">
                <h3>No deals available at the moment</h3>
                <p>Check back soon for amazing offers on premium electronics!</p>
                <a href="product.php" class="btn btn-electric-primary">Browse All Products</a>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="deals-footer">
        <p class="deals-note">* Prices shown include all applicable discounts. Limited time offers while supplies last.</p>
        <a href="product.php?deals=1" class="btn btn-electric-secondary">View All Deals</a>
    </div>
</section>

<!-- Electronics Categories Section -->
<section id="categories" class="categories-section">
    <h2 class="section-title">Shop by Category</h2>
    <div class="categories-grid">
        <div class="category-card" data-category="mobile">
            <div class="category-icon">
                <span class="icon">📱</span>
            </div>
            <h3>Mobile</h3>
            <p>Latest smartphones with cutting-edge technology</p>
            <a href="product.php#mobile-section" class="category-link">
                <span>Explore</span>
                <span class="arrow">→</span>
            </a>
        </div>
        
        <div class="category-card" data-category="laptop">
            <div class="category-icon">
                <span class="icon">💻</span>
            </div>
            <h3>Laptop</h3>
            <p>High-performance laptops for work and gaming</p>
            <a href="product.php#laptop-section" class="category-link">
                <span>Explore</span>
                <span class="arrow">→</span>
            </a>
        </div>
        
        <div class="category-card" data-category="keyboard-mouse">
            <div class="category-icon">
                <span class="icon">⌨️</span>
            </div>
            <h3>Keyboard and Mouse</h3>
            <p>Premium input devices for productivity and gaming</p>
            <a href="product.php#keyboard-mouse-section" class="category-link">
                <span>Explore</span>
                <span class="arrow">→</span>
            </a>
        </div>
        
        <div class="category-card" data-category="headphones">
            <div class="category-icon">
                <span class="icon">🎧</span>
            </div>
            <h3>Headphones</h3>
            <p>Premium audio devices for immersive sound experience</p>
            <a href="product.php#headphones-section" class="category-link">
                <span>Explore</span>
                <span class="arrow">→</span>
            </a>
        </div>
        
        <div class="category-card" data-category="accessories">
            <div class="category-icon">
                <span class="icon">🔌</span>
            </div>
            <h3>Accessories</h3>
            <p>Essential tech accessories and peripherals</p>
            <a href="product.php#accessories-section" class="category-link">
                <span>Explore</span>
                <span class="arrow">→</span>
            </a>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section id="products">
    <h2 style="text-align: center; margin: 50px 0 30px 0; font-size: 2.5rem; background: linear-gradient(135deg, var(--primary), var(--accent)); background-clip: text; -webkit-background-clip: text; color: transparent;">Featured Products</h2>
    
    <div class="product-grid">
        <?php 
        // Reset the result pointer for products
        $result = $conn->query("SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC");
        while ($product = $result->fetch_assoc()): ?>
            <div class="product-card">
                <?php if (strtotime($product['created_at']) > strtotime('-7 days')): ?>
                    <div class="product-badge badge-new">New</div>
                <?php endif; ?>
                
                <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     onerror="this.src='assets/images/placeholder.jpg'">
                
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
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
        <?php endwhile; ?>
    </div>
</section>

<!-- Newsletter Section -->
<section id="newsletter" class="newsletter-section">
    <h2>Join the ElectroCart Community</h2>
    <p>Subscribe to our newsletter for exclusive deals on premium electronics and the latest tech updates</p>
    <form class="newsletter-form" onsubmit="subscribeNewsletter(event)">
        <input type="email" placeholder="Enter your email address" required>
        <button type="submit" class="btn btn-cosmic">Subscribe</button>
    </form>
</section>

<script>
    function subscribeNewsletter(event) {
        event.preventDefault();
        const email = event.target.querySelector('input[type="email"]').value;
        showToast('Thank you for subscribing to our cosmic newsletter!', 'success');
        event.target.reset();
    }

    // Enhanced smooth scrolling for anchor links with cross-page navigation support
    function initSmoothScrolling() {
        // Handle same-page anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const target = document.querySelector(targetId);
                
                if (target) {
                    scrollToTarget(target);
                }
            });
        });

        // Handle cross-page category navigation links
        document.querySelectorAll('a[href^="product.php#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                const [page, hash] = href.split('#');
                
                // Store the target hash for the product page
                sessionStorage.setItem('scrollToSection', hash);
                
                // Navigate to the product page
                window.location.href = href;
            });
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

        // Update active section indicator after scroll completes
        if (scrollPromise && scrollPromise.then) {
            scrollPromise.then(() => {
                if (scrollManager) {
                    scrollManager.updateActiveSectionIndicator(target);
                }
            });
        } else {
            // Fallback for immediate update
            setTimeout(() => {
                if (scrollManager) {
                    scrollManager.updateActiveSectionIndicator(target);
                }
            }, 100);
        }
        
        return scrollPromise;
    }

    // Update active section visual indicator
    function updateActiveSectionIndicator(activeTarget) {
        // Remove active class from all category cards
        document.querySelectorAll('.category-card').forEach(card => {
            card.classList.remove('active-category');
        });

        // Add active class to corresponding category card if we're on homepage
        if (activeTarget && activeTarget.id) {
            const categoryMap = {
                'mobile-section': 'mobile',
                'laptop-section': 'laptop', 
                'keyboard-mouse-section': 'keyboard-mouse',
                'headphones-section': 'headphones',
                'accessories-section': 'accessories'
            };
            
            const categoryKey = categoryMap[activeTarget.id];
            if (categoryKey) {
                const categoryCard = document.querySelector(`[data-category="${categoryKey}"]`);
                if (categoryCard) {
                    categoryCard.classList.add('active-category');
                }
            }
        }
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
            
            // Cleanup function for interruption
            return () => {
                if (animationId) {
                    cancelAnimationFrame(animationId);
                    resolve();
                }
            };
        });
    }

    // Initialize smooth scrolling when DOM is ready
    document.addEventListener('DOMContentLoaded', initSmoothScrolling);

    // Enhanced page loading and scroll management with cross-page navigation support
    function initPageScrolling() {
        // Add loading class initially
        document.body.classList.add('page-loading');
        
        // Remove loading class when page is fully loaded
        window.addEventListener('load', () => {
            document.body.classList.remove('page-loading');
            document.body.classList.add('page-loaded');
            
            // Handle cross-page navigation after page load
            handleCrossPageNavigation();
        });

        // Handle hash in URL on page load (for direct links)
        if (window.location.hash) {
            setTimeout(() => {
                const target = document.querySelector(window.location.hash);
                if (target) {
                    scrollToTarget(target);
                }
            }, 100); // Small delay to ensure page is rendered
        }

        // Handle cross-page navigation from sessionStorage
        function handleCrossPageNavigation() {
            const targetSection = sessionStorage.getItem('scrollToSection');
            if (targetSection) {
                // Clear the stored section
                sessionStorage.removeItem('scrollToSection');
                
                // Find and scroll to the target section
                const target = document.querySelector(`#${targetSection}`);
                if (target) {
                    // Add a longer delay for cross-page navigation to ensure page is fully rendered
                    setTimeout(() => {
                        scrollToTarget(target, 3000); // Longer highlight duration for cross-page navigation
                    }, 200);
                }
            }
        }
    }

    // Optimized scroll performance manager
    class ScrollPerformanceManager {
        constructor() {
            this.sections = [];
            this.navLinks = [];
            this.scrollBtn = null;
            this.lastScrollY = 0;
            this.ticking = false;
            this.scrollThreshold = 300;
            
            // Cache DOM elements on initialization
            this.cacheDOMElements();
            this.initScrollHandlers();
        }
        
        cacheDOMElements() {
            // Cache sections and their positions
            this.sections = Array.from(document.querySelectorAll('section[id]')).map(section => ({
                element: section,
                id: section.id,
                top: section.offsetTop,
                height: section.offsetHeight
            }));
            
            // Cache navigation links
            this.navLinks = Array.from(document.querySelectorAll('.nav-menu a[href^="#"]'));
            
            // Create and cache scroll to top button
            this.createScrollToTopButton();
        }
        
        createScrollToTopButton() {
            this.scrollBtn = document.createElement('button');
            this.scrollBtn.className = 'scroll-to-top';
            this.scrollBtn.innerHTML = '↑';
            this.scrollBtn.setAttribute('aria-label', 'Scroll to top');
            document.body.appendChild(this.scrollBtn);
            
            this.scrollBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
        
        initScrollHandlers() {
            // Single optimized scroll handler
            window.addEventListener('scroll', this.handleScroll.bind(this), { passive: true });
            
            // Update cached positions on resize (throttled)
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
                section.height = section.element.offsetHeight;
            });
        }
        
        handleScroll() {
            if (!this.ticking) {
                requestAnimationFrame(() => {
                    this.updateScrollState();
                    this.ticking = false;
                });
                this.ticking = true;
            }
        }
        
        updateScrollState() {
            const scrollY = window.scrollY;
            const scrollDirection = scrollY > this.lastScrollY ? 'down' : 'up';
            
            // Update active section (optimized)
            this.updateActiveSection(scrollY);
            
            // Update scroll to top button visibility
            this.updateScrollButton(scrollY);
            
            this.lastScrollY = scrollY;
        }
        
        updateActiveSection(scrollY) {
            const scrollPosition = scrollY + 200; // Offset for better detection
            let currentSection = null;
            
            // Use cached positions for better performance
            for (const section of this.sections) {
                if (scrollPosition >= section.top && scrollPosition < section.top + section.height) {
                    currentSection = section;
                    break;
                }
            }
            
            if (currentSection) {
                this.updateActiveSectionIndicator(currentSection.element);
                this.updateNavigationLinks(currentSection.id);
            }
        }
        
        updateScrollButton(scrollY) {
            const shouldShow = scrollY > this.scrollThreshold;
            const isVisible = this.scrollBtn.classList.contains('visible');
            
            if (shouldShow && !isVisible) {
                this.scrollBtn.classList.add('visible');
            } else if (!shouldShow && isVisible) {
                this.scrollBtn.classList.remove('visible');
            }
        }
        
        updateNavigationLinks(currentSectionId) {
            // Batch DOM updates for better performance
            const targetHref = `#${currentSectionId}`;
            
            this.navLinks.forEach(link => {
                const isActive = link.getAttribute('href') === targetHref;
                const hasActive = link.classList.contains('active');
                
                if (isActive && !hasActive) {
                    link.classList.add('active');
                } else if (!isActive && hasActive) {
                    link.classList.remove('active');
                }
            });
        }
        
        updateActiveSectionIndicator(activeTarget) {
            // Remove active class from all category cards (batch operation)
            const categoryCards = document.querySelectorAll('.category-card');
            categoryCards.forEach(card => {
                if (card.classList.contains('active-category')) {
                    card.classList.remove('active-category');
                }
            });

            // Add active class to corresponding category card if we're on homepage
            if (activeTarget && activeTarget.id) {
                const categoryMap = {
                    'mobile-section': 'mobile',
                    'laptop-section': 'laptop', 
                    'keyboard-mouse-section': 'keyboard-mouse',
                    'headphones-section': 'headphones',
                    'accessories-section': 'accessories'
                };
                
                const categoryKey = categoryMap[activeTarget.id];
                if (categoryKey) {
                    const categoryCard = document.querySelector(`[data-category="${categoryKey}"]`);
                    if (categoryCard) {
                        categoryCard.classList.add('active-category');
                    }
                }
            }
        }
    }

    // Enhanced scroll performance features
    class ScrollEnhancementManager {
        constructor() {
            this.scrollProgress = null;
            this.perfMonitor = null;
            this.lastFrameTime = performance.now();
            this.frameCount = 0;
            this.fps = 0;
            
            this.initScrollProgress();
            this.initPerformanceMonitoring();
        }
        
        initScrollProgress() {
            // Create scroll progress indicator
            this.scrollProgress = document.createElement('div');
            this.scrollProgress.className = 'scroll-progress';
            document.body.appendChild(this.scrollProgress);
            
            // Update progress on scroll (throttled)
            let progressTicking = false;
            window.addEventListener('scroll', () => {
                if (!progressTicking) {
                    requestAnimationFrame(() => {
                        this.updateScrollProgress();
                        progressTicking = false;
                    });
                    progressTicking = true;
                }
            }, { passive: true });
        }
        
        updateScrollProgress() {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = Math.min(scrollTop / docHeight, 1);
            
            this.scrollProgress.style.transform = `scaleX(${scrollPercent})`;
        }
        
        initPerformanceMonitoring() {
            // Create performance monitor (hidden by default)
            this.perfMonitor = document.createElement('div');
            this.perfMonitor.className = 'perf-monitor';
            this.perfMonitor.innerHTML = `
                <div>FPS: <span id="fps-counter">--</span></div>
                <div>Scroll: <span id="scroll-pos">--</span></div>
                <div>Memory: <span id="memory-usage">--</span></div>
            `;
            document.body.appendChild(this.perfMonitor);
            
            // Show monitor with Ctrl+Shift+P
            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey && e.shiftKey && e.key === 'P') {
                    this.togglePerformanceMonitor();
                }
            });
            
            // Start performance monitoring
            this.startPerformanceMonitoring();
        }
        
        togglePerformanceMonitor() {
            this.perfMonitor.classList.toggle('visible');
        }
        
        startPerformanceMonitoring() {
            const updatePerformanceStats = () => {
                const currentTime = performance.now();
                const deltaTime = currentTime - this.lastFrameTime;
                
                this.frameCount++;
                
                // Update FPS every second
                if (deltaTime >= 1000) {
                    this.fps = Math.round((this.frameCount * 1000) / deltaTime);
                    this.frameCount = 0;
                    this.lastFrameTime = currentTime;
                    
                    // Update performance display
                    if (this.perfMonitor.classList.contains('visible')) {
                        document.getElementById('fps-counter').textContent = this.fps;
                        document.getElementById('scroll-pos').textContent = Math.round(window.scrollY);
                        
                        if ('memory' in performance) {
                            const memoryMB = Math.round(performance.memory.usedJSHeapSize / 1024 / 1024);
                            document.getElementById('memory-usage').textContent = memoryMB + 'MB';
                        }
                    }
                }
                
                requestAnimationFrame(updatePerformanceStats);
            };
            
            requestAnimationFrame(updatePerformanceStats);
        }
    }

    // Initialize optimized scroll manager and enhancements
    let scrollManager, scrollEnhancements;
    document.addEventListener('DOMContentLoaded', () => {
        // Wait for layout to stabilize before initializing
        setTimeout(() => {
            scrollManager = new ScrollPerformanceManager();
            scrollEnhancements = new ScrollEnhancementManager();
            
            // Log performance optimization status
            console.log('🚀 Scroll performance optimizations loaded');
            console.log('📊 Press Ctrl+Shift+P to toggle performance monitor');
        }, 100);
    });

    // Initialize page scrolling
    document.addEventListener('DOMContentLoaded', initPageScrolling);

    // Performance optimization: Intersection Observer for animations
    function initAnimationObserver() {
        const animatedElements = document.querySelectorAll('.product-card, .deal-card, .category-card');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        animatedElements.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            observer.observe(el);
        });
    }

    // Initialize animation observer
    document.addEventListener('DOMContentLoaded', initAnimationObserver);
</script>

<?php include 'inc/footer.php'; ?> 
