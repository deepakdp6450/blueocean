<?php
namespace QuickSwish;
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
        $this->add_redirection_flag();
    }
    
    /**
     * Add time and version on DB
     */
    public function add_version() {
        $installed = get_option( 'quickswish_installed' );

        if ( ! $installed ) {
            update_option( 'quickswish_installed', time() );
        }

        update_option( 'quickswish_version', QUICKSWISH_VERSION );
    }

    /**
     * [add_redirection_flag] redirection flug
     */
    public function add_redirection_flag(){
        add_option( 'quickswish_do_activation_redirect', true );
    }


}