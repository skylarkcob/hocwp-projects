<?php
$parent_slug = 'themes.php';

$option_theme_setting = new HOCWP_Option(__('Theme settings', 'hocwp'), 'hocwp_theme_setting');
$option_theme_setting->set_parent_slug($parent_slug);
$option_theme_setting->set_use_style_and_script(true);
$option_theme_setting->set_use_media_upload(true);
$option_theme_setting->add_field(array('id' => 'favicon', 'title' => __('Favicon', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload'));
$option_theme_setting->add_field(array('id' => 'logo', 'title' => __('Logo', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload'));
$option_theme_setting->init();
hocwp_option_add_object_to_list($option_theme_setting);