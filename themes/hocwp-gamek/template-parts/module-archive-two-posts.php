<?php
global $wp_query;
if($wp_query->have_posts()) {
    $two_posts = array_slice($wp_query->posts, 0, 2);
    ?>
    <div class="two-posts four-recent-posts clearfix">
        <div class="col-left">
            <?php
            $post = array_shift($two_posts);
            setup_postdata($post);
            hocwp_theme_custom_loop_top_large_post(339, 262);
            wp_reset_postdata();
            ?>
        </div>
        <div class="col-right">
            <?php
            if(hocwp_array_has_value($two_posts)) {
                $post = array_pop($two_posts);
                setup_postdata($post);
                hocwp_theme_custom_loop_top_large_post(278, 262);
                wp_reset_postdata();
            }
            ?>
        </div>
    </div>
    <?php
} else {
    hocwp_theme_get_content_none();
}