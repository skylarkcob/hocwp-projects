<?php
if(!hocwp_content_captcha_license_valid()) {
	return;
}

$option = new HOCWP_Option('Content Captcha', 'hocwp_content_captcha');
$option->set_parent_slug('options-general.php');
$option->set_use_style_and_script(true);
$option->set_use_jquery_ui_sortable(true);
$option->add_section(array('id' => 'recaptcha', 'title' => 'reCaptcha', 'description' => ''));
$option->add_field(array('id' => 'use_session', 'title' => 'Session', 'label' => 'Allow user to view post\'s content after pass captcha for a session.', 'field_callback' => 'hocwp_field_input_checkbox'));
$option->add_field(array('id' => 'post_type', 'title' => __('Post Types', 'hocwp-content-captcha'), 'field_callback' => 'hocwp_field_sortable_post_type', 'connect' => true));
$option->add_field(array('id' => 'category', 'title' => __('Categories', 'hocwp-content-captcha'), 'field_callback' => 'hocwp_field_sortable_term', 'connect' => true, 'taxonomies' => hocwp_content_captcha_get_taxonomies()));
$option->add_field(array('id' => 'site_key', 'title' => __('Site key', 'hocwp-content-captcha'), 'section' => 'recaptcha'));
$option->add_field(array('id' => 'secret_key', 'title' => __('Secret key', 'hocwp-content-captcha'), 'section' => 'recaptcha'));
$option->init();
hocwp_option_add_object_to_list($option);