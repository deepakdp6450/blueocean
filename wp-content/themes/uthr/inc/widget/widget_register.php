<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function uthr_widgets_init(){

    register_sidebar( array(
        'name'          => esc_html__( 'Sidebar ', 'uthr' ),
        'id'            => 'sidebar-1',
        'description'   => esc_html__( 'Add widgets here.', 'uthr' ),
        'before_widget' => '<div id="%1$s" class="uthr-sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="uthr-sidebar-widget-title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => esc_html__( 'Shop Sidebar ', 'uthr' ),
        'id'            => 'sidebar-shop',
        'description'   => esc_html__( 'Add widgets here.', 'uthr' ),
        'before_widget' => '<div id="%1$s" class="uthr-sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="uthr-sidebar-widget-title">',
        'after_title'   => '</h3>',
    ) );

    $footer_columns = 4;
    $j = 1;
    for( $i = 1; $i <= $footer_columns; $i++ ){
        $j++;
        register_sidebar( array(
            'name'          => esc_html__( 'Footer ', 'uthr' ) . esc_html( $i ),
            'id'            => 'sidebar-'.$j,
            'description'   => esc_html__( 'Add widgets here.', 'uthr' ),
            'before_widget' => '<div id="%1$s" class="uthr-footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="uthr-footer-widget-title">',
            'after_title'   => '</h4>',
        ) );
    }

}
add_action( 'widgets_init', 'uthr_widgets_init' );