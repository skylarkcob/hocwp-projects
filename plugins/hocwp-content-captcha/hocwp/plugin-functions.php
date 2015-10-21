<?php
function hocwp_plugin_get_image_url($base_url, $name) {
    return trailingslashit($base_url) . 'images/' . $name;
}

function hocwp_plugin_get_template($base_path, $slug, $name = '') {
    if(!empty($name)) {
        $slug .= '-' . $name;
    }
    $slug .= '.php';
    $base_path = trailingslashit($base_path) . $slug;
    if(file_exists($base_path)) {
        include($base_path);
    }
}

function hocwp_plugin_get_module($base_path, $name) {
    hocwp_plugin_get_template($base_path, 'module', $name);
}