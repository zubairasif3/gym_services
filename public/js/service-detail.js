/**
 * Service Detail Page - Enhanced JavaScript
 * Handles image gallery, fullscreen, and interactive features
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        initImageGallery();
        initFullscreenSupport();
        initSmoothScrolling();
        initShareButtons();
        initTooltips();
        initLazyLoading();
    });

    /**
     * Initialize Owl Carousel for image gallery
     */
    function initImageGallery() {
        if ($('.service-single-slider').length) {
            $('.service-single-slider').owlCarousel({
                loop: true,
                margin: 10,
                nav: true,
                dots: true,
                items: 1,
                autoHeight: true,
                navText: [
                    "<i class='fas fa-chevron-left'></i>",
                    "<i class='fas fa-chevron-right'></i>"
                ],
                responsive: {
                    0: {
                        nav: false,
                        dots: true
                    },
                    768: {
                        nav: true,
                        dots: false
                    }
                }
            });

            // Sync with thumbnail navigation
            $('.thumbnail-item img').on('click', function() {
                var index = $(this).parent().index();
                $('.service-single-slider').trigger('to.owl.carousel', [index, 300]);
                
                // Update active thumbnail
                $('.thumbnail-item').removeClass('active');
                $(this).parent().addClass('active');
            });
        }
    }

    /**
     * Initialize fullscreen support for images
     */
    function initFullscreenSupport() {
        window.openFullscreen = function(button) {
            const galleryItem = $(button).closest('.gallery-item');
            const img = galleryItem.find('img')[0];
            
            if (!img) return;
            
            // Try different fullscreen APIs
            if (img.requestFullscreen) {
                img.requestFullscreen();
            } else if (img.webkitRequestFullscreen) {
                img.webkitRequestFullscreen();
            } else if (img.msRequestFullscreen) {
                img.msRequestFullscreen();
            } else if (img.mozRequestFullScreen) {
                img.mozRequestFullScreen();
            }
        };

        // Handle fullscreen change events
        $(document).on('fullscreenchange webkitfullscreenchange mozfullscreenchange msfullscreenchange', function() {
            if (!document.fullscreenElement && 
                !document.webkitFullscreenElement && 
                !document.mozFullScreenElement && 
                !document.msFullscreenElement) {
                console.log('Exited fullscreen');
            }
        });
    }

    /**
     * Navigate to specific slide in carousel
     */
    window.goToSlide = function(index) {
        if ($('.service-single-slider').length) {
            $('.service-single-slider').trigger('to.owl.carousel', [index, 300]);
            
            // Update active thumbnail
            $('.thumbnail-item').removeClass('active');
            $('.thumbnail-item').eq(index).addClass('active');
        }
    };

    /**
     * Initialize smooth scrolling for anchor links
     */
    function initSmoothScrolling() {
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.hash);
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 800, 'swing');
            }
        });
    }

    /**
     * Enhanced share button functionality
     */
    function initShareButtons() {
        // Copy link functionality
        window.copyToClipboard = function(text) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    showNotification('Link copied to clipboard!', 'success');
                }).catch(function(err) {
                    console.error('Failed to copy: ', err);
                    fallbackCopyToClipboard(text);
                });
            } else {
                fallbackCopyToClipboard(text);
            }
        };

        // Fallback copy method for older browsers
        function fallbackCopyToClipboard(text) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-9999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                document.execCommand('copy');
                showNotification('Link copied to clipboard!', 'success');
            } catch (err) {
                console.error('Fallback: Failed to copy', err);
                showNotification('Failed to copy link', 'error');
            }

            document.body.removeChild(textArea);
        }
    }

    /**
     * Initialize Bootstrap tooltips
     */
    function initTooltips() {
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }

    /**
     * Initialize lazy loading for images
     */
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        const src = img.getAttribute('data-src');
                        if (src) {
                            img.src = src;
                            img.removeAttribute('data-src');
                            img.classList.add('fade-in');
                        }
                        observer.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Show notification toast
     */
    function showNotification(message, type = 'info') {
        const toast = $('<div class="toast-notification ' + type + '">' + message + '</div>');
        
        $('body').append(toast);
        
        setTimeout(function() {
            toast.addClass('show');
        }, 100);

        setTimeout(function() {
            toast.removeClass('show');
            setTimeout(function() {
                toast.remove();
            }, 300);
        }, 3000);
    }

    /**
     * Handle review form interactions
     */
    $(document).on('click', '.rating-star', function() {
        const rating = $(this).data('rating');
        $('.rating-star').each(function(index) {
            if (index < rating) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });
    });

    /**
     * Auto-hide alerts after delay
     */
    setTimeout(function() {
        $('.alert').not('.alert-permanent').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);

    /**
     * Image zoom on hover (optional enhancement)
     */
    $('.gallery-item img').on('mouseenter', function() {
        $(this).css('cursor', 'zoom-in');
    });

    /**
     * Handle window resize for responsive adjustments
     */
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Refresh carousel if needed
            if ($('.service-single-slider').length) {
                $('.service-single-slider').trigger('refresh.owl.carousel');
            }
        }, 250);
    });

    /**
     * Sticky sidebar scroll handling
     */
    function handleStickyScroll() {
        const sidebar = $('.sticky-sidebar');
        const footer = $('footer');
        
        if (sidebar.length && footer.length) {
            const sidebarHeight = sidebar.outerHeight();
            const footerTop = footer.offset().top;
            const scrollTop = $(window).scrollTop();
            const windowHeight = $(window).height();
            
            if (scrollTop + sidebarHeight + 100 > footerTop) {
                sidebar.css('position', 'absolute');
                sidebar.css('top', footerTop - sidebarHeight - 100);
            } else {
                sidebar.css('position', 'sticky');
                sidebar.css('top', '100px');
            }
        }
    }

    $(window).on('scroll', handleStickyScroll);

    /**
     * Enhanced loading state for AJAX requests
     */
    $(document).ajaxStart(function() {
        $('body').addClass('loading');
    }).ajaxStop(function() {
        $('body').removeClass('loading');
    });

    /**
     * Print functionality
     */
    window.printService = function() {
        window.print();
    };

    /**
     * Add to comparison functionality (future enhancement)
     */
    window.addToComparison = function(gigId) {
        let comparison = JSON.parse(localStorage.getItem('comparison') || '[]');
        if (!comparison.includes(gigId)) {
            comparison.push(gigId);
            localStorage.setItem('comparison', JSON.stringify(comparison));
            showNotification('Added to comparison', 'success');
        } else {
            showNotification('Already in comparison', 'info');
        }
    };

    /**
     * Report service functionality
     */
    window.reportService = function(gigId) {
        // Show report modal or form
        showNotification('Report functionality coming soon', 'info');
    };

})(jQuery);

/**
 * Livewire event listeners
 */
document.addEventListener('livewire:init', () => {
    // Listen for copy-to-clipboard event
    Livewire.on('copy-to-clipboard', (event) => {
        if (typeof copyToClipboard === 'function') {
            copyToClipboard(event.text);
        }
    });
    
    // Listen for open-share-url event
    Livewire.on('open-share-url', (event) => {
        window.open(event.url, '_blank', 'width=600,height=400,scrollbars=yes');
    });
    
    // Listen for review submitted
    Livewire.on('review-submitted', () => {
        // Close review form or show success message
        console.log('Review submitted successfully');
    });
    
    // Listen for gig saved/unsaved
    Livewire.on('gig-save-toggled', (event) => {
        console.log('Gig save status:', event.saved);
    });
    
    // Listen for gig shared
    Livewire.on('gig-shared', (event) => {
        console.log('Gig shared on:', event.platform);
    });
});

/**
 * Service Worker for offline capability (optional)
 */
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        // Uncomment to enable service worker
        // navigator.serviceWorker.register('/sw.js');
    });
}

