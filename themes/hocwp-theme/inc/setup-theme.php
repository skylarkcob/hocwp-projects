<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

$lang        = hocwp_get_language();
$dash_widget = hocwp_dashboard_services_news_widget();

remove_action( 'wp_head', 'wp_generator' );

global $hocwp_theme_license, $pagenow;

function hocwp_theme_switched( $new_name, $new_theme ) {
	if ( ! current_user_can( 'switch_themes' ) ) {
		return;
	}
	flush_rewrite_rules();
	do_action( 'hocwp_theme_deactivation', $new_name, $new_theme );
}

add_action( 'switch_theme', 'hocwp_theme_switched', 10, 2 );

function hocwp_theme_after_switch( $old_name, $old_theme ) {
	if ( ! current_user_can( 'switch_themes' ) ) {
		return;
	}
	update_option( 'hocwp_version', HOCWP_VERSION );
	if ( hocwp_is_debugging() || hocwp_is_localhost() ) {
		hocwp_update_permalink_struct( '/%category%/%postname%.html' );
	}
	flush_rewrite_rules();
	do_action( 'hocwp_theme_activation', $old_name, $old_theme );
}

add_action( 'after_switch_theme', 'hocwp_theme_after_switch', 10, 2 );

function hocwp_setup_theme_change_default_data() {
	if ( hocwp_is_localhost() ) {
		$page = hocwp_get_post_by_slug( 'sample-page', 'page' );
		if ( is_a( $page, 'WP_Post' ) ) {
			$data = array(
				'ID'         => $page->ID,
				'post_title' => 'Giới thiệu',
				'post_name'  => 'gioi-thieu'
			);
			wp_update_post( $data );
		}
	}
}

add_action( 'hocwp_theme_activation', 'hocwp_setup_theme_change_default_data' );

function hocwp_setup_theme_data() {
	load_theme_textdomain( 'hocwp-theme', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	register_nav_menus(
		array(
			'top'       => __( 'Top menu', 'hocwp-theme' ),
			'primary'   => __( 'Primary menu', 'hocwp-theme' ),
			'secondary' => __( 'Secondary menu', 'hocwp-theme' ),
			'mobile'    => __( 'Mobile menu', 'hocwp-theme' ),
			'footer'    => __( 'Footer menu', 'hocwp-theme' )
		)
	);
	$support_args = array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' );
	add_theme_support( 'html5', $support_args );
	$support_args = array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
		'gallery',
		'status',
		'audio',
		'chat'
	);
	add_theme_support( 'post-formats', $support_args );
}

add_action( 'after_setup_theme', 'hocwp_setup_theme_data' );

function hocwp_setup_theme_init_hook() {
	if ( ! current_user_can( 'read' ) ) {
		show_admin_bar( false );
	}
	$hour  = absint( hocwp_get_current_date( 'h' ) );
	$range = hocwp_newsletter_time_range();
	if ( $range[0] < $hour && $range[1] > $hour ) {
		add_action( 'hocwp_after_wp_footer', 'hocwp_theme_notification_posts_ajax_script' );
	}
	do_action( 'hocwp_sliders_init' );
}

add_action( 'init', 'hocwp_setup_theme_init_hook' );

function hocwp_setup_theme_body_class( $classes ) {
	$classes[] = 'front-end';
	if ( is_single() || is_page() || is_singular() ) {
		$classes[] = 'hocwp-single';
	}
	$classes[] = hocwp_get_browser();
	if ( ! hocwp_theme_license_valid( hocwp_theme_get_license_defined_data() ) ) {
		$classes[] = 'hocwp-invalid-license';
	}
	if ( is_user_logged_in() ) {
		$classes[] = 'hocwp-user';
		global $current_user;
		if ( hocwp_is_admin( $current_user ) ) {
			$classes[] = 'hocwp-user-admin';
		}
		$classes[] = 'hocwp-user';
	} else {
		$classes[] = 'hocwp-guest';
	}

	$only_content = apply_filters( 'hocwp_body_class_only_content', false );
	if ( $only_content ) {
		$classes[] = 'only-content';
	}

	return $classes;
}

add_filter( 'body_class', 'hocwp_setup_theme_body_class' );

function hocwp_setup_theme_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'hocwp_content_width', 640 );
}

add_action( 'after_setup_theme', 'hocwp_setup_theme_content_width', 0 );

function hocwp_setup_theme_widgets_init() {
	global $hocwp_reading_options;
	$statistics = (bool) hocwp_get_value_by_key( $hocwp_reading_options, 'statistics' );
	$statistics = apply_filters( 'hocwp_use_statistics', $statistics );
	register_widget( 'HOCWP_Widget_Banner' );
	register_widget( 'HOCWP_Widget_Facebook_Box' );
	register_widget( 'HOCWP_Widget_Facebook_Messenger' );
	register_widget( 'HOCWP_Widget_Post' );
	register_widget( 'HOCWP_Widget_Top_Commenter' );
	register_widget( 'HOCWP_Widget_Icon' );
	register_widget( 'HOCWP_Widget_FeedBurner' );
	register_widget( 'HOCWP_Widget_Subscribe' );
	register_widget( 'HOCWP_Widget_Social' );
	register_widget( 'HOCWP_Widget_Term' );
	register_widget( 'HOCWP_Widget_Tabber' );
	if ( $statistics ) {
		register_widget( 'HOCWP_Widget_Statistics' );
	}
	$link_manager = apply_filters( 'pre_option_link_manager_enabled', false );
	if ( $link_manager ) {
		register_widget( 'HOCWP_Widget_Link' );
	}
	$default_sidebars = hocwp_theme_get_default_sidebars();
	foreach ( $default_sidebars as $name => $data ) {
		$query  = hocwp_get_post_by_meta( 'sidebar_id', $name, array( 'post_type' => 'hocwp_sidebar' ) );
		$active = true;
		if ( $query->have_posts() ) {
			$current = current( $query->posts );
			$active  = (bool) hocwp_get_post_meta( 'active', $current->ID );
		}
		if ( $active ) {
			hocwp_register_sidebar( $name, $data['name'], $data['description'], $data['tag'] );
		}
	}
}

add_action( 'widgets_init', 'hocwp_setup_theme_widgets_init' );

function hocwp_setup_theme_load_style_and_script( $use ) {
	global $pagenow;
	$current_page = hocwp_get_current_admin_page();
	if ( 'widgets.php' == $pagenow || 'post.php' == $pagenow || 'options-writing.php' == $pagenow || 'options-reading.php' == $pagenow ) {
		$use = true;
	} elseif ( 'hocwp_theme_setting' == $current_page ) {
		$use = true;
	}

	return $use;
}

add_filter( 'hocwp_use_admin_style_and_script', 'hocwp_setup_theme_load_style_and_script' );

function hocwp_setup_theme_support_enqueue_media( $use ) {
	global $pagenow;
	$current_page = hocwp_get_current_admin_page();
	if ( 'widgets.php' == $pagenow || 'options-writing.php' == $pagenow || 'options-reading.php' == $pagenow ) {
		$use = true;
	} elseif ( 'hocwp_theme_setting' == $current_page ) {
		$use = true;
	}

	return $use;
}

add_filter( 'hocwp_wp_enqueue_media', 'hocwp_setup_theme_support_enqueue_media' );

function hocwp_setup_theme_scripts() {
	do_action( 'hocwp_enqueue_scripts' );
	if ( hocwp_use_jquery_cdn() ) {
		hocwp_load_jquery_from_cdn();
	}
	hocwp_theme_register_lib_superfish();
	hocwp_theme_register_lib_bootstrap();
	hocwp_theme_register_lib_font_awesome();
	hocwp_theme_register_core_style_and_script();
	if ( hocwp_theme_sticky_last_widget() ) {
		hocwp_theme_register_lib_sticky();
	}
	$localize_object = array(
		'expand'   => '<span class="screen-reader-text">' . esc_html__( 'expand child menu', 'hocwp-theme' ) . '</span>',
		'collapse' => '<span class="screen-reader-text">' . esc_html__( 'collapse child menu', 'hocwp-theme' ) . '</span>'
	);
	$localize_object = wp_parse_args( $localize_object, hocwp_theme_default_script_localize_object() );
	$use             = hocwp_use_core_style();
	$superfish       = hocwp_use_superfish_menu();
	if ( hocwp_is_debugging() ) {
		wp_localize_script( 'hocwp', 'hocwp', $localize_object );
		wp_register_style( 'hocwp-front-end-style', get_template_directory_uri() . '/hocwp/css/hocwp-front-end' . HOCWP_CSS_SUFFIX, array( 'hocwp-style' ) );
		wp_register_script( 'hocwp-front-end', get_template_directory_uri() . '/hocwp/js/hocwp-front-end' . HOCWP_JS_SUFFIX, array( 'hocwp' ), false, true );
		$style_deps  = array( 'bootstrap-style', 'font-awesome-style', 'superfish-style', 'hocwp-front-end-style' );
		$script_deps = array( 'superfish', 'bootstrap', 'hocwp-front-end' );
		if ( ! $use ) {
			unset( $style_deps[ array_search( 'hocwp-front-end-style', $style_deps ) ] );
		}
		if ( ! $superfish ) {
			unset( $style_deps[ array_search( 'superfish-style', $style_deps ) ] );
			unset( $script_deps[ array_search( 'superfish', $script_deps ) ] );
		}
		wp_register_style( 'hocwp-custom-font-style', get_template_directory_uri() . '/css/hocwp-custom-font' . HOCWP_CSS_SUFFIX );
		$style_deps[] = 'hocwp-custom-font-style';
		wp_register_style( 'hocwp-custom-front-end-style', get_template_directory_uri() . '/css/hocwp-custom-front-end' . HOCWP_CSS_SUFFIX, $style_deps );
		wp_register_script( 'hocwp-custom-front-end', get_template_directory_uri() . '/js/hocwp-custom-front-end' . HOCWP_JS_SUFFIX, $script_deps, false, true );
	} else {
		$style_deps  = array( 'bootstrap-style', 'font-awesome-style', 'superfish-style' );
		$script_deps = array( 'superfish', 'bootstrap' );
		if ( ! $superfish ) {
			unset( $style_deps[ array_search( 'superfish-style', $style_deps ) ] );
			unset( $script_deps[ array_search( 'superfish', $script_deps ) ] );
		}
		wp_register_style( 'hocwp-custom-front-end-style', get_template_directory_uri() . '/css/hocwp-custom-front-end' . HOCWP_CSS_SUFFIX, $style_deps, HOCWP_THEME_VERSION );
		wp_register_script( 'hocwp-custom-front-end', get_template_directory_uri() . '/js/hocwp-custom-front-end' . HOCWP_JS_SUFFIX, $script_deps, HOCWP_THEME_VERSION, true );
		wp_localize_script( 'hocwp-custom-front-end', 'hocwp', $localize_object );
	}
	if ( ! hocwp_in_maintenance_mode() ) {
		wp_enqueue_style( 'hocwp-custom-front-end-style' );
		wp_enqueue_script( 'hocwp-custom-front-end' );
	}
	if ( is_singular() ) {
		$post_id = get_the_ID();
		if ( comments_open( $post_id ) && (bool) get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
}

add_action( 'wp_enqueue_scripts', 'hocwp_setup_theme_scripts' );

function hocwp_setup_theme_login_scripts() {
	hocwp_theme_register_lib_bootstrap();
	hocwp_theme_register_core_style_and_script();
	wp_register_style( 'hocwp-login-style', get_template_directory_uri() . '/hocwp/css/hocwp-login' . HOCWP_CSS_SUFFIX, array( 'bootstrap-theme-style' ), HOCWP_THEME_VERSION );
	wp_register_script( 'hocwp-login', get_template_directory_uri() . '/hocwp/js/hocwp-login' . HOCWP_JS_SUFFIX, array(
		'jquery',
		'hocwp'
	), HOCWP_THEME_VERSION, true );
	wp_localize_script( 'hocwp', 'hocwp', hocwp_theme_default_script_localize_object() );
	wp_enqueue_style( 'hocwp-login-style' );
	wp_enqueue_script( 'hocwp-login' );
}

add_action( 'login_enqueue_scripts', 'hocwp_setup_theme_login_scripts' );

function hocwp_setup_theme_admin_scripts() {
	global $pagenow;
	if ( 'post-new.php' == $pagenow || 'post.php' == $pagenow ) {
		$post_type = hocwp_get_current_post_type();
		if ( 'hocwp_slider' == $post_type ) {
			add_filter( 'hocwp_use_color_picker', '__return_true' );
		}
	}
	hocwp_admin_enqueue_scripts();
	$jquery_ui_datetime_picker = apply_filters( 'hocwp_admin_jquery_datetime_picker', false );
	if ( (bool) $jquery_ui_datetime_picker ) {
		hocwp_enqueue_jquery_ui_datepicker();
	}
	if ( 'profile.php' == $pagenow ) {
		hocwp_google_plus_client_script();
	}
}

add_action( 'admin_enqueue_scripts', 'hocwp_setup_theme_admin_scripts' );

function hocwp_setup_theme_check_javascript_supported() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}

add_action( 'wp_head', 'hocwp_setup_theme_check_javascript_supported', 99 );

function hocwp_setup_theme_admin_footer_text() {
	$text = sprintf( __( 'Thank you for creating with %s. Proudly powered by WordPress.' ), '<a href="' . HOCWP_HOMEPAGE . '">hocwp</a>' );

	return '<span id="footer-thankyou">' . $text . '</span>';
}

add_filter( 'admin_footer_text', 'hocwp_setup_theme_admin_footer_text', 99 );

function hocwp_setup_theme_admin_footer() {
	global $pagenow;
	if ( 'index.php' === $pagenow ) {
		hocwp_dashboard_widget_script();
	}
}

add_action( 'admin_footer', 'hocwp_setup_theme_admin_footer' );

function hocwp_setup_theme_update_footer( $text ) {
	$tmp = strtolower( $text );
	if ( hocwp_string_contain( $tmp, 'version' ) ) {
		$text = sprintf( __( 'Theme core version %s', 'hocwp-theme' ), HOCWP_THEME_CORE_VERSION );
	}

	return $text;
}

add_filter( 'update_footer', 'hocwp_setup_theme_update_footer', 99 );

function hocwp_setup_theme_remove_editor_menu() {
	$remove = apply_filters( 'hocwp_remove_theme_editor_menu', true );
	if ( $remove ) {
		$current_page = isset( $GLOBALS['pagenow'] ) ? $GLOBALS['pagenow'] : '';
		if ( 'theme-editor.php' == $current_page ) {
			wp_redirect( admin_url( '/' ) );
			exit;
		}
		remove_submenu_page( 'themes.php', 'theme-editor.php' );
	}
	$current_admin_page = hocwp_get_current_admin_page();
	if ( 'hocwp_theme_option' == $current_admin_page ) {
		$admin_url = admin_url( 'admin.php' );
		$admin_url = add_query_arg( array( 'page' => $current_admin_page ), $admin_url );
		wp_redirect( $admin_url );
		exit;
	}
}

add_action( 'admin_init', 'hocwp_setup_theme_remove_editor_menu' );

function hocwp_setup_theme_login_headerurl() {
	$url = home_url( '/' );
	$url = apply_filters( 'hocwp_login_logo_url', $url );

	return $url;
}

add_filter( 'login_headerurl', 'hocwp_setup_theme_login_headerurl' );

function hocwp_setup_theme_login_headertitle() {
	$desc = get_bloginfo( 'description' );
	$desc = apply_filters( 'hocwp_login_logo_description', $desc );

	return $desc;
}

add_filter( 'login_headertitle', 'hocwp_setup_theme_login_headertitle' );

function hocwp_setup_theme_check_license() {
	if ( ! isset( $_POST['submit'] ) && ! hocwp_is_login_page() ) {
		if ( ! hocwp_theme_license_valid( hocwp_theme_get_license_defined_data() ) || ! has_action( 'hocwp_check_license', 'hocwp_theme_custom_check_license' ) ) {
			hocwp_theme_invalid_license_redirect();
		}
	}
}

add_action( 'hocwp_check_license', 'hocwp_setup_theme_check_license' );

function hocwp_setup_theme_invalid_license_message() {
	delete_transient( 'hocwp_invalid_theme_license' );
	$args = array(
		'error' => true,
		'title' => __( 'Error', 'hocwp-theme' ),
		'text'  => sprintf( __( 'Your theme is using an invalid license key! If you does not have one, please contact %1$s via email address %2$s for more information.', 'hocwp-theme' ), '<strong>' . HOCWP_NAME . '</strong>', '<a href="mailto:' . esc_attr( HOCWP_EMAIL ) . '">' . HOCWP_EMAIL . '</a>' )
	);
	hocwp_admin_notice( $args );
	$theme = wp_get_theme();
	hocwp_send_mail_invalid_license( $theme->get( 'Name' ) );
}

function hocwp_setup_theme_invalid_license_admin_notice() {
	$transient_name = hocwp_build_transient_name( 'hocwp_invalid_theme_license_%s', '' );
	if ( false !== ( $result = get_transient( $transient_name ) ) && 1 == $result ) {
		hocwp_setup_theme_invalid_license_message();
	}
}

add_action( 'admin_notices', 'hocwp_setup_theme_invalid_license_admin_notice' );

function hocwp_setup_theme_admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {
	$args = array(
		'id'     => 'theme-options',
		'title'  => __( 'Theme Options', 'hocwp-theme' ),
		'href'   => admin_url( 'admin.php?page=hocwp_theme_setting' ),
		'parent' => 'site-name'
	);
	$wp_admin_bar->add_node( $args );

	if ( hocwp_wc_installed() ) {
		$args = array(
			'id'     => 'shop-settings',
			'title'  => __( 'Shop Settings', 'hocwp-theme' ),
			'href'   => admin_url( 'admin.php?page=wc-settings' ),
			'parent' => 'site-name'
		);
		$wp_admin_bar->add_node( $args );
	}

	$args = array(
		'id'     => 'plugins',
		'title'  => __( 'Plugins', 'hocwp-theme' ),
		'href'   => admin_url( 'plugins.php' ),
		'parent' => 'site-name'
	);
	$wp_admin_bar->add_node( $args );

	if ( hocwp_plugin_wpsupercache_installed() ) {
		$args = array(
			'id'     => 'wpsupercache-content',
			'title'  => __( 'Delete cache', 'hocwp-theme' ),
			'href'   => admin_url( 'options-general.php?page=wpsupercache&tab=contents#listfiles' ),
			'parent' => 'site-name'
		);
		$wp_admin_bar->add_node( $args );
	}
}

if ( ! is_admin() && current_user_can( 'create_users' ) ) {
	add_action( 'admin_bar_menu', 'hocwp_setup_theme_admin_bar_menu', 99 );
}

function hocwp_setup_theme_language_attributes( $output ) {
	if ( ! is_admin() ) {
		if ( 'vi' == hocwp_get_language() ) {
			$output = 'lang="vi"';
		}
	}

	return $output;
}

add_filter( 'language_attributes', 'hocwp_setup_theme_language_attributes' );

function hocwp_setup_theme_wpseo_locale( $locale ) {
	if ( ! is_admin() ) {
		if ( 'vi' == hocwp_get_language() ) {
			$locale = 'vi';
		}
	}

	return $locale;
}

add_filter( 'wpseo_locale', 'hocwp_setup_theme_wpseo_locale' );

function hocwp_setup_theme_wpseo_meta_box_priority() {
	return 'low';
}

add_filter( 'wpseo_metabox_prio', 'hocwp_setup_theme_wpseo_meta_box_priority' );

function hocwp_setup_theme_default_hidden_meta_boxes( $hidden, $screen ) {
	if ( 'post' == $screen->base ) {
		$defaults = array(
			'slugdiv',
			'trackbacksdiv',
			'postcustom',
			'postexcerpt',
			'commentstatusdiv',
			'commentsdiv',
			'authordiv',
			'revisionsdiv'
		);
		$hidden   = wp_parse_args( $hidden, $defaults );
	}

	return $hidden;
}

add_filter( 'default_hidden_meta_boxes', 'hocwp_setup_theme_default_hidden_meta_boxes', 10, 2 );

function hocwp_theme_pre_ping( &$links ) {
	$home = get_option( 'home' );
	foreach ( $links as $l => $link ) {
		if ( 0 === strpos( $link, $home ) ) {
			unset( $links[ $l ] );
		}
	}
}

add_action( 'pre_ping', 'hocwp_theme_pre_ping' );

function hocwp_theme_intermediate_image_sizes_advanced( $sizes ) {
	if ( isset( $sizes['thumbnail'] ) ) {
		unset( $sizes['thumbnail'] );
	}
	if ( isset( $sizes['medium'] ) ) {
		unset( $sizes['medium'] );
	}
	if ( isset( $sizes['large'] ) ) {
		unset( $sizes['large'] );
	}

	return $sizes;
}

add_filter( 'intermediate_image_sizes_advanced', 'hocwp_theme_intermediate_image_sizes_advanced' );

function hocwp_setup_theme_wp_dashboard_setup() {
	$is_admin = hocwp_is_admin();
	if ( $is_admin ) {
		wp_add_dashboard_widget( 'hocwp_useful_links', __( 'Useful Links', 'hocwp-theme' ), 'hocwp_theme_dashboard_widget_useful_links' );
		wp_add_dashboard_widget( 'hocwp_services_news', __( 'Services Information', 'hocwp-theme' ), 'hocwp_theme_dashboard_widget_services_news' );
	}
	wp_add_dashboard_widget( 'hocwp_wordpress_release', __( 'WordPress Releases', 'hocwp-theme' ), 'hocwp_theme_dashboard_widget_wordpress_release' );

	global $wp_meta_boxes;

	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
	$backups          = array();
	if ( $is_admin ) {
		$backups['hocwp_useful_links'] = $normal_dashboard['hocwp_useful_links'];
	}
	foreach ( $backups as $key => $widget ) {
		unset( $normal_dashboard[ $key ] );
	}
	$sorted_dashboard                             = array_merge( $backups, $normal_dashboard );
	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;

	if ( $is_admin ) {
		$backup = $wp_meta_boxes['dashboard']['normal']['core']['hocwp_services_news'];
		unset( $wp_meta_boxes['dashboard']['normal']['core']['hocwp_services_news'] );
		$wp_meta_boxes['dashboard']['side']['core']['hocwp_services_news'] = $backup;
	}

	$side_dashboard = $wp_meta_boxes['dashboard']['side']['core'];
	$backups        = array();
	if ( $is_admin ) {
		$backups['hocwp_services_news'] = $side_dashboard['hocwp_services_news'];
	}
	foreach ( $backups as $key => $widget ) {
		unset( $side_dashboard[ $key ] );
	}
	$sorted_dashboard                           = array_merge( $backups, $side_dashboard );
	$wp_meta_boxes['dashboard']['side']['core'] = $sorted_dashboard;

	$backup = $wp_meta_boxes['dashboard']['normal']['core']['hocwp_wordpress_release'];
	unset( $wp_meta_boxes['dashboard']['normal']['core']['hocwp_wordpress_release'] );
	$wp_meta_boxes['dashboard']['side']['core']['hocwp_wordpress_release'] = $backup;
}

function hocwp_theme_dashboard_widget_wordpress_release() {
	$feeds = array(
		'url'    => 'http://wordpress.org/news/category/releases/feed/',
		'number' => 3
	);
	hocwp_dashboard_widget_cache( 'hocwp_wordpress_release', 'hocwp_dashboard_widget_rss_cache', $feeds );
}

function hocwp_theme_dashboard_widget_useful_links() {
	?>
	<div class="rss-widget">
		<ul>
			<li>
				<a target="_blank" href="http://hocwp.net/donate/" class="rss-link">Thông tin chuyển khoản</a>
			</li>
			<li>
				<a target="_blank" href="http://hocwp.net/blog/wp-guides/" class="rss-link">Hướng dẫn tạo blog WordPress
					chi tiết</a>
			</li>
			<li>
				<a target="_blank" href="http://hocwp.net/thong-tin-dich-vu/" class="rss-link">Cập nhật các thông báo,
					thông tin dịch vụ</a>
			</li>
		</ul>
	</div>
	<?php
}

function hocwp_theme_dashboard_widget_services_news() {
	hocwp_dashboard_widget_cache( 'hocwp_services_news', 'hocwp_dashboard_widget_rss_cache', trailingslashit( HOCWP_HOMEPAGE ) . 'home/feed/' );
}

function hocwp_setup_theme_dashboard_primary_link() {
	return trailingslashit( HOCWP_HOMEPAGE );
}

function hocwp_setup_theme_dashboard_primary_feed() {
	return trailingslashit( HOCWP_HOMEPAGE ) . 'feed/';
}

function hocwp_setup_theme_dashboard_secondary_link() {
	return trailingslashit( HOCWP_HOMEPAGE ) . 'blog/';
}

function hocwp_setup_theme_dashboard_secondary_feed() {
	return trailingslashit( HOCWP_HOMEPAGE ) . 'blog/feed/';
}

function hocwp_setup_theme_dashboard_primary_title() {
	return __( 'Services News', 'hocwp-theme' );
}

function hocwp_setup_theme_dashboard_secondary_items() {
	return 5;
}

if ( 'vi' == $lang && $dash_widget ) {
	add_action( 'wp_dashboard_setup', 'hocwp_setup_theme_wp_dashboard_setup' );
	add_filter( 'dashboard_primary_link', 'hocwp_setup_theme_dashboard_primary_link' );
	add_filter( 'dashboard_primary_feed', 'hocwp_setup_theme_dashboard_primary_feed' );
	add_filter( 'dashboard_secondary_link', 'hocwp_setup_theme_dashboard_secondary_link' );
	add_filter( 'dashboard_secondary_feed', 'hocwp_setup_theme_dashboard_secondary_feed' );
	add_filter( 'dashboard_primary_title', 'hocwp_setup_theme_dashboard_primary_title', 1 );
	add_filter( 'dashboard_secondary_title', 'hocwp_setup_theme_dashboard_primary_title' );
	add_filter( 'dashboard_secondary_items', 'hocwp_setup_theme_dashboard_secondary_items' );
}

function hocwp_theme_on_upgrade() {
	global $hocwp_reading_options;
	$version = get_option( 'hocwp_version' );
	if ( version_compare( $version, HOCWP_VERSION, '<' ) ) {
		update_option( 'hocwp_version', HOCWP_VERSION );
		do_action( 'hocwp_theme_upgrade' );
	}
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	$reading = $hocwp_reading_options;
	if ( (bool) hocwp_get_value_by_key( $reading, 'trending' ) ) {
		hocwp_post_trending_table_init();
	}
	if ( (bool) hocwp_get_value_by_key( $reading, 'post_statistics' ) ) {
		hocwp_statistics_table_init();
	}
	if ( (bool) hocwp_get_value_by_key( $reading, 'search_tracking' ) ) {
		hocwp_search_tracking_table_init();
	}
}

add_action( 'admin_init', 'hocwp_theme_on_upgrade' );

function hocwp_theme_update_rewrite_rules() {
	flush_rewrite_rules();
}

add_action( 'hocwp_theme_upgrade', 'hocwp_theme_update_rewrite_rules' );
add_action( 'hocwp_theme_activation', 'hocwp_theme_update_rewrite_rules' );
add_action( 'hocwp_change_domain', 'hocwp_theme_update_rewrite_rules' );

function hocwp_setup_theme_esc_comment_author_url( $commentdata ) {
	$comment_author_url = hocwp_get_value_by_key( $commentdata, 'comment_author_url' );
	if ( ! empty( $comment_author_url ) ) {
		$commentdata['comment_author_url'] = esc_url( hocwp_get_root_domain_name( $comment_author_url ) );
	}

	return $commentdata;
}

add_filter( 'preprocess_comment', 'hocwp_setup_theme_esc_comment_author_url' );

function hocwp_setup_theme_admin_menu() {
	global $hocwp_private_post_types;
	$hocwp_private_post_types = hocwp_sanitize_array( $hocwp_private_post_types );
	if ( hocwp_array_has_value( $hocwp_private_post_types ) ) {
		foreach ( $hocwp_private_post_types as $post_type ) {
			$object = get_post_type_object( $post_type );
			remove_menu_page( 'edit.php?post_type=' . $post_type );
			add_submenu_page( 'hocwp_private_types', $object->labels->name, $object->labels->name, 'manage_options', 'edit.php?post_type=' . $post_type );
		}
	}
}

add_action( 'admin_menu', 'hocwp_setup_theme_admin_menu', 99 );

function hocwp_setup_theme_admin_parent_file() {
	global $pagenow;
	if ( 'post-new.php' == $pagenow || 'post.php' == $pagenow || 'edit.php' == $pagenow ) {
		global $hocwp_private_post_types;
		$post_type = hocwp_get_current_post_type();
		if ( is_array( $hocwp_private_post_types ) && in_array( $post_type, $hocwp_private_post_types ) ) {
			global $parent_file, $submenu_file;
			$parent_file  = 'hocwp_private_types';
			$submenu_file = 'edit.php?post_type=' . $post_type;
			$script       = 'jQuery(document).ready(function($) {';
			$script .= '$(\'#toplevel_page_' . $parent_file . ', #toplevel_page_' . $parent_file . ' > a\').removeClass(\'wp-not-current-submenu\').addClass(\'wp-has-current-submenu\');';
			$script .= '});';
			hocwp_inline_script( $script );
		}
	}
}

add_action( 'admin_head', 'hocwp_setup_theme_admin_parent_file' );

function hocwp_setup_theme_remove_admin_menu() {
	global $submenu;
	unset( $submenu['themes.php'][6] );
	remove_submenu_page( 'hocwp_theme_option', 'hocwp_theme_option' );
	remove_submenu_page( 'hocwp_private_types', 'hocwp_private_types' );
}

add_action( 'admin_menu', 'hocwp_setup_theme_remove_admin_menu', 999 );

add_filter( 'hocwp_allow_user_subscribe', '__return_true' );

function hocwp_setup_theme_new_post_type_and_taxonomy() {
	$args = array(
		'name'          => 'Sidebars',
		'singular_name' => 'Sidebar',
		'slug'          => 'hocwp_sidebar',
		'show_in_menu'  => '',
		'labels'        => array(
			'all_items' => 'Sidebars'
		)
	);
	hocwp_register_post_type_private( $args );

	if ( hocwp_wc_installed() ) {
		$args = array(
			'name'          => 'Product Tabs',
			'singular_name' => 'Product Tab',
			'slug'          => 'hocwp_product_tab',
			'show_in_menu'  => '',
			'labels'        => array(
				'all_items' => 'Product Tabs'
			),
			'supports'      => array( 'editor' )
		);
		hocwp_register_post_type_private( $args );
	}

	$args = array(
		'name'          => 'Sliders',
		'singular_name' => 'Slider',
		'slug'          => 'hocwp_slider',
		'show_in_menu'  => '',
		'labels'        => array(
			'all_items' => 'Sliders'
		)
	);
	hocwp_register_post_type_private( $args );

	$args = array(
		'name'          => 'Ads',
		'singular_name' => 'Ads',
		'slug'          => 'hocwp_ads',
		'show_in_menu'  => '',
		'labels'        => array(
			'all_items' => 'Ads'
		)
	);
	hocwp_register_post_type_private( $args );

	$user_subscribe = apply_filters( 'hocwp_allow_user_subscribe', false );
	if ( $user_subscribe ) {
		$args = array(
			'name'          => 'Subscribers',
			'singular_name' => 'Subscriber',
			'slug'          => 'hocwp_subscriber',
			'show_in_menu'  => '',
			'labels'        => array(
				'all_items' => 'Subscribers'
			)
		);
		hocwp_register_post_type_private( $args );
	}
}

add_action( 'init', 'hocwp_setup_theme_new_post_type_and_taxonomy', 99 );

function hocwp_setup_theme_the_content( $content ) {
	$add_to_content = apply_filters( 'hocwp_add_to_the_content', false );
	if ( $add_to_content ) {
		$backup     = $content;
		$delimiter  = '</p>';
		$paragrahps = explode( $delimiter, $content );
		$count      = 1;
		$custom     = false;
		if ( hocwp_array_has_value( $paragrahps ) ) {
			$paragrahps = hocwp_sanitize_array( $paragrahps );
			$count_p    = count( $paragrahps );
			foreach ( $paragrahps as $key => $value ) {
				if ( trim( $value ) ) {
					$paragrahps[ $key ] .= $delimiter;
					if ( 1 == $count ) {
						$add = apply_filters( 'hocwp_add_to_the_content_after_first_paragraph', '' );
						if ( ! empty( $add ) ) {
							$paragrahps[ $key ] .= $add;
							$custom = true;
						}
					} elseif ( 2 == $count ) {
						$add = apply_filters( 'hocwp_add_to_the_content_after_second_paragraph', '' );
						if ( ! empty( $add ) ) {
							$paragrahps[ $key ] .= $add;
							$custom = true;
						}
					}
					if ( $count == ( $count_p - 1 ) ) {
						$add = apply_filters( 'hocwp_add_to_the_content_before_last_paragraph', '' );
						if ( ! empty( $add ) ) {
							$paragrahps[ $key ] .= $add;
							$custom = true;
						}
					}
					$count ++;
				} else {
					$count_p --;
				}
			}
			if ( $custom ) {
				$content = implode( '', $paragrahps );
			}
			$content = apply_filters( 'hocwp_custom_the_content', $content, $backup );
		}
	}

	return $content;
}

add_filter( 'the_content', 'hocwp_setup_theme_the_content' );

function hocwp_setup_theme_add_default_sidebars() {
	$added = (bool) get_option( 'hocwp_default_sidebars_added' );
	if ( $added ) {
		$query = hocwp_query( array( 'post_type' => 'hocwp_sidebar', 'posts_per_page' => - 1 ) );
		if ( ! $query->have_posts() ) {
			$added = false;
		}
	}
	if ( ! $added ) {
		$sidebars = hocwp_theme_get_default_sidebars();
		foreach ( $sidebars as $name => $data ) {
			$sidebar = hocwp_get_post_by_meta( 'sidebar_id', $name );
			if ( ! $sidebar->have_posts() ) {
				$post_data = array(
					'post_title'  => $data['name'],
					'post_type'   => 'hocwp_sidebar',
					'post_status' => 'publish'
				);
				$post_id   = hocwp_insert_post( $post_data );
				if ( hocwp_id_number_valid( $post_id ) ) {
					update_post_meta( $post_id, 'sidebar_default', 1 );
					update_post_meta( $post_id, 'sidebar_id', $name );
					update_post_meta( $post_id, 'sidebar_name', $data['name'] );
					update_post_meta( $post_id, 'sidebar_description', $data['description'] );
					update_post_meta( $post_id, 'sidebar_tag', $data['tag'] );
					update_post_meta( $post_id, 'active', 1 );
				}
			}
		}
		update_option( 'hocwp_default_sidebars_added', 1 );
	}
}

add_action( 'admin_init', 'hocwp_setup_theme_add_default_sidebars' );

function hocwp_setup_theme_prevent_delete_sidebar( $post_id ) {
	$post = get_post( $post_id );
	if ( 'hocwp_sidebar' == $post->post_type ) {
		$prevent = false;
		if ( ! hocwp_is_admin() ) {
			$prevent = true;
		}
		$default = (bool) get_post_meta( $post_id, 'sidebar_default' );
		if ( $default ) {
			$prevent = true;
		}
		if ( $prevent ) {
			wp_die( __( 'You don\'t have permission to delete this post!', 'hocwp-theme' ) );
		}
	}
}

add_action( 'wp_trash_post', 'hocwp_setup_theme_prevent_delete_sidebar', 10, 1 );
add_action( 'before_delete_post', 'hocwp_setup_theme_prevent_delete_sidebar', 10, 1 );

function hocwp_setup_theme_more_user_profile( $user ) {
	$user_id = $user->ID;
	?>
	<h3><?php _e( 'Social Accounts', 'hocwp-theme' ); ?></h3>
	<table class="form-table">
		<tr>
			<th><label for="facebook">Facebook</label></th>
			<td>
				<?php
				$facebook = get_the_author_meta( 'facebook', $user_id );
				$input    = new HOCWP_HTML( 'input' );
				$input->set_attribute( 'name', 'facebook' );
				if ( empty( $facebook ) ) {
					$input->set_attribute( 'type', 'button' );
					$input->set_text( __( 'Connect with Facebook account', 'hocwp-theme' ) );
					$input->set_class( 'button button-secondary hide-if-no-js hocwp-connect-facebook facebook' );
					$input->set_attribute( 'onclick', 'hocwp_facebook_login();' );
				} else {
					$facebook_data = get_the_author_meta( 'facebook_data', $user_id );
					$avatar        = hocwp_get_value_by_key( $facebook_data, array( 'picture', 'data', 'url' ) );
					$email         = hocwp_get_value_by_key( $facebook_data, 'email' );
					if ( ! empty( $avatar ) ) {
						$img = new HOCWP_HTML( 'img' );
						$img->set_image_alt( '' );
						$img->set_image_src( $avatar );
					}
					$input->set_attribute( 'type', 'text' );
					$input->set_attribute( 'readonly', 'readonly' );
					$input->set_attribute( 'value', $facebook . ' - ' . $email );
					$input->set_class( 'regular-text hocwp-disconnect-social facebook' );
					$input->set_attribute( 'data-user-id', $user_id );
					$input->set_attribute( 'data-social', 'facebook' );
				}
				if ( empty( $facebook ) && 'profile.php' == $GLOBALS['pagenow'] ) {
					$input->output();
					if ( empty( $facebook ) ) {
						hocwp_facebook_login_script( array( 'connect' => true ) );
					}
				} else {
					if ( ! empty( $facebook ) ) {
						$input->output();
					} else {
						_e( 'You can only connect to social account on profile page.', 'hocwp-theme' );
					}
				}
				?>
			</td>
		</tr>
		<tr>
			<th><label for="google">Google</label></th>
			<td>
				<?php
				$social    = 'google';
				$social_id = get_the_author_meta( $social, $user_id );
				$input     = new HOCWP_HTML( 'input' );
				$input->set_attribute( 'name', $social );
				if ( empty( $social_id ) ) {
					$input->set_attribute( 'type', 'button' );
					$input->set_text( __( 'Connect with Google account', 'hocwp-theme' ) );
					$input->set_class( 'button button-secondary hide-if-no-js hocwp-connect-' . $social . ' ' . $social );
					$input->set_attribute( 'onclick', 'hocwp_google_login();' );
				} else {
					$facebook_data = get_the_author_meta( $social . '_data', $user_id );
					$avatar        = hocwp_get_value_by_key( $facebook_data, array( 'picture', 'data', 'url' ) );
					$email         = hocwp_get_value_by_key( $facebook_data, array( 'emails', 0, 'value' ) );
					if ( ! empty( $avatar ) ) {
						$img = new HOCWP_HTML( 'img' );
						$img->set_image_alt( '' );
						$img->set_image_src( $avatar );
					}
					$input->set_attribute( 'type', 'text' );
					$input->set_attribute( 'readonly', 'readonly' );
					$input->set_attribute( 'value', $social_id . ' - ' . $email );
					$input->set_class( 'regular-text hocwp-disconnect-social ' . $social );
					$input->set_attribute( 'data-user-id', $user_id );
					$input->set_attribute( 'data-social', $social );
				}
				if ( empty( $social_id ) && 'profile.php' == $GLOBALS['pagenow'] ) {
					$input->output();
					if ( empty( $social_id ) ) {
						hocwp_google_login_script( array( 'connect' => true ) );
					}
				} else {
					if ( ! empty( $social_id ) ) {
						$input->output();
					} else {
						_e( 'You can only connect to social account on profile page.', 'hocwp-theme' );
					}
				}
				?>
			</td>
		</tr>
	</table>
	<?php
}

add_action( 'show_user_profile', 'hocwp_setup_theme_more_user_profile', 1 );
add_action( 'edit_user_profile', 'hocwp_setup_theme_more_user_profile', 1 );

if ( 'profile.php' == $pagenow ) {
	add_filter( 'hocwp_use_admin_style_and_script', '__return_true' );
}

function hocwp_setup_theme_change_language( $lang ) {
	if ( function_exists( 'qtranxf_getLanguage' ) ) {
		$lang = qtranxf_getLanguage();
	}

	return $lang;
}

if ( ! is_admin() ) {
	add_filter( 'hocwp_language', 'hocwp_setup_theme_change_language' );
}

function hocwp_get_archive_title( $prefix = '' ) {
	if ( is_tax() ) {
		$title = single_term_title( '', false );
	} elseif ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	} elseif ( is_author() ) {
		$title = get_the_author();
	} elseif ( is_year() ) {
		$title = get_the_date( 'Y' );
	} elseif ( is_month() ) {
		if ( 'vi' == hocwp_get_language() ) {
			$month = get_the_date( 'F' );
			$title = hocwp_convert_month_name_to_vietnamese( $month );
			hocwp_add_string_with_space_before( $title, get_the_date( 'Y' ) );
		} else {
			$title = get_the_date( 'F Y' );
		}
	} elseif ( is_day() ) {
		if ( 'vi' == hocwp_get_language() ) {
			$month = get_the_date( 'F' );
			$title = hocwp_convert_month_name_to_vietnamese( $month );
			hocwp_add_string_with_space_before( $title, get_the_date( 'Y' ) );
			$title = 'Ngày ' . get_the_date( 'j' ) . ' ' . strtolower( $title );
		} else {
			$title = get_the_date( 'F j, Y' );
		}
	} else {
		if ( is_search() ) {
			$title = get_search_query();
			if ( empty( $title ) ) {
				$title = hocwp_text( 'Kết quả tìm kiếm', __( 'Search results', 'hocwp-theme' ), false );
			}
		} else {
			$title = hocwp_text( 'Lưu trữ', __( 'Archive', 'hocwp-theme' ), false );
		}
	}
	if ( ! empty( $prefix ) ) {
		$title = $prefix . $title;
	}

	return apply_filters( 'hocwp_get_archive_title', $title, $prefix );
}

function hocwp_setup_theme_archive_title( $title ) {
	if ( 'vi' == hocwp_get_language() ) {
		$title = hocwp_get_archive_title();
	}

	return $title;
}

add_filter( 'get_the_archive_title', 'hocwp_setup_theme_archive_title' );

function hocwp_setup_theme_schedule_event() {
	if ( ! wp_next_scheduled( 'hocwp_daily_event' ) ) {
		wp_schedule_event( time(), 'daily', 'hocwp_daily_event' );
	}
}

add_action( 'hocwp_theme_activation', 'hocwp_setup_theme_schedule_event' );

function hocwp_setup_theme_clear_scheduled_event() {
	wp_clear_scheduled_hook( 'hocwp_daily_event' );
}

add_action( 'hocwp_theme_deactivation', 'hocwp_setup_theme_clear_scheduled_event' );

function hocwp_setup_theme_woocommerce_product_tabs( $tabs ) {
	$args  = array(
		'posts_per_page' => - 1,
		'post_type'      => 'hocwp_product_tab'
	);
	$query = hocwp_query( $args );
	if ( $query->have_posts() ) {
		foreach ( $query->posts as $tab ) {
			$id          = hocwp_sanitize_id( $tab->post_name );
			$tabs[ $id ] = array(
				'title'    => $tab->post_title,
				'priority' => 20,
				'callback' => 'hocwp_setup_theme_woocommerce_product_tabs_callback'
			);
		}
	}

	return $tabs;

}

function hocwp_setup_theme_404_site_content_inner_before() {
	echo '<div class="col-xs-12">';
}

add_action( 'hocwp_404_site_content_inner_before', 'hocwp_setup_theme_404_site_content_inner_before' );

function hocwp_setup_theme_404_site_content_inner_after() {
	echo '</div>';
}

add_action( 'hocwp_404_site_content_inner_after', 'hocwp_setup_theme_404_site_content_inner_after' );

function hocwp_setup_theme_woocommerce_product_tabs_callback( $key, $tab ) {
	$content = hocwp_get_post_meta( $key, get_the_ID() );
	$content = trim( $content );
	$title   = hocwp_get_value_by_key( $tab, 'title' );
	if ( empty( $content ) ) {
		$slug              = hocwp_sanitize_html_class( $key );
		$hocwp_product_tab = hocwp_get_post_by_slug( $slug, 'hocwp_product_tab' );
		if ( ! hocwp_is_post( $hocwp_product_tab ) ) {
			$hocwp_product_tab = hocwp_get_post_by_column( 'post_title', $title );
		}
		if ( hocwp_is_post( $hocwp_product_tab ) ) {
			$content = $hocwp_product_tab->post_content;
			$content = trim( $content );
		}
	}
	if ( ! empty( $content ) ) {
		if ( ! empty( $title ) ) {
			echo hocwp_wrap_tag( $title, 'h2', 'tab-title' );
		}
		echo '<div class="hocwp-product-info">';
		$embed   = new WP_Embed();
		$content = $embed->run_shortcode( $content );
		$content = $embed->autoembed( $content );
		hocwp_the_custom_content( $content );
		echo '</div>';
	}
}

add_filter( 'woocommerce_product_tabs', 'hocwp_setup_theme_woocommerce_product_tabs' );

function hocwp_setup_theme_build_transient_name( $transient, $format, $dynamic ) {
	if ( defined( 'HOCWP_THEME_CORE_VERSION' ) ) {
		$dynamic .= HOCWP_THEME_CORE_VERSION;
	}
	if ( defined( 'HOCWP_THEME_VERSION' ) ) {
		$dynamic .= HOCWP_THEME_VERSION;
	}
	$dynamic = md5( $dynamic );
	$transient .= '_' . $dynamic;

	return $transient;
}

add_filter( 'hocwp_build_transient_name', 'hocwp_setup_theme_build_transient_name', 10, 3 );