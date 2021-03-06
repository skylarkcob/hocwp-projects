<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

if ( ! defined( 'HOCWP_REQUIRE_WP_VERSION' ) ) {
	define( 'HOCWP_REQUIRE_WP_VERSION', '4.4' );
}

if ( ! defined( 'HOCWP_MINIMUM_PHP_VERSION' ) ) {
	define( 'HOCWP_MINIMUM_PHP_VERSION', '5.4' );
}

if ( ! defined( 'HOCWP_RECOMMEND_PHP_VERSION' ) ) {
	define( 'HOCWP_RECOMMEND_PHP_VERSION', '5.6' );
}

if ( version_compare( $GLOBALS['wp_version'], HOCWP_REQUIRE_WP_VERSION, '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';

	return;
}

define( 'HOCWP_THEME_CORE_VERSION', '5.2.3' );

define( 'HOCWP_THEME_REQUIRE_CORE_VERSION', '3.4.5' );

define( 'HOCWP_THEME_PATH', get_template_directory() );

define( 'HOCWP_THEME_INC_PATH', HOCWP_THEME_PATH . '/inc' );

define( 'HOCWP_THEME_URL', get_template_directory_uri() );

define( 'HOCWP_THEME_INC_URL', HOCWP_THEME_URL . '/inc' );

define( 'HOCWP_THEME_CUSTOM_PATH', HOCWP_THEME_PATH . '/custom' );

define( 'HOCWP_THEME_TEMPLATE_PARTS_PATH', HOCWP_THEME_PATH . '/template-parts' );

if ( ! defined( 'HOCWP_URL' ) ) {
	define( 'HOCWP_URL', untrailingslashit( get_template_directory_uri() ) . '/hocwp' );
}

function hocwp_theme_missing_core_notice() {
	?>
	<div class="updated notice settings-error error">
		<p>
			<strong><?php _e( 'Error:', 'hocwp-theme' ); ?></strong> <?php _e( 'Current theme cannot be run properly because of missing core.', 'hocwp-theme' ); ?>
		</p>
	</div>
	<?php
}

require_once( HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-pre-hook.php' );

if ( ! defined( 'HOCWP_PATH' ) ) {
	if ( ! file_exists( HOCWP_THEME_PATH . '/hocwp/load.php' ) ) {
		if ( is_admin() ) {
			add_action( 'admin_notices', 'hocwp_theme_missing_core_notice' );
		} else {
			wp_die( sprintf( __( '%s Theme cannot be displayed because of missing core. Please contact administrator for assistance.', 'hocwp-theme' ), '<strong>' . __( 'Error:', 'hocwp-theme' ) . '</strong> ' ), __( 'Missing Core', 'hocwp-theme' ) );
			exit;
		}

		return;
	}
	require_once( HOCWP_THEME_PATH . '/hocwp/load.php' );
}

function hocwp_theme_invalid_core_version_notice() {
	?>
	<div class="updated notice settings-error error">
		<p>
			<strong><?php _e( 'Error:', 'hocwp-theme' ); ?></strong> <?php _e( 'Current theme cannot be run properly because of using invalid core version. Please update core to latest version or contact administrator for assistance.', 'hocwp-theme' ); ?>
		</p>
	</div>
	<?php
}

if ( version_compare( HOCWP_VERSION, HOCWP_THEME_REQUIRE_CORE_VERSION, '<' ) ) {
	global $pagenow;
	if ( is_admin() ) {
		add_action( 'admin_notices', 'hocwp_theme_invalid_core_version_notice' );
	} else {
		if ( 'wp-login.php' != $pagenow ) {
			wp_die( sprintf( __( '%s Theme cannot be displayed because of using invalid core version. Please contact administrator for assistance.', 'hocwp-theme' ), '<strong>' . __( 'Error:', 'hocwp-theme' ) . '</strong> ' ), __( 'Invalid Core Version', 'hocwp-theme' ) );
			exit;
		}
	}

	return;
}

function hocwp_theme_load_init_action() {
	global $hocwp_reading_options;
	$hocwp_reading_options = hocwp_option_reading();
}

add_action( 'init', 'hocwp_theme_load_init_action' );

require_once( HOCWP_THEME_INC_PATH . '/theme-functions.php' );

require_once( HOCWP_THEME_INC_PATH . '/options/theme-option.php' );

require_once( HOCWP_THEME_INC_PATH . '/setup-theme.php' );

require_once( HOCWP_THEME_INC_PATH . '/meta.php' );

require HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-functions.php';

require HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-shortcode.php';

require HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-admin.php';

require HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-post-type-and-taxonomy.php';

require HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-meta.php';

require HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-hook.php';

require HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-ajax.php';

require HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-translation.php';

require_once( HOCWP_THEME_INC_PATH . '/setup-theme-after.php' );