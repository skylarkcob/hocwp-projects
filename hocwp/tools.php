<?php
function hocwp_maintenance_mode_default_settings() {
    $defaults = array(
        'title' => __('Maintenance mode', 'hocwp'),
        'heading' => __('Maintenance mode', 'hocwp'),
        'text' => __('<p>Sorry for the inconvenience.<br />Our website is currently undergoing scheduled maintenance.<br />Thank you for your understanding.</p>', 'hocwp')
    );
    return apply_filters('hocwp_maintenance_mode_default_settings', $defaults);
}

function hocwp_maintenance_mode_settings() {
    $defaults = hocwp_maintenance_mode_default_settings();
    $args = get_option('hocwp_maintenance');
    $args = wp_parse_args($args, $defaults);
    return apply_filters('hocwp_maintenance_mode_settings', $args);
}