<div class="container">
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <?php
            $args = array(
                'posts_per_page' => 1
            );
            if(false !== ($post_id = get_transient('hocwp_current_video_id'))) {
                $args['post__in'] = array($post_id);
                delete_transient('hocwp_current_video_id');
            }
            $query = hocwp_query_post_by_format('video', $args);
            $excludes = array();
            if($query->have_posts()) {
                $list_posts = $query->posts;
                ?>
                <div class="tructieptop trainghiemtop video-box clearfix">
                    <div class="left">
                        <div id="video-embeb" class="videotructiep clearfix">
                            <?php
                            $post = array_shift($list_posts);
                            setup_postdata($post);
                            $cats = wp_get_post_categories(get_the_ID());
                            if(hocwp_array_has_value($cats)) {
                                $args = array(
                                    'posts_per_page' => 6,
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => 'category',
                                            'field' => 'id',
                                            'terms' => $cats
                                        )
                                    )
                                );
                                $query = hocwp_query_post_by_format('video', $args);
                                $list_posts = $query->posts;
                            }
                            $excludes[] = get_the_ID();
                            hocwp_theme_get_loop('video-player');
                            wp_reset_postdata(); ?>
                        </div>
                    </div>
                    <div class="right">
                        <div class="videosamecattitle"><h4>VIDEO <span class="videoredtitle">CÙNG CHUYÊN MỤC</span></h4></div>
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
                'posts_per_page' => 5,
                'post__not_in' => $excludes
            );
            $args = hocwp_query_sanitize_featured_args($args);
            $args['meta_query']['relation'] = 'AND';
            $query = hocwp_query_post_by_format('video', $args);
            hocwp_theme_custom_video_box($query, 'ĐÁNG CHÚ Ý', 'featured');
            $video_cats = get_terms('video_cat', array('fields' => 'ids'));
            if(hocwp_array_has_value($video_cats)) {
                foreach($video_cats as $term_id) {
                    $term = get_term_by('id', $term_id, 'video_cat');
                    $args = array(
                        'posts_per_page' => 10,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'video_cat',
                                'field' => 'id',
                                'terms' => $term->term_id
                            )
                        ),
                        'post__not_in' => $excludes
                    );
                    $meta_item = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'featured',
                            'value' => '1',
                            'compare' => '!=',
                            'type' => 'NUMERIC'
                        ),
                        array(
                            'key' => 'featured',
                            'compare' => 'NOT EXISTS'
                        )
                    );
                    $args = hocwp_query_sanitize_meta_query($meta_item, $args);
                    $args['meta_query']['relation'] = 'AND';
                    $query = hocwp_query_post_by_format('video', $args);
                    hocwp_theme_custom_video_box($query, $term->name);
                }
            }
            $args = array(
                'posts_per_page' => 10,
                'post__not_in' => $excludes
            );
            $meta_item = array(
                'relation' => 'OR',
                array(
                    'key' => 'featured',
                    'value' => '1',
                    'compare' => '!=',
                    'type' => 'NUMERIC'
                ),
                array(
                    'key' => 'featured',
                    'compare' => 'NOT EXISTS'
                )
            );
            $args = hocwp_query_sanitize_meta_query($meta_item, $args);
            if(hocwp_array_has_value($video_cats)) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'video_cat',
                        'terms' => $video_cats,
                        'operator' => 'NOT IN'
                    )
                );
            }
            $args['meta_query']['relation'] = 'AND';
            $query = hocwp_query_post_by_format('video', $args);
            hocwp_theme_custom_video_box($query, 'KHÁC');
            ?>
        </main><!-- .site-main -->
    </div><!-- .content-area -->
</div>