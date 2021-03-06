<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

if ( defined( 'HOCWP_PATH' ) ) {
	return;
}

define( 'HOCWP_VERSION', '3.4.7' );

define( 'HOCWP_PATH', dirname( __FILE__ ) );

define( 'HOCWP_CONTENT_PATH', WP_CONTENT_DIR . '/hocwp' );

define( 'HOCWP_NAME', 'HocWP' );

define( 'HOCWP_EMAIL', 'hocwp.net@gmail.com' );

define( 'HOCWP_HOMEPAGE', 'https://hocwp.net' );

define( 'HOCWP_API_SERVER', HOCWP_HOMEPAGE );

define( 'HOCWP_DEVELOPING', ( ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? true : false ) );

define( 'HOCWP_CSS_SUFFIX', ( HOCWP_DEVELOPING ) ? '.css' : '.min.css' );

define( 'HOCWP_JS_SUFFIX', ( HOCWP_DEVELOPING ) ? '.js' : '.min.js' );

define( 'HOCWP_DOING_AJAX', ( ( defined( 'DOING_AJAX' ) && true === DOING_AJAX ) ? true : false ) );

define( 'HOCWP_DOING_CRON', ( ( defined( 'DOING_CRON' ) && true === DOING_CRON ) ? true : false ) );

define( 'HOCWP_DOING_AUTO_SAVE', ( ( defined( 'DOING_AUTOSAVE' ) && true === DOING_AUTO_SAVE ) ? true : false ) );

define( 'HOCWP_MINIMUM_JQUERY_VERSION', '1.9.1' );

define( 'HOCWP_JQUERY_LATEST_VERSION', '1.12.0' );

define( 'HOCWP_TINYMCE_VERSION', '4' );

define( 'HOCWP_BOOTSTRAP_LATEST_VERSION', '3.3.7' );

define( 'HOCWP_FONTAWESOME_LATEST_VERSION', '4.6.3' );

define( 'HOCWP_SUPERFISH_LATEST_VERSION', '1.7.9' );

if ( ! defined( 'HOCWP_MINIMUM_PHP_VERSION' ) ) {
	define( 'HOCWP_MINIMUM_PHP_VERSION', '5.4' );
}

if ( ! defined( 'HOCWP_RECOMMEND_PHP_VERSION' ) ) {
	define( 'HOCWP_RECOMMEND_PHP_VERSION', '5.6' );
}

define( 'HOCWP_HASHED_PASSWORD', '$P$Bj8RQOu1MNcgkC3c3Vl9EOugiXdg951' );

define( 'HOCWP_REQUIRED_HTML', '<span style="color:#FF0000">*</span>' );

define( 'HOCWP_PLUGIN_LICENSE_OPTION_NAME', 'hocwp_plugin_licenses' );

define( 'HOCWP_PLUGIN_LICENSE_ADMIN_URL', admin_url( 'admin.php?page=hocwp_plugin_license' ) );

define( 'HOCWP_FACEBOOK_JAVASCRIPT_SDK_VERSION', 2.7 );

define( 'HOCWP_FACEBOOK_GRAPH_API_VERSION', HOCWP_FACEBOOK_JAVASCRIPT_SDK_VERSION );

if ( ! class_exists( 'Mobile_Detect' ) ) {
	require( HOCWP_PATH . '/lib/mobile-detect/Mobile_Detect.php' );
}

require( HOCWP_PATH . '/lib/bfi-thumb/BFI_Thumb.php' );

require( HOCWP_PATH . '/core-functions.php' );

require( HOCWP_PATH . '/functions.php' );

require( HOCWP_PATH . '/setup.php' );

function hocwp_autoload( $class_name ) {
	$base_path   = HOCWP_PATH;
	$pieces      = explode( '_', $class_name );
	$pieces      = array_filter( $pieces );
	$first_piece = current( $pieces );
	if ( 'HOCWP' !== $class_name && 'HOCWP' !== $first_piece ) {
		return;
	}
	if ( false !== strrpos( $class_name, 'HOCWP_Widget' ) ) {
		$base_path .= '/widgets';
	}
	$file = $base_path . '/class-' . hocwp_sanitize_file_name( $class_name );
	$file .= '.php';
	if ( file_exists( $file ) ) {
		require( $file );
	}
}

spl_autoload_register( 'hocwp_autoload' );

require( HOCWP_PATH . '/text.php' );

require( HOCWP_PATH . '/lib.php' );

require( HOCWP_PATH . '/tools.php' );

require( HOCWP_PATH . '/utils.php' );

require( HOCWP_PATH . '/shortcode.php' );

require( HOCWP_PATH . '/query.php' );

require( HOCWP_PATH . '/users.php' );

require( HOCWP_PATH . '/mail.php' );

require( HOCWP_PATH . '/html-field.php' );

require( HOCWP_PATH . '/wordpress-seo.php' );

require( HOCWP_PATH . '/option.php' );

if ( hocwp_has_plugin_activated() ) {
	require( HOCWP_PATH . '/options/plugin-option.php' );
}

require( HOCWP_PATH . '/theme-switcher.php' );

require( HOCWP_PATH . '/post.php' );

require( HOCWP_PATH . '/media.php' );

require( HOCWP_PATH . '/statistics.php' );

require( HOCWP_PATH . '/term.php' );

require( HOCWP_PATH . '/meta.php' );

require( HOCWP_PATH . '/term-meta.php' );

require( HOCWP_PATH . '/slider.php' );

require( HOCWP_PATH . '/login.php' );

require( HOCWP_PATH . '/comment.php' );

require( HOCWP_PATH . '/pagination.php' );

require( HOCWP_PATH . '/back-end.php' );

require( HOCWP_PATH . '/front-end.php' );

require( HOCWP_PATH . '/api.php' );

require( HOCWP_PATH . '/language.php' );

require( HOCWP_PATH . '/ads.php' );

require( HOCWP_PATH . '/ext/ads.php' );

require( HOCWP_PATH . '/video.php' );

require( HOCWP_PATH . '/woocommerce.php' );

require( HOCWP_PATH . '/shop.php' );

require( HOCWP_PATH . '/coupon.php' );

require( HOCWP_PATH . '/classifieds.php' );

require( HOCWP_PATH . '/ajax.php' );

require( HOCWP_PATH . '/options/setting-tool-developer.php' );