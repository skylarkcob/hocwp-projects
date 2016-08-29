<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

global $hocwp_plugin_option, $hocwp_pos_tabs;

$hocwp_plugin_option = new HOCWP_Option( __( 'Plugin Options', 'hocwp-theme' ), 'hocwp_plugin_option' );
$hocwp_plugin_option->set_parent_slug( '' );
$hocwp_plugin_option->set_icon_url( 'dashicons-admin-generic' );
$hocwp_plugin_option->set_position( 66 );
$hocwp_plugin_option->set_use_style_and_script( true );
$hocwp_plugin_option->init();

function hocwp_plugin_remove_option_submenu_page() {
	remove_submenu_page( 'hocwp_plugin_option', 'hocwp_plugin_option' );
}

add_action( 'admin_menu', 'hocwp_plugin_remove_option_submenu_page', 99 );

function hocwp_plugin_redirect_option_page() {
	if ( ! hocwp_doc_man_license_valid() ) {
		$page = hocwp_get_current_admin_page();
		if ( 'hocwp_plugin_option' == $page ) {
			$base_url = admin_url( 'admin.php' );
			$base_url = add_query_arg( 'page', 'hocwp_plugin_license', $base_url );
			wp_redirect( $base_url );
			exit;
		}
	}
}

add_action( 'admin_init', 'hocwp_plugin_redirect_option_page' );

require( HOCWP_PATH . '/options/setting-plugin-license.php' );
require( HOCWP_PATH . '/options/setting-plugin-custom-css.php' );