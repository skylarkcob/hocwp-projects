<?php
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