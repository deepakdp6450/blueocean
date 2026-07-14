<?php
/**
 * Initial functions.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Class to manipulate menus.
 *
 * @since 1.0.0
 */
class uthr_Nav_Walker {

    public function __construct() {

        // Add custom fields to menu
        add_filter( 'wp_setup_nav_menu_item', [ $this, 'add_custom_fields_meta' ] );
        add_action( 'wp_nav_menu_item_custom_fields', [ $this, 'add_custom_fields' ], 10, 4 );

        // Save menu custom fields
        add_action( 'wp_update_nav_menu_item', [ $this, 'update_custom_nav_fields' ], 10, 3 );

    }

    public function add_custom_fields_meta( $menu_item ) {

        $menu_item->megamenu = get_post_meta( $menu_item->ID, '_menu_item_megamenu', true );

        $menu_item->megamenucolumn = get_post_meta( $menu_item->ID, '_menu_item_megamenucolumn', true );

        $menu_item->columntitle = get_post_meta( $menu_item->ID, '_menu_item_columntitle', true );

        return $menu_item;
    }

    public function add_custom_fields( $id, $item, $depth, $args ) {
        ?>
        <p class="field-enablemegamenu description description-wide hidn">
            <label for="edit-menu-item-megamenu-<?php echo esc_attr( $item->ID ); ?>">
                <input type="checkbox" id="edit-menu-item-megamenu-<?php echo esc_attr( $item->ID ); ?>" value="enabled" name="menu-item-megamenu[<?php echo esc_attr( $item->ID ); ?>]"<?php checked( $item->megamenu, 'enabled' ); ?> />
                <strong><?php esc_html_e( 'Enable MegaMenu','uthr' ); ?></strong>
            </label>
        </p>

        <p class="field-megamenucolumn description description-wide hidn">

            <label for="edit-menu-item-megamenucolumn-<?php echo esc_attr( $item->ID ); ?>">

                <?php esc_html_e( 'Column', 'uthr' ); ?>

                <select id="edit-menu-item-megamenucolumn-<?php echo esc_attr( $item->ID ); ?>" class="widefat code edit-menu-item-custom" name="menu-item-megamenucolumn[<?php echo esc_attr( $item->ID ); ?>]">
                    <option value="0"><?php esc_html_e( 'Select Column', 'uthr' ); ?></option>

                    <?php
                        $columns = array(
                            'one'   => esc_html__( 'Column One', 'uthr' ),
                            'two'   => esc_html__( 'Column Two', 'uthr' ),
                            'three' => esc_html__( 'Column Three', 'uthr' ),
                            'four'  => esc_html__( 'Column Four', 'uthr' ),
                            'five'  => esc_html__( 'Column Five', 'uthr' ),
                        );

                        foreach ( $columns as $key => $column ) {
                        ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $item->megamenucolumn, $key ); ?>><?php echo esc_html( $column ); ?>
                            </option>
                        <?php 
                        }
                    ?>

                </select>
            </label>
        </p>

        <p class="field-columntitle description description-wide">
            <label for="edit-menu-item-columntitle-<?php echo esc_attr( $item->ID ); ?>">
                <input type="checkbox" id="edit-menu-item-columntitle-<?php echo esc_attr( $item->ID ); ?>" value="enabled" name="menu-item-columntitle[<?php echo esc_attr( $item->ID ); ?>]"<?php checked( $item->columntitle, 'enabled' ); ?> />
                <strong><?php esc_html_e( 'Disable Link','uthr' ); ?></strong>
            </label>
        </p>

        <?php
    }

    public function update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {

        $field_list = array( 
            'megamenu', 
            'megamenucolumn', 
            'columntitle',  
        );

        foreach ( $field_list as $cusfield ) {
            if( !isset( $_POST['menu-item-'.$cusfield][$menu_item_db_id]) ) {
                $_POST['menu-item-'.$cusfield][$menu_item_db_id] = '';
            }

            $fieldvalue = sanitize_text_field( wp_unslash( $_POST['menu-item-'.$cusfield][$menu_item_db_id] ) );

            update_post_meta( $menu_item_db_id, '_menu_item_'.$cusfield, $fieldvalue );
        }

    }

}

new uthr_Nav_Walker();