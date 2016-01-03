<?php
if(!function_exists('add_filter')) exit;

global $hocwp_tos_tabs;

$writing_option = new HOCWP_Option(__('Writing', 'hocwp'), 'hocwp_writing');
$writing_option->set_parent_slug('hocwp_theme_option');
$writing_option->add_field(array('id' => 'default_post_thumbnail', 'title' => __('Default post thumbnail', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload'));

$writing_option->add_option_tab($hocwp_tos_tabs);
$writing_option->set_page_header_callback('hocwp_theme_option_form_before');
$writing_option->set_page_footer_callback('hocwp_theme_option_form_after');
$writing_option->set_page_sidebar_callback('hocwp_theme_option_sidebar_tab');
$writing_option->init();
hocwp_option_add_object_to_list($writing_option);