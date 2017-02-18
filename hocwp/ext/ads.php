<?php
function hocwp_use_ads_system() {
	return apply_filters( 'hocwp_use_ads_system', false );
}

$use = hocwp_use_ads_system();

if ( ! $use ) {
	return;
}

function hocwp_show_ads_in_post_content_after_first_paragraph( $html ) {
	ob_start();
	hocwp_show_ads( 'after_first_paragraph' );
	$html = ob_get_clean();

	return $html;
}

add_filter( 'hocwp_add_to_the_content_after_first_paragraph', 'hocwp_show_ads_in_post_content_after_first_paragraph' );