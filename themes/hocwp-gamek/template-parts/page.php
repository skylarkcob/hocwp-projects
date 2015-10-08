<div class="container">
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <?php
            hocwp_article_before();
            hocwp_article_header();
            hocwp_article_content();
            hocwp_article_after();
            ?>
        </main><!-- .site-main -->
    </div><!-- .content-area -->
    <?php get_sidebar(); ?>
</div>