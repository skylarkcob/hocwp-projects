<?php
if(!function_exists('add_filter')) exit;

global $hocwp_tos_tabs;
$parent_slug = 'hocwp_theme_option';

$option = new HOCWP_Option(__('Add to head', 'hocwp-theme'), 'hocwp_theme_add_to_head');
$option->set_parent_slug($parent_slug);
$option->add_field(array('id' => 'code', 'title' => __('Code', 'hocwp-theme'), 'class' => 'widefat', 'row' => 30, 'field_callback' => 'hocwp_field_textarea'));
$option->add_option_tab($hocwp_tos_tabs);
$option->set_page_header_callback('hocwp_theme_option_form_before');
$option->set_page_footer_callback('hocwp_theme_option_form_after');
$option->set_page_sidebar_callback('hocwp_theme_option_sidebar_tab');
$option->init();
hocwp_option_add_object_to_list($option);