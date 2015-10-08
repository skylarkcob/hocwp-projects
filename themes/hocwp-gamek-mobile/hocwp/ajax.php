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