<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}
if ( is_active_sidebar( 'primary' ) ) {
	do_action( 'hocwp_before_sidebar' );
	do_action( 'hocwp_before_primary_sidebar' );
	$class = apply_filters( 'hocwp_sidebar_class', '', 'primary' );
	hocwp_add_string_with_space_before( $class, 'sidebar widget-area primary' );
	?>
	<div id="secondary" class="<?php echo $class; ?>" role="complementary">
		<?php
		do_action( 'hocwp_before_sidebar_widget' );
		do_action( 'hocwp_before_primary_sidebar_widget' );
		dynamic_sidebar( 'primary' );
		do_action( 'hocwp_after_primary_sidebar_widget' );
		do_action( 'hocwp_after_sidebar_widget' );
		?>
	</div><!-- .sidebar .widget-area -->
	<?php
	do_action( 'hocwp_after_primary_sidebar' );
	do_action( 'hocwp_after_sidebar' );
}