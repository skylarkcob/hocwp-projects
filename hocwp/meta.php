<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}
function hocwp_meta_table_registered( $type ) {
	return _get_meta_table( $type );
}

function hocwp_meta_box_post_attribute( $post_types ) {
	global $pagenow;
	if ( 'post-new.php' == $pagenow || 'post.php' == $pagenow ) {
		$post_type = hocwp_get_current_post_type();
		if ( is_array( $post_type ) ) {
			$post_type = current( $post_type );
		}
		if ( empty( $post_type ) ) {
			$post_type = 'post';
		}
		$post_type = hocwp_uppercase_first_char_only( $post_type );
		$meta_id   = $post_type . '_attributes';
		$meta_id   = hocwp_sanitize_id( $meta_id );
		$meta      = new HOCWP_Meta( 'post' );
		$meta->set_post_types( $post_types );
		$meta->set_id( $meta_id );
		$meta->set_title( $post_type . ' Attributes' );
		$meta->set_context( 'side' );
		$meta->set_priority( 'core' );
		$meta->init();
	}
}

function hocwp_meta_box_side_image( $args = array() ) {
	global $pagenow;
	if ( 'post-new.php' == $pagenow || 'post.php' == $pagenow ) {
		$id         = hocwp_get_value_by_key( $args, 'id', 'secondary_image_box' );
		$title      = hocwp_get_value_by_key( $args, 'title', __( 'Secondary Image', 'hocwp-theme' ) );
		$post_types = hocwp_get_value_by_key( $args, 'post_type' );
		if ( 'all' == $post_types ) {
			$post_types = array();
			$types      = get_post_types( array( 'public' => true ), 'objects' );
			hocwp_exclude_special_post_types( $types );
			foreach ( $types as $key => $object_type ) {
				$post_types[] = $key;
			}
		}
		$post_types = hocwp_sanitize_array( $post_types );
		$field_id   = hocwp_get_value_by_key( $args, 'field_id', 'secondary_image' );
		$post_types = apply_filters( 'hocwp_post_type_user_large_thumbnail', $post_types );
		if ( ! hocwp_array_has_value( $post_types ) ) {
			return;
		}
		$meta = new HOCWP_Meta( 'post' );
		$meta->set_post_types( $post_types );
		$meta->set_id( $id );
		$meta->set_title( $title );
		$meta->set_context( 'side' );
		$meta->set_priority( 'low' );
		$field_args         = array( 'id' => $field_id, 'field_callback' => 'hocwp_field_media_upload_simple' );
		$field_name         = hocwp_get_value_by_key( $args, 'field_name', $field_id );
		$field_args['name'] = $field_name;
		$meta->add_field( $field_args );
		$meta->init();
	}
}

function hocwp_meta_box_page_additional_information() {
	global $pagenow;
	if ( 'post-new.php' == $pagenow || 'post.php' == $pagenow ) {
		$meta = new HOCWP_Meta( 'post' );
		$meta->set_title( __( 'Additional Information', 'hocwp-theme' ) );
		$meta->set_id( 'page_additional_information' );
		$meta->set_post_types( array( 'page' ) );
		$meta->add_field( array( 'id' => 'different_title', 'label' => __( 'Different title:', 'hocwp-theme' ) ) );
		$meta->add_field( array(
			'id'             => 'sidebar',
			'label'          => __( 'Sidebar', 'hocwp-theme' ),
			'field_callback' => 'hocwp_field_select_sidebar'
		) );
		$meta->init();
	}
}

function hocwp_meta_box_google_maps( $args = array() ) {
	global $pagenow;
	if ( 'post-new.php' == $pagenow || 'post.php' == $pagenow ) {
		$post_id    = hocwp_get_value_by_key( $_REQUEST, 'post' );
		$id         = hocwp_get_value_by_key( $args, 'id', 'google_maps_box' );
		$title      = hocwp_get_value_by_key( $args, 'title', __( 'Maps', 'hocwp-theme' ) );
		$post_types = hocwp_get_value_by_key( $args, 'post_types', array( 'post' ) );
		$meta       = new HOCWP_Meta( 'post' );
		$meta->set_title( $title );
		$meta->set_id( $id );
		$meta->set_post_types( $post_types );
		$map_args = array(
			'id'             => 'maps_content',
			'label'          => '',
			'field_callback' => 'hocwp_field_google_maps',
			'names'          => array( 'google_maps' )
		);
		if ( hocwp_id_number_valid( $post_id ) ) {
			$google_maps      = hocwp_get_post_meta( 'google_maps', $post_id );
			$google_maps      = hocwp_json_string_to_array( $google_maps );
			$map_args['lat']  = hocwp_get_value_by_key( $google_maps, 'lat' );
			$map_args['long'] = hocwp_get_value_by_key( $google_maps, 'lng' );
		}
		$meta->add_field( $map_args );
		//$meta->add_field(array('id' => 'google_maps', 'label' => '', 'field_callback' => 'hocwp_field_input_hidden'));
		$meta->init();
	}
}

function hocwp_meta_box_editor( $args = array() ) {
	global $pagenow;
	if ( 'post-new.php' == $pagenow || 'post.php' == $pagenow ) {
		$post_type = hocwp_get_value_by_key( $args, 'post_type' );
		if ( ! is_array( $post_type ) ) {
			$post_type = array( $post_type );
		}
		$box_title    = hocwp_get_value_by_key( $args, 'title', __( 'Additional Information', 'hocwp-theme' ) );
		$current_type = hocwp_get_current_post_type();
		if ( is_array( $current_type ) ) {
			$current_type = current( $current_type );
		}
		$box_id = hocwp_get_value_by_key( $args, 'id' );
		if ( empty( $box_id ) ) {
			$box_id = hocwp_sanitize_id( $box_title );
			if ( empty( $box_id ) ) {
				return;
			}
		}
		if ( ! empty( $current_type ) ) {
			$box_id = $current_type . '_' . $box_id;
		}
		$field_args                   = hocwp_get_value_by_key( $args, 'field_args', array() );
		$field_args                   = hocwp_sanitize_array( $field_args );
		$field_args['field_callback'] = 'hocwp_field_editor';
		$field_args['label']          = '';
		$field_id                     = hocwp_get_value_by_key( $args, 'field_id', hocwp_get_value_by_key( $field_args, 'field_id' ) );
		$field_name                   = hocwp_get_value_by_key( $args, 'field_name', hocwp_get_value_by_key( $field_args, 'field_name' ) );
		hocwp_transmit_id_and_name( $field_id, $field_name );
		if ( empty( $field_id ) ) {
			return;
		}
		$field_args['id']   = $field_id;
		$field_args['name'] = $field_name;
		$meta               = new HOCWP_Meta( 'post' );
		$meta->set_title( $box_title );
		$meta->set_id( $box_id );
		$meta->set_post_types( $post_type );
		$meta->add_field( $field_args );
		$meta->init();
	}
}

function hocwp_meta_box_editor_gallery( $args = array() ) {
	$defaults = array(
		'title'      => __( 'Gallery', 'hocwp-theme' ),
		'field_id'   => 'image_gallery',
		'field_name' => 'gallery',
		'field_args' => array(
			'teeny'   => true,
			'toolbar' => false
		)
	);
	$args     = wp_parse_args( $args, $defaults );
	hocwp_meta_box_editor( $args );
}

function hocwp_meta_box_links_manager() {
	add_meta_box( 'hocwp_link_featured_image', 'Featured Image', 'hocwp_meta_box_link_featured_image', 'link', 'side', 'low' );
}

add_action( 'add_meta_boxes_link', 'hocwp_meta_box_links_manager' );

function hocwp_meta_boxes_init( $post_type, $post ) {
	do_action( 'hocwp_meta_boxes', $post_type, $post );
	do_action( 'hocwp_' . $post_type . '_meta_boxes', $post );
}

add_action( 'add_meta_boxes', 'hocwp_meta_boxes_init', 6, 2 );

function hocwp_update_link_meta( $link_id, $meta_key, $meta_value ) {
	$meta_key = 'hocwp_link_' . $link_id . '_' . $meta_key;
	update_option( $meta_key, $meta_key );
}

function hocwp_get_link_meta( $link_id, $meta_key ) {
	$meta_key = 'hocwp_link_' . $link_id . '_' . $meta_key;

	return get_option( $meta_key );
}

function hocwp_meta_box_link_featured_image( $link ) {
	wp_nonce_field( 'hocwp_link_meta', 'fetured_image_nonce' );
	$args = array(
		'name' => 'thumbnail'
	);
	if ( is_object( $link ) && isset( $link->link_id ) ) {
		$args['value'] = hocwp_get_link_meta( $link->link_id, 'thumbnail' );
	}
	hocwp_field_media_upload_simple( $args );
}

function hocwp_meta_box_save_link_featured_image( $link_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST['fetured_image_nonce'] ) || ! wp_verify_nonce( $_POST['fetured_image_nonce'], 'hocwp_link_meta' ) ) {
		return;
	}
	$thumbnail = hocwp_get_method_value( 'thumbnail' );
	update_option( 'hocwp_link_' . $link_id . '_thumbnail', $thumbnail );
}

add_action( 'edit_link', 'hocwp_meta_box_save_link_featured_image' );
add_action( 'add_link', 'hocwp_meta_box_save_link_featured_image' );

function hocwp_term_meta_edit_field( $args = array() ) {
	$class    = hocwp_get_value_by_key( $args, 'class' );
	$name     = hocwp_get_value_by_key( $args, 'name' );
	$id       = hocwp_get_value_by_key( $args, 'id' );
	$label    = hocwp_get_value_by_key( $args, 'label' );
	$callback = hocwp_get_value_by_key( $args, 'callback' );
	if ( ! hocwp_callback_exists( $callback ) ) {
		$callback = hocwp_get_value_by_key( $args, 'field_callback' );
	}
	$field_args = hocwp_get_value_by_key( $args, 'field_args' );
	$field_args = wp_parse_args( $field_args, $args );
	hocwp_transmit_id_and_name( $id, $name );
	$tmp = hocwp_sanitize_html_class( $name );
	hocwp_add_string_with_space_before( $class, 'form-field term-' . $name . '-wrap hocwp' );
	?>
	<tr class="<?php echo $class; ?>">
		<th scope="row">
			<label for="<?php echo esc_attr( hocwp_sanitize_id( $id ) ); ?>"><?php echo $label; ?></label>
		</th>
		<td>
			<?php
			if ( hocwp_callback_exists( $callback ) ) {
				unset( $field_args['label'] );
				call_user_func( $callback, $field_args );
			} else {
				_e( 'Please set a valid callback for this field', 'hocwp-theme' );
			}
			?>
		</td>
	</tr>
	<?php
}

function hocwp_term_meta_add_field( $args = array() ) {
	$callback = hocwp_get_value_by_key( $args, 'callback' );
	if ( ! hocwp_callback_exists( $callback ) ) {
		$callback = hocwp_get_value_by_key( $args, 'field_callback' );
	}
	$field_args = hocwp_get_value_by_key( $args, 'field_args' );
	$field_args = wp_parse_args( $field_args, $args );
	$class      = hocwp_get_value_by_key( $args, 'class' );
	$name       = hocwp_get_value_by_key( $args, 'name' );
	$id         = hocwp_get_value_by_key( $args, 'id' );
	hocwp_transmit_id_and_name( $id, $name );
	$tmp = hocwp_sanitize_html_class( $name );
	hocwp_add_string_with_space_before( $class, 'form-field term-' . $name . '-wrap hocwp' );
	?>
	<div class="<?php echo $class; ?>">
		<?php
		if ( hocwp_callback_exists( $callback ) ) {
			call_user_func( $callback, $field_args );
		} else {
			_e( 'Please set a valid callback for this field', 'hocwp-theme' );
		}
		?>
	</div>
	<?php
}