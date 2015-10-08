<?php
function hocwp_query($args = array()) {
    if(!isset($args['post_type'])) {
        $args['post_type'] = 'post';
    }
    return new WP_Query($args);
}

function hocwp_query_product($args = array()) {
    $args['post_type'] = 'product';
    return hocwp_query($args);
}

function hocwp_query_post_by_category($term, $args = array()) {
    hocwp_query_sanitize_post_by_category($term, $args);
    return hocwp_query($args);
}

function hocwp_query_product_by_category($term, $args = array()) {
    hocwp_query_sanitize_post_by_category($term, $args);
    return hocwp_query_product($args);
}

function hocwp_query_sanitize_post_by_category($term, &$args = array()) {
    $tax_item = array(
        'taxonomy' => $term->taxonomy,
        'field' => 'id',
        'terms' => $term->term_id
    );
    hocwp_query_sanitize_tax_query($tax_item, $args);
    return $args;
}

function hocwp_query_sanitize_tax_query($tax_item, &$args) {
    if(is_array($args)) {
        if(!isset($args['tax_query']['relation'])) {
            $args['tax_query']['relation'] = 'OR';
        }
        if(isset($args['tax_query'])) {
            array_push($args['tax_query'], $tax_item);
        } else {
            $args['tax_query'] = array($tax_item);
        }
    }
    return $args;
}

function hocwp_query_featured($args = array()) {
    $args = hocwp_query_sanitize_featured_args($args);
    return hocwp_query($args);
}

function hocwp_query_sanitize_featured_args(&$args = array()) {
    $meta_item = array(
        'key' => 'featured',
        'value' => 1,
        'type' => 'NUMERIC'
    );
    hocwp_query_sanitize_meta_query($meta_item, $args);
    return $args;
}

function hocwp_query_sanitize_meta_query($item, &$args) {
    if(is_array($args)) {
        if(!isset($args['meta_query']['relation'])) {
            $args['meta_query']['relation'] = 'OR';
        }
        if(isset($args['meta_query'])) {
            array_push($args['meta_query'], $item);
        } else {
            $args['meta_query'] = array($item);
        }
    }
    return $args;
}

function hocwp_query_post_by_format($format, $args = array()) {
    $meta_item = array(
        'key' => 'post_format',
        'value' => $format
    );
    $args = hocwp_query_sanitize_meta_query($meta_item, $args);
    return hocwp_query($args);
}

function hocwp_query_related_post($args = array()) {
    $post_id = isset($args['post_id']) ? $args['post_id'] : get_the_ID();
    $taxonomies = isset($args['taxonomies']) ? $args['taxonomies'] : array('post_tag', 'category');
    $defaults = array();
    foreach($taxonomies as $taxonomy) {
        $term_ids = wp_get_post_terms($post_id, $taxonomies, array('fields' => 'ids'));
        if(hocwp_array_has_value($term_ids)) {
            $tax_item = array(
                'taxonomy' => $taxonomy,
                'field' => 'id',
                'terms' => $term_ids
            );
            $defaults = hocwp_query_sanitize_tax_query($tax_item, $defaults);
        }
    }
    $defaults['post__not_in'] = array($post_id);
    $defaults['tax_query']['relation'] = 'OR';
    $args = wp_parse_args($args, $defaults);
    return hocwp_query($args);
}

function hocwp_query_all($post_type) {
    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => -1
    );
    if('page' == $post_type) {
        $args['orderby'] = 'title';
        $args['order'] = 'ASC';
    }
    return hocwp_query($args);
}