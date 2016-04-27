<?php
if(!function_exists('add_filter')) exit;

function hocwp_option_utilities_defaults() {
	$alls = hocwp_option_defaults();
	$defaults = hocwp_get_value_by_key($alls, 'utilities');
	if(!hocwp_array_has_value($defaults)) {
		$defaults = array(
			'link_manager' => 0,
			'dashboard_widget' => 1
		);
	}
	return apply_filters('hocwp_option_utilities_defaults', $defaults);
}

function hocwp_option_utilities() {
	$defaults = hocwp_option_utilities_defaults();
	$options = get_option('hocwp_utilities');
	$options = wp_parse_args($options, $defaults);
	return apply_filters('hocwp_option_utilities', $options);
}

$args = hocwp_option_utilities();

$dashboard_widget = hocwp_get_value_by_key($args, 'dashboard_widget');

global $hocwp_tos_tabs;
$parent_slug = 'hocwp_theme_option';

$option = new HOCWP_Option(__('Utilities', 'hocwp'), 'hocwp_utilities');
$option->set_parent_slug($parent_slug);

$option->add_field(array('id' => 'link_manager', 'title' => __('Link Manager', 'hocwp'), 'label' => __('Enable link manager on your site.', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox'));
$option->add_field(array('id' => 'dashboard_widget', 'title' => __('Dashboard Widgets', 'hocwp'), 'default' => 1, 'value' => $dashboard_widget, 'label' => __('Display custom widget on Dashboard for Services News.', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox'));

$option->add_option_tab($hocwp_tos_tabs);
$option->set_page_header_callback('hocwp_theme_option_form_before');
$option->set_page_footer_callback('hocwp_theme_option_form_after');
$option->set_page_sidebar_callback('hocwp_theme_option_sidebar_tab');

$option->init();

hocwp_option_add_object_to_list($option);

function hocwp_dashboard_services_news_widget() {
	$args = hocwp_option_utilities();
	$dashboard_widget = hocwp_get_value_by_key($args, 'dashboard_widget');
	return (bool)apply_filters('hocwp_dashboard_services_news_widget', $dashboard_widget);
}