<?php
if(!function_exists('add_filter')) exit;

global $hocwp_tos_tabs;
$parent_slug = 'hocwp_theme_option';

$theme = wp_get_theme();
$template = hocwp_sanitize_id($theme->get_template());

$option = new HOCWP_Option(__('Custom CSS', 'hocwp'), 'hocwp_theme_custom_css');
$option->set_parent_slug($parent_slug);
$option->add_field(array('id' => $template, 'title' => __('Theme Custom CSS', 'hocwp'), 'class' => 'widefat', 'row' => 30, 'field_callback' => 'hocwp_field_textarea'));
$option->add_option_tab($hocwp_tos_tabs);
$option->set_page_header_callback('hocwp_theme_option_form_before');
$option->set_page_footer_callback('hocwp_theme_option_form_after');
$option->set_page_sidebar_callback('hocwp_theme_option_sidebar_tab');
$option->init();
hocwp_option_add_object_to_list($option);