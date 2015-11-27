<?php
if(!function_exists('add_filter')) exit;
function hocwp_media_sanitize_upload_file_name($file) {
    $file_name = isset($file['name']) ? $file['name'] : '';
    $file['name'] = hocwp_sanitize_file_name($file_name);
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'hocwp_media_sanitize_upload_file_name');

function hocwp_get_media_file_path($media_id) {
    return get_attached_file($media_id);
}