<?php
$fsb_title = hocwp_theme_get_option('fsb_title');
$fsb_description = hocwp_theme_get_option('fsb_description');
$copyright = hocwp_theme_get_footer_text();
if(is_active_sidebar('footer')) {
	?>
	<div class="footer-links">
		<div class="<?php hocwp_wrap_class(); ?>">
			<?php dynamic_sidebar('footer'); ?>
		</div>
	</div>
	<?php
}
if(!empty($copyright)) {
	?>
	<div class="copyright">
		<div class="<?php hocwp_wrap_class('copyright-inner'); ?>">
			<div class="copyright-info">
				<?php echo wpautop($copyright); ?>
			</div>
			<div class="links">
				<?php hocwp_theme_the_menu(array('theme_location' => 'footer')); ?>
			</div>
		</div>
	</div>
	<?php
}