<?php
function hocwp_dat_ve_tau_ini_session_save_path() {

}
add_action('hocwp_session_start_before', 'hocwp_dat_ve_tau_ini_session_save_path');

//add_filter('hocwp_use_session', '__return_true');