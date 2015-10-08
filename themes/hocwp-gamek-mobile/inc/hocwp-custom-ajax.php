<?php
function hocwp_load_more_home_post_ajax_callback() {
    $result = array(
        'more' => true,
        'have_posts' => false
    );
    $post_not_in = get_option('hocwp_home_post_not_in');
    $post_not_in = hocwp_sanitize_array($post_not_in);
    $query_vars = isset($_POST['query_vars']) ? $_POST['query_vars'] : array();
    $query_vars = hocwp_json_string_to_array($query_vars);
    $paged = isset($_POST['paged']) ? $_POST['paged'] : 1;
    $posts_per_page = isset($_POST['posts_per_page']) ? $_POST['posts_per_page'] : 10;
    $offset = isset($_POST['offset']) ? $_POST['offset'] : 20;
    $args = array(
        'paged' => $paged,
        'post__not_in' => $post_not_in
    );
    //$args = hocwp_theme_custom_sanitize_normal_post_query_args($args);
    //$args = wp_parse_args($args, $query_vars);
    $query = hocwp_query($args);
    if($query->have_posts()) {
        $result['have_posts'] = true;
        if($query->post_count < $posts_per_page) {
            $result['more'] = false;
        }
        $offset += $query->post_count;
        $paged++;
        $result['offset'] = $offset;
        $result['paged'] = $paged;
        ob_start();
        while($query->have_posts()) {
            $query->the_post();
            hocwp_theme_get_loop('home-more-recent-posts');
        }
        wp_reset_postdata();
        $html = ob_get_clean();
        $result['html'] = $html;
    }
    $result['query_vars'] = '';
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_load_more_home_post', 'hocwp_load_more_home_post_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_load_more_home_post', 'hocwp_load_more_home_post_ajax_callback');

function hocwp_load_more_archive_post_ajax_callback() {
    $result = array(
        'more' => true,
        'have_posts' => false
    );
    $query_vars = isset($_POST['query_vars']) ? $_POST['query_vars'] : array();
    $query_vars = hocwp_json_string_to_array($query_vars);
    if(hocwp_array_has_value($query_vars)) {
        $paged = isset($query_vars['paged']) ? $query_vars['paged'] : 1;
        $paged++;
        $query_vars['paged'] = $paged;
        $query = hocwp_query($query_vars);
        if($query->have_posts()) {
            $result['query_vars'] = $query->query_vars;
            $result['have_posts'] = true;
            $posts_per_page = isset($query->query_vars['posts_per_page']) ? $query->query_vars['posts_per_page'] : get_option('posts_per_page');
            if($query->post_count < $posts_per_page) {
                $result['more'] = false;
            }
            ob_start();
            while($query->have_posts()) {
                $query->the_post();
                hocwp_theme_get_loop('home-more-recent-posts');
            }
            wp_reset_postdata();
            $result['html'] = ob_get_clean();
        } else {
            $result['more'] = false;
        }
    } else {
        $result['more'] = false;
    }
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_load_more_archive_post', 'hocwp_load_more_archive_post_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_load_more_archive_post', 'hocwp_load_more_archive_post_ajax_callback');

function hocwp_load_more_video_box_ajax_callback() {
    $query_vars = isset($_POST['query_vars']) ? $_POST['query_vars'] : array();
    $query_vars = hocwp_json_string_to_array($query_vars);
    $result = array(
        'more' => true,
        'have_posts' => false
    );
    if(hocwp_array_has_value($query_vars)) {
        $paged = isset($query_vars['paged']) ? absint($query_vars['paged']) : 1;
        $paged++;
        $query_vars['paged'] = $paged;
        $query = hocwp_query($query_vars);
        $result['query_vars'] = $query->query_vars;
        if($query->have_posts()) {
            $result['have_posts'] = true;
            $posts_per_page = isset($query->query_vars['posts_per_page']) ? $query->query_vars['posts_per_page'] : get_option('posts_per_page');
            if($query->post_count < $posts_per_page) {
                $result['more'] = false;
            }
            ob_start();
            while($query->have_posts()) {
                $query->the_post();
                hocwp_theme_get_loop('video-box-post');
            }
            wp_reset_postdata();
            $result['html'] = ob_get_clean();
            $result['post_count'] = $query->post_count;
        } else {
            $result['more'] = false;
        }
    }
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_load_more_video_box', 'hocwp_load_more_video_box_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_load_more_video_box', 'hocwp_load_more_video_box_ajax_callback');

function hocwp_play_video_change_ajax_callback() {
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
    $result = array();
    if($post_id > 0) {
        global $post;
        $post = get_post($post_id);
        if(hocwp_object_valid($post)) {
            set_transient('hocwp_current_video_id', $post_id);
            /*
            $post_id = $post->ID;
            $video_url = get_post_meta($post_id, 'video_url', true);
            $video_code = get_post_meta($post_id, 'video_code', true);
            if(!empty($video_url) || !empty($video_code)) {
                $result['has_video'] = true;
                setup_postdata($post);
                ob_start();
                hocwp_theme_get_loop('video-player');
                $result['html'] = ob_get_clean();
                wp_reset_postdata();
            }
            */
        }
    }
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_play_video_change', 'hocwp_play_video_change_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_play_video_change', 'hocwp_play_video_change_ajax_callback');