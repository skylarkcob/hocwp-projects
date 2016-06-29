<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

class HOCWP_Widget_Term extends WP_Widget {
	public $args = array();
	public $admin_args;
	public $instance;

	private function get_defaults() {
		$defaults = array(
			'taxonomy'         => array( array( 'value' => 'category' ) ),
			'show_count'       => 0,
			'only_thumbnail'   => 0,
			'hide_thumbnail'   => 1,
			'thumbnail_size'   => array( 64, 64 ),
			'number'           => 5,
			'full_width_items' => array(
				'none'       => __( 'None', 'hocwp' ),
				'first'      => __( 'First item', 'hocwp' ),
				'last'       => __( 'Last item', 'hocwp' ),
				'first_last' => __( 'First item and last item', 'hocwp' ),
				'odd'        => __( 'Odd items', 'hocwp' ),
				'even'       => __( 'Even items', 'hocwp' ),
				'all'        => __( 'All items', 'hocwp' )
			),
			'full_width_item'  => 'none',
			'orders'           => array( 'asc', 'desc' ),
			'order'            => 'asc',
			'orderbys'         => array(
				'name'       => __( 'Name', 'hocwp' ),
				'slug'       => __( 'Slug', 'hocwp' ),
				'count'      => __( 'Count', 'hocwp' ),
				'term_group' => __( 'Term group', 'hocwp' ),
				'term_id'    => __( 'Term ID', 'hocwp' ),
				'none'       => __( 'None', 'hocwp' )
			),
			'orderby'          => 'name',
			'count_format'     => '(%TERM_COUNT%)',
			'different_name'   => 0
		);
		$defaults = apply_filters( 'hocwp_widget_term_defaults', $defaults, $this );
		$args     = apply_filters( 'hocwp_widget_term_args', array(), $this );
		$args     = wp_parse_args( $args, $defaults );

		return $args;
	}

	public function __construct() {
		$this->args       = $this->get_defaults();
		$this->admin_args = array(
			'id'          => 'hocwp_widget_term',
			'name'        => 'HocWP Term',
			'class'       => 'hocwp-widget-term',
			'description' => __( 'A list of terms.', 'hocwp' ),
			'width'       => 400
		);
		$this->admin_args = apply_filters( 'hocwp_widget_term_admin_args', $this->admin_args, $this );
		parent::__construct( $this->admin_args['id'], $this->admin_args['name'],
			array(
				'classname'   => $this->admin_args['class'],
				'description' => $this->admin_args['description'],
			),
			array(
				'width' => $this->admin_args['width']
			)
		);
	}

	private function get_taxonomy_from_instance( $instance ) {
		$taxonomy = isset( $instance['taxonomy'] ) ? $instance['taxonomy'] : json_encode( $this->args['taxonomy'] );
		$taxonomy = hocwp_json_string_to_array( $taxonomy );
		if ( ! hocwp_array_has_value( $taxonomy ) ) {
			$taxonomy = array(
				array(
					'value' => apply_filters( 'hocwp_widget_term_default_taxonomy', 'category', $this )
				)
			);
		}

		return $taxonomy;
	}

	public function widget( $args, $instance ) {
		$this->instance = $instance;
		$taxonomy       = $this->get_taxonomy_from_instance( $instance );
		$taxonomies     = array();
		foreach ( $taxonomy as $tax ) {
			$tax = hocwp_get_value_by_key( $tax, 'value' );
			if ( ! empty( $tax ) ) {
				$taxonomies[] = $tax;
			}
		}

		$number          = hocwp_get_value_by_key( $instance, 'number', hocwp_get_value_by_key( $this->args, 'number' ) );
		$thumbnail_size  = hocwp_get_value_by_key( $instance, 'thumbnail_size', hocwp_get_value_by_key( $this->args, 'thumbnail_size' ) );
		$thumbnail_size  = hocwp_sanitize_size( $thumbnail_size );
		$full_width_item = hocwp_get_value_by_key( $instance, 'full_width_item', hocwp_get_value_by_key( $this->args, 'full_width_item' ) );
		$show_count      = hocwp_get_value_by_key( $instance, 'show_count', hocwp_get_value_by_key( $this->args, 'show_count' ) );
		$hide_thumbnail  = hocwp_get_value_by_key( $instance, 'hide_thumbnail', hocwp_get_value_by_key( $this->args, 'hide_thumbnail' ) );
		$only_thumbnail  = hocwp_get_value_by_key( $instance, 'only_thumbnail', hocwp_get_value_by_key( $this->args, 'only_thumbnail' ) );
		$order           = hocwp_get_value_by_key( $instance, 'order', hocwp_get_value_by_key( $this->args, 'order' ) );
		$orderby         = hocwp_get_value_by_key( $instance, 'orderby', hocwp_get_value_by_key( $this->args, 'orderby' ) );
		$count_format    = hocwp_get_value_by_key( $instance, 'count_format', hocwp_get_value_by_key( $this->args, 'count_format' ) );
		$different_name  = (bool) hocwp_get_value_by_key( $instance, 'different_name', hocwp_get_value_by_key( $this->args, 'different_name' ) );

		if ( $hide_thumbnail ) {
			$only_thumbnail = false;
		}

		$tax_args = array(
			'order'   => $order,
			'orderby' => $orderby,
			'number'  => absint( $number )
		);
		$terms    = hocwp_get_terms( $taxonomies, $tax_args );

		hocwp_widget_before( $args, $instance );
		ob_start();
		if ( hocwp_array_has_value( $terms ) ) {
			$count_terms = count( $terms );
			$html        = '<ul class="list-unstyled list-terms">';
			$count       = 0;
			foreach ( $terms as $term ) {
				$item_class = 'term-item';
				hocwp_add_string_with_space_before( $item_class, hocwp_sanitize_html_class( 'tax-' . $term->taxonomy ) );
				if ( ! (bool) $hide_thumbnail ) {
					hocwp_add_string_with_space_before( $item_class, 'show-thumbnail' );
				}
				if ( (bool) $only_thumbnail ) {
					hocwp_add_string_with_space_before( $item_class, 'only-thumbnail' );
				}
				$full_width = hocwp_widget_item_full_width_result( $full_width_item, $count_terms, $count );
				if ( $full_width ) {
					hocwp_add_string_with_space_before( $item_class, 'full-width' );
				}
				if ( (bool) $show_count ) {
					hocwp_add_string_with_space_before( $item_class, 'show-count' );
				} else {
					hocwp_add_string_with_space_before( $item_class, 'no-count' );
				}
				$html .= '<li class="' . $item_class . '">';
				if ( ! (bool) $hide_thumbnail ) {
					$html .= hocwp_term_get_thumbnail_html( array( 'term'      => $term,
					                                               'width'     => $thumbnail_size[0],
						$thumbnail_size[1],
						                                           'bfi_thumb' => false
					) );
				}
				if ( ! (bool) $only_thumbnail ) {
					$term_name = $term->name;
					if ( $different_name ) {
						$term_name = hocwp_term_get_name( $term );
					}
					$html .= '<a class="term-name" href="' . get_term_link( $term ) . '">' . $term_name . '</a>';
					if ( (bool) $show_count && ! empty( $count_format ) ) {
						$html .= ' <span class="count">' . str_replace( '%TERM_COUNT%', $term->count, $count_format ) . '</span>';
					}
				}
				$html .= '</li>';
				$count ++;
			}
			$html .= '</ul>';
			echo $html;
		} else {
			_e( 'Sorry, nothing found.', 'hocwp' );
		}
		$widget_html = ob_get_clean();
		$widget_html = apply_filters( 'hocwp_widget_term_html', $widget_html, $args, $instance, $this );
		echo $widget_html;
		hocwp_widget_after( $args, $instance );
	}

	public function form( $instance ) {
		$this->instance  = $instance;
		$title           = hocwp_get_value_by_key( $instance, 'title' );
		$taxonomy        = $this->get_taxonomy_from_instance( $instance );
		$number          = hocwp_get_value_by_key( $instance, 'number', hocwp_get_value_by_key( $this->args, 'number' ) );
		$thumbnail_size  = hocwp_get_value_by_key( $instance, 'thumbnail_size', hocwp_get_value_by_key( $this->args, 'thumbnail_size' ) );
		$full_width_item = hocwp_get_value_by_key( $instance, 'full_width_item', hocwp_get_value_by_key( $this->args, 'full_width_item' ) );
		$count_format    = hocwp_get_value_by_key( $instance, 'count_format', hocwp_get_value_by_key( $this->args, 'count_format' ) );
		$show_count      = hocwp_get_value_by_key( $instance, 'show_count', hocwp_get_value_by_key( $this->args, 'show_count' ) );
		$hide_thumbnail  = hocwp_get_value_by_key( $instance, 'hide_thumbnail', hocwp_get_value_by_key( $this->args, 'hide_thumbnail' ) );
		$only_thumbnail  = hocwp_get_value_by_key( $instance, 'only_thumbnail', hocwp_get_value_by_key( $this->args, 'only_thumbnail' ) );
		$order           = hocwp_get_value_by_key( $instance, 'order', hocwp_get_value_by_key( $this->args, 'order' ) );
		$orderby         = hocwp_get_value_by_key( $instance, 'orderby', hocwp_get_value_by_key( $this->args, 'orderby' ) );
		$different_name  = (bool) hocwp_get_value_by_key( $instance, 'different_name', hocwp_get_value_by_key( $this->args, 'different_name' ) );

		hocwp_field_widget_before( $this->admin_args['class'] );

		hocwp_widget_field_title( $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), $title );

		$lists = get_taxonomies( array( '_builtin' => false, 'public' => true ), 'objects' );
		if ( ! array_key_exists( 'post_tag', $lists ) ) {
			array_unshift( $lists, get_taxonomy( 'post_tag' ) );
		}
		if ( ! array_key_exists( 'category', $lists ) ) {
			array_unshift( $lists, get_taxonomy( 'category' ) );
		}
		$all_option = '';

		foreach ( $lists as $lvalue ) {
			$selected = '';
			if ( ! hocwp_array_has_value( $taxonomy ) ) {
				$taxonomy[] = array( 'value' => 'category' );
			}
			foreach ( $taxonomy as $ptvalue ) {
				$ptype = isset( $ptvalue['value'] ) ? $ptvalue['value'] : '';
				if ( $lvalue->name == $ptype ) {
					$selected = $lvalue->name;
					break;
				}
			}
			$all_option .= hocwp_field_get_option( array( 'value'    => $lvalue->name,
			                                              'text'     => $lvalue->labels->singular_name,
			                                              'selected' => $selected
			) );
		}

		$args = array(
			'id'          => $this->get_field_id( 'taxonomy' ),
			'name'        => $this->get_field_name( 'taxonomy' ),
			'all_option'  => $all_option,
			'value'       => $taxonomy,
			'label'       => __( 'Taxonomy:', 'hocwp' ),
			'placeholder' => __( 'Choose taxonomy', 'hocwp' ),
			'multiple'    => true
		);
		hocwp_widget_field( 'hocwp_field_select_chosen', $args );

		$args = array(
			'id'    => $this->get_field_id( 'number' ),
			'name'  => $this->get_field_name( 'number' ),
			'value' => $number,
			'label' => __( 'Number items:', 'hocwp' )
		);
		hocwp_widget_field( 'hocwp_field_input_number', $args );

		$lists      = $this->args['orderbys'];
		$all_option = '';
		foreach ( $lists as $lkey => $lvalue ) {
			$all_option .= hocwp_field_get_option( array( 'value'    => $lkey,
			                                              'text'     => $lvalue,
			                                              'selected' => $orderby
			) );
		}
		$args = array(
			'id'         => $this->get_field_id( 'orderby' ),
			'name'       => $this->get_field_name( 'orderby' ),
			'value'      => $orderby,
			'all_option' => $all_option,
			'label'      => __( 'Order by:', 'hocwp' ),
			'class'      => 'orderby'
		);
		hocwp_widget_field( 'hocwp_field_select', $args );

		$lists      = $this->args['orders'];
		$all_option = '';
		foreach ( $lists as $lkey => $lvalue ) {
			$all_option .= hocwp_field_get_option( array( 'value'    => strtolower( $lvalue ),
			                                              'text'     => strtoupper( $lvalue ),
			                                              'selected' => $order
			) );
		}
		$args = array(
			'id'         => $this->get_field_id( 'order' ),
			'name'       => $this->get_field_name( 'order' ),
			'value'      => $order,
			'all_option' => $all_option,
			'label'      => __( 'Order:', 'hocwp' ),
			'class'      => 'order'
		);
		hocwp_widget_field( 'hocwp_field_select', $args );

		$args = array(
			'id_width'    => $this->get_field_id( 'thumbnail_size_width' ),
			'name_width'  => $this->get_field_name( 'thumbnail_size_width' ),
			'id_height'   => $this->get_field_id( 'thumbnail_size_height' ),
			'name_height' => $this->get_field_name( 'thumbnail_size_height' ),
			'value'       => $thumbnail_size,
			'label'       => __( 'Thumbnail size:', 'hocwp' )
		);
		hocwp_widget_field( 'hocwp_field_size', $args );

		$lists      = $this->args['full_width_items'];
		$all_option = '';
		foreach ( $lists as $lkey => $lvalue ) {
			$all_option .= hocwp_field_get_option( array( 'value'    => $lkey,
			                                              'text'     => $lvalue,
			                                              'selected' => $full_width_item
			) );
		}
		$args = array(
			'id'         => $this->get_field_id( 'full_width_item' ),
			'name'       => $this->get_field_name( 'full_width_item' ),
			'value'      => $full_width_item,
			'all_option' => $all_option,
			'label'      => __( 'Full width items:', 'hocwp' ),
			'class'      => 'full-width-item'
		);
		hocwp_widget_field( 'hocwp_field_select', $args );

		$args = array(
			'id'    => $this->get_field_id( 'count_format' ),
			'name'  => $this->get_field_name( 'count_format' ),
			'value' => $count_format,
			'label' => __( 'Count format:', 'hocwp' )
		);
		hocwp_widget_field( 'hocwp_field_input', $args );

		$args = array(
			'id'    => $this->get_field_id( 'hide_thumbnail' ),
			'name'  => $this->get_field_name( 'hide_thumbnail' ),
			'value' => $hide_thumbnail,
			'label' => __( 'Hide term thumbnail?', 'hocwp' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'show_count' ),
			'name'  => $this->get_field_name( 'show_count' ),
			'value' => $show_count,
			'label' => __( 'Show count?', 'hocwp' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'only_thumbnail' ),
			'name'  => $this->get_field_name( 'only_thumbnail' ),
			'value' => $only_thumbnail,
			'label' => __( 'Only thumbnail?', 'hocwp' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'different_name' ),
			'name'  => $this->get_field_name( 'different_name' ),
			'value' => $different_name,
			'label' => __( 'Use different term name?', 'hocwp' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		hocwp_field_widget_after();
	}

	public function update( $new_instance, $old_instance ) {
		$instance                    = $old_instance;
		$instance['title']           = strip_tags( hocwp_get_value_by_key( $new_instance, 'title' ) );
		$instance['taxonomy']        = hocwp_get_value_by_key( $new_instance, 'taxonomy', json_encode( $this->args['taxonomy'] ) );
		$instance['number']          = hocwp_get_value_by_key( $new_instance, 'number', $this->args['number'] );
		$instance['order']           = hocwp_get_value_by_key( $new_instance, 'order', $this->args['order'] );
		$instance['orderby']         = hocwp_get_value_by_key( $new_instance, 'orderby', $this->args['orderby'] );
		$instance['full_width_item'] = hocwp_get_value_by_key( $new_instance, 'full_width_item', $this->args['full_width_item'] );
		$instance['count_format']    = hocwp_get_value_by_key( $new_instance, 'count_format', $this->args['count_format'] );
		$width                       = hocwp_get_value_by_key( $new_instance, 'thumbnail_size_width', $this->args['thumbnail_size'][0] );
		$height                      = hocwp_get_value_by_key( $new_instance, 'thumbnail_size_height', $this->args['thumbnail_size'][1] );
		$instance['thumbnail_size']  = array( $width, $height );
		$instance['hide_thumbnail']  = hocwp_checkbox_post_data_value( $new_instance, 'hide_thumbnail' );
		$instance['show_count']      = hocwp_checkbox_post_data_value( $new_instance, 'show_count' );
		$instance['only_thumbnail']  = hocwp_checkbox_post_data_value( $new_instance, 'only_thumbnail' );
		$instance['different_name']  = hocwp_checkbox_post_data_value( $new_instance, 'different_name' );

		return $instance;
	}
}