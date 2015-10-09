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

function hocwp_option_get_value($base, $key) {
    $result = '';
    $option = hocwp_option_get_object_from_list($base);
    if(hocwp_object_valid($option)) {
        $data = $option->get();
        $result = hocwp_get_value_by_key($data, $key);
    }
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