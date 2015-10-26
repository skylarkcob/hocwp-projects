<?php
function hocwp_theme_custom_sanitize_normal_post_query_args($args = array()) {
    $defaults = array(
        'meta_query' => array(
            array(
                'relation' => 'OR',
                array(
                    'key' => 'post_format',
                    'compare' => 'NOT EXISTS'
                ),
                array(
                    'key' => 'post_format',
                    'value' => 'default',
                    'compare' => '='
                ),
                array(
                    'key' => 'post_format',
                    'value' => '',
                    'compare' => '='
                )
            )
        )
    );
    $args = wp_parse_args($args, $defaults);
    return $args;
}

function hocwp_theme_custom_post_label() {
    $terms = get_the_terms(get_the_ID(), 'label');
    if(hocwp_array_has_value($terms)) {
        $term = array_shift($terms);
        if(hocwp_object_valid($term)) {
            $a = new HOCWP_HTML('a');
            $a->set_class('label');
            $a->set_attribute('href', get_term_link($term));
            $a->set_text($term->name);
            $a->output();
        }
    }
}

function hocwp_theme_custom_post_icon_play() {
    $format = get_post_meta(get_the_ID(), 'post_format', true);
    if('experience' == $format || 'video' == $format) {
        echo '<a href="' . get_permalink() . '"><span class="icon-play"></span></a>';
    }
}

function hocwp_theme_custom_loop_top_large_post($width, $height) {
    hocwp_article_before('label-top-left icon-play-medium');
    $args = array('width' => $width, 'height' => $height);
    if(!wp_is_mobile()) {
        $args['bfi_thumb'] = false;
    }
    hocwp_post_thumbnail($args);
    hocwp_post_title_link();
    hocwp_theme_custom_post_label();
    hocwp_theme_custom_post_icon_play();
    hocwp_article_after();
}

function hocwp_theme_custom_trai_nghiem_noi_bat_carousel($args) {
    if(is_array($args)) {
        $posts = isset($args['posts']) ? $args['posts'] : array();
        $posts_per_page = isset($args['posts_per_page']) ? $args['posts_per_page'] : get_option('posts_per_page');
        $list_posts = array_chunk($posts, $posts_per_page);
        $count = 0;
        foreach($list_posts as $posts) {
            $class = 'item';
            if(0 == $count) {
                $class .= ' active';
            }
            ?>
            <div class="<?php echo $class; ?>">
                <?php
                global $post;
                foreach($posts as $post) {
                    setup_postdata($post);
                    hocwp_article_before('icon-play-bottom-left');
                    hocwp_post_thumbnail(array('width' => 178, 'height' => 102, 'loop' => true));
                    hocwp_post_title_link();
                    hocwp_theme_custom_post_icon_play();
                    hocwp_article_after();
                }
                wp_reset_postdata();
                ?>
            </div>
            <?php
            $count++;
        }
    }
}

function hocwp_theme_custom_trai_nghiem_game_box(WP_Query $query, $title) {
    if($query->have_posts()) {
        ?>
        <div class="clear"></div>
        <div class="boxtrainghiem mgt20">
            <div class="labeltop">
                <h2><?php echo $title; ?></h2>
            </div>
            <div class="list">
                <?php
                $args = array(
                    'id' => 'trai_nghiem_game_trong_nuoc',
                    'posts' => $query->posts,
                    'posts_per_page' => 5,
                    'callback' => 'hocwp_theme_custom_trai_nghiem_game_box_carousel',
                    'indicator_with_control' => true,
                    'auto_slide' => false
                );
                hocwp_carousel_bootstrap($args);
                ?>
            </div>
        </div>
        <div class="trainghiemborder mgt15"></div>
        <?php
    }
}

function hocwp_theme_custom_video_box(WP_Query $query, $title, $type = 'default') {
    if($query->have_posts()) {
        ?>
        <div class="clear"></div>
        <div class="video-box clearfix mgt20 videocatlist <?php echo $type; ?>">
            <div class="title">
                <h3>VIDEO<span class="videoredtitle"> <?php echo $title; ?></span><a title="Xem thêm" rel="nofollow" class="viewmorevideolist view-more" data-query-vars="<?php echo esc_attr(json_encode($query->query_vars)); ?>" href="javascript:">XEM<span class="videoredtitle"> THÊM</span> &gt;&gt;</a></h3>
            </div>
            <div class="list clearfix">
                <?php
                while($query->have_posts()) {
                    $query->the_post();
                    hocwp_theme_get_loop('video-box-post');
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>
        <?php
    }
}

function hocwp_theme_custom_trai_nghiem_game_box_carousel($args) {
    if(is_array($args)) {
        $posts = isset($args['posts']) ? $args['posts'] : array();
        $posts_per_page = isset($args['posts_per_page']) ? $args['posts_per_page'] : get_option('posts_per_page');
        $list_posts = array_chunk($posts, $posts_per_page);
        $count = 0;
        foreach($list_posts as $posts) {
            $class = 'item';
            if(0 == $count) {
                $class .= ' active';
            }
            ?>
            <div class="<?php echo $class; ?>">
                <?php
                global $post;
                $post = array_shift($posts);
                setup_postdata($post);
                ?>
                <div class="big-video">
                    <?php
                    hocwp_article_before('icon-play-medium icon-play-with-text with-text');
                    hocwp_post_thumbnail(array('width' => 500, 'height' => 285, 'loop' => true));
                    hocwp_post_title_link();
                    hocwp_theme_custom_post_icon_play();
                    hocwp_addthis_toolbox();
                    hocwp_article_after();
                    ?>
                </div>
                <?php
                wp_reset_postdata();
                foreach($posts as $post) {
                    setup_postdata($post);
                    hocwp_article_before('small-video');
                    hocwp_post_thumbnail(array('width' => 217, 'height' => 125, 'loop' => true));
                    hocwp_post_title_link();
                    hocwp_theme_custom_post_icon_play();
                    hocwp_article_after();
                }
                wp_reset_postdata();
                ?>
            </div>
            <?php
            $count++;
        }
    }
}