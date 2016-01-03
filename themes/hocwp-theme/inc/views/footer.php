<?php if(!function_exists('add_filter')) exit; ?>
<?php $maintenance_mode = hocwp_in_maintenance_mode(); ?>
</div><!-- .site-content -->
<?php do_action('hocwp_after_site_content'); ?>
<?php if(!$maintenance_mode) : ?>
    <?php do_action('hocwp_before_site_footer'); ?>
    <footer id="colophon" class="site-footer clearfix"<?php hocwp_html_tag_attributes('footer', 'site_footer'); ?>>
        <?php hocwp_theme_get_template('footer'); ?>
    </footer><!-- .site-footer -->
    <?php do_action('hocwp_after_site_footer'); ?>
<?php endif; ?>
</div><!-- .site-inner -->
</div><!-- .site -->
<?php
if(!$maintenance_mode) {
    do_action('hocwp_after_site');
    do_action('hocwp_before_wp_footer');
    wp_footer();
    do_action('hocwp_after_wp_footer');
    do_action('hocwp_close_body');
} else {
    do_action('hocwp_maintenance_footer');
}
?>
</body>
</html>