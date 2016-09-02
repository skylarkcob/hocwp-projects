<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

if ( ! post_type_exists( 'hocwp_slider' ) ) {
	//return;
}

function hocwp_get_slider_positions() {
	global $hocwp_slider_positions;
	if ( ! is_array( $hocwp_slider_positions ) ) {
		$hocwp_slider_positions = array();
	}

	return $hocwp_slider_positions;
}

function hocwp_register_slider_position( $id, $name, $description = '' ) {
	global $hocwp_slider_positions;
	if ( ! is_array( $hocwp_slider_positions ) ) {
		$hocwp_slider_positions = array();
	}
	$id = hocwp_sanitize_id( $id );
	if ( array_key_exists( $id, $hocwp_slider_positions ) ) {
		return;
	}
	$new_slider_item               = array(
		'id'          => $id,
		'name'        => $name,
		'description' => $description
	);
	$hocwp_slider_positions[ $id ] = $new_slider_item;
}

function hocwp_sanitize_slider_item( $items ) {
	$list_items = isset( $items['items'] ) ? $items['items'] : array();
	$item_order = isset( $items['order'] ) ? $items['order'] : '';
	if ( is_string( $item_order ) ) {
		$item_order = explode( ',', $item_order );
	}
	if ( ! is_array( $item_order ) ) {
		$item_order = array();
	}
	$item_order     = array_unique( $item_order );
	$items['items'] = $list_items;
	$items['order'] = $item_order;

	return $items;
}

function hocwp_get_slider_items( $post_id = null, $sanitize = true ) {
	$post_id = hocwp_return_post( $post_id, 'id' );
	$items   = hocwp_get_post_meta( 'slider_items', $post_id );
	$items   = hocwp_sanitize_slider_item( $items );

	return $items;
}

function hocwp_slider_meta_box_field( $post_type, $post ) {
	if ( 'hocwp_slider' == $post_type ) {
		$post_id = $post->ID;
		$meta    = new HOCWP_Meta( 'post' );
		$meta->add_post_type( 'hocwp_slider' );
		$meta->set_title( __( 'Slider Information', 'hocwp-theme' ) );
		$meta->is_on_sidebar( true );

		$all_options  = '<option value="">-- ' . __( 'Choose position', 'hocwp-theme' ) . ' --</option>';
		$slider_items = hocwp_get_slider_positions();
		$position     = hocwp_get_post_meta( 'position', $post->ID );

		foreach ( $slider_items as $slider ) {
			$args = array(
				'value'    => $slider['id'],
				'text'     => $slider['name'],
				'selected' => $position
			);
			$all_options .= hocwp_field_get_option( $args );
		}
		$args = array(
			'id'              => 'position',
			'label'           => __( 'Position:', 'hocwp-theme' ),
			'description'     => __( 'Choose position where you want the slider to be displayed.', 'hocwp-theme' ),
			'field_class'     => 'display-block',
			'all_option'      => $all_options,
			'container_class' => 'margin-bottom-10',
			'field_callback'  => 'hocwp_field_select'
		);
		$meta->add_field( $args );

		$meta->add_field(
			array(
				'id'             => 'active',
				'label'          => __( 'Make slider active?', 'hocwp-theme' ),
				'field_callback' => 'hocwp_field_checkbox'
			)
		);
		$meta->init();

		$meta = new HOCWP_Meta( 'post' );
		$meta->set_id( 'hocwp_slider_item_information' );
		$meta->set_title( __( 'Slider Items', 'hocwp-theme' ) );
		$meta->add_post_type( 'hocwp_slider' );
		$meta->set_use_media_upload( true );
		$meta->register_field( 'slider_items' );

		$value      = hocwp_get_slider_items( $post_id, true );
		$list_items = $value['items'];
		$item_order = $value['order'];
		if ( ! is_array( $item_order ) ) {
			$item_order = array();
		}
		$max_item_id = absint( hocwp_get_max_number( $item_order ) );
		$field_html  = '';
		foreach ( $item_order as $key => $item_id ) {
			$item = isset( $list_items[ $item_id ] ) ? $list_items[ $item_id ] : array();
			if ( ! hocwp_array_has_value( $item ) ) {
				unset( $item_order[ $key ] );
				continue;
			}
			$title       = isset( $item['title'] ) ? $item['title'] : '';
			$link        = isset( $item['link'] ) ? $item['link'] : '';
			$description = isset( $item['description'] ) ? $item['description'] : '';
			$image_url   = isset( $item['image_url'] ) ? $item['image_url'] : '';
			$image_id    = isset( $item['image_id'] ) ? $item['image_id'] : 0;
			if ( $image_id > 0 ) {
				$media_url = hocwp_get_media_image_url( $image_id );
				if ( ! empty( $media_url ) ) {
					$image_url = $media_url;
				}
			}
			if ( empty( $image_url ) ) {
				continue;
			}
			ob_start();
			?>
			<li data-item="<?php echo $item_id; ?>">
				<img class="item-image" src="<?php echo $image_url; ?>">

				<div class="item-info">
					<input type="text" name="slider_items[items][<?php echo $item_id; ?>][title]" class="item-title"
					       value="<?php echo $title; ?>" placeholder="<?php _e( 'Title', 'hocwp-theme' ); ?>">
					<input type="url" name="slider_items[items][<?php echo $item_id; ?>][link]" class="item-link"
					       value="<?php echo $link; ?>"
					       placeholder="<?php _e( 'Link for this item', 'hocwp-theme' ); ?>">
						<textarea name="slider_items[items][<?php echo $item_id; ?>][description]"
						          class="item-description"><?php echo $description; ?></textarea>
				</div>
				<input type="hidden" class="item-image-url" value="<?php echo $image_url; ?>"
				       name="slider_items[items][<?php echo $item_id; ?>][image_url]">
				<input type="hidden" class="item-image-id" value="<?php echo $image_id; ?>"
				       name="slider_items[items][<?php echo $item_id; ?>][image_id]">
				<span class="item-icon icon-delete icon-sortable-ui"></span>
				<span class="item-icon icon-drag icon-sortable-ui"></span>
			</li>
			<?php
			$field_html .= ob_get_clean();
		}

		$meta->add_field(
			array(
				'id'             => 'list_slider_items',
				'class'          => 'list-slider-items ui-sortable sortable hocwp-sortable',
				'tag'            => 'ul',
				'field_callback' => 'hocwp_field_html_tag',
				'attributes'     => array(
					'data-max-id'            => $max_item_id,
					'data-items'             => count( $item_order ),
					'data-post'              => $post->ID,
					'data-disable-selection' => 0
				),
				'html'           => $field_html,
				'after_html'     => '<input type="hidden" name="slider_items[order]" value="' . implode( ',', $item_order ) . '" class="item-order" autocomplete="off">'
			)
		);

		$meta->add_field(
			array(
				'id'             => 'add_slider',
				'label'          => __( 'Add item', 'hocwp-theme' ),
				'field_callback' => 'hocwp_field_button'
			)
		);

		$meta->init();
	}
}

function hocwp_slider_on_save_slider_info( $post_id ) {
	if ( ! hocwp_can_save_post( $post_id ) ) {
		return;
	}
	$slider = get_post( $post_id );
	if ( 'hocwp_slider' == $slider->post_type ) {
		$items = hocwp_get_method_value( 'slider_items' );
		$saved = hocwp_get_post_meta( 'slider_items', $post_id );
		if ( ! is_array( $items ) ) {
			$items = array();
		}
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		$items = wp_parse_args( $items, $saved );
		update_post_meta( $post_id, 'slider_items', $items );
	}
}

add_action( 'save_post', 'hocwp_slider_on_save_slider_info' );

function hocwp_get_slider_item_html( $args = array() ) {
	$max_item_id = hocwp_get_value_by_key( $args, 'max_item_id' );
	$max_item_id = absint( $max_item_id );
	$media_url   = hocwp_get_value_by_key( $args, 'media_url' );
	$media_url   = esc_url( $media_url );
	$media_id    = hocwp_get_value_by_key( $args, 'media_id' );
	$media_id    = absint( $media_id );
	$item_html   = '<li data-item="' . $max_item_id . '">';
	$item_html .= '<img class="item-image" src="' . $media_url . '">';
	$item_html .= '<div class="item-info">';
	$item_html .= '<input type="text" placeholder="' . __( 'Title', 'hocwp-theme' ) . '" value="" class="item-title" name="slider_items[items][' . $max_item_id . '][title]">';
	$item_html .= '<input type="url" placeholder="' . __( 'Link for this item', 'hocwp-theme' ) . '" value="" class="item-link" name="slider_items[items][' . $max_item_id . '][link]">';
	$item_html .= '<textarea class="item-description" name="slider_items[items][' . $max_item_id . '][description]"></textarea>';
	$item_html .= '</div>';
	$item_html .= '<input type="hidden" class="item-image-url" name="slider_items[items][' . $max_item_id . '][image_url]" value="' . $media_url . '">';
	$item_html .= '<input type="hidden" class="item-image-id" name="slider_items[items][' . $max_item_id . '][image_id]" value="' . $media_id . '">';
	$item_html .= '<span class="item-icon icon-delete icon-sortable-ui"></span>';
	$item_html .= '<span class="item-icon icon-drag icon-sortable-ui"></span>';
	$item_html .= '</li>';

	return $item_html;
}

function hocwp_get_slider_by_position( $position ) {
	$args   = array(
		'post_type' => 'hocwp_slider'
	);
	$query  = hocwp_query_post_by_meta( 'position', $position, $args );
	$slider = array_shift( $query->posts );

	return $slider;
}

function hocwp_slider_html( $args = array() ) {
	if ( ! is_array( $args ) ) {
		$position = $args;
	} else {
		$position = hocwp_get_value_by_key( $args, 'position' );
	}
	$slider = hocwp_get_slider_by_position( $position );
	if ( hocwp_is_post( $slider ) ) {
		$items = hocwp_get_post_meta( 'slider_items', $slider->ID );
		$order = hocwp_get_value_by_key( $items, 'order' );
		if ( ! empty( $order ) ) {
			$order        = explode( ',', $order );
			$items        = hocwp_get_value_by_key( $items, 'items' );
			$slider_class = 'hocwp-slider';
			hocwp_add_string_with_space_before( $slider_class, hocwp_sanitize_html_class( $position ) );
			echo '<div class="' . $slider_class . '">';
			echo '<ul class="list-unstyled list-items slickslide list-inline">';
			$list_paging = '';
			foreach ( $order as $item_id ) {
				$item = hocwp_get_value_by_key( $items, $item_id );
				if ( hocwp_array_has_value( $item ) ) {
					$title       = hocwp_get_value_by_key( $item, 'title' );
					$link        = hocwp_get_value_by_key( $item, 'link' );
					$description = hocwp_get_value_by_key( $item, 'description' );
					$image_url   = hocwp_get_value_by_key( $item, 'image_url' );
					$image_id    = hocwp_get_value_by_key( $item, 'image_id' );
					$image_url   = hocwp_return_media_url( $image_url, $image_id );
					$img         = new HOCWP_HTML( 'img' );
					$img->set_image_src( $image_url );
					$li = new HOCWP_HTML( 'li' );
					$li->set_text( $img );
					$list_paging .= $li->build();
					if ( ! empty( $link ) ) {
						$a = new HOCWP_HTML( 'a' );
						$a->set_href( $link );
						$a->set_text( $img );
						$li->set_text( $a );
					} else {
						$li->set_text( $img );
					}
					$li->output();
				}
			}
			echo '</ul>';
			echo '<div class="thumbs-paging slick-thumbs">';
			echo '<ul class="list-unstyled list-paging">';
			echo $list_paging;
			echo '</ul>';
			echo '</div>';
			echo '</div>';
		}
	}
}