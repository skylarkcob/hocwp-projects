<?php
function hocwp_theme_custom_license_data($data) {
    $data = array(
        'hashed' => '$P$Bl/ZAjtEtre2NmjxmvJl9PC8r5G7BP/',
        'key_map' => 'a:5:{i:0;s:4:"code";i:1;s:5:"email";i:2;s:7:"use_for";i:3;s:6:"domain";i:4;s:15:"hashed_password";}'
    );
    //$data = array();
    return $data;
}
add_filter('hocwp_theme_license_defined_data', 'hocwp_theme_custom_license_data');