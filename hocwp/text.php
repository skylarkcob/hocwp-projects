<?php
if(!function_exists('add_filter')) exit;

function hocwp_translate_text($text, $echo = false) {
	if(hocwp_qtranslate_x_installed()) {
		$mo = new HOCWP_MO();
		$id = $mo->get_id($text);
		if(hocwp_id_number_valid($id)) {
			$post = get_post($id);
			if(is_a($post, 'WP_Post')) {
				if(!empty($post->post_content)) {
					$text = $post->post_content;
				}
			}
		}
		$three_chars = substr($text, -3);
		if('[:]' == $three_chars) {
			$text = apply_filters('translate_text', $text);
		}
	}
	if($echo) {
		echo $text;
	}
	return $text;
}

function hocwp_text($vi, $en, $echo = true) {
	$lang = hocwp_get_language();
	if(function_exists('qtranxf_getLanguage')) {
		$lang = qtranxf_getLanguage();
	}
	if('vi' == $lang) {
		$text = $vi;
	} else {
		$text = $en;
	}
	$text = apply_filters('hocwp_text', $text, $vi, $en, $echo);
	if($echo) {
		echo $text;
	}
	return $text;
}

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