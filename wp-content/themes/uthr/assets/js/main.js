(function ($) {
    "use strict";
    // Preloader 
      $(window).on('load', function() {
    stylePreloader();
  });

  // Preloader
  function stylePreloader() {
    $('body').addClass('preloader-deactive');
  }
    /*--
        Commons Variables
    -----------------------------------*/
    var $window = $(window),
        $body = $('body');

    /*--
        Custom script to call Background
        Image & Color from html data attribute
    -----------------------------------*/
    $('[data-bg-image]').each(function () {
        var $this = $(this),
            $image = $this.data('bg-image');
        $this.css('background-image', 'url(' + $image + ')');
    });
    $('[data-bg-color]').each(function () {
        var $this = $(this),
            $color = $this.data('bg-color');
        $this.css('background-color', $color);
    });
    $('[data-color]').each(function () {
        var $this = $(this),
            $color = $this.data('color');
        $this.css('color', $color);
    });

    /*--
        Header Sticky
    -----------------------------------*/
    $window.on('scroll', function () {
        if ($window.scrollTop() > 80) {
            $('.uthr-header').addClass('is-sticky');
        } else {
            $('.uthr-header').removeClass('is-sticky');
        }
    });


    /*--
        Off Canvas Function
    -----------------------------------*/
    $('.uthr-mobile-menu-toggle, .uthr-mobile-menu-close').on('click', '.toggle', function () {
        $body.toggleClass('mobile-menu-open');
    });
    $('.uthr-site-mobile-menu').on('click', '.menu-toggle', function (e) {
        e.preventDefault();
        var $this = $(this);
        if ($this.siblings('.uthr-sub-menu:visible').length) {
            $this.siblings('.uthr-sub-menu').slideUp().parent().removeClass('open').find('.uthr-sub-menu').slideUp().parent().removeClass('open');
        } else {
            $this.siblings('.uthr-sub-menu').slideDown().parent().addClass('open').siblings().find('.uthr-sub-menu').slideUp().parent().removeClass('open');
        }
    });
    $body.on('click', function (e) {
        if (!$(e.target).closest('.uthr-site-main-mobile-menu').length && !$(e.target).closest('.uthr-mobile-menu-toggle').length) {
            $body.removeClass('mobile-menu-open');
        }
    });



    //Header Search
    if( $('.search-box-outer').length ) {
        $('.search-box-outer').on('click', function() {
            $('body').addClass('search-active');
        });
        $('.close-search').on('click', function() {
            $('body').removeClass('search-active');
        });
    }


    
    /*----------------------------
        Cart Plus Minus Button
    ------------------------------ */
    // for quantity increase / decrease
    $( 'body' ).on( 'click', '.quantity .inc', function( e ) {
      var $input = $( this ).parent().parent().find( 'input.qty' );
      $input.val( parseInt( $input.val() ) + 1 );
      $input.trigger( 'change' );
    });

    
    $( 'body' ).on( 'click', '.quantity .dec', function( e ) {
      var $input = $( this ).parent().parent().find( 'input.qty' );
      var value = parseInt( $input.val() ) - 1;
      if ( value < 0 ) value = 0;
      $input.val( value );
      $input.trigger( 'change' );
    });


$('.woocommerce-product-gallery__image a').on('click', function(event) {
    event.preventDefault();

});



   if ($(".product-gallery-list").length == 1) {
      $(".woocommerce #content div.product div.images, .woocommerce div.product div.images, .woocommerce-page #content div.product div.images, .woocommerce-page div.product div.images").css("width", "50%");
    } 


     /*---shop grid activation---*/
    $('.shop_toolbar_btn ul li a').on('click', function (e) {
        
        e.preventDefault();
        
        $('.shop_toolbar_btn ul li a').removeClass('active');
        $(this).addClass('active');
        
        var parentsDiv = $('.shop_wrapper');
        var viewMode = $(this).data('role');
        
        
        parentsDiv.removeClass('grid_3 grid_4 grid_5 grid_list').addClass(viewMode);

        if(viewMode == 'grid_3'){
            parentsDiv.children().addClass('col-lg-4 col-md-4 col-sm-6').removeClass('col-lg-3 col-cust-5 col-12');
            
        }

        if(viewMode == 'grid_4'){
            parentsDiv.children().addClass('col-lg-3 col-md-4 col-sm-6').removeClass('col-lg-4 col-cust-5 col-12');
        }
        
        if(viewMode == 'grid_list'){
            parentsDiv.children().addClass('col-12').removeClass('col-lg-3 col-lg-4 col-md-4 col-sm-6 col-cust-5');
        }
            
    });
$(".newsletter_subscribe form .mc4wp-form-fields").append('<i class="icon-envelope-open"></i>');

$('.masonry-blog').imagesLoaded( function() {
    $('.masonry-blog').masonry({
      itemSelector: 'div[class*="col-"]',
    });
});


})(jQuery);