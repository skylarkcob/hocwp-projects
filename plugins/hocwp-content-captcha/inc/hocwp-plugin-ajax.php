<?php
if(!hocwp_content_captcha_license_valid()) {
	return;
}

function hocwp_content_captcha_ajax_callback() {
	$post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
	$post_id = absint($post_id);
	$result = array(
		'success' => false
	);
	if($post_id > 0) {
		$content_captcha = isset($_POST['content_captcha']) ? $_POST['content_captcha'] : 0;
		if(0 == $content_captcha) {
			$content_captcha = 1;
		} else {
			$content_captcha = 0;
		}
		update_post_meta($post_id, 'content_captcha', $content_captcha);
		$result['success'] = true;
	}
	echo json_encode($result);
	die();
}
add_action('wp_ajax_hocwp_content_captcha', 'hocwp_content_captcha_ajax_callback');