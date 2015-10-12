<?php
function hocwp_get_administrators($args = array()) {
    $args['role'] = 'administrator';
    return get_users($args);
}

function hocwp_get_first_admin($args = array()) {
    $users = hocwp_get_administrators($args);
    $user = new WP_User();
    foreach($users as $value) {
        $user = $value;
        break;
    }
    return $user;
}