<?php
if(!function_exists('add_filter')) exit;

global $hocwp_tos_tabs;
$parent_slug = 'hocwp_theme_option';

$option = new HOCWP_Option(__('General', 'hocwp'), 'hocwp_theme_setting');
$option->set_parent_slug($parent_slug);
$option->set_use_style_and_script(true);
$option->set_use_media_upload(true);
$option->add_option_tab($hocwp_tos_tabs);
$option->add_field(array('id' => 'language', 'title' => __('Language', 'hocwp'), 'field_callback' => 'hocwp_field_select_language'));
$option->add_field(array('id' => 'favicon', 'title' => __('Favicon', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload'));
$option->add_field(array('id' => 'logo', 'title' => __('Logo', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload'));
$option->set_page_header_callback('hocwp_theme_option_form_before');
$option->set_page_footer_callback('hocwp_theme_option_form_after');
$option->set_page_sidebar_callback('hocwp_theme_option_sidebar_tab');
$option->init();
hocwp_option_add_object_to_list($option);