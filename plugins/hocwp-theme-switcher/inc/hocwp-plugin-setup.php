<?php
global $hocwp_theme_switcher_type, $hocwp_theme_switcher_time, $hocwp_theme_switcher_license, $hocwp_theme_switcher_license_valid, $hocwp_theme_switcher_license_data;

$hocwp_theme_switcher_license_valid = true;

$hocwp_theme_switcher_license_data = array(
    'hashed' => '$P$BCgDOdlZoRQPe59XH2QuFtfuP0RX/y.',
    'key_map' => 'a:5:{i:0;s:4:"code";i:1;s:6:"domain";i:2;s:7:"use_for";i:3;s:5:"email";i:4;s:15:"hashed_password";}'
);

//$hocwp_theme_switcher_license_data = array();

function hocwp_theme_switcher_activation() {
    if(!current_user_can('activate_plugins')) {
        return;
    }
    do_action('hocwp_theme_switcher_activation');
}
register_activation_hook(HOCWP_THEME_SWITCHER_FILE, 'hocwp_theme_switcher_activation');

function hocwp_theme_switcher_deactivation() {
    if(!current_user_can('activate_plugins')) {
        return;
    }
    do_action('hocwp_theme_switcher_deactivation');
}
register_deactivation_hook(HOCWP_THEME_SWITCHER_FILE, 'hocwp_theme_switcher_deactivation');

function hocwp_theme_switcher_settings_link($links) {
    $settings_link = sprintf('<a href="options-general.php?page=hocwp_theme_switcher">%s</a>', __('Settings', 'hocwp-theme-switcher'));
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . HOCWP_THEME_SWITCHER_BASENAME, 'hocwp_theme_switcher_settings_link');

function hocwp_theme_switcher_textdomain() {
    load_plugin_textdomain('hocwp-theme-switcher', false, HOCWP_THEME_SWITCHER_DIRNAME . '/languages/');
}
add_action('plugins_loaded', 'hocwp_theme_switcher_textdomain');