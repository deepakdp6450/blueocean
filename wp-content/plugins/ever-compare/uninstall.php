<?php
/*
 * EverCompare Uninstall plugin
 * Uninstalling EverCompare deletes page, and options.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit; // Exit if accessed directly

function ever_compare_uninstall(){

	// Delete page created for this plugin
	$option_data = get_option( 'ever_compare_table_settings_tabs' );
	wp_delete_post( $option_data['compare_page'], true );

	// Option delete
	delete_option( 'evercompare_version' );
	delete_option( 'ever_compare_settings_tabs' );
	delete_option( 'ever_compare_table_settings_tabs' );
	delete_option( 'ever_compare_style_tabs' );
	
}
ever_compare_uninstall();