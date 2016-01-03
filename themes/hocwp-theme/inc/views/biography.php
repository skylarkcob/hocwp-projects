<?php
if(!function_exists('add_filter')) exit;
do_action('hocwp_before_author_box');
?>
<div class="author-info">
    <div class="author-avatar">
        <?php
        $author_bio_avatar_size = apply_filters('hocwp_author_bio_avatar_size', 49);
        echo get_avatar(get_author_meta('user_email'), $author_bio_avatar_size);
        ?>
    </div><!-- .author-avatar -->
    <div class="author-description">
        <h3 class="author-title">
            <span class="author-heading"><?php _e('Author:', 'hocwp'); ?></span> <?php echo esc_html(get_author()); ?>
        </h3>
        <p class="author-bio">
            <?php author_meta('description'); ?>
            <a class="author-link" href="<?php echo esc_url(get_author_posts_url(get_author_meta('ID'))); ?>" rel="author">
                <?php printf(esc_html__('View all posts by %s', 'hocwp'), esc_html(get_author())); ?>
            </a>
        </p><!-- .author-bio -->
    </div><!-- .author-description -->
</div><!-- .author-info -->
<?php do_action('hocwp_after_author_box'); ?>