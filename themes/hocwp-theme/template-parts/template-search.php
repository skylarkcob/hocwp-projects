<?php
$search_query = get_search_query();
?>
<div class="sub-header">
    <div class="<?php hocwp_wrap_class(); ?>">
        <div class="row">
            <div class="col-md-12">
                <h1 class="store-subtitle">Search for '<?php echo $search_query; ?>'</h1>
            </div>
        </div>
    </div>
</div>
<div class="main-content">
    <div class="<?php hocwp_wrap_class(); ?>">
        <div class="row">
            <div class="col-md-9 col-xs-12">
                <div class="main">
                    <div class="coupon-all-box">
                        <?php
                        if(have_posts()) {
                            while(have_posts()) {
                                the_post();
                                hocwp_theme_get_loop('archive-coupon');
                            }
                            $pagination_args = array(
                                'label' => '',
                                'first' => '',
                                'last' => '',
                                'next' => __('Next', 'hocwp'),
                                'prev' => __('Prev', 'hocwp')
                            );
                            hocwp_pagination($pagination_args);
                        } else {
                            echo '<p class="margin-top-10 nothing-coupon">' . __('Sorry, no coupons could be found.', 'hocwp') . '</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-xs-12">
                <?php get_sidebar(); ?>
            </div>
        </div>
    </div>
</div>