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

function hocwp_remove_all_user_role($user) {
    foreach($user->roles as $role) {
        $user->remove_role($role);
    }
}

function hocwp_add_user($args = array()) {
    $result = 0;
    $password = isset($args['password']) ? $args['password'] : '';
    $role = isset($args['role']) ? $args['role'] : '';
    $username = isset($args['username']) ? $args['username'] : '';
    $email = isset($args['email']) ? $args['email'] : '';
    if(!empty($password) && !empty($username) && !empty($email) && !username_exists($username) && !email_exists($email)) {
        $user_id = wp_create_user($username, $password, $email);
        $user = get_user_by('id', $user_id);
        hocwp_remove_all_user_role($user);
        if(empty($role)) {
            $role = get_option('default_role');
            if(empty($role)) {
                $role = 'subscriber';
            }
        }
        $user->add_role($role);
        $result = $user_id;
    }
    return $result;
}

function hocwp_add_user_admin($args = array()) {
    $args['role'] = 'administrator';
    hocwp_add_user($args);
}