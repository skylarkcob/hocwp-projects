<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

add_filter( 'hocwp_use_session', '__return_true' );
add_filter( 'hocwp_theme_switcher_enabled', '__return_true' );