/**
 * Category Navigation Validation Test Suite
 * Automated tests to validate all requirements for category navigation functionality
 */

class CategoryNavigationValidator {
    constructor() {
        this.testResults = [];
        this.requirements = {
            '1.1': 'Display category buttons for Mobile, Laptop, Keyboard and Mouse, Headphones, and Accessories',
            '1.2': 'Each category button displays appropriate icon and description',
            '1.3': 'Category buttons are visually consistent with existing design theme',
            '2.1': 'Mobile category button navigates to Mobile category section',
            '2.2': 'Laptop category button navigates to Laptop category section',
            '2.3': 'Keyboard and Mouse category button navigates to Keyboard and Mouse category section',
            '2.4': 'Headphones category button navigates to Headphones category section',
            '2.5': 'Accessories category button navigates to Accessories category section',
            '3.1': 'Use smooth scrolling animation to target section',
            '3.2': 'Position section header properly below navigation bar',
            '3.3': 'Provide visual feedback to indicate active section',
            '4.1': 'Navigate from homepage to product page and scroll to correct category section',
            '4.2': 'Scroll to correct category section when already on product page',
            '4.3': 'Maintain proper scroll position and page state with browser navigation'
        };
    }

    // Initialize validator
    async init() {
        console.log('🔍 Initializing Category Navigation Validator...');
        await this.loadPages();
        return this;
    }

    // Load required pages for testing
    async loadPages() {
        try {
            // Load homepage content
            const homepageResponse = await fetch('index.php');
            this.homepageContent = await homepageResponse.text();
            
            // Load product page content
            const productResponse = await fetch('product.php');
            this.productPageContent = await productResponse.text();
            
            console.log('✅ Pages loaded successfully');
        } catch (error) {
            console.error('❌ Failed to load pages:', error);
            throw error;
        }
    }

    // Test Requirement 1.1: Category buttons display
    testRequirement1_1() {
        console.log('🧪 Testing Requirement 1.1: Category buttons display');
        
        const expectedCategories = ['Mobile', 'Laptop', 'Keyboard and Mouse', 'Headphones', 'Accessories'];
        const foundCategories = [];
        
        expectedCategories.forEach(category => {
            if (this.homepageContent.includes(`<h3>${category}</h3>`)) {
                foundCategories.push(category);
            }
        });
        
        const passed = foundCategories.length === expectedCategories.length;
        
        this.testResults.push({
            requirement: '1.1',
            description: this.requirements['1.1'],
            passed,
            details: {
                expected: expectedCategories,
                found: foundCategories,
                missing: expectedCategories.filter(cat => !foundCategories.includes(cat))
            }
        });
        
        return passed;
    }

    // Test Requirement 1.2: Icons and descriptions
    testRequirement1_2() {
        console.log('🧪 Testing Requirement 1.2: Icons and descriptions');
        
        const expectedIcons = {
            'Mobile': '📱',
            'Laptop': '💻',
            'Keyboard and Mouse': '⌨️',
            'Headphones': '🎧',
            'Accessories': '🔌'
        };
        
        const expectedDescriptions = {
            'Mobile': 'Latest smartphones with cutting-edge technology',
            'Laptop': 'High-performance laptops for work and gaming',
            'Keyboard and Mouse': 'Premium input devices for productivity and gaming',
            'Headphones': 'Premium audio devices for immersive sound experience',
            'Accessories': 'Essential tech accessories and peripherals'
        };
        
        let iconsFound = 0;
        let descriptionsFound = 0;
        
        Object.entries(expectedIcons).forEach(([category, icon]) => {
            if (this.homepageContent.includes(`<span class="icon">${icon}</span>`)) {
                iconsFound++;
            }
        });
        
        Object.entries(expectedDescriptions).forEach(([category, description]) => {
            if (this.homepageContent.includes(description)) {
                descriptionsFound++;
            }
        });
        
        const passed = iconsFound === 5 && descriptionsFound === 5;
        
        this.testResults.push({
            requirement: '1.2',
            description: this.requirements['1.2'],
            passed,
            details: {
                iconsFound,
                descriptionsFound,
                totalExpected: 5
            }
        });
        
        return passed;
    }

    // Test Requirement 1.3: Visual consistency
    testRequirement1_3() {
        console.log('🧪 Testing Requirement 1.3: Visual consistency');
        
        const hasConsistentStructure = this.homepageContent.includes('class="category-card"') &&
                                     this.homepageContent.includes('class="category-icon"') &&
                                     this.homepageContent.includes('class="category-link"');
        
        const hasConsistentStyling = this.homepageContent.includes('categories-grid') &&
                                   this.homepageContent.includes('categories-section');
        
        const passed = hasConsistentStructure && hasConsistentStyling;
        
        this.testResults.push({
            requirement: '1.3',
            description: this.requirements['1.3'],
            passed,
            details: {
                hasConsistentStructure,
                hasConsistentStyling
            }
        });
        
        return passed;
    }

    // Test Requirements 2.1-2.5: Category navigation links
    testRequirements2_1to2_5() {
        console.log('🧪 Testing Requirements 2.1-2.5: Category navigation links');
        
        const expectedLinks = {
            '2.1': { category: 'Mobile', anchor: 'mobile-section' },
            '2.2': { category: 'Laptop', anchor: 'laptop-section' },
            '2.3': { category: 'Keyboard and Mouse', anchor: 'keyboard-mouse-section' },
            '2.4': { category: 'Headphones', anchor: 'headphones-section' },
            '2.5': { category: 'Accessories', anchor: 'accessories-section' }
        };
        
        Object.entries(expectedLinks).forEach(([reqId, { category, anchor }]) => {
            // Check homepage link
            const homepageLinkExists = this.homepageContent.includes(`href="product.php#${anchor}"`);
            
            // Check product page anchor
            const productAnchorExists = this.productPageContent.includes(`id="${anchor}"`);
            
            const passed = homepageLinkExists && productAnchorExists;
            
            this.testResults.push({
                requirement: reqId,
                description: this.requirements[reqId],
                passed,
                details: {
                    category,
                    anchor,
                    homepageLinkExists,
                    productAnchorExists
                }
            });
        });
        
        return true;
    }

    // Test Requirement 3.1: Smooth scrolling
    testRequirement3_1() {
        console.log('🧪 Testing Requirement 3.1: Smooth scrolling');
        
        // Check for smooth scrolling JavaScript implementation
        const hasSmoothScrollJS = this.homepageContent.includes('scrollToTarget') &&
                                this.homepageContent.includes('behavior: \'smooth\'') &&
                                this.productPageContent.includes('scrollToTarget');
        
        // Check for CSS smooth scrolling
        const hasCSSScrollBehavior = this.homepageContent.includes('scroll-behavior') ||
                                   this.productPageContent.includes('scroll-behavior');
        
        const passed = hasSmoothScrollJS;
        
        this.testResults.push({
            requirement: '3.1',
            description: this.requirements['3.1'],
            passed,
            details: {
                hasSmoothScrollJS,
                hasCSSScrollBehavior
            }
        });
        
        return passed;
    }

    // Test Requirement 3.2: Proper positioning
    testRequirement3_2() {
        console.log('🧪 Testing Requirement 3.2: Proper positioning');
        
        // Check for offset calculation in JavaScript
        const hasOffsetCalculation = this.homepageContent.includes('headerHeight') &&
                                    this.homepageContent.includes('additionalOffset') &&
                                    this.homepageContent.includes('offsetTop');
        
        // Check for scroll margin in CSS or JavaScript
        const hasScrollMargin = this.homepageContent.includes('scrollMarginTop') ||
                              this.productPageContent.includes('scroll-margin');
        
        const passed = hasOffsetCalculation;
        
        this.testResults.push({
            requirement: '3.2',
            description: this.requirements['3.2'],
            passed,
            details: {
                hasOffsetCalculation,
                hasScrollMargin
            }
        });
        
        return passed;
    }

    // Test Requirement 3.3: Visual feedback
    testRequirement3_3() {
        console.log('🧪 Testing Requirement 3.3: Visual feedback');
        
        // Check for visual feedback implementation
        const hasHighlightEffect = this.homepageContent.includes('section-highlight') ||
                                  this.homepageContent.includes('classList.add');
        
        const hasActiveIndicator = this.homepageContent.includes('updateActiveSectionIndicator') ||
                                 this.homepageContent.includes('active-category');
        
        const passed = hasHighlightEffect && hasActiveIndicator;
        
        this.testResults.push({
            requirement: '3.3',
            description: this.requirements['3.3'],
            passed,
            details: {
                hasHighlightEffect,
                hasActiveIndicator
            }
        });
        
        return passed;
    }

    // Test Requirement 4.1: Cross-page navigation
    testRequirement4_1() {
        console.log('🧪 Testing Requirement 4.1: Cross-page navigation');
        
        // Check for cross-page navigation handling
        const hasCrossPageHandling = this.homepageContent.includes('sessionStorage.setItem') &&
                                   this.homepageContent.includes('scrollToSection') &&
                                   this.productPageContent.includes('sessionStorage.getItem');
        
        const hasPageTransition = this.homepageContent.includes('window.location.href') ||
                                this.homepageContent.includes('product.php#');
        
        const passed = hasCrossPageHandling && hasPageTransition;
        
        this.testResults.push({
            requirement: '4.1',
            description: this.requirements['4.1'],
            passed,
            details: {
                hasCrossPageHandling,
                hasPageTransition
            }
        });
        
        return passed;
    }

    // Test Requirement 4.2: Same-page navigation
    testRequirement4_2() {
        console.log('🧪 Testing Requirement 4.2: Same-page navigation');
        
        // Check for same-page navigation handling
        const hasSamePageHandling = this.productPageContent.includes('scrollToTarget') &&
                                  this.productPageContent.includes('window.location.hash');
        
        const hasHashHandling = this.productPageContent.includes('querySelector') &&
                              this.productPageContent.includes('#');
        
        const passed = hasSamePageHandling && hasHashHandling;
        
        this.testResults.push({
            requirement: '4.2',
            description: this.requirements['4.2'],
            passed,
            details: {
                hasSamePageHandling,
                hasHashHandling
            }
        });
        
        return passed;
    }

    // Test Requirement 4.3: Browser navigation state
    testRequirement4_3() {
        console.log('🧪 Testing Requirement 4.3: Browser navigation state');
        
        // Check for browser state management
        const hasStateManagement = this.homepageContent.includes('window.location.hash') &&
                                  this.productPageContent.includes('window.location.hash');
        
        const hasHistoryHandling = this.homepageContent.includes('addEventListener') ||
                                 this.productPageContent.includes('addEventListener');
        
        const passed = hasStateManagement && hasHistoryHandling;
        
        this.testResults.push({
            requirement: '4.3',
            description: this.requirements['4.3'],
            passed,
            details: {
                hasStateManagement,
                hasHistoryHandling
            }
        });
        
        return passed;
    }

    // Run all validation tests
    async runAllTests() {
        console.log('🚀 Starting Category Navigation Validation Tests...');
        
        const startTime = performance.now();
        
        try {
            // Run all requirement tests
            this.testRequirement1_1();
            this.testRequirement1_2();
            this.testRequirement1_3();
            this.testRequirements2_1to2_5();
            this.testRequirement3_1();
            this.testRequirement3_2();
            this.testRequirement3_3();
            this.testRequirement4_1();
            this.testRequirement4_2();
            this.testRequirement4_3();
            
            const endTime = performance.now();
            const duration = endTime - startTime;
            
            // Generate summary report
            const report = this.generateValidationReport(duration);
            console.log('📊 Validation tests completed:', report);
            
            return report;
            
        } catch (error) {
            console.error('❌ Validation tests failed:', error);
            return { error: error.message };
        }
    }

    // Generate validation report
    generateValidationReport(duration) {
        const totalTests = this.testResults.length;
        const passedTests = this.testResults.filter(test => test.passed).length;
        const failedTests = totalTests - passedTests;
        const successRate = Math.round((passedTests / totalTests) * 100);
        
        const report = {
            summary: {
                totalTests,
                passedTests,
                failedTests,
                successRate,
                duration,
                timestamp: new Date().toISOString()
            },
            results: this.testResults,
            failedRequirements: this.testResults.filter(test => !test.passed),
            recommendations: []
        };
        
        // Generate recommendations based on failed tests
        report.failedRequirements.forEach(test => {
            switch (test.requirement) {
                case '1.1':
                    report.recommendations.push('Update homepage to include all required category buttons');
                    break;
                case '1.2':
                    report.recommendations.push('Add missing icons or descriptions to category buttons');
                    break;
                case '1.3':
                    report.recommendations.push('Ensure category buttons follow consistent design patterns');
                    break;
                case '3.1':
                    report.recommendations.push('Implement smooth scrolling functionality');
                    break;
                case '3.2':
                    report.recommendations.push('Add proper offset calculation for section positioning');
                    break;
                case '3.3':
                    report.recommendations.push('Implement visual feedback for active sections');
                    break;
                case '4.1':
                    report.recommendations.push('Add cross-page navigation handling with sessionStorage');
                    break;
                case '4.2':
                    report.recommendations.push('Implement same-page navigation for product page');
                    break;
                case '4.3':
                    report.recommendations.push('Add browser state management for navigation');
                    break;
                default:
                    if (test.requirement.startsWith('2.')) {
                        report.recommendations.push(`Fix navigation link for ${test.details.category} category`);
                    }
            }
        });
        
        return report;
    }

    // Export validation results
    exportValidationResults() {
        const exportData = {
            timestamp: new Date().toISOString(),
            userAgent: navigator.userAgent,
            testResults: this.testResults,
            requirements: this.requirements
        };
        
        const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        
        const a = document.createElement('a');
        a.href = url;
        a.download = `category-navigation-validation-${Date.now()}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        
        URL.revokeObjectURL(url);
        
        console.log('📁 Validation results exported');
    }

    // Generate HTML report
    generateHTMLReport() {
        const totalTests = this.testResults.length;
        const passedTests = this.testResults.filter(test => test.passed).length;
        const successRate = Math.round((passedTests / totalTests) * 100);
        
        let html = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Category Navigation Validation Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
                .header { background: #8b5cf6; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
                .summary { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .test-result { background: white; margin: 10px 0; padding: 15px; border-radius: 8px; border-left: 4px solid #ddd; }
                .passed { border-left-color: #10b981; }
                .failed { border-left-color: #ef4444; }
                .requirement { font-weight: bold; color: #333; }
                .description { color: #666; margin: 5px 0; }
                .details { background: #f9f9f9; padding: 10px; border-radius: 4px; margin-top: 10px; font-size: 0.9em; }
                .success-rate { font-size: 2em; color: ${successRate >= 80 ? '#10b981' : successRate >= 60 ? '#f59e0b' : '#ef4444'}; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>🧪 Category Navigation Validation Report</h1>
                <p>Generated: ${new Date().toLocaleString()}</p>
            </div>
            
            <div class="summary">
                <h2>📊 Test Summary</h2>
                <div class="success-rate">${successRate}% Success Rate</div>
                <p><strong>Total Tests:</strong> ${totalTests}</p>
                <p><strong>Passed:</strong> ${passedTests}</p>
                <p><strong>Failed:</strong> ${totalTests - passedTests}</p>
            </div>
            
            <h2>📋 Detailed Results</h2>
        `;
        
        this.testResults.forEach(test => {
            html += `
            <div class="test-result ${test.passed ? 'passed' : 'failed'}">
                <div class="requirement">Requirement ${test.requirement}: ${test.passed ? '✅ PASSED' : '❌ FAILED'}</div>
                <div class="description">${test.description}</div>
                <div class="details">
                    <strong>Details:</strong><br>
                    <pre>${JSON.stringify(test.details, null, 2)}</pre>
                </div>
            </div>
            `;
        });
        
        html += `
        </body>
        </html>
        `;
        
        const blob = new Blob([html], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        
        const a = document.createElement('a');
        a.href = url;
        a.download = `category-navigation-validation-report-${Date.now()}.html`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        
        URL.revokeObjectURL(url);
        
        console.log('📄 HTML validation report generated');
    }
}

// Initialize and expose globally
window.CategoryNavigationValidator = CategoryNavigationValidator;

// Auto-initialize when script loads
document.addEventListener('DOMContentLoaded', async () => {
    try {
        window.categoryValidator = await new CategoryNavigationValidator().init();
        console.log('🔍 Category Navigation Validator ready. Use categoryValidator.runAllTests() to start validation.');
    } catch (error) {
        console.error('❌ Failed to initialize Category Navigation Validator:', error);
    }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CategoryNavigationValidator;
}