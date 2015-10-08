<?php
function hocwp_theme_switcher_license_valid() {
    global $hocwp_theme_switcher_license, $hocwp_theme_switcher_license_valid;

    if(!hocwp_object_valid($hocwp_theme_switcher_license)) {
        $hocwp_theme_switcher_license = new HOCWP_License();
        $hocwp_theme_switcher_license->set_type('plugin');
        $hocwp_theme_switcher_license->set_use_for(HOCWP_THEME_SWITCHER_BASENAME);
        $hocwp_theme_switcher_license->set_option_name('hocwp_plugin_licenses');
    }

    $hocwp_theme_switcher_license_valid = $hocwp_theme_switcher_license->check_valid();
    return $hocwp_theme_switcher_license_valid;
}