<?php
if(!function_exists('add_filter')) exit;
$parent_slug = 'themes.php';

$option = new HOCWP_Option(__('Add to head', 'hocwp'), 'hocwp_theme_add_to_head');
$option->set_parent_slug($parent_slug);
$option->add_field(array('id' => 'code', 'title' => __('Cocde', 'hocwp'), 'class' => 'widefat', 'row' => 30, 'field_callback' => 'hocwp_field_textarea'));

$option->init();
hocwp_option_add_object_to_list($option);