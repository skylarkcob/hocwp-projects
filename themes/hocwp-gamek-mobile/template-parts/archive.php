<div class="container">
    <section id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <?php
            global $wp_query;
            hocwp_theme_get_module('archive-two-posts');
            hocwp_theme_get_module('archive-another-posts');
            hocwp_pagination();
            if($wp_query->post_count >= get_option('posts_per_page')) {
                ?>
                <div class="clear"></div>
                <div class="paging margin-top-10" data-query-vars="<?php echo esc_attr(json_encode($wp_query->query_vars)); ?>">
                    <a href="javascript:void(0)" class="load-more">Xem thêm </a>
                </div>
                <?php
            }
            ?>
        </main><!-- .site-main -->
    </section><!-- .content-area -->
    <?php get_sidebar(); ?>
</div>