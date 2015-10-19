<?php
function hocwp_video_source_meta_box($post_types = array()) {
    if(!hocwp_array_has_value($post_types)) {
        $post_types[] = 'post';
    }
    $meta = new HOCWP_Meta('post');
    $meta->set_post_types($post_types);
    $meta->set_title(__('Video Source Information', 'hocwp'));
    $meta->set_id('hocwp_theme_video_source_information');
    $meta->add_field(array('field_args' => array('id' => 'video_url', 'label' => 'Video URL:')));
    $meta->add_field(array('field_args' => array('id' => 'video_code', 'label' => 'Video code:'), 'field_callback' => 'hocwp_field_textarea'));
    $meta->init();
}

function hocwp_video_play($args = array()) {
    $post_id = isset($args['post_id']) ? $args['post_id'] : get_the_ID();
    $video_url = get_post_meta($post_id, 'video_url', true);
    $video_code = get_post_meta($post_id, 'video_code', true);
    $autoplay = isset($args['autoplay']) ? $args['autoplay'] : false;
    $width = isset($args['width']) ? $args['width'] : '';
    $height = isset($args['height']) ? $args['height'] : '';
    $rel = isset($args['rel']) ? $args['rel'] : false;
    $cc = isset($args['cc_load_policy']) ? $args['cc_load_policy'] : false;
    $iv = isset($args['iv_load_policy']) ? $args['iv_load_policy'] : false;
    $showinfo = isset($args['showinfo']) ? $args['showinfo'] : false;
    if(!empty($video_code)) {
        if($height > 0) {
            $video_code = preg_replace('/height="(.*?)"/i', 'height="' . $height . '"', $video_code);
        }
        if($width > 0) {
            $video_code = preg_replace('/width="(.*?)"/i', 'width="' . $width . '"', $video_code);
        }
        echo $video_code;
    } else {
        if(!empty($video_url)) {
            $video_args = array(
                'rel' => 0,
                'showinfo' => 0,
                'cc_load_policy' => 0,
                'iv_load_policy' => 3,
                'start' => 1
            );
            if($showinfo) {
                $video_args['showinfo'] = 1;
            }
            if($cc) {
                $video_args['cc_load_policy'] = 1;
            }
            if($iv) {
                $video_args['iv_load_policy'] = 1;
            }
            if((bool)$autoplay) {
                $video_args['autoplay'] = 1;
            }
            if($rel) {
                $video_args['rel'] = 1;
            }
            $video_args = apply_filters('hocwp_embed_video_args', $video_args);
            $html = wp_oembed_get($video_url, $video_args);
            if($height > 0) {
                $html = preg_replace('/height="(.*?)"/i', 'height="' . $height . '"', $html);
            }
            if($width > 0) {
                $html = preg_replace('/width="(.*?)"/i', 'width="' . $width . '"', $html);
            }
            $html = apply_filters('hocwp_embed_video_result', $html, $video_args);
            echo $html;
        }
    }
}

function hocwp_add_parameter_to_oembed_result($html, $url, $args) {
    $args['ogenerated'] = 'hocwp';
    $parameters = http_build_query($args);
    $html = str_replace('?feature=oembed', '?feature=oembed'. '&amp;' . $parameters, $html);
    return $html;
}
add_filter('oembed_result','hocwp_add_parameter_to_oembed_result', 99, 3);