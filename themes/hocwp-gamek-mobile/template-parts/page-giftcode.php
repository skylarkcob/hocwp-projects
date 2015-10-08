<div class="container">
    <section id="primary" class="content-area">
        <main id="main" class="site-main new-information" role="main">
            <div class="page-info text-center">
                <h1>Thông tin mới về <span><?php the_title(); ?></span></h1>
                <div class="page-meta">
                    <b>CẬP NHẬT</b> <?php echo hocwp_get_current_date('H:i'); ?> GMT+7 - <?php hocwp_current_weekday('d.m.Y'); ?>
                </div>
                <div class="share text-left margin-top-15">
                    <p>Bạn thích các tin tức trong chủ đề này? Hãy bình luận, gửi, chia sẻ với bạn bè nhé!</p>
                    <?php hocwp_addthis_toolbox(); ?>
                </div>
            </div>
            <?php
            $query = hocwp_query_post_by_format('giftcode');
            if($query->have_posts()) {
                ?>
                <ul class="list-unstyled list-posts clearfix loop-new-information">
                    <?php
                    while($query->have_posts()) {
                        $query->the_post();
                        hocwp_theme_get_loop('new-information-post');
                    }
                    wp_reset_postdata();
                    ?>
                </ul>
                <?php
                hocwp_pagination(array('query' => $query));
            } else {
                hocwp_theme_get_content_none();
            }
            ?>
        </main><!-- .site-main -->
    </section><!-- .content-area -->
    <?php get_sidebar('recent-news'); ?>
</div>