<?php
if(!function_exists('add_filter')) exit;

function hocwp_option_home_setting_defaults() {
    $alls = hocwp_option_defaults();
    $defaults = hocwp_get_value_by_key($alls, 'home_setting');
    if(!hocwp_array_has_value($defaults)) {
        $defaults = array(
            'recent_posts' => 1,
            'posts_per_page' => hocwp_get_posts_per_page(),
            'pagination' => 1
        );
    }
    return apply_filters('hocwp_option_home_setting_defaults', $defaults);
}

function hocwp_option_home_setting() {
    $defaults = hocwp_option_home_setting_defaults();
    $options = get_option('hocwp_home_setting');
    $options = wp_parse_args($options, $defaults);
    return apply_filters('hocwp_option_home_setting', $options);
}

$options = hocwp_option_home_setting();
$posts_per_page = hocwp_get_value_by_key($options, 'posts_per_page');
$pagination = hocwp_get_value_by_key($options, 'pagination');
$recent_posts = hocwp_get_value_by_key($options, 'recent_posts');

global $hocwp_tos_tabs;
$parent_slug = 'hocwp_theme_option';

$option = new HOCWP_Option(__('Home Settings', 'hocwp-theme'), 'hocwp_home_setting');
$option->set_parent_slug($parent_slug);
$option->set_use_style_and_script(true);
$option->set_use_media_upload(true);

$option->add_field(array('id' => 'recent_posts', 'title' => __('Recent Posts', 'hocwp-theme'), 'label' => __('Show recent posts on home page?', 'hocwp-theme'), 'value' => $recent_posts, 'field_callback' => 'hocwp_field_input_checkbox'));
$option->add_field(array('id' => 'posts_per_page', 'title' => __('Posts Number', 'hocwp-theme'), 'value' => $posts_per_page, 'field_callback' => 'hocwp_field_input_number'));
$option->add_field(array('id' => 'pagination', 'title' => __('Pagination', 'hocwp-theme'), 'label' => __('Show pagination on home page?', 'hocwp-theme'), 'value' => $pagination, 'field_callback' => 'hocwp_field_input_checkbox'));

$option->add_option_tab($hocwp_tos_tabs);
$option->set_page_header_callback('hocwp_theme_option_form_before');
$option->set_page_footer_callback('hocwp_theme_option_form_after');
$option->set_page_sidebar_callback('hocwp_theme_option_sidebar_tab');

$option->init();

hocwp_option_add_object_to_list($option);