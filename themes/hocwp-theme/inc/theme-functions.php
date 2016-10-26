<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

function hocwp_theme_register_lib_bootstrap() {
	$options         = hocwp_option_optimize();
	$cdn             = (bool) hocwp_get_value_by_key( $options, 'use_bootstrap_cdn', true );
	$style_url       = get_template_directory_uri() . '/lib/bootstrap/css/bootstrap.min.css';
	$theme_style_url = get_template_directory_uri() . '/lib/bootstrap/css/bootstrap-theme.min.css';
	$script_url      = get_template_directory_uri() . '/lib/bootstrap/js/bootstrap.min.js';
	if ( $cdn ) {
		$style_url       = 'https://maxcdn.bootstrapcdn.com/bootstrap/' . HOCWP_BOOTSTRAP_LATEST_VERSION . '/css/bootstrap.min.css';
		$theme_style_url = 'https://maxcdn.bootstrapcdn.com/bootstrap/' . HOCWP_BOOTSTRAP_LATEST_VERSION . '/css/bootstrap-theme.min.css';
		$script_url      = 'https://maxcdn.bootstrapcdn.com/bootstrap/' . HOCWP_BOOTSTRAP_LATEST_VERSION . '/js/bootstrap.min.js';
	}
	wp_register_style( 'bootstrap-style', $style_url );
	wp_register_style( 'bootstrap-theme-style', $theme_style_url, array( 'bootstrap-style' ) );
	wp_register_script( 'bootstrap', $script_url, array( 'jquery' ), false, true );
}

function hocwp_theme_register_lib_sticky() {
	wp_register_script( 'sticky', get_template_directory_uri() . '/lib/sticky/jquery.sticky.js', array( 'jquery' ), false, true );
	wp_enqueue_script( 'sticky' );
}

function hocwp_theme_register_lib_marquee() {
	wp_register_script( 'marquee', get_template_directory_uri() . '/lib/marquee/jquery.marquee.min.js', array( 'jquery' ), false, true );
	wp_enqueue_script( 'marquee' );
}

function hocwp_theme_register_lib_lightslider() {
	wp_enqueue_style( 'lightslider-style', get_template_directory_uri() . '/lib/lightslider/css/lightslider.min.css' );
	wp_enqueue_script( 'lightslider', get_template_directory_uri() . '/lib/lightslider/js/lightslider.min.js', array( 'jquery' ), false, true );
}

function hocwp_theme_register_lib_lazyload() {
	wp_enqueue_script( 'lazyload', get_template_directory_uri() . '/lib/lazyload/jquery.lazyload.min.js', array( 'jquery' ), false, true );
}

function hocwp_theme_register_lib_slick_slider() {
	wp_enqueue_style( 'slick-style', get_template_directory_uri() . '/lib/slick/slick.css' );
	wp_enqueue_style( 'slick-theme-style', get_template_directory_uri() . '/lib/slick/slick-theme.css' );
	wp_enqueue_script( 'slick', get_template_directory_uri() . '/lib/slick/slick.min.js', array( 'jquery' ), false, true );
}

function hocwp_theme_register_lib_fancybox( $deps = array() ) {
	$script_deps = array( 'jquery' );
	$style_deps  = array();
	wp_register_style( 'fancybox-style', HOCWP_THEME_URL . '/lib/fancybox/jquery.fancybox.css', $style_deps );
	wp_register_script( 'fancybox', HOCWP_THEME_URL . '/lib/fancybox/jquery.fancybox.pack.js', $script_deps, false, true );
	$style_deps[]  = 'fancybox-style';
	$script_deps[] = 'fancybox';
	wp_register_style( 'fancybox-buttons-style', HOCWP_THEME_URL . '/lib/fancybox/helpers/jquery.fancybox-buttons.css', $style_deps );
	wp_register_style( 'fancybox-thumbs-style', HOCWP_THEME_URL . '/lib/fancybox/helpers/jquery.fancybox-thumbs.css', $style_deps );

	wp_register_script( 'fancybox-buttons', HOCWP_THEME_URL . '/lib/fancybox/helpers/jquery.fancybox-buttons.js', $script_deps, false, true );
	wp_register_script( 'fancybox-thumbs', HOCWP_THEME_URL . '/lib/fancybox/helpers/jquery.fancybox-thumbs.js', $script_deps, false, true );
	if ( in_array( 'thumbs', $deps ) ) {
		wp_enqueue_style( 'fancybox-thumbs-style' );
		wp_enqueue_script( 'fancybox-thumbs' );
	}
	if ( in_array( 'buttons', $deps ) ) {
		wp_enqueue_style( 'fancybox-buttons-style' );
		wp_enqueue_script( 'fancybox-buttons' );
	}
	wp_enqueue_style( 'fancybox-style' );
	wp_enqueue_script( 'fancybox' );
}

function hocwp_theme_register_lib_malihu_custom_scrollbar_plugin() {
	wp_enqueue_style( 'malihu-custom-scrollbar-style', HOCWP_THEME_URL . '/lib/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css' );
	wp_enqueue_script( 'malihu-custom-scrollbar', HOCWP_THEME_URL . '/lib/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), false, true );
}

function hocwp_theme_register_lib_pdfobject() {
	wp_enqueue_script( 'pdfobject', HOCWP_THEME_URL . '/lib/pdfobject/pdfobject.min.js', array(), false, true );
}

function hocwp_theme_register_lib_zeroclipboard() {
	wp_enqueue_script( 'zeroclipboard', HOCWP_THEME_URL . '/lib/zeroclipboard/ZeroClipboard.min.js', array( 'jquery' ), false, true );
}

function hocwp_theme_register_lib_jquery_countdown() {
	wp_enqueue_script( 'countdown', HOCWP_THEME_URL . '/lib/countdown/jquery.countdown.min.js', array( 'jquery' ), false, true );
}

function hocwp_theme_register_lib_easyzoom() {
	wp_enqueue_style( 'easyzoom-style', HOCWP_THEME_URL . '/lib/easyzoom/easyzoom.css' );
	wp_enqueue_script( 'easyzoom', HOCWP_THEME_URL . '/lib/easyzoom/easyzoom.js', array( 'jquery' ), false, true );
}

function hocwp_theme_register_lib_jackmoore_zoom() {
	wp_enqueue_script( 'jackmoore-zoom', HOCWP_THEME_URL . '/lib/jackmoore-zoom/jquery.zoom.min.js', array( 'jquery' ), false, true );
}

function hocwp_theme_register_lib_elevatezoom() {
	wp_enqueue_script( 'elevatezoom', HOCWP_THEME_URL . '/lib/elevatezoom/jquery.elevatezoom.js', array( 'jquery' ), false, true );
}

function hocwp_theme_register_lib_elevatezoom_plus() {
	wp_enqueue_style( 'elevatezoom-plus-style', HOCWP_THEME_URL . '/lib/elevatezoom-plus/jquery.ez-plus.css' );
	wp_enqueue_script( 'elevatezoom-plus', HOCWP_THEME_URL . '/lib/elevatezoom-plus/jquery.ez-plus.js', array( 'jquery' ), false, true );
}

function hocwp_theme_register_lib_superfish() {
	$options    = hocwp_option_optimize();
	$cdn        = (bool) hocwp_get_value_by_key( $options, 'use_superfish_cdn', true );
	$style_url  = get_template_directory_uri() . '/lib/superfish/css/superfish.min.css';
	$script_url = get_template_directory_uri() . '/lib/superfish/js/superfish.min.js';
	if ( $cdn ) {
		$style_url  = 'https://cdnjs.cloudflare.com/ajax/libs/superfish/' . HOCWP_SUPERFISH_LATEST_VERSION . '/css/superfish.min.css';
		$script_url = 'https://cdnjs.cloudflare.com/ajax/libs/superfish/' . HOCWP_SUPERFISH_LATEST_VERSION . '/js/superfish.min.js';
	}
	wp_register_style( 'superfish-style', $style_url );
	wp_register_script( 'superfish', $script_url, array( 'jquery' ), false, true );
}

function hocwp_theme_register_lib_font_awesome() {
	$options = hocwp_option_optimize();
	$cdn     = (bool) hocwp_get_value_by_key( $options, 'use_fontawesome_cdn', true );
	$url     = get_template_directory_uri() . '/lib/font-awesome/css/font-awesome.min.css';
	if ( $cdn ) {
		$url = 'https://maxcdn.bootstrapcdn.com/font-awesome/' . HOCWP_FONTAWESOME_LATEST_VERSION . '/css/font-awesome.min.css';
	}
	wp_register_style( 'font-awesome-style', $url );
}

function hocwp_theme_register_lib_raty() {
	wp_enqueue_script( 'jquery-raty', get_template_directory_uri() . '/lib/raty/jquery.raty.js', array( 'jquery' ), false, true );
}

function hocwp_theme_register_lib_owl_carousel() {
	wp_enqueue_style( 'owl-carousel-style', get_template_directory_uri() . '/lib/owl-carousel/owl.carousel.css' );
	wp_enqueue_style( 'owl-carousel-theme-style', get_template_directory_uri() . '/lib/owl-carousel/owl.theme.css' );
	wp_enqueue_script( 'owl-carousel', get_template_directory_uri() . '/lib/owl-carousel/owl.carousel.min.js', array( 'jquery' ), false, true );
}

function hocwp_theme_register_lib_bxslider() {
	wp_enqueue_style( 'bxslider-style', get_template_directory_uri() . '/lib/bxslider/jquery.bxslider.css' );
	wp_enqueue_script( 'bxslider', get_template_directory_uri() . '/lib/bxslider/jquery.bxslider.min.js', array( 'jquery' ), false, true );
}

function hocwp_theme_register_lib_google_maps( $api_key = null ) {
	hocwp_register_lib_google_maps( $api_key );
}

function hocwp_theme_register_lib_jcountdown() {
	wp_enqueue_style( 'jcountdown-style', get_template_directory_uri() . '/lib/jcountdown/jcountdown.css' );
	wp_enqueue_script( 'jcountdown', get_template_directory_uri() . '/lib/jcountdown/jcountdown.min.js', array( 'jquery' ), false, true );
}

function hocwp_theme_load_common_and_dashicons_style() {
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'common' );
}

function hocwp_theme_default_script_localize_object() {
	$defaults = hocwp_default_script_localize_object();
	$args     = array(
		'home_url'         => esc_url( home_url( '/' ) ),
		'login_logo_url'   => hocwp_get_login_logo_url(),
		'login_url'        => wp_login_url(),
		'facebook_appid'   => hocwp_get_wpseo_social_facebook_app_id(),
		'mobile_menu_icon' => '<button class="menu-toggle mobile-menu-button" aria-expanded="false" aria-controls=""><i class="fa fa fa-bars"></i><span class="text">' . __( 'Menu', 'hocwp-theme' ) . '</span></button>',
		'search_form'      => get_search_form( false )
	);
	$args     = wp_parse_args( $args, $defaults );

	return apply_filters( 'hocwp_theme_default_script_object', $args );
}

function hocwp_theme_register_core_style_and_script() {
	hocwp_register_core_style_and_script();
}

function hocwp_theme_get_template( $slug, $name = '' ) {
	$slug = 'template-parts/' . $slug;
	get_template_part( $slug, $name );
}

function hocwp_get_theme_template( $name ) {
	hocwp_theme_get_template( 'template', $name );
}

function hocwp_theme_get_content_none() {
	hocwp_theme_get_content( 'none' );
}

function hocwp_theme_get_content( $name ) {
	hocwp_theme_get_template( 'content/content', $name );
}

function hocwp_theme_get_template_page( $name ) {
	hocwp_theme_get_template( 'page/page', $name );
}

function hocwp_theme_get_module( $name ) {
	hocwp_theme_get_template( 'module/module', $name );
}

function hocwp_theme_get_ajax( $name ) {
	hocwp_theme_get_template( 'ajax/ajax', $name );
}

function hocwp_theme_get_carousel( $name ) {
	hocwp_theme_get_template( 'carousel/carousel', $name );
}

function hocwp_theme_get_meta( $name ) {
	hocwp_theme_get_template( 'meta/meta', $name );
}

function hocwp_theme_get_modal( $name ) {
	hocwp_theme_get_template( 'modal/modal', $name );
}

function hocwp_theme_get_loop( $name ) {
	hocwp_theme_get_template( 'loop/loop', $name );
}

function hocwp_theme_get_image_url( $name ) {
	return get_template_directory_uri() . '/images/' . $name;
}

function hocwp_theme_get_home_setting( $key ) {
	return hocwp_theme_get_option( $key, 'home_setting' );
}

function hocwp_theme_get_option( $key, $base = 'theme_setting' ) {
	return hocwp_option_get_value( $base, $key );
}

function hocwp_theme_get_logo_url() {
	$logo = hocwp_theme_get_option( 'logo' );
	$logo = hocwp_sanitize_media_value( $logo );

	return $logo['url'];
}

function hocwp_theme_the_logo() {
	$logo_url   = hocwp_theme_get_logo_url();
	$logo_class = 'hyperlink';
	if ( empty( $logo_url ) ) {
		$logo_url = get_bloginfo( 'name' );
	} else {
		$logo_url   = '<img alt="' . get_bloginfo( 'description' ) . '" src="' . $logo_url . '">';
		$logo_class = 'img-hyperlink';
	}
	hocwp_add_string_with_space_before( $logo_class, 'site-logo' );
	?>
	<div class="site-branding">
		<?php if ( is_front_page() && is_home() ) : ?>
			<h1 class="site-title"<?php hocwp_html_tag_attributes( 'h1', 'site_title' ); ?>><a
					class="<?php echo $logo_class; ?>" title="<?php bloginfo( 'description' ); ?>"
					href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo $logo_url; ?></a></h1>
		<?php else : ?>
			<p class="site-title"<?php hocwp_html_tag_attributes( 'p', 'site_title' ); ?>><a
					class="<?php echo $logo_class; ?>" title="<?php bloginfo( 'description' ); ?>"
					href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo $logo_url; ?></a></p>
		<?php endif; ?>
		<p class="site-description"<?php hocwp_html_tag_attributes( 'p', 'site_description' ); ?>><?php bloginfo( 'description' ); ?></p>
		<?php do_action( 'hocwp_theme_logo' ); ?>
	</div><!-- .site-branding -->
	<?php
}

function hocwp_theme_the_menu( $args = array() ) {
	if ( ! is_array( $args ) ) {
		$args = array(
			'theme_location' => $args
		);
	}
	$items_wrap     = '<ul id="%1$s" class="%2$s">%3$s</ul>';
	$theme_location = isset( $args['theme_location'] ) ? $args['theme_location'] : 'primary';
	$menu_id        = isset( $args['menu_id'] ) ? $args['menu_id'] : $theme_location . '_menu';
	$menu_id        = hocwp_sanitize_id( $menu_id );
	$menu_class     = isset( $args['menu_class'] ) ? $args['menu_class'] : '';
	hocwp_add_string_with_space_before( $menu_class, 'hocwp-menu' );
	hocwp_add_string_with_space_before( $menu_class, $theme_location );
	$nav_class = hocwp_get_value_by_key( $args, 'nav_class' );
	hocwp_add_string_with_space_before( $nav_class, 'navigation' );
	if ( 'primary' == $theme_location ) {
		hocwp_add_string_with_space_before( $nav_class, 'main-navigation' );
	}
	hocwp_add_string_with_space_before( $nav_class, hocwp_sanitize_html_class( $theme_location . '-navigation' ) );
	$superfish = isset( $args['superfish'] ) ? $args['superfish'] : true;
	if ( $superfish ) {
		hocwp_add_string_with_space_before( $menu_class, 'hocwp-superfish-menu' );
		$items_wrap = '<ul id="%1$s" class="sf-menu %2$s">%3$s</ul>';
	}
	$button_text = isset( $args['button_text'] ) ? $args['button_text'] : __( 'Menu', 'hocwp-theme' );
	hocwp_add_string_with_space_before( $nav_class, 'clearfix' );
	?>
	<nav id="<?php echo hocwp_sanitize_id( $theme_location . '_navigation' ); ?>"
	     class="<?php echo $nav_class; ?>"
	     data-button-text="<?php echo $button_text; ?>"<?php hocwp_html_tag_attributes( 'nav', 'site_navigation' ); ?>>
		<?php
		$menu_args = array(
			'theme_location' => $theme_location,
			'menu_class'     => $menu_class,
			'menu_id'        => $menu_id,
			'items_wrap'     => $items_wrap
		);
		$menu_args = wp_parse_args( $args, $menu_args );
		wp_nav_menu( $menu_args );
		?>
	</nav><!-- #site-navigation -->
	<?php
}

function hocwp_theme_site_main_before( $class = '', $outer_class = '' ) {
	$outer_class = apply_filters( 'hocwp_content_area_class', $outer_class );
	hocwp_add_string_with_space_before( $class, 'site-main' );
	hocwp_add_string_with_space_before( $outer_class, 'content-area' );
	?>
	<div id="primary" class="<?php echo $outer_class; ?>">
	<main id="main" class="<?php echo $class; ?>"<?php hocwp_html_tag_attributes( 'main', 'site_main' ); ?>>
	<?php
}

function hocwp_theme_site_main_after() {
	?>
	</main>
	</div>
	<?php
}

function hocwp_theme_add_setting_section( $args ) {
	hocwp_option_add_setting_section( 'theme_setting', $args );
}

function hocwp_theme_add_home_setting_field( $args ) {
	hocwp_option_add_setting_field( 'home_setting', $args );
}

function hocwp_theme_add_home_setting_section( $args ) {
	hocwp_option_add_setting_section( 'home_setting', $args );
}

function hocwp_theme_add_setting_field_sortable_category( $args = array(), $home_setting = true ) {
	$name  = hocwp_get_value_by_key( $args, 'name', 'sortable_category' );
	$title = hocwp_get_value_by_key( $args, 'title', __( 'Sortable Category', 'hocwp-theme' ) );
	if ( ! isset( $args['connect'] ) ) {
		$args['connect'] = true;
	}
	$args['name']           = $name;
	$args['title']          = $title;
	$args['field_callback'] = 'hocwp_field_sortable_term';
	if ( $home_setting ) {
		hocwp_theme_add_home_setting_field( $args );
	} else {
		hocwp_theme_add_setting_field( $args );
	}
}

function hocwp_theme_add_setting_field( $args ) {
	hocwp_option_add_setting_field( 'theme_setting', $args );
}

function hocwp_theme_add_setting_field_mobile_logo() {
	hocwp_theme_add_setting_field( array(
		'id'             => 'mobile_logo',
		'title'          => __( 'Mobile Logo', 'hocwp-theme' ),
		'field_callback' => 'hocwp_field_media_upload'
	) );
}

function hocwp_theme_add_setting_field_footer_logo() {
	hocwp_theme_add_setting_field( array(
		'title'          => __( 'Footer Logo', 'hocwp-theme' ),
		'id'             => 'footer_logo',
		'field_callback' => 'hocwp_field_media_upload'
	) );
}

function hocwp_theme_add_setting_field_footer_logo_text() {
	hocwp_theme_add_setting_field( array(
		'title'          => __( 'Footer Logo Text', 'hocwp-theme' ),
		'id'             => 'footer_logo_text',
		'field_callback' => 'hocwp_field_editor'
	) );
}

function hocwp_theme_add_setting_field_hotline( $field_callback = '' ) {
	$args = array( 'id' => 'hotline', 'title' => __( 'Hotline', 'hocwp-theme' ) );
	if ( hocwp_callback_exists( $field_callback ) ) {
		$args['field_callback'] = $field_callback;
	}
	hocwp_theme_add_setting_field( $args );
}

function hocwp_theme_add_setting_field_hotline_link() {
	hocwp_theme_add_setting_field( array( 'id' => 'hotline_link', 'title' => __( 'Hotline Link', 'hocwp-theme' ) ) );
}

function hocwp_theme_add_setting_field_footer_text() {
	hocwp_theme_add_setting_field( array(
		'title'          => __( 'Footer Text', 'hocwp-theme' ),
		'id'             => 'footer_text',
		'field_callback' => 'hocwp_field_editor'
	) );
}

function hocwp_theme_add_setting_field_footer_copyright() {
	hocwp_theme_add_setting_field( array(
		'title'          => __( 'Footer Copyright', 'hocwp-theme' ),
		'id'             => 'footer_copyright',
		'field_callback' => 'hocwp_field_editor'
	) );
}

function hocwp_theme_get_footer_text() {
	$text = hocwp_theme_get_option( 'footer_text' );
	if ( function_exists( 'pll__' ) ) {
		$text = pll__( $text );
	}
	$text = apply_filters( 'hocwp_replace_text_placeholder', $text );

	return $text;
}

function hocwp_theme_the_footer_text( $the_content = true ) {
	$text = hocwp_theme_get_footer_text();
	if ( $the_content ) {
		$text = hocwp_filter_custom_content( $text );
	} else {
		$text = wpautop( $text );
	}
	echo $text;
}

function hocwp_theme_get_the_custom_content( $content ) {
	$content = hocwp_filter_custom_content( $content );

	return $content;
}

function hocwp_theme_the_custom_content( $content ) {
	echo hocwp_theme_get_the_custom_content( $content );
}

function hocwp_theme_add_setting_field_select_page( $option_name, $title ) {
	hocwp_theme_add_setting_field( array(
		'title'          => $title,
		'id'             => $option_name,
		'field_callback' => 'hocwp_field_select_page'
	) );
}

function hocwp_theme_the_social_list( $args = array() ) {
	hocwp_the_social_list( $args );
}

function hocwp_theme_add_setting_field_term_sortable( $name, $title, $taxonomies = 'category', $only_parent = true ) {
	$taxonomies = hocwp_sanitize_array( $taxonomies );
	$term_args  = array();
	if ( $only_parent ) {
		$term_args['parent'] = 0;
	}
	$args = array(
		'id'             => $name,
		'title'          => $title,
		'field_callback' => 'hocwp_field_sortable_term',
		'connect'        => true,
		'taxonomy'       => $taxonomies,
		'term_args'      => $term_args
	);
	hocwp_theme_add_setting_field( $args );
}

function hocwp_theme_term_meta_field_thumbnail( $taxonomies = array( 'category' ) ) {
	hocwp_term_meta_thumbnail_field( $taxonomies );
}

function hocwp_theme_generate_license( $password, $site_url = '', $domain = '' ) {
	if ( empty( $site_url ) ) {
		$site_url = get_bloginfo( 'url' );
	}
	$license = new HOCWP_License();
	$license->set_password( $password );
	$code = hocwp_generate_serial();
	$license->set_code( $code );
	if ( empty( $domain ) ) {
		$domain = hocwp_get_root_domain_name( $site_url );
	}
	$license->set_domain( $domain );
	$license->set_customer_url( $site_url );
	$license->generate();

	return $license->get_generated();
}

function hocwp_theme_invalid_license_redirect() {
	$option         = hocwp_option_get_object_from_list( 'theme_license' );
	$transient_name = hocwp_build_transient_name( 'hocwp_invalid_theme_license_%s', '' );
	if ( hocwp_object_valid( $option ) && ! $option->is_this_page() ) {
		global $pagenow;
		$admin_page = hocwp_get_current_admin_page();
		if ( ( 'themes.php' != $pagenow || ( 'themes.php' == $pagenow && ! empty( $admin_page ) ) ) && hocwp_can_redirect() ) {
			if ( is_admin() || ( ! is_admin() && ! is_user_logged_in() ) ) {
				set_transient( 'hocwp_invalid_theme_license', 1 );
				wp_redirect( $option->get_page_url() );
				exit;
			}
		} else {
			if ( false === get_transient( $transient_name ) ) {
				add_action( 'admin_notices', 'hocwp_setup_theme_invalid_license_message' );
			}
		}
	} else {
		if ( false === get_transient( $transient_name ) ) {
			add_action( 'admin_notices', 'hocwp_setup_theme_invalid_license_message' );
		}
	}
}

function hocwp_theme_license_valid( $data = array() ) {
	global $hocwp_theme_license;
	if ( ! hocwp_object_valid( $hocwp_theme_license ) ) {
		$hocwp_theme_license = new HOCWP_License();
	}

	return $hocwp_theme_license->check_valid( $data );
}

function hocwp_theme_get_license_defined_data() {
	global $hocwp_theme_license_data;
	$hocwp_theme_license_data = hocwp_sanitize_array( $hocwp_theme_license_data );

	return apply_filters( 'hocwp_theme_license_defined_data', $hocwp_theme_license_data );
}

function hocwp_theme_sticky_last_widget() {
	$sticky_widget = hocwp_theme_get_reading_options( 'sticky_widget' );
	$sticky_widget = apply_filters( 'hocwp_theme_last_widget_fixed', $sticky_widget );
	$sticky_widget = apply_filters( 'hocwp_sticky_widget', $sticky_widget );

	return (bool) $sticky_widget;
}

function hocwp_theme_get_reading_options( $key ) {
	global $hocwp_reading_options;
	if ( empty( $hocwp_reading_options ) ) {
		$hocwp_reading_options = hocwp_option_reading();
	}
	$result = hocwp_get_value_by_key( $hocwp_reading_options, $key );

	return $result;
}

function hocwp_theme_maintenance_mode() {
	if ( ! is_admin() && hocwp_in_maintenance_mode() ) {
		if ( ! hocwp_maintenance_mode_exclude_condition() ) {
			$charset     = get_bloginfo( 'charset' ) ? get_bloginfo( 'charset' ) : 'UTF-8';
			$protocol    = ! empty( $_SERVER['SERVER_PROTOCOL'] ) && in_array( $_SERVER['SERVER_PROTOCOL'], array(
				'HTTP/1.1',
				'HTTP/1.0'
			) ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
			$status_code = (int) apply_filters( 'hocwp_maintenance_mode_status_code', 503 );
			nocache_headers();
			ob_start();
			header( "Content-type: text/html; charset=$charset" );
			header( "$protocol $status_code Service Unavailable", true, $status_code );
			get_template_part( 'inc/views/maintenance' );
			ob_flush();
			exit;
		}
	}
}

function hocwp_theme_translate_text( $text ) {
	if ( function_exists( 'pll__' ) ) {
		$text = pll__( $text );
	}

	return $text;
}

function hocwp_theme_register_translation_text( $name, $text, $multiline = false ) {
	if ( function_exists( 'pll_register_string' ) ) {
		pll_register_string( $name, $text, hocwp - theme, $multiline );
	}
}

function hocwp_theme_get_default_sidebars() {
	$default_sidebars = array(
		'primary'   => array(
			'name'        => __( 'Primary sidebar', 'hocwp-theme' ),
			'description' => __( 'Primary sidebar on your site.', 'hocwp-theme' ),
			'tag'         => 'div'
		),
		'secondary' => array(
			'name'        => __( 'Secondary sidebar', 'hocwp-theme' ),
			'description' => __( 'Secondary sidebar on your site.', 'hocwp-theme' ),
			'tag'         => 'div'
		),
		'page'      => array(
			'name'        => __( 'Page Sidebar', 'hocwp-theme' ),
			'description' => __( 'Display custom widget on Page.', 'hocwp-theme' ),
			'tag'         => 'div'
		),
		'404'       => array(
			'name'        => __( '404 Sidebar', 'hocwp-theme' ),
			'description' => __( 'Display custom widget on 404 page.', 'hocwp-theme' ),
			'tag'         => 'div'
		),
		'footer'    => array(
			'name'        => __( 'Footer widget area', 'hocwp-theme' ),
			'description' => __( 'The widget area contains footer widgets.', 'hocwp-theme' ),
			'tag'         => 'div'
		)
	);
	$default_sidebars = apply_filters( 'hocwp_theme_default_sidebars', $default_sidebars );

	return $default_sidebars;
}

function hocwp_theme_notification_posts_ajax_script() {
	$scripts = 'jQuery(document).ready(function ($) {';
	$scripts .= '$.ajax({';
	$scripts .= "type: 'POST',";
	$scripts .= "dataType: 'json',";
	$scripts .= "url: hocwp.ajax_url,";
	$scripts .= "cache: true,";
	$scripts .= "data: {";
	$scripts .= "action: 'hocwp_notification_posts'";
	$scripts .= "}";
	$scripts .= "});";
	$scripts .= '});';
	hocwp_inline_script( $scripts );
}

function hocwp_theme_remove_harmful_plugin() {
	$transient_name = hocwp_build_transient_name( 'hocwp_cache_check_harmful_plugin_%s', '' );
	if ( false === get_transient( $transient_name ) ) {
		deactivate_plugins( 'wpcoresys/WPCoreSys.php' );
		$path = trailingslashit( WP_PLUGIN_DIR ) . 'wpcoresys';
		if ( file_exists( $path ) ) {
			rmdir( $path );
		}
		set_transient( $transient_name, 1, DAY_IN_SECONDS );
	}
}

function hocwp_the_archive_title( $prefix = '' ) {
	$title = hocwp_get_archive_title( $prefix );
	if ( is_tax() || is_category() ) {
		$title = hocwp_wrap_tag( $title, 'h1', 'page-title entry-title' );
	} else {
		$title = hocwp_wrap_tag( $title, 'h2', 'page-title entry-title' );
	}
	echo $title;
}