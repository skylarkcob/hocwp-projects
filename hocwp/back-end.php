<?php
if(!function_exists('add_filter')) exit;

function hocwp_upgrader_process_complete($upgrader, $options) {
    $type = hocwp_get_value_by_key($options, 'type');
    switch($type) {
        case 'plugin':
            do_action('hocwp_plugin_upgrader_process_complete', $upgrader, $options);
            break;
    }
}
add_action('upgrader_process_complete', 'hocwp_upgrader_process_complete', 10, 2);

function hocwp_plugin_upgrader_process_complete($upgrader, $options) {
    $plugins = hocwp_get_value_by_key($options, 'plugins');
    if(!hocwp_array_has_value($plugins)) {
        return;
    }
    foreach($plugins as $plugin) {
        $slug = hocwp_get_plugin_slug_from_file_path($plugin);
        $transient_name = 'hocwp_plugins_api_' . $slug . '_plugin_information';
        $transient_name = hocwp_sanitize_id($transient_name);
        delete_transient($transient_name);
    }
}
add_action('hocwp_plugin_upgrader_process_complete', 'hocwp_plugin_upgrader_process_complete', 10, 2);