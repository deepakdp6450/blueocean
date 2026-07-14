<?php

/**
 * Custom walker class.
 */
class uthr_Walker_Nav_Menu extends Walker_Nav_Menu {

    public $megamenu = '';
    public $megamenucolumn = '';
    public $device = '';
 
    /**
     * Starts the list before the elements are added.
     *
     * Adds classes to the unordered list sub-menus.
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     */
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        // Depth-dependent classes.
        $indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // code indent
        $display_depth = ( $depth + 1 ); // because it counts the first submenu as 0
        $classes = array(
            ( $this->megamenu !== 'enabled' ? 'sub-menu' : '' ),
            ( $display_depth % 2 ? 'menu-odd' : 'menu-even' ),
            ( $display_depth >=2 ? 'sub-sub-menu' : '' ),
            ( $this->device == 'mobile' ? 'uthr-sub-menu' : '' ),
            'menu-depth-' . $display_depth,
        );

        if( $this->megamenu === 'enabled' && $this->device != 'mobile' ){
            $classes[] = 'uthr-mega-menu';
        }

        if(  $this->megamenucolumn != '' ){
            $classes[] = 'uthr-mega-menu-col-'.$this->megamenucolumn;
        }

        $class_names = implode( ' ', $classes );
 
        // Build HTML for output.
        $output .= "\n" . $indent . '<ul class="' . $class_names . '">' . "\n";

    }
 
    /**
     * Start the element output.
     *
     * Adds main/sub-classes to the list items and links.
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     * @param int    $id     Current item ID.
     */
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $wp_query;
        $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
 
        // Depth-dependent classes.
        $depth_classes = array(
            ( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
            ( $depth >=2 ? 'sub-sub-menu-item' : '' ),
            ( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
            'menu-item-depth-' . $depth
        );
        $depth_class_names = esc_attr( implode( ' ', $depth_classes ) );
 
        // Passed classes.
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );
 
        // Build HTML.
        $output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';
 
        // Link attributes.
        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        $attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . '"';


        if( isset( $item->megamenu ) ){
            $this->megamenu = $item->megamenu;
        }else{
            $this->megamenu ='';
        }

        if( isset( $item->megamenucolumn ) ){
            $this->megamenucolumn = $item->megamenucolumn;
        }else{
            $this->megamenucolumn ='';
        }

        $icon = $title = '';

        if( $item->columntitle === 'enabled' ){
            $title = true;
        }

        if( $this->has_children ){
            $icon = '<span class="menu-toggle"><i class="icofont-rounded-down"></i></span>';
        }

        if( $args->device == 'mobile' ){
            $this->device = 'mobile';
        }else{
            $this->device = 'desktop';
        }

        // Build HTML output and pass through the proper filter.
        if( $title === true && $this->device != 'mobile' ){
            $item_output = sprintf( '%1$s<span class="menu-title">%2$s</span>%3$s',
                $args->before,
                apply_filters( 'the_title', $item->title, $item->ID ),
                $args->after
            );
        }else{
            if( $args->device == 'mobile' ){
                $item_output = sprintf( '%1$s<a%2$s>%3$s<span class="text">%4$s</span>%5$s</a>%6$s%7$s',
                    $args->before,
                    $attributes,
                    $args->link_before,
                    apply_filters( 'the_title', $item->title, $item->ID ),
                    $args->link_after,
                    $icon,
                    $args->after
                );
            }else{
                $item_output = sprintf( '%1$s<a%2$s>%3$s<span class="text">%4$s</span>%5$s%6$s</a>%7$s',
                    $args->before,
                    $attributes,
                    $args->link_before,
                    apply_filters( 'the_title', $item->title, $item->ID ),
                    $icon,
                    $args->link_after,
                    $args->after
                );
            }

        }
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

}