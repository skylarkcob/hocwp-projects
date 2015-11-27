<?php
if(!function_exists('add_filter')) exit;
$reading_option = new HOCWP_Option('', 'reading');
$reading_option->set_page('options-reading.php');
$reading_option->add_field(array('id' => 'post_statistics', 'title' => __('Post Statistics', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'label' => __('Track post views on your site.', 'hocwp')));
$reading_option->add_section(array('id' => 'scroll_top_section', 'title' => __('Scroll To Top', 'hocwp'), 'description' => __('This option can help you to display scroll to top button on your site.', 'hocwp')));
$reading_option->add_field(array('id' => 'go_to_top', 'title' => __('Scroll Top Button', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'label' => __('Display scroll top to top button on bottom right of site.', 'hocwp'), 'section' => 'scroll_top_section'));
$reading_option->add_field(array('id' => 'scroll_top_icon', 'title' => __('Button Icon', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload', 'section' => 'scroll_top_section'));
$reading_option->init();
hocwp_option_add_object_to_list($reading_option);