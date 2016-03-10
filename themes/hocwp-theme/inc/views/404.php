<div class="<?php hocwp_wrap_class(); ?>">
    <div class="row">
        <div id="primary" class="content-area col-md-9 col-xs-12">
            <main id="main" class="site-main">
                <section class="error-404 not-found">
                    <header class="page-header">
                        <h1 class="page-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'hocwp' ); ?></h1>
                    </header><!-- .page-header -->
                    <div class="page-content">
                        <p><?php _e( 'It looks like nothing was found at this location. Maybe try a search?', 'hocwp' ); ?></p>
                        <?php get_search_form(); ?>
                    </div><!-- .page-content -->
                </section><!-- .error-404 -->
            </main><!-- .site-main -->
        </div><!-- .content-area -->
        <?php get_sidebar('404'); ?>
    </div>
</div>