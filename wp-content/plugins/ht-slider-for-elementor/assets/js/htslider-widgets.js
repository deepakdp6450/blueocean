(function ($) {
    "use strict";

    // Carousel Handler
    var WidgetHtsliderCarouselHandler = function ($scope, $) {

        var carousel_elem = $scope.find('.htslider-carousel-activation').eq(0);
        if (carousel_elem.length > 0) {
            var settings = carousel_elem.data('settings');
            var arrows = settings['arrows'];
            var dots = settings['dots'];
            var autoplay = settings['autoplay'];
            var autoplay_speed = parseInt(settings['autoplay_speed']) || 3000;
            var animation_speed = parseInt(settings['animation_speed']) || 300;
            var pause_on_hover = settings['pause_on_hover'];
            var center_mode = settings['center_mode'];
            var center_padding = settings['center_padding'] ? settings['center_padding'] + 'px' : '50px';
            var display_columns = parseInt(settings['display_columns']) || 1;
            var scroll_columns = parseInt(settings['scroll_columns']) || 1;
            var tablet_width = parseInt(settings['tablet_width']) || 800;
            var tablet_display_columns = parseInt(settings['tablet_display_columns']) || 1;
            var tablet_scroll_columns = parseInt(settings['tablet_scroll_columns']) || 1;
            var mobile_width = parseInt(settings['mobile_width']) || 480;
            var mobile_display_columns = parseInt(settings['mobile_display_columns']) || 1;
            var mobile_scroll_columns = parseInt(settings['mobile_scroll_columns']) || 1;
            var carousel_style_ck = parseInt(settings['carousel_style_ck']) || 1;
            var carousel_rtl = $('html').attr('dir') === 'rtl' ? true : false;
            var progressBar = settings['progress_bar'] ? settings['progress_bar'] : false;

            if (carousel_style_ck == 4) {
                carousel_elem.slick({
                    arrows: arrows,
                    prevArrow: $('<div />').append($scope.find('.htslider-carosul-prev').clone().show()).html(),
                    nextArrow: $('<div />').append($scope.find('.htslider-carosul-next').clone().show()).html(),
                    dots: dots,
                    customPaging: function (slick, index) {
                        var data_title = slick.$slides.eq(index).find('.htslider-data-title').data('title');
                        return '<h6>' + data_title + '</h6>';
                    },
                    infinite: true,
                    autoplay: autoplay,
                    autoplaySpeed: autoplay_speed,
                    speed: animation_speed,
                    fade: false,
                    pauseOnHover: pause_on_hover,
                    slidesToShow: display_columns,
                    slidesToScroll: scroll_columns,
                    centerMode: center_mode,
                    centerPadding: center_padding,
                    rtl: carousel_rtl,
                    responsive: [
                        {
                            breakpoint: tablet_width,
                            settings: {
                                slidesToShow: tablet_display_columns,
                                slidesToScroll: tablet_scroll_columns
                            }
                        },
                        {
                            breakpoint: mobile_width,
                            settings: {
                                slidesToShow: mobile_display_columns,
                                slidesToScroll: mobile_scroll_columns
                            }
                        }
                    ]
                }).css({
                    visibility: 'visible',
                    'height': 'initial'
                });
            } else {
                carousel_elem.slick({
                    arrows: arrows,
                    prevArrow: $('<div />').append($scope.find('.htslider-carosul-prev').clone().show()).html(),
                    nextArrow: $('<div />').append($scope.find('.htslider-carosul-next').clone().show()).html(),
                    dots: dots,
                    infinite: true,
                    autoplay: autoplay,
                    autoplaySpeed: autoplay_speed,
                    speed: animation_speed,
                    fade: false,
                    pauseOnHover: pause_on_hover,
                    slidesToShow: display_columns,
                    slidesToScroll: scroll_columns,
                    centerMode: center_mode,
                    centerPadding: center_padding,
                    rtl: carousel_rtl,
                    responsive: [
                        {
                            breakpoint: tablet_width,
                            settings: {
                                slidesToShow: tablet_display_columns,
                                slidesToScroll: tablet_scroll_columns
                            }
                        },
                        {
                            breakpoint: mobile_width,
                            settings: {
                                slidesToShow: mobile_display_columns,
                                slidesToScroll: mobile_scroll_columns
                            }
                        }
                    ]

                }).css({
                    visibility: 'visible',
                    'height': 'initial'
                });

            }


            // Slider Area Element Animation
            //var $sliderArea = $('.htslider-carousel-activation');
            var $sliderArea = carousel_elem;
            if ($sliderArea.length) {
                $sliderArea.each(function () {
                    var $this = $(this),
                        $singleSlideElem = $this.find('.slick-slide .elementor-widget-wrap .elementor-element, .slick-slide .e-con-inner .elementor-element, .slick-slide .e-con .elementor-element');
                    function $slideElemAnimation() {
                        $singleSlideElem.each(function () {
                            var $this = $(this),
                                $thisSetting = $this.data('settings') ? $this.data('settings') : '',
                                $animationName = $thisSetting._animation,
                                $animationDelay = $thisSetting._animation_delay;
                            $this.removeClass('animated ' + $animationName).css('display', 'none').addClass('animated')
                            if ($this.closest('.slick-slide').hasClass('slick-current') || $this.closest('.slick-slide').hasClass('slick-active')) {
                                $this.removeClass('animated fadeOut').addClass('animated ' + $animationName).css({
                                    'animation-delay': $animationDelay + 's', 'display': ''
                                });
                            }
                        });
                    }
                    //$slideElemAnimation();
                    $this.on('afterChange', function (slick, currentSlide) {
                        $slideElemAnimation();
                    });
                    $this.on('beforeChange', function (slick, currentSlide) {
                        $slideElemAnimation();
                    });
                    $this.on('init', function (slick) {
                        $slideElemAnimation();
                    });
                });
            }

            if (progressBar && autoplay) {
                var $progressBar = carousel_elem.siblings('.htslider-progress').find('.htslider-progress-bar');
                var time = autoplay_speed;
                var isPaused = false;

                function startProgressBar() {
                    if (isPaused) return;
                    resetProgressBar();
                    $progressBar.css({
                        width: '100%',
                        transition: 'width ' + time + 'ms linear'
                    });
                }

                function resetProgressBar() {
                    $progressBar.css({
                        width: 0,
                        transition: 'width 0s'
                    });
                }

                // Initialize progress bar
                startProgressBar();

                // Attach event handlers to slick events
                carousel_elem.on('beforeChange', function () {
                    resetProgressBar();
                });

                carousel_elem.on('afterChange', function () {
                    startProgressBar();
                });

                if (pause_on_hover) {
                    carousel_elem.on('mouseenter', function () {
                        isPaused = true;
                        $progressBar.css('transition', 'none');
                    }).on('mouseleave', function () {
                        isPaused = false;
                        var remainingTime = time * (1 - $progressBar.width() / $progressBar.parent().width());
                        $progressBar.css({
                            transition: 'width ' + remainingTime + 'ms linear',
                            width: '100%'
                        });
                    });
                }
            }
        }
    }
    /*======= Scroll Navigation Activation ========*/
    var WidgetNavigationScrollHandler = function ($scope) {
        var $slider = $scope.find('.swiper-container');
        if (!$slider.length) { return; }

        var swiper_opt = $slider.data('settings');
        if (typeof swiper_opt === 'undefined') { return; }

        var sliderOptions = {
            direction: swiper_opt.direction,
            slidesPerView: swiper_opt.slideitem,
            initialSlide: swiper_opt.initialslide,
            spaceBetween: 0,
            mousewheelControl: true,
            mousewheel: swiper_opt.mousewheel ? {
                releaseOnEdges: true
            } : false,
            simulateTouch: swiper_opt.simulateTouch,
            speed: swiper_opt.speed,
            pagination: swiper_opt.pagination ? {
                el: '.htslider-swiper-pagination',
                clickable: true,
                renderBullet: function (index, className) {
                    var $slide = $(this.slides[index]);
                    var slideTitle = $slide.data('slide-title') || $slide.find('.htslider-scroll-navigation-content').text().trim();

                    // Escape HTML special characters to prevent XSS
                    var safeSlideTitle = String(slideTitle)
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");

                    if (swiper_opt.show_pagination_title) {
                        return '<span class="' + className + '" data-title="' + safeSlideTitle + '"><span class="htslider-pagination-title">' + safeSlideTitle + '</span></span>';
                    }
                    return '<span class="' + className + '" data-title="' + safeSlideTitle + '"></span>';
                }
            } : false,
            on: {
                init: function () {
                    var swiper = this;
                    var settings = swiper_opt;

                    if (settings.show_pagination_title && this.params.pagination && this.params.pagination.el) {
                        var $paginationEl = $(this.params.pagination.el);
                        $paginationEl.addClass('htslider-pagination-with-title');
                        $paginationEl.addClass('htslider-pagination-title-' + settings.pagination_title_position);
                        $paginationEl.addClass('htslider-pagination-trigger-' + settings.pagination_title_trigger);

                        // Add animation class if specified
                        if (settings.pagination_title_animation && settings.pagination_title_animation !== 'none') {
                            $paginationEl.addClass('htslider-pagination-animation-' + settings.pagination_title_animation);
                        }

                        // Set animation duration
                        var animationDuration = settings.pagination_title_animation_duration || 300;
                        $paginationEl.find('.htslider-pagination-title').css('transition-duration', animationDuration + 'ms');

                        // Setup hover functionality
                        setupPaginationTitleEvents(swiper, settings);

                        // Set initial active state
                        updateActiveTitle(swiper, settings);
                    }
                },
                slideChange: function () {
                    var swiper = this;
                    var settings = swiper_opt;

                    if (settings.show_pagination_title) {
                        updateActiveTitle(swiper, settings);
                    }
                }
            },
            navigation: swiper_opt.arrow ? {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            } : false,
            keyboard: swiper_opt.keyboardscroll ? {
                enabled: true,
                onlyInViewport: false,
            } : false
        };

        var swiper = new Swiper($slider[0], sliderOptions);

        if (!swiper_opt.mousewheel) {
            swiper.mousewheel.disable();
        }

        if (true == swiper_opt.slide_custom_menu) {
            $('a[href^="#htslider-scroll-slide"]').on('click', function (e) {
                e.preventDefault();
                var fullIndex = $(this).attr('href');
                var slideIndex = parseInt(fullIndex.replace('#htslider-scroll-slide-', ''), 0);
                if (fullIndex !== slideIndex && slideIndex > 0) {
                    swiper.slideTo(slideIndex - 1);
                }
            });
        }

        // Helper function to setup pagination title events
        function setupPaginationTitleEvents(swiper, settings) {
            var $bullets = $(swiper.pagination.el).find('.swiper-pagination-bullet');

            $bullets.each(function (index) {
                var $bullet = $(this);
                var $title = $bullet.find('.htslider-pagination-title');

                // Mouse enter event
                $bullet.on('mouseenter', function () {
                    if (settings.pagination_title_trigger === 'hover' ||
                        settings.pagination_title_trigger === 'both' ||
                        settings.pagination_title_trigger === 'always') {
                        showTitle($title, settings);
                    }
                });

                // Mouse leave event
                $bullet.on('mouseleave', function () {
                    if (settings.pagination_title_trigger === 'hover' && !$bullet.hasClass('swiper-pagination-bullet-active')) {
                        hideTitle($title, settings);
                    } else if (settings.pagination_title_trigger === 'both' && !$bullet.hasClass('swiper-pagination-bullet-active')) {
                        hideTitle($title, settings);
                    }
                });
            });
        }

        // Helper function to update active title
        function updateActiveTitle(swiper, settings) {
            var $bullets = $(swiper.pagination.el).find('.swiper-pagination-bullet');
            var $activeBullet = $bullets.eq(swiper.realIndex);
            var $activeTitle = $activeBullet.find('.htslider-pagination-title');

            // Hide all titles first
            $bullets.find('.htslider-pagination-title').each(function () {
                if (settings.pagination_title_trigger !== 'always') {
                    hideTitle($(this), settings);
                }
            });

            // Show active title if needed
            if (settings.pagination_title_trigger === 'active' ||
                settings.pagination_title_trigger === 'both' ||
                settings.pagination_title_trigger === 'always') {
                showTitle($activeTitle, settings);
            }
        }

        // Helper function to show title with animation
        function showTitle($title, settings) {
            if (!$title.length) return;

            $title.addClass('htslider-pagination-title-visible');

            // Apply animation class
            if (settings.pagination_title_animation && settings.pagination_title_animation !== 'none') {
                $title.removeClass('htslider-animation-out').addClass('htslider-animation-in htslider-animation-' + settings.pagination_title_animation);
            }
        }

        // Helper function to hide title with animation
        function hideTitle($title, settings) {
            if (!$title.length) return;

            if (settings.pagination_title_trigger === 'always') return;

            $title.removeClass('htslider-pagination-title-visible');

            // Apply animation class
            if (settings.pagination_title_animation && settings.pagination_title_animation !== 'none') {
                $title.removeClass('htslider-animation-in').addClass('htslider-animation-out');
            }
        }
    }

    // Run this code under Elementor.
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/htslider-postslider-addons.default', WidgetHtsliderCarouselHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/htsliderpro-addons.default', WidgetHtsliderCarouselHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/htslider-scrollnavigation-addons.default', WidgetNavigationScrollHandler);

    });

})(jQuery);