<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$alldata = \MoveAddons\Elementor\Admin_Options_Fields::instance()->userdata();

?>

<div id="htmove-tab-content-userdata" class="htmove-admin-tab-pane">

    <!-- Head Start -->
    <div class="htmove-tab-head">
        <div class="htmove-tab-head-left">
            <div class="htmove-tab-head-icon"><i class="move-server"></i></div>
            <h3 class="htmove-tab-head-title"><?php esc_html_e( 'User Data', 'moveaddons' ); ?></h3>
        </div>
        <div class="htmove-tab-head-right">
            <button class="htmove-admin-btn htmove-option-btn button button-primary disabled" type="submit" disabled="disabled"><?php esc_html_e( 'Save Settings', 'moveaddons' ); ?></button>
        </div>
    </div>
    <!-- Head End -->

    <div class="htmove-admin-accordion">

        <?php
            $i = 0;
            foreach ( $alldata as $data_key => $userdata ) {
                $i++;

                $value = '';
                if ( !empty( $userdata['value'] ) ) {
                    $value = 'value="'.$userdata['value'].'"';
                }
                $fieldtitle = ( isset( $userdata['filedtitle'] ) ? $userdata['filedtitle'] : 'Token' );
                $field_desc = ( isset( $userdata['description'] ) ? $userdata['description'] : '' );

                // Multiple field
                $field_list = ( isset( $userdata['fields'] ) ? $userdata['fields'] : '' );

        ?>
        <div class="htmove-admin-accordion-card <?php echo ( $i== 1 ? 'active' : '' ); ?>">
            <div class="htmove-admin-accordion-head"><?php echo esc_html__( $userdata['title'], 'move' ); ?></div>

            <div class="htmove-admin-accordion-body">
                <div class="htmove-admin-accordion-content">
                    <?php 
                        if( is_array( $field_list ) ):
                            foreach ( $field_list as $fkey => $field ) {
                                $fvalue = '';
                                if ( !empty( $field['fvalue'] ) ) {
                                    $fvalue = 'value="'.$field['fvalue'].'"';
                                }
                                ?>
                                    <div class="htmove-admin-from-field-area">
                                        <h6 class="title"><?php echo esc_html( $field['ftitle'] ); ?></h6>
                                        <div class="htmove-admin-from-field">
                                            <input type="text" placeholder="<?php echo esc_attr( $field['ftitle'] ); ?>" id="<?php echo esc_attr( $fkey ); ?>" name="<?php echo esc_attr( $fkey ); ?>" <?php echo $fvalue; ?>>
                                        </div>
                                    </div>
                                <?php
                            } 
                    ?>
                    <?php else:?>
                        <h6 class="title">
                            <?php 
                                echo esc_html( $fieldtitle );
                                if( !empty( $field_desc ) ){
                                    echo "<span class='field_desc'>{$field_desc}</span>";
                                }
                            ?>
                        </h6>
                        <div class="htmove-admin-from-field">
                            <input type="text" placeholder="<?php echo esc_attr( $fieldtitle ); ?>" id="<?php echo esc_attr( $data_key ); ?>" name="<?php echo esc_attr( $data_key ); ?>" <?php echo $value; ?>>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php } ?>

    </div>

    <div class="htmove-btn-footer">
        <button class="htmove-admin-btn htmove-option-btn button button-primary disabled" type="submit" disabled="disabled"><?php esc_html_e( 'Save Settings', 'moveaddons' ); ?></button>
    </div>

</div>