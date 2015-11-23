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
    return 'hocwp_check_license_' . md5($name);
}

function hocwp_change_tag_attribute($tag, $attr, $value) {
    $tag = preg_replace('/' . $attr . '="(.*?)"/i', $attr . '="' . $value . '"', $tag);
    return $tag;
}

function hocwp_in_maintenance_mode() {
    $option = get_option('hocwp_maintenance');
    $result = hocwp_get_value_by_key($option, 'enabled');
    $result = (bool)$result;
    $result = apply_filters('hocwp_enable_maintenance_mode', $result);
    if(hocwp_maintenance_mode_exclude_condition()) {
        $result = false;
    }
    return $result;
}

function hocwp_in_maintenance_mode_notice() {
    if(hocwp_in_maintenance_mode()) {
        $page = hocwp_get_current_admin_page();
        if('hocwp_maintenance' != $page) {
            $args = array(
                'text' => sprintf(__('Your site is running in maintenance mode, so you can go to %s and turn it off when done.', 'hocwp'), '<a href="' . admin_url('tools.php?page=hocwp_maintenance') . '">' . __('setting page', 'hocwp') . '</a>')
            );
            hocwp_admin_notice($args);
        }
    }
}

function hocwp_maintenance_mode_exclude_condition() {
    $condition = hocwp_is_admin();
    return apply_filters('hocwp_maintenance_mode_exclude_condition', $condition);
}

function hocwp_maintenance_mode() {
    if(!is_admin() && hocwp_in_maintenance_mode()) {
        if(!hocwp_maintenance_mode_exclude_condition()) {
            $charset = get_bloginfo('charset') ? get_bloginfo('charset') : 'UTF-8';
            $protocol = !empty($_SERVER['SERVER_PROTOCOL']) && in_array($_SERVER['SERVER_PROTOCOL'], array('HTTP/1.1', 'HTTP/1.0')) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
            $status_code = (int)apply_filters('hocwp_maintenance_mode_status_code', 503);
            nocache_headers();
            ob_start();
            header("Content-type: text/html; charset=$charset");
            header("$protocol $status_code Service Unavailable", TRUE, $status_code);
            hocwp_get_views_template('maintenance');
            ob_flush();
            exit;
        }
    }
}

function hocwp_get_views_template($slug, $name = '') {
    $template = $slug;
    $template = str_replace('.php', '', $template);
    if(!empty($name)) {
        $name = str_replace('.php', '', $name);
        $template .= '-' . $name;
    }
    $template .= '.php';
    $template = HOCWP_PATH . '/views/' . $template;
    if(file_exists($template)) {
        include($template);
    }
}

function hocwp_use_jquery_cdn() {
    $option = get_option('hocwp_optimize');
    $use = hocwp_get_value_by_key($option, 'use_jquery_cdn', 1);
    $use = (bool)$use;
    $use = apply_filters('hocwp_use_jquery_google_cdn', $use);
    return $use;
}

function hocwp_load_jquery_from_cdn() {
    if(!is_admin()) {
        $use = hocwp_use_jquery_cdn();
        if($use) {
            global $wp_version, $wp_scripts;
            $handle = (version_compare($wp_version, '3.6-alpha1', '>=') ) ? 'jquery-core' : 'jquery';
            $enqueued = wp_script_is($handle);
            wp_enqueue_script($handle);
            $version = '';
            if(is_a($wp_scripts, 'WP_Scripts')) {
                $registered = $wp_scripts->registered;
                if(isset($registered[$handle])) {
                    $version = $registered[$handle]->ver;
                }
            }
            if(empty($version)) {
                $version = HOCWP_JQUERY_LATEST_VERSION;
            }
            wp_deregister_script($handle);
            wp_register_script($handle, '//ajax.googleapis.com/ajax/libs/jquery/'. $version . '/jquery.min.js');
            if($enqueued) {
                wp_enqueue_script($handle);
                add_action('wp_head', 'hocwp_jquery_google_cdn_fallback');
            }
        }
    }
}

function hocwp_jquery_google_cdn_fallback() {
    echo '<script>window.jQuery || document.write(\'<script src="' . includes_url('js/jquery/jquery.js') . '"><\/script>\')</script>' . "\n";
}