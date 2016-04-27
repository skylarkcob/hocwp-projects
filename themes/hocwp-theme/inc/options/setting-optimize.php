<?php
if(!function_exists('add_filter')) exit;

function hocwp_option_optimize_defaults() {
	$alls = hocwp_option_defaults();
	$defaults = hocwp_get_value_by_key($alls, 'optimize');
	if(!hocwp_array_has_value($defaults)) {
		$defaults = array(
			'use_jquery_cdn' => 1,
			'use_bootstrap_cdn' => 1,
			'use_fontawesome_cdn' => 1,
			'use_superfish_cdn' => 1
		);
	}
	return apply_filters('hocwp_option_optimize_defaults', $defaults);
}

function hocwp_option_optimize() {
	$defaults = hocwp_option_optimize_defaults();
	$options = get_option('hocwp_optimize');
	$options = wp_parse_args($options, $defaults);
	return apply_filters('hocwp_option_optimize', $options);
}

global $hocwp_tos_tabs;
$parent_slug = 'hocwp_theme_option';

$option = new HOCWP_Option(__('Optimize', 'hocwp'), 'hocwp_optimize');
$option->set_parent_slug($parent_slug);

$option->add_field(array('id' => 'use_jquery_cdn', 'title' => __('jQuery CDN', 'hocwp'), 'label' => __('Load jQuery from Google CDN server.', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'default' => 1));
$option->add_field(array('id' => 'use_bootstrap_cdn', 'title' => __('Bootstrap CDN', 'hocwp'), 'label' => __('Load Bootstrap from Max CDN server.', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'default' => 1));
$option->add_field(array('id' => 'use_fontawesome_cdn', 'title' => __('FontAwesome CDN', 'hocwp'), 'label' => __('Load FontAwesome from Max CDN server.', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'default' => 1));
$option->add_field(array('id' => 'use_superfish_cdn', 'title' => __('Superfish CDN', 'hocwp'), 'label' => __('Load Superfish from CloudFlare CDN server.', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'default' => 1));

$option->add_option_tab($hocwp_tos_tabs);
$option->set_page_header_callback('hocwp_theme_option_form_before');
$option->set_page_footer_callback('hocwp_theme_option_form_after');
$option->set_page_sidebar_callback('hocwp_theme_option_sidebar_tab');

$option->init();

hocwp_option_add_object_to_list($option);