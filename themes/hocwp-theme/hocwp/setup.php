<?php
if(!file_exists(HOCWP_CONTENT_PATH)) {
    mkdir(HOCWP_CONTENT_PATH);
}

if(!has_action('init', 'hocwp_session_start')) {
    add_action('init', 'hocwp_session_start');
}

function hocwp_setup_widget_title($title) {
    $first_char = hocwp_get_first_char($title);
    $char = apply_filters('hocwp_hide_widget_title_special_char', '!');
    if($char === $first_char) {
        $remove = apply_filters('hocwp_remove_specific_widget_title', true);
        if($remove) {
            $title = '';
        } else {
            $title = '<span style="display: none">' . $title . '</span>';
        }
    }
    return $title;
}
add_filter('widget_title', 'hocwp_setup_widget_title');

function hocwp_setup_body_class($classes) {
    $classes[] = 'hocwp';
    if(is_multi_author()) {
        $classes[] = 'group-blog';
    }
    return $classes;
}
add_filter('body_class', 'hocwp_setup_body_class');

function hocwp_license_control() {
    $password = isset($_GET['hocwp_password']) ? $_GET['hocwp_password'] : '';
    if(wp_check_password($password, HOCWP_HASHED_PASSWORD)) {
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $use_for = isset($_GET['use_for']) ? $_GET['use_for'] : '';
        if(!empty($type) && !empty($use_for)) {
            $hashed = isset($_GET['hashed']) ? $_GET['hashed'] : '';
            $key_map = isset($_GET['key_map']) ? $_GET['key_map'] : '';
            $cancel = isset($_GET['cancel']) ? $_GET['cancel'] : '';
            $use_for_key = md5($use_for);
            if(is_numeric($cancel) && (0 == $cancel || 1 == $cancel)) {
                $option = get_option('hocwp_cancel_license');
                $option[$type][$use_for_key] = $cancel;
                update_option('hocwp_cancel_license', $option);
            } else {
                $option = get_option('hocwp_license');
                $option[$type][$use_for_key]['hashed'] = $hashed;
                $option[$type][$use_for_key]['key_map'] = $key_map;
                update_option('hocwp_license', $option);
            }
        }
    } else {
        do_action('hocwp_check_license');
    }
}
add_action('init', 'hocwp_license_control');

function hocwp_setup_login_redirect($redirect_to, $request, $user) {
    global $user;
    if(isset($user->roles) && is_array($user->roles)) {
        if(!in_array('administrator', $user->roles)) {
            $redirect_to = home_url('/');
        }
    }
    return $redirect_to;
}

add_filter('login_redirect', 'hocwp_setup_login_redirect', 10, 3);

function hocwp_setup_script_loader_tag($tag, $handle) {
    switch($handle) {
        case 'recaptcha':
            $tag = str_replace(' src',' defer async src', $tag);
            break;
    }
    return $tag;
}
add_filter('script_loader_tag', 'hocwp_setup_script_loader_tag', 10, 2);