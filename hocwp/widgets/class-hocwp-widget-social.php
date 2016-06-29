<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

class HOCWP_Widget_Social extends WP_Widget {
	public $args = array();
	public $admin_args;
	public $instance;

	private function get_defaults() {
		$option_socials = hocwp_option_defaults();
		$option_socials = $option_socials['social'];
		$defaults       = array(
			'order'        => $option_socials['order'],
			'option_names' => $option_socials['option_names'],
			'icons'        => $option_socials['icons']
		);
		$defaults       = apply_filters( 'hocwp_widget_social_defaults', $defaults, $this );
		$args           = apply_filters( 'hocwp_widget_social_args', array(), $this );
		$args           = wp_parse_args( $args, $defaults );

		return $args;
	}

	public function __construct() {
		$this->args       = $this->get_defaults();
		$this->admin_args = array(
			'id'          => 'hocwp_widget_social',
			'name'        => 'HOCWP Social',
			'class'       => 'hocwp-social-widget',
			'description' => __( 'Display list social icons on sidebar.', 'hocwp' ),
			'width'       => 400
		);
		$this->admin_args = apply_filters( 'hocwp_widget_social_admin_args', $this->admin_args, $this );
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
		$this->instance = $instance;
		$order          = hocwp_get_value_by_key( $instance, 'order', hocwp_get_value_by_key( $this->args, 'order' ) );
		$orders         = explode( ',', $order );
		$orders         = array_map( 'trim', $orders );
		$orders         = hocwp_sanitize_array( $orders );
		$option_names   = $this->args['option_names'];
		$options        = hocwp_get_option( 'option_social' );
		$icons          = $this->args['icons'];
		$description    = hocwp_get_value_by_key( $instance, 'description' );
		hocwp_widget_before( $args, $instance );
		ob_start();
		if ( ! empty( $description ) ) {
			echo hocwp_wrap_tag( wpautop( $description ), 'div', 'description' );
		}
		if ( hocwp_array_has_value( $orders ) ) {
			foreach ( $orders as $social ) {
				$option_name = hocwp_get_value_by_key( $option_names, $social );
				$item        = hocwp_get_value_by_key( $options, $option_name );
				if ( ! empty( $item ) ) {
					$icon = '<i class="fa ' . $icons[ $social ] . '"></i>';
					$a    = new HOCWP_HTML( 'a' );
					$a->set_href( $item );
					$a->set_class( 'social-item link-' . $social );
					$a->set_text( $icon );
					$a->output();
				}
			}
		}
		$widget_html = ob_get_clean();
		$widget_html = apply_filters( 'hocwp_widget_social_html', $widget_html, $args, $instance, $this );
		echo $widget_html;
		hocwp_widget_after( $args, $instance );
	}

	public function form( $instance ) {
		$this->instance = $instance;
		$title          = hocwp_get_value_by_key( $instance, 'title' );
		$order          = hocwp_get_value_by_key( $instance, 'order', hocwp_get_value_by_key( $this->args, 'order' ) );
		$description    = hocwp_get_value_by_key( $instance, 'description' );

		hocwp_field_widget_before( $this->admin_args['class'] );
		hocwp_widget_field_title( $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), $title );

		$args = array(
			'id'    => $this->get_field_id( 'order' ),
			'name'  => $this->get_field_name( 'order' ),
			'value' => $order,
			'label' => __( 'Order:', 'hocwp' )
		);
		hocwp_widget_field( 'hocwp_field_input_text', $args );

		$args = array(
			'id'    => $this->get_field_id( 'description' ),
			'name'  => $this->get_field_name( 'description' ),
			'value' => $description,
			'label' => __( 'Description:', 'hocwp' )
		);
		hocwp_widget_field( 'hocwp_field_textarea', $args );

		hocwp_field_widget_after();
	}

	public function update( $new_instance, $old_instance ) {
		$instance                = $old_instance;
		$instance['title']       = strip_tags( hocwp_get_value_by_key( $new_instance, 'title' ) );
		$instance['order']       = hocwp_get_value_by_key( $new_instance, 'order', hocwp_get_value_by_key( $this->args, 'order' ) );
		$instance['description'] = hocwp_get_value_by_key( $new_instance, 'description' );

		return $instance;
	}
}