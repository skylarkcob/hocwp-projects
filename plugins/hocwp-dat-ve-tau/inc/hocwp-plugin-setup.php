<?php
global $hocwp_dat_ve_tau_type, $hocwp_dat_ve_tau_time, $hocwp_dat_ve_tau_license, $hocwp_dat_ve_tau_license_valid, $hocwp_dat_ve_tau_license_data;

$hocwp_dat_ve_tau_license_valid = true;

$hocwp_dat_ve_tau_license_data = array(
    'hashed' => '$P$BGGIRuU7utU/k1U/KqqOM1irs7vay20',
    'key_map' => 'a:5:{i:0;s:4:"code";i:1;s:5:"email";i:2;s:6:"domain";i:3;s:7:"use_for";i:4;s:15:"hashed_password";}'
);

$hocwp_dat_ve_tau_license_data = array();

function hocwp_dat_ve_tau_activation() {
    if(!current_user_can('activate_plugins')) {
        return;
    }
    do_action('hocwp_dat_ve_tau_activation');
}
register_activation_hook(HOCWP_DAT_VE_TAU_FILE, 'hocwp_dat_ve_tau_activation');

function hocwp_dat_ve_tau_deactivation() {
    if(!current_user_can('activate_plugins')) {
        return;
    }
    do_action('hocwp_dat_ve_tau_deactivation');
}
register_deactivation_hook(HOCWP_DAT_VE_TAU_FILE, 'hocwp_dat_ve_tau_deactivation');

function hocwp_dat_ve_tau_settings_link($links) {
    $settings_link = sprintf('<a href="edit.php?post_type=ve_tau&page=hocwp_booking_form">%s</a>', __('Settings', 'hocwp-dat-ve-tau'));
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . HOCWP_DAT_VE_TAU_BASENAME, 'hocwp_dat_ve_tau_settings_link');

function hocwp_dat_ve_tau_textdomain() {
    load_plugin_textdomain('hocwp-dat-ve-tau', false, HOCWP_DAT_VE_TAU_DIRNAME . '/languages/');
}
add_action('plugins_loaded', 'hocwp_dat_ve_tau_textdomain');