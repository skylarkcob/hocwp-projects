<?php
add_action('init', 'hocwp_session_start');

function hocwp_dat_ve_tau_admin_bar_menu($wp_admin_bar) {
    $args = array(
        'id' => 'theme-license',
        'title' => __('Plugin Licenses', 'hocwp-theme-switcher'),
        'href' => admin_url('plugins.php?page=hocwp_plugin_license'),
        'parent' => 'plugins'
    );
    $wp_admin_bar->add_node($args);
}
if(!is_admin()) add_action('admin_bar_menu', 'hocwp_dat_ve_tau_admin_bar_menu', 99);

function hocwp_dat_ve_tau_check_license() {
    if(!isset($_POST['submit']) && !hocwp_is_login_page()) {
        if(!hocwp_dat_ve_tau_license_valid()) {
            if(!is_admin() && current_user_can('manage_options')) {
                wp_redirect(admin_url('plugins.php?page=hocwp_plugin_license'));
                exit;
            }
            add_action('admin_notices', 'hocwp_dat_ve_tau_invalid_license_notice');
        }
    }
}
add_action('hocwp_check_license', 'hocwp_dat_ve_tau_check_license');

function hocwp_dat_ve_tau_invalid_license_notice() {
    $plugin_name = hocwp_get_plugin_info(HOCWP_DAT_VE_TAU_FILE, HOCWP_DAT_VE_TAU_BASENAME);
    $plugin_name = hocwp_wrap_tag($plugin_name, 'strong');
    $args = array(
        'error' => true,
        'title' => __('Error', 'hocwp-dat-ve-tau'),
        'text' => sprintf(__('Plugin %1$s is using an invalid license key! If you does not have one, please contact %2$s via email address %3$s for more information.', 'hocwp-dat-ve-tau'), $plugin_name, '<strong>' . HOCWP_NAME . '</strong>', '<a href="mailto:' . esc_attr(HOCWP_EMAIL) . '">' . HOCWP_EMAIL . '</a>')
    );
    hocwp_admin_notice($args);
}

function hocwp_dat_ve_tau_enqueue_scripts() {
    hocwp_enqueue_jquery_ui_style();
    hocwp_enqueue_jquery_ui_datepicker();
    hocwp_register_core_style_and_script();
    $localize_object = hocwp_default_script_localize_object();
    if(hocwp_is_debugging()) {
        wp_localize_script('hocwp', 'hocwp', $localize_object);
        wp_register_script('hocwp-front-end', HOCWP_URL . '/js/hocwp-front-end' . HOCWP_JS_SUFFIX, array('hocwp'), false, true);
        wp_register_script('hocwp-dat-ve-tau', HOCWP_DAT_VE_TAU_URL . '/js/hocwp-plugin' . HOCWP_JS_SUFFIX, array('hocwp-front-end'), false, true);
    } else {
        wp_register_script('hocwp-dat-ve-tau', HOCWP_DAT_VE_TAU_URL . '/js/hocwp-plugin' . HOCWP_JS_SUFFIX, array(), false, true);
        wp_localize_script('hocwp-dat-ve-tau', 'hocwp', $localize_object);
    }
    wp_register_style('hocwp-dat-ve-tau-style', HOCWP_DAT_VE_TAU_URL . '/css/hocwp-plugin' . HOCWP_CSS_SUFFIX);
    wp_enqueue_style('hocwp-dat-ve-tau-style');
    wp_enqueue_script('hocwp-dat-ve-tau');
}
add_action('wp_enqueue_scripts', 'hocwp_dat_ve_tau_enqueue_scripts');

function hocwp_dat_ve_tau_admin_style_and_script() {
    hocwp_register_core_style_and_script();
    wp_register_style('hocwp-admin-style', HOCWP_URL . '/css/hocwp-admin'. HOCWP_CSS_SUFFIX, array('hocwp-style'));
    wp_register_script('hocwp-admin', HOCWP_URL . '/js/hocwp-admin' . HOCWP_JS_SUFFIX, array('jquery', 'hocwp'), false, true);
    wp_localize_script('hocwp', 'hocwp', hocwp_default_script_localize_object());
    wp_enqueue_style('hocwp-admin-style');
    wp_enqueue_script('hocwp-admin');
}
add_action('admin_enqueue_scripts', 'hocwp_dat_ve_tau_admin_style_and_script');

function hocwp_dat_ve_tau_post_type_and_taxonomy() {
    $args = array(
        'name' => __('Vé tàu', 'hocwp-dat-ve-tau'),
        'slug' => 've_tau'
    );
    hocwp_register_post_type_private($args);

    $args = array(
        'name' => __('Ga', 'hocwp-dat-ve-tau'),
        'slug' => 'ga',
        'post_types' => 've_tau',
        'show_ui' => false,
        'show_admin_column' => false
    );
    hocwp_register_taxonomy($args);

    $args = array(
        'name' => __('Hạng ghế', 'hocwp-dat-ve-tau'),
        'slug' => 'hang_ghe',
        'post_types' => 've_tau',
        'show_ui' => false,
        'show_admin_column' => false
    );
    hocwp_register_taxonomy($args);

    $args = array(
        'name' => __('Địa chỉ', 'hocwp-dat-ve-tau'),
        'slug' => 'dia_chi',
        'post_types' => 've_tau',
        'show_ui' => false,
        'show_admin_column' => false
    );
    hocwp_register_taxonomy($args);
}
add_action('init', 'hocwp_dat_ve_tau_post_type_and_taxonomy');

function hocwp_dat_ve_tau_datepicker_icon() {
    return hocwp_plugin_get_image_url(HOCWP_DAT_VE_TAU_URL, 'calendar.gif');
}
add_filter('hocwp_datepicker_icon', 'hocwp_dat_ve_tau_datepicker_icon');

function hocwp_dat_ve_tau_remove_taxonomy_columns($posts_columns, $post_type) {
    if('ve_tau' == $post_type) {
        unset($posts_columns['ga']);
    }
    return $posts_columns;
}
add_filter('manage_posts_columns', 'hocwp_dat_ve_tau_remove_taxonomy_columns', 99, 2);