<?php
if(!function_exists('add_filter')) exit;
$parent_slug = 'tools.php';

$defaults = hocwp_maintenance_mode_default_settings();

$option = new HOCWP_Option(__('Maintenance', 'hocwp'), 'hocwp_maintenance');
$option->set_parent_slug($parent_slug);
$option->set_use_media_upload(true);
$option->set_use_style_and_script(true);
$option->add_field(array('id' => 'enabled', 'title' => __('Enable', 'hocwp'), 'label' => __('Put your WordPress site in maintenance mode.', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox'));

$option->add_section(array('id' => 'front_end', 'title' => __('Front-end', 'hocwp'), 'description' => __('All settings to display on front-end.', 'hocwp')));
$option->add_field(array('id' => 'background', 'title' => __('Background', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload', 'section' => 'front_end'));
$option->add_field(array('id' => 'heading', 'title' => __('Heading', 'hocwp'), 'default' => hocwp_get_value_by_key($defaults, 'heading'), 'section' => 'front_end'));
$option->add_field(array('id' => 'text', 'title' => __('Text', 'hocwp'), 'default' => hocwp_get_value_by_key($defaults, 'text'), 'field_callback' => 'hocwp_field_rich_editor', 'section' => 'front_end'));
$option->init();
hocwp_option_add_object_to_list($option);