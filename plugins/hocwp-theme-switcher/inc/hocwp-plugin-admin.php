<?php
$parent_slug = 'options-general.php';
$option_theme_switcher = new HOCWP_Option(__('Theme switcher', 'hocwp'), 'hocwp_theme_switcher');
$option_theme_switcher->set_parent_slug($parent_slug);
$option_theme_switcher->add_field(array('id' => 'mobile_theme', 'title' => __('Default Mobile Theme', 'hocwp'), 'field_callback' => 'hocwp_field_select_theme'));
if(hocwp_theme_switcher_enabled()) {
    $option_theme_switcher->init();
}
hocwp_option_add_object_to_list($option_theme_switcher);