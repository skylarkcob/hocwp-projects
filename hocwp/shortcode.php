<?php
$use_shortcode = apply_filters('hocwp_add_tiny_mce_shortcode_button', false);
if($use_shortcode) {
	new HOCWP_TinyMCE_Shortcode();
}