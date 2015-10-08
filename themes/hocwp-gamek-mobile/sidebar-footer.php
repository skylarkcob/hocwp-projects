<?php
if(!function_exists('add_filter')) exit;
if(!is_active_sidebar('footer')) {
    return;
}
do_action('hocwp_before_footer_widget_area');
?>
<div id="footer_widget_area" class="widget-area" role="complementary">
    <?php
    do_action('hocwp_before_footer_widget_area_widget');
    dynamic_sidebar('footer');
    do_action('hocwp_after_footer_widget_area_widget');
    ?>
</div><!-- .widget-area -->
<?php do_action('hocwp_after_footer_widget_area'); ?>