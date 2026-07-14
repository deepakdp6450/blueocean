<?php
namespace EverCompare;

defined( 'ABSPATH' ) || exit;
/**
 * Installer class
 */
class Installer {

    /**
     * Run the installer
     *
     * @return void
     */
    public function run() {
        $this->add_version();
        $this->create_page();
        $this->add_redirection_flag();
    }

    /**
     * Add time and version on DB
     */
    public function add_version() {
        $installed = get_option( 'evercompare_installed' );

        if ( ! $installed ) {
            update_option( 'evercompare_installed', time() );
        }

        update_option( 'evercompare_version', EVERCOMPARE_VERSION );
    }

    /**
     * [add_redirection_flag] redirection flug
     */
    public function add_redirection_flag(){
        add_option( 'evercompare_do_activation_redirect', true );
    }

    /**
     * [create_page] Create page
     * @return [void]
     */
    private function create_page() {
        if ( function_exists( 'wc_create_page' ) ) {
            $create_page_id = wc_create_page(
                sanitize_title_with_dashes( _x( 'ever-compare', 'page_slug', 'ever-compare' ) ),
                '',
                __( 'EverCompare', 'ever-compare' ),
                '<!-- wp:shortcode -->[evercompare_table]<!-- /wp:shortcode -->'
            );
            if( $create_page_id ){
                ever_compare_update_option( 'ever_compare_table_settings_tabs','compare_page', $create_page_id );
            }
        }
    }


}