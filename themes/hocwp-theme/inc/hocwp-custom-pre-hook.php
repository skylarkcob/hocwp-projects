<?php
function hocwp_theme_custom_license_data($data) {
    $data = array(
        'hashed' => '',
        'key_map' => ''
    );
    $data = array();
    return $data;
}
add_filter('hocwp_theme_license_defined_data', 'hocwp_theme_custom_license_data');