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
				'none'       => __( 'None', 'hocwp-theme' ),
				'first'      => __( 'First item', 'hocwp-theme' ),
				'last'       => __( 'Last item', 'hocwp-theme' ),
				'first_last' => __( 'First item and last item', 'hocwp-theme' ),
				'odd'        => __( 'Odd items', 'hocwp-theme' ),
				'even'       => __( 'Even items', 'hocwp-theme' ),
				'all'        => __( 'All items', 'hocwp-theme' )
			),
			'full_width_item'  => 'none',
			'orders'           => array( 'asc', 'desc' ),
			'order'            => 'asc',
			'orderbys'         => array(
				'name'       => __( 'Name', 'hocwp-theme' ),
				'slug'       => __( 'Slug', 'hocwp-theme' ),
				'count'      => __( 'Count', 'hocwp-theme' ),
				'term_group' => __( 'Term group', 'hocwp-theme' ),
				'term_id'    => __( 'Term ID', 'hocwp-theme' ),
				'none'       => __( 'None', 'hocwp-theme' )
			),
			'orderby'          => 'name',
			'count_format'     => '(%TERM_COUNT%)',
			'different_name'   => 0,
			'in_current_post'  => 0,
			'hide_empty'       => 0,
			'only_parent'      => 0,
			'child_of_current' => 0,
			'child_of_parent'  => 0,
			'parent_as_title'  => 0
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
			'description' => __( 'A list of terms.', 'hocwp-theme' ),
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
		$before_widget  = hocwp_get_value_by_key( $args, 'before_widget' );
		$taxonomy       = $this->get_taxonomy_from_instance( $instance );
		$taxonomies     = array();
		$widget_class   = '';
		foreach ( $taxonomy as $tax ) {
			$tax = hocwp_get_value_by_key( $tax, 'value' );
			if ( ! empty( $tax ) ) {
				$taxonomies[] = $tax;
				hocwp_add_string_with_space_before( $widget_class, hocwp_sanitize_html_class( $tax ) );
			}
		}
		$before_widget         = hocwp_add_class_to_string( '', $before_widget, $widget_class );
		$args['before_widget'] = $before_widget;
		$in_current_post       = (bool) hocwp_get_value_by_key( $instance, 'in_current_post', hocwp_get_value_by_key( $this->args, 'in_current_post' ) );
		if ( $in_current_post && ( ! is_singular() || is_page() ) ) {
			return;
		}
		if ( $in_current_post ) {
			$post_id      = get_the_ID();
			$current_post = get_post( $post_id );
			$obj_taxs     = get_object_taxonomies( $current_post->post_type );
			$has_tax      = false;
			foreach ( $taxonomies as $tax ) {
				foreach ( $obj_taxs as $tax_name ) {
					if ( $tax == $tax_name ) {
						$has_tax = true;
						break;
					}
				}
			}
			if ( ! $has_tax ) {
				return;
			}
			$before_widget         = hocwp_add_class_to_string( '', $before_widget, 'in-current-post' );
			$args['before_widget'] = $before_widget;
		}

		$number = hocwp_get_value_by_key( $instance, 'number', hocwp_get_value_by_key( $this->args, 'number' ) );
		if ( 0 > $number ) {
			$number = 0;
		}
		$thumbnail_size   = hocwp_get_value_by_key( $instance, 'thumbnail_size', hocwp_get_value_by_key( $this->args, 'thumbnail_size' ) );
		$thumbnail_size   = hocwp_sanitize_size( $thumbnail_size );
		$full_width_item  = hocwp_get_value_by_key( $instance, 'full_width_item', hocwp_get_value_by_key( $this->args, 'full_width_item' ) );
		$show_count       = hocwp_get_value_by_key( $instance, 'show_count', hocwp_get_value_by_key( $this->args, 'show_count' ) );
		$hide_thumbnail   = hocwp_get_value_by_key( $instance, 'hide_thumbnail', hocwp_get_value_by_key( $this->args, 'hide_thumbnail' ) );
		$only_thumbnail   = hocwp_get_value_by_key( $instance, 'only_thumbnail', hocwp_get_value_by_key( $this->args, 'only_thumbnail' ) );
		$order            = hocwp_get_value_by_key( $instance, 'order', hocwp_get_value_by_key( $this->args, 'order' ) );
		$orderby          = hocwp_get_value_by_key( $instance, 'orderby', hocwp_get_value_by_key( $this->args, 'orderby' ) );
		$count_format     = hocwp_get_value_by_key( $instance, 'count_format', hocwp_get_value_by_key( $this->args, 'count_format' ) );
		$different_name   = (bool) hocwp_get_value_by_key( $instance, 'different_name', hocwp_get_value_by_key( $this->args, 'different_name' ) );
		$hide_empty       = hocwp_get_value_by_key( $instance, 'hide_empty', hocwp_get_value_by_key( $this->args, 'hide_empty' ) );
		$only_parent      = hocwp_get_value_by_key( $instance, 'only_parent', hocwp_get_value_by_key( $this->args, 'only_parent' ) );
		$child_of_current = hocwp_get_value_by_key( $instance, 'child_of_current', hocwp_get_value_by_key( $this->args, 'child_of_current' ) );
		$child_of_parent  = hocwp_get_value_by_key( $instance, 'child_of_parent', hocwp_get_value_by_key( $this->args, 'child_of_parent' ) );
		$parent_as_title  = hocwp_get_value_by_key( $instance, 'parent_as_title', hocwp_get_value_by_key( $this->args, 'parent_as_title' ) );

		if ( $hide_thumbnail ) {
			$only_thumbnail = false;
		}

		$defaults = array(
			'order'   => $order,
			'orderby' => $orderby,
			'number'  => absint( $number )
		);
		if ( 0 == $hide_empty || ! (bool) $hide_empty ) {
			$defaults['hide_empty'] = 0;
		} else {
			$defaults['hide_empty'] = 1;
		}
		if ( $only_parent ) {
			$defaults['parent'] = 0;
		}
		if ( $in_current_post && is_singular() ) {
			$terms = wp_get_post_terms( get_the_ID(), $taxonomies );
		} elseif ( ( $child_of_current || $child_of_parent ) && is_tax() ) {
			$current_term = hocwp_term_get_current();
			$tax_args     = array( 'child_of' => $current_term->term_id );
			$tax_args     = wp_parse_args( $tax_args, $defaults );
			unset( $tax_args['parent'] );
			$terms = hocwp_get_terms( $current_term->taxonomy, $tax_args );
			if ( $child_of_parent ) {
				if ( ! hocwp_array_has_value( $terms ) && hocwp_id_number_valid( $current_term->parent ) ) {
					$parent               = hocwp_term_get_top_most_parent( $current_term );
					$tax_args['child_of'] = $parent->term_id;
					$terms                = hocwp_get_terms( $parent->taxonomy, $tax_args );
				}
			}
		} else {
			$terms = hocwp_get_terms( $taxonomies, $defaults );
		}

		if ( ( $in_current_post || $child_of_current ) && ! hocwp_array_has_value( $terms ) ) {
			return;
		}
		if ( $parent_as_title && is_tax() ) {
			$current_term = hocwp_term_get_current();
			if ( hocwp_id_number_valid( $current_term->parent ) ) {
				$parent            = hocwp_term_get_top_most_parent( $current_term );
				$instance['title'] = $parent->name;
			} elseif ( $child_of_current ) {
				$instance['title'] = $current_term->name;
			}
		}
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
					$html .= hocwp_term_get_thumbnail_html( array(
						'term'      => $term,
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
			_e( 'Sorry, nothing found.', 'hocwp-theme' );
		}
		$widget_html = ob_get_clean();
		$widget_html = apply_filters( 'hocwp_widget_term_html', $widget_html, $args, $instance, $this );
		echo $widget_html;
		hocwp_widget_after( $args, $instance );
	}

	public function form( $instance ) {
		$this->instance   = $instance;
		$title            = hocwp_get_value_by_key( $instance, 'title' );
		$taxonomy         = $this->get_taxonomy_from_instance( $instance );
		$number           = hocwp_get_value_by_key( $instance, 'number', hocwp_get_value_by_key( $this->args, 'number' ) );
		$thumbnail_size   = hocwp_get_value_by_key( $instance, 'thumbnail_size', hocwp_get_value_by_key( $this->args, 'thumbnail_size' ) );
		$full_width_item  = hocwp_get_value_by_key( $instance, 'full_width_item', hocwp_get_value_by_key( $this->args, 'full_width_item' ) );
		$count_format     = hocwp_get_value_by_key( $instance, 'count_format', hocwp_get_value_by_key( $this->args, 'count_format' ) );
		$show_count       = hocwp_get_value_by_key( $instance, 'show_count', hocwp_get_value_by_key( $this->args, 'show_count' ) );
		$hide_thumbnail   = hocwp_get_value_by_key( $instance, 'hide_thumbnail', hocwp_get_value_by_key( $this->args, 'hide_thumbnail' ) );
		$only_thumbnail   = hocwp_get_value_by_key( $instance, 'only_thumbnail', hocwp_get_value_by_key( $this->args, 'only_thumbnail' ) );
		$order            = hocwp_get_value_by_key( $instance, 'order', hocwp_get_value_by_key( $this->args, 'order' ) );
		$orderby          = hocwp_get_value_by_key( $instance, 'orderby', hocwp_get_value_by_key( $this->args, 'orderby' ) );
		$different_name   = (bool) hocwp_get_value_by_key( $instance, 'different_name', hocwp_get_value_by_key( $this->args, 'different_name' ) );
		$in_current_post  = (bool) hocwp_get_value_by_key( $instance, 'in_current_post', hocwp_get_value_by_key( $this->args, 'in_current_post' ) );
		$hide_empty       = hocwp_get_value_by_key( $instance, 'hide_empty', hocwp_get_value_by_key( $this->args, 'hide_empty' ) );
		$only_parent      = hocwp_get_value_by_key( $instance, 'only_parent', hocwp_get_value_by_key( $this->args, 'only_parent' ) );
		$child_of_current = hocwp_get_value_by_key( $instance, 'child_of_current', hocwp_get_value_by_key( $this->args, 'child_of_current' ) );
		$child_of_parent  = hocwp_get_value_by_key( $instance, 'child_of_parent', hocwp_get_value_by_key( $this->args, 'child_of_parent' ) );
		$parent_as_title  = hocwp_get_value_by_key( $instance, 'parent_as_title', hocwp_get_value_by_key( $this->args, 'parent_as_title' ) );

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
			$all_option .= hocwp_field_get_option( array(
				'value'    => $lvalue->name,
				'text'     => $lvalue->labels->singular_name,
				'selected' => $selected
			) );
		}

		$args = array(
			'id'          => $this->get_field_id( 'taxonomy' ),
			'name'        => $this->get_field_name( 'taxonomy' ),
			'all_option'  => $all_option,
			'value'       => $taxonomy,
			'label'       => __( 'Taxonomy:', 'hocwp-theme' ),
			'placeholder' => __( 'Choose taxonomy', 'hocwp-theme' ),
			'multiple'    => true
		);
		hocwp_widget_field( 'hocwp_field_select_chosen', $args );

		$args = array(
			'id'    => $this->get_field_id( 'number' ),
			'name'  => $this->get_field_name( 'number' ),
			'value' => $number,
			'label' => __( 'Number items:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_number', $args );

		$lists      = $this->args['orderbys'];
		$all_option = '';
		foreach ( $lists as $lkey => $lvalue ) {
			$all_option .= hocwp_field_get_option( array(
				'value'    => $lkey,
				'text'     => $lvalue,
				'selected' => $orderby
			) );
		}
		$args = array(
			'id'         => $this->get_field_id( 'orderby' ),
			'name'       => $this->get_field_name( 'orderby' ),
			'value'      => $orderby,
			'all_option' => $all_option,
			'label'      => __( 'Order by:', 'hocwp-theme' ),
			'class'      => 'orderby'
		);
		hocwp_widget_field( 'hocwp_field_select', $args );

		$lists      = $this->args['orders'];
		$all_option = '';
		foreach ( $lists as $lkey => $lvalue ) {
			$all_option .= hocwp_field_get_option( array(
				'value'    => strtolower( $lvalue ),
				'text'     => strtoupper( $lvalue ),
				'selected' => $order
			) );
		}
		$args = array(
			'id'         => $this->get_field_id( 'order' ),
			'name'       => $this->get_field_name( 'order' ),
			'value'      => $order,
			'all_option' => $all_option,
			'label'      => __( 'Order:', 'hocwp-theme' ),
			'class'      => 'order'
		);
		hocwp_widget_field( 'hocwp_field_select', $args );

		$args = array(
			'id_width'    => $this->get_field_id( 'thumbnail_size_width' ),
			'name_width'  => $this->get_field_name( 'thumbnail_size_width' ),
			'id_height'   => $this->get_field_id( 'thumbnail_size_height' ),
			'name_height' => $this->get_field_name( 'thumbnail_size_height' ),
			'value'       => $thumbnail_size,
			'label'       => __( 'Thumbnail size:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_size', $args );

		$lists      = $this->args['full_width_items'];
		$all_option = '';
		foreach ( $lists as $lkey => $lvalue ) {
			$all_option .= hocwp_field_get_option( array(
				'value'    => $lkey,
				'text'     => $lvalue,
				'selected' => $full_width_item
			) );
		}
		$args = array(
			'id'         => $this->get_field_id( 'full_width_item' ),
			'name'       => $this->get_field_name( 'full_width_item' ),
			'value'      => $full_width_item,
			'all_option' => $all_option,
			'label'      => __( 'Full width items:', 'hocwp-theme' ),
			'class'      => 'full-width-item'
		);
		hocwp_widget_field( 'hocwp_field_select', $args );

		$args = array(
			'id'    => $this->get_field_id( 'count_format' ),
			'name'  => $this->get_field_name( 'count_format' ),
			'value' => $count_format,
			'label' => __( 'Count format:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input', $args );

		$args = array(
			'id'    => $this->get_field_id( 'hide_thumbnail' ),
			'name'  => $this->get_field_name( 'hide_thumbnail' ),
			'value' => $hide_thumbnail,
			'label' => __( 'Hide term thumbnail?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'show_count' ),
			'name'  => $this->get_field_name( 'show_count' ),
			'value' => $show_count,
			'label' => __( 'Show count?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'only_thumbnail' ),
			'name'  => $this->get_field_name( 'only_thumbnail' ),
			'value' => $only_thumbnail,
			'label' => __( 'Only thumbnail?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'different_name' ),
			'name'  => $this->get_field_name( 'different_name' ),
			'value' => $different_name,
			'label' => __( 'Use different term name?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'in_current_post' ),
			'name'  => $this->get_field_name( 'in_current_post' ),
			'value' => $in_current_post,
			'label' => __( 'Get term in current post?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'only_parent' ),
			'name'  => $this->get_field_name( 'only_parent' ),
			'value' => $only_parent,
			'label' => __( 'Show terms have no parent only?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'child_of_current' ),
			'name'  => $this->get_field_name( 'child_of_current' ),
			'value' => $child_of_current,
			'label' => __( 'Get all childs of current term?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'child_of_parent' ),
			'name'  => $this->get_field_name( 'child_of_parent' ),
			'value' => $child_of_parent,
			'label' => __( 'Get all childs of parent if current term has no child?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'parent_as_title' ),
			'name'  => $this->get_field_name( 'parent_as_title' ),
			'value' => $parent_as_title,
			'label' => __( 'Display parent term as widget title?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'hide_empty' ),
			'name'  => $this->get_field_name( 'hide_empty' ),
			'value' => $hide_empty,
			'label' => __( 'Hide term if it doesn\'t have post.', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		hocwp_field_widget_after();
	}

	public function update( $new_instance, $old_instance ) {
		$instance                     = $old_instance;
		$instance['title']            = strip_tags( hocwp_get_value_by_key( $new_instance, 'title' ) );
		$instance['taxonomy']         = hocwp_get_value_by_key( $new_instance, 'taxonomy', json_encode( $this->args['taxonomy'] ) );
		$instance['number']           = hocwp_get_value_by_key( $new_instance, 'number', $this->args['number'] );
		$instance['order']            = hocwp_get_value_by_key( $new_instance, 'order', $this->args['order'] );
		$instance['orderby']          = hocwp_get_value_by_key( $new_instance, 'orderby', $this->args['orderby'] );
		$instance['full_width_item']  = hocwp_get_value_by_key( $new_instance, 'full_width_item', $this->args['full_width_item'] );
		$instance['count_format']     = hocwp_get_value_by_key( $new_instance, 'count_format', $this->args['count_format'] );
		$width                        = hocwp_get_value_by_key( $new_instance, 'thumbnail_size_width', $this->args['thumbnail_size'][0] );
		$height                       = hocwp_get_value_by_key( $new_instance, 'thumbnail_size_height', $this->args['thumbnail_size'][1] );
		$instance['thumbnail_size']   = array( $width, $height );
		$instance['hide_thumbnail']   = hocwp_checkbox_post_data_value( $new_instance, 'hide_thumbnail' );
		$instance['show_count']       = hocwp_checkbox_post_data_value( $new_instance, 'show_count' );
		$instance['only_thumbnail']   = hocwp_checkbox_post_data_value( $new_instance, 'only_thumbnail' );
		$instance['different_name']   = hocwp_checkbox_post_data_value( $new_instance, 'different_name' );
		$instance['in_current_post']  = hocwp_checkbox_post_data_value( $new_instance, 'in_current_post' );
		$instance['hide_empty']       = hocwp_checkbox_post_data_value( $new_instance, 'hide_empty' );
		$instance['only_parent']      = hocwp_checkbox_post_data_value( $new_instance, 'only_parent' );
		$instance['child_of_current'] = hocwp_checkbox_post_data_value( $new_instance, 'child_of_current' );
		$instance['child_of_parent']  = hocwp_checkbox_post_data_value( $new_instance, 'child_of_parent' );
		$instance['parent_as_title']  = hocwp_checkbox_post_data_value( $new_instance, 'parent_as_title' );

		return $instance;
	}
}