;(function($){
"use strict";

    var QuickSwishAdmin = {

        /**
         * [init]
         * @return {[void]} Initial Function
         */
        init: function(){
            this.MenuActive();

            /**
             * For save value
             */
            this.HideShowField( '.button_position .description', QSVIEW.option_data['button_position'], 'use_shortcode' );
            this.HideShowField( '.button_custom_icon', QSVIEW.option_data['button_icon_type'], 'custom' );
            this.HideShowField( '.popup_custom_style', QSVIEW.option_data['popup_style'], 'custom' );
            this.HideShowField( '.depend_social_share_enable', QSVIEW.option_data['enable_social_share'], 'on' );
            this.HideShowField( '.depend_thumbnail_layout_slider', QSVIEW.option_data['thumbnail_layout'], 'slider' );
            this.HideShowField( '.depend_button_custom_style', QSVIEW.option_data['button_style'], 'custom' );

            /**
             * After Change
             */
            this.ConditionField( '.button_position select', '.button_position .description', 'use_shortcode' );
            this.ConditionField( '.button_icon_type select', '.button_custom_icon', 'custom' );
            this.ConditionField( '.popup_style select', '.popup_custom_style', 'custom' );
            this.ConditionField( '.enable_social_share .checkbox', '.depend_social_share_enable', 'on', 'radio' );
            this.ConditionField( '.thumbnail_layout select', '.depend_thumbnail_layout_slider', 'slider' );
            this.ConditionField( '.button_style select', '.depend_button_custom_style', 'custom' );
            
        },

        /**
         * [MenuActive] Active first menu item
         */
        MenuActive: function(){
            if ( typeof QSVIEW.is_settings != "undefined" && QSVIEW.is_settings == 1 ){
                $('.toplevel_page_quickswish .wp-first-item').addClass('current');
            }
        },

        /**
         * [ConditionField]
         * @param {[String]} controller
         * @param {[String]} field
         * @param {[String]} condition_value
         * @param {String} fieldtype
         */
        ConditionField: function( controller, field, condition_value, fieldtype = 'select' ){
            $( controller ).on('change',function(){
                var change_value = '';
                if( fieldtype === 'radio' ){
                    if( $(this).is(":checked") ){
                        change_value = $(this).val();
                    }
                }else{
                    change_value = $(this).val();
                }
                QuickSwishAdmin.HideShowField( field, change_value, condition_value );
            });

        },

        /**
         * [HideShowField]
         * @param {[String]} field
         * @param {[String]} current_value
         * @param {[String]} condition_value
         */
        HideShowField: function( field, current_value, condition_value ){
            if( current_value === condition_value ){
                $( field ).show();
            }else{
                $( field ).hide();
            }
        },


    };

    $( document ).ready( function() {
        QuickSwishAdmin.init();
    });


})(jQuery);