<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

class HOCWP_Widget_FeedBurner extends WP_Widget {
	public $args = array();
	public $admin_args;
	public $instance;

	private function get_defaults() {
		$defaults = array(
			'button_text'    => __( 'Subscribe', 'hocwp-theme' ),
			'placeholder'    => __( 'Enter your email', 'hocwp-theme' ),
			'description'    => '',
			'desc_position'  => 'before',
			'desc_positions' => array(
				'before' => __( 'Before email field', 'hocwp-theme' ),
				'after'  => __( 'After email field', 'hocwp-theme' )
			)
		);
		$defaults = apply_filters( 'hocwp_widget_feedburner_defaults', $defaults, $this );
		$args     = apply_filters( 'hocwp_widget_feedburner_args', array(), $this );
		$args     = wp_parse_args( $args, $defaults );

		return $args;
	}

	public function __construct() {
		$this->args       = $this->get_defaults();
		$this->admin_args = array(
			'id'          => 'hocwp_widget_feedburner',
			'name'        => 'HOCWP FeedBurner',
			'class'       => 'hocwp-feedburner-widget',
			'description' => __( 'Display FeedBurner subscription box on sidebar.', 'hocwp-theme' ),
			'width'       => 400
		);
		$this->admin_args = apply_filters( 'hocwp_widget_feedburner_admin_args', $this->admin_args, $this );
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

	public function widget( $args, $instance ) {
		$this->instance  = $instance;
		$feedburner_name = hocwp_get_value_by_key( $instance, 'feedburner_name' );
		if ( ! empty( $feedburner_name ) ) {
			$button_text   = hocwp_get_value_by_key( $instance, 'button_text', hocwp_get_value_by_key( $this->args, 'button_text' ) );
			$placeholder   = hocwp_get_value_by_key( $instance, 'placeholder', hocwp_get_value_by_key( $this->args, 'placeholder' ) );
			$description   = hocwp_get_value_by_key( $instance, 'description', hocwp_get_value_by_key( $this->args, 'description' ) );
			$desc_position = hocwp_get_value_by_key( $instance, 'desc_position', hocwp_get_value_by_key( $this->args, 'desc_position' ) );
			hocwp_widget_before( $args, $instance );
			ob_start();
			if ( ! empty( $description ) && 'before' == $desc_position ) {
				echo '<p class="description">' . $description . '</p>';
			}
			$fb_args = array(
				'button_text' => $button_text,
				'name'        => $feedburner_name,
				'placeholder' => $placeholder
			);
			hocwp_feedburner_form( $fb_args );
			if ( ! empty( $description ) && 'after' == $desc_position ) {
				echo '<p class="description">' . $description . '</p>';
			}
			$widget_html = ob_get_clean();
			$widget_html = apply_filters( 'hocwp_widget_feedburner_html', $widget_html, $args, $instance, $this );
			echo $widget_html;
			hocwp_widget_after( $args, $instance );
		}
	}

	public function form( $instance ) {
		$this->instance  = $instance;
		$title           = isset( $instance['title'] ) ? $instance['title'] : '';
		$feedburner_name = hocwp_get_value_by_key( $instance, 'feedburner_name' );
		$button_text     = hocwp_get_value_by_key( $instance, 'button_text', hocwp_get_value_by_key( $this->args, 'button_text' ) );
		$placeholder     = hocwp_get_value_by_key( $instance, 'placeholder', hocwp_get_value_by_key( $this->args, 'placeholder' ) );
		$description     = hocwp_get_value_by_key( $instance, 'description', hocwp_get_value_by_key( $this->args, 'description' ) );
		$desc_position   = hocwp_get_value_by_key( $instance, 'desc_position', hocwp_get_value_by_key( $this->args, 'desc_position' ) );
		hocwp_field_widget_before( $this->admin_args['class'] );
		hocwp_widget_field_title( $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), $title );

		$args = array(
			'id'    => $this->get_field_id( 'feedburner_name' ),
			'name'  => $this->get_field_name( 'feedburner_name' ),
			'value' => $feedburner_name,
			'label' => __( 'Name:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_text', $args );

		$args = array(
			'id'    => $this->get_field_id( 'button_text' ),
			'name'  => $this->get_field_name( 'button_text' ),
			'value' => $button_text,
			'label' => __( 'Button text:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_text', $args );

		$args = array(
			'id'    => $this->get_field_id( 'placeholder' ),
			'name'  => $this->get_field_name( 'placeholder' ),
			'value' => $placeholder,
			'label' => __( 'Placeholder:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_text', $args );

		$args = array(
			'id'    => $this->get_field_id( 'description' ),
			'name'  => $this->get_field_name( 'description' ),
			'value' => $description,
			'label' => __( 'Description:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_textarea', $args );

		$lists      = $this->args['desc_positions'];
		$all_option = '';
		foreach ( $lists as $lkey => $lvalue ) {
			$all_option .= hocwp_field_get_option( array(
				'value'    => $lkey,
				'text'     => $lvalue,
				'selected' => $desc_position
			) );
		}
		$args = array(
			'id'         => $this->get_field_id( 'desc_position' ),
			'name'       => $this->get_field_name( 'desc_position' ),
			'value'      => $desc_position,
			'all_option' => $all_option,
			'label'      => __( 'Description position:', 'hocwp-theme' ),
			'class'      => 'desc-position'
		);
		hocwp_widget_field( 'hocwp_field_select', $args );

		hocwp_field_widget_after();
	}

	public function update( $new_instance, $old_instance ) {
		$instance                    = $old_instance;
		$instance['title']           = strip_tags( hocwp_get_value_by_key( $new_instance, 'title' ) );
		$instance['feedburner_name'] = hocwp_get_value_by_key( $new_instance, 'feedburner_name' );
		$instance['button_text']     = hocwp_get_value_by_key( $new_instance, 'button_text', hocwp_get_value_by_key( $this->args, 'button_text' ) );
		$instance['placeholder']     = hocwp_get_value_by_key( $new_instance, 'placeholder', hocwp_get_value_by_key( $this->args, 'placeholder' ) );
		$instance['description']     = hocwp_get_value_by_key( $new_instance, 'description', hocwp_get_value_by_key( $this->args, 'description' ) );
		$instance['desc_position']   = hocwp_get_value_by_key( $new_instance, 'desc_position', $this->args['desc_position'] );

		return $instance;
	}
}