<?php
define('HOCWP_TERM_META_TABLE', 'termmeta');

function hocwp_term_meta_table_init() {
	global $wpdb;
	$max_index_length = 191;
	$charset_collate = '';
	if(!empty($wpdb->charset)) {
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	}
	if(!empty($wpdb->collate)) {
		$charset_collate .= " COLLATE $wpdb->collate";
	}
	$table = $wpdb->prefix . HOCWP_TERM_META_TABLE;
	$sql = "CREATE TABLE $table (
        meta_id bigint(20) unsigned NOT NULL auto_increment,
		term_id bigint(20) unsigned NOT NULL default '0',
		meta_key varchar(255) default NULL,
		meta_value longtext,
		PRIMARY KEY  (meta_id),
		KEY term_id (term_id),
		KEY meta_key (meta_key($max_index_length))
	) $charset_collate;\n";
	if(!function_exists('dbDelta')) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	}
	dbDelta($sql);
}
add_action('after_switch_theme', 'hocwp_term_meta_table_init');

function hocwp_term_register_termmeta_table() {
	if(!hocwp_meta_table_registered('term')) {
		global $wpdb;
		$wpdb->termmeta = $wpdb->prefix . HOCWP_TERM_META_TABLE;
	}
}

function hocwp_term_add_meta($term_id, $meta_key, $meta_value, $unique = false) {
	return add_metadata('term', $term_id, $meta_key, $meta_value, $unique);
}

function hocwp_term_get_meta($term_id, $meta_key, $single = true) {
	hocwp_term_register_termmeta_table();
	return get_metadata('term', $term_id, $meta_key, $single);
}

function hocwp_term_update_meta($term_id, $meta_key, $meta_value) {
	hocwp_term_register_termmeta_table();
	return update_metadata('term', $term_id, $meta_key, $meta_value);
}

function hocwp_term_delete_meta($term_id, $meta_key, $meta_value = '', $delete_all = false) {
	return delete_metadata('term', $term_id, $meta_value, $meta_value, $delete_all);
}