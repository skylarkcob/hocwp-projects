<?php
$count_post_format = 0;
$excludes = array();
$args = array(
    'posts_per_page' => 4,
    'post__not_in' => $excludes
);
$query = hocwp_query($args);
if($query->have_posts()) {
    $left_posts = array();
    $right_posts = array();
    $count = 0;
    foreach($query->posts as $post) {
        $excludes[] = $post->ID;
        if($count % 2 == 0) {
            $left_posts[] = $post;
        } else {
            $right_posts[] = $post;
        }
        $count++;
    }
    ?>
    <div class="four-recent-posts clearfix">
        <div class="col-left">
            <?php
            $post = array_shift($left_posts);
            setup_postdata($post);
            hocwp_theme_custom_loop_top_large_post(339, 262);
            wp_reset_postdata();
            $post = array_shift($left_posts);
            setup_postdata($post);
            hocwp_theme_custom_loop_top_large_post(339, 163);
            wp_reset_postdata();
            ?>
        </div>
        <div class="col-right">
            <?php
            $post = array_shift($right_posts);
            setup_postdata($post);
            hocwp_theme_custom_loop_top_large_post(278, 262);
            wp_reset_postdata();
            $post = array_shift($right_posts);
            setup_postdata($post);
            hocwp_theme_custom_loop_top_large_post(278, 163);
            wp_reset_postdata();
            ?>
        </div>
    </div>
    <?php
} else {
    hocwp_theme_get_content_none();
}
$args['posts_per_page'] = 6;
$args['post__not_in'] = $excludes;
$query = hocwp_query($args);
if($query->have_posts()) {
    ?>
    <div class="six-recent-posts clearfix">
        <div class="col-left">
            <?php
            while($query->have_posts()) {
                $query->the_post();
                hocwp_theme_get_loop('six-recent-posts');
                $excludes[] = get_the_ID();
            }
            wp_reset_postdata();
            ?>
        </div>
        <div class="col-right">
            <?php
            $exp_args = array(
                'posts_per_page' => 2,
                'meta_key' => 'featured',
                'meta_value' => 1
            );
            $exp_query = hocwp_query_post_by_format('experience', $exp_args);
            if($exp_query->have_posts()) {
                $count_post_format += $exp_query->post_count;
                $pages = hocwp_get_pages_by_template('experience.php', array('posts_per_page' => 1));
                $a = new HOCWP_HTML('a');
                $a->set_text(__('Video đáng chú ý', 'hocwp') . '<span></span>');
                if(hocwp_array_has_value($pages)) {
                    $page = array_shift($pages);
                    $a->set_attribute('href', get_permalink($page->ID));
                } else {
                    $a->set_attribute('href', '#');
                }
                ?>
                <div class="experience-news clearfix">
                    <h2><?php $a->output(); ?></h2>
                    <ul class="list-unstyled list-sidebar-posts">
                        <?php
                        while($exp_query->have_posts()) {
                            $exp_query->the_post();
                            hocwp_theme_get_loop('sidebar-post');
                            $excludes[] = get_the_ID();
                        }
                        wp_reset_postdata();
                        ?>
                    </ul>
                </div>
                <?php
            }
            $query = hocwp_query_post_by_format('review', array('posts_per_page' => 5, 'post__not_in' => $excludes));
            if($query->have_posts()) {
                $count_post_format += $query->post_count;
                $pages = hocwp_get_pages_by_template('review.php', array('posts_per_page' => 1));
                $a = new HOCWP_HTML('a');
                $a->set_text(__('Đánh giá/Giới thiệu', 'hocwp') . '<span></span>');
                if(hocwp_array_has_value($pages)) {
                    $page = array_shift($pages);
                    $a->set_attribute('href', get_permalink($page->ID));
                } else {
                    $a->set_attribute('href', '#');
                }
                ?>
                <div class="experience-news clearfix">
                    <h2><?php $a->output(); ?></h2>
                    <ul class="list-unstyled list-sidebar-posts">
                        <?php
                        while($query->have_posts()) {
                            $query->the_post();
                            hocwp_theme_get_loop('sidebar-post');
                            $excludes[] = get_the_ID();
                        }
                        wp_reset_postdata();
                        ?>
                    </ul>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
}
hocwp_theme_get_module('home-game-event');
$args = array(
    'posts__not_in' => $excludes,
    'posts_per_page' => 10
);
$query = hocwp_query($args);
if($query->have_posts()) {
    ?>
    <div class="more-recent">
        <h2><a href="#"><?php _e('Mới cập nhật', 'hocwp'); ?></a></h2>
        <ul class="list-unstyled list-more-recent-posts loop-default clearfix">
            <?php
            while($query->have_posts()) {
                $query->the_post();
                hocwp_theme_get_loop('home-more-recent-posts');
                $excludes[] = get_the_ID();
            }
            wp_reset_postdata();
            ?>
        </ul>
        <?php if($query->post_count >= 10) : ?>
            <div class="paging margin-top-10" data-offset="0" data-paged="1" data-posts-per-page="10" data-query-vars="">
                <a href="javascript:void(0)" class="load-more">Xem thêm </a>
            </div>
        <?php endif; ?>
    </div>
    <?php
}
update_option('hocwp_home_post_not_in', $excludes);