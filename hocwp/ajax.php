<?php
if(!function_exists('add_filter')) exit;

function hocwp_debug_log_ajax_callback() {
    $object = hocwp_get_method_value('object');
    $object = hocwp_json_string_to_array($object);
    hocwp_debug_log($object);
    exit;
}
add_action('wp_ajax_hocwp_debug_log', 'hocwp_debug_log_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_debug_log', 'hocwp_debug_log_ajax_callback');

function hocwp_comment_likes_ajax_callback() {
    $result = array();
    $likes = isset($_POST['likes']) ? absint($_POST['likes']) : 0;
    $comment_id = isset($_POST['comment_id']) ? absint($_POST['comment_id']) : 0;
    $likes++;
    update_comment_meta($comment_id, 'likes', $likes);
    $result['likes'] = hocwp_number_format($likes);
    $_SESSION['comment_' . $comment_id . '_likes'] = 1;
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_comment_likes', 'hocwp_comment_likes_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_comment_likes', 'hocwp_comment_likes_ajax_callback');

function hocwp_comment_report_ajax_callback() {
    $result = array();
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_comment_report', 'hocwp_comment_report_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_comment_report', 'hocwp_comment_report_ajax_callback');

function hocwp_fetch_plugin_license_ajax_callback() {
    $result = array(
        'customer_email' => '',
        'license_code' => ''
    );
    $use_for = isset($_POST['use_for']) ? $_POST['use_for'] : '';
    if(!empty($use_for)) {
        $use_for_key = md5($use_for);
        $option = get_option('hocwp_plugin_licenses');
        $customer_email = hocwp_get_value_by_key($option, array($use_for_key, 'customer_email'));
        if(is_array($customer_email) || !is_email($customer_email)) {
            $customer_email = '';
        }
        $license_code = hocwp_get_value_by_key($option, array($use_for_key, 'license_code'));
        if(is_array($license_code) || strlen($license_code) < 5) {
            $license_code = '';
        }
        $result['customer_email'] = $customer_email;
        $result['license_code'] = $license_code;
        update_option('test', $result);
    }
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_fetch_plugin_license', 'hocwp_fetch_plugin_license_ajax_callback');

function hocwp_change_captcha_image_ajax_callback() {
    $result = array(
        'success' => false
    );
    $captcha = new HOCWP_Captcha();
    $url = $captcha->generate_image();
    if(!empty($url)) {
        $result['success'] = true;
        $result['captcha_image_url'] = $url;
    } else {
        $result['message'] = __('Sorry, cannot generate captcha image, please try again or contact administrator!', 'hocwp');
    }
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_change_captcha_image', 'hocwp_change_captcha_image_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_change_captcha_image', 'hocwp_change_captcha_image_ajax_callback');

function hocwp_vote_post_ajax_callback() {
    $result = array(
        'success' => false
    );
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
    $post_id = absint($post_id);
    if($post_id > 0) {
        $type = isset($_POST['type']) ? $_POST['type'] : hocwp_get_method_value('vote_type');
        $session_name = 'hocwp_vote_' . $type . '_post_' . $post_id;
        if(!isset($_SESSION[$session_name]) || 1 != $_SESSION[$session_name]) {
            $value = isset($_POST['value']) ? $_POST['value'] : '';
            $value = absint($value);
            $value++;
            if('up' == $type || 'like' == $type) {
                update_post_meta($post_id, 'likes', $value);
            } elseif('down' == $type || 'dislike' == $type) {
                update_post_meta($post_id, 'dislikes', $value);
            }
            $result['value'] = $value;
            $result['type'] = $type;
            $result['post_id'] = $post_id;
            $result['value_html'] = number_format($value);
            $_SESSION[$session_name] = 1;
            $result['success'] = true;
        }
    }
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_vote_post', 'hocwp_vote_post_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_vote_post', 'hocwp_vote_post_ajax_callback');

function hocwp_favorite_post_ajax_callback() {
    $result = array(
        'html_data' => '',
        'success' => false,
        'remove' => false
    );
    $post_id = hocwp_get_method_value('post_id');
    if(hocwp_id_number_valid($post_id) && is_user_logged_in()) {
        $user = wp_get_current_user();
        $type = hocwp_get_method_value('type');
        if(empty($type)) {
            $type = 'favorite';
        }
        $action = hocwp_get_method_value('data_action');
        if(empty($action)) {
            $action = 'do';
        }
        if('favorite' == $type) {
            $favorites = get_user_meta($user->ID, 'favorite_posts', true);
            if(!is_array($favorites)) {
                $favorites = array();
            }
            if(!in_array($post_id, $favorites)) {
                $favorites[] = $post_id;
            } else {
                unset($favorites[array_search($post_id, $favorites)]);
                $result['remove'] = true;
            }
            $updated = update_user_meta($user->ID, 'favorite_posts', $favorites);
            if($updated) {
                $result['success'] = true;
                if($result['remove']) {
                    $result['html_data'] = '<i class="fa fa-heart-o"></i> Lưu tin';
                } else {
                    $result['html_data'] = '<i class="fa fa-heart"></i> Bỏ lưu';
                }
            }
        } elseif('save' == $type) {
            $result['success'] = hocwp_update_user_saved_posts($user->ID, $post_id);
        }
        if('undo' == $action) {
            $result['remove'] = true;
        }
    }
    wp_send_json($result);
}
add_action('wp_ajax_hocwp_favorite_post', 'hocwp_favorite_post_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_favorite_post', 'hocwp_favorite_post_ajax_callback');

function hocwp_sanitize_media_value_ajax_callback() {
    $id = isset($_POST['id']) ? $_POST['id'] : 0;
    $url = isset($_POST['url']) ? $_POST['url'] : '';
    $result = array('id' => $id, 'url' => $url);
    $result = hocwp_sanitize_media_value($result);
    echo json_encode($result);
    exit;
}
add_action('wp_ajax_hocwp_sanitize_media_value', 'hocwp_sanitize_media_value_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_sanitize_media_value', 'hocwp_sanitize_media_value_ajax_callback');

function hocwp_fetch_administrative_boundaries_ajax_callback() {
    $result = array();
    $default = hocwp_get_method_value('default');
    $default = str_replace('\\', '', $default);
    //$type = hocwp_get_method_value('type');
    if(empty($default)) {

    }
    $html_data = $default;
    $parent = hocwp_get_method_value('parent');
    if(hocwp_id_number_valid($parent)) {
        $taxonomy = hocwp_get_method_value('taxonomy');
        if(!empty($taxonomy)) {
            $terms = hocwp_get_terms($taxonomy, array('parent' => $parent, 'orderby' => 'NAME'));
            if(hocwp_array_has_value($terms)) {
                foreach($terms as $term) {
                    $option = hocwp_field_get_option(array('value' => $term->term_id, 'text' => $term->name));
                    $html_data .= $option;
                }
            }
        }
    }
    $result['html_data'] = $html_data;
    wp_send_json($result);
}
add_action('wp_ajax_hocwp_fetch_administrative_boundaries', 'hocwp_fetch_administrative_boundaries_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_fetch_administrative_boundaries', 'hocwp_fetch_administrative_boundaries_ajax_callback');

function hocwp_get_term_ajax_callback() {
    $term_id = hocwp_get_method_value('term_id');
    $result = array(
        'term' => new WP_Error()
    );
    if(hocwp_id_number_valid($term_id)) {
        $taxonomy = hocwp_get_method_value('taxonomy');
        if(!empty($taxonomy)) {
            $result['term'] = get_term($term_id, $taxonomy);
        }
    }
    wp_send_json($result);
}
add_action('wp_ajax_hocwp_get_term', 'hocwp_get_term_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_get_term', 'hocwp_get_term_ajax_callback');

function hocwp_get_term_administrative_boundaries_address_ajax_callback() {
    $result = array(
        'address' => ''
    );
    $term_id = hocwp_get_method_value('term_id');
    if(hocwp_id_number_valid($term_id)) {
        $taxonomy = hocwp_get_method_value('taxonomy');
        if(!empty($taxonomy)) {
            $term = get_term($term_id, $taxonomy);
            $address = $term->name;
            while($term->parent > 0) {
                $address .= ', ';
                $term = get_term($term->parent, $taxonomy);
                $address .= $term->name;
            }
            $address = rtrim($address, ', ');
            $result['address'] = $address;
        }
    }
    wp_send_json($result);
}
add_action('wp_ajax_hocwp_get_term_administrative_boundaries_address', 'hocwp_get_term_administrative_boundaries_address_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_get_term_administrative_boundaries_address', 'hocwp_get_term_administrative_boundaries_address_ajax_callback');

function hocwp_dashboard_widget_ajax_callback() {
    $result = array(
        'html_data' => ''
    );
    $widget = hocwp_get_method_value('widget');
    if(!empty($widget)) {
        $widgets = explode('_', $widget);
        array_shift($widgets);
        $widget = implode('_', $widgets);
        $callback = 'hocwp_theme_dashboard_widget_' . $widget;
        if(hocwp_callback_exists($callback)) {
            ob_start();
            call_user_func($callback);
            $result['html_data'] = ob_get_clean();
        }
    }
    wp_send_json($result);
}
add_action('wp_ajax_hocwp_dashboard_widget', 'hocwp_dashboard_widget_ajax_callback');

function hocwp_social_login_facebook_ajax_callback() {
    $result = array(
        'redirect_to' => '',
        'logged_in' => false
    );
    $data = hocwp_get_method_value('data');
    $data = hocwp_json_string_to_array($data);
    $connect = (bool)hocwp_get_method_value('connect');
    if(hocwp_array_has_value($data)) {
        $verified = (bool)hocwp_get_value_by_key($data, 'verified');
        $allow_not_verified = apply_filters('hocwp_allow_social_user_signup_not_verified', true);
        if($verified || $allow_not_verified) {
            $id = hocwp_get_value_by_key($data, 'id');
            $requested_redirect_to = hocwp_get_method_value('redirect_to');
            $redirect_to = home_url('/');
            $transient_name = 'hocwp_social_login_facebook_' . md5($id);
            $user_id = get_transient($transient_name);
            $user = get_user_by('ID', $user_id);
            if($connect && is_user_logged_in()) {
                $user = wp_get_current_user();
                $user_id = $user->ID;
            }
            $find_users = get_users(array('meta_key' => 'facebook', 'meta_value' => $id));
            if(hocwp_array_has_value($find_users)) {
                $user = $find_users[0];
                $user_id = $user->ID;
            }
            if(false === $user_id || !hocwp_id_number_valid($user_id) || !is_a($user, 'WP_User') || $connect) {
                $avatar = hocwp_get_value_by_key($data, array('picture', 'data', 'url'));
                if($connect) {
                    update_user_meta($user_id, 'facebook', $id);
                    update_user_meta($user_id, 'facebook_data', $data);
                    update_user_meta($user_id, 'avatar', $avatar);
                    $result['redirect_to'] = get_edit_profile_url($user_id);
                    $result['logged_in'] = true;
                } else {
                    $email = hocwp_get_value_by_key($data, 'email');
                    if(is_email($email)) {
                        $name = hocwp_get_value_by_key($data, 'name');
                        $first_name = hocwp_get_value_by_key($data, 'first_name');
                        $last_name = hocwp_get_value_by_key($data, 'last_name');

                        $password = wp_generate_password();
                        $user_id = null;
                        if(username_exists($email)) {
                            $user = get_user_by('login', $email);
                            $user_id = $user->ID;
                        } elseif(email_exists($email)) {
                            $user = get_user_by('email', $email);
                            $user_id = $user->ID;
                        }
                        $old_user = true;
                        if(!hocwp_id_number_valid($user_id)) {
                            $user_data = array(
                                'username' => $email,
                                'email' => $email,
                                'password' => $password
                            );
                            $user_id = hocwp_add_user($user_data);
                            if(hocwp_id_number_valid($user_id)) {
                                $old_user = false;
                            }
                        }
                        if(hocwp_id_number_valid($user_id)) {
                            $user = get_user_by('id', $user_id);
                            $redirect_to = apply_filters('login_redirect', $redirect_to, $requested_redirect_to, $user);
                            if(!$old_user) {
                                update_user_meta($user_id, 'facebook', $id);
                                $user_data = array(
                                    'ID' => $user_id,
                                    'display_name' => $name,
                                    'first_name' => $first_name,
                                    'last_name' => $last_name
                                );
                                wp_update_user($user_data);
                                update_user_meta($user_id, 'avatar', $avatar);
                                update_user_meta($user_id, 'facebook_data', $data);
                            }
                            hocwp_user_force_login($user_id);
                            $result['redirect_to'] = $redirect_to;
                            $result['logged_in'] = true;
                            set_transient($transient_name, $user_id, DAY_IN_SECONDS);
                        }
                    }
                }
            } else {
                update_user_meta($user_id, 'facebook_data', $data);
                $user = get_user_by('id', $user_id);
                $redirect_to = apply_filters('login_redirect', $redirect_to, $requested_redirect_to, $user);
                hocwp_user_force_login($user_id);
                $result['redirect_to'] = $redirect_to;
                $result['logged_in'] = true;
            }
        }
    }
    wp_send_json($result);
}
add_action('wp_ajax_hocwp_social_login_facebook', 'hocwp_social_login_facebook_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_social_login_facebook', 'hocwp_social_login_facebook_ajax_callback');

function hocwp_social_login_google_ajax_callback() {
    $result = array(
        'redirect_to' => '',
        'logged_in' => false
    );
    $data = hocwp_get_method_value('data');
    $data = hocwp_json_string_to_array($data);
    $connect = hocwp_get_method_value('connect');
    if(hocwp_array_has_value($data)) {
        $verified = (bool)hocwp_get_value_by_key($data, 'verified');
        $allow_not_verified = apply_filters('hocwp_allow_social_user_signup_not_verified', true);
        if($verified || $allow_not_verified) {
            $id = hocwp_get_value_by_key($data, 'id');
            $requested_redirect_to = hocwp_get_method_value('redirect_to');
            $redirect_to = home_url('/');
            $transient_name = 'hocwp_social_login_google_' . md5($id);
            $user_id = get_transient($transient_name);
            $user = get_user_by('id', $user_id);
            if($connect && is_user_logged_in()) {
                $user = wp_get_current_user();
                $user_id = $user->ID;
            }
            $find_users = get_users(array('meta_key' => 'google', 'meta_value' => $id));
            if(hocwp_array_has_value($find_users)) {
                $user = $find_users[0];
                $user_id = $user->ID;
            }
            if(false === $user_id || !hocwp_id_number_valid($user_id) || !is_a($user, 'WP_User') || $connect) {
                $avatar = hocwp_get_value_by_key($data, array('image', 'url'));
                if($connect) {
                    update_user_meta($user_id, 'google', $id);
                    update_user_meta($user_id, 'avatar', $avatar);
                    update_user_meta($user_id, 'google_data', $data);
                    $result['redirect_to'] = get_edit_profile_url($user_id);
                    $result['logged_in'] = true;
                } else {
                    $email = hocwp_get_value_by_key($data, array('emails', 0, 'value'));
                    if(is_email($email)) {
                        $name = hocwp_get_value_by_key($data, 'displayName');
                        $first_name = hocwp_get_value_by_key($data, array('name', 'givenName'));
                        $last_name = hocwp_get_value_by_key($data, array('name', 'familyName'));
                        $password = wp_generate_password();
                        $user_id = null;
                        if(username_exists($email)) {
                            $user = get_user_by('login', $email);
                            $user_id = $user->ID;
                        } elseif(email_exists($email)) {
                            $user = get_user_by('email', $email);
                            $user_id = $user->ID;
                        }
                        $old_user = true;
                        if(!hocwp_id_number_valid($user_id)) {
                            $user_data = array(
                                'username' => $email,
                                'email' => $email,
                                'password' => $password
                            );
                            $user_id = hocwp_add_user($user_data);
                            if(hocwp_id_number_valid($user_id)) {
                                $old_user = false;
                            }
                        }
                        if(hocwp_id_number_valid($user_id)) {
                            $user = get_user_by('id', $user_id);
                            $redirect_to = apply_filters('login_redirect', $redirect_to, $requested_redirect_to, $user);
                            if(!$old_user) {
                                update_user_meta($user_id, 'google', $id);
                                $user_data = array(
                                    'ID' => $user_id,
                                    'display_name' => $name,
                                    'first_name' => $first_name,
                                    'last_name' => $last_name
                                );
                                wp_update_user($user_data);
                                update_user_meta($user_id, 'avatar', $avatar);
                                update_user_meta($user_id, 'google_data', $data);
                            }
                            hocwp_user_force_login($user_id);
                            $result['redirect_to'] = $redirect_to;
                            $result['logged_in'] = true;
                            set_transient($transient_name, $user_id, DAY_IN_SECONDS);
                        }
                    }
                }
            } else {
                update_user_meta($user_id, 'google_data', $data);
                $user = get_user_by('id', $user_id);
                $redirect_to = apply_filters('login_redirect', $redirect_to, $requested_redirect_to, $user);
                hocwp_user_force_login($user_id);
                $result['redirect_to'] = $redirect_to;
                $result['logged_in'] = true;
            }
        }
    }
    wp_send_json($result);
}
add_action('wp_ajax_hocwp_social_login_google', 'hocwp_social_login_google_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_social_login_google', 'hocwp_social_login_google_ajax_callback');

function hocwp_disconnect_social_account_ajax_callback() {
    $social = hocwp_get_method_value('social');
    $user_id = hocwp_get_method_value('user_id');
    if(hocwp_id_number_valid($user_id)) {
        switch($social) {
            case 'facebook':
                delete_user_meta($user_id, 'facebook');
                delete_user_meta($user_id, 'facebook_data');
                break;
            case 'google':
                delete_user_meta($user_id, 'google');
                delete_user_meta($user_id, 'google_data');
                break;
        }
    }
    exit;
}
add_action('wp_ajax_hocwp_disconnect_social_account', 'hocwp_disconnect_social_account_ajax_callback');

function hocwp_compress_style_and_script_ajax_callback() {
    $result = array();
    $type = hocwp_get_method_value('type');
    $type = hocwp_json_string_to_array($type);
    $force_compress = hocwp_get_method_value('force_compress');
    $args = array(
        'type' => $type,
        'force_compress' => $force_compress
    );
    hocwp_compress_style_and_script($args);
    wp_send_json($result);
}
add_action('wp_ajax_hocwp_compress_style_and_script', 'hocwp_compress_style_and_script_ajax_callback');