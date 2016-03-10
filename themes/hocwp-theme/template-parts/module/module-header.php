<?php
$popular_searches = hocwp_theme_custom_get_popular_searches();
$hs_placeholder = hocwp_theme_custom_get_search_placeholder();
?>
<div class="top-menus top-menu-nav">
    <div class="<?php hocwp_wrap_class(); ?>">
        <?php hocwp_theme_the_menu(array('theme_location' => 'top', 'nav_class' => 'pull-right')); ?>
    </div>
</div>
<div class="logo-area top-header">
    <div class="<?php hocwp_wrap_class(); ?>">
        <div class="row">
            <div class="logo pull-left col-lg-3 col-md-3 col-sm-3 col-xs-12 text-center">
                <?php hocwp_theme_the_logo(); ?>
            </div>
            <div class="col-md-9 col-sm-9 col-lg-9 col-xs-12 search">
                <form method="get" class="store-search text-left" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="input-group">
                        <input type="text" placeholder="<?php echo $hs_placeholder; ?>" id="s" name="s" value="">
                        <div class="input-group-addon">
                            <i class="df-search-ic"></i>
                        </div>
                    </div>
                    <?php if(hocwp_array_has_value($popular_searches)) : ?>
                        <p class="search-example hidden-sm hidden-xs">Popular Searches:
                            <?php
                            foreach($popular_searches as $search_query) {
                                echo '<a href="' . esc_url(home_url('?s=' . $search_query)) . '">' . $search_query . '</a>';
                            }
                            ?>
                        </p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
if(!is_home()) {
    ?>
    <div class="nav-links-bar">
        <div class="<?php hocwp_wrap_class(); ?>">
            <?php hocwp_breadcrumb(); ?>
        </div>
    </div>
    <?php
}
?>