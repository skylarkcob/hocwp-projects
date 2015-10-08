<div class="container">
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <?php
            $query = hocwp_query_post_by_format('experience', array('posts_per_page' => 8));
            $excludes = array();
            if($query->have_posts()) {
                $list_posts = $query->posts;
                ?>
                <div class="tructieptop trainghiemtop">
                    <div class="left">
                        <h3>CLIP TRẢI NGHIỆM MỚI NHẤT</h3>
                        <div id="video-embeb" class="videotructiep clearfix">
                            <?php
                            $post = array_shift($list_posts);
                            setup_postdata($post);
                            $excludes[] = get_the_ID();
                            hocwp_video_play();
                            ?>
                            <div class="clear"></div>
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <p class="time"><?php echo get_the_date('H:i:s'); ?> <span><?php echo get_the_date('d/m/Y'); ?></span></p>
                            <div class="clear"></div>
                            <div class="share margin-top-10">
                                <?php hocwp_addthis_toolbox(); ?>
                            </div>
                            <?php wp_reset_postdata(); ?>
                        </div>
                    </div>
                    <div class="right">
                        <ul class="list-posts list-experiences list-unstyled loop-sidebar-video">
                            <?php
                            foreach($list_posts as $post) {
                                setup_postdata($post);
                                hocwp_theme_get_loop('experience-sidebar-post');
                                $excludes[] = get_the_ID();
                            }
                            wp_reset_postdata();
                            ?>
                        </ul>
                    </div>
                </div>
                <?php
            } else {
                hocwp_theme_get_content_none();
            }
            $args = array(
                'posts_per_page' => 15,
                'post__not_in' => $excludes
            );
            $args = hocwp_query_sanitize_featured_args($args);
            $args['meta_query']['relation'] = 'AND';
            $query = hocwp_query_post_by_format('experience', $args);
            if($query->have_posts()) {
                ?>
                <div class="trainghiemnoibat">
                    <div class="labeltop">
                        <h2>CLIP TRẢI NGHIỆM NỔI BẬT</h2>
                    </div>
                    <div class="list">
                        <?php
                        $args = array(
                            'id' => 'trai_nghiem_noi_bat',
                            'posts' => $query->posts,
                            'posts_per_page' => 5,
                            'callback' => 'hocwp_theme_custom_trai_nghiem_noi_bat_carousel',
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
            $args = array(
                'posts_per_page' => 15,
                'post__not_in' => $excludes
            );
            $meta_item = array(
                'relation' => 'OR',
                array(
                    'key' => 'game_location',
                    'value' => 'vietnam'
                ),
                array(
                    'key' => 'game_location',
                    'compare' => 'NOT EXISTS'
                )
            );
            $args = hocwp_query_sanitize_meta_query($meta_item, $args);
            $args['meta_query']['relation'] = 'AND';
            $query = hocwp_query_post_by_format('experience', $args);
            hocwp_theme_custom_trai_nghiem_game_box($query, 'TRẢI NGHIỆM GAME TRONG NƯỚC');
            $args = array(
                'posts_per_page' => 15,
                'post__not_in' => $excludes
            );
            $meta_item = array(
                array(
                    'key' => 'game_location',
                    'value' => 'abroad'
                )
            );
            $args = hocwp_query_sanitize_meta_query($meta_item, $args);
            $args['meta_query']['relation'] = 'AND';
            $query = hocwp_query_post_by_format('experience', $args);
            hocwp_theme_custom_trai_nghiem_game_box($query, 'TRẢI NGHIỆM GAME NƯỚC NGOÀI');
            ?>
        </main><!-- .site-main -->
    </div><!-- .content-area -->
</div>