<?php
if(!function_exists('add_filter')) exit;
$discussion_option = new HOCWP_Option('', 'discussion');
$discussion_option->set_page('options-discussion.php');
$discussion_option->add_field(array('id' => 'allow_shortcode', 'title' => __('Shortcode', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'label' => __('Allow user to post shortcode in comment.', 'hocwp')));
$discussion_option->add_section(array('id' => 'comment_form', 'title' => __('Comment Form', 'hocwp'), 'description' => __('These options can help you to customize comment form on your site.', 'hocwp')));
$field_options = array(
	array(
		'id' => 'comment_system_default',
		'label' => __('Use WordPress default comment system.', 'hocwp'),
		'option_value' => 'default'
	),
	array(
		'id' => 'comment_system_facebook',
		'label' => __('Use Facebook comment system.', 'hocwp'),
		'option_value' => 'facebook'
	),
	array(
		'id' => 'comment_system_default_and_facebook',
		'label' => __('Display bold WordPress default comment system and Facebook comment system.', 'hocwp'),
		'option_value' => 'default_and_facebook'
	)
);
$discussion_option->add_field(array('id' => 'comment_system', 'title' => __('Comment System', 'hocwp'), 'field_callback' => 'hocwp_field_input_radio', 'options' => $field_options, 'section' => 'comment_form'));
$field_options = array(
	array(
		'id' => 'use_captcha',
		'label' => __('Use captcha to validate human on comment form.', 'hocwp'),
		'default' => 0
	),
	array(
		'id' => 'user_no_captcha',
		'label' => __('Disable captcha if user is logged in.', 'hocwp'),
		'default' => 1
	)
);
$discussion_option->add_field(array('id' => 'captcha', 'title' => __('Captcha', 'hocwp'), 'options' => $field_options, 'field_callback' => 'hocwp_field_input_checkbox', 'section' => 'comment_form'));
$discussion_option->init();
hocwp_option_add_object_to_list($discussion_option);