<div class="container">
    <section id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <?php
            if(have_posts()) {
                ?>
                <p class="search_result">Kết quả tìm kiếm bài viết có từ khóa "<?php echo get_search_query(); ?>"</p>
                <ul class="list-unstyled list-more-recent-posts loop-default clearfix">
                    <?php
                    while(have_posts()) {
                        the_post();
                        hocwp_theme_get_loop('home-more-recent-posts');
                    }
                    ?>
                </ul>
                <?php
                hocwp_pagination();
            } else {
                hocwp_theme_get_content_none();
            }
            ?>
        </main><!-- .site-main -->
    </section><!-- .content-area -->
    <?php get_sidebar(); ?>
</div>