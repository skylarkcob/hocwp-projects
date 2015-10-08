<li>
    <?php
    hocwp_article_before('post icon-play-medium');
    hocwp_post_thumbnail(array('width' => 310, 'height' => 200));
    hocwp_theme_custom_post_label();
    hocwp_theme_custom_post_icon_play();
    hocwp_post_title_link();
    the_excerpt();
    ?>
    <div class="post-meta margin-top-15">
        <?php the_author_posts_link(); ?>
        -
        <?php the_category(', '); ?>
        <?php echo get_the_date('d/m/Y H:i'); ?>
    </div>
    <?php
    hocwp_article_after();
    ?>
</li>