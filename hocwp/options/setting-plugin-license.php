<?php
if(!function_exists('add_filter')) exit;
$parent_slug = 'plugins.php';
$option_plugin_license = new HOCWP_Option(__('Plugin Licenses', 'hocwp'), 'hocwp_plugin_license');
$option_plugin_license->set_parent_slug($parent_slug);
$option_plugin_license->set_use_style_and_script(true);
$option_plugin_license->add_field(array('id' => 'use_for', 'title' => __('For plugin', 'hocwp'), 'field_callback' => 'hocwp_field_select_plugin'));
$option_plugin_license->add_field(array('id' => 'customer_email', 'title' => __('Customer email', 'hocwp')));
$option_plugin_license->add_field(array('id' => 'license_code', 'title' => __('License code', 'hocwp')));
if(!hocwp_menu_page_exists('hocwp_plugin_license')) {
	$option_plugin_license->init();
}
hocwp_option_add_object_to_list($option_plugin_license);

function hocwp_option_plugin_license_sanitized($input) {
	$use_for = isset($input['use_for']) ? $input['use_for'] : '';
	if(!empty($use_for)) {
		$customer_email = isset($input['customer_email']) ? $input['customer_email'] : '';
		if(is_email($customer_email)) {
			$code = isset($input['license_code']) ? $input['license_code'] : '';
			$code = strtoupper($code);
			$option = get_option('hocwp_plugin_licenses');
			$use_for_key = md5($use_for);
			$option[$use_for_key]['customer_email'] = $customer_email;
			$option[$use_for_key]['license_code'] = $code;
			update_option('hocwp_plugin_licenses', $option);
		}
	}
	hocwp_delete_transient_license_valid();
}
add_action('hocwp_sanitize_' . $option_plugin_license->get_option_name_no_prefix() . '_option', 'hocwp_option_plugin_license_sanitized');