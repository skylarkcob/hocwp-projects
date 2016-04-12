<?php
if(!function_exists('add_filter')) exit;

function hocwp_maintenance_mode_default_settings() {
    $defaults = array(
        'title' => __('Maintenance mode', 'hocwp'),
        'heading' => __('Maintenance mode', 'hocwp'),
        'text' => __('<p>Sorry for the inconvenience.<br />Our website is currently undergoing scheduled maintenance.<br />Thank you for your understanding.</p>', 'hocwp')
    );
    return apply_filters('hocwp_maintenance_mode_default_settings', $defaults);
}

function hocwp_maintenance_mode_settings() {
    $defaults = hocwp_maintenance_mode_default_settings();
    $args = get_option('hocwp_maintenance');
    $args = wp_parse_args($args, $defaults);
    return apply_filters('hocwp_maintenance_mode_settings', $args);
}

function hocwp_google_login_script($args = array()) {
    if(is_user_logged_in()) {
        return;
    }
    $clientid = hocwp_get_value_by_key($args, 'clientid', hocwp_get_google_client_id());
    if(empty($clientid)) {
        _e('Please set your Google Client ID first.', 'hocwp');
        return;
    }
    ?>
    <script type="text/javascript">
        function hocwp_google_login() {
            var params = {
                clientid: '<?php echo $clientid; ?>',
                cookiepolicy: 'single_host_origin',
                callback: 'hocwp_google_login_on_signin',
                scope: 'email',
                theme: 'dark'
            };
            gapi.auth.signIn(params);
        }
        function hocwp_google_login_on_signin(response) {
            if(response['status']['signed_in'] && !response['_aa']) {
                gapi.client.load('plus', 'v1', hocwp_google_login_client_loaded);
            }
        }
        function hocwp_google_login_client_loaded(response) {
            var request = gapi.client.plus.people.get({userId: 'me'});
            request.execute(function(response) {
                hocwp_google_login_connected_callback(response);
            });
        }
        function hocwp_google_logout() {
            gapi.auth.signOut();
            location.reload();
        }
        function hocwp_google_login_connected_callback(response) {
            (function($) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: hocwp.ajax_url,
                    data: {
                        action: 'hocwp_social_login_google',
                        data: JSON.stringify(response)
                    },
                    success: function(response){
                        var href = window.location.href;
                        if($.trim(response.redirect_to)) {
                            href = response.redirect_to;
                        }
                        if(response.logged_in) {
                            window.location.href = href;
                        }
                    }
                });
            })(jQuery);
        }
    </script>
    <?php
}

function hocwp_facebook_login_script($args = array()) {
    if(is_user_logged_in()) {
        return;
    }
    $lang = hocwp_get_language();
    $language = hocwp_get_value_by_key($args, 'language');
    if(empty($language) && 'vi' === $lang) {
        $language = 'vi_VN';
    }
    $app_id = hocwp_get_wpseo_social_facebook_app_id();
    if(empty($app_id)) {
        _e('Please set your Facebook APP ID first.', 'hocwp');
        return;
    }
    ?>
    <script type="text/javascript">
        window.hocwp = window.hocwp || {};
        function hocwp_facebook_login_status_callback(response) {
            if(response.status === 'connected') {
                hocwp_facebook_login_connected_callback();
            } else if(response.status === 'not_authorized') {

            } else {

            }
        }
        function hocwp_facebook_login() {
            FB.login(function(response) {
                hocwp_facebook_login_status_callback(response);
            }, { scope: 'email,public_profile,user_friends' });
        }
        window.fbAsyncInit = function() {
            FB.init({
                appId: '<?php echo $app_id; ?>',
                cookie: true,
                xfbml: true,
                version: 'v<?php echo HOCWP_FACEBOOK_GRAPH_API_VERSION; ?>'
            });
        };
        if(typeof FB === 'undefined') {
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/<?php echo $language; ?>/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        }
        function hocwp_facebook_login_connected_callback() {
            FB.api('/me', {fields: 'id,name,first_name,last_name,picture,verified,email'}, function(response) {
                (function($) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: hocwp.ajax_url,
                        data: {
                            action: 'hocwp_social_login_facebook',
                            data: JSON.stringify(response)
                        },
                        success: function(response){
                            var href = window.location.href;
                            if($.trim(response.redirect_to)) {
                                href = response.redirect_to;
                            }
                            if(response.logged_in) {
                                window.location.href = href;
                            }
                        }
                    });
                })(jQuery);
            });
        }
    </script>
    <?php
}

function hocwp_get_default_lat_long() {
    $lat_long = array(
        'lat' => '37.42200662799378',
        'lng' => '-122.08403290000001'
    );
    if('vi' == hocwp_get_language()) {
        $lat_long['lat'] = '21.003118';
        $lat_long['lng'] = '105.820141';
    }
    return apply_filters('hocwp_default_lat_lng', $lat_long);
}

function hocwp_register_post_type_news($args = array()) {
    $lang = hocwp_get_language();
    $slug = 'news';
    if('vi' == $lang) {
        $slug = 'tin-tuc';
    }
    $slug = apply_filters('hocwp_post_type_news_base_slug', $slug);
    $defaults = array(
        'name' => __('News', 'hocwp'),
        'slug' => $slug,
        'post_type' => 'news',
        'supports' => array('editor', 'thumbnail', 'comments')
    );
    $args = wp_parse_args($args, $defaults);
    hocwp_register_post_type($args);
    $slug = 'news-cat';
    if('vi' == $lang) {
        $slug = 'chuyen-muc';
    }
    $slug = apply_filters('hocwp_taxonomy_news_category_base_slug', $slug);
    $args = array(
        'name' => __('News Categories', 'hocwp'),
        'singular_name' => __('News Category', 'hocwp'),
        'post_types' => 'news',
        'menu_name' => __('Categories', 'hocwp'),
        'slug' => $slug,
        'taxonomy' => 'news_cat'
    );
    hocwp_register_taxonomy($args);
    $news_tag = apply_filters('hocwp_post_type_news_tag', false);
    if($news_tag) {
        $slug = 'news-tag';
        if('vi' == $lang) {
            $slug = 'the';
        }
        $slug = apply_filters('hocwp_taxonomy_news_tag_base_slug', $slug);
        $args = array(
            'name' => __('News Tags', 'hocwp'),
            'singular_name' => __('News Tag', 'hocwp'),
            'post_types' => 'news',
            'menu_name' => __('Tags', 'hocwp'),
            'slug' => $slug,
            'hierarchical' => false,
            'taxonomy' => 'news_tag'
        );
        hocwp_register_taxonomy($args);
    }
}

function hocwp_register_lib_google_maps($api_key = null) {
    if(empty($api_key)) {
        $options = get_option('hocwp_option_social');
        $api_key = hocwp_get_value_by_key($options, 'google_api_key');
    }
    if(empty($api_key)) {
        return;
    }
    wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $api_key, array(), false, true);
}

function hocwp_register_lib_tinymce() {
    wp_enqueue_script('tinymce', '//cdn.tinymce.com/' . HOCWP_TINYMCE_VERSION . '/tinymce.min.js', array(), false, true);
}

function hocwp_inline_css($elements, $properties) {
    $css = hocwp_build_css_rule($elements, $properties);
    if(!empty($css)) {
        $style = new HOCWP_HTML('style');
        $style->set_attribute('type', 'text/css');
        $css = hocwp_minify_css($css);
        $style->set_text($css);
        if(!empty($css)) {
            $style->output();
        }
    }
}

function hocwp_favorite_post_button_text($post_id = null) {
    if(!hocwp_id_number_valid($post_id)) {
        $post_id = get_the_ID();
    }
    $text = '<i class="fa fa-heart-o"></i> Lưu tin';
    if(is_user_logged_in()) {
        $user = wp_get_current_user();
        $favorite = hocwp_get_user_favorite_posts($user->ID);
        if(in_array($post_id, $favorite)) {
            $text = '<i class="fa fa-heart"></i> Bỏ lưu';;
        }
    }
    $text = apply_filters('hocwp_favorite_post_button_text', $text);
    echo $text;
}

function hocwp_get_geo_code($args = array()) {
    if(!is_array($args) && !empty($args)) {
        $args = array(
            'address' => $args
        );
    }
    $options = get_option('hocwp_option_social');
    $api_key = hocwp_get_value_by_key($options, 'google_api_key');
    $defaults = array(
        'sensor' => false,
        'region' => 'Vietnam',
        'key' => $api_key
    );
    $args = wp_parse_args($args, $defaults);
    $address = hocwp_get_value_by_key($args, 'address');
    if(empty($address)) {
        return '';
    }
    $address = str_replace(' ', '+', $address);
    $args['address'] = $address;
    $transient_name = 'hocwp_geo_code_' . md5(implode('_', $args));
    if(false === ($results = get_transient($transient_name))) {
        $base = 'https://maps.googleapis.com/maps/api/geocode/json';
        $base = add_query_arg($args, $base);
        $json = @file_get_contents($base);
        $results = json_decode($json);
        if('OK' === $results->status) {
            set_transient($transient_name, $results, MONTH_IN_SECONDS);
        }
    }
    return $results;
}

function hocwp_post_rating_ajax_callback() {
    $result = array(
        'success' => false
    );
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
    if(hocwp_id_number_valid($post_id)) {
        $score = isset($_POST['score']) ? $_POST['score'] : 0;
        if(is_numeric($score) && $score > 0) {
            $number = isset($_POST['number']) ? $_POST['number'] : 5;
            $number_max = isset($_POST['number_max']) ? $_POST['number_max'] : 5;
            $high_number = $number;
            if($number > $number_max) {
                $high_number = $number_max;
            }
            $ratings_score = floatval(get_post_meta($post_id, 'ratings_score', true));
            $ratings_score += $score;
            $ratings_users = absint(get_post_meta($post_id, 'ratings_users', true));
            $ratings_users++;
            $high_ratings_users = absint(get_post_meta($post_id, 'high_ratings_users', true));
            if($score == $high_number) {
                $high_ratings_users++;
                update_post_meta($post_id, 'high_ratings_users', $high_ratings_users);
            }
            $ratings_average = $score;
            update_post_meta($post_id, 'ratings_users', $ratings_users);
            update_post_meta($post_id, 'ratings_score', $ratings_score);
            if($ratings_users > 0) {
                $ratings_average = $ratings_score / $ratings_users;
            }
            update_post_meta($post_id, 'ratings_average', $ratings_average);
            $result['success'] = true;
            $result['score'] = $ratings_average;
            $session_key = 'hocwp_post_' . $post_id . '_rated';
            $_SESSION[$session_key] = 1;
            do_action('hocwp_post_rated', $score, $post_id);
        }
    }
    return $result;
}

function hocwp_change_url($new_url, $old_url = '', $force_update = false) {
    $transient_name = 'hocwp_update_data_after_url_changed';
    $site_url = trailingslashit(get_bloginfo('url'));
    if(!empty($old_url)) {
        $old_url = trailingslashit($old_url);
        if($old_url != $site_url && !$force_update) {
            return;
        }
    } else {
        $old_url = $site_url;
    }
    $new_url = trailingslashit($new_url);
    if($old_url == $new_url && !$force_update) {
        return;
    }
    if(false === get_transient($transient_name) || $force_update) {
        global $wpdb;
        $wpdb->query("UPDATE $wpdb->options SET option_value = replace(option_value, '$old_url', '$new_url') WHERE option_name = 'home' OR option_name = 'siteurl'");
        $wpdb->query("UPDATE $wpdb->posts SET guid = (REPLACE (guid, '$old_url', '$new_url'))");
        $wpdb->query("UPDATE $wpdb->posts SET post_content = (REPLACE (post_content, '$old_url', '$new_url'))");

        $wpdb->query("UPDATE $wpdb->postmeta SET meta_value = (REPLACE (meta_value, '$old_url', '$new_url'))");
        $wpdb->query("UPDATE $wpdb->termmeta SET meta_value = (REPLACE (meta_value, '$old_url', '$new_url'))");
        $wpdb->query("UPDATE $wpdb->commentmeta SET meta_value = (REPLACE (meta_value, '$old_url', '$new_url'))");
        $wpdb->query("UPDATE $wpdb->usermeta SET meta_value = (REPLACE (meta_value, '$old_url', '$new_url'))");
        if(is_multisite()) {
            $wpdb->query("UPDATE $wpdb->sitemeta SET meta_value = (REPLACE (meta_value, '$old_url', '$new_url'))");
        }
        set_transient($transient_name, 1, 5 * MINUTE_IN_SECONDS);
    }
}

function hocwp_disable_emoji() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
}