<?php
$meta = new HOCWP_Meta('post');
$meta->add_post_type('post');
$meta->set_title(__('Post Formats', 'hocwp'));
$meta->set_id('hocwp_theme_post_type');
$meta->set_context('side');
$meta->set_priority('core');
$field_options = array(
    array(
        'id' => 'post_format_default',
        'label' => __('Default', 'hocwp'),
        'option_value' => 'default'
    ),
    array(
        'id' => 'post_format_video',
        'label' => __('Video', 'hocwp'),
        'option_value' => 'video'
    ),
    array(
        'id' => 'post_format_giftcode',
        'label' => __('Giftcode', 'hocwp'),
        'option_value' => 'giftcode'
    ),
    array(
        'id' => 'post_format_review',
        'label' => __('Reviews', 'hocwp'),
        'option_value' => 'review'
    ),
    array(
        'id' => 'post_format_experience',
        'label' => __('Experience', 'hocwp'),
        'option_value' => 'experience'
    )
);
$meta->add_field(array('field_args' => array('id' => 'post_format'), 'field_callback' => 'hocwp_field_input_radio', 'options' => $field_options));
$meta->init();

$meta = new HOCWP_Meta('post');
$meta->add_post_type('post');
$meta->set_title(__('Game Location', 'hocwp'));
$meta->set_id('hocwp_theme_game_location');
$meta->set_context('side');
$meta->set_priority('core');
$field_options = array(
    array(
        'id' => 'game_location_vietnam',
        'label' => __('Vietnam', 'hocwp'),
        'option_value' => 'vietnam'
    ),
    array(
        'id' => 'game_location_abroad',
        'label' => __('Abroad', 'hocwp'),
        'option_value' => 'abroad'
    )
);
$meta->add_field(array('field_args' => array('id' => 'game_location'), 'field_callback' => 'hocwp_field_input_radio', 'options' => $field_options));
$meta->init();

$meta = new HOCWP_Meta('post');
$meta->add_post_type('event');
$meta->set_title(__('Event Information', 'hocwp'));
$meta->set_id('hocwp_theme_event_information');
$meta->add_field(array('field_args' => array('id' => 'event_url', 'label' => 'Event URL:')));
$meta->init();

hocwp_video_source_meta_box();