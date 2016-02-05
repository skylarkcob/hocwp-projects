<?php
if(!function_exists('add_filter')) exit;
function hocwp_get_term_link($term) {
    return '<a href="' . esc_url(get_term_link($term)) . '" rel="category tag">' . $term->name.'</a>';
}

function hocwp_get_hierarchical_terms($taxonomies, $args = array()) {
    if(!hocwp_array_has_value($taxonomies)) {
        $taxonomies = array('category');
    }
    $args['hierarchical'] = true;
    return get_terms($taxonomies, $args);
}

function hocwp_get_taxonomies($args = array()) {
    return get_taxonomies($args, 'objects');
}

function hocwp_get_hierarchical_taxonomies($args = array()) {
    $args['hierarchical'] = true;
    return hocwp_get_taxonomies($args);
}

function hocwp_term_meta_thumbnail_field($taxonomies = array()) {
    if(!hocwp_array_has_value($taxonomies)) {
        $taxonomies = array('category');
    }
    $meta = new HOCWP_Meta('term');
    $meta->set_taxonomies($taxonomies);
    $meta->add_field(array('id' => 'thumbnail', 'label' => __('Thumbnail', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload'));
    $meta->init();
}

function hocwp_term_get_thumbnail_url($args = array()) {
    $term_id = hocwp_get_value_by_key($args, 'term_id');
    if(!hocwp_id_number_valid($term_id)) {
        $term = hocwp_get_value_by_key($args, 'term');
        if(is_a($term, 'WP_Term')) {
            $term_id = $term->term_id;
        }
    }
    if(!hocwp_id_number_valid($term_id)) {
        $term_id = 0;
    }
    $value = get_term_meta($term_id, 'thumbnail', true);
    $use_default_term_thumbnail = apply_filters('hocwp_use_default_term_thumbnail', true);
    $value = hocwp_sanitize_media_value($value);
    $value = $value['url'];
    if(empty($value) && (bool)$use_default_term_thumbnail) {
        $value = hocwp_get_image_url('no-thumbnail.png');
    }
    $bfi_thumb = hocwp_get_value_by_key($args, 'bfi_thumb', true);
    if((bool)$bfi_thumb) {
        $size = hocwp_sanitize_size($args);
        $params = array();
        $width = $size[0];
        if(hocwp_id_number_valid($width)) {
            $params['width'] = $width;
        }
        $height = $size[1];
        if(hocwp_id_number_valid($height)) {
            $params['height'] = $height;
        }
        $value = bfi_thumb($value, $params);
    }
    return apply_filters('hocwp_term_thumbnail', $value, $term_id);
}

function hocwp_term_get_current() {
    return get_queried_object();
}

function hocwp_term_get_current_id() {
    return get_queried_object_id();
}

function hocwp_term_get_top_most_parent_ids($term) {
    $term_ids = array();
    if(is_a($term, 'WP_Term')) {
        $term_ids = get_ancestors($term->term_id, $term->taxonomy, 'taxonomy');
    }
    return $term_ids;
}

function hocwp_term_get_top_most_parent($term) {
    $term_ids = hocwp_term_get_top_most_parent_ids($term);
    $term_id = array_shift($term_ids);
    $parent = '';
    if(hocwp_id_number_valid($term_id)) {
        $parent = get_term($term_id, $term->taxonomy);
    }
    return $parent;
}