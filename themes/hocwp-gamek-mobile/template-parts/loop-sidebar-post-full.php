<li>
    <?php
    hocwp_article_before();
    hocwp_post_thumbnail(array('width' => 300, 'height' => 200));
    hocwp_post_title_link();
    the_excerpt();
    hocwp_article_after();
    ?>
</li>