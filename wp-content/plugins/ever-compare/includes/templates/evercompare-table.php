<?php defined( 'ABSPATH' ) || exit; ?>
<div class="htcompare-table">
    <?php
        // do_action( 'ever_compare_before_table' );

        if ( ! empty( $evercompare_args['products'] ) ) {
            $products = $evercompare_args['products'];
            array_unshift( $products, array() );
            foreach ( $evercompare_args['fields'] as $field_id => $field ) {
                if ( ! $evercompare_args['evercompare']->is_products_have_field( $field_id, $products ) ) {
                    continue;
                }

                // Generate Filed name
                $name = $evercompare_args['evercompare']->field_name( $field );
                if( array_key_exists( $field_id, $evercompare_args['heading_txt'] ) && !empty( $evercompare_args['heading_txt'][$field_id] ) ){
                    $name = $evercompare_args['evercompare']->field_name( $evercompare_args['heading_txt'][$field_id], true );
                }

                ?>
                    <div class="htcompare-row compare-data-<?php echo esc_attr( $field_id ); ?>">
                        <?php foreach ( $products as $product_id => $product ) : ?>
                            <?php if ( ! empty( $product ) ) : ?>
                                <div class="htcompare-col htcolumn-value" data-title="<?php echo esc_attr( $name ); ?>">
                                    <?php $evercompare_args['evercompare']->compare_display_field( $field_id, $product ); ?>
                                </div>
                            <?php else: ?>
                                <div class="htcompare-col htcolumn-field-name">
                                    <?php echo esc_html( $name ); ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach ?>
                    </div>
                <?php
            }
            echo '<div class="htcompare-table-loader"></div>';
        } else {
            if ( $evercompare_args['empty_compare_text'] ){
                echo '<div class="htcompare-empty-page-text">'.wp_kses_post( $evercompare_args['empty_compare_text'] ).'</div>';
            }

            if( $evercompare_args['return_shop_button'] ){
                echo '<div class="htcompare-return-to-shop"><a href="'.esc_url( wc_get_page_permalink( 'shop' ) ).'" class="button">'.esc_html( $evercompare_args['return_shop_button'] ).'</a></div>';
            }
        }

        do_action( 'ever_compare_after_table' );
    ?>
</div>