<?php
if(!function_exists('add_filter')) exit;

function hocwp_plugin_default_license_data($data) {
    $data = array(
        'hashed' => '',
        'key_map' => ''
    );
    return $data;
}
add_filter('hocwp_plugin_default_license_defined_data', 'hocwp_plugin_default_license_data');