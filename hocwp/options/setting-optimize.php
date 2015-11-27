<?php
if(!function_exists('add_filter')) exit;
$parent_slug = 'options-general.php';

$option = new HOCWP_Option(__('Optimize', 'hocwp'), 'hocwp_optimize');
$option->set_parent_slug($parent_slug);
$option->add_field(array('id' => 'use_jquery_cdn', 'title' => __('jQuery CDN', 'hocwp'), 'label' => __('Load jQuery from Google CDN server.', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'default' => 1));

$option->init();
hocwp_option_add_object_to_list($option);