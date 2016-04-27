<?php
if(!function_exists('add_filter')) exit;

function hocwp_option_theme_setting_defaults() {
    $alls = hocwp_option_defaults();
    $defaults = hocwp_get_value_by_key($alls, 'theme_setting');
    if(!hocwp_array_has_value($defaults)) {
        $defaults = array(
            'language' => 'vi'
        );
    }
    return apply_filters('hocwp_option_theme_setting_defaults', $defaults);
}

function hocwp_option_theme_setting() {
    $defaults = hocwp_option_theme_setting_defaults();
    $options = get_option('hocwp_theme_setting');
    $options = wp_parse_args($options, $defaults);
    return apply_filters('hocwp_option_theme_setting', $options);
}

global $hocwp_tos_tabs;
$parent_slug = 'hocwp_theme_option';

$option = new HOCWP_Option(__('General', 'hocwp'), 'hocwp_theme_setting');
$option->set_parent_slug($parent_slug);
$option->set_use_style_and_script(true);
$option->set_use_media_upload(true);

$option->add_field(array('id' => 'language', 'title' => __('Language', 'hocwp'), 'field_callback' => 'hocwp_field_select_language'));
$option->add_field(array('id' => 'favicon', 'title' => __('Favicon', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload'));
$option->add_field(array('id' => 'logo', 'title' => __('Logo', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload'));

$option->add_option_tab($hocwp_tos_tabs);
$option->set_page_header_callback('hocwp_theme_option_form_before');
$option->set_page_footer_callback('hocwp_theme_option_form_after');
$option->set_page_sidebar_callback('hocwp_theme_option_sidebar_tab');

$option->init();

hocwp_option_add_object_to_list($option);