<div class="main-content">
    <div class="<?php hocwp_wrap_class(); ?>">
        <div class="row">
            <div class="col-md-9 col-xs-12">
                <div class="main">
                    <div class="main-box-title">
                        <h1><?php the_archive_title(); ?></h1>
                    </div>
                    <div class="main-box-inside clearfix">
                        <?php
                        if(have_posts()) {
                            while(have_posts()) {
                                the_post();
                                hocwp_theme_get_loop('post');
                            }
                            wp_reset_postdata();
                        } else {
                            hocwp_theme_get_content_none();
                        }
                        ?>
                    </div>
                    <?php
                    $p_args = array(
                        'label' => 'Page %PAGED% of %TOTAL_PAGES%',
                        'first' => '« First',
                        'last' => 'Last »'
                    );
                    hocwp_pagination($p_args);
                    ?>
                </div>
            </div>
            <div class="col-md-3 col-xs-12">
                <?php get_sidebar('secondary'); ?>
            </div>
        </div>
    </div>
</div>