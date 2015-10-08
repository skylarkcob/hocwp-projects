<li>
    <?php
    hocwp_article_before();
    hocwp_post_thumbnail(array('width' => 220, 'height' => 160, 'loop' => true));
    hocwp_post_title_link();
    ?>
    <div class="post-meta margin-top-10">
        <?php echo get_the_date('d/m/Y H:i'); ?>
    </div>
    <?php
    the_excerpt();
    hocwp_article_after();
    ?>
</li>