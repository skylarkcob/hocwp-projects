<?php
if(!function_exists('add_filter')) exit;
function hocwp_meta_table_registered($type) {
	return _get_meta_table($type);
}

function hocwp_meta_box_post_attribute($post_types) {
	global $pagenow;
	if('edit.php' == $pagenow || 'post.php' == $pagenow) {
		$post_type = hocwp_get_current_post_type();
		if(is_array($post_type)) {
			$post_type = current($post_type);
		}
		if(empty($post_type)) {
			$post_type = 'post';
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
}

function hocwp_meta_box_side_image($args = array()) {
	global $pagenow;
	if('edit.php' == $pagenow || 'post.php' == $pagenow) {
		$id = hocwp_get_value_by_key($args, 'id', 'secondary_image_box');
		$title = hocwp_get_value_by_key($args, 'title', __('Secondary Image', 'hocwp'));
		$post_types = hocwp_get_value_by_key($args, 'post_type');
		if('all' == $post_types) {
			$post_types = array();
			$types = get_post_types(array('public' => true), 'objects');
			hocwp_exclude_special_post_types($types);
			foreach($types as $key => $object_type) {
				$post_types[] = $key;
			}
		}
		$post_types = hocwp_sanitize_array($post_types);
		$field_id = hocwp_get_value_by_key($args, 'field_id', 'secondary_image');
		if(!hocwp_array_has_value($post_types)) {
			return;
		}
		$meta = new HOCWP_Meta('post');
		$meta->set_post_types($post_types);
		$meta->set_id($id);
		$meta->set_title($title);
		$meta->set_context('side');
		$meta->set_priority('low');
		$field_args = array('id' => $field_id, 'field_callback' => 'hocwp_field_media_upload_simple');
		$field_name = hocwp_get_value_by_key($args, 'field_name', $field_id);
		$field_args['name'] = $field_name;
		$meta->add_field($field_args);
		$meta->init();
	}
}

function hocwp_meta_box_page_additional_information() {
	global $pagenow;
	if('edit.php' == $pagenow || 'post.php' == $pagenow) {
		$meta = new HOCWP_Meta('post');
		$meta->set_title(__('Additional Information', 'hocwp'));
		$meta->set_id('page_additional_information');
		$meta->set_post_types(array('page'));
		$meta->add_field(array('id' => 'different_title', 'label' => __('Different title:', 'hocwp')));
		$meta->add_field(array('id' => 'sidebar', 'label' => __('Sidebar', 'hocwp'), 'field_callback' => 'hocwp_field_select_sidebar'));
		$meta->init();
	}
}

function hocwp_meta_box_editor($args = array()) {
	global $pagenow;
	if('edit.php' == $pagenow || 'post.php' == $pagenow) {
		$post_type = hocwp_get_value_by_key($args, 'post_type');
		if(!is_array($post_type)) {
			$post_type = array($post_type);
		}
		$box_title = hocwp_get_value_by_key($args, 'title', __('Additional Information', 'hocwp'));
		$current_type = hocwp_get_current_post_type();
		if(is_array($current_type)) {
			$current_type = current($current_type);
		}
		$box_id = hocwp_get_value_by_key($args, 'id');
		if(empty($box_id)) {
			return;
		}
		if(!empty($current_type)) {
			$box_id = $current_type . '_' . $box_id;
		}
		$field_args = hocwp_get_value_by_key($args, 'field_args', array());
		$field_args = hocwp_sanitize_array($field_args);
		$field_args['field_callback'] = 'hocwp_field_editor';
		$field_args['label'] = '';
		$field_id = hocwp_get_value_by_key($args, 'field_id', hocwp_get_value_by_key($field_args, 'field_id'));
		$field_name = hocwp_get_value_by_key($args, 'field_name', hocwp_get_value_by_key($field_args, 'field_name'));
		hocwp_transmit_id_and_name($field_id, $field_name);
		if(empty($field_id)) {
			return;
		}
		$field_args['id'] = $field_id;
		$field_args['name'] = $field_name;
		$meta = new HOCWP_Meta('post');
		$meta->set_title($box_title);
		$meta->set_id($box_id);
		$meta->set_post_types($post_type);
		$meta->add_field($field_args);
		$meta->init();
	}
}