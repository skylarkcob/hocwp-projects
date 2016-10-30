<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}
global $hocwp_plugin_theme_switcher;

$option = new HOCWP_Option( __( 'Theme switcher', 'hocwp-theme-switcher' ), $hocwp_plugin_theme_switcher->option_name );

$args = array(
	'id'             => 'mobile_theme',
	'title'          => __( 'Default Mobile Theme', 'hocwp-theme-switcher' ),
	'field_callback' => 'hocwp_field_select_theme'
);
$option->add_field( $args );

$hocwp_plugin_theme_switcher->add_option_to_sidebar_tab( $option );

if ( hocwp_theme_switcher_enabled() ) {
	$option->init();
}

hocwp_option_add_object_to_list( $option );