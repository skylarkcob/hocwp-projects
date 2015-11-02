<?php
function hocwp_theme_check_load_facebook_javascript_sdk() {
    $data = apply_filters('hocwp_load_facebook_javascript_sdk_on_page_sidebar', array());
    foreach($data as $value) {
        $conditional_functions = isset($value['condition']) ? $value['condition'] : '';
        $conditional_functions = hocwp_sanitize_array($conditional_functions);
        $condition_result = false;
        foreach($conditional_functions as $function) {
            if(!hocwp_callback_exists($function)) {
                continue;
            }
            if(call_user_func($function)) {
                $condition_result = true;
                break;
            }
        }
        $sidebar = isset($value['sidebar']) ? $value['sidebar'] : '';
        $sidebars = hocwp_sanitize_array($sidebar);
        foreach($sidebars as $sidebar) {
            if(is_active_sidebar($sidebar) && $condition_result && hocwp_sidebar_has_widget($sidebar, 'hocwp_widget_facebook_box')) {
                return true;
            }
        }
    }
    $comment_system = hocwp_theme_get_option('comment_system', 'discussion');
    if('facebook' == $comment_system || 'default_and_facebook' == $comment_system) {
        if(comments_open() || get_comments_number()) {
            return true;
        }
    }
    return false;
}
add_filter('hocwp_use_facebook_javascript_sdk', 'hocwp_theme_check_load_facebook_javascript_sdk');

function hocwp_setup_theme_add_facebook_javascript_sdk() {
    if(hocwp_use_facebook_javascript_sdk()) {
        $args = array();
        $app_id = hocwp_get_wpseo_social_value('fbadminapp');
        if(!empty($app_id)) {
            $args['app_id'] = $app_id;
        }
        hocwp_facebook_javascript_sdk($args);
    }
}
add_action('hocwp_close_body', 'hocwp_setup_theme_add_facebook_javascript_sdk');

function hocwp_more_mce_buttons_toolbar_1($buttons) {
    if(!hocwp_use_full_mce_toolbar()) {
        return $buttons;
    }
    $tmp = $buttons;
    unset($buttons);
    $buttons[] = 'fontselect';
    $buttons[] = 'fontsizeselect';
    $last = array_pop($tmp);
    $buttons = array_merge($buttons, $tmp);
    $buttons[] = 'styleselect';
    $buttons[] = $last;
    return $buttons;
}
add_filter('mce_buttons', 'hocwp_more_mce_buttons_toolbar_1');

function hocwp_more_mce_buttons_toolbar_2($buttons) {
    if(!hocwp_use_full_mce_toolbar()) {
        return $buttons;
    }
    $buttons[] = 'subscript';
    $buttons[] = 'superscript';
    $buttons[] = 'hr';
    $buttons[] = 'cut';
    $buttons[] = 'copy';
    $buttons[] = 'paste';
    $buttons[] = 'backcolor';
    $buttons[] = 'newdocument';
    return $buttons;
}
add_filter('mce_buttons_2', 'hocwp_more_mce_buttons_toolbar_2');

function hocwp_load_addthis_script() {
    $use = apply_filters('hocwp_use_addthis', false);
    if($use) {
        hocwp_addthis_script();
    }
}
add_action('wp_footer', 'hocwp_load_addthis_script');

unset($GLOBALS['wpdb']->dbpassword);
unset($GLOBALS['wpdb']->dbname);

function hocwp_theme_custom_check_license() {
    $option = get_option('hocwp_cancel_license');
    $theme_key = md5(get_option('template'));
    $cancel = absint(isset($option['theme'][$theme_key]) ? $option['theme'][$theme_key] : '');
    if(1 == $cancel || !has_action('hocwp_check_license', 'hocwp_setup_theme_check_license')) {
        hocwp_theme_invalid_license_redirect();
    }
}
add_action('hocwp_check_license', 'hocwp_theme_custom_check_license');

function hocwp_theme_post_submitbox_misc_actions() {
    global $post;
    if(!hocwp_object_valid($post)) {
        return;
    }
    $post_type = $post->post_type;
    $post_types = hocwp_post_type_no_featured_field();
    if(!in_array($post_type, $post_types)) {
        $key = 'featured';
        $value = get_post_meta($post->ID, $key, true);
        $args = array(
            'id' => 'hocwp_featured_post',
            'name' => $key,
            'value' => $value,
            'label' => __('Featured?', 'hocwp')
        );
        hocwp_field_publish_box('hocwp_field_input_checkbox', $args);
    }
    do_action('hocwp_publish_box_field');
}
add_action('post_submitbox_misc_actions', 'hocwp_theme_post_submitbox_misc_actions');

function hocwp_theme_use_admin_style_and_script($use) {
    global $pagenow;
    if('edit.php' == $pagenow) {
        $use = true;
    }
    return $use;
}
add_filter('hocwp_use_admin_style_and_script', 'hocwp_theme_use_admin_style_and_script');

function hocwp_theme_post_column_head_featured($columns) {
    global $post_type;
    $exclude_types = hocwp_post_type_no_featured_field();
    if(!in_array($post_type, $exclude_types)) {
        $columns['featured'] = __('Featured', 'hocwp');
    }
    return $columns;
}
add_filter('manage_posts_columns', 'hocwp_theme_post_column_head_featured');

function hocwp_theme_post_column_content_featured($column, $post_id) {
    if('featured' == $column) {
        hocwp_icon_circle_ajax($post_id, 'featured');
    }
}
add_action('manage_posts_custom_column', 'hocwp_theme_post_column_content_featured', 10, 2);

function hocwp_theme_switcher_ajax_ajax_callback() {
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
    $post_id = absint($post_id);
    $result = array(
        'success' => false
    );
    if($post_id > 0) {
        $value = isset($_POST['value']) ? $_POST['value'] : 0;
        if(0 == $value) {
            $value = 1;
        } else {
            $value = 0;
        }
        $key = isset($_POST['key']) ? $_POST['key'] : '';
        if(!empty($key)) {
            update_post_meta($post_id, $key, $value);
            $result['success'] = true;
        }
    }
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_switcher_ajax', 'hocwp_theme_switcher_ajax_ajax_callback');

function hocwp_theme_save_post_featured_meta($post_id) {
    if(!hocwp_can_save_post($post_id)) {
        return $post_id;
    }
    $value = isset($_POST['featured']) ? 1 : 0;
    update_post_meta($post_id, 'featured', $value);
    return $post_id;
}
add_action('save_post', 'hocwp_theme_save_post_featured_meta');

function hocwp_theme_last_widget_fixed() {
    $fixed = apply_filters('hocwp_theme_last_widget_fixed', true);
    if($fixed) {
        get_template_part('hocwp/theme/fixed-widget');
    }
}
add_action('hocwp_close_body', 'hocwp_theme_last_widget_fixed');

function hocwp_bold_first_paragraph($content) {
    $bold = apply_filters('hocwp_bold_post_content_first_paragraph', false);
    if($bold) {
        return preg_replace('/<p([^>]+)?>/', '<p$1 class="first-paragraph">', $content, 1);
    }
    return $content;
}
add_filter('the_content', 'hocwp_bold_first_paragraph');

function hocwp_theme_add_full_screen_loading() {
    get_template_part('/hocwp/theme/ajax-loading', 'full-screen');
}
add_action('hocwp_close_body', 'hocwp_theme_add_full_screen_loading');

function hocwp_setup_theme_after_go_to_top_button() {
    $button = (bool)hocwp_option_get_value('reading', 'go_to_top');
    $button = apply_filters('hocwp_theme_go_to_top_button', $button);
    if($button) {
        $icon = hocwp_option_get_value('reading', 'scroll_top_icon');
        $icon = hocwp_sanitize_media_value($icon);
        $icon = $icon['url'];
        $class = 'hocwp-go-top';
        if(empty($icon)) {
            $icon = '<i class="fa fa-chevron-up"></i>';
            hocwp_add_string_with_space_before($class, 'icon-default');
        }
        $icon = apply_filters('hocwp_theme_go_to_top_button_icon', $icon);
        if(hocwp_url_valid($icon)) {
            $icon = '<img src="' . $icon . '">';
            hocwp_add_string_with_space_before($class, 'icon-image');
        }
        $a = new HOCWP_HTML('a');
        $a->set_attribute('id', 'hocwp_go_top');
        $a->set_text($icon);
        $a->set_attribute('href', '#');
        $a->set_attribute('class', $class);
        $a->output();
    }
}
add_action('hocwp_before_wp_footer', 'hocwp_setup_theme_after_go_to_top_button');

function hocwp_setup_theme_add_favicon() {
    $favicon = hocwp_theme_get_option('favicon');
    $favicon = hocwp_sanitize_media_value($favicon);
    if(!empty($favicon['url'])) {
        echo '<link type="image/x-icon" href="' . $favicon['url'] . '" rel="shortcut icon">';
    }
}
add_action('wp_head', 'hocwp_setup_theme_add_favicon');

if('vi' == hocwp_get_language() && !is_admin()) {
    include HOCWP_PATH . '/theme/theme-translation.php';
}