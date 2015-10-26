<?php
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
    require_once(HOCWP_PATH . '/options/setting-smtp-email.php');
}