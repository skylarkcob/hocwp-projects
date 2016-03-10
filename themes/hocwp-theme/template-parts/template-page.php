<div class="main-content">
    <div class="<?php hocwp_wrap_class(); ?>">
        <div class="row">
            <div class="col-md-9 col-xs-12">
                <div class="main">
                    <div class="post entry">
                        <?php
                        hocwp_post_title_single(array('class' => 'page-title'));
                        hocwp_entry_content();
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-xs-12">
                <?php get_sidebar('page'); ?>
            </div>
        </div>
    </div>
</div>