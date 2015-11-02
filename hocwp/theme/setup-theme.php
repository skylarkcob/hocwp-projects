<?php
global $hocwp_theme_license;

function hocwp_theme_after_switch() {
    if(!current_user_can('switch_themes')) {
        return;
    }
    update_option('hocwp_version', HOCWP_VERSION);
    if(hocwp_is_debugging() || hocwp_is_localhost()) {
        hocwp_update_permalink_struct('/%category%/%postname%.html');
    }
    do_action('hocwp_theme_activation');
}
add_action('after_switch_theme', 'hocwp_theme_after_switch');

function hocwp_setup_theme_data() {
    load_theme_textdomain('hocwp', get_template_directory() . '/languages');
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus(
        array(
            'top' => __('Top menu', 'hocwp'),
            'primary'   => __('Primary menu', 'hocwp'),
            'secondary' => __('Secondary menu', 'hocwp'),
            'mobile' => __('Mobile menu', 'hocwp'),
            'footer' => __('Footer menu', 'hocwp')
        )
    );
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
}
add_action('after_setup_theme', 'hocwp_setup_theme_data');

function hocwp_theme_hide_admin_bar() {
    if(!current_user_can('read')) {
        show_admin_bar(false);
    }
}
add_action('init', 'hocwp_theme_hide_admin_bar');

function hocwp_setup_theme_body_class($classes) {
    if(is_single() || is_page() || is_singular()) {
        $classes[] = 'hocwp-single';
    }
    $classes[] = hocwp_get_browser();
    if(!hocwp_theme_license_valid(hocwp_theme_get_license_defined_data())) {
        $classes[] = 'hocwp-invalid-license';
    }
    if(is_user_logged_in()) {
        $classes[] = 'hocwp-user';
        global $current_user;
        if(hocwp_is_admin($current_user)) {
            $classes[] = 'hocwp-user-admin';
        }
    }
    return $classes;
}
add_filter('body_class', 'hocwp_setup_theme_body_class');

function hocwp_setup_theme_content_width() {
    $GLOBALS['content_width'] = apply_filters('hocwp_content_width', 640);
}
add_action('after_setup_theme', 'hocwp_setup_theme_content_width', 0);

function hocwp_setup_theme_widgets_init() {
    register_widget('HOCWP_Widget_Banner');
    register_widget('HOCWP_Widget_Facebook_Box');
    hocwp_register_sidebar('primary', __('Primary sidebar', 'hocwp'), __('Primary sidebar on your site.', 'hocwp'));
    hocwp_register_sidebar('secondary', __('Secondary sidebar', 'hocwp'), __('Secondary sidebar on your site.', 'hocwp'));
    hocwp_register_sidebar('footer', __('Footer widget area', 'hocwp'), __('The widget area contains footer widgets.', 'hocwp'));
}
add_action('widgets_init', 'hocwp_setup_theme_widgets_init');

function hocwp_setup_theme_load_style_and_script($use) {
    global $pagenow;
    $current_page = hocwp_get_current_admin_page();
    if('widgets.php' == $pagenow || 'post.php' == $pagenow || 'options-writing.php' == $pagenow || 'options-reading.php' == $pagenow) {
        $use = true;
    }
    return $use;
}
add_filter('hocwp_use_admin_style_and_script', 'hocwp_setup_theme_load_style_and_script');

function hocwp_setup_theme_support_enqueue_media($use) {
    global $pagenow;
    $current_page = hocwp_get_current_admin_page();
    if('widgets.php' == $pagenow || 'options-writing.php' == $pagenow || 'options-reading.php' == $pagenow) {
        $use = true;
    }
    return $use;
}
add_filter('hocwp_wp_enqueue_media', 'hocwp_setup_theme_support_enqueue_media');

function hocwp_setup_theme_scripts() {
    hocwp_theme_register_lib_superfish();
    hocwp_theme_register_lib_bootstrap();
    hocwp_theme_register_lib_font_awesome();
    hocwp_theme_register_core_style_and_script();
    $localize_object = array(
        'expand' => '<span class="screen-reader-text">' . esc_html__('expand child menu', 'hocwp') . '</span>',
        'collapse' => '<span class="screen-reader-text">' . esc_html__('collapse child menu', 'hocwp') . '</span>'
    );
    $localize_object = wp_parse_args($localize_object, hocwp_theme_default_script_localize_object());
    if(hocwp_is_debugging()) {
        wp_localize_script('hocwp', 'hocwp', $localize_object);
        wp_register_style('hocwp-front-end-style', get_template_directory_uri() . '/hocwp/css/hocwp-front-end' . HOCWP_CSS_SUFFIX, array('hocwp-style'));
        wp_register_script('hocwp-front-end', get_template_directory_uri() . '/hocwp/js/hocwp-front-end' . HOCWP_JS_SUFFIX, array('hocwp'), false, true);
        wp_register_style('hocwp-custom-front-end-style', get_template_directory_uri() . '/css/hocwp-custom-front-end' . HOCWP_CSS_SUFFIX, array('bootstrap-style', 'font-awesome-style', 'superfish-style', 'hocwp-front-end-style'));
        wp_register_script('hocwp-custom-front-end', get_template_directory_uri() . '/js/hocwp-custom-front-end' . HOCWP_JS_SUFFIX, array('superfish', 'bootstrap', 'hocwp-front-end'), false, true);
    } else {
        wp_register_style('hocwp-custom-front-end-style', get_template_directory_uri() . '/css/hocwp-custom-front-end' . HOCWP_CSS_SUFFIX, array('bootstrap-style', 'font-awesome-style', 'superfish-style'));
        wp_register_script('hocwp-custom-front-end', get_template_directory_uri() . '/js/hocwp-custom-front-end' . HOCWP_JS_SUFFIX, array('superfish', 'bootstrap'), false, true);
        wp_localize_script('hocwp-custom-front-end', 'hocwp', $localize_object);
    }
    wp_enqueue_style('hocwp-custom-front-end-style');
    wp_enqueue_script('hocwp-custom-front-end');
    if(is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'hocwp_setup_theme_scripts');

function hocwp_setup_theme_login_scripts() {
    hocwp_theme_register_lib_bootstrap();
    hocwp_theme_register_core_style_and_script();
    wp_register_style('hocwp-login-style', get_template_directory_uri() . '/hocwp/css/hocwp-login' . HOCWP_CSS_SUFFIX, array('bootstrap-theme-style'));
    wp_register_script('hocwp-login', get_template_directory_uri() . '/hocwp/js/hocwp-login' . HOCWP_JS_SUFFIX, array('jquery', 'hocwp'), false, true);
    wp_localize_script('hocwp', 'hocwp', hocwp_theme_default_script_localize_object());
    wp_enqueue_style('hocwp-login-style');
    wp_enqueue_script('hocwp-login');
}
add_action('login_enqueue_scripts', 'hocwp_setup_theme_login_scripts');

function hocwp_setup_theme_admin_scripts() {
    global $pagenow;
    $current_page = hocwp_get_current_admin_page();
    $use = apply_filters('hocwp_use_jquery_ui', false);
    if($use || ('themes.php' == $pagenow && 'hocwp_theme_setting' == $current_page)) {
        wp_enqueue_script('jquery-ui-core');
    }
    $wp_enqueue_media = apply_filters('hocwp_wp_enqueue_media', false);
    if($wp_enqueue_media) {
        wp_enqueue_media();
    }
    hocwp_theme_register_core_style_and_script();
    wp_register_style('hocwp-admin-style', get_template_directory_uri() . '/hocwp/css/hocwp-admin'. HOCWP_CSS_SUFFIX, array('hocwp-style'));
    wp_register_script('hocwp-admin', get_template_directory_uri() . '/hocwp/js/hocwp-admin' . HOCWP_JS_SUFFIX, array('jquery', 'hocwp'), false, true);
    wp_localize_script('hocwp', 'hocwp', hocwp_theme_default_script_localize_object());
    $use = apply_filters('hocwp_use_admin_style_and_script', false);
    if($use) {
        wp_enqueue_style('hocwp-admin-style');
        wp_enqueue_script('hocwp-admin');
    }
}
add_action('admin_enqueue_scripts', 'hocwp_setup_theme_admin_scripts');

function hocwp_setup_theme_check_javascript_supported() {
    echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action('wp_head', 'hocwp_setup_theme_check_javascript_supported', 99);

function hocwp_setup_theme_admin_footer_text($text) {
    $text = sprintf(__('Thank you for creating with %s. Proudly powered by WordPress.'), '<a href="' . HOCWP_HOMEPAGE . '">hocwp</a>');
    return '<span id="footer-thankyou">' . $text . '</span>';
}
add_filter('admin_footer_text', 'hocwp_setup_theme_admin_footer_text', 99);

function hocwp_setup_theme_update_footer($text) {
    $tmp = strtolower($text);
    if(hocwp_string_contain($tmp, 'version')) {
        $text = sprintf(__('Theme core version %s', 'hocwp'), HOCWP_THEME_VERSION);
    }
    return $text;
}
add_filter('update_footer', 'hocwp_setup_theme_update_footer', 99);

function hocwp_setup_theme_remove_editor_menu() {
    $remove = apply_filters('hocwp_remove_theme_editor_menu', true);
    if($remove) {
        $current_page = isset($GLOBALS['pagenow']) ? $GLOBALS['pagenow'] : '';
        if('theme-editor.php' == $current_page) {
            wp_redirect(admin_url('/'));
            exit;
        }
        remove_submenu_page('themes.php', 'theme-editor.php');
    }
}
add_action('admin_init', 'hocwp_setup_theme_remove_editor_menu');

function hocwp_setup_theme_login_headerurl() {
    $url = home_url('/');
    $url = apply_filters('hocwp_login_logo_url', $url);
    return $url;
}
add_filter('login_headerurl', 'hocwp_setup_theme_login_headerurl');

function hocwp_setup_theme_login_headertitle() {
    $desc = get_bloginfo('description');
    $desc = apply_filters('hocwp_login_logo_description', $desc);
    return $desc;
}
add_filter('login_headertitle', 'hocwp_setup_theme_login_headertitle');

function hocwp_setup_theme_check_license() {
    if(!isset($_POST['submit']) && !hocwp_is_login_page()) {
        if(!hocwp_theme_license_valid(hocwp_theme_get_license_defined_data()) || !has_action('hocwp_check_license', 'hocwp_theme_custom_check_license')) {
            hocwp_theme_invalid_license_redirect();
        }
    }
}
add_action('hocwp_check_license', 'hocwp_setup_theme_check_license');

function hocwp_setup_theme_invalid_license_message() {
    delete_transient('hocwp_invalid_theme_license');
    $args = array(
        'error' => true,
        'title' => __('Error', 'hocwp'),
        'text' => sprintf(__('Your theme is using an invalid license key! If you does not have one, please contact %1$s via email address %2$s for more information.', 'hocwp'), '<strong>' . HOCWP_NAME . '</strong>', '<a href="mailto:' . esc_attr(HOCWP_EMAIL) . '">' . HOCWP_EMAIL . '</a>')
    );
    hocwp_admin_notice($args);
}

function hocwp_setup_theme_invalid_license_admin_notice() {
    if(false !== ($result = get_transient('hocwp_invalid_theme_license')) && 1 == $result) {
        hocwp_setup_theme_invalid_license_message();
    }
}
add_action('admin_notices', 'hocwp_setup_theme_invalid_license_admin_notice');

function hocwp_setup_theme_admin_bar_menu($wp_admin_bar) {
    $option = hocwp_option_get_object_from_list('theme_setting');
    if(hocwp_object_valid($option) && current_user_can($option->get_capability())) {
        $args = array(
            'id' => hocwp_sanitize_id($option->get_menu_slug()),
            'title' => $option->get_menu_title(),
            'href' => $option->get_page_url(),
            'parent' => 'themes'
        );
        $wp_admin_bar->add_node($args);
    }
    $option = hocwp_option_get_object_from_list('theme_license');
    if(hocwp_object_valid($option) && current_user_can($option->get_capability())) {
        $args = array(
            'id' => hocwp_sanitize_id($option->get_menu_slug()),
            'title' => $option->get_menu_title(),
            'href' => $option->get_page_url(),
            'parent' => 'themes'
        );
        $wp_admin_bar->add_node($args);
    }
    $args = array(
        'id' => 'users',
        'title' => __('Users', 'hocwp'),
        'href' => admin_url('users.php'),
        'parent' => 'site-name'
    );
    $wp_admin_bar->add_node($args);
    $option = hocwp_option_get_object_from_list('user_login');
    if(hocwp_object_valid($option) && current_user_can($option->get_capability())) {
        $args = array(
            'id' => hocwp_sanitize_id($option->get_menu_slug()),
            'title' => $option->get_menu_title(),
            'href' => $option->get_page_url(),
            'parent' => 'users'
        );
        $wp_admin_bar->add_node($args);
    }
    $args = array(
        'id' => 'options-general',
        'title' => __('Settings', 'hocwp'),
        'href' => admin_url('options-general.php'),
        'parent' => 'site-name'
    );
    $wp_admin_bar->add_node($args);
    $option = hocwp_option_get_object_from_list('option_social');
    if(hocwp_object_valid($option) && current_user_can($option->get_capability())) {
        $args = array(
            'id' => hocwp_sanitize_id($option->get_menu_slug()),
            'title' => $option->get_menu_title(),
            'href' => $option->get_page_url(),
            'parent' => 'options-general'
        );
        $wp_admin_bar->add_node($args);
    }
    $option = hocwp_option_get_object_from_list('option_smtp_email');
    if(hocwp_object_valid($option) && current_user_can($option->get_capability())) {
        $args = array(
            'id' => hocwp_sanitize_id($option->get_menu_slug()),
            'title' => $option->get_menu_title(),
            'href' => $option->get_page_url(),
            'parent' => 'options-general'
        );
        $wp_admin_bar->add_node($args);
    }
    $args = array(
        'id' => 'options-writing',
        'title' => __('Writing', 'hocwp'),
        'href' => admin_url('options-writing.php'),
        'parent' => 'options-general'
    );
    $wp_admin_bar->add_node($args);
    $args = array(
        'id' => 'options-reading',
        'title' => __('Reading', 'hocwp'),
        'href' => admin_url('options-reading.php'),
        'parent' => 'options-general'
    );
    $wp_admin_bar->add_node($args);
    $args = array(
        'id' => 'options-discussion',
        'title' => __('Discussion', 'hocwp'),
        'href' => admin_url('options-discussion.php'),
        'parent' => 'options-general'
    );
    $wp_admin_bar->add_node($args);
    $args = array(
        'id' => 'options-permalink',
        'title' => __('Permalinks', 'hocwp'),
        'href' => admin_url('options-permalink.php'),
        'parent' => 'options-general'
    );
    $wp_admin_bar->add_node($args);
    $args = array(
        'id' => 'list-posts',
        'title' => __('Posts', 'hocwp'),
        'href' => admin_url('edit.php'),
        'parent' => 'site-name'
    );
    $wp_admin_bar->add_node($args);
    $args = array(
        'id' => 'plugins',
        'title' => __('Plugins', 'hocwp'),
        'href' => admin_url('plugins.php'),
        'parent' => 'site-name'
    );
    $wp_admin_bar->add_node($args);
}
if(!is_admin()) add_action('admin_bar_menu', 'hocwp_setup_theme_admin_bar_menu');

function hocwp_setup_theme_language_attributes($output) {
    if(!is_admin()) {
        if('vi' == hocwp_get_language()) {
            $output = 'lang="vi"';
        }
    }
    return $output;
}
add_filter('language_attributes', 'hocwp_setup_theme_language_attributes');

function hocwp_setup_theme_wpseo_locale($locale) {
    if(!is_admin()) {
        if('vi' == hocwp_get_language()) {
            $locale = 'vi';
        }
    }
    return $locale;
}
add_filter('wpseo_locale', 'hocwp_setup_theme_wpseo_locale');

function hocwp_setup_theme_wpseo_meta_box_priority() {
    return 'low';
}
add_filter('wpseo_metabox_prio', 'hocwp_setup_theme_wpseo_meta_box_priority');

function hocwp_setup_theme_default_hidden_meta_boxes($hidden, $screen) {
    if('post' == $screen->base) {
        $defaults = array('slugdiv', 'trackbacksdiv', 'postcustom', 'postexcerpt', 'commentstatusdiv', 'commentsdiv', 'authordiv', 'revisionsdiv');
        $hidden = wp_parse_args($hidden, $defaults);
    }
    return $hidden;
}
add_filter('default_hidden_meta_boxes', 'hocwp_setup_theme_default_hidden_meta_boxes', 10, 2);

function hocwp_theme_pre_ping(&$links) {
    $home = get_option('home');
    foreach($links as $l => $link) {
        if(0 === strpos($link, $home)) {
            unset($links[$l]);
        }
    }
}
add_action('pre_ping', 'hocwp_theme_pre_ping');

function hocwp_theme_intermediate_image_sizes_advanced($sizes) {
    if(isset($sizes['thumbnail'])) {
        unset($sizes['thumbnail']);
    }
    if(isset($sizes['medium'])) {
        unset($sizes['medium']);
    }
    if(isset($sizes['large'])) {
        unset($sizes['large']);
    }
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'hocwp_theme_intermediate_image_sizes_advanced');

function hocwp_theme_on_upgrade() {
    $version = get_option('hocwp_version');
    if(version_compare($version, HOCWP_VERSION, '<')) {
        update_option('hocwp_version', HOCWP_VERSION);
        do_action('hocwp_theme_upgrade');
    }
}
add_action('admin_init', 'hocwp_theme_on_upgrade');

function hocwp_theme_update_rewrite_rules() {
    flush_rewrite_rules();
}
add_action('hocwp_theme_upgrade', 'hocwp_theme_update_rewrite_rules');
add_action('hocwp_theme_activation', 'hocwp_theme_update_rewrite_rules');