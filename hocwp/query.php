<?php
if(!function_exists('add_filter')) exit;
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

function hocwp_query_post_by_meta($meta_key, $meta_value, $args = array(), $meta_type = '', $compare = '=') {
    $meta_item = array(
        'key' => $meta_key,
        'value' => $meta_value,
        'type' => $meta_type,
        'compare' => $compare
    );
    $args = hocwp_query_sanitize_meta_query($meta_item, $args);
    return hocwp_query($args);
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
    $post_id = absint(isset($args['post_id']) ? $args['post_id'] : get_the_ID());
    if($post_id < 1) {
        return new WP_Query();
    }
    $posts_per_page = hocwp_get_value_by_key($args, 'posts_per_page', hocwp_get_posts_per_page());
    $transient_name = 'hocwp_post_' . $post_id . '_related_query_' . $posts_per_page;
    $cache = isset($args['cache']) ? $args['cache'] : true;
    if(!$cache || ($cache && false === ($query = get_transient($transient_name)))) {
        $taxonomies = get_post_taxonomies($post_id);
        $defaults = array();
        foreach($taxonomies as $taxonomy) {
            $tax = get_taxonomy($taxonomy);
            if((bool)$tax->hierarchical) {
                continue;
            }
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
        $query = hocwp_query($args);
        $posts_per_page = isset($query->query_vars['posts_per_page']) ? $query->query_vars['posts_per_page'] : hocwp_get_posts_per_page();
        if($query->post_count < $posts_per_page) {
            $missing = $posts_per_page - $query->post_count;
            $defaults = array();
            foreach($taxonomies as $taxonomy) {
                $tax = get_taxonomy($taxonomy);
                if(!(bool)$tax->hierarchical) {
                    continue;
                }
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
            $defaults['posts_per_page'] = $missing;
            unset($args['tax_query']);
            $args = wp_parse_args($args, $defaults);
            $cat_query = hocwp_query($args);
            $post_ids = array();
            foreach($query->posts as $post) {
                array_push($post_ids, $post->ID);
            }
            foreach($cat_query->posts as $post) {
                array_push($post_ids, $post->ID);
            }
            $args['posts_per_page'] = $posts_per_page;
            $args['post__in'] = $post_ids;
            $args['orderby'] = 'post__in';
            $query = hocwp_query($args);
        }
        $cache_days = apply_filters('hocwp_related_post_cache_days', 3);
        if(!$query->have_posts()) {
            $cache_days = 1;
        }
        if($cache) {
            set_transient($transient_name, $query, $cache_days * DAY_IN_SECONDS);
        }
    }
    return $query;
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