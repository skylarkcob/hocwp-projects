<?php
if(!function_exists('add_filter')) exit;
function hocwp_meta_table_registered($type) {
	return _get_meta_table($type);
}

function hocwp_meta_box_post_attribute($post_types) {
	$post_type = hocwp_get_current_post_type();
	if(empty($post_type)) {
		return;
	}
	if(is_array($post_type)) {
		$post_type = current($post_type);
	}
	$post_type =  hocwp_uppercase_first_char_only($post_type);
	$meta_id = $post_type . '_attributes';
	$meta_id = hocwp_sanitize_id($meta_id);
	$meta = new HOCWP_Meta('post');
	$meta->set_post_types($post_types);
	$meta->set_id($meta_id);
	$meta->set_title($post_type . ' Attributes');
	$meta->set_context('side');
	$meta->set_priority('core');
	$meta->init();
}