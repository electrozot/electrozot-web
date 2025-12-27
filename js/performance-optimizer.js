/**
 * Performance Optimizer for ElectroZot Website
 * Implements lazy loading and performance improvements
 */

(function() {
    'use strict';
    
    // Performance optimization utilities
    const PerformanceOptimizer = {
        
        // Lazy load images
        initLazyLoading: function() {
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.classList.remove('lazy');
                                img.classList.add('loaded');
                                observer.unobserve(img);
                            }
                        }
                    });
                });
                
                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            } else {
                // Fallback for older browsers
                document.querySelectorAll('img[data-src]').forEach(img => {
                    img.src = img.dataset.src;
                });
            }
        },
        
        // Lazy load sections
        initSectionLazyLoading: function() {
            if ('IntersectionObserver' in window) {
                const sectionObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const section = entry.target;
                            section.classList.add('section-loaded');
                            
                            // Trigger any animations
                            const animatedElements = section.querySelectorAll('.animate-on-scroll');
                            animatedElements.forEach(el => {
                                el.classList.add('animated');
                            });
                        }
                    });
                }, {
                    rootMargin: '50px'
                });
                
                document.querySelectorAll('.lazy-section').forEach(section => {
                    sectionObserver.observe(section);
                });
            }
        },
        
        // Optimize form loading
        optimizeFormLoading: function() {
            const bookingForm = document.getElementById('booking-form');
            if (bookingForm) {
                // Defer non-critical form enhancements
                setTimeout(() => {
                    this.enhanceFormUX();
                }, 1000);
            }
        },
        
        // Enhance form UX after initial load
        enhanceFormUX: function() {
            // Add smooth focus transitions
            const formInputs = document.querySelectorAll('.form-control');
            formInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });
        },
        
        // Optimize animations
        optimizeAnimations: function() {
            // Reduce animations on slower devices
            if (navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4) {
                document.body.classList.add('reduced-animations');
            }
            
            // Pause animations when tab is not visible
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    document.body.classList.add('paused-animations');
                } else {
                    document.body.classList.remove('paused-animations');
                }
            });
        },
        
        // Preload critical resources
        preloadCriticalResources: function() {
            const criticalImages = [
                'vendor/EZlogonew.png',
                'assets/favicon.ico'
            ];
            
            criticalImages.forEach(src => {
                const link = document.createElement('link');
                link.rel = 'preload';
                link.as = 'image';
                link.href = src;
                document.head.appendChild(link);
            });
        },
        
        // Optimize third-party scripts
        optimizeThirdPartyScripts: function() {
            // Defer non-critical third-party scripts
            const deferredScripts = document.querySelectorAll('script[data-defer]');
            deferredScripts.forEach(script => {
                setTimeout(() => {
                    const newScript = document.createElement('script');
                    newScript.src = script.dataset.src;
                    newScript.async = true;
                    document.head.appendChild(newScript);
                }, 2000);
            });
        },
        
        // Initialize all optimizations
        init: function() {
            // Run immediately
            this.preloadCriticalResources();
            this.optimizeAnimations();
            
            // Run after DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    this.initLazyLoading();
                    this.initSectionLazyLoading();
                    this.optimizeFormLoading();
                });
            } else {
                this.initLazyLoading();
                this.initSectionLazyLoading();
                this.optimizeFormLoading();
            }
            
            // Run after page load
            window.addEventListener('load', () => {
                this.optimizeThirdPartyScripts();
            });
        }
    };
    
    // Initialize performance optimizations
    PerformanceOptimizer.init();
    
    // Add CSS for performance optimizations
    const style = document.createElement('style');
    style.textContent = `
        /* Lazy loading styles */
        img.lazy {
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        img.loaded {
            opacity: 1;
        }
        
        /* Section lazy loading */
        .lazy-section {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .lazy-section.section-loaded {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Reduced animations for slower devices */
        .reduced-animations * {
            animation-duration: 0.1s !important;
            transition-duration: 0.1s !important;
        }
        
        /* Paused animations when tab not visible */
        .paused-animations * {
            animation-play-state: paused !important;
        }
        
        /* Form focus optimization */
        .form-group.focused .form-control {
            border-color: #EC4899;
            box-shadow: 0 0 0 0.2rem rgba(236, 72, 153, 0.25);
        }
        
        /* Optimize hover effects on touch devices */
        @media (hover: none) {
            .booking-step-card:hover,
            .contact-btn:hover,
            .btn:hover {
                transform: none !important;
            }
        }
    `;
    document.head.appendChild(style);
    
})();