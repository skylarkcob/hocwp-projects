<?php
hocwp_theme_add_setting_field(array('id' => 'mobile_logo', 'title' => __('Mobile Logo', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload'));
hocwp_theme_add_setting_field_select_page('search_page', __('Search Page', 'hocwp'));
hocwp_theme_add_setting_field(array('title' => __('Footer Left Text', 'hocwp'), 'id' => 'footer_left_text', 'field_callback' => 'hocwp_field_editor'));
hocwp_theme_add_setting_field(array('title' => __('Footer Right Text', 'hocwp'), 'id' => 'footer_right_text', 'field_callback' => 'hocwp_field_editor'));