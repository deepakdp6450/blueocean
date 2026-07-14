;(function($){
"use strict";

    var QuickSwish = {

        body: $('body'),
        modal: $('#quickswish-modal'),
        modalbody: $('.quickswish-modal-body'),

        /**
         * [init]
         * @return {[void]} Initial Function
         */
        init: function(){
            this.wrapperHeight();
            $( document )
                .on( 'click.QuickSwish', 'a.quickswish-btn', this.openQuickView )
                .on( 'click.QuickSwish', '.quickswish-modal-close', this.closeQuickView )
                .on( 'click.QuickSwish', '.quickswish-overlay', this.closeQuickView );

            $( document ).keyup( this.closeKeyUp );

            if( QSVIEW.option_data['enable_ajax_cart'] === 'on' ){
                $( document ).on( 'click.QuickSwish', '.quickswish-modal-content .single_add_to_cart_button:not(.disabled)', this.addToCart );
            }

        },

        /**
         * [openQuickView] Open quickview
         * @param  event
         * @return {[void]}
         */
        openQuickView: function( event ){
            event.preventDefault();

            var $this = $(this),
                id = $this.data('product_id');

            QuickSwish.modalbody.html(''); /*clear content*/

            $this.addClass('loading');
            QuickSwish.modal.addClass('loading');

            $.ajax({
                url: QSVIEW.ajaxurl,
                data: {
                    action: 'quickswish_product',
                    id: id,
                    nonce: QSVIEW.nonce,
                },
                method: 'POST',
                success: function (response) {
                    if ( response ) {
                        QuickSwish.modalbody.html( response );
                        QuickSwish.variation( QuickSwish.modalbody );
                        if( QSVIEW.option_data['thumbnail_layout'] === 'slider' ){
                            QuickSwish.imageSlider();
                        }
                    } else {
                        console.log( 'Something wrong loading compare data' );
                    }
                },
                error: function (response) {
                    console.log('Something wrong with AJAX response.');
                },
                complete: function () {
                    QuickSwish.modal.removeClass('loading').addClass('quickswish-open');
                    $this.removeClass('loading');
                },
            });

        },

        /**
         * [variation] Product variation data manager
         * @param  {[String]} $container
         * @return {[void]} 
         */
        variation: function( $container ){

            var $formvariation = $container.find('.variations_form');
            $formvariation.each( function() {
                $( this ).wc_variation_form();
            });

            $formvariation.trigger( 'check_variations' );
            $formvariation.trigger( 'reset_image' );

            if( typeof $.fn.wc_product_gallery !== 'undefined' ) {
                $container.find('.woocommerce-product-gallery').each( function () {
                    $(this).wc_product_gallery();
                } );
            }

            if( QSVIEW.option_data['thumbnail_layout'] === 'slider' ){
                QuickSwish.variationData( $container );
            }

        },

        /**
         * [variationData] Manage varition data
         * @param  {[String]} $product
         * @return {[void]}
         */
        variationData: function( $product ){

            $( '.single_variation_wrap' ).on( 'show_variation', function ( event, variation ) {
                $product.find('.quickswish-main-image-slider').slick('slickGoTo', 0);
            });

        },

        /**
         * [closeQuickView] Close quickview
         * @param  event
         * @return {[void]}
         */
        closeQuickView: function( event ) {
            event.preventDefault();
            QuickSwish.modal.removeClass('quickswish-open');
        },

        /**
         * [closeKeyUp] Close quickview after press ESC Button
         * @param  event
         * @return {[void]}
         */
        closeKeyUp: function(event){
            if( event.keyCode === 27 ){
                QuickSwish.modal.removeClass('quickswish-open');
            }
        },

        /**
         * [wrapperHeight] Manage Modal wrapper height
         * @return {[void]}
         */
        wrapperHeight: function(){
            var window_width = $(window).width(),
                window_height = $(window).height();

            $('.quickswish-modal-wrapper').css({"max-height": ( window_height-150 )+"px"});
        },

        /*
        * Cuctom image slider
        */
        MainImageSlider: function(){
            $('.quickswish-main-image-slider').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: true,
                fade: true,
                asNavFor: '.quickswish-thumbnail-slider',
                prevArrow: '<span class="quickswish-slick-prev">&#8592;</span>',
                nextArrow: '<span class="quickswish-slick-next">&#8594;</span>',
            });
        },
        ThumbnailSlider: function(){
            $('.quickswish-thumbnail-slider').slick({
                slidesToShow: 4,
                slidesToScroll: 1,
                asNavFor: '.quickswish-main-image-slider',
                dots: false,
                arrows: true,
                focusOnSelect: true,
                prevArrow: '<span class="quickswish-slick-prev">&#8592;</span>',
                nextArrow: '<span class="quickswish-slick-next">&#8594;</span>',
            });
        },
        imageSlider: function(){
            this.MainImageSlider();
            this.ThumbnailSlider();
        },

        /**
         * [addToCart]
         * @param event
         */
        addToCart: function( event ){
            event.preventDefault();

            var $this = $(this),
                $form           = $this.closest('form.cart'),
                all_data        = $form.serialize(),
                product_qty     = $form.find('input[name=quantity]').val() || 1,
                product_id      = $form.find('input[name=product_id]').val() || $this.val(),
                variation_id    = $form.find('input[name=variation_id]').val() || 0;


            /* For Variation product */    
            var item = {},
                variations = $form.find( 'select[name^=attribute]' );
                if ( !variations.length) {
                    variations = $form.find( '[name^=attribute]:checked' );
                }
                if ( !variations.length) {
                    variations = $form.find( 'input[name^=attribute]' );
                }

                variations.each( function() {
                    var $thisitem = $( this ),
                        attributeName = $thisitem.attr( 'name' ),
                        attributevalue = $thisitem.val(),
                        index,
                        attributeTaxName;
                        $thisitem.removeClass( 'error' );
                    if ( attributevalue.length === 0 ) {
                        index = attributeName.lastIndexOf( '_' );
                        attributeTaxName = attributeName.substring( index + 1 );
                        $thisitem.addClass( 'required error' );
                    } else {
                        item[attributeName] = attributevalue;
                    }
                });

            var data = {
                // action: 'quickswish_insert_to_cart',
                product_id: product_id,
                product_sku: '',
                quantity: product_qty,
                variation_id: variation_id,
                variations: item,
                all_data: all_data,
            };

            var alldata = data.all_data + '&product_id='+ data.product_id + '&product_sku='+ data.product_sku + '&quantity='+ data.quantity + '&variation_id='+ data.variation_id + '&variations='+ JSON.stringify( data.variations ) +'&action=quickswish_insert_to_cart' +'&nonce='+QSVIEW.nonce;

            $( document.body ).trigger('adding_to_cart', [$this, data]);

            $.ajax({
                type: 'post',
                url: QSVIEW.ajaxurl,
                data: alldata,

                beforeSend: function (response) {
                    $this.removeClass('added').addClass('loading');
                },

                complete: function (response) {
                    $this.addClass('added').removeClass('loading');
                },

                success: function (response) {
                    if ( response.error & response.product_url ) {
                        window.location = response.product_url;
                        return;
                    } else {
                        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $this]);
                    }
                },

            });

        }

    };

    $( document ).ready( function() {
        QuickSwish.init();
        $( window ).on( 'resize', QuickSwish.wrapperHeight );
    });

})(jQuery);