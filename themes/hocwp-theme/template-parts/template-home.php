<?php
$ts_rows = hocwp_theme_custom_get_home_top_store_row();
$top_stores = hocwp_get_top_store_by_coupon_count(array('number' => 0, 'hide_empty' => false, 'order' => 'DESC'));
$stores = array_slice($top_stores, 0, ($ts_rows * 5));
if(hocwp_array_has_value($stores)) {
    ?>
    <div class="featured-coupons">
        <div class="<?php hocwp_wrap_class(); ?>">
            <div class="row">
                <?php
                foreach($stores as $term) {
                    $thumbnail_url = hocwp_term_get_thumbnail_url(array('term' => $term, 'width' => 180, 'height' => 110));
                    ?>
                    <div class="col-sm-2 col-md-15 col-sm-15 col-xs-6 item-container">
                        <div class="item">
                            <a href="<?php echo get_term_link($term); ?>">
                                <img class="img-responsive" src="<?php echo $thumbnail_url; ?>">
                            </a>
                            <p><?php printf(__('%s coupons', 'hocwp'), $term->count); ?></p>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}
?>
<div class="homepage-coupons">
    <div class="<?php hocwp_wrap_class(); ?>">
        <h2><?php echo hocwp_theme_custom_get_home_coupon_box_title(); ?></h2>
        <div class="row">
            <?php
            if(have_posts()) {
                while(have_posts()) {
                    the_post();
                    hocwp_theme_get_loop('home-coupon');
                }
            } else {
                echo '<p class="margin-top-10">' . __('There is no coupon on your site.', 'hocwp') . '</p>';
            }
            ?>
        </div>
    </div>
</div>
<div class="popular-tabs">
    <div class="<?php hocwp_wrap_class(); ?>">
        <ul class="tabs-nav clearfix">
            <li class="active">
                <a data-toggle="tab" href="#store" aria-expanded="true"><?php _e('Popular Stores', 'hocwp'); ?></a>
            </li>
            <li class="">
                <a data-toggle="tab" href="#categogies" aria-expanded="false"><?php _e('Popular Categories', 'hocwp'); ?></a>
            </li>
        </ul>
        <div class="line"></div>
        <div class="tab-content">
            <?php
            $stores = array_slice($top_stores, 0, 15);
            $categories = hocwp_get_top_category_by_coupon_count(array('number' => 15, 'hide_empty' => 0));
            ?>
            <div id="store" class="tab-pane popular-store active">
                <?php
                if(hocwp_array_has_value($stores)) {
                    ?>
                    <ul class="clearfix">
                        <?php
                        foreach($stores as $term) {
                            echo '<li><a href="' . get_term_link($term) . '">' . $term->name . '</a></li>';
                        }
                        ?>
                    </ul>
                    <?php
                } else {
                    echo '<p class="margin-top-10">' . __('There is no store on your site.', 'hocwp') . '</p>';
                }
                ?>
            </div>
            <div id="categogies" class="tab-pane popluar-categogies popular-categories">
                <?php
                if(hocwp_array_has_value($categories)) {
                    ?>
                    <ul class="clearfix">
                        <?php
                        foreach($categories as $term) {
                            echo '<li><a href="' . get_term_link($term) . '">' . $term->name . '</a></li>';
                        }
                        ?>
                    </ul>
                    <?php
                } else {
                    echo '<p class="margin-top-10">' . __('There is no store on your site.', 'hocwp') . '</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
