<?php
if(!function_exists('add_filter')) exit;
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

function hocwp_is_admin($user = null) {
    if(!is_a($user, 'WP_User')) {
        return current_user_can('manage_options');
    }
    if(array_intersect($user->roles, array('administrator'))) {
        return true;
    }
    return false;
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

function hocwp_get_user_roles($user = null) {
    $roles = array();
    if(hocwp_id_number_valid($user)) {
        $user = get_user_by('id', $user);
    }
    if(!is_a($user, 'WP_User')) {
        $user = wp_get_current_user();
    }
    if(is_a($user, 'WP_User')) {
        $roles = (array)$user->roles;
    }
    return $roles;
}

function hocwp_get_user_role($user = null) {
    $roles = hocwp_get_user_roles($user);
    return current($roles);
}