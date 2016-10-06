<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}
$sidebar = hocwp_get_post_meta( 'sidebar' );
if ( ! is_active_sidebar( $sidebar ) ) {
	$sidebar = 'page';
}
if ( is_active_sidebar( $sidebar ) ) {
	do_action( 'hocwp_before_sidebar' );
	do_action( 'hocwp_before_page_sidebar' );
	$class = apply_filters( 'hocwp_sidebar_class', '', 'page' );
	hocwp_add_string_with_space_before( $class, 'sidebar widget-area page' );
	?>
	<div id="secondary" class="<?php echo $class; ?>" role="complementary">
		<?php
		do_action( 'hocwp_before_sidebar_widget' );
		do_action( 'hocwp_before_page_sidebar_widget' );
		dynamic_sidebar( $sidebar );
		do_action( 'hocwp_after_page_sidebar_widget' );
		do_action( 'hocwp_after_sidebar_widget' );
		?>
	</div><!-- .sidebar .widget-area -->
	<?php
	do_action( 'hocwp_after_page_sidebar' );
	do_action( 'hocwp_after_sidebar' );
} else {
	get_sidebar( 'secondary' );
}