<?php
global $hocwp_theme_switcher_type, $hocwp_theme_switcher_time, $hocwp_theme_switcher_license, $hocwp_theme_switcher_license_valid;

$hocwp_theme_switcher_license_valid = true;

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
    $settings_link = sprintf('<a href="themes.php?page=hocwp_theme_switcher">%s</a>', __('Settings', 'hocwp-theme-switcher') );
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . HOCWP_THEME_SWITCHER_BASENAME, 'hocwp_theme_switcher_settings_link');

function hocwp_theme_switcher_textdomain() {
    load_plugin_textdomain('hocwp-theme-switcher', false, HOCWP_THEME_SWITCHER_DIRNAME . '/languages/');
}
add_action('plugins_loaded', 'hocwp_theme_switcher_textdomain');