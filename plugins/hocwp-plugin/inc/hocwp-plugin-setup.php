<?php
global $hocwp_plugin_default_type, $hocwp_plugin_default_time, $hocwp_plugin_default_license, $hocwp_plugin_default_license_valid, $hocwp_plugin_default_license_data;

$hocwp_plugin_default_license_valid = true;

$hocwp_plugin_default_license_data = array(
    'hashed' => '',
    'key_map' => ''
);

function hocwp_plugin_default_activation() {
    if(!current_user_can('activate_plugins')) {
        return;
    }
    flush_rewrite_rules();
    do_action('hocwp_plugin_default_activation');
}
register_activation_hook(HOCWP_PLUGIN_DEFAULT_FILE, 'hocwp_plugin_default_activation');

function hocwp_plugin_default_deactivation() {
    if(!current_user_can('activate_plugins')) {
        return;
    }
    flush_rewrite_rules();
    do_action('hocwp_plugin_default_deactivation');
}
register_deactivation_hook(HOCWP_PLUGIN_DEFAULT_FILE, 'hocwp_plugin_default_deactivation');

function hocwp_plugin_default_settings_link($links) {
    $settings_link = sprintf('<a href="' . HOCWP_PLUGIN_DEFAULT_SETTINGS_URL . '">%s</a>', __('Settings', 'hocwp-plugin-default'));
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . HOCWP_PLUGIN_DEFAULT_BASENAME, 'hocwp_plugin_default_settings_link');

function hocwp_plugin_default_textdomain() {
    load_plugin_textdomain('hocwp-plugin-default', false, HOCWP_PLUGIN_DEFAULT_DIRNAME . '/languages/');
}
add_action('plugins_loaded', 'hocwp_plugin_default_textdomain');