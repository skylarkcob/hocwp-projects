<?php
function hocwp_theme_switcher_enabled() {
    return apply_filters('hocwp_theme_switcher_enabled', defined('HOCWP_THEME_SWITCHER_VERSION'));
}

function hocwp_theme_switcher_default_mobile_theme_name() {
    $name = hocwp_option_get_value('theme_switcher', 'mobile_theme');
    return $name;
}

function hocwp_theme_switcher_get_mobile_theme_name() {
    $name = hocwp_theme_switcher_default_mobile_theme_name();
    return $name;
}

function hocwp_theme_switcher_control($name) {
    $mobile_theme = hocwp_theme_switcher_get_mobile_theme_name();
    if(!empty($mobile_theme)) {
        $name = $mobile_theme;
    }
    return $name;
}