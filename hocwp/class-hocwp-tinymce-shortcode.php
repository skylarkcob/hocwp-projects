<?php
/*
 * Name: HocWP TinyMCE Shortcode
 * Version: 1.0.0
 * Last updated: 24/02/2016
 */
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

class HOCWP_TinyMCE_Shortcode {
	public function __construct() {
		$mce        = new HOCWP_TinyMCE();
		$script_url = HOCWP_URL . '/js/hocwp-tinymce-shortcode-button' . HOCWP_JS_SUFFIX;
		$item_args  = array(
			'name'   => 'hocwp_shortcode',
			'script' => $script_url,
			'type'   => 'listbox'
		);
		$mce->add_item( $item_args, 2 );
		$mce->init();
	}
}