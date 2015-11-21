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

function hocwp_plugin_load_custom_css() {
    $option = get_option('hocwp_plugin_custom_css');
    $css = hocwp_get_value_by_key($option, 'code');
    if(!empty($css)) {
        $css = hocwp_minify_css($css);
        $style = new HOCWP_HTML('style');
        $style->set_attribute('type', 'text/css');
        $style->set_text($css);
        $style->output();
    }
}
add_action('wp_head', 'hocwp_plugin_load_custom_css', 99);