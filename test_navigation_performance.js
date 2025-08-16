/**
 * Category Navigation Performance Test Suite
 * Tests smooth scrolling performance, animation frame rates, and memory usage
 */

class NavigationPerformanceTester {
    constructor() {
        this.testResults = {};
        this.performanceMetrics = {};
        this.isRunning = false;
    }

    // Initialize performance testing
    init() {
        console.log('🚀 Initializing Navigation Performance Tests...');
        this.setupPerformanceObserver();
        this.setupMemoryMonitoring();
        return this;
    }

    // Setup Performance Observer for navigation timing
    setupPerformanceObserver() {
        if ('PerformanceObserver' in window) {
            const observer = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach(entry => {
                    if (entry.entryType === 'navigation') {
                        this.performanceMetrics.navigationTiming = {
                            domContentLoaded: entry.domContentLoadedEventEnd - entry.domContentLoadedEventStart,
                            loadComplete: entry.loadEventEnd - entry.loadEventStart,
                            totalTime: entry.loadEventEnd - entry.fetchStart
                        };
                    }
                });
            });

            try {
                observer.observe({ entryTypes: ['navigation', 'measure'] });
                console.log('✅ Performance Observer initialized');
            } catch (error) {
                console.warn('⚠️ Performance Observer not fully supported:', error.message);
            }
        }
    }

    // Setup memory monitoring
    setupMemoryMonitoring() {
        if ('memory' in performance) {
            this.performanceMetrics.initialMemory = {
                used: performance.memory.usedJSHeapSize,
                total: performance.memory.totalJSHeapSize,
                limit: performance.memory.jsHeapSizeLimit
            };
            console.log('✅ Memory monitoring initialized');
        } else {
            console.warn('⚠️ Memory monitoring not supported in this browser');
        }
    }

    // Test smooth scrolling performance
    async testSmoothScrolling() {
        console.log('🎯 Testing smooth scrolling performance...');
        
        const testResults = {
            testName: 'Smooth Scrolling Performance',
            startTime: performance.now(),
            frameRates: [],
            scrollDurations: [],
            errors: []
        };

        try {
            // Test different scroll distances
            const scrollTests = [
                { distance: 500, description: 'Short scroll' },
                { distance: 1500, description: 'Medium scroll' },
                { distance: 3000, description: 'Long scroll' }
            ];

            for (const test of scrollTests) {
                const scrollResult = await this.performScrollTest(test.distance, test.description);
                testResults.scrollDurations.push(scrollResult);
            }

            // Test frame rate during scrolling
            const frameRateResult = await this.testScrollFrameRate();
            testResults.frameRates = frameRateResult;

        } catch (error) {
            testResults.errors.push(error.message);
            console.error('❌ Smooth scrolling test error:', error);
        }

        testResults.endTime = performance.now();
        testResults.totalDuration = testResults.endTime - testResults.startTime;
        
        this.testResults.smoothScrolling = testResults;
        return testResults;
    }

    // Perform individual scroll test
    performScrollTest(distance, description) {
        return new Promise((resolve) => {
            const startTime = performance.now();
            const startPosition = window.scrollY;
            
            // Perform smooth scroll
            window.scrollTo({
                top: distance,
                behavior: 'smooth'
            });

            // Monitor scroll completion
            const checkScrollComplete = () => {
                const currentPosition = window.scrollY;
                const targetReached = Math.abs(currentPosition - distance) < 10;
                
                if (targetReached) {
                    const endTime = performance.now();
                    const duration = endTime - startTime;
                    
                    resolve({
                        description,
                        distance,
                        duration,
                        startPosition,
                        endPosition: currentPosition,
                        success: true
                    });
                } else {
                    // Continue checking
                    requestAnimationFrame(checkScrollComplete);
                }
            };

            // Start monitoring
            requestAnimationFrame(checkScrollComplete);
            
            // Timeout fallback
            setTimeout(() => {
                resolve({
                    description,
                    distance,
                    duration: 5000,
                    startPosition,
                    endPosition: window.scrollY,
                    success: false,
                    error: 'Scroll timeout'
                });
            }, 5000);
        });
    }

    // Test frame rate during scrolling
    testScrollFrameRate() {
        return new Promise((resolve) => {
            const frameRates = [];
            let frameCount = 0;
            let lastTime = performance.now();
            
            const measureFrameRate = () => {
                frameCount++;
                const currentTime = performance.now();
                const deltaTime = currentTime - lastTime;
                
                if (deltaTime >= 1000) { // Measure every second
                    const fps = Math.round((frameCount * 1000) / deltaTime);
                    frameRates.push(fps);
                    frameCount = 0;
                    lastTime = currentTime;
                }
                
                if (frameRates.length < 5) { // Collect 5 seconds of data
                    requestAnimationFrame(measureFrameRate);
                } else {
                    resolve(frameRates);
                }
            };

            // Start scrolling animation to generate frames
            let scrollPosition = 0;
            const animateScroll = () => {
                scrollPosition += 10;
                window.scrollTo(0, scrollPosition);
                
                if (scrollPosition < 2000) {
                    requestAnimationFrame(animateScroll);
                }
            };

            requestAnimationFrame(measureFrameRate);
            requestAnimationFrame(animateScroll);
        });
    }

    // Test category link navigation performance
    async testCategoryNavigation() {
        console.log('📱 Testing category navigation performance...');
        
        const testResults = {
            testName: 'Category Navigation Performance',
            startTime: performance.now(),
            navigationTests: [],
            errors: []
        };

        const categories = [
            'mobile-section',
            'laptop-section', 
            'keyboard-mouse-section',
            'headphones-section',
            'accessories-section'
        ];

        try {
            for (const category of categories) {
                const navResult = await this.testCategoryLinkPerformance(category);
                testResults.navigationTests.push(navResult);
                
                // Add delay between tests
                await this.delay(500);
            }
        } catch (error) {
            testResults.errors.push(error.message);
            console.error('❌ Category navigation test error:', error);
        }

        testResults.endTime = performance.now();
        testResults.totalDuration = testResults.endTime - testResults.startTime;
        
        this.testResults.categoryNavigation = testResults;
        return testResults;
    }

    // Test individual category link performance
    testCategoryLinkPerformance(categoryId) {
        return new Promise((resolve) => {
            const startTime = performance.now();
            
            // Find the target element
            const targetElement = document.getElementById(categoryId);
            if (!targetElement) {
                resolve({
                    categoryId,
                    success: false,
                    error: 'Target element not found',
                    duration: 0
                });
                return;
            }

            // Simulate category link click
            const targetPosition = targetElement.offsetTop - 110; // Account for navbar
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });

            // Monitor navigation completion
            const checkNavComplete = () => {
                const currentPosition = window.scrollY;
                const targetReached = Math.abs(currentPosition - targetPosition) < 20;
                
                if (targetReached) {
                    const endTime = performance.now();
                    const duration = endTime - startTime;
                    
                    resolve({
                        categoryId,
                        success: true,
                        duration,
                        targetPosition,
                        actualPosition: currentPosition,
                        accuracy: Math.abs(currentPosition - targetPosition)
                    });
                } else {
                    requestAnimationFrame(checkNavComplete);
                }
            };

            requestAnimationFrame(checkNavComplete);
            
            // Timeout fallback
            setTimeout(() => {
                resolve({
                    categoryId,
                    success: false,
                    error: 'Navigation timeout',
                    duration: 3000,
                    targetPosition,
                    actualPosition: window.scrollY
                });
            }, 3000);
        });
    }

    // Test cross-page navigation performance
    async testCrossPageNavigation() {
        console.log('🔄 Testing cross-page navigation performance...');
        
        const testResults = {
            testName: 'Cross-Page Navigation Performance',
            startTime: performance.now(),
            sessionStorageTests: [],
            urlHashTests: [],
            errors: []
        };

        try {
            // Test sessionStorage performance
            const sessionStorageResult = await this.testSessionStoragePerformance();
            testResults.sessionStorageTests = sessionStorageResult;

            // Test URL hash handling performance
            const urlHashResult = await this.testUrlHashPerformance();
            testResults.urlHashTests = urlHashResult;

        } catch (error) {
            testResults.errors.push(error.message);
            console.error('❌ Cross-page navigation test error:', error);
        }

        testResults.endTime = performance.now();
        testResults.totalDuration = testResults.endTime - testResults.startTime;
        
        this.testResults.crossPageNavigation = testResults;
        return testResults;
    }

    // Test sessionStorage performance
    testSessionStoragePerformance() {
        const startTime = performance.now();
        const testData = 'mobile-section';
        const iterations = 1000;
        
        try {
            // Test write performance
            for (let i = 0; i < iterations; i++) {
                sessionStorage.setItem('scrollToSection', testData);
            }
            
            const writeTime = performance.now() - startTime;
            
            // Test read performance
            const readStartTime = performance.now();
            for (let i = 0; i < iterations; i++) {
                sessionStorage.getItem('scrollToSection');
            }
            
            const readTime = performance.now() - readStartTime;
            
            // Cleanup
            sessionStorage.removeItem('scrollToSection');
            
            return {
                writeTime,
                readTime,
                iterations,
                avgWriteTime: writeTime / iterations,
                avgReadTime: readTime / iterations,
                success: true
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Test URL hash handling performance
    testUrlHashPerformance() {
        const testHashes = [
            '#mobile-section',
            '#laptop-section',
            '#keyboard-mouse-section',
            '#headphones-section',
            '#accessories-section'
        ];
        
        const results = [];
        
        testHashes.forEach(hash => {
            const startTime = performance.now();
            
            // Simulate hash change
            window.location.hash = hash;
            
            // Test hash parsing
            const parsedHash = window.location.hash.substring(1);
            const targetElement = document.getElementById(parsedHash);
            
            const endTime = performance.now();
            
            results.push({
                hash,
                duration: endTime - startTime,
                elementFound: !!targetElement,
                success: true
            });
        });
        
        // Reset hash
        window.location.hash = '';
        
        return results;
    }

    // Test memory usage during navigation
    async testMemoryUsage() {
        console.log('💾 Testing memory usage during navigation...');
        
        if (!('memory' in performance)) {
            console.warn('⚠️ Memory testing not supported in this browser');
            return { supported: false };
        }

        const testResults = {
            testName: 'Memory Usage Test',
            startTime: performance.now(),
            memorySnapshots: [],
            errors: []
        };

        try {
            // Take initial memory snapshot
            testResults.memorySnapshots.push({
                phase: 'initial',
                memory: this.getMemorySnapshot()
            });

            // Perform intensive navigation operations
            for (let i = 0; i < 50; i++) {
                // Simulate rapid navigation
                const categories = ['mobile-section', 'laptop-section', 'headphones-section'];
                const randomCategory = categories[Math.floor(Math.random() * categories.length)];
                
                const targetElement = document.getElementById(randomCategory);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 110,
                        behavior: 'smooth'
                    });
                }
                
                // Take memory snapshot every 10 iterations
                if (i % 10 === 0) {
                    testResults.memorySnapshots.push({
                        phase: `iteration-${i}`,
                        memory: this.getMemorySnapshot()
                    });
                }
                
                await this.delay(50);
            }

            // Take final memory snapshot
            testResults.memorySnapshots.push({
                phase: 'final',
                memory: this.getMemorySnapshot()
            });

        } catch (error) {
            testResults.errors.push(error.message);
            console.error('❌ Memory usage test error:', error);
        }

        testResults.endTime = performance.now();
        testResults.totalDuration = testResults.endTime - testResults.startTime;
        
        this.testResults.memoryUsage = testResults;
        return testResults;
    }

    // Get current memory snapshot
    getMemorySnapshot() {
        if ('memory' in performance) {
            return {
                used: performance.memory.usedJSHeapSize,
                total: performance.memory.totalJSHeapSize,
                limit: performance.memory.jsHeapSizeLimit,
                timestamp: performance.now()
            };
        }
        return null;
    }

    // Run all performance tests
    async runAllTests() {
        console.log('🚀 Starting comprehensive performance test suite...');
        
        this.isRunning = true;
        const overallStartTime = performance.now();
        
        try {
            // Run all test categories
            await this.testSmoothScrolling();
            await this.testCategoryNavigation();
            await this.testCrossPageNavigation();
            await this.testMemoryUsage();
            
            const overallEndTime = performance.now();
            const totalDuration = overallEndTime - overallStartTime;
            
            // Generate comprehensive report
            const report = this.generatePerformanceReport(totalDuration);
            console.log('📊 Performance test suite completed:', report);
            
            return report;
            
        } catch (error) {
            console.error('❌ Performance test suite failed:', error);
            return { error: error.message };
        } finally {
            this.isRunning = false;
        }
    }

    // Generate performance report
    generatePerformanceReport(totalDuration) {
        const report = {
            summary: {
                totalDuration,
                testsRun: Object.keys(this.testResults).length,
                timestamp: new Date().toISOString()
            },
            results: this.testResults,
            recommendations: []
        };

        // Analyze results and generate recommendations
        if (this.testResults.smoothScrolling) {
            const avgScrollDuration = this.testResults.smoothScrolling.scrollDurations
                .reduce((sum, test) => sum + test.duration, 0) / this.testResults.smoothScrolling.scrollDurations.length;
            
            if (avgScrollDuration > 1000) {
                report.recommendations.push('Consider optimizing smooth scrolling - average duration is high');
            }
            
            const avgFrameRate = this.testResults.smoothScrolling.frameRates
                .reduce((sum, fps) => sum + fps, 0) / this.testResults.smoothScrolling.frameRates.length;
            
            if (avgFrameRate < 30) {
                report.recommendations.push('Frame rate during scrolling is below optimal (30fps)');
            }
        }

        if (this.testResults.categoryNavigation) {
            const failedNavigations = this.testResults.categoryNavigation.navigationTests
                .filter(test => !test.success);
            
            if (failedNavigations.length > 0) {
                report.recommendations.push(`${failedNavigations.length} category navigation tests failed`);
            }
        }

        if (this.testResults.memoryUsage && this.testResults.memoryUsage.memorySnapshots.length > 1) {
            const initialMemory = this.testResults.memoryUsage.memorySnapshots[0].memory.used;
            const finalMemory = this.testResults.memoryUsage.memorySnapshots[this.testResults.memoryUsage.memorySnapshots.length - 1].memory.used;
            const memoryIncrease = finalMemory - initialMemory;
            
            if (memoryIncrease > 5000000) { // 5MB
                report.recommendations.push('Significant memory increase detected during navigation tests');
            }
        }

        return report;
    }

    // Utility function for delays
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // Export results to JSON
    exportResults() {
        const exportData = {
            timestamp: new Date().toISOString(),
            userAgent: navigator.userAgent,
            viewport: {
                width: window.innerWidth,
                height: window.innerHeight
            },
            performanceMetrics: this.performanceMetrics,
            testResults: this.testResults
        };

        const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        
        const a = document.createElement('a');
        a.href = url;
        a.download = `navigation-performance-test-${Date.now()}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        
        URL.revokeObjectURL(url);
        
        console.log('📁 Performance test results exported');
    }
}

// Initialize and expose globally
window.NavigationPerformanceTester = NavigationPerformanceTester;

// Auto-initialize when script loads
document.addEventListener('DOMContentLoaded', () => {
    window.navPerfTester = new NavigationPerformanceTester().init();
    console.log('🧪 Navigation Performance Tester ready. Use navPerfTester.runAllTests() to start testing.');
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NavigationPerformanceTester;
}