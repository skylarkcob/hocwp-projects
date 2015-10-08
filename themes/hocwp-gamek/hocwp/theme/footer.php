<?php if(!function_exists('add_filter')) exit; ?>
</div><!-- .site-content -->
<?php
do_action('hocwp_after_site_content');
do_action('hocwp_before_site_footer');
?>
<footer id="colophon" class="site-footer" role="contentinfo">
    <?php hocwp_theme_get_template('footer'); ?>
</footer><!-- .site-footer -->
<?php do_action('hocwp_after_site_footer'); ?>
</div><!-- .site-inner -->
</div><!-- .site -->
<?php
do_action('hocwp_after_site');
do_action('hocwp_before_wp_footer');
wp_footer();
do_action('hocwp_after_wp_footer');
do_action('hocwp_close_body');
?>
</body>
</html>