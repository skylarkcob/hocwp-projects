<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}
get_header();
hocwp_get_theme_template( 'index' );
get_footer();