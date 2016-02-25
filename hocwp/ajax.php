<?php
if(!function_exists('add_filter')) exit;
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
        $customer_email = hocwp_get_value_by_key($option, array($use_for_key, 'customer_email'));
        if(is_array($customer_email) || !is_email($customer_email)) {
            $customer_email = '';
        }
        $license_code = hocwp_get_value_by_key($option, array($use_for_key, 'license_code'));
        if(is_array($license_code) || strlen($license_code) < 5) {
            $license_code = '';
        }
        $result['customer_email'] = $customer_email;
        $result['license_code'] = $license_code;
        update_option('test', $result);
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

function hocwp_vote_post_ajax_callback() {
    $result = array(
        'success' => false
    );
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
    $post_id = absint($post_id);
    if($post_id > 0) {
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $session_name = 'hocwp_vote_' . $type . '_post_' . $post_id;
        if(!isset($_SESSION[$session_name]) || 1 != $_SESSION[$session_name]) {
            $value = isset($_POST['value']) ? $_POST['value'] : '';
            $value = absint($value);
            $value++;
            if('up' == $type || 'like' == $type) {
                update_post_meta($post_id, 'likes', $value);
            } elseif('down' == $type || 'dislike' == $type) {
                update_post_meta($post_id, 'dislikes', $value);
            }
            $result['value'] = $value;
            $result['type'] = $type;
            $result['post_id'] = $post_id;
            $result['value_html'] = number_format($value);
            $_SESSION[$session_name] = 1;
            $result['success'] = true;
        }
    }
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_vote_post', 'hocwp_vote_post_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_vote_post', 'hocwp_vote_post_ajax_callback');

function hocwp_sanitize_media_value_ajax_callback() {
    $id = isset($_POST['id']) ? $_POST['id'] : 0;
    $url = isset($_POST['url']) ? $_POST['url'] : '';
    $result = array('id' => $id, 'url' => $url);
    $result = hocwp_sanitize_media_value($result);
    echo json_encode($result);
    exit;
}
add_action('wp_ajax_hocwp_sanitize_media_value', 'hocwp_sanitize_media_value_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_sanitize_media_value', 'hocwp_sanitize_media_value_ajax_callback');