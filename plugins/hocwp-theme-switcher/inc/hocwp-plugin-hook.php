<?php
add_action('init', 'hocwp_session_start');

function hocwp_theme_switcher_plugins_loaded() {
    global $hocwp_theme_switcher_type, $hocwp_theme_switcher_time, $hocwp_theme_switcher_license_valid;

    if(!hocwp_theme_switcher_license_valid()) {
        return;
    }

    if(!empty($hocwp_theme_switcher_time)) {
        $hocwp_theme_switcher_time = DAY_IN_SECONDS;
    }

    if(hocwp_is_force_mobile()) {
        $hocwp_theme_switcher_type = 'mobile';
        $_SESSION['hocwp_theme_switcher_type'] = $hocwp_theme_switcher_type;
        setcookie('hocwp_theme_switcher_type', $hocwp_theme_switcher_type, $hocwp_theme_switcher_time, '/');
        $_COOKIE['hocwp_theme_switcher_type'] = $hocwp_theme_switcher_type;
    } else {
        if('false' == hocwp_get_force_mobile()) {
            $hocwp_theme_switcher_type = 'desktop';
            $_SESSION['hocwp_theme_switcher_type'] = $hocwp_theme_switcher_type;
            setcookie('hocwp_theme_switcher_type', $hocwp_theme_switcher_type, $hocwp_theme_switcher_time, '/');
            $_COOKIE['hocwp_theme_switcher_type'] = $hocwp_theme_switcher_type;
        }
    }

    if('mobile' == $hocwp_theme_switcher_type || hocwp_is_force_mobile_session('hocwp_theme_switcher_type') || hocwp_is_force_mobile_cookie('hocwp_theme_switcher_type')) {
        add_filter('stylesheet', 'hocwp_theme_switcher_control');
        add_filter('template', 'hocwp_theme_switcher_control');
        add_filter('post_link', 'hocwp_theme_switcher_post_link', 10, 3);
        add_filter('term_link', 'hocwp_theme_switcher_term_link', 10, 3);
        $hocwp_theme_switcher_type = 'mobile';
        $_SESSION['hocwp_theme_switcher_type'] = $hocwp_theme_switcher_type;
        setcookie('hocwp_theme_switcher_type', $hocwp_theme_switcher_type, $hocwp_theme_switcher_time, '/');
        $_COOKIE['hocwp_theme_switcher_type'] = $hocwp_theme_switcher_type;
    } else {
        if(wp_is_mobile() || hocwp_is_mobile_domain_blog()) {
            add_filter('stylesheet', 'hocwp_theme_switcher_control');
            add_filter('template', 'hocwp_theme_switcher_control');
            add_filter('post_link', 'hocwp_theme_switcher_post_link', 10, 3);
            add_filter('term_link', 'hocwp_theme_switcher_term_link', 10, 3);
            $hocwp_theme_switcher_type = 'mobile';
            $_SESSION['hocwp_theme_switcher_type'] = $hocwp_theme_switcher_type;
            setcookie('hocwp_theme_switcher_type', $hocwp_theme_switcher_type, $hocwp_theme_switcher_time, '/');
            $_COOKIE['hocwp_theme_switcher_type'] = $hocwp_theme_switcher_type;
        }
    }
}
if(!is_admin()) add_action('plugins_loaded', 'hocwp_theme_switcher_plugins_loaded', 1);

function hocwp_theme_switcher_flush_rewrite_rules() {
    hocwp_flush_rewrite_rules_after_site_url_changed();
}
add_action('init', 'hocwp_theme_switcher_flush_rewrite_rules');

function hocwp_theme_switcher_add_mobile_query_to_link($url) {
    if(!empty($url)) {
        $url = add_query_arg(array('mobile' => 'true'), $url);
    }
    return $url;
}

function hocwp_theme_switcher_post_link($permalink, $post, $leavename) {
    $permalink = hocwp_theme_switcher_add_mobile_query_to_link($permalink);
    return $permalink;
}

function hocwp_theme_switcher_term_link($termlink, $term, $taxonomy) {
    $termlink = hocwp_theme_switcher_add_mobile_query_to_link($termlink);
    return $termlink;
}

function hocwp_theme_switcher_home_url($url, $path, $orig_scheme, $blog_id) {
    $url = hocwp_theme_switcher_add_mobile_query_to_link($url);
    return $url;
}

function hocwp_theme_switcher_buttons() {
    $home_url = home_url('/');
    $mobile_text = apply_filters('hocwp_theme_switcher_mobile_button_text', __('Mobile', 'hocwp'));
    $mobile_url = add_query_arg(array('mobile' => 'true'), $home_url);
    $desktop_text = apply_filters('hocwp_theme_switcher_desktop_button_text', __('Desktop', 'hocwp'));
    $desktop_url = add_query_arg(array('mobile' => 'false'), $home_url);
    $desktop_url = str_replace('m.', '', $desktop_url);
    ?>
    <ul id="theme_switcher_buttons" class="list-inline list-unstyled clearfix" style="width: 100%; margin: 0;">
        <li class="text-center col-sm-6 col-xs-6" style="padding: 0;">
            <a href="<?php echo esc_url($mobile_url); ?>" class="btn btn-primary" style="width: 100%; display: block; border-radius: 0; background-color: transparent; border: medium none; color: rgb(187, 187, 187);"><?php echo $mobile_text; ?></a>
        </li>
        <li class="text-center col-sm-6 col-xs-6" style="padding: 0;">
            <a href="<?php echo esc_url($desktop_url); ?>" class="btn btn-primary" style="width: 100%; display: block; border-radius: 0; border: medium none; background-color: rgb(170, 170, 170); color: rgb(221, 221, 221);"><?php echo $desktop_text; ?></a>
        </li>
    </ul>
    <?php
}
if(wp_is_mobile() || hocwp_is_mobile_domain_blog()) add_action('wp_footer', 'hocwp_theme_switcher_buttons');

function hocwp_theme_switcher_check_license() {
    if(!isset($_POST['submit']) && !hocwp_is_login_page()) {
        if(!hocwp_theme_switcher_license_valid()) {
            if(!is_admin() && current_user_can('manage_options')) {
                wp_redirect(admin_url('plugins.php?page=hocwp_plugin_license'));
                exit;
            }
            add_action('admin_notices', 'hocwp_theme_switcher_invalid_license_notice');
        }
    }
}
add_action('hocwp_check_license', 'hocwp_theme_switcher_check_license');

function hocwp_theme_switcher_invalid_license_notice() {
    $plugin_name = hocwp_get_plugin_info(HOCWP_THEME_SWITCHER_FILE, HOCWP_THEME_SWITCHER_BASENAME);
    $plugin_name = hocwp_wrap_tag($plugin_name, 'strong');
    $args = array(
        'error' => true,
        'title' => __('Error', 'hocwp'),
        'text' => sprintf(__('Plugin %1$s is using an invalid license key! If you does not have one, please contact %2$s via email address %3$s for more information.', 'hocwp'), $plugin_name, '<strong>' . HOCWP_NAME . '</strong>', '<a href="mailto:' . esc_attr(HOCWP_EMAIL) . '">' . HOCWP_EMAIL . '</a>')
    );
    hocwp_admin_notice($args);
}

function hocwp_theme_switcher_invalid_license_body_class($classes) {
    if(!hocwp_theme_switcher_license_valid()) {
        $classes[] = 'hocwp-invalid-license';
    }
    return $classes;
}
add_filter('body_class', 'hocwp_theme_switcher_invalid_license_body_class');