<div class="main-content">
    <div class="<?php hocwp_wrap_class(); ?>">
        <div class="row">
            <div class="col-md-9 col-xs-12">
                <div class="main">
                    <div class="post entry">
                        <div class="entry-header">
                            <p class="entry-cat">
                                <?php the_category(', '); ?>
                            </p>
                            <?php hocwp_post_title_single(); ?>
                            <p class="entry-meta">
                                <span class="time"><?php the_date(); ?></span>
                                <i class="fa fa-circle"></i>
                                <span class="author">by <?php the_author(); ?></span>
                                <i class="fa fa-circle"></i>
                                <span class="add-comment">
                                    <?php comments_popup_link(); ?>
                                </span>
                            </p>
                        </div>
                        <?php hocwp_entry_content(); ?>
                        <div class="entry-footer">
                            <p class="entry-tag">
                                <?php the_tags('', ''); ?>
                            </p>
                        </div>
                    </div>
                    <?php hocwp_comments_template(); ?>
                </div>
            </div>
            <div class="col-md-3 col-xs-12">
                <?php get_sidebar('secondary'); ?>
            </div>
        </div>
    </div>
</div>