<?php
if(!function_exists('add_filter')) exit;
if(!has_action('init', 'hocwp_session_start')) {
    add_action('init', 'hocwp_session_start');
}

function hocwp_plugin_default_admin_bar_menu($wp_admin_bar) {
    $args = array(
        'id' => 'plugin-license',
        'title' => __('Plugin Licenses', 'hocwp-plugin-default'),
        'href' => HOCWP_PLUGIN_LICENSE_ADMIN_URL,
        'parent' => 'plugins'
    );
    $wp_admin_bar->add_node($args);
}
if(!is_admin()) add_action('admin_bar_menu', 'hocwp_plugin_default_admin_bar_menu', 99);

function hocwp_plugin_default_check_license() {
    if(!isset($_POST['submit']) && !hocwp_is_login_page()) {
        if(!hocwp_plugin_default_license_valid()) {
            if(!is_admin() && current_user_can('manage_options')) {
                wp_redirect(HOCWP_PLUGIN_LICENSE_ADMIN_URL);
                exit;
            }
            add_action('admin_notices', 'hocwp_plugin_default_invalid_license_notice');
        }
    }
}
add_action('hocwp_check_license', 'hocwp_plugin_default_check_license');

function hocwp_plugin_default_invalid_license_notice() {
    $plugin_name = hocwp_get_plugin_name(HOCWP_PLUGIN_DEFAULT_FILE, HOCWP_PLUGIN_DEFAULT_BASENAME);
    $plugin_name = hocwp_wrap_tag($plugin_name, 'strong');
    $args = array(
        'error' => true,
        'title' => __('Error', 'hocwp-plugin-default'),
        'text' => sprintf(__('Plugin %1$s is using an invalid license key! If you does not have one, please contact %2$s via email address %3$s for more information.', 'hocwp-plugin-default'), $plugin_name, '<strong>' . HOCWP_NAME . '</strong>', '<a href="mailto:' . esc_attr(HOCWP_EMAIL) . '">' . HOCWP_EMAIL . '</a>')
    );
    hocwp_admin_notice($args);
}

if(!hocwp_plugin_default_license_valid()) {
    return;
}

function hocwp_plugin_default_enqueue_scripts() {
    hocwp_register_core_style_and_script();
    $localize_object = hocwp_default_script_localize_object();
    if(hocwp_is_debugging()) {
        wp_localize_script('hocwp', 'hocwp', $localize_object);
        wp_register_script('hocwp-front-end', HOCWP_URL . '/js/hocwp-front-end' . HOCWP_JS_SUFFIX, array('hocwp'), false, true);
        wp_register_script('hocwp-plugin-default', HOCWP_PLUGIN_DEFAULT_URL . '/js/hocwp-plugin' . HOCWP_JS_SUFFIX, array('hocwp-front-end'), false, true);
    } else {
        wp_register_script('hocwp-plugin-default', HOCWP_PLUGIN_DEFAULT_URL . '/js/hocwp-plugin' . HOCWP_JS_SUFFIX, array(), HOCWP_PLUGIN_DEFAULT_VERSION, true);
        wp_localize_script('hocwp-plugin-default', 'hocwp', $localize_object);
    }
    wp_register_style('hocwp-plugin-default-style', HOCWP_PLUGIN_DEFAULT_URL . '/css/hocwp-plugin' . HOCWP_CSS_SUFFIX, array(), HOCWP_PLUGIN_DEFAULT_VERSION);
    wp_enqueue_style('hocwp-plugin-default-style');
    wp_enqueue_script('hocwp-plugin-default');
}
add_action('wp_enqueue_scripts', 'hocwp_plugin_default_enqueue_scripts');

function hocwp_plugin_default_admin_style_and_script() {
    hocwp_register_core_style_and_script();
    wp_register_style('hocwp-admin-style', HOCWP_URL . '/css/hocwp-admin'. HOCWP_CSS_SUFFIX, array('hocwp-style'), HOCWP_PLUGIN_DEFAULT_VERSION);
    wp_register_script('hocwp-admin', HOCWP_URL . '/js/hocwp-admin' . HOCWP_JS_SUFFIX, array('jquery', 'hocwp'), HOCWP_PLUGIN_DEFAULT_VERSION, true);
    wp_register_style('hocwp-plugin-default-style', HOCWP_PLUGIN_DEFAULT_URL . '/css/hocwp-plugin-admin' . HOCWP_CSS_SUFFIX, array('hocwp-admin-style'), HOCWP_PLUGIN_DEFAULT_VERSION);
    wp_register_script('hocwp-plugin-default', HOCWP_PLUGIN_DEFAULT_URL . '/js/hocwp-plugin-admin' . HOCWP_JS_SUFFIX, array('hocwp-admin'), HOCWP_PLUGIN_DEFAULT_VERSION, true);
    wp_localize_script('hocwp-plugin-default', 'hocwp', hocwp_default_script_localize_object());
    wp_enqueue_style('hocwp-plugin-default-style');
    wp_enqueue_script('hocwp-plugin-default');
}
add_action('admin_enqueue_scripts', 'hocwp_plugin_default_admin_style_and_script');