<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

global $hocwp_plugin_theme_switcher;

function hocwp_theme_switcher_wp_init() {
	if ( ! is_admin() ) {
		$theme = hocwp_get_method_value( 'theme', 'get' );
		if ( empty( $theme ) && hocwp_is_force_mobile() ) {
			$theme = hocwp_theme_switcher_get_mobile_theme_name();
		}
		if ( ! empty( $theme ) ) {
			hocwp_set_session( 'hocwp_current_theme', $theme );
			hocwp_set_cookie( 'hocwp_current_theme', $theme, time() + DAY_IN_SECONDS, '/' );
			if ( is_user_logged_in() ) {
				$unique = get_current_user_id();
				update_option( 'hocwp_user_' . $unique . '_theme', $theme );
				unset( $unique );
			}
		}
		unset( $theme );
	}
}

add_action( 'init', 'hocwp_theme_switcher_wp_init', 10 );

function hocwp_theme_switcher_admin_bar_menu( $wp_admin_bar ) {
	global $hocwp_plugin_theme_switcher;
	$args = array(
		'id'     => 'theme-switcher',
		'title'  => __( 'Theme Switcher', 'hocwp-theme-switcher' ),
		'href'   => $hocwp_plugin_theme_switcher->setting_url,
		'parent' => 'plugins'
	);
	$wp_admin_bar->add_node( $args );
}

if ( ! is_admin() ) {
	add_action( 'admin_bar_menu', 'hocwp_theme_switcher_admin_bar_menu', 99 );
}

function hocwp_theme_switcher_plugins_loaded() {
	global $hocwp_theme_switcher_type, $hocwp_theme_switcher_time, $hocwp_plugin_theme_switcher;

	if ( ! $hocwp_plugin_theme_switcher->license_valid() ) {
		return;
	}

	hocwp_session_start();

	if ( ! empty( $hocwp_theme_switcher_time ) ) {
		$hocwp_theme_switcher_time = time() + DAY_IN_SECONDS;
	}

	$theme = hocwp_theme_switcher_get_current_theme();
	$find  = wp_get_theme( $theme );
	if ( is_a( $find, 'WP_Theme' ) ) {
		add_filter( 'stylesheet', 'hocwp_theme_switcher_control' );
		add_filter( 'template', 'hocwp_theme_switcher_control' );
	}
}

if ( ! is_admin() ) {
	add_action( 'plugins_loaded', 'hocwp_theme_switcher_plugins_loaded', 1 );
}

function hocwp_theme_switcher_to_mobile_theme( $time ) {
	global $hocwp_theme_switcher_type;
	add_filter( 'stylesheet', 'hocwp_theme_switcher_to_mobile_control' );
	add_filter( 'template', 'hocwp_theme_switcher_to_mobile_control' );
	add_filter( 'post_link', 'hocwp_theme_switcher_post_link', 10, 3 );
	add_filter( 'term_link', 'hocwp_theme_switcher_term_link', 10, 3 );
	$hocwp_theme_switcher_type = 'mobile';
	hocwp_theme_switcher_set_cookie( $hocwp_theme_switcher_type, $time );
}

function hocwp_theme_switcher_set_cookie( $type, $time ) {
	hocwp_set_session( 'hocwp_theme_switcher_type', $type );
	hocwp_set_cookie( 'hocwp_theme_switcher_type', $type, $time, '/' );
}

function hocwp_theme_switcher_flush_rewrite_rules() {
	hocwp_flush_rewrite_rules_after_site_url_changed();
}

add_action( 'init', 'hocwp_theme_switcher_flush_rewrite_rules' );

function hocwp_theme_switcher_add_mobile_query_to_link( $url ) {
	if ( ! empty( $url ) ) {
		$url = add_query_arg( array( 'mobile' => 'true' ), $url );
	}

	return $url;
}

function hocwp_theme_switcher_post_link( $permalink, $post, $leavename ) {
	$permalink = hocwp_theme_switcher_add_mobile_query_to_link( $permalink );

	return $permalink;
}

function hocwp_theme_switcher_term_link( $termlink, $term, $taxonomy ) {
	$termlink = hocwp_theme_switcher_add_mobile_query_to_link( $termlink );

	return $termlink;
}

function hocwp_theme_switcher_home_url( $url, $path, $orig_scheme, $blog_id ) {
	$url = hocwp_theme_switcher_add_mobile_query_to_link( $url );

	return $url;
}

function hocwp_theme_switcher_buttons() {
	$home_url     = home_url( '/' );
	$mobile_text  = apply_filters( 'hocwp_theme_switcher_mobile_button_text', __( 'Mobile', 'hocwp-theme-switcher' ) );
	$mobile_url   = add_query_arg( array( 'mobile' => 'true' ), $home_url );
	$desktop_text = apply_filters( 'hocwp_theme_switcher_desktop_button_text', __( 'Desktop', 'hocwp-theme-switcher' ) );
	$desktop_url  = add_query_arg( array( 'mobile' => 'false' ), $home_url );
	$desktop_url  = str_replace( 'm.', '', $desktop_url );
	?>
	<ul id="theme_switcher_buttons" class="list-inline list-unstyled clearfix" style="width: 100%; margin: 0;">
		<li class="text-center col-sm-6 col-xs-6" style="padding: 0;">
			<a href="<?php echo esc_url( $mobile_url ); ?>" class="btn btn-primary"
			   style="width: 100%; display: block; border-radius: 0; background-color: transparent; border: medium none; color: rgb(187, 187, 187);"><?php echo $mobile_text; ?></a>
		</li>
		<li class="text-center col-sm-6 col-xs-6" style="padding: 0;">
			<a href="<?php echo esc_url( $desktop_url ); ?>" class="btn btn-primary"
			   style="width: 100%; display: block; border-radius: 0; border: medium none; background-color: rgb(170, 170, 170); color: rgb(221, 221, 221);"><?php echo $desktop_text; ?></a>
		</li>
	</ul>
	<?php
}

if ( wp_is_mobile() || hocwp_is_mobile_domain_blog() ) {
	add_action( 'wp_footer', 'hocwp_theme_switcher_buttons' );
}