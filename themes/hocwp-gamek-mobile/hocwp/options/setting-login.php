<?php
$parent_slug = 'users.php';

$option_user_login = new HOCWP_Option(__('Login settings', 'hocwp'), 'hocwp_user_login');
$option_user_login->set_parent_slug($parent_slug);
$option_user_login->set_use_style_and_script(true);
$option_user_login->set_use_media_upload(true);
$option_user_login->add_field(array('id' => 'logo', 'title' => __('Logo', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload'));
$option_user_login->add_field(array('id' => 'users_can_register', 'title' => __('Membership', 'hocwp'), 'label' => __('Anyone can register', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'value' => hocwp_users_can_register()));
$option_user_login->init();
hocwp_option_add_object_to_list($option_user_login);

function hocwp_users_can_register() {
	$result = (bool)get_option('users_can_register');
	return $result;
}

function hocwp_option_user_login_update($input) {
	$users_can_register = isset($input['users_can_register']) ? 1 : 0;
	if((bool)$users_can_register) {
		update_option('users_can_register', 1);
	} else {
		update_option('users_can_register', 0);
	}
}
add_action('hocwp_sanitize_' . $option_user_login->get_option_name_no_prefix() . '_option', 'hocwp_option_user_login_update');