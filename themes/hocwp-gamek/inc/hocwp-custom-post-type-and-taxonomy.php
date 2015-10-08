<?php
function hocwp_theme_register_post_type_and_taxonomy() {
    $args = array(
        'name' => 'Events',
        'singular_name' => 'Event',
        'slug' => 'event',
        'supports' => array('editor')
    );
    hocwp_register_post_type_private($args);

    $args = array(
        'name' => 'Labels',
        'singular_name' => 'Label',
        'slug' => 'label',
        'post_types' => array('post'),
        'hierarchical' => false
    );
    hocwp_register_taxonomy($args);

    $args = array(
        'name' => 'Video cats',
        'singular_name' => 'Video cat',
        'slug' => 'video_cat',
        'post_types' => array('post')
    );
    hocwp_register_taxonomy($args);
}
add_action('init', 'hocwp_theme_register_post_type_and_taxonomy', 0);