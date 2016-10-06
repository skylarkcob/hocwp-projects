<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

if ( defined( 'WOOCOMMERCE_VERSION' ) ) {
	add_filter( 'hocwp_shop_site', '__return_true' );
}

if ( ! defined( 'HOCWP_THEME_VERSION' ) ) {
	define( 'HOCWP_THEME_VERSION', '1.0.0' );
}

function hocwp_theme_custom_license_data() {
	$data = array(
		'hashed'  => '',
		'key_map' => ''
	);

	return $data;
}

add_filter( 'hocwp_theme_license_defined_data', 'hocwp_theme_custom_license_data' );