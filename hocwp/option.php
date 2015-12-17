<?php
if(!function_exists('add_filter')) exit;
function hocwp_option_get_list_object() {
    global $hocwp_options;
    return $hocwp_options;
}

function hocwp_option_add_object_to_list(HOCWP_Option $option) {
    global $hocwp_options;
    $option_name = $option->get_option_name_no_prefix();
    $hocwp_options[$option_name] = $option;
}

function hocwp_option_get_object_from_list($key) {
    global $hocwp_options;
    return isset($hocwp_options[$key]) ? $hocwp_options[$key] : null;
}

function hocwp_option_get_data($base_slug) {
    $data = array();
    $option = hocwp_option_get_object_from_list($base_slug);
    if(hocwp_object_valid($option)) {
        $data = $option->get();
    } else {
        $data = get_option('hocwp_' . $base_slug);
    }
    return $data;
}

function hocwp_option_get_value($base, $key) {
    $data = hocwp_option_get_data($base);
    $result = hocwp_get_value_by_key($data, $key);
    return $result;
}

function hocwp_option_add_setting_field($base, $args) {
    $option = hocwp_option_get_object_from_list($base);
    if(hocwp_object_valid($option)) {
        $id = isset($args['id']) ? $args['id'] : '';
        $name = isset($args['name']) ? $args['name'] : '';
        hocwp_transmit_id_and_name($id, $name);
        $args['id'] = $option->get_field_id($id);
        $args['name'] = $option->get_field_name($name);
        if(!isset($args['value'])) {
            $args['value'] = $option->get_by_key($name);
        }
        $option->add_field($args);
    }
}

function hocwp_get_option($base_name) {
    $option = hocwp_option_get_object_from_list($base_name);
    if(hocwp_object_valid($option)) {
        return $option->get();
    }
    return array();
}

function hocwp_add_option_page_smtp_email($parent_slug = null) {
    if(null != $parent_slug) {
        _deprecated_argument(__FUNCTION__, '2.7.4', __('Please do not use $parent_slug argument since core version 2.7.4 or later.', 'hocwp'));
    }
    require(HOCWP_PATH . '/options/setting-smtp-email.php');
}

function hocwp_get_google_api_key() {
    $key = hocwp_option_get_value('option_social', 'google_api_key');
    $key = apply_filters('hocwp_google_api_key', $key);
    return $key;
}

function hocwp_get_footer_logo_url() {
    $result = hocwp_theme_get_option('footer_logo');
    $result = hocwp_sanitize_media_value($result);
    $result = $result['url'];
    return $result;
}

function hocwp_option_defaults() {
    $defaults = array(
        'theme_custom' => array(
            'background_music' => array(
                'play_ons' => array(
                    'home' => __('Homepage', 'hocwp'),
                    'single' => __('Single', 'hocwp'),
                    'page' => __('Page', 'hocwp'),
                    'archive' => __('Archive', 'hocwp'),
                    'search' => __('Search', 'hocwp'),
                    'all' => __('Play on whole page', 'hocwp')
                ),
                'play_on' => 'home'
            )
        )
    );
    return apply_filters('hocwp_option_defaults', $defaults);
}

function hocwp_recommended_plugins() {
    $required = array();
    $required = apply_filters('hocwp_required_plugins', $required);
    $defaults = array(
        'required' => $required,
        'recommended' => array(
            'wordpress-seo',
            'wp-super-cache',
            'wp-optimize',
            'wp-external-links',
            'syntaxhighlighter',
            'akismet',
            'google-analytics-for-wordpress',
            'updraftplus'
        )
    );
    return apply_filters('hocwp_recommended_plugins', $defaults);
}