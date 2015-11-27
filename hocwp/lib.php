<?php
if(!function_exists('add_filter')) exit;
function hocwp_lib_load_chosen() {
    wp_enqueue_script('chosen', HOCWP_URL . '/lib/chosen/chosen.jquery.min.js', array('jquery'), false, true);
    wp_enqueue_style('chosen-style', HOCWP_URL . '/lib/chosen/chosen.min.css');
}

function hocwp_lib_admin_style_and_script() {
    global $pagenow;
    $use_chosen_select = apply_filters('hocwp_use_chosen_select', false);
    if('widgets.php' == $pagenow || $use_chosen_select) {
        hocwp_lib_load_chosen();
    }
}
add_action('admin_enqueue_scripts', 'hocwp_lib_admin_style_and_script');