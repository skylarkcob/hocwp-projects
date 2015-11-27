<?php
if(!function_exists('add_filter')) exit;
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
    $player_id = hocwp_get_value_by_key($args, 'player_id', 'hocwp_player');
    if(empty($player_id)) {
        $player_id = 'hocwp_player';
    }

    if(!empty($video_code)) {
        if($height > 0) {
            $video_code = preg_replace('/height="(.*?)"/i', 'height="' . $height . '"', $video_code);
        }
        if($width > 0) {
            $video_code = preg_replace('/width="(.*?)"/i', 'width="' . $width . '"', $video_code);
        }
        $video_code = preg_replace('/id="(.*?)"/i', 'id="' . $player_id . '"', $video_code);
        if(!hocwp_string_contain($video_code, 'id="')) {
            $video_code = str_replace('<iframe', '<iframe id="' . $player_id . '"', $video_code);
        }
        $video_code = apply_filters('hocwp_video_code_result', $video_code, $args);
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
            $html = preg_replace('/id="(.*?)"/i', 'id="' . $player_id . '"', $html);
            if(!hocwp_string_contain($html, 'id="')) {
                $html = str_replace('<iframe', '<iframe id="' . $player_id . '"', $html);
            }
            $html = apply_filters('hocwp_embed_video_result', $html, $video_args);
            echo $html;
        }
    }

    $video_id = get_post_meta($post_id, 'video_id', true);
    if(!empty($video_id)) {
        $video_server = get_post_meta($post_id, 'video_server', true);
        if('youtube' == $video_server) {

        }
    }
}

function hocwp_detect_video_server_name($url) {
    $result = 'unknown';
    if(is_array($url)) {
        $url = array_shift($url);
    }
    if(false !== strrpos($url, 'youtube') || false !== strrpos($url, 'youtu.be')) {
        $result = 'youtube';
    } elseif(false !== strrpos($url, 'vimeo')) {
        $result = 'vimeo';
    } elseif(false !== strrpos($url, 'dailymotion') || false !== strrpos($url, 'dai.ly')) {
        $result = 'dailymotion';
    }
    return $result;
}

function hocwp_detect_video_id($url) {
    $result = '';
    if(is_array($url)) {
        $url = array_shift($url);
    }
    $server = hocwp_detect_video_server_name($url);
    $data = parse_url($url);
    $query = isset($data['query']) ? $data['query'] : '';
    parse_str($query, $output);
    switch($server) {
        case 'youtube':
            $result = isset($output['v']) ? $output['v'] : '';
            if(empty($result)) {
                $result = hocwp_get_last_part_in_url($url);
            }
            break;
        case 'vimeo':
            $result = intval(hocwp_get_last_part_in_url($url));
            break;
        case 'dailymotion':
            $result = hocwp_get_last_part_in_url($url);
            break;
    }
    return $result;
}

function hocwp_save_video_default_meta($post_id) {
    if(!is_numeric($post_id) || $post_id < 1) {
        return;
    }
    $video_url = get_post_meta($post_id, 'video_url');
    $server_name = hocwp_detect_video_server_name($video_url);
    update_post_meta($post_id, 'video_server', $server_name);
    $video_id = hocwp_detect_video_id($video_url);
    update_post_meta($post_id, 'video_id', $video_id);
    if(!has_post_thumbnail($post_id)) {
        $thumbnail_url = '';
        $thumbnails = array();
        switch($server_name) {
            case 'youtube':
                $api_key = hocwp_get_google_api_key();
                $data = hocwp_get_youtube_thumbnail_data_object($api_key, $video_id);
                $thumbnails = hocwp_get_youtube_thumbnails($api_key, $video_id, $data);
                $thumbnail_url = hocwp_get_youtube_thumbnail($api_key, $video_id, 'medium', $thumbnails);
                break;
            case 'vimeo':
                $thumbnails = hocwp_get_vimeo_thumbnails($video_id);
                $thumbnail_url = hocwp_get_vimeo_thumbnail($video_id, 'medium', $thumbnails);
                break;
            case 'dailymotion':
                $thumbnails = hocwp_get_dailymotion_thumbnails($video_id);
                $thumbnail_url = hocwp_get_dailymotion_thumbnail($video_id, 'medium', $thumbnails);
                break;
        }
        update_post_meta($post_id, 'thumbnail_url', $thumbnail_url);
        update_post_meta($post_id, 'thumbnails', $thumbnails);
    }
}

function hocwp_add_parameter_to_oembed_result($html, $url, $args) {
    $args['ogenerated'] = 'hocwp';
    $parameters = http_build_query($args);
    $html = str_replace('?feature=oembed', '?feature=oembed'. '&amp;' . $parameters, $html);
    return $html;
}
add_filter('oembed_result','hocwp_add_parameter_to_oembed_result', 99, 3);

function hocwp_get_youtube_data_object($api_key, $video_id) {
    $transient_name = 'hocwp_theme_youtube_' . $video_id . '_data_object';
    $transient_name = strtolower($transient_name);
    if(false === ($data = get_transient($transient_name))) {
        $data = file_get_contents('https://www.googleapis.com/youtube/v3/videos?key=' . $api_key . '&part=snippet&id=' . $video_id);
        $data = json_decode($data);
        set_transient($transient_name, $data, YEAR_IN_SECONDS);
    }
    return $data;
}

function hocwp_get_youtube_thumbnail_data_object($api_key, $video_id) {
    $transient_name = 'hocwp_youtube_' . $video_id . '_thumbnail_object';
    $transient_name = strtolower($transient_name);
    if(false === ($data = get_transient($transient_name))) {
        $data = hocwp_get_youtube_data_object($api_key, $video_id);
        $data = $data->items[0]->snippet->thumbnails;
        set_transient($transient_name, $data, YEAR_IN_SECONDS);
    }
    return $data;
}

function hocwp_get_valid_video_thumbnail_data($arr, $key) {
    return hocwp_find_valid_value_in_array($arr, $key);
}

function hocwp_get_valid_youtube_thumbnail($arr, $key) {
    $result = '';
    if(is_array($arr)) {
        if(isset($arr[$key])) {
            $result = isset($arr[$key]['url']) ? $arr[$key]['url'] : '';
        } else {
            $index = absint(count($arr)/2);
            if(isset($arr[$index])) {
                $last = $arr[$index];
            } else {
                $last = current($arr);
            }
            $result = isset($last['url']) ? $last['url'] : '';
        }
    }
    return $result;
}

function hocwp_get_youtube_thumbnails($api_key, $video_id, $data = null) {
    if(null == $data) {
        $data = hocwp_get_youtube_thumbnail_data_object($api_key, $video_id);
        $data = hocwp_std_object_to_array($data);
    } elseif(is_object($data)) {
        $data = hocwp_std_object_to_array($data);
    }
    $result = array(
        'small' => hocwp_get_value_by_key($data, array('default', 'url')),
        'medium' => hocwp_get_value_by_key($data, array('medium', 'url')),
        'high' => hocwp_get_value_by_key($data, array('high', 'url')),
        'standard' => hocwp_get_value_by_key($data, array('standard', 'url')),
        'large' => hocwp_get_value_by_key($data, array('maxres', 'url'))
    );
    return $result;
}

function hocwp_get_youtube_thumbnail($api_key, $video_id, $type = 'medium', $thumbnails = null) {
    if(!is_array($thumbnails)) {
        $thumbnails = hocwp_get_youtube_thumbnails($api_key, $video_id);
    }
    return hocwp_get_valid_video_thumbnail_data($thumbnails, $type);
}

function hocwp_get_youtube_thumbnail_url($api_key, $video_id, $type = 'medium', $data = null) {
    if(null == $data) {
        $data = hocwp_get_youtube_thumbnail_data_object($api_key, $video_id);
        $data = hocwp_std_object_to_array($data);
    } elseif(is_object($data)) {
        $data = hocwp_std_object_to_array($data);
    }
    $result = hocwp_get_valid_youtube_thumbnail($data, $type);
    return $result;
}

function hocwp_get_vimeo_data($id) {
    $transient_name = 'hocwp_vimeo_' . $id . '_data';
    $transient_name = strtolower($transient_name);
    if(false === ($data = get_transient($transient_name))) {
        $url = 'http://vimeo.com/api/v2/video/' . $id . '.php';
        $data = unserialize(file_get_contents($url));
        $data = isset($data[0]) ? $data[0] : array();
        set_transient($transient_name, $data, YEAR_IN_SECONDS);
    }
    return $data;
}

function hocwp_get_vimeo_thumbnails($id) {
    $data = hocwp_get_vimeo_data($id);
    $small = hocwp_get_value_by_key($data, 'thumbnail_small');
    $medium = hocwp_get_value_by_key($data, 'thumbnail_medium');
    $large = hocwp_get_value_by_key($data, 'thumbnail_large');
    $result = array(
        'thumbnail_small' => $small,
        'thumbnail_medium' => $medium,
        'thumbnail_large' => $large,
        'small' => $small,
        'medium' => $medium,
        'large' => $large
    );
    return $result;
}

function hocwp_get_vimeo_thumbnail($id, $type = 'medium', $thumbnails = null) {
    if(!is_array($thumbnails)) {
        $thumbnails = hocwp_get_vimeo_thumbnails($id);
    }
    return hocwp_get_valid_video_thumbnail_data($thumbnails, $type);
}

function hocwp_get_dailymotion_data($id) {
    $transient_name = 'hocwp_dailymotion_' . $id . '_data';
    $transient_name = strtolower($transient_name);
    if(false === ($data = get_transient($transient_name))) {
        $fields = array(
            'thumbnail_small_url',
            'thumbnail_medium_url',
            'thumbnail_large_url',
            'thumbnail_720_url'
        );
        $fields = apply_filters('hocwp_dailymotion_data_fields', $fields);
        $fields = implode(',', $fields);
        $url = 'https://api.dailymotion.com/video/' . $id . '?fields=' . $fields;
        $data = file_get_contents($url);
        $data = hocwp_json_string_to_array($data);
        set_transient($transient_name, $data, YEAR_IN_SECONDS);
    }
    return $data;
}

function hocwp_get_dailymotion_thumbnails($id) {
    $data = hocwp_get_dailymotion_data($id);
    $small = hocwp_get_value_by_key($data, 'thumbnail_small_url');
    $medium = hocwp_get_value_by_key($data, 'thumbnail_medium_url');
    $large = hocwp_get_value_by_key($data, 'thumbnail_large_url');
    $result = array(
        'thumbnail_small' => $small,
        'thumbnail_medium' => $medium,
        'thumbnail_large' => $large,
        'small' => $small,
        'medium' => $medium,
        'large' => $large
    );
    return $result;
}

function hocwp_get_dailymotion_thumbnail($id, $type = 'medium', $thumbnails = null) {
    if(!is_array($thumbnails)) {
        $thumbnails = hocwp_get_dailymotion_thumbnails($id);
    }
    return hocwp_get_valid_video_thumbnail_data($thumbnails, $type);
}