<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}
get_header();
$show_on_front = get_option( 'show_on_front' );
if ( 'page' == $show_on_front ) {
	while ( have_posts() ) {
		the_post();
		hocwp_get_theme_template( 'front-page' );
	}
} else {
	hocwp_get_theme_template( 'index' );
}
get_footer();