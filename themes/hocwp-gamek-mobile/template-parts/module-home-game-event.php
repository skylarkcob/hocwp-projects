<?php
$args = array(
    'posts_per_page' => 1,
    'post_type' => 'event'
);
$query = hocwp_query($args);
if($query->have_posts()) {
    ?>
    <div class="game-event">
        <div class="left">
            <h2>SỰ KIỆN<span>ĐÁNG CHÚ Ý</span></h2>
        </div>
        <div class="event-content clear padding-top-10">
            <?php
            $event_url = '';
            while($query->have_posts()) {
                $query->the_post();
                the_content();
                $event_url = get_post_meta(get_the_ID(), 'event_url', true);
            }
            wp_reset_postdata();
            ?>
            <a target="_blank" href="<?php echo $event_url; ?>" class="view-detail">Chi tiết</a>
        </div>
    </div>
    <?php
}