<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}
$sidebar = '404';
if ( is_active_sidebar( $sidebar ) ) {
	do_action( 'hocwp_before_sidebar' );
	do_action( 'hocwp_before_404_sidebar' );
	$class = apply_filters( 'hocwp_sidebar_class', '', '404' );
	hocwp_add_string_with_space_before( $class, 'sidebar widget-area not-found' );
	?>
	<div id="secondary" class="<?php echo $class; ?>" role="complementary">
		<?php
		do_action( 'hocwp_before_sidebar_widget' );
		do_action( 'hocwp_before_404_sidebar_widget' );
		dynamic_sidebar( $sidebar );
		do_action( 'hocwp_after_404_sidebar_widget' );
		do_action( 'hocwp_after_sidebar_widget' );
		?>
	</div><!-- .sidebar .widget-area -->
	<?php
	do_action( 'hocwp_after_404_sidebar' );
	do_action( 'hocwp_after_sidebar' );
} else {
	get_sidebar( 'page' );
}