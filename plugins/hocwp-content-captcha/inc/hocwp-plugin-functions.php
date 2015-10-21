<?php
function hocwp_content_captcha_get_license_defined_data() {
    global $hocwp_content_captcha_license_data;
    $hocwp_content_captcha_license_data = hocwp_sanitize_array($hocwp_content_captcha_license_data);
    return apply_filters('hocwp_content_captcha_license_defined_data', $hocwp_content_captcha_license_data);
}

function hocwp_content_captcha_license_valid() {
    global $hocwp_content_captcha_license, $hocwp_content_captcha_license_valid;

    if(!hocwp_object_valid($hocwp_content_captcha_license)) {
        $hocwp_content_captcha_license = new HOCWP_License();
        $hocwp_content_captcha_license->set_type('plugin');
        $hocwp_content_captcha_license->set_use_for(HOCWP_CONTENT_CAPTCHA_BASENAME);
        $hocwp_content_captcha_license->set_option_name(HOCWP_PLUGIN_LICENSE_OPTION_NAME);
    }

    $hocwp_content_captcha_license_valid = $hocwp_content_captcha_license->check_valid(hocwp_content_captcha_get_license_defined_data());
    return $hocwp_content_captcha_license_valid;
}

function hocwp_content_captcha_get_post_types() {
    $post_types = hocwp_option_get_value('content_captcha', 'post_type');
    $post_types = hocwp_json_string_to_array($post_types);
    $lists = $post_types;
    $post_types = array();
    foreach($lists as $data) {
        $post_type = isset($data['id']) ? $data['id'] : '';
        if(!empty($post_type)) {
            $post_types[] = $post_type;
        }
    }
    return $post_types;
}

function hocwp_content_captcha_get_taxonomies() {
    $args = array(
        'object_type' => hocwp_content_captcha_get_post_types(),
        'hierarchical' => 1
    );
    return get_taxonomies($args);
}

function hocwp_content_captcha_get_terms() {
    $value = hocwp_option_get_value('content_captcha', 'category');
    $value = hocwp_json_string_to_array($value);
    $result = array();
    foreach($value as $data) {
        $id = isset($data['id']) ? $data['id'] : '';
        $id = absint($id);
        if($id > 0) {
            $taxonomy = isset($data['taxonomy']) ? $data['taxonomy'] : '';
            if(!empty($taxonomy)) {
                $item = get_term_by('id', $id, $taxonomy);
                if(hocwp_object_valid($item)) {
                    $result[] = $item;
                }
            }
        }
    }
    return $result;
}