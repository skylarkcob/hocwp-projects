<?php
function hocwp_register_post_type_product() {
    $args = array(
        'name' => __('Products', 'hocwp'),
        'singular_name' => __('Product', 'hocwp'),
        'slug' => 'product',
        'menu_icon' => 'dashicons-products'
    );
    hocwp_register_post_type_normal($args);
}

function hocwp_register_taxonomy_product_cat() {
    $args = array(
        'name' => __('Product cats', 'hocwp'),
        'singular_name' => __('Product cat', 'hocwp'),
        'slug' => 'product_cat',
        'post_types' => 'product'
    );
    hocwp_register_taxonomy($args);
}

function hocwp_register_taxonomy_product_tag() {
    $args = array(
        'name' => __('Product tags', 'hocwp'),
        'singular_name' => __('Product tag', 'hocwp'),
        'slug' => 'product_tag',
        'post_types' => 'product'
    );
    hocwp_register_taxonomy($args);
}