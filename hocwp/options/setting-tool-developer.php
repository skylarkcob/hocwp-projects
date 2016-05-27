<?php
if(!function_exists('add_filter')) exit;

global $pagenow;

$parent_slug = 'tools.php';

$option = new HOCWP_Option(__('Developers', 'hocwp'), 'hocwp_developers');
$option->set_parent_slug($parent_slug);
$option->disable_sidebar();

$option->add_field(array('id' => 'compress_css', 'title' => __('Compress CSS', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'label' => __('Compress all style in current theme or plugins?', 'hocwp'), 'default' => 1));
$option->add_field(array('id' => 'compress_js', 'title' => __('Compress Javascript', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'label' => __('Compress all javascript in current theme or plugins?', 'hocwp'), 'default' => 1));
$option->add_field(array('id' => 're_compress', 'title' => __('Recompress', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'label' => __('Check here if you want to recompress all minified files?', 'hocwp')));
$option->add_field(array('id' => 'force_compress', 'title' => __('Force Compress', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'label' => __('Disable compress cache each 15 minutes?', 'hocwp')));
$option->add_field(array('id' => 'compress_css_js', 'field_callback' => 'hocwp_field_button', 'value' => __('Compress CSS and Javascript', 'hocwp')));

if(HOCWP_DEVELOPING && hocwp_is_localhost()) {
	$option->init();
}

hocwp_option_add_object_to_list($option);