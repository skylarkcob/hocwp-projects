<?php
function hocwp_wrap_tag($text, $tag) {
    $html = new HOCWP_HTML($tag);
    $html->set_text($text);
    return $html->build();
}

function hocwp_use_comment_form_captcha() {
    $use = get_option('hocwp_discussion');
    $use = hocwp_get_value_by_key($use, 'use_captcha');
    $use = apply_filters('hocwp_use_comment_form_captcha', $use);
    return (bool)$use;
}

function hocwp_user_not_use_comment_form_captcha() {
    $use = get_option('hocwp_discussion');
    $use = hocwp_get_value_by_key($use, 'user_no_captcha', 1);
    $use = apply_filters('hocwp_user_not_use_comment_form_captcha', $use);
    return (bool)$use;
}

function hocwp_use_comment_form_captcha_custom_position() {
    return apply_filters('hocwp_use_comment_form_captcha_custom_position', false);
}

function hocwp_build_license_transient_name($type, $use_for) {
    $name = 'hocwp_' . $type . '_' . $use_for . '_license_valid';
    return md5($name);
}