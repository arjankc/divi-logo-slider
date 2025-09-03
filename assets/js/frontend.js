jQuery(document).ready(function($) {
    'use strict';
    
    // Detect Divi Visual Builder (common indicators)
    var isVB = false;
    try {
        isVB = (typeof window.ET_Builder !== 'undefined' && window.ET_Builder.is_builder) ||
               (typeof window.et_fb !== 'undefined' && window.et_fb) ||
               (typeof window.ET_Builder !== 'undefined' && window.ET_Builder.API && window.ET_Builder.API.Version) ||
               (typeof et_core_page_resource_fallback !== 'undefined');
    } catch (e) {}

    // If in Visual Builder, do not initialize Swiper to avoid rendering artifacts in VB
    if (isVB) {
        return;
    }

    // Initialize all logo sliders on the page
    initLogoSliders();
    
    function initLogoSliders() {
        $('.lsfd-logo-slider').each(function() {
            const slider = this;
            const $slider = $(slider);
            
            // Get configuration from data attributes
            const config = getSliderConfig($slider);
            
            // Initialize Swiper
            const swiper = new Swiper(slider, config);
            
            // Store swiper instance for potential later use
            $slider.data('swiper', swiper);
            
            // Handle pause on hover if enabled
            if (config.autoplay && config.pauseOnHover) {
                setupHoverPause($slider, swiper);
            }
        });
    }
    
    function getSliderConfig($slider) {
        // Get data attributes with defaults
        const slidesPerView = parseInt($slider.data('slides-per-view')) || 5;
        const spaceBetween = parseInt($slider.data('space-between')) || 30;
        const sliderSpeed = parseInt($slider.data('slider-speed')) || 500;
        const autoplay = $slider.data('autoplay') === 'on';
        const pauseOnHover = $slider.data('pause-on-hover') === 'on';
        const navigation = $slider.data('navigation') === 'on';
        const pagination = $slider.data('pagination') === 'on';
        
        // Base configuration
        const config = {
            slidesPerView: slidesPerView,
            spaceBetween: spaceBetween,
            speed: sliderSpeed,
            loop: true,
            loopAdditionalSlides: 2,
            watchSlidesProgress: true,
            watchSlidesVisibility: true,
            preventInteractionOnTransition: true,
            
            // Responsive breakpoints
            breakpoints: {
                320: {
                    slidesPerView: Math.min(slidesPerView, 2),
                    spaceBetween: Math.max(spaceBetween * 0.5, 15)
                },
                768: {
                    slidesPerView: Math.min(slidesPerView, 3),
                    spaceBetween: Math.max(spaceBetween * 0.7, 20)
                },
                1024: {
                    slidesPerView: Math.min(slidesPerView, 4),
                    spaceBetween: Math.max(spaceBetween * 0.8, 25)
                },
                1200: {
                    slidesPerView: slidesPerView,
                    spaceBetween: spaceBetween
                }
            }
        };
        
        // Add autoplay if enabled
        if (autoplay) {
            config.autoplay = {
                delay: 3000,
                disableOnInteraction: false,
                pauseOnMouseEnter: pauseOnHover,
                reverseDirection: false
            };
        }
        
        // Add navigation if enabled
        if (navigation) {
            config.navigation = {
                nextEl: $slider.find('.swiper-button-next')[0],
                prevEl: $slider.find('.swiper-button-prev')[0],
                disabledClass: 'swiper-button-disabled'
            };
        }
        
        // Add pagination if enabled
        if (pagination) {
            config.pagination = {
                el: $slider.find('.swiper-pagination')[0],
                clickable: true,
                dynamicBullets: false,
                type: 'bullets'
            };
        }
        
        // Store config for hover handling
        config.pauseOnHover = pauseOnHover;
        
        return config;
    }
    
    function setupHoverPause($slider, swiper) {
        $slider.on('mouseenter', function() {
            if (swiper.autoplay && swiper.autoplay.running) {
                swiper.autoplay.stop();
            }
        });
        
        $slider.on('mouseleave', function() {
            if (swiper.autoplay) {
                swiper.autoplay.start();
            }
        });
    }
    
    // Handle window resize (optional enhancement)
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            $('.lsfd-logo-slider').each(function() {
                const swiper = $(this).data('swiper');
                if (swiper) {
                    swiper.update();
                }
            });
        }, 250);
    });
    
    // Handle visibility change (pause when tab is hidden)
    $(document).on('visibilitychange', function() {
        $('.lsfd-logo-slider').each(function() {
            const swiper = $(this).data('swiper');
            if (swiper && swiper.autoplay) {
                if (document.hidden) {
                    swiper.autoplay.stop();
                } else {
                    swiper.autoplay.start();
                }
            }
        });
    });
    
    // Re-initialize sliders when new content is loaded (e.g., via AJAX)
    $(document).on('et_pb_after_init_modules', function() {
        if (!isVB) {
            // Wait a bit for DOM to be ready
            setTimeout(initLogoSliders, 100);
        }
    });
    
    // Divi Builder compatibility
    if (window.et_pb_custom) {
        window.et_pb_custom.add_custom_init(function() {
            if (!isVB) initLogoSliders();
        });
    }
});

// Expose functions for potential external use
window.LSFDLogoSlider = {
    init: function() {
        jQuery(document).ready(function($) {
            initLogoSliders();
        });
    }
};
