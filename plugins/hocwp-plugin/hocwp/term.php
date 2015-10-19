<?php
function hocwp_get_term_link($term) {
    return '<a href="' . esc_url(get_term_link($term)) . '" rel="category tag">' . $term->name.'</a>';
}