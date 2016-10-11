<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

global $pagenow, $post;

function hocwp_theme_meta_box_sidebar_information( $post_type, $post ) {
	if ( ! hocwp_is_post( $post ) ) {
		return;
	}
	global $pagenow;
	if ( 'hocwp_sidebar' == $post_type ) {
		$readonly = false;
		$post_id  = 0;
		if ( 'post.php' == $pagenow ) {
			$post_id = hocwp_get_value_by_key( $_REQUEST, 'post' );
			$default = (bool) get_post_meta( $post_id, 'sidebar_default' );
			if ( $default ) {
				$readonly = true;
			}
		}
		$post_id = $post->ID;
		$meta    = new HOCWP_Meta( 'post' );
		$meta->set_id( 'hocwp_sidebar_information' );
		$meta->set_title( __( 'Sidebar Information', 'hocwp-theme' ) );
		$meta->add_post_type( 'hocwp_sidebar' );

		$field_args = array(
			'id'    => 'sidebar_id',
			'label' => __( 'Sidebar ID:', 'hocwp-theme' )
		);
		if ( $readonly ) {
			$field_args['readonly'] = true;
		}
		$meta->add_field( $field_args );
		$field_args = array(
			'id'    => 'sidebar_name',
			'label' => __( 'Sidebar name:', 'hocwp-theme' )
		);
		if ( $readonly ) {
			$field_args['readonly'] = true;
		}
		$meta->add_field( $field_args );
		$field_args = array(
			'id'    => 'sidebar_description',
			'label' => __( 'Sidebar description:', 'hocwp-theme' )
		);
		if ( $readonly ) {
			$field_args['readonly'] = true;
		}
		$meta->add_field( $field_args );
		$field_args = array(
			'id'    => 'sidebar_tag',
			'label' => __( 'Sidebar tag:', 'hocwp-theme' )
		);
		if ( $readonly ) {
			$field_args['readonly'] = true;
		}
		$meta->add_field( $field_args );
		$meta->init();
	}
}

function hocwp_theme_meta_box_subscriber_information( $post_type, $post ) {
	if ( ! hocwp_is_post( $post ) ) {
		return;
	}
	$post_id = $post->ID;
	if ( 'hocwp_subscriber' == $post_type ) {
		$meta = new HOCWP_Meta( 'post' );
		$meta->set_id( 'hocwp_subscriber_information' );
		$meta->set_title( __( 'Subscriber Information', 'hocwp-theme' ) );
		$meta->add_post_type( 'hocwp_subscriber' );
		$field_args = array(
			'id'       => 'subscriber_email',
			'label'    => __( 'Email:', 'hocwp-theme' ),
			'readonly' => true
		);
		$meta->add_field( $field_args );
		$field_args = array(
			'id'    => 'subscriber_name',
			'label' => __( 'Name:', 'hocwp-theme' )
		);
		$meta->add_field( $field_args );
		$field_args = array(
			'id'    => 'subscriber_phone',
			'label' => __( 'Phone:', 'hocwp-theme' )
		);
		$meta->add_field( $field_args );
		if ( hocwp_id_number_valid( $post_id ) ) {
			$field_args = array(
				'id'       => 'subscriber_user',
				'label'    => __( 'User ID:', 'hocwp-theme' ),
				'readonly' => true
			);
			$meta->add_field( $field_args );
		}
		$meta->init();
	}
}

function hocwp_theme_meta_box_ads_information( $post_type, $post ) {
	global $pagenow;
	if ( 'hocwp_ads' == $post_type ) {
		if ( hocwp_is_post( $post ) ) {
			$post_id = $post->ID;
		} else {
			$post_id = 0;
		}
		$meta = new HOCWP_Meta( 'post' );
		$meta->add_post_type( 'hocwp_ads' );
		$meta->set_id( 'hocwp_ads_information' );
		$meta->set_title( __( 'Ads Information', 'hocwp-theme' ) );

		$meta->add_field(
			array(
				'id'             => 'image',
				'label'          => __( 'Image:', 'hocwp-theme' ),
				'container'      => true,
				'field_callback' => 'hocwp_field_media_upload'
			)
		);

		$meta->add_field(
			array(
				'id'    => 'url',
				'label' => __( 'Url:', 'hocwp-theme' )
			)
		);

		$positions = hocwp_get_ads_positions();

		if ( hocwp_array_has_value( $positions ) ) {
			$all_option = hocwp_field_get_option( array( 'text' => '--Choose Position--' ) );
			$selected   = get_post_meta( $post_id, 'position', true );
			foreach ( $positions as $position ) {
				$all_option .= hocwp_field_get_option(
					array(
						'text'     => $position['name'],
						'value'    => $position['id'],
						'selected' => $selected
					)
				);
			}
			$field_args = array(
				'id'             => 'position',
				'label'          => __( 'Position:', 'hocwp-theme' ),
				'field_callback' => 'hocwp_field_select',
				'all_option'     => $all_option
			);

			$meta->add_field( $field_args );
		}

		$meta->add_field(
			array(
				'id'             => 'expire',
				'label'          => __( 'Expire:', 'hocwp-theme' ),
				'field_callback' => 'hocwp_field_datetime_picker'
			)
		);

		do_action( 'hocwp_meta_box_ads_fields', $meta, $post );

		$meta->init();

		$args = array(
			'id'         => 'code_box',
			'title'      => __( 'Code:', 'hocwp-theme' ),
			'field_id'   => 'code',
			'post_type'  => $post_type,
			'field_args' => array(
				'teeny' => true
			)
		);
		hocwp_meta_box_editor( $args );
	}
}

if ( ! hocwp_is_post( $post ) ) {
	if ( 'post.php' == $pagenow ) {
		$post_id = hocwp_get_method_value( 'post', 'get' );
		$post    = get_post( $post_id );
	}
}
$post_type = hocwp_get_current_post_type();

if ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) {
	if ( ! hocwp_is_post( $post ) ) {
		$current_post = hocwp_get_current_new_post();
		if ( hocwp_is_post( $current_post ) ) {
			$post = $current_post;
		}
	}
	hocwp_meta_box_page_additional_information();
	hocwp_meta_box_side_image(
		array(
			'post_type' => 'all',
			'id'        => 'hocwp_large_thumbnail_box',
			'title'     => __( 'Large Thumbnail', 'hocwp-theme' ),
			'field_id'  => 'large_thumbnail'
		)
	);
	hocwp_theme_meta_box_sidebar_information( $post_type, $post );
	hocwp_theme_meta_box_subscriber_information( $post_type, $post );
	do_action( 'hocwp_post_meta_boxes', $post_type, $post );
}

function hocwp_theme_meta_boxes_init( $post_type, $post ) {
	hocwp_slider_meta_box_field( $post_type, $post );
	hocwp_theme_meta_box_ads_information( $post_type, $post );
	do_action( 'hocwp_theme_meta_boxes_init', $post_type, $post );
}

add_action( 'add_meta_boxes', 'hocwp_theme_meta_boxes_init', 6, 2 );

function hocwp_setup_theme_save_post_meta_hook( $post_id ) {
	if ( ! hocwp_can_save_post( $post_id ) ) {
		return $post_id;
	}
	$post      = get_post( $post_id );
	$post_type = $post->post_type;
	global $hocwp_metas;
	if ( hocwp_array_has_value( $hocwp_metas ) ) {
		foreach ( $hocwp_metas as $meta ) {
			if ( 'post' == $meta->get_type() ) {
				$post_types = $meta->get_post_types();
				$fields     = $meta->get_fields();
				foreach ( $fields as $field ) {
					$meta->save_post_meta_helper( $post_id, $field );
				}
				$fields = $meta->get_custom_fields();
				if ( is_array( $fields ) ) {
					foreach ( $fields as $field ) {
						$meta->save_post_meta_helper( $post_id, $field );
					}
				}
			}
		}
	}

	$value = isset( $_POST['featured'] ) ? 1 : 0;
	update_post_meta( $post_id, 'featured', $value );

	$value = isset( $_POST['active'] ) ? 1 : 0;
	update_post_meta( $post_id, 'active', $value );
	if ( 'hocwp_subscriber' == $post->post_type ) {
		update_post_meta( $post_id, 'subscriber_verified', $value );
	}
	switch ( $post_type ) {
		case 'hocwp_sidebar':
			if ( isset( $_POST['sidebar_id'] ) ) {
				update_post_meta( $post_id, 'sidebar_id', hocwp_sanitize_id( $_POST['sidebar_id'] ) );
			}
			if ( isset( $_POST['sidebar_name'] ) ) {
				update_post_meta( $post_id, 'sidebar_name', sanitize_text_field( $_POST['sidebar_name'] ) );
			}
			if ( isset( $_POST['sidebar_description'] ) ) {
				update_post_meta( $post_id, 'sidebar_description', sanitize_text_field( $_POST['sidebar_description'] ) );
			}
			if ( isset( $_POST['sidebar_tag'] ) ) {
				update_post_meta( $post_id, 'sidebar_tag', sanitize_text_field( $_POST['sidebar_tag'] ) );
			}
			break;
		case 'hocwp_slider':
			if ( isset( $_POST['slider_items'] ) ) {
				update_post_meta( $post_id, 'slider_items', $_POST['slider_items'] );
			}
			if ( isset( $_POST['position'] ) ) {
				update_post_meta( $post_id, 'position', $_POST['position'] );
			}
			break;
		case 'hocwp_ads':
			if ( isset( $_POST['position'] ) ) {
				update_post_meta( $post_id, 'position', $_POST['position'] );
			}
			if ( isset( $_POST['expire'] ) ) {
				update_post_meta( $post_id, 'expire', strtotime( $_POST['expire'] ) );
			}
			if ( isset( $_POST['code'] ) ) {
				update_post_meta( $post_id, 'code', $_POST['code'] );
			}
			$image = hocwp_get_method_value( 'image' );
			update_post_meta( $post_id, 'image', $image );
			$url = hocwp_get_method_value( 'url' );
			update_post_meta( $post_id, 'url', $url );
			break;
		case 'hocwp_subscriber':
			if ( isset( $_POST['subscriber_name'] ) ) {
				update_post_meta( $post_id, 'subscriber_name', sanitize_text_field( $_POST['subscriber_name'] ) );
			}
			if ( isset( $_POST['subscriber_phone'] ) ) {
				update_post_meta( $post_id, 'subscriber_phone', sanitize_text_field( $_POST['subscriber_phone'] ) );
			}
			break;
		case 'coupon':
			if ( isset( $_POST['expired_date'] ) ) {
				update_post_meta( $post_id, 'expired_date', strtotime( $_POST['expired_date'] ) );
			}
			break;
		case 'product':
			$args  = array(
				'posts_per_page' => - 1,
				'post_type'      => 'hocwp_product_tab'
			);
			$query = hocwp_query( $args );
			if ( $query->have_posts() ) {
				foreach ( $query->posts as $product ) {
					$field_name = hocwp_sanitize_id( $product->post_name );
					if ( isset( $_POST[ $field_name ] ) ) {
						update_post_meta( $post_id, $field_name, $_POST[ $field_name ] );
					}
				}
			}
			break;
	}

	return $post_id;
}

add_action( 'save_post', 'hocwp_setup_theme_save_post_meta_hook', 99 );

function hocwp_theme_post_submitbox_misc_actions() {
	global $post;
	if ( ! hocwp_object_valid( $post ) ) {
		return;
	}
	$post_type   = $post->post_type;
	$type_object = get_post_type_object( $post_type );
	if ( (bool) $type_object->public ) {
		$post_types = hocwp_post_type_no_featured_field();
		if ( ! in_array( $post_type, $post_types ) ) {
			$key   = 'featured';
			$value = get_post_meta( $post->ID, $key, true );
			$args  = array(
				'id'    => 'hocwp_featured_post',
				'name'  => $key,
				'value' => $value,
				'label' => __( 'Featured?', 'hocwp-theme' )
			);
			hocwp_field_publish_box( 'hocwp_field_input_checkbox', $args );
		}
	}
	do_action( 'hocwp_' . $post_type . '_publish_box_field', $post );
	do_action( 'hocwp_publish_box_field', $post_type, $post );
}

add_action( 'post_submitbox_misc_actions', 'hocwp_theme_post_submitbox_misc_actions' );

function hocwp_theme_meta_product_tab_field_box( $post_type, $post ) {
	if ( hocwp_wc_installed() ) {
		$args  = array(
			'posts_per_page' => - 1,
			'post_type'      => 'hocwp_product_tab'
		);
		$query = hocwp_query( $args );
		if ( $query->have_posts() ) {
			hocwp_theme_meta_product_tab_field_box_helper( $query->posts );
		}
	}
}

add_action( 'hocwp_theme_meta_boxes_init', 'hocwp_theme_meta_product_tab_field_box', 10, 2 );

function hocwp_theme_meta_product_tab_field_box_helper( $posts ) {
	foreach ( $posts as $tab ) {
		$id   = hocwp_sanitize_id( $tab->post_name );
		$args = array(
			'post_type' => 'product',
			'title'     => $tab->post_title,
			'field_id'  => $id,
			'id'        => 'meta_box_' . $id
		);
		hocwp_meta_box_editor( $args );
	}
}