<?php
if(!function_exists('add_filter')) exit;
$sidebar = '404';
if(!is_active_sidebar($sidebar)) {
    $sidebar = 'page';
}
if(!is_active_sidebar($sidebar)) {
    $sidebar = 'secondary';
}
if(!is_active_sidebar($sidebar)) {
    $sidebar = 'primary';
}
if(is_active_sidebar($sidebar)) :
    do_action('hocwp_before_sidebar');
    do_action('hocwp_before_404_sidebar');
    ?>
    <div id="secondary" class="sidebar widget-area col-md-3 col-xs-12" role="complementary">
        <?php
        do_action('hocwp_before_sidebar_widget');
        do_action('hocwp_before_404_sidebar_widget');
        dynamic_sidebar($sidebar);
        do_action('hocwp_after_404_sidebar_widget');
        do_action('hocwp_after_sidebar_widget');
        ?>
    </div><!-- .sidebar .widget-area -->
    <?php
    do_action('hocwp_after_404_sidebar');
    do_action('hocwp_after_sidebar');
endif; ?>