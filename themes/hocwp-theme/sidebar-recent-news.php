<?php
if(!function_exists('add_filter')) exit;
do_action('hocwp_before_sidebar');
do_action('hocwp_before_recent_news_sidebar');
$args = array(
    'posts_per_page' => 10,
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key' => 'post_format',
            'compare' => 'NOT EXISTS'
        ),
        array(
            'key' => 'post_format',
            'value' => 'default',
            'compare' => '='
        ),
        array(
            'key' => 'post_format',
            'value' => '',
            'compare' => '='
        )
    )
);
$query = hocwp_query($args);
?>
<div id="secondary" class="sidebar widget-area" role="complementary">
    <?php
    do_action('hocwp_before_sidebar_widget');
    do_action('hocwp_before_recent_news_sidebar_widget');
    ?>
    <section class="widget widget-page-content">
        <?php the_content(); ?>
    </section>
    <section class="widget widget-recent-news">
        <h4 class="widget-title"><span>Tin mới</span></h4>
        <ul class="list-unstyled list-posts loop-post-full">
            <?php
            if($query->have_posts()) {
                while($query->have_posts()) {
                    $query->the_post();
                    hocwp_theme_get_loop('sidebar-post-full');
                }
                wp_reset_postdata();
            }
            ?>
        </ul>
    </section>
    <?php
    do_action('hocwp_after_recent_news_sidebar_widget');
    do_action('hocwp_after_sidebar_widget');
    ?>
</div><!-- .sidebar .widget-area -->
<?php
do_action('hocwp_after_recent_news_sidebar');
do_action('hocwp_after_sidebar');
