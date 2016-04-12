<?php
if(!function_exists('add_filter')) exit;

$lang = hocwp_get_language();

remove_action('wp_head', 'wp_generator');

global $hocwp_theme_license, $pagenow;

function hocwp_theme_switched($new_name, $new_theme) {
    if(!current_user_can('switch_themes')) {
        return;
    }
    flush_rewrite_rules();
    do_action('hocwp_theme_deactivation');
}
add_action('switch_theme', 'hocwp_theme_switched', 10, 2);

function hocwp_theme_after_switch($old_name, $old_theme) {
    if(!current_user_can('switch_themes')) {
        return;
    }
    update_option('hocwp_version', HOCWP_VERSION);
    if(hocwp_is_debugging() || hocwp_is_localhost()) {
        hocwp_update_permalink_struct('/%category%/%postname%.html');
    }
    flush_rewrite_rules();
    do_action('hocwp_theme_activation');
}
add_action('after_switch_theme', 'hocwp_theme_after_switch', 10, 2);

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
    $classes[] = 'front-end';
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
    register_widget('HOCWP_Widget_Post');
    register_widget('HOCWP_Widget_Top_Commenter');
    register_widget('HOCWP_Widget_Icon');
    register_widget('HOCWP_Widget_FeedBurner');
    register_widget('HOCWP_Widget_Subscribe');
    register_widget('HOCWP_Widget_Social');
    register_widget('HOCWP_Widget_Term');
    register_widget('HOCWP_Widget_Tabber');
    $default_sidebars = hocwp_theme_get_default_sidebars();
    foreach($default_sidebars as $name => $data) {
        $query = hocwp_get_post_by_meta('sidebar_id', $name, array('post_type' => 'hocwp_sidebar'));
        $active = true;
        if($query->have_posts()) {
            $current = current($query->posts);
            $active = (bool)hocwp_get_post_meta('active', $current->ID);
        }
        if($active) {
            hocwp_register_sidebar($name, $data['name'], $data['description'], $data['tag']);
        }
    }
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
    if(hocwp_use_jquery_cdn()) {
        hocwp_load_jquery_from_cdn();
    }
    hocwp_theme_register_lib_superfish();
    hocwp_theme_register_lib_bootstrap();
    hocwp_theme_register_lib_font_awesome();
    hocwp_theme_register_core_style_and_script();
    if(hocwp_theme_sticky_last_widget()) {
        hocwp_theme_register_lib_sticky();
    }
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
        wp_register_style('hocwp-custom-front-end-style', get_template_directory_uri() . '/css/hocwp-custom-front-end' . HOCWP_CSS_SUFFIX, array('bootstrap-style', 'font-awesome-style', 'superfish-style'), HOCWP_THEME_VERSION);
        wp_register_script('hocwp-custom-front-end', get_template_directory_uri() . '/js/hocwp-custom-front-end' . HOCWP_JS_SUFFIX, array('superfish', 'bootstrap'), HOCWP_THEME_VERSION, true);
        wp_localize_script('hocwp-custom-front-end', 'hocwp', $localize_object);
    }
    if(!hocwp_in_maintenance_mode()) {
        wp_enqueue_style('hocwp-custom-front-end-style');
        wp_enqueue_script('hocwp-custom-front-end');
    }
    if(is_singular()) {
        $post_id = get_the_ID();
        if(comments_open($post_id) && (bool)get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }
}
add_action('wp_enqueue_scripts', 'hocwp_setup_theme_scripts');

function hocwp_setup_theme_login_scripts() {
    hocwp_theme_register_lib_bootstrap();
    hocwp_theme_register_core_style_and_script();
    wp_register_style('hocwp-login-style', get_template_directory_uri() . '/hocwp/css/hocwp-login' . HOCWP_CSS_SUFFIX, array('bootstrap-theme-style'), HOCWP_THEME_VERSION);
    wp_register_script('hocwp-login', get_template_directory_uri() . '/hocwp/js/hocwp-login' . HOCWP_JS_SUFFIX, array('jquery', 'hocwp'), HOCWP_THEME_VERSION, true);
    wp_localize_script('hocwp', 'hocwp', hocwp_theme_default_script_localize_object());
    wp_enqueue_style('hocwp-login-style');
    wp_enqueue_script('hocwp-login');
}
add_action('login_enqueue_scripts', 'hocwp_setup_theme_login_scripts');

function hocwp_setup_theme_admin_scripts() {
    hocwp_admin_enqueue_scripts();
    $jquery_ui_datetime_picker = apply_filters('hocwp_admin_jquery_datetime_picker', false);
    if((bool)$jquery_ui_datetime_picker) {
        hocwp_enqueue_jquery_ui_datepicker();
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

function hocwp_setup_theme_admin_footer() {
    global $pagenow;
    if('index.php' === $pagenow) {
        hocwp_dashboard_widget_script();
    }
}
add_action('admin_footer', 'hocwp_setup_theme_admin_footer');

function hocwp_setup_theme_update_footer($text) {
    $tmp = strtolower($text);
    if(hocwp_string_contain($tmp, 'version')) {
        $text = sprintf(__('Theme core version %s', 'hocwp'), HOCWP_THEME_CORE_VERSION);
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
    $current_admin_page = hocwp_get_current_admin_page();
    if('hocwp_theme_option' == $current_admin_page) {
        $admin_url = admin_url('admin.php');
        $admin_url = add_query_arg(array('page' => $current_admin_page), $admin_url);
        wp_redirect($admin_url);
        exit;
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
    $theme = wp_get_theme();
    hocwp_send_mail_invalid_license($theme->get('Name'));
}

function hocwp_setup_theme_invalid_license_admin_notice() {
    if(false !== ($result = get_transient('hocwp_invalid_theme_license')) && 1 == $result) {
        hocwp_setup_theme_invalid_license_message();
    }
}
add_action('admin_notices', 'hocwp_setup_theme_invalid_license_admin_notice');

function hocwp_setup_theme_admin_bar_menu($wp_admin_bar) {
    $args = array(
        'id' => 'theme-options',
        'title' => __('Theme Options', 'hocwp'),
        'href' => admin_url('admin.php?page=hocwp_theme_setting'),
        'parent' => 'site-name'
    );
    $wp_admin_bar->add_node($args);

    if(hocwp_wc_installed()) {
        $args = array(
            'id' => 'shop-settings',
            'title' => __('Shop Settings', 'hocwp'),
            'href' => admin_url('admin.php?page=wc-settings'),
            'parent' => 'site-name'
        );
        $wp_admin_bar->add_node($args);
    }

    $args = array(
        'id' => 'plugins',
        'title' => __('Plugins', 'hocwp'),
        'href' => admin_url('plugins.php'),
        'parent' => 'site-name'
    );
    $wp_admin_bar->add_node($args);

    if(hocwp_plugin_wpsupercache_installed()) {
        $args = array(
            'id' => 'wpsupercache-content',
            'title' => __('Delete cache', 'hocwp'),
            'href' => admin_url('options-general.php?page=wpsupercache&tab=contents#listfiles'),
            'parent' => 'site-name'
        );
        $wp_admin_bar->add_node($args);
    }
}
if(!is_admin() && current_user_can('create_users')) add_action('admin_bar_menu', 'hocwp_setup_theme_admin_bar_menu', 99);

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

function hocwp_setup_theme_wp_dashboard_setup() {
    wp_add_dashboard_widget('hocwp_useful_links', __('Useful Links', 'hocwp'), 'hocwp_theme_dashboard_widget_useful_links');
    wp_add_dashboard_widget('hocwp_services_news', __('Services Information', 'hocwp'), 'hocwp_theme_dashboard_widget_services_news');
    wp_add_dashboard_widget('hocwp_wordpress_release', __('WordPress Releases', 'hocwp'), 'hocwp_theme_dashboard_widget_wordpress_release');

    global $wp_meta_boxes;

    $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
    $backups = array(
        'hocwp_useful_links' => $normal_dashboard['hocwp_useful_links']
    );
    foreach($backups as $key => $widget) {
        unset($normal_dashboard[$key]);
    }
    $sorted_dashboard = array_merge($backups, $normal_dashboard);
    $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;

    $backup = $wp_meta_boxes['dashboard']['normal']['core']['hocwp_services_news'];
    unset($wp_meta_boxes['dashboard']['normal']['core']['hocwp_services_news']);
    $wp_meta_boxes['dashboard']['side']['core']['hocwp_services_news'] = $backup;
    $side_dashboard = $wp_meta_boxes['dashboard']['side']['core'];
    $backups = array(
        'hocwp_services_news' => $side_dashboard['hocwp_services_news']
    );
    foreach($backups as $key => $widget) {
        unset($side_dashboard[$key]);
    }
    $sorted_dashboard = array_merge($backups, $side_dashboard);
    $wp_meta_boxes['dashboard']['side']['core'] = $sorted_dashboard;

    $backup = $wp_meta_boxes['dashboard']['normal']['core']['hocwp_wordpress_release'];
    unset($wp_meta_boxes['dashboard']['normal']['core']['hocwp_wordpress_release']);
    $wp_meta_boxes['dashboard']['side']['core']['hocwp_wordpress_release'] = $backup;
}
if('vi' == $lang) add_action('wp_dashboard_setup', 'hocwp_setup_theme_wp_dashboard_setup');

function hocwp_theme_dashboard_widget_wordpress_release() {
    $feeds = array(
        'url' => 'http://wordpress.org/news/category/releases/feed/',
        'number' => 3
    );
    hocwp_dashboard_widget_cache('hocwp_wordpress_release', 'hocwp_dashboard_widget_rss_cache', $feeds);
}

function hocwp_theme_dashboard_widget_useful_links() {
    ?>
    <div class="rss-widget">
        <ul>
            <li>
                <a target="_blank" href="http://hocwp.net/donate/" class="rss-link">Thông tin chuyển khoản</a>
            </li>
            <li>
                <a target="_blank" href="http://hocwp.net/blog/wp-guides/" class="rss-link">Hướng dẫn tạo blog WordPress chi tiết</a>
            </li>
            <li>
                <a target="_blank" href="http://hocwp.net/thong-tin-dich-vu/" class="rss-link">Cập nhật các thông báo, thông tin dịch vụ</a>
            </li>
        </ul>
    </div>
    <?php
}

function hocwp_theme_dashboard_widget_services_news() {
    hocwp_dashboard_widget_cache('hocwp_services_news', 'hocwp_dashboard_widget_rss_cache', trailingslashit(HOCWP_HOMEPAGE) . 'home/feed/');
}

function hocwp_setup_theme_dashboard_primary_link() {
    return trailingslashit(HOCWP_HOMEPAGE) . 'blog/';
}
if('vi' == $lang) add_filter('dashboard_primary_link', 'hocwp_setup_theme_dashboard_primary_link');

function hocwp_setup_theme_dashboard_secondary_link() {
    return trailingslashit(HOCWP_HOMEPAGE);
}
if('vi' == $lang) add_filter('dashboard_secondary_link', 'hocwp_setup_theme_dashboard_secondary_link');

function hocwp_setup_theme_dashboard_primary_feed() {
    return trailingslashit(HOCWP_HOMEPAGE) . 'feed/';
}
if('vi' == $lang) add_filter('dashboard_primary_feed', 'hocwp_setup_theme_dashboard_primary_feed');

function hocwp_setup_theme_dashboard_secondary_feed() {
    return trailingslashit(HOCWP_HOMEPAGE) . 'blog/feed/';
}
if('vi' == $lang) add_filter('dashboard_secondary_feed', 'hocwp_setup_theme_dashboard_secondary_feed');

function hocwp_setup_theme_dashboard_primary_title() {
    return __('Services News', 'hocwp');
}
if('vi' == $lang) add_filter('dashboard_primary_title', 'hocwp_setup_theme_dashboard_primary_title', 1);
if('vi' == $lang) add_filter('dashboard_secondary_title', 'hocwp_setup_theme_dashboard_primary_title');

function hocwp_setup_theme_dashboard_secondary_items() {
    return 5;
}
if('vi' == $lang) add_filter('dashboard_secondary_items', 'hocwp_setup_theme_dashboard_secondary_items');

function hocwp_theme_on_upgrade() {
    $version = get_option('hocwp_version');
    if(version_compare($version, HOCWP_VERSION, '<')) {
        update_option('hocwp_version', HOCWP_VERSION);
        do_action('hocwp_theme_upgrade');
    }
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
}
add_action('admin_init', 'hocwp_theme_on_upgrade');

function hocwp_theme_update_rewrite_rules() {
    flush_rewrite_rules();
}
add_action('hocwp_theme_upgrade', 'hocwp_theme_update_rewrite_rules');
add_action('hocwp_theme_activation', 'hocwp_theme_update_rewrite_rules');
add_action('hocwp_change_domain', 'hocwp_theme_update_rewrite_rules');

function hocwp_setup_theme_esc_comment_author_url($commentdata) {
    $comment_author_url = hocwp_get_value_by_key($commentdata, 'comment_author_url');
    if(!empty($comment_author_url)) {
        $commentdata['comment_author_url'] = esc_url(hocwp_get_root_domain_name($comment_author_url));
    }
    return $commentdata;
}
add_filter('preprocess_comment', 'hocwp_setup_theme_esc_comment_author_url');

function hocwp_setup_theme_admin_menu() {
    global $submenu;
    unset($submenu['themes.php'][6]);
    remove_submenu_page('hocwp_theme_option', 'hocwp_theme_option');
}
add_action('admin_menu', 'hocwp_setup_theme_admin_menu', 99);

add_filter('hocwp_allow_user_subscribe', '__return_true');

function hocwp_setup_theme_new_post_type_and_taxonomy() {
    $args = array(
        'name' => 'Sidebars',
        'singular_name' => 'Sidebar',
        'slug' => 'hocwp_sidebar'
    );
    hocwp_register_post_type_private($args);

    $user_subscribe = apply_filters('hocwp_allow_user_subscribe', false);
    if($user_subscribe) {
        $args = array(
            'name' => 'Subscribers',
            'singular_name' => 'Subscriber',
            'slug' => 'hocwp_subscriber'
        );
        hocwp_register_post_type_private($args);
    }
}
add_action('init', 'hocwp_setup_theme_new_post_type_and_taxonomy', 0);

function hocwp_setup_theme_the_content($content) {
    $add_to_content = apply_filters('hocwp_add_to_the_content', false);
    if($add_to_content) {
        $backup = $content;
        $delimiter = '</p>';
        $paragrahps = explode($delimiter, $content);
        $count = 1;
        $custom = false;
        if(hocwp_array_has_value($paragrahps)) {
            $paragrahps = hocwp_sanitize_array($paragrahps);
            $count_p = count($paragrahps);
            foreach($paragrahps as $key => $value) {
                if(trim($value)) {
                    $paragrahps[$key] .= $delimiter;
                    if(1 == $count) {
                        $add = apply_filters('hocwp_add_to_the_content_after_first_paragraph', '');
                        if(!empty($add)) {
                            $paragrahps[$key] .= $add;
                            $custom = true;
                        }
                    } elseif(2 == $count) {
                        $add = apply_filters('hocwp_add_to_the_content_after_second_paragraph', '');
                        if(!empty($add)) {
                            $paragrahps[$key] .= $add;
                            $custom = true;
                        }
                    }
                    if($count == ($count_p - 1)) {
                        $add = apply_filters('hocwp_add_to_the_content_before_last_paragraph', '');
                        if(!empty($add)) {
                            $paragrahps[$key] .= $add;
                            $custom = true;
                        }
                    }
                    $count++;
                } else {
                    $count_p--;
                }
            }
            if($custom) {
                $content = implode('', $paragrahps);
            }
            $content = apply_filters('hocwp_custom_the_content', $content, $backup);
        }
    }
    return $content;
}
add_filter('the_content', 'hocwp_setup_theme_the_content');

function hocwp_setup_theme_add_default_sidebars() {
    $added = (bool)get_option('hocwp_default_sidebars_added');
    if($added) {
        $query = hocwp_query(array('post_type' => 'hocwp_sidebar', 'posts_per_page' => -1));
        if(!$query->have_posts()) {
            $added = false;
        }
    }
    if(!$added) {
        $sidebars = hocwp_theme_get_default_sidebars();
        foreach($sidebars as $name => $data) {
            $post_data = array(
                'post_title' => $data['name'],
                'post_type' => 'hocwp_sidebar',
                'post_status' => 'publish'
            );
            $post_id = hocwp_insert_post($post_data);
            if(hocwp_id_number_valid($post_id)) {
                update_post_meta($post_id, 'sidebar_default', 1);
                update_post_meta($post_id, 'sidebar_id', $name);
                update_post_meta($post_id, 'sidebar_name', $data['name']);
                update_post_meta($post_id, 'sidebar_description', $data['description']);
                update_post_meta($post_id, 'sidebar_tag', $data['tag']);
                update_post_meta($post_id, 'active', 1);
            }
        }
        update_option('hocwp_default_sidebars_added', 1);
    }
}
add_action('admin_init', 'hocwp_setup_theme_add_default_sidebars');

function hocwp_setup_theme_prevent_delete_sidebar($post_id) {
    $post = get_post($post_id);
    if('hocwp_sidebar' == $post->post_type) {
        $prevent = false;
        if(!is_super_admin()) {
            $prevent = true;
        }
        $default = (bool)get_post_meta($post_id, 'sidebar_default');
        if($default) {
            $prevent = true;
        }
        if($prevent) {
            exit(__('You don\'t have permission to delete this post!', 'hocwp'));
        }
    }
}
add_action('wp_trash_post', 'hocwp_setup_theme_prevent_delete_sidebar', 10, 1);
add_action('before_delete_post', 'hocwp_setup_theme_prevent_delete_sidebar', 10, 1);

if('post.php' == $pagenow || 'post-new.php' == $pagenow) {
    $readonly = false;
    $post_id = 0;
    if('post.php' == $pagenow) {
        $post_id = hocwp_get_value_by_key($_REQUEST, 'post');
        $default = (bool)get_post_meta($post_id, 'sidebar_default');
        if($default) {
            $readonly = true;
        }
    }
    $meta = new HOCWP_Meta('post');
    $meta->set_id('hocwp_sidebar_information');
    $meta->set_title(__('Sidebar Information', 'hocwp'));
    $meta->add_post_type('hocwp_sidebar');
    $field_args = array(
        'id' => 'sidebar_id',
        'label' => __('Sidebar ID:', 'hocwp')
    );
    if($readonly) {
        $field_args['readonly'] = true;
    }
    $meta->add_field($field_args);
    $field_args = array(
        'id' => 'sidebar_name',
        'label' => __('Sidebar name:', 'hocwp')
    );
    if($readonly) {
        $field_args['readonly'] = true;
    }
    $meta->add_field($field_args);
    $field_args = array(
        'id' => 'sidebar_description',
        'label' => __('Sidebar description:', 'hocwp')
    );
    if($readonly) {
        $field_args['readonly'] = true;
    }
    $meta->add_field($field_args);
    $field_args = array(
        'id' => 'sidebar_tag',
        'label' => __('Sidebar tag:', 'hocwp')
    );
    if($readonly) {
        $field_args['readonly'] = true;
    }
    $meta->add_field($field_args);
    $meta->init();

    $meta = new HOCWP_Meta('post');
    $meta->set_id('hocwp_subscriber_information');
    $meta->set_title(__('Subscriber Information', 'hocwp'));
    $meta->add_post_type('hocwp_subscriber');
    $field_args = array(
        'id' => 'subscriber_email',
        'label' => __('Email:', 'hocwp'),
        'readonly' => true
    );
    $meta->add_field($field_args);
    $field_args = array(
        'id' => 'subscriber_name',
        'label' => __('Name:', 'hocwp')
    );
    $meta->add_field($field_args);
    $field_args = array(
        'id' => 'subscriber_phone',
        'label' => __('Phone:', 'hocwp')
    );
    $meta->add_field($field_args);
    if(hocwp_id_number_valid($post_id)) {
        $field_args = array(
            'id' => 'subscriber_user',
            'label' => __('User ID:', 'hocwp'),
            'readonly' => true
        );
        $meta->add_field($field_args);
    }
    $meta->init();
}