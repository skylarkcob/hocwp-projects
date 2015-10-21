<?php
global $hocwp_content_captcha_type, $hocwp_content_captcha_time, $hocwp_content_captcha_license, $hocwp_content_captcha_license_valid, $hocwp_content_captcha_license_data;

$hocwp_content_captcha_license_valid = true;

$hocwp_content_captcha_license_data = array(
    'hashed' => '$P$BtFGxREeSiQGi9lmyiwP1bMSUwp1wV0',
    'key_map' => 'a:5:{i:0;s:6:"domain";i:1;s:4:"code";i:2;s:5:"email";i:3;s:7:"use_for";i:4;s:15:"hashed_password";}'
);

//$hocwp_content_captcha_license_data = array();

function hocwp_content_captcha_activation() {
    if(!current_user_can('activate_plugins')) {
        return;
    }
    do_action('hocwp_content_captcha_activation');
}
register_activation_hook(HOCWP_CONTENT_CAPTCHA_FILE, 'hocwp_content_captcha_activation');

function hocwp_content_captcha_deactivation() {
    if(!current_user_can('activate_plugins')) {
        return;
    }
    do_action('hocwp_content_captcha_deactivation');
}
register_deactivation_hook(HOCWP_CONTENT_CAPTCHA_FILE, 'hocwp_content_captcha_deactivation');

function hocwp_content_captcha_settings_link($links) {
    $settings_link = sprintf('<a href="' . HOCWP_CONTENT_CAPTCHA_SETTINGS_URL . '">%s</a>', __('Settings', 'hocwp-content-captcha'));
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . HOCWP_CONTENT_CAPTCHA_BASENAME, 'hocwp_content_captcha_settings_link');

function hocwp_content_captcha_textdomain() {
    load_plugin_textdomain('hocwp-content-captcha', false, HOCWP_CONTENT_CAPTCHA_DIRNAME . '/languages/');
}
add_action('plugins_loaded', 'hocwp_content_captcha_textdomain');