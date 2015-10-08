<?php
if(!function_exists('add_filter')) exit;
if(is_active_sidebar('secondary')) :
    do_action('hocwp_before_sidebar');
    do_action('hocwp_before_secondary_sidebar');
    ?>
    <div id="secondary" class="sidebar widget-area" role="complementary">
        <?php
        do_action('hocwp_before_sidebar_widget');
        do_action('hocwp_before_secondary_sidebar_widget');
        dynamic_sidebar('secondary');
        do_action('hocwp_after_secondary_sidebar_widget');
        do_action('hocwp_after_sidebar_widget');
        ?>
    </div><!-- .sidebar .widget-area -->
    <?php
    do_action('hocwp_after_secondary_sidebar');
    do_action('hocwp_after_sidebar');
endif; ?>
