<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

class HOCWP_Widget_Link extends WP_Widget {
	public $args = array();
	public $admin_args;
	public $instance;

	private function get_defaults() {
		$defaults = array(
			'category' => array()
		);
		$defaults = apply_filters( 'hocwp_widget_link_defaults', $defaults, $this );
		$args     = apply_filters( 'hocwp_widget_link_args', array(), $this );
		$args     = wp_parse_args( $args, $defaults );

		return $args;
	}

	public function __construct() {
		$this->args       = $this->get_defaults();
		$this->admin_args = array(
			'id'          => 'hocwp_widget_link',
			'name'        => 'HOCWP Link',
			'class'       => 'hocwp-link-widget',
			'description' => __( 'Display link on sidebar.', 'hocwp-theme' ),
			'width'       => 400
		);
		$this->admin_args = apply_filters( 'hocwp_widget_link_admin_args', $this->admin_args, $this );
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

	private function get_category_from_instance( $instance ) {
		$category = isset( $instance['category'] ) ? $instance['category'] : json_encode( $this->args['category'] );
		$category = hocwp_json_string_to_array( $category );
		if ( ! hocwp_array_has_value( $category ) ) {
			$category = array(
				array(
					'value' => apply_filters( 'hocwp_widget_link_default_category', '', $this )
				)
			);
		}

		return $category;
	}

	public function widget( $args, $instance ) {
		$this->instance = $instance;
		$category       = $this->get_category_from_instance( $instance );
		$categories     = array();
		foreach ( $category as $pvalue ) {
			$value = hocwp_get_value_by_key( $pvalue, 'value' );
			if ( hocwp_id_number_valid( $value ) ) {
				$categories[] = $value;
			}
		}
		$bm_args   = array(
			'category' => implode( ',', $categories )
		);
		$bookmarks = get_bookmarks( $bm_args );
		hocwp_widget_before( $args, $instance );
		$bookmarks = hocwp_sanitize_bookmark_link_image( $bookmarks );
		$bm_args   = array(
			'before' => '',
			'after'  => ''
		);
		echo _walk_bookmarks( $bookmarks, $bm_args );
		hocwp_widget_after( $args, $instance );
	}

	public function form( $instance ) {
		$this->instance = $instance;
		$title          = isset( $instance['title'] ) ? $instance['title'] : '';
		$category       = $this->get_category_from_instance( $instance );
		hocwp_field_widget_before( $this->admin_args['class'] );
		hocwp_widget_field_title( $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), $title );

		$all_option = '';
		$lists      = hocwp_get_terms( 'link_category' );
		foreach ( $lists as $lvalue ) {
			$selected = '';
			if ( ! hocwp_array_has_value( $category ) ) {
				$category[] = array( 'value' => '' );
			}
			foreach ( $category as $ptvalue ) {
				$ptype = isset( $ptvalue['value'] ) ? $ptvalue['value'] : '';
				if ( $lvalue->term_id == $ptype ) {
					$selected = $lvalue->term_id;
					break;
				}
			}
			$all_option .= hocwp_field_get_option( array(
				'value'    => $lvalue->term_id,
				'text'     => $lvalue->name,
				'selected' => $selected
			) );
		}

		$args = array(
			'id'          => $this->get_field_id( 'category' ),
			'name'        => $this->get_field_name( 'category' ),
			'all_option'  => $all_option,
			'value'       => $category,
			'label'       => __( 'Category:', 'hocwp-theme' ),
			'placeholder' => __( 'Choose category', 'hocwp-theme' ),
			'multiple'    => true
		);
		hocwp_widget_field( 'hocwp_field_select_chosen', $args );

		hocwp_field_widget_after();
	}

	public function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['title']    = strip_tags( hocwp_get_value_by_key( $new_instance, 'title' ) );
		$instance['category'] = hocwp_get_value_by_key( $new_instance, 'category', json_encode( $this->args['category'] ) );

		return $instance;
	}
}