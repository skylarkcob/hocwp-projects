<?php
function hocwp_lib_load_chosen() {
    wp_enqueue_script('chosen', HOCWP_URL . '/lib/chosen/chosen.jquery.min.js', array('jquery'), false, true);
    wp_enqueue_style('chosen-style', HOCWP_URL . '/lib/chosen/chosen.min.css');
}

function hocwp_lib_admin_style_and_script() {
    global $pagenow;
    if('widgets.php' == $pagenow) {
        hocwp_lib_load_chosen();
    }
}
add_action('admin_enqueue_scripts', 'hocwp_lib_admin_style_and_script');