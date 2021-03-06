<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

define( 'HOCWP_TERM_META_TABLE', 'termmeta' );

function hocwp_term_meta_table_init() {
	$version = hocwp_get_wp_version();
	if ( version_compare( $version, '4.4', '>=' ) ) {
		return;
	}
	global $wpdb;
	$max_index_length = 191;
	$table            = $wpdb->prefix . HOCWP_TERM_META_TABLE;
	$sql              = "meta_id bigint(20) unsigned NOT NULL auto_increment,
		term_id bigint(20) unsigned NOT NULL default '0',
		meta_key varchar(255) default NULL,
		meta_value longtext,
		PRIMARY KEY  (meta_id),
		KEY term_id (term_id),
		KEY meta_key (meta_key($max_index_length))";
	hocwp_create_database_table( $table, $sql );
}

add_action( 'after_switch_theme', 'hocwp_term_meta_table_init' );

function hocwp_term_register_termmeta_table() {
	$version = hocwp_get_wp_version();
	if ( version_compare( $version, '4.4', '>=' ) ) {
		return;
	}
	if ( ! hocwp_meta_table_registered( 'term' ) ) {
		global $wpdb;
		$wpdb->termmeta = $wpdb->prefix . HOCWP_TERM_META_TABLE;
	}
}

function hocwp_term_add_meta( $term_id, $meta_key, $meta_value, $unique = false ) {
	$version = hocwp_get_wp_version();
	if ( version_compare( $version, '4.4', '>=' ) ) {
		return add_term_meta( $term_id, $meta_key, $meta_value, $unique );
	}

	return add_metadata( 'term', $term_id, $meta_key, $meta_value, $unique );
}

function hocwp_term_get_meta( $term_id, $meta_key, $single = true ) {
	$version = hocwp_get_wp_version();
	if ( version_compare( $version, '4.4', '>=' ) ) {
		return get_term_meta( $term_id, $meta_key, $single );
	}
	hocwp_term_register_termmeta_table();

	return get_metadata( 'term', $term_id, $meta_key, $single );
}

function hocwp_term_update_meta( $term_id, $meta_key, $meta_value ) {
	$version = hocwp_get_wp_version();
	if ( version_compare( $version, '4.4', '>=' ) ) {
		return update_term_meta( $term_id, $meta_key, $meta_value );
	}
	hocwp_term_register_termmeta_table();

	return update_metadata( 'term', $term_id, $meta_key, $meta_value );
}

function hocwp_term_delete_meta( $term_id, $meta_key, $meta_value = '', $delete_all = false ) {
	$version = hocwp_get_wp_version();
	if ( version_compare( $version, '4.4', '>=' ) ) {
		return delete_term_meta( $term_id, $meta_key, $meta_value );
	}

	return delete_metadata( 'term', $term_id, $meta_value, $meta_value, $delete_all );
}

function hocwp_term_meta_icon_field( $taxonomies = array() ) {
	global $pagenow;
	if ( 'edit-tags.php' == $pagenow || 'term.php' == $pagenow ) {
		if ( ! hocwp_array_has_value( $taxonomies ) ) {
			$taxonomies = array( 'category' );
		}
		$meta = new HOCWP_Meta( 'term' );
		$meta->set_taxonomies( $taxonomies );
		$meta->set_use_media_upload( true );
		$meta->add_field(
			array(
				'id'             => 'icon',
				'label'          => __( 'Icon', 'hocwp-theme' ),
				'field_callback' => 'hocwp_field_media_upload'
			)
		);
		$meta->add_field(
			array(
				'id'    => 'icon_html',
				'label' => __( 'Icon HTML', 'hocwp-theme' )
			)
		);
		$meta->init();
	}
}

function hocwp_term_meta_icon_html_field( $taxonomies = array() ) {
	_deprecated_function( __FUNCTION__, '3.3.8', 'hocwp_term_meta_icon_field' );
}

function hocwp_term_meta_color_field( $taxonomies = array() ) {
	global $pagenow;
	if ( 'edit-tags.php' == $pagenow || 'term.php' == $pagenow ) {
		if ( ! hocwp_array_has_value( $taxonomies ) ) {
			$taxonomies = array( 'category' );
		}
		$meta = new HOCWP_Meta( 'term' );
		$meta->set_taxonomies( $taxonomies );
		$meta->set_use_color_picker( true );
		$meta->add_field( array(
			'id'             => 'color',
			'label'          => __( 'Color', 'hocwp-theme' ),
			'field_callback' => 'hocwp_field_color_picker'
		) );
		$meta->init();
	}
}

function hocwp_term_meta_long_description_field( $taxonomies = array() ) {
	global $pagenow;
	if ( 'edit-tags.php' == $pagenow || 'term.php' == $pagenow ) {
		if ( ! hocwp_array_has_value( $taxonomies ) ) {
			$taxonomies = array( 'category' );
		}
		$meta = new HOCWP_Meta( 'term' );
		$meta->set_taxonomies( $taxonomies );
		$meta->set_use_media_upload( true );
		$meta->add_field(
			array(
				'id'             => 'long_description',
				'label'          => __( 'Long description', 'hocwp-theme' ),
				'field_callback' => 'hocwp_field_editor'
			)
		);
		$meta->init();
	}
}

function hocwp_term_meta_thumbnail_field( $taxonomies = array() ) {
	global $pagenow;
	if ( 'edit-tags.php' == $pagenow || 'term.php' == $pagenow ) {
		if ( ! hocwp_array_has_value( $taxonomies ) ) {
			$taxonomies = array( 'category' );
		}
		$meta = new HOCWP_Meta( 'term' );
		$meta->set_taxonomies( $taxonomies );
		$meta->set_use_media_upload( true );
		$meta->add_field(
			array(
				'id'             => 'thumbnail',
				'label'          => __( 'Thumbnail', 'hocwp-theme' ),
				'field_callback' => 'hocwp_field_media_upload'
			)
		);
		$meta->init();
	}
}

function hocwp_term_meta_banner_field( $taxonomies = array(), $rotate = false ) {
	global $pagenow;
	if ( 'edit-tags.php' == $pagenow || 'term.php' == $pagenow ) {
		if ( ! hocwp_array_has_value( $taxonomies ) ) {
			$taxonomies = array( 'category' );
		}
		$meta = new HOCWP_Meta( 'term' );
		$meta->set_taxonomies( $taxonomies );
		$meta->set_use_media_upload( true );
		$meta->add_field(
			array(
				'id'             => 'banner',
				'label'          => __( 'Banner', 'hocwp-theme' ),
				'field_callback' => 'hocwp_field_media_upload'
			)
		);
		if ( $rotate ) {
			$meta->add_field(
				array(
					'id'             => 'horizontal_banner',
					'label'          => __( 'Horizontal Banner', 'hocwp-theme' ),
					'field_callback' => 'hocwp_field_media_upload'
				)
			);
			$meta->add_field(
				array(
					'id'             => 'vertical_banner',
					'label'          => __( 'Vertical Banner', 'hocwp-theme' ),
					'field_callback' => 'hocwp_field_media_upload'
				)
			);
		}
		$meta->init();
	}
}

function hocwp_term_meta_different_name_field( $taxonomies = array() ) {
	global $pagenow;
	if ( 'edit-tags.php' == $pagenow || 'term.php' == $pagenow ) {
		if ( ! hocwp_array_has_value( $taxonomies ) ) {
			$taxonomies = get_taxonomies( array( 'public' => true ) );
		}
		$taxonomies = apply_filters( 'hocwp_term_different_name_field_taxonomies', $taxonomies );
		hocwp_exclude_special_taxonomies( $taxonomies );
		if ( ! hocwp_array_has_value( $taxonomies ) ) {
			$taxonomies = array( 'category' );
		}
		$meta = new HOCWP_Meta( 'term' );
		$meta->set_taxonomies( $taxonomies );
		$meta->add_field(
			array(
				'id'    => 'different_name',
				'label' => __( 'Different Name', 'hocwp-theme' )
			)
		);
		$meta->init();
	}
}