<?php
if(!function_exists('add_filter')) exit;

global $pagenow;

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
        if(is_singular()) {
            $post_id = get_the_ID();
            if(comments_open($post_id) || get_comments_number($post_id)) {
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

function hocwp_more_mce_buttons_toolbar_1($buttons, $editor_id) {
    if(!hocwp_use_full_mce_toolbar() || 'content' != $editor_id) {
        return $buttons;
    }
    $tmp = $buttons;
    unset($buttons);
    $buttons[] = 'fontselect';
    $buttons[] = 'fontsizeselect';
    $last = array_pop($tmp);
    $buttons = array_merge($buttons, $tmp);
    $buttons[] = 'styleselect';
    $buttons[] = 'newdocument';
    $buttons[] = $last;
    return $buttons;
}
add_filter('mce_buttons', 'hocwp_more_mce_buttons_toolbar_1', 10, 2);

function hocwp_more_mce_buttons_toolbar_2($buttons, $editor_id) {
    if(!hocwp_use_full_mce_toolbar() || 'content' != $editor_id) {
        return $buttons;
    }
    $buttons[] = 'subscript';
    $buttons[] = 'superscript';
    //$buttons[] = 'hr';
    $buttons[] = 'cut';
    $buttons[] = 'copy';
    $buttons[] = 'paste';
    $buttons[] = 'backcolor';
    foreach($buttons as $key => $name) {
        if('wp_help' == $name) {
            unset($buttons[$key]);
            break;
        }
    }
    $buttons[] = 'wp_help';
    return $buttons;
}
add_filter('mce_buttons_2', 'hocwp_more_mce_buttons_toolbar_2', 10, 2);

function hocwp_setup_theme_remove_admin_bar_item() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');
    $wp_admin_bar->remove_menu('customize');
}
add_action('wp_before_admin_bar_render', 'hocwp_setup_theme_remove_admin_bar_item');

function hocwp_load_addthis_script() {
    $use = apply_filters('hocwp_use_addthis', false);
    if($use) {
        $page = apply_filters('hocwp_use_addthis_on_page', false);
        $home = apply_filters('hocwp_use_addthis_on_home', false);
        if((is_page() && $page) || !is_page() || (is_home() && $home) || !is_home()) {
            hocwp_addthis_script();
        }
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
    $type_object = get_post_type_object($post_type);
    if((bool)$type_object->public) {
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
    $type_object = get_post_type_object($post_type);
    if(hocwp_object_valid($type_object) && (bool)$type_object->public) {
        $exclude_types = hocwp_post_type_no_featured_field();
        if(!in_array($post_type, $exclude_types)) {
            $columns['featured'] = __('Featured', 'hocwp');
        }
    }
    return $columns;
}
add_filter('manage_posts_columns', 'hocwp_theme_post_column_head_featured');

function hocwp_theme_post_column_content_featured($column, $post_id) {
    $post_type = get_post_type($post_id);
    $type_object = get_post_type_object($post_type);
    if(!hocwp_wc_installed() || (hocwp_wc_installed() && 'product' != $post_type)) {
        if((bool)$type_object->public) {
            if('featured' == $column) {
                hocwp_icon_circle_ajax($post_id, 'featured');
            }
        }
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
    $fixed = hocwp_theme_sticky_last_widget();
    if($fixed) {
        get_template_part('inc/views/fixed-widget');
    }
}
add_action('hocwp_close_body', 'hocwp_theme_last_widget_fixed');

function hocwp_bold_first_paragraph($content) {
    $reading = get_option('hocwp_reading');
    $bold = (bool)hocwp_get_value_by_key($reading, 'bold_first_paragraph', false);
    $bold = apply_filters('hocwp_bold_post_content_first_paragraph', $bold);
    if($bold) {
        return preg_replace('/<p([^>]+)?>/', '<p$1 class="first-paragraph bold-it">', $content, 1);
    }
    return $content;
}
add_filter('the_content', 'hocwp_bold_first_paragraph');

function hocwp_theme_add_full_screen_loading() {
    get_template_part('/hocwp/theme/ajax-loading', 'full-screen');
}
add_action('hocwp_close_body', 'hocwp_theme_add_full_screen_loading');

function hocwp_setup_theme_wp_hook() {
    if(is_404()) {
        $redirect_404 = (bool)hocwp_option_get_value('reading', 'redirect_404');
        if($redirect_404) {
            hocwp_redirect_home();
        }
    } else {
        if(is_user_logged_in()) {
            if(is_page_template('page-templates/register.php') || is_page_template('page-templates/login.php')) {
                wp_redirect(get_edit_profile_url());
                exit;
            }
        } else {
            if(is_page_template('page-templates/register.php')) {
                if(hocwp_users_can_register()) {
                    hocwp_execute_register();
                } else {
                    $page = hocwp_get_page_by_template('page-templates/login.php');
                    if(is_a($page, 'WP_Post')) {
                        wp_redirect(get_permalink($page));
                        exit;
                    }
                    hocwp_redirect_home();
                }
            } else {
                if(hocwp_is_login_page()) {
                    $page = hocwp_get_pages_by_template('page-templates/login.php', array('output' => 'object'));
                    if(is_a($page, 'WP_Post')) {
                        wp_redirect(get_permalink($page));
                        exit;
                    }
                } elseif(is_page_template('page-templates/favorite-posts.php')) {
                    wp_redirect(wp_login_url());
                    exit;
                }
            }
        }
    }
}
add_action('wp', 'hocwp_setup_theme_wp_hook');

function hocwp_setup_theme_authenticate($user, $username) {
    global $wp_version;
    if(version_compare($wp_version, '4.5', '<')) {
        if(hocwp_allow_user_login_with_email() && is_email($username) && email_exists($username)) {
            $user = get_user_by('email', $username);
        }
    }
    return $user;
}
add_filter('authenticate', 'hocwp_setup_theme_authenticate', 10, 2);

function hocwp_setup_theme_login_error_messages() {

}

function hocwp_setup_theme_login_init() {
    $action = hocwp_get_value_by_key($_REQUEST, 'action');
    $user = new WP_Error();
    $login = false;
    if('logout' !== $action) {
        $log = hocwp_get_value_by_key($_REQUEST, 'log');
        $pwd = hocwp_get_value_by_key($_REQUEST, 'pwd');
        $rememberme = (bool)hocwp_get_value_by_key($_REQUEST, 'rememberme');
        if(!empty($log) && !empty($pwd)) {
            $user = hocwp_user_login($log, $pwd, $rememberme);
            $login = true;
        }
    }
    if(!is_a($user, 'WP_User')) {
        $url = '';
        switch($action) {
            case 'signup':
            case 'register':
                $url = hocwp_get_account_url('register');
                break;
            case 'lostpassword':
                $url = hocwp_get_account_url('lostpassword');
                break;
            case 'logout':
                wp_logout();
                $url = home_url('/');
                break;
            default:
                if(empty($action) || 'login' === $action) {
                    $url = hocwp_get_account_url();
                }
        }
        if(!empty($url)) {
            if($login || isset($_REQUEST['wp-submit'])) {
                $url = add_query_arg(array('error' => 'invalidlogin'), $url);
            }
            wp_redirect($url);
            exit;
        }
    }
}
add_action('login_init', 'hocwp_setup_theme_login_init');

function hocwp_setup_theme_login_url($url) {
    $custom_url = hocwp_get_account_url();
    if(!empty($custom_url)) {
        $url = $custom_url;
    }
    return $url;
}
add_filter('login_url', 'hocwp_setup_theme_login_url');

function hocwp_setup_theme_logout_redirect_url($url) {
    $page = hocwp_get_pages_by_template('page-templates/account.php', array('output' => 'object'));
    if(is_a($page, 'WP_Post')) {
        $url = get_permalink($page);
        $url = trailingslashit($url);
        $url = add_query_arg(array('loggedout' => 1), $url);
    }
    return $url;
}
add_filter('logout_redirect', 'hocwp_setup_theme_logout_redirect_url');

function hocwp_setup_theme_edit_profile_url($url) {
    $custom_url = hocwp_get_account_url('');
    if(!empty($custom_url)) {
        $url = $custom_url;
    }
    return $url;
}
add_filter('edit_profile_url', 'hocwp_setup_theme_edit_profile_url');

function hocwp_setup_theme_lostpassword_url($url) {
    $custom_url = hocwp_get_account_url('lostpassword');
    if(!empty($custom_url)) {
        $url = $custom_url;
    }
    return $url;
}
add_filter('lostpassword_url', 'hocwp_setup_theme_lostpassword_url');

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
    include HOCWP_THEME_INC_PATH . '/theme-translation.php';
}

function hocwp_setup_theme_custom_css() {
    $option = get_option('hocwp_theme_custom_css');
    $theme = wp_get_theme();
    $template = hocwp_sanitize_id($theme->get_template());
    $css = hocwp_get_value_by_key($option, $template);
    if(!empty($css)) {
        $css = hocwp_minify_css($css);
        $style = new HOCWP_HTML('style');
        $style->set_attribute('type', 'text/css');
        $style->set_text($css);
        $style->output();
    }
}
add_action('wp_head', 'hocwp_setup_theme_custom_css', 99);

function hocwp_setup_theme_custom_head_data() {
    $option = get_option('hocwp_theme_add_to_head');
    $code = hocwp_get_value_by_key($option, 'code');
    if(!empty($code)) {
        echo $code;
    }
}
add_action('wp_head', 'hocwp_setup_theme_custom_head_data', 99);

function hocwp_setup_theme_the_excerpt($excerpt) {
    $excerpt = str_replace('<p>', '<p class="post-excerpt">', $excerpt);
    return $excerpt;
}
add_filter('the_excerpt', 'hocwp_setup_theme_the_excerpt');

function hocwp_setup_theme_comment_form() {

}
add_action('comment_form', 'hocwp_setup_theme_comment_form');

function hocwp_setup_theme_comment_form_submit_field($submit_field, $args) {
    if(hocwp_use_comment_form_captcha() && !hocwp_use_comment_form_captcha_custom_position()) {
        $disable_captcha_user = hocwp_user_not_use_comment_form_captcha();
        if(!$disable_captcha_user || ($disable_captcha_user && !is_user_logged_in())) {
            $submit_field = str_replace('form-submit', 'form-submit captcha-beside', $submit_field);
            ob_start();
            $args = array(
                'before' => '<p class="captcha-group">',
                'after' => '</p>',
                'input_width' => 165
            );
            if('vi' == hocwp_get_language()) {
                $args['placeholder'] = __('Nhập mã bảo mật', 'hocwp');
            }
            hocwp_field_captcha($args);
            $captcha_field = ob_get_clean();
            $submit_field .= $captcha_field;
        }
    }
    return $submit_field;
}
add_filter('comment_form_submit_field', 'hocwp_setup_theme_comment_form_submit_field', 10, 2);

function hocwp_setup_theme_preprocess_comment($commentdata) {
    $disable_captcha_user = hocwp_user_not_use_comment_form_captcha();
    if(hocwp_use_comment_form_captcha() && (!$disable_captcha_user || ($disable_captcha_user && !is_user_logged_in()))) {
        $lang = hocwp_get_language();
        if(isset($_POST['captcha'])) {
            $captcha = $_POST['captcha'];
            if(empty($captcha)) {
                if('vi' == $lang) {
                    wp_die(__('Để xác nhận bạn không phải là máy tính, xin vui lòng nhập mã bảo mật!', 'hocwp'), __('Chưa nhập mã bảo mật', 'hocwp'));
                } else {
                    wp_die(__('To confirm you are not a computer, please enter the security code!', 'hocwp'), __('Empty captcha code error', 'hocwp'));
                }
                exit;
            } else {
                $hw_captcha = new HOCWP_Captcha();
                if(!$hw_captcha->check($captcha)) {
                    if('vi' == $lang) {
                        wp_die(__('Mã bảo mật bạn nhập không chính xác, xin vui lòng thử lại!', 'hocwp'), __('Sai mã bảo mật', 'hocwp'));
                    } else {
                        wp_die(__('The security code you entered is incorrect, please try again!', 'hocwp'), __('Invalid captcha code', 'hocwp'));
                    }
                    exit;
                }
            }
        } else {
            $commentdata = null;
            if('vi' == $lang) {
                wp_die(__('Hệ thống đã phát hiện bạn không phải là người!', 'hocwp'), __('Lỗi gửi bình luận', 'hocwp'));
            } else {
                wp_die(__('Our systems have detected that you are not a human!', 'hocwp'), __('Post comment error', 'hocwp'));
            }
            exit;
        }
    }
    return $commentdata;
}
add_filter('preprocess_comment', 'hocwp_setup_theme_preprocess_comment', 1);

function hocwp_setup_theme_enable_session($use) {
    if(!is_admin()) {
        $disable_captcha_user = hocwp_user_not_use_comment_form_captcha();
        if(hocwp_use_comment_form_captcha() && (!$disable_captcha_user || ($disable_captcha_user && !is_user_logged_in()))) {
            $use = true;
        }
    }
    return $use;
}
add_filter('hocwp_use_session', 'hocwp_setup_theme_enable_session');

$maintenance_mode = hocwp_in_maintenance_mode();

function hocwp_setup_theme_in_maintenance_mode_notice() {
    hocwp_in_maintenance_mode_notice();
}

function hocwp_setup_theme_maintenance_head() {
    $args = hocwp_maintenance_mode_settings();
    $background = hocwp_get_value_by_key($args, 'background');
    $background = hocwp_sanitize_media_value($background);
    $background = $background['url'];
    $css = '';
    if(!empty($background)) {
        $css .= hocwp_build_css_rule(array('.hocwp-maintenance'), array('background-image' => 'url("' . $background . '")'));
    }
    if(!empty($css)) {
        $css = hocwp_minify_css($css);
        echo '<style type="text/css">' . $css . '</style>';
    }
}

function hocwp_setup_theme_maintenance() {
    $options = hocwp_maintenance_mode_settings();
    $heading = hocwp_get_value_by_key($options, 'heading');
    $text = hocwp_get_value_by_key($options, 'text');
    echo '<h2 class="heading">' . $heading . '</h2>';
    echo wpautop($text);
}

function hocwp_setup_theme_maintenance_scripts() {
    wp_enqueue_style('hocwp-maintenance-style', HOCWP_URL . '/css/hocwp-maintenance' . HOCWP_CSS_SUFFIX, array());
}

function hocwp_setup_theme_maintenance_body_class($classes) {
    $classes[] = 'hocwp-maintenance';
    return $classes;
}

function hocwp_setup_theme_navigation_markup_template($template) {
    $template = '<nav class="navigation %1$s">
		<h2 class="screen-reader-text">%2$s</h2>
		<div class="nav-links">%3$s</div>
	</nav>';
    return $template;
}
add_filter('navigation_markup_template', 'hocwp_setup_theme_navigation_markup_template');

function hocwp_setup_theme_get_search_form($form) {
    $format = current_theme_supports('html5', 'search-form') ? 'html5' : 'xhtml';
    $format = apply_filters('search_form_format', $format);
    if('html5' == $format) {
        $form = '<form method="get" class="search-form" action="' . esc_url(home_url('/')) . '">
				<label>
					<span class="screen-reader-text">' . _x('Search for:', 'label') . '</span>
					<input type="search" class="search-field" placeholder="' . esc_attr_x('Search &hellip;', 'placeholder') . '" value="' . get_search_query() . '" name="s" title="' . esc_attr_x('Search for:', 'label') . '" />
				</label>
				<input type="submit" class="search-submit" value="'. esc_attr_x('Search', 'submit button') .'" />
			</form>';
    } else {
        $form = '<form method="get" id="searchform" class="searchform" action="' . esc_url(home_url('/')) . '">
				<div>
					<label class="screen-reader-text" for="s">' . _x('Search for:', 'label') . '</label>
					<input type="text" value="' . get_search_query() . '" name="s" id="s" />
					<input type="submit" id="searchsubmit" value="'. esc_attr_x('Search', 'submit button') .'" />
				</div>
			</form>';
    }
    return $form;
}
add_filter('get_search_form', 'hocwp_setup_theme_get_search_form');

function hocwp_setup_theme_wpseo_breadcrumb_separator($separator) {
    if(!hocwp_string_contain($separator, '</')) {
        $separator = '<span class="sep separator">' . $separator . '</span>';
    }
    return $separator;
}
add_filter('wpseo_breadcrumb_separator', 'hocwp_setup_theme_wpseo_breadcrumb_separator');

function hocwp_setup_theme_wpseo_breadcrumb_links($crumbs) {
    $options = get_option('hocwp_reading');
    $disable_post_title = hocwp_get_value_by_key($options, 'disable_post_title_breadcrumb');
    $disable_post_title = apply_filters('hocwp_disable_post_title_breadcrumb', $disable_post_title);
    if((bool)$disable_post_title) {
        if(hocwp_array_has_value($crumbs)) {
            array_pop($crumbs);
        }
    }
    return $crumbs;
}
add_filter('wpseo_breadcrumb_links', 'hocwp_setup_theme_wpseo_breadcrumb_links');

function hocwp_setup_theme_wpseo_breadcrumb_single_link($output, $crumbs) {
    $options = get_option('hocwp_reading');
    $link_last_item = hocwp_get_value_by_key($options, 'link_last_item_breadcrumb');
    $link_last_item = apply_filters('hocwp_link_last_item_breadcrumb', $link_last_item);
    if((bool)$link_last_item) {
        if(hocwp_array_has_value($crumbs)) {
            if(strpos($output, '<span class="breadcrumb_last"') !== false || strpos($output, '<strong class="breadcrumb_last"') !== false) {
                $output = '<a class="breadcrumb_last" property="v:title" rel="v:url" href="'. $crumbs['url']. '">';
                $output .= $crumbs['text'];
                $output .= '</a></span>';
            }
        }
    }
    return $output;
}
add_filter('wpseo_breadcrumb_single_link', 'hocwp_setup_theme_wpseo_breadcrumb_single_link' , 10, 2);

function hocwp_setup_theme_get_comment_author($author, $comment_id, $comment) {
    if(!is_admin()) {
        if(!is_email($author)) {
            $author = hocwp_uppercase_first_char_words($author);
        }
    }
    return $author;
}
add_filter('get_comment_author', 'hocwp_setup_theme_get_comment_author', 10, 3);

if($maintenance_mode && !hocwp_maintenance_mode_exclude_condition()) {
    add_action('admin_notices', 'hocwp_setup_theme_in_maintenance_mode_notice');
    add_action('init', 'hocwp_theme_maintenance_mode');
    add_action('hocwp_maintenance_head', 'hocwp_setup_theme_maintenance_head');
    add_action('hocwp_maintenance', 'hocwp_setup_theme_maintenance');
    add_action('wp_enqueue_scripts', 'hocwp_setup_theme_maintenance_scripts');
    add_filter('body_class', 'hocwp_setup_theme_maintenance_body_class');
}

function hocwp_setup_theme_allow_shortcode_in_comment() {
    $options = get_option('hocwp_discussion');
    $allow_shortcode = hocwp_get_value_by_key($options, 'allow_shortcode');
    if((bool)$allow_shortcode) {
        add_filter('comment_text', 'do_shortcode');
    }
}
add_action('hocwp_front_end_init', 'hocwp_setup_theme_allow_shortcode_in_comment');

function hocwp_setup_theme_widget_title($title) {
    $title = hocwp_wrap_tag($title, 'span', hocwp_sanitize_html_class($title));
    return $title;
}
add_filter('widget_title', 'hocwp_setup_theme_widget_title');

function hocwp_setup_theme_add_required_plugins($plugins) {
    if(current_theme_supports('woocommerce') || hocwp_wc_installed()) {
        $plugins[] = 'woocommerce';
    }
    return $plugins;
}
add_filter('hocwp_required_plugins', 'hocwp_setup_theme_add_required_plugins', 99);

function hocwp_setup_theme_change_post_title($title, $post_id) {
    $tmp_post = get_post($post_id);
    if(is_a($tmp_post, 'WP_Post')) {
        if('page' == $tmp_post->post_type) {
            if(is_page()) {
                $diff_title = hocwp_get_post_meta('different_title', $post_id);
                if(!empty($diff_title)) {
                    $title = $diff_title;
                }
            }
        }
    }
    return $title;
}
add_filter('the_title', 'hocwp_setup_theme_change_post_title', 10, 2);
/*
function hocwp_setup_theme_remove_vietnamese_permalink($title, $raw_title, $context) {
    $title = hocwp_sanitize_html_class($title);
    return $title;
}
*/
//add_filter('sanitize_title', 'hocwp_setup_theme_remove_vietnamese_permalink', 10, 3);

function hocwp_setup_theme_admin_notice_required_plugins() {
    $required_plugins = hocwp_get_theme_required_plugins();
    if(hocwp_array_has_value($required_plugins)) {
        $active_plugins = get_option('active_plugins');
        $missing_required = false;
        if(!hocwp_array_has_value($active_plugins)) {
            $missing_required = true;
        } else {
            $not_active = array();
            foreach($required_plugins as $slug) {
                if('woocommerce' == $slug && hocwp_wc_installed()) {
                    continue;
                }
                $install = false;
                foreach($active_plugins as $basename) {
                    $tmp = basename(dirname($basename));
                    if($tmp == $slug) {
                        $install = true;
                    }
                }
                if(!$install) {
                    $not_active[] = $slug;
                }
            }
            if(hocwp_array_has_value($not_active)) {
                $missing_required = true;
            }
        }
        if($missing_required) {
            $admin_url = admin_url('admin.php');
            $admin_url = add_query_arg(array('page' => 'hocwp_recommended_plugin', 'tab' => 'required'), $admin_url);
            hocwp_admin_notice(array('text' => sprintf(__('Please install the required plugins for your theme. You can <a href="%s">click here</a> to see the list of required plugins for this theme.', 'hocwp'), $admin_url), 'error' => true));
        }
    }
}
add_action('admin_notices', 'hocwp_setup_theme_admin_notice_required_plugins');

$utilities = get_option('hocwp_utilities');
$link_manager = hocwp_get_value_by_key($utilities, 'link_manager');
$auto_update = hocwp_get_value_by_key($utilities, 'auto_update');

if((bool)$link_manager) {
    add_filter( 'pre_option_link_manager_enabled', '__return_true' );
}

if((bool)$auto_update) {
    add_filter('auto_update_translation', '__return_true');
    add_filter('auto_update_plugin', '__return_true');
    add_filter('auto_update_theme', '__return_true');
    add_filter('auto_update_core', '__return_true');
}

function hocwp_setup_theme_custom_head() {
    $options = get_option('hocwp_theme_custom');
    $background_image = hocwp_get_value_by_key($options, 'background_image');
    $background_image = hocwp_get_media_option_url($background_image);
    if(hocwp_url_valid($background_image)) {
        $style = new HOCWP_HTML('style');
        $style->set_attribute('type', 'text/css');
        $elements = array('body.hocwp');
        $properties = array(
            'background-image' => 'url("' . $background_image . '")',
            'background-repeat' => 'no-repeat',
            'background-color' => 'rgba(0,0,0,0)'
        );
        $background_repeat = hocwp_get_value_by_key($options, 'background_repeat');
        if((bool)$background_repeat) {
            $properties['background-repeat'] = 'repeat';
        }
        $background_color = hocwp_get_value_by_key($options, 'background_color');
        if(hocwp_color_valid($background_color)) {
            $properties['background-color'] = $background_color;
        }
        $background_size = hocwp_get_value_by_key($options, 'background_size');
        if(!empty($background_size)) {
            $properties['background-size'] = $background_size;
        }
        $background_position = hocwp_get_value_by_key($options, 'background_position');
        if(!empty($background_position)) {
            $properties['background-position'] = $background_position;
        }
        $background_attachment = hocwp_get_value_by_key($options, 'background_attachment');
        if(!empty($background_attachment)) {
            $properties['background-attachment'] = $background_attachment;
        }
        $css = hocwp_build_css_rule($elements, $properties);
        $css = hocwp_minify_css($css);
        $style->set_text($css);
        if(!empty($css)) {
            $style->output();
        }
    }
}
add_action('wp_head', 'hocwp_setup_theme_custom_head');

function hocwp_setup_theme_social_login_button() {
    $login_settings = get_option('hocwp_user_login');
    if((bool)hocwp_get_value_by_key($login_settings, 'login_with_facebook')) {
        ?>
        <p class="login-with-facebook social-login">
            <?php hocwp_facebook_login_button(); ?>
        </p>
        <?php
    }
    if((bool)hocwp_get_value_by_key($login_settings, 'login_with_google')) {
        ?>
        <p class="login-with-google social-login">
            <?php hocwp_google_login_button(); ?>
        </p>
        <?php
    }
}
add_action('hocwp_login_form_before', 'hocwp_setup_theme_social_login_button');

if(hocwp_is_login_page()) {
    add_action('login_form', 'hocwp_setup_theme_social_login_button');
    add_action('register_form', 'hocwp_setup_theme_social_login_button');
}

function hocwp_setup_theme_login_enqueue_scripts() {
    $login_settings = get_option('hocwp_user_login');
    if((bool)hocwp_get_value_by_key($login_settings, 'login_with_google')) {
        hocwp_google_plus_client_script();
    }
}
add_action('login_enqueue_scripts', 'hocwp_setup_theme_login_enqueue_scripts');

function hocwp_setup_theme_custom_footer() {
    if(!wp_is_mobile()) {
        $options = get_option('hocwp_theme_custom');
        $background_music = hocwp_get_value_by_key($options, 'background_music');
        if(!empty($background_music)) {
            $play_on = hocwp_get_value_by_key($options, 'play_on');
            if(empty($play_on)) {
                $defaults = hocwp_option_defaults();
                $play_on = hocwp_get_value_by_key($defaults, array('theme_custom', 'background_music', 'play_on'));
            }
            $play = false;
            if('home' == $play_on && is_home()) {
                $play = true;
            } elseif('single' == $play_on && is_single()) {
                $play = true;
            } elseif('page' == $play_on && is_page()) {
                $play = true;
            } elseif('archive' == $play_on && is_archive()) {
                $play = true;
            } elseif('search' == $play_on && is_search()) {
                $play = true;
            } elseif('all' == $play_on) {
                $play = true;
            }
            $play = apply_filters('hocwp_play_background_music', $play);
            if((bool)$play) {
                $div = new HOCWP_HTML('div');
                $div->set_class('hocwp-background-music hocwp-hidden');
                if(hocwp_url_valid($background_music)) {

                }
                $div->set_text($background_music);
                $div->output();
            }
        }
    }
}
add_action('wp_footer', 'hocwp_setup_theme_custom_footer');

function hocwp_setup_theme_social_login_script() {
    if(!is_user_logged_in()) {
        if(is_page_template('page-templates/register.php') || is_page_template('page-templates/login.php') || is_page_template('page-templates/account.php') || hocwp_is_login_page()) {
            $login_settings = get_option('hocwp_user_login');
            if((bool)hocwp_get_value_by_key($login_settings, 'login_with_facebook')) {
                hocwp_facebook_login_script();
            }
            if((bool)hocwp_get_value_by_key($login_settings, 'login_with_google')) {
                hocwp_google_login_script();
            }
        }
    }
}
add_action('wp_footer', 'hocwp_setup_theme_social_login_script', 99);
add_action('login_footer', 'hocwp_setup_theme_social_login_script', 99);


function hocwp_setup_theme_custom_footer_data() {
    $option = get_option('hocwp_theme_add_to_footer');
    $code = hocwp_get_value_by_key($option, 'code');
    if(!empty($code)) {
        echo $code;
    }
}
add_action('wp_footer', 'hocwp_setup_theme_custom_footer_data', 99);

add_filter('hocwp_replace_text_placeholder', 'hocwp_replace_text_placeholder');

$hocwp_permalinks = get_option('hocwp_permalink');

if(isset($hocwp_permalinks['remove_taxonomy_base'])) {
    $json_taxs = $hocwp_permalinks['remove_taxonomy_base'];
    $json_taxs = hocwp_json_string_to_array($json_taxs);
    if(hocwp_array_has_value($json_taxs)) {
        $taxonomies = array();
        foreach($json_taxs as $data) {
            $id = hocwp_get_value_by_key($data, 'id');
            if(!empty($id)) {
                $taxonomies[] = $id;
            }
        }
        if(hocwp_array_has_value($taxonomies)) {
            $rewrite = new HOCWP_Rewrite();
            $rewrite->set_taxonomies($taxonomies);
            $rewrite->remove_taxonomy_base();
        }
    }
}

$admin_page = hocwp_get_current_admin_page();
if(is_admin() && 'hocwp_permalink' == $admin_page) {
    flush_rewrite_rules();
}

function hocwp_theme_post_submitbox_misc_active() {
    global $post;
    if(!hocwp_object_valid($post)) {
        return;
    }
    $post_type = $post->post_type;
    //$type_object = get_post_type_object($post_type);
    if('hocwp_sidebar' == $post_type || 'hocwp_ads' == $post_type || 'license' == $post_type) {
        $key = 'active';
        $value = get_post_meta($post->ID, $key, true);
        $args = array(
            'id' => 'hocwp_post_active',
            'name' => $key,
            'value' => $value,
            'label' => __('Active?', 'hocwp')
        );
        hocwp_field_publish_box('hocwp_field_input_checkbox', $args);
    }
}
add_action('hocwp_publish_box_field', 'hocwp_theme_post_submitbox_misc_active');

function hocwp_setup_theme_user_contactmethods($methods) {
    $methods['phone'] = __('Phone', 'hocwp');
    $methods['address'] = __('Address', 'hocwp');
    return $methods;
}
add_filter('user_contactmethods', 'hocwp_setup_theme_user_contactmethods');

function hocwp_theme_save_post_active_meta($post_id) {
    if(!hocwp_can_save_post($post_id)) {
        return $post_id;
    }
    $value = isset($_POST['active']) ? 1 : 0;
    update_post_meta($post_id, 'active', $value);
    return $post_id;
}
add_action('save_post', 'hocwp_theme_save_post_active_meta');

function hocwp_theme_post_column_head_default_data($columns) {
    global $post_type;
    $remove_column_date = false;
    if('hocwp_sidebar' == $post_type) {
        $columns['sidebar_id'] = __('Sidebar ID', 'hocwp');
        $columns['sidebar_description'] = __('Sidebar Description', 'hocwp');
        $columns['active'] = __('Active', 'hocwp');
        $remove_column_date = true;
    } elseif('hocwp_subscriber' == $post_type) {
        $columns['subscriber_name'] = __('Name', 'hocwp');
        $columns['subscriber_phone'] = __('Phone', 'hocwp');
        $columns['subscriber_email'] = __('Email', 'hocwp');
        $columns['subscriber_user'] = __('User ID', 'hocwp');
        $columns['subscriber_date'] = __('Date', 'hocwp');
        $remove_column_date = true;
    } elseif('hocwp_ads' == $post_type) {
        $columns['ads_position'] = __('Position', 'hocwp');
        $columns['ads_expire'] = __('Expire', 'hocwp');
        $columns['active'] = __('Active', 'hocwp');
        $remove_column_date = true;
    } elseif('license' == $post_type) {
        $columns['active'] = __('Active', 'hocwp');
    }
    if($remove_column_date) {
        unset($columns['date']);
    }
    return $columns;
}
add_filter('manage_posts_columns', 'hocwp_theme_post_column_head_default_data', 99);

function hocwp_setup_theme_delete_subscriber_user($user_id) {
    $query = hocwp_get_post_by_meta('subscriber_user', $user_id);
    if($query->have_posts()) {
        foreach($query->posts as $subscriber) {
            update_post_meta($subscriber->ID, 'subscriber_user', '');
        }
    }
}
add_action('deleted_user', 'hocwp_setup_theme_delete_subscriber_user');

function hocwp_setup_theme_sidebar_id_column_sortable($columns) {
    $columns['sidebar_id'] = __('Sidebar ID', 'hocwp');
    return $columns;
}
add_filter('manage_edit-hocwp_sidebar_sortable_columns', 'hocwp_setup_theme_sidebar_id_column_sortable');

function hocwp_theme_post_column_content_active($column, $post_id) {
    if('active' == $column) {
        hocwp_icon_circle_ajax($post_id, 'active');
    }
}
add_action('manage_posts_custom_column', 'hocwp_theme_post_column_content_active', 10, 2);

function hocwp_theme_post_column_content_ads($column, $post_id) {
    if('ads_position' == $column) {
        $positions = hocwp_get_ads_positions();
        $position = hocwp_get_post_meta('position', $post_id);
        if(isset($positions[$position])) {
            $position = $positions[$position];
            echo $position['name'];
        }
    } elseif('ads_expire' == $column) {
        $expire = hocwp_get_post_meta('expire', $post_id);
        if(!empty($expire)) {
            if(is_numeric($expire)) {
                $expire = date(hocwp_get_date_format(), $expire);
            }
            echo $expire;
        }
    }
}
add_action('manage_posts_custom_column', 'hocwp_theme_post_column_content_ads', 10, 2);

function hocwp_theme_post_column_content_sidebar($column, $post_id) {
    if('sidebar_id' == $column) {
        $info = hocwp_get_sidebar_info(get_post($post_id));
        echo $info['id'];
    } elseif('sidebar_description' == $column) {
        $info = hocwp_get_sidebar_info(get_post($post_id));
        echo esc_html($info['description']);
    }
}
add_action('manage_posts_custom_column', 'hocwp_theme_post_column_content_sidebar', 10, 2);

function hocwp_theme_post_column_content_subscriber($column, $post_id) {
    global $post_type;
    $post = get_post($post_id);
    if('hocwp_subscriber' == $post_type) {
        if('subscriber_date' == $column) {
            echo date(hocwp_get_date_format(), strtotime($post->post_date));
        } else {
            echo hocwp_get_post_meta($column, $post_id);
        }
    }
}
add_action('manage_posts_custom_column', 'hocwp_theme_post_column_content_subscriber', 10, 2);

function hocwp_setup_theme_register_user_sidebar() {
    $args = array(
        'post_type' => 'hocwp_sidebar',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'relation' => 'OR',
                array(
                    'key' => 'sidebar_default',
                    'compare' => 'NOT EXISTS'
                ),
                array(
                    'key' => 'sidebar_default',
                    'compare' => '!=',
                    'type' => 'numeric'
                )
            )
        )
    );
    $query = hocwp_query($args);
    if($query->have_posts()) {
        while($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $current = get_post($post_id);
            $info = hocwp_get_sidebar_info($current);
            if(!$info['active'] || $info['default']) {
                continue;
            }
            hocwp_register_sidebar($info['id'], $info['name'], $info['description'], $info['tag']);
        }
        wp_reset_postdata();
    }
}
add_action('widgets_init', 'hocwp_setup_theme_register_user_sidebar');

function hocwp_setup_theme_pre_get_posts(WP_Query $query) {
    if($query->is_main_query()) {
        if(is_home()) {
            $options = hocwp_option_home_setting();
            $posts_per_page = absint(hocwp_get_value_by_key($options, 'posts_per_page'));
            $query->set('posts_per_page', $posts_per_page);
        } elseif(is_search()) {
            $post_types = $query->get('post_type');
        }
    }
    return $query;
}
if(!is_admin()) add_action('pre_get_posts', 'hocwp_setup_theme_pre_get_posts');

function hocwp_setup_theme_pre_get_users(WP_User_Query $query) {
    if(is_admin()) {
        if($GLOBALS['pagenow'] == 'users.php') {
            $user = wp_get_current_user();
            $exclude = (array)$query->get('exclude');
            $exclude[] = $user->ID;
            $exclude = array_unique($exclude);
            $query->set('exclude', $exclude);
        }
    }
    return $query;
}
add_action('pre_get_users', 'hocwp_setup_theme_pre_get_users');

function hocwp_setup_theme_change_contact_form_7_recaptcha_language() {
    if(wp_script_is('google-recaptcha', 'registered')) {
        wp_deregister_script('google-recaptcha');
        $url = 'https://www.google.com/recaptcha/api.js';
        $lang = hocwp_get_language();
        if(function_exists('qtranxf_getLanguage')) {
            $lang = qtranxf_getLanguage();
        }
        $lang = apply_filters('hocwp_wpcf7_recaptcha_language', $lang);
        $url = add_query_arg(array('hl' => $lang, 'onload' => 'recaptchaCallback', 'render' => 'explicit'), $url);
        wp_register_script('google-recaptcha', $url, array(), false, true);
    }
}
add_action('wpcf7_enqueue_scripts', 'hocwp_setup_theme_change_contact_form_7_recaptcha_language');

function hocwp_setup_theme_wp_setup_nav_menu_item($menu_item) {
    if(is_a($menu_item, 'WP_Post') && $menu_item->post_type == 'nav_menu_item') {
        if('trang-chu' == $menu_item->post_name || 'home' == $menu_item->post_name) {
            $menu_url = $menu_item->url;
            $home_url = home_url('/');
            if(hocwp_get_domain_name($menu_url) != hocwp_get_domain_name($home_url)) {
                $menu_item->url = $home_url;
                update_post_meta($menu_item->ID, '_menu_item_url', $home_url);
                wp_update_nav_menu_item($menu_item->ID, $menu_item->db_id, array('url' => $home_url));
            }
        } elseif('page' == $menu_item->object) {
            $post_id = $menu_item->object_id;
            $page = get_post($post_id);
            $diff_title = get_post_meta($post_id, 'different_title', true);
            $title = $menu_item->title;
            if(!hocwp_string_empty($diff_title) && $page->post_title != $diff_title && $title == $diff_title) {
                $title = do_shortcode($page->post_title);
                $title = apply_filters('translate_text', $title, $lang = null, $flags = 0);
                $menu_item->title = $title;
            }
        }
    }
    return $menu_item;
}
add_filter('wp_setup_nav_menu_item', 'hocwp_setup_theme_wp_setup_nav_menu_item');

function hocwp_setup_theme_nav_menu_item_title($title, $menu_item, $args, $depth) {
    if(is_a($menu_item, 'WP_Post') && $menu_item->post_type == 'nav_menu_item') {
        if('page' == $menu_item->object) {
            $post_id = $menu_item->object_id;
            $page = get_post($post_id);
            $diff_title = get_post_meta($post_id, 'different_title', true);
            if(!hocwp_string_empty($diff_title) && $page->post_title != $diff_title && $title == $diff_title) {
                $title = do_shortcode($page->post_title);
                $title = apply_filters('translate_text', $title, $lang = null, $flags = 0);
            }
        }
    }
    return $title;
}
add_filter('nav_menu_item_title', 'hocwp_setup_theme_nav_menu_item_title', 10, 4);

function hocwp_setup_theme_locale($locale) {
    if(is_admin()) {
        if(hocwp_force_admin_english()) {
            $locale = 'en_US';
        }
    }
    return $locale;
}
add_filter('locale', 'hocwp_setup_theme_locale', 99);