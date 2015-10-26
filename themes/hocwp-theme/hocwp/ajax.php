<?php
function hocwp_comment_likes_ajax_callback() {
    $result = array();
    $likes = isset($_POST['likes']) ? absint($_POST['likes']) : 0;
    $comment_id = isset($_POST['comment_id']) ? absint($_POST['comment_id']) : 0;
    $likes++;
    update_comment_meta($comment_id, 'likes', $likes);
    $result['likes'] = hocwp_number_format($likes);
    $_SESSION['comment_' . $comment_id . '_likes'] = 1;
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_comment_likes', 'hocwp_comment_likes_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_comment_likes', 'hocwp_comment_likes_ajax_callback');

function hocwp_comment_report_ajax_callback() {
    $result = array();
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_comment_report', 'hocwp_comment_report_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_comment_report', 'hocwp_comment_report_ajax_callback');

function hocwp_fetch_plugin_license_ajax_callback() {
    $result = array(
        'customer_email' => '',
        'license_code' => ''
    );
    $use_for = isset($_POST['use_for']) ? $_POST['use_for'] : '';
    if(!empty($use_for)) {
        $use_for_key = md5($use_for);
        $option = get_option('hocwp_plugin_licenses');
        $result['customer_email'] = hocwp_get_value_by_key($option, array($use_for_key, 'customer_email'));
        $result['license_code'] = hocwp_get_value_by_key($option, array($use_for_key, 'license_code'));
    }
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_fetch_plugin_license', 'hocwp_fetch_plugin_license_ajax_callback');

function hocwp_change_captcha_image_ajax_callback() {
    $result = array(
        'success' => false
    );
    $captcha = new HOCWP_Captcha();
    $url = $captcha->generate_image();
    if(!empty($url)) {
        $result['success'] = true;
        $result['captcha_image_url'] = $url;
    } else {
        $result['message'] = __('Sorry, cannot generate captcha image, please try again or contact administrator!', 'hocwp');
    }
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_change_captcha_image', 'hocwp_change_captcha_image_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_change_captcha_image', 'hocwp_change_captcha_image_ajax_callback');