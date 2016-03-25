<?php
if(!function_exists('add_filter')) exit;

function hocwp_get_text($lang, $args = array()) {
	$text = apply_filters('hocwp_get_text', hocwp_get_value_by_key($args, $lang), $lang, $args);
	return $text;
}

function hocwp_text_error_default($lang = 'vi') {
	$text = hocwp_get_text($lang, array(
		'vi' => __('Đã có lỗi xảy ra, xin vui lòng thử lại!', 'hocwp'),
		'en' => __('There was an error occurred, please try again!', 'hocwp')
	));
	return apply_filters('hocwp_text_error_default', $text, $lang);
}

function hocwp_text_error_email_exists($lang = 'vi') {
	$text = hocwp_get_text($lang, array(
		'vi' => __('Địa chỉ email đã tồn tại!', 'hocwp'),
		'en' => __('Email address already exists!', 'hocwp')
	));
	return apply_filters('hocwp_text_error_email_exists', $text, $lang);
}

function hocwp_text_error_email_not_valid($lang = 'vi') {
	$text = hocwp_get_text($lang, array(
		'vi' => __('Địa chỉ email không đúng!', 'hocwp'),
		'en' => __('The email address is not correct!', 'hocwp')
	));
	return apply_filters('hocwp_text_error_email_not_valid', $text, $lang);
}

function hocwp_text_error_captcha_not_valid($lang = 'vi') {
	$text = hocwp_get_text($lang, array(
		'vi' => __('Mã bảo mật không đúng!', 'hocwp'),
		'en' => __('The captcha code is not correct!', 'hocwp')
	));
	return apply_filters('hocwp_text_error_captcha_not_valid', $text, $lang);
}

function hocwp_text_success_register_and_verify_email($lang = 'vi') {
	$text = hocwp_get_text($lang, array(
		'vi' => __('Bạn đã đăng ký thành công, xin vui lòng kiểm tra email để kích hoạt.', 'hocwp'),
		'en' => __('You have successfully registered, please check your email for activation.', 'hocwp')
	));
	return apply_filters('hocwp_text_success_register_and_verify_email', $text, $lang);
}

function hocwp_text_email_subject_verify_subscription($lang = 'vi') {
	$text = hocwp_get_text($lang, array(
		'vi' => __('Kích hoạt địa chỉ email của bạn tại: %s', 'hocwp'),
		'en' => __('Activate your Email Subscription to: %s', 'hocwp')
	));
	$text = sprintf($text, get_bloginfo('name'));
	return apply_filters('hocwp_text_email_subject_verify_subscription', $text, $lang);
}