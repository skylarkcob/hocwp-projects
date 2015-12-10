<?php
if(!function_exists('add_filter')) exit;
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

function hocwp_use_captcha_for_login_page() {
    $options = get_option('hocwp_user_login');
    $use_captcha = hocwp_get_value_by_key($options, 'use_captcha');
    $use_captcha = apply_filters('hocwp_use_captcha_for_login_page', $use_captcha);
    return (bool)$use_captcha;
}

function hocwp_login_captcha_field() {
    $args = array(
        'before' => '<p>',
        'after' => '</p>'
    );
    hocwp_field_captcha($args);
}

function hocwp_verify_login_captcha($user, $password) {
    if(isset($_POST['captcha'])) {
        $captcha_code = $_POST['captcha'];
        $captcha = new HOCWP_Captcha();
        if($captcha->check($captcha_code)) {
            return $user;
        }
        return new WP_Error(__('Captcha Invalid', 'hocwp'), '<strong>' . __('ERROR:', 'hocwp') . '</strong> ' . __('Please enter a valid captcha.', 'hocwp'));
    }
    return new WP_Error(__('Captcha Invalid', 'hocwp'), '<strong>' . __('ERROR:', 'hocwp') . '</strong> ' . __('You are a robot, if not please check JavaScript enabled on your browser.', 'hocwp'));
}

function hocwp_verify_registration_captcha($errors, $sanitized_user_login, $user_email) {
    if(isset($_POST['captcha'])) {
        $captcha_code = $_POST['captcha'];
        $captcha = new HOCWP_Captcha();
        if(!$captcha->check($captcha_code)) {
            $errors->add(__('Captcha Invalid', 'hocwp'), '<strong>' . __('ERROR:', 'hocwp') . '</strong> ' . __('Please enter a valid captcha.', 'hocwp'));
        }
    } else {
        $errors->add(__('Captcha Invalid', 'hocwp'), '<strong>' . __('ERROR:', 'hocwp') . '</strong> ' . __('You are a robot, if not please check JavaScript enabled on your browser.', 'hocwp'));
    }
    return $errors;
}

function hocwp_verify_lostpassword_captcha() {
    if(isset($_POST['captcha'])) {
        $captcha_code = $_POST['captcha'];
        $captcha = new HOCWP_Captcha();
        if(!$captcha->check($captcha_code)) {
            wp_die('<strong>' . __('ERROR:', 'hocwp') . '</strong> ' . __('Please enter a valid captcha.', 'hocwp'), __('Captcha Invalid', 'hocwp'));
        }
    } else {
        wp_die('<strong>' . __('ERROR:', 'hocwp') . '</strong> ' . __('You are a robot, if not please check JavaScript enabled on your browser.', 'hocwp'), __('Captcha Invalid', 'hocwp'));
    }
}

if(hocwp_use_captcha_for_login_page()) {
    add_action('login_form', 'hocwp_login_captcha_field');
    add_action('lostpassword_form', 'hocwp_login_captcha_field');
    add_action('register_form', 'hocwp_login_captcha_field');
    add_filter('wp_authenticate_user', 'hocwp_verify_login_captcha', 10, 2);
    add_filter('registration_errors', 'hocwp_verify_registration_captcha', 10, 3);
    add_action('lostpassword_post', 'hocwp_verify_lostpassword_captcha');
}