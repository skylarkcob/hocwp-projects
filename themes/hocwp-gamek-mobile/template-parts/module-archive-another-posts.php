<?php
global $wp_query;
if($wp_query->have_posts()) {
    $posts = array_slice($wp_query->posts, 2, $wp_query->post_count - 2);
    if(hocwp_array_has_value($posts)) {
        ?>
        <ul class="list-unstyled list-more-recent-posts loop-default clearfix">
            <?php
            foreach($posts as $post) {
                setup_postdata($post);
                hocwp_theme_get_loop('home-more-recent-posts');
            }
            wp_reset_postdata();
            ?>
        </ul>
        <?php
    }
}