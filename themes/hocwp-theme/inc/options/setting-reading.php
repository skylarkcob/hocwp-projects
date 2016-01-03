<?php
if(!function_exists('add_filter')) exit;

global $hocwp_tos_tabs;
$option = new HOCWP_Option(__('Reading', 'hocwp'), 'hocwp_reading');
$option->set_parent_slug('hocwp_theme_option');
$option->add_field(array('id' => 'post_statistics', 'title' => __('Post Statistics', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'label' => __('Track post views on your site.', 'hocwp')));
$option->add_field(array('id' => 'sticky_widget', 'title' => __('Sticky Widget', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'label' => __('Make last widget fixed when scroll down.', 'hocwp')));
$option->add_section(array('id' => 'breadcrumb', 'title' => __('Breadcrumb', 'hocwp'), 'description' => __('Custom breadcrumb on your site.', 'hocwp')));
$option->add_field(array('id' => 'breadcrumb_label', 'title' => __('Breadcrumb Label', 'hocwp'), 'value' => hocwp_wpseo_internallink_value('breadcrumbs-prefix'), 'section' => 'breadcrumb'));
$option->add_field(array('id' => 'disable_post_title_breadcrumb', 'title' => __('Disable Post Title', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'label' => __('Prevent post title to be shown on last item.', 'hocwp'), 'section' => 'breadcrumb'));
$option->add_field(array('id' => 'link_last_item_breadcrumb', 'title' => __('Link Last Item', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'label' => __('Add link to last item instead of text.', 'hocwp'), 'section' => 'breadcrumb'));
$option->add_section(array('id' => 'scroll_top_section', 'title' => __('Scroll To Top', 'hocwp'), 'description' => __('This option can help you to display scroll to top button on your site.', 'hocwp')));
$option->add_field(array('id' => 'go_to_top', 'title' => __('Scroll Top Button', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'label' => __('Display scroll top to top button on bottom right of site.', 'hocwp'), 'section' => 'scroll_top_section'));
$option->add_field(array('id' => 'scroll_top_icon', 'title' => __('Button Icon', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload', 'section' => 'scroll_top_section'));

$option->add_option_tab($hocwp_tos_tabs);
$option->set_page_header_callback('hocwp_theme_option_form_before');
$option->set_page_footer_callback('hocwp_theme_option_form_after');
$option->set_page_sidebar_callback('hocwp_theme_option_sidebar_tab');
$option->init();
hocwp_option_add_object_to_list($option);

function hocwp_option_reading_update($input) {
    $breadcrumb_label = hocwp_get_value_by_key($input, 'breadcrumb_label');
    if(!empty($breadcrumb_label)) {
        $breadcrumb_label = hocwp_remove_last_char($breadcrumb_label, ':');
        $breadcrumb_label .= ':';
        $wpseo_internallinks = get_option('wpseo_internallinks');
        $wpseo_internallinks['breadcrumbs-prefix'] = $breadcrumb_label;
        update_option('wpseo_internallinks', $wpseo_internallinks);
    }
}
add_action('hocwp_sanitize_' . $option->get_option_name_no_prefix() . '_option', 'hocwp_option_reading_update');