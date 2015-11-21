<?php
$parent_slug = 'themes.php';

$theme = wp_get_theme();
$template = hocwp_sanitize_id($theme->get_template());

$option = new HOCWP_Option(__('Custom CSS', 'hocwp'), 'hocwp_theme_custom_css');
$option->set_parent_slug($parent_slug);
$option->add_field(array('id' => $template, 'title' => $theme->get('Name') . ' ' . __('Custom CSS', 'hocwp'), 'class' => 'widefat', 'row' => 30, 'field_callback' => 'hocwp_field_textarea'));

$option->init();
hocwp_option_add_object_to_list($option);