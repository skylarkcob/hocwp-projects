<?php
if(!function_exists('add_filter')) exit;

global $hocwp_pos_tabs;

function hocwp_option_get_list_object() {
    global $hocwp_options;
    return $hocwp_options;
}

function hocwp_option_add_object_to_list(HOCWP_Option $option) {
    global $hocwp_options;
    $option_name = $option->get_option_name_no_prefix();
    $hocwp_options[$option_name] = $option;
}

function hocwp_option_get_object_from_list($key) {
    global $hocwp_options;
    return isset($hocwp_options[$key]) ? $hocwp_options[$key] : null;
}

function hocwp_option_get_data($base_slug) {
    $data = array();
    $option = hocwp_option_get_object_from_list($base_slug);
    if(hocwp_object_valid($option)) {
        $data = $option->get();
    } else {
        $base_slug = str_replace('hocwp_', '', $base_slug);
        $data = get_option('hocwp_' . $base_slug);
    }
    return $data;
}

function hocwp_option_get_value($base, $key) {
    $data = hocwp_option_get_data($base);
    $base_slug = str_replace('hocwp_', '', $base);
    $defaults = hocwp_option_defaults();
    $defaults = hocwp_get_value_by_key($defaults, $base_slug);
    if(hocwp_array_has_value($defaults)) {
        $data = (array)$data;
        $data = wp_parse_args($data, $defaults);
    }
    if(!empty($key)) {
        $result = hocwp_get_value_by_key($data, $key);
    } else {
        $result = $data;
    }
    return $result;
}

function hocwp_get_option_by_name($base, $name = '') {
    return hocwp_option_get_value($base, $name);
}

function hocwp_get_reading_option($name = '') {
    return hocwp_get_option_by_name('reading', $name);
}

function hocwp_get_optimize_option($name = '') {
    return hocwp_get_option_by_name('optimize', $name);
}

function hocwp_get_thumbnail_size($name = 'thumbnail_small') {
    $width = 0;
    $height = 0;
    switch($name) {
        case 'thumbnail_small':
            $width = absint(get_option('thumbnail_size_w'));
            $height = absint(get_option('thumbnail_size_h'));
            break;
        case 'thumbnail_medium':
            $width = absint(get_option('medium_size_w'));
            $height = absint(get_option('medium_size_h'));
            break;
        case 'thumbnail_large':
            $width = absint(get_option('large_size_w'));
            $height = absint(get_option('large_size_h'));
            break;
    }
    $value = array($width, $height);
    return $value;
}

function hocwp_option_add_setting_field($base, $args) {
    $option = hocwp_option_get_object_from_list($base);
    if(hocwp_object_valid($option)) {
        $id = isset($args['id']) ? $args['id'] : '';
        $name = isset($args['name']) ? $args['name'] : '';
        hocwp_transmit_id_and_name($id, $name);
        $args['id'] = $option->get_field_id($id);
        $args['name'] = $option->get_field_name($name);
        if(!isset($args['value'])) {
            $args['value'] = $option->get_by_key($name);
        }
        $option->add_field($args);
    }
}

function hocwp_get_option($base_name) {
    $option = hocwp_option_get_object_from_list($base_name);
    if(hocwp_object_valid($option)) {
        return $option->get();
    }
    return array();
}

function hocwp_add_option_page_smtp_email($parent_slug = null) {
    if(null != $parent_slug) {
        _deprecated_argument(__FUNCTION__, '2.7.4', __('Please do not use $parent_slug argument since core version 2.7.4 or later.', 'hocwp'));
    }
    require(HOCWP_PATH . '/options/setting-smtp-email.php');
}

function hocwp_get_google_api_key() {
    $key = hocwp_option_get_value('option_social', 'google_api_key');
    $key = apply_filters('hocwp_google_api_key', $key);
    return $key;
}

function hocwp_get_footer_logo_url() {
    $result = hocwp_theme_get_option('footer_logo');
    $result = hocwp_sanitize_media_value($result);
    $result = $result['url'];
    return $result;
}

function hocwp_option_defaults() {
    $defaults = array(
        'theme_custom' => array(
            'background_music' => array(
                'play_ons' => array(
                    'home' => __('Homepage', 'hocwp'),
                    'single' => __('Single', 'hocwp'),
                    'page' => __('Page', 'hocwp'),
                    'archive' => __('Archive', 'hocwp'),
                    'search' => __('Search', 'hocwp'),
                    'all' => __('Play on whole page', 'hocwp')
                ),
                'play_on' => 'home'
            )
        ),
        'optimize' => array(
            'use_jquery_cdn' => 1,
            'use_bootstrap' => 1,
            'use_bootstrap_cdn' => 1,
            'use_fontawesome' => 1,
            'use_fontawesome_cdn' => 1,
            'use_superfish' => 1,
            'use_superfish_cdn' => 1
        )
    );
    return apply_filters('hocwp_option_defaults', $defaults);
}

function hocwp_get_theme_required_plugins() {
    $required = array();
    $required = apply_filters('hocwp_required_plugins', $required);
    return $required;
}

function hocwp_recommended_plugins() {
    $required = hocwp_get_theme_required_plugins();
    $defaults = array(
        'required' => $required,
        'recommended' => array(
            'wordpress-seo',
            'wp-super-cache',
            'wp-optimize',
            'wp-external-links',
            'syntaxhighlighter',
            'akismet',
            'google-analytics-for-wordpress',
            'updraftplus'
        )
    );
    return apply_filters('hocwp_recommended_plugins', $defaults);
}

function hocwp_plugin_option_page_header() {
    ?>
    <div class="page-header">
        <h2 class="theme-name"><?php _e('Plugin Options', 'hocwp'); ?></h2>
        <span class="theme-version"><?php printf(__('Core Version: %s', 'hocwp'), HOCWP_VERSION); ?></span>
    </div>
    <?php
}

function hocwp_plugin_option_page_footer() {
    hocwp_theme_option_form_after();
}

function hocwp_plugin_option_page_sidebar() {
    global $hocwp_pos_tabs;
    if(hocwp_array_has_value($hocwp_pos_tabs)) {
        $current_page = hocwp_get_current_admin_page();
        ?>
        <ul class="list-tabs">
            <?php foreach($hocwp_pos_tabs as $key => $value) : ?>
                <?php
                $admin_url = admin_url('plugins.php');
                $admin_url = add_query_arg(array('page' => $key), $admin_url);
                $item_class = hocwp_sanitize_html_class($key);
                if($key == $current_page) {
                    hocwp_add_string_with_space_before($item_class, 'active');
                    $admin_url = 'javascript:;';
                }
                $text = hocwp_get_value_by_key($value, 'text');
                if(empty($text)) {
                    continue;
                }
                ?>
                <li class="<?php echo $item_class; ?>"><a href="<?php echo $admin_url; ?>"><span><?php echo $text; ?></span></a></li>
            <?php endforeach; ?>
        </ul>
        <?php
    }
}

function hocwp_theme_option_form_before() {
    global $hocwp_theme_option;
    $theme = wp_get_theme();
    $name = $theme->get('Name');
    if(empty($name)) {
        $name = __('Unknown', 'hocwp');
    }
    $version = $theme->get('Version');
    if(empty($version)) {
        $version = '1.0.0';
    }
    ?>
    <div class="page-header">
        <h2 class="theme-name"><?php echo $name; ?></h2>
        <span class="theme-version"><?php printf(__('Version: %s', 'hocwp'), $version); ?></span>
    </div>
    <?php
}

function hocwp_theme_option_form_after() {
    $hocwp_root_domain = hocwp_get_root_domain_name(HOCWP_HOMEPAGE);
    ?>
    <div class="page-footer">
        <p>Created by <?php echo $hocwp_root_domain; ?>. If you have any questions, please send us an email via address: <em><?php echo HOCWP_EMAIL; ?></em></p>
    </div>
    <div class="copyright">
        <p>&copy; 2008 - <?php echo date('Y'); ?> <a target="_blank" href="<?php echo HOCWP_HOMEPAGE; ?>"><?php echo $hocwp_root_domain; ?></a>. All Rights Reserved.</p>
    </div>
    <?php
}

function hocwp_theme_option_sidebar_tab() {
    global $hocwp_tos_tabs;
    if(hocwp_array_has_value($hocwp_tos_tabs)) {
        $current_page = hocwp_get_current_admin_page();
        ?>
        <ul class="list-tabs">
            <?php foreach($hocwp_tos_tabs as $key => $value) : ?>
                <?php
                $admin_url = admin_url('admin.php');
                $admin_url = add_query_arg(array('page' => $key), $admin_url);
                $item_class = hocwp_sanitize_html_class($key);
                if($key == $current_page) {
                    hocwp_add_string_with_space_before($item_class, 'active');
                    $admin_url = 'javascript:;';
                }
                $text = hocwp_get_value_by_key($value, 'text');
                if(empty($text)) {
                    continue;
                }
                ?>
                <li class="<?php echo $item_class; ?>"><a href="<?php echo $admin_url; ?>"><span><?php echo $text; ?></span></a></li>
            <?php endforeach; ?>
        </ul>
        <?php
    }
}