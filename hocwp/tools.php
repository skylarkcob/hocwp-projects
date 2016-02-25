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