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

function hocwp_crop_image($args = array()) {
    $attachment_id = hocwp_get_value_by_key($args, 'attachment_id');
    $url = hocwp_get_value_by_key($args, 'url');
    if(!hocwp_id_number_valid($attachment_id) && !empty($url)) {
        $attachment_id = hocwp_get_media_id($url);
    }
    if(!hocwp_id_number_valid($attachment_id)) {
        if(empty($url)) {
            return new WP_Error('crop_image_size', __('Attachment ID is not valid.', 'hocwp'));
        } else {
            $cropped = $url;
        }
    } else {
        $file_path = hocwp_get_media_file_path($attachment_id);
        $width = hocwp_get_value_by_key($args, 'width');
        $height = hocwp_get_value_by_key($args, 'height');
        $size = hocwp_get_image_sizes($attachment_id);
        $size = hocwp_sanitize_size($size);
        $base_url = '';
        if(empty($width) && empty($height)) {
            $cropped = $file_path;
        } else {
            if(empty($width)) {
                $width = $size[0];
            }
            if(empty($height)) {
                $height = $size[1];
            }
            $x = apply_filters('hocwp_crop_image_x', 0, $args);
            $y = apply_filters('hocwp_crop_image_y', 0, $args);
            $x = hocwp_get_value_by_key($args, 'x', $x);
            $y = hocwp_get_value_by_key($args, 'y', $y);
            $dest_file = hocwp_get_value_by_key($args, 'dest_file', '');
            $path_info = pathinfo($file_path);
            if(empty($dest_file)) {
                $upload_dir = hocwp_get_upload_folder_details();
                $base_path = apply_filters('hocwp_custom_thumbnail_base_path', untrailingslashit($upload_dir['path']) . '/hocwp/thumbs/', $args);
                if(!file_exists($base_path)) {
                    mkdir($base_path);
                }
                $base_url = apply_filters('hocwp_custom_thumbnail_base_url', untrailingslashit($upload_dir['url']) . '/hocwp/thumbs/', $args);
                $filename = $path_info['filename'];
                $dest_file = $base_path . str_replace($filename, $filename . '-' . $width . '-' . $height, basename($file_path));
            }
            if(file_exists($dest_file)) {
                $override = hocwp_get_value_by_key($args, 'override', false);
                if($override) {
                    unlink($dest_file);
                    $cropped = wp_crop_image($attachment_id, $x, $y, $size[0], $size[1], $width, $height, false, $dest_file);
                } else {
                    $cropped = $dest_file;
                }
            } else {
                $cropped = wp_crop_image($attachment_id, $x, $y, $size[0], $size[1], $width, $height, false, $dest_file);
            }
        }
    }
    $output = hocwp_get_value_by_key($args, 'output', 'url');
    if('url' == $output) {
        $cropped = hocwp_media_path_to_url($attachment_id, $cropped, $base_url);
    }
    return apply_filters('hocwp_crop_image', $cropped, $args);
}

function hocwp_media_path_to_url($attachment_id, $file_path, $base_url = '') {
    if(empty($base_url)) {
        $parent_url = wp_get_attachment_url($attachment_id);
        $url = str_replace(basename($parent_url), basename($file_path), $parent_url);
    } else {
        $url = trailingslashit($base_url) . basename($file_path);
    }
    return apply_filters('hocwp_media_path_to_url', $url, $attachment_id, $file_path);
}

function hocwp_post_thumbnail_by_ajax($url, $thumbnail_url, $params) {
    if(HOCWP_DOING_AJAX) {
        $params['url'] = $thumbnail_url;
        $params['ajax_thumbnail'] = true;
        $url = hocwp_crop_image($params);
    }
    return $url;
}
add_filter('hocwp_pre_bfi_thumb', 'hocwp_post_thumbnail_by_ajax', 10, 3);