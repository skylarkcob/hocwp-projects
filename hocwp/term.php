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