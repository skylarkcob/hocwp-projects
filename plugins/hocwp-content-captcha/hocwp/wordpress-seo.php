<?php
function hocwp_get_wpseo_social() {
    return get_option('wpseo_social');
}

function hocwp_get_wpseo_social_value($key) {
    $social = hocwp_get_wpseo_social();
    return hocwp_get_value_by_key($social, $key);
}

function hocwp_get_wpseo_social_facebook_admin() {
    return hocwp_get_wpseo_social_value('fb_admins');
}

function hocwp_get_wpseo_social_facebook_app_id() {
    return hocwp_get_wpseo_social_value('fbadminapp');
}

function hocwp_wpseo_installed() {
    return defined('WPSEO_FILE');
}

function hocwp_update_wpseo_social($key, $value) {
    $social = hocwp_get_wpseo_social();
    $social[$key] = $value;
    update_option('wpseo_social', $social);
}

function hocwp_wpseo_get_internallinks() {
    return get_option('wpseo_internallinks');
}

function hocwp_wpseo_breadcrumb_enabled() {
    $option = hocwp_wpseo_get_internallinks();
    $value = hocwp_get_value_by_key($option, 'breadcrumbs-enable');
    return (bool)$value;
}

function hocwp_wpseo_get_post_title($post_id) {
    $title = get_post_meta($post_id, '_yoast_wpseo_title', true);
    if(empty($title)) {
        $title = get_the_title($post_id);
    }
    return $title;
}