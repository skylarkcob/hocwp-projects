<div class="container">
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <?php
            $cats = wp_get_post_categories(get_the_ID());
            $cat = array_shift($cats);
            if(is_numeric($cat)) {
                $term = get_category($cat);
                ?>
                <div class="post-bar">
                    <a class="category-link" href="<?php echo get_category_link($term); ?>"><?php echo $term->name; ?></a>
                    <?php hocwp_theme_custom_post_label(); ?>
                </div>
                <?php
            }
            $args = array(
                'posts_per_page' => 3
            );
            $query = hocwp_query_related_post($args);
            hocwp_article_header(array('entry_meta' => false));
            $excludes = array(get_the_ID());
            ?>
            <div class="entry-meta">
                <?php the_author_posts_link(); ?>
                -
                <?php echo get_the_date('d/m/Y H:i'); ?>
            </div>
            <div class="share-tool share-top">
                <?php hocwp_addthis_toolbox(); ?>
            </div>
            <div class="content-box">
                <div class="leftdetail">
                    <?php if($query->have_posts()) : ?>
                        <div class="tinlienquan">
                            <p class="namebox">TIN LIÊN QUAN</p>
                            <ul class="list-unstyled list-posts">
                                <?php
                                $list_posts = $query->posts;
                                $post = array_shift($list_posts);
                                setup_postdata($post);
                                ?>
                                <li class="with-thumb">
                                    <?php
                                    hocwp_article_before();
                                    hocwp_post_thumbnail(array('width' => 170, 'height' => 113, 'loop' => true));
                                    hocwp_post_title_link();
                                    hocwp_article_after();
                                    ?>
                                </li>
                                <?php
                                $excludes[] = get_the_ID();
                                wp_reset_postdata();
                                foreach($list_posts as $post) {
                                    setup_postdata($post);
                                    hocwp_theme_get_loop('single-left-col-post');
                                    $excludes[] = get_the_ID();
                                }
                                wp_reset_postdata();
                                ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <?php
                    $args = array(
                        'posts_per_page' => 6,
                        'post__not_in' => $excludes
                    );
                    $args = hocwp_theme_custom_sanitize_normal_post_query_args($args);
                    $args['meta_query']['relation'] = 'AND';
                    $query = hocwp_query_featured($args);
                    ?>
                    <?php if($query->have_posts()) : ?>
                        <div class="tindangdoc">
                            <p class="namebox">TIN ĐÁNG ĐỌC</p>
                            <ul class="list-unstyled list-posts">
                                <?php
                                $list_posts = $query->posts;
                                $post = array_shift($list_posts);
                                setup_postdata($post);
                                ?>
                                <li class="with-thumb">
                                    <?php
                                    hocwp_article_before();
                                    hocwp_post_thumbnail(array('width' => 170, 'height' => 113, 'loop' => true));
                                    hocwp_post_title_link();
                                    hocwp_article_after();
                                    ?>
                                </li>
                                <?php
                                $excludes[] = get_the_ID();
                                wp_reset_postdata();
                                foreach($list_posts as $post) {
                                    setup_postdata($post);
                                    hocwp_theme_get_loop('single-left-col-post');
                                    $excludes[] = get_the_ID();
                                }
                                wp_reset_postdata();
                                ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="rightdetail">
                    <?php
                    hocwp_article_before();
                    hocwp_article_content();
                    hocwp_article_after();
                    ?>
                    <div class="share-tool share-bottom">
                        <?php hocwp_addthis_toolbox(); ?>
                    </div>
                    <div class="list-tags margin-top-10">
                        <?php the_tags('<span>Xem thêm:</span>', ''); ?>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <div class="bottom-ads one-widget margin-top-10">
                <?php dynamic_sidebar('post_bottom_banner'); ?>
            </div>
            <div class="clear"></div>
            <div class="margin-top-20 clearfix cothequantam">
                <div class="section-bar">Có thể bạn quan tâm</div>
                <ul class="list-posts list-unstyled">
                    <?php
                    $args = array(
                        'posts_per_page' => 6,
                        'post__not_in' => $excludes,
                        'offset' => get_option('posts_per_page') * 2
                    );
                    $query = hocwp_query_related_post($args);
                    while($query->have_posts()) {
                        $query->the_post();
                        hocwp_theme_get_loop('maybe-interest');
                        $excludes[] = get_the_ID();
                    }
                    wp_reset_postdata();
                    ?>
                </ul>
            </div>
            <div class="clear"></div>
            <div class="margin-top-20 clearfix">
                <div class="section-bar">Bình luận về bài viết</div>
                <?php hocwp_comments_template(); ?>
            </div>
            <div class="clear"></div>
            <div class="most-view-most-new">
                <div class="tabnoibatandmoinhan"></div>
                <div class="tinnoibatdetail">
                    <div class="tabtinnoibatdetail tab-item">ĐỌC NHIỀU NHẤT</div>
                    <ul class="list-posts list-unstyled">
                        <?php
                        $args = array(
                            'posts_per_page' => 3,
                            'meta_key' => 'views',
                            'orderby' => 'meta_value_num',
                            'post__not_in' => $excludes
                        );
                        $query = hocwp_query_related_post($args);
                        while($query->have_posts()) {
                            $query->the_post();
                            hocwp_theme_get_loop('sidebar-post-full');
                            $excludes[] = get_the_ID();
                        }
                        wp_reset_postdata();
                        ?>
                    </ul>
                </div>
                <div class="tinhotdetail">
                    <div class="tabtinhotdetail tab-item">TIN MỚI NHẤT</div>
                    <ul class="list-posts list-unstyled">
                        <?php
                        $args = array(
                            'posts_per_page' => 3,
                            'post__not_in' => $excludes
                        );
                        $query = hocwp_query($args);
                        while($query->have_posts()) {
                            $query->the_post();
                            hocwp_theme_get_loop('sidebar-post-full');
                            $excludes[] = get_the_ID();
                        }
                        wp_reset_postdata();
                        ?>
                    </ul>
                </div>
            </div>
        </main><!-- .site-main -->
    </div><!-- .content-area -->
    <?php get_sidebar(); ?>
</div>