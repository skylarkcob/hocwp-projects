<?php
if(!has_action('init', 'hocwp_session_start')) {
    add_action('init', 'hocwp_session_start');
}

function hocwp_content_captcha_admin_bar_menu($wp_admin_bar) {
    $args = array(
        'id' => 'plugin-license',
        'title' => __('Plugin Licenses', 'hocwp-content-captcha'),
        'href' => HOCWP_PLUGIN_LICENSE_ADMIN_URL,
        'parent' => 'plugins'
    );
    $wp_admin_bar->add_node($args);
}
if(!is_admin()) add_action('admin_bar_menu', 'hocwp_content_captcha_admin_bar_menu', 99);

function hocwp_content_captcha_check_license() {
    if(!isset($_POST['submit']) && !hocwp_is_login_page()) {
        if(!hocwp_content_captcha_license_valid()) {
            if(!is_admin() && current_user_can('manage_options')) {
                wp_redirect(HOCWP_PLUGIN_LICENSE_ADMIN_URL);
                exit;
            }
            add_action('admin_notices', 'hocwp_content_captcha_invalid_license_notice');
        }
    }
}
add_action('hocwp_check_license', 'hocwp_content_captcha_check_license');

function hocwp_content_captcha_invalid_license_notice() {
    $plugin_name = hocwp_get_plugin_name(HOCWP_CONTENT_CAPTCHA_FILE, HOCWP_CONTENT_CAPTCHA_BASENAME);
    $plugin_name = hocwp_wrap_tag($plugin_name, 'strong');
    $args = array(
        'error' => true,
        'title' => __('Error', 'hocwp-content-captcha'),
        'text' => sprintf(__('Plugin %1$s is using an invalid license key! If you does not have one, please contact %2$s via email address %3$s for more information.', 'hocwp-content-captcha'), $plugin_name, '<strong>' . HOCWP_NAME . '</strong>', '<a href="mailto:' . esc_attr(HOCWP_EMAIL) . '">' . HOCWP_EMAIL . '</a>')
    );
    hocwp_admin_notice($args);
}

if(!hocwp_content_captcha_license_valid()) {
    return;
}

function hocwp_content_captcha_enqueue_scripts() {
    hocwp_enqueue_recaptcha();
    hocwp_register_core_style_and_script();
    $localize_object = hocwp_default_script_localize_object();
    $data = hocwp_option_get_data('content_captcha');
    $site_key = hocwp_get_value_by_key($data, 'site_key');
    $localize_args = array(
        'recaptcha_site_key' => $site_key
    );
    $localize_object = wp_parse_args($localize_args, $localize_object);
    if(hocwp_is_debugging()) {
        wp_localize_script('hocwp', 'hocwp', $localize_object);
        wp_register_script('hocwp-front-end', HOCWP_URL . '/js/hocwp-front-end' . HOCWP_JS_SUFFIX, array('hocwp'), false, true);
        wp_register_script('hocwp-content-captcha', HOCWP_CONTENT_CAPTCHA_URL . '/js/hocwp-plugin' . HOCWP_JS_SUFFIX, array('hocwp-front-end'), false, true);
    } else {
        wp_register_script('hocwp-content-captcha', HOCWP_CONTENT_CAPTCHA_URL . '/js/hocwp-plugin' . HOCWP_JS_SUFFIX, array(), false, true);
        wp_localize_script('hocwp-content-captcha', 'hocwp', $localize_object);
    }
    wp_register_style('hocwp-content-captcha-style', HOCWP_CONTENT_CAPTCHA_URL . '/css/hocwp-plugin' . HOCWP_CSS_SUFFIX);
    wp_enqueue_style('hocwp-content-captcha-style');
    wp_enqueue_script('hocwp-content-captcha');
}
add_action('wp_enqueue_scripts', 'hocwp_content_captcha_enqueue_scripts');

function hocwp_content_captcha_admin_style_and_script() {
    hocwp_register_core_style_and_script();
    wp_register_style('hocwp-admin-style', HOCWP_URL . '/css/hocwp-admin'. HOCWP_CSS_SUFFIX, array('hocwp-style'));
    wp_register_script('hocwp-admin', HOCWP_URL . '/js/hocwp-admin' . HOCWP_JS_SUFFIX, array('jquery', 'hocwp'), false, true);
    wp_register_style('hocwp-content-captcha-style', HOCWP_CONTENT_CAPTCHA_URL . '/css/hocwp-plugin-admin' . HOCWP_CSS_SUFFIX, array('hocwp-admin-style'));
    wp_register_script('hocwp-content-captcha', HOCWP_CONTENT_CAPTCHA_URL . '/js/hocwp-plugin-admin' . HOCWP_JS_SUFFIX, array('hocwp-admin'), false, true);
    wp_localize_script('hocwp-content-captcha', 'hocwp', hocwp_default_script_localize_object());
    wp_enqueue_style('hocwp-content-captcha-style');
    wp_enqueue_script('hocwp-content-captcha');
}
add_action('admin_enqueue_scripts', 'hocwp_content_captcha_admin_style_and_script');

add_filter('hocwp_multiple_recaptcha', '__return_true');

add_filter('hocwp_use_session', '__return_true');

function hocwp_content_captcha_filter_content($content) {
    global $post;
    $post_types = hocwp_content_captcha_get_post_types();
    $post_type = get_post_type($post);
    if(in_array($post_type, $post_types)) {
        $force_term = false;
        $support_terms = hocwp_content_captcha_get_terms();
        $taxonomies = hocwp_content_captcha_get_taxonomies();
        foreach($taxonomies as $tax) {
            foreach($support_terms as $term) {
                if($term->taxonomy == $tax) {
                    $terms = get_the_terms($post, $tax);
                    if(hocwp_array_has_value($terms)) {
                        foreach($terms as $post_term) {
                            if($post_term->term_id == $term->term_id) {
                                $force_term = true;
                                break;
                            }
                        }
                    }
                }
            }
            if($force_term) {
                break;
            }
        }
        $use_captcha = get_post_meta($post->ID, 'content_captcha', true);

        if((bool)$use_captcha || $force_term) {
            $data = hocwp_option_get_data('content_captcha');
            $use_session = (bool)hocwp_get_value_by_key($data, 'use_session');
            $secret_key = hocwp_get_value_by_key($data, 'secret_key');
            $captcha_valid = false;
            $session_key = 'hocwp_post_' . $post->ID . '_captcha_valid';
            $session_value = isset($_SESSION[$session_key]) ? $_SESSION[$session_key] : '';
            if(!$use_session) {
                $session_value = 0;
            }
            $session_value = absint($session_value);
            $recaptcha_post_id = isset($_POST['recaptcha_post_id']) ? $_POST['recaptcha_post_id'] : 0;
            if($_SERVER['REQUEST_METHOD'] == 'POST' && !(bool)$session_value && $post->ID == $recaptcha_post_id) {
                if(hocwp_recaptcha_response($secret_key)) {
                    $captcha_valid = true;
                    $session_value = 1;
                    $_SESSION[$session_key] = $session_value;
                } else {
                    $captcha_valid = false;
                    $session_value = 0;
                    $_SESSION[$session_key] = $session_value;
                }
            }
            if(!$use_session) {
                $session_value = 0;
            }
            $session_value = absint($session_value);
            if(!$captcha_valid && !(bool)$session_value) {
                unset($_SESSION[$session_key]);
                ob_start();
                hocwp_plugin_get_module(HOCWP_CONTENT_CAPTCHA_INC_PATH, 'recaptcha-form');
                $content = ob_get_clean();
            }
        }
    }
    return $content;
}
add_filter('the_content', 'hocwp_content_captcha_filter_content');

function hocwp_content_captcha_filter_language($lang) {
    $lang = 'en';
    return $lang;
}
add_filter('hocwp_language', 'hocwp_content_captcha_filter_language');

function hocwp_content_captcha_publish_box_meta_field() {
    global $post;
    if(!hocwp_object_valid($post)) {
        return;
    }
    $post_type = $post->post_type;
    $post_types = hocwp_content_captcha_get_post_types();
    if(in_array($post_type, $post_types)) {
        $key = 'content_captcha';
        $value = get_post_meta($post->ID, $key, true);
        $args = array(
            'id' => 'hocwp_post_use_content_captcha',
            'name' => $key,
            'value' => $value,
            'label' => __('Use content captcha?', 'hocwp-content-captcha')
        );
        hocwp_field_publish_box('hocwp_field_input_checkbox', $args);
    }
}
add_action('post_submitbox_misc_actions', 'hocwp_content_captcha_publish_box_meta_field');

function hocwp_content_captcha_save_post_meta($post_id) {
    if(!hocwp_can_save_post($post_id)) {
        return $post_id;
    }
    $value = isset($_POST['content_captcha']) ? 1 : 0;
    update_post_meta($post_id, 'content_captcha', $value);
    return $post_id;
}
add_action('save_post', 'hocwp_content_captcha_save_post_meta');

$post_types = hocwp_content_captcha_get_post_types();

function hocwp_content_captcha_post_column_head($columns) {
    $columns['content_captcha'] = 'Content captcha';
    return $columns;
}

function hocwp_content_captcha_post_column_content($column, $post_id) {
    if('content_captcha' == $column) {
        $div = new HOCWP_HTML('div');
        $div->set_attribute('style', 'text-align: center');
        $span = new HOCWP_HTML('span');
        $circle_class = 'icon-circle';
        $content_captcha = get_post_meta($post_id, 'content_captcha', true);
        if(1 == $content_captcha) {
            $circle_class .= ' icon-circle-success';
        }
        $span->set_attribute('data-id', $post_id);
        $span->set_attribute('data-content-captcha', $content_captcha);
        $span->set_class($circle_class);
        $div->set_text($span->build());
        $div->output();
    }
}

foreach($post_types as $post_type) {
    add_filter('manage_' . $post_type . '_posts_columns', 'hocwp_content_captcha_post_column_head');
    add_action('manage_' . $post_type . '_posts_custom_column', 'hocwp_content_captcha_post_column_content', 10, 2);
}

function hocwp_content_captcha_custom_bulk_admin_footer() {
    global $post_type;
    $post_types = hocwp_content_captcha_get_post_types();
    if(in_array($post_type, $post_types)) {
        ?>
        <script type="text/javascript">
            (function($) {
                var actions = [['content_captcha', 'Content captcha'], ['remove_content_captcha', 'Remove content captcha']];
                hocwp.addBulkAction(actions);
            })(jQuery);
        </script>
        <?php
    }
}
add_action('admin_footer-edit.php', 'hocwp_content_captcha_custom_bulk_admin_footer');

function hocwp_content_captcha_custom_bulk_action() {
    global $typenow;
    $post_type = $typenow;
    $post_types = hocwp_content_captcha_get_post_types();
    if(!in_array($post_type, $post_types)) {
        return;
    }
    $wp_list_table = _get_list_table('WP_Posts_List_Table');
    $action = $wp_list_table->current_action();
    $allowed_actions = array('content_captcha', 'remove_content_captcha');
    if(!in_array($action, $allowed_actions)) {
        return;
    }
    check_admin_referer('bulk-posts');
    $post_ids = '';
    if(isset($_REQUEST['post'])) {
        $post_ids = array_map('intval', $_REQUEST['post']);
    }
    if(!is_array($post_ids)) {
        return;
    }
    $sendback = remove_query_arg(array('selected', 'untrashed', 'deleted', 'ids'), wp_get_referer());
    if(!$sendback) {
        $sendback = admin_url('edit.php?post_type=' . $post_type);
    }
    $pagenum = $wp_list_table->get_pagenum();
    $sendback = add_query_arg(array('paged' => $pagenum, 'selected' => count($post_ids), 'ids' => join(',', $post_ids)), $sendback);
    switch($action) {
        case 'content_captcha':
            foreach($post_ids as $post_id) {
                update_post_meta($post_id, 'content_captcha', 1);
            }
            $sendback = add_query_arg(array('content_captcha' => 1), $sendback);
            break;
        case 'remove_content_captcha':
            foreach($post_ids as $post_id) {
                update_post_meta($post_id, 'content_captcha', 0);
            }
            $sendback = add_query_arg(array('remove_content_captcha' => 1), $sendback);
            break;
        default:
            return;
    }
    $sendback = remove_query_arg(array('action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view'), $sendback);
    wp_redirect($sendback);
    exit();
}
add_action('load-edit.php', 'hocwp_content_captcha_custom_bulk_action');

function hocwp_content_captcha_bulk_action_admin_notice() {
    global $post_type, $pagenow;
    $post_types = hocwp_content_captcha_get_post_types();
    if('edit.php' == $pagenow) {
        if(!in_array($post_type, $post_types)) {
            return;
        }
        $selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : 0;
        $selected = absint($selected);
        if($selected > 0 && (isset($_REQUEST['content_captcha']) || isset($_REQUEST['remove_content_captcha']))) {
            $message = $selected;
            if($selected > 1) {
                $message .= ' posts';
            } else {
                $message .= ' post';
            }

            if(isset($_REQUEST['content_captcha'])) {
                $message .= ' marked';
            } elseif(isset($_REQUEST['remove_content_captcha'])) {
                $message .= ' unmarked';
            }

            $message .= ' using captcha to confirm before viewing the content.';

            $args = array(
                'text' => $message,
                'dismissible' => true
            );
            hocwp_admin_notice($args);
        }
    }
}
add_action('admin_notices', 'hocwp_content_captcha_bulk_action_admin_notice');

function hocwp_content_captcha_restrict_manage_posts() {
    global $typenow;
    $post_types = hocwp_content_captcha_get_post_types();
    if(!in_array($typenow, $post_types)) {
        return;
    }
    ?>
    <select name="content_captcha" style="float: none;">
        <option value="-1">Filter Content captcha</option>
        <option value="1">Use content captcha</option>
        <option value="0">Not use content captcha</option>
    </select>
    <?php
}
add_action('restrict_manage_posts', 'hocwp_content_captcha_restrict_manage_posts');

function hocwp_content_captcha_parse_query($query) {
    global $pagenow, $typenow;
    if(is_admin() && 'edit.php' == $pagenow && isset($_REQUEST['filter_action'])) {
        $post_types = hocwp_content_captcha_get_post_types();
        if(!in_array($typenow, $post_types)) {
            return;
        }
        $content_captcha = isset($_REQUEST['content_captcha']) ? $_REQUEST['content_captcha'] : '';
        $meta_query = (array)$query->get('meta_query');
        if(isset($_REQUEST['content_captcha']) && 1 == $content_captcha) {
            $meta_item = array(
                'key' => 'content_captcha',
                'type' => 'numeric',
                'value' => 1,
                'compare' => '='
            );
            $meta_query[] = $meta_item;
            $query->set('meta_query', $meta_query);
        } elseif(isset($_REQUEST['content_captcha']) && 0 == $content_captcha) {
            $meta_item = array(
                'relation' => 'or',
                array(
                    'key' => 'content_captcha',
                    'type' => 'numeric',
                    'value' => 1,
                    'compare' => '!='
                ),
                array(
                    'key' => 'content_captcha',
                    'value' => '',
                    'compare' => '='
                ),
                array(
                    'key' => 'content_captcha',
                    'compare' => 'not exists'
                )
            );
            $meta_query[] = $meta_item;
            $query->set('meta_query', $meta_query);
        }
    }
}
add_action('parse_query', 'hocwp_content_captcha_parse_query');
