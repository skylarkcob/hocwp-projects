<div class="logo-area top-header">
    <div class="<?php hocwp_wrap_class(); ?>">
        <div class="row">
            <div class="logo pull-left col-lg-4 col-md-4 col-sm-4 col-xs-12 text-center">
                <?php hocwp_theme_the_logo(); ?>
            </div>
            <div class="col-md-8 col-sm-8 col-lg-8 col-xs-12 leaderboard">
                <?php hocwp_show_ads('leaderboard'); ?>
            </div>
        </div>
    </div>
</div>
<div class="primary-menus">
    <div class="<?php hocwp_wrap_class(); ?>">
        <div class="row">
            <div class="col-xs-12">
                <?php hocwp_theme_the_menu(array('theme_location' => 'primary')); ?>
            </div>
        </div>
    </div>
</div>