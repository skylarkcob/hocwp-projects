<?php
if(!function_exists('add_filter')) exit;

global $hocwp_pos_tabs;
$parent_slug = 'hocwp_plugin_option';

$option = new HOCWP_Option(__('Custom CSS', 'hocwp'), 'hocwp_plugin_custom_css');
$option->set_parent_slug($parent_slug);
$option->add_field(array('id' => 'code', 'title' => __('Your Custom CSS Code', 'hocwp'), 'class' => 'widefat', 'row' => 30, 'field_callback' => 'hocwp_field_textarea'));
$option->add_option_tab($hocwp_pos_tabs);
$option->set_page_header_callback('hocwp_plugin_option_page_header');
$option->set_page_footer_callback('hocwp_plugin_option_page_footer');
$option->set_page_sidebar_callback('hocwp_plugin_option_page_sidebar');
$option->init();
hocwp_option_add_object_to_list($option);