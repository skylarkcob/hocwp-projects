<?php
function hocwp_custom_add_item_to_menu($items, $args) {
    if('primary' == $args->theme_location) {
        ob_start();
        hocwp_theme_the_logo();
        $logo = ob_get_clean();
        $items = '<li class="menu-item logo">' . $logo . '</li>' . $items;
        $search_form = hocwp_search_form(array('echo' => false, 'search_icon' => true, 'placeholder' => __('Tìm kiếm', 'hocwp') . '&hellip;'));
        $items .= '<li class="menu-item search pull-right">' . $search_form . '</li>';
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'hocwp_custom_add_item_to_menu', 10, 2);

function hocwp_theme_custom_widgets_init() {
    hocwp_register_sidebar('home_top_banner', __('Home top banner', 'hocwp'), __('Banner quảng cáo lớn ở phần trên của trang chủ.', 'hocwp'));
    hocwp_register_sidebar('post_bottom_banner', __('Post bottom banner', 'hocwp'), __('Banner quảng cáo nằm dưới chân nội dung bài viết.', 'hocwp'));
}
add_action('widgets_init', 'hocwp_theme_custom_widgets_init');

function hocwp_theme_custom_bold_post_first_praragraph($bold) {
    if(is_single()) {
        $bold = true;
    }
    return $bold;
}
add_filter('hocwp_bold_post_content_first_paragraph', 'hocwp_theme_custom_bold_post_first_praragraph');

function hocwp_theme_custom_excerpt_more_lenght($length) {
    $length = 23;
    return $length;
}
add_filter('excerpt_length', 'hocwp_theme_custom_excerpt_more_lenght');

function hocwp_theme_custom_excerpt_more($more) {
    return '&hellip;';
}
add_filter('excerpt_more', 'hocwp_theme_custom_excerpt_more');

function hocwp_theme_custom_widget_title($title) {
    if(!empty($title) && !hocwp_string_contain($title, 'span')) {
        $title = hocwp_wrap_tag($title, 'span');
    }
    return $title;
}
add_filter('widget_title', 'hocwp_theme_custom_widget_title');

function hocwp_theme_custom_use_addthis($use) {
    if(is_page() || is_single() || is_singular()) {
        $use = true;
    }
    return $use;
}
add_filter('hocwp_use_addthis', 'hocwp_theme_custom_use_addthis');

add_filter('hocwp_post_statistics', '__return_true');