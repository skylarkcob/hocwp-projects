<?php
function hocwp_login_body_class($classes, $action) {
    $classes[] = 'hocwp';
    if(!empty($action)) {
        $classes[] = 'action-' . $action;
    }
    return $classes;
}
add_filter('login_body_class', 'hocwp_login_body_class', 10, 2);

function hocwp_login_redirect_if_logged_in() {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    if(empty($action) && is_user_logged_in()) {
        wp_redirect(home_url('/'));
        exit;
    }
}
add_action('login_init', 'hocwp_login_redirect_if_logged_in');

function hocwp_get_login_logo_url() {
    $user_login = hocwp_option_get_object_from_list('user_login');
    $url = '';
    if(hocwp_object_valid($user_login)) {
        $option = $user_login->get();
        $logo = hocwp_get_value_by_key($option, 'logo');
        $logo = hocwp_sanitize_media_value($logo);
        $url = $logo['url'];
    }
    if(empty($url)) {
        $theme_setting = hocwp_option_get_object_from_list('theme_setting');
        if(hocwp_object_valid($theme_setting)) {
            $option = $theme_setting->get();
            $logo = hocwp_get_value_by_key($option, 'logo');
            $logo = hocwp_sanitize_media_value($logo);
            $url = $logo['url'];
        }
    }
    return $url;
}