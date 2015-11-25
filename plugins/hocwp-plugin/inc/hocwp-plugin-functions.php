<?php
if(!function_exists('add_filter')) exit;
function hocwp_plugin_default_get_option_defaults() {
    $defaults = array();
    $defaults = apply_filters(HOCWP_PLUGIN_DEFAULT_OPTION_NAME . '_option_defaults', $defaults);
    return $defaults;
}

function hocwp_plugin_default_get_option() {
    $defaults = hocwp_plugin_default_get_option_defaults();
    $option = get_option(HOCWP_PLUGIN_DEFAULT_OPTION_NAME);
    if(!hocwp_array_has_value($option)) {
        $option = array();
    }
    $option = wp_parse_args($option, $defaults);
    return apply_filters(HOCWP_PLUGIN_DEFAULT_OPTION_NAME . '_options', $option);
}

function hocwp_plugin_default_get_license_defined_data() {
    global $hocwp_plugin_default_license_data;
    $hocwp_plugin_default_license_data = hocwp_sanitize_array($hocwp_plugin_default_license_data);
    return apply_filters('hocwp_plugin_default_license_defined_data', $hocwp_plugin_default_license_data);
}

function hocwp_plugin_default_license_valid() {
    global $hocwp_plugin_default_license, $hocwp_plugin_default_license_valid;

    if(!hocwp_object_valid($hocwp_plugin_default_license)) {
        $hocwp_plugin_default_license = new HOCWP_License();
        $hocwp_plugin_default_license->set_type('plugin');
        $hocwp_plugin_default_license->set_use_for(HOCWP_PLUGIN_DEFAULT_BASENAME);
        $hocwp_plugin_default_license->set_option_name(HOCWP_PLUGIN_LICENSE_OPTION_NAME);
    }

    $hocwp_plugin_default_license_valid = $hocwp_plugin_default_license->check_valid(hocwp_plugin_default_get_license_defined_data());
    return $hocwp_plugin_default_license_valid;
}