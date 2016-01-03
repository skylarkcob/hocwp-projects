<?php
if(!function_exists('add_filter')) exit;

$parent_slug = 'plugins.php';

$option = new HOCWP_Option(__('Custom CSS', 'hocwp'), 'hocwp_plugin_custom_css');
$option->set_parent_slug($parent_slug);
$option->add_field(array('id' => 'code', 'title' => __('Your Custom CSS Code', 'hocwp'), 'class' => 'widefat', 'row' => 30, 'field_callback' => 'hocwp_field_textarea'));

$option->init();
hocwp_option_add_object_to_list($option);