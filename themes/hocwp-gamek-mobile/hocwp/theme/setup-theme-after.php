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
        $value = get_post_meta($post->ID, 'featured', true);
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
        ?>
        <script>
            (function($) {
                var $window = $(window),
                    window_top = $window.scrollTop(),
                    $content_area = $('.sidebar').prev(),
                    content_area_height = $content_area.height(),
                    content_area_offset_top = $content_area.offset().top,
                    $last_widget = $('.hocwp .sidebar .widget:last'),
                    widget_width = $last_widget.width(),
                    widget_offset_top = $last_widget.offset().top,
                    widget_height = $last_widget.height(),
                    $admin_bar = $('#wpadminbar'),
                    $site_footer = $('.site-footer'),
                    site_footer_margin_top = parseInt($site_footer.css('margin-top').replace('px', '')),
                    site_footer_height = $site_footer.height(),
                    site_footer_offset_top = $site_footer.offset().top,
                    last_scroll_top = 0;
                if(content_area_height < widget_height || 0 == widget_width) {
                    return false;
                }
                if($admin_bar.length) {
                    widget_offset_top -= $admin_bar.height();
                }
                if(window_top > widget_offset_top) {
                    $last_widget.addClass('fixed');
                } else {
                    $last_widget.removeClass('fixed');
                }
                $window.scroll(function() {
                    window_top = $(this).scrollTop();
                    var scroll_down = true;
                    if(window_top > last_scroll_top) {
                        scroll_down = true;
                    } else {
                        scroll_down = false;
                    }
                    last_scroll_top = window_top;
                    content_area_height = $content_area.height();
                    if(window_top > (content_area_height - content_area_offset_top + site_footer_height)) {
                        $last_widget.addClass('fixed-bottom');
                    } else {
                        $last_widget.removeClass('fixed-bottom');
                        $last_widget.css({'top' : '0', 'bottom' : 'auto'});
                    }
                    if(window_top > widget_offset_top) {
                        $last_widget.addClass('fixed');
                    } else {
                        $last_widget.removeClass('fixed');
                    }
                    if($last_widget.hasClass('fixed-bottom')) {
                        var bottom = (site_footer_height + site_footer_margin_top),
                            white_space = site_footer_offset_top - window_top;
                        $last_widget.css({'bottom' : bottom + 'px', 'top' : 'auto'});
                    }
                });
            })(jQuery);
        </script>
        <?php
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