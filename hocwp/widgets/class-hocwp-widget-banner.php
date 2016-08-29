<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

class HOCWP_Widget_Banner extends WP_Widget {
	public $args = array();
	public $admin_args;
	public $instance;

	private function get_defaults() {
		$defaults = array(
			'nofollow' => 0
		);
		$defaults = apply_filters( 'hocwp_widget_banner_defaults', $defaults, $this );
		$args     = apply_filters( 'hocwp_widget_banner_args', array(), $this );
		$args     = wp_parse_args( $args, $defaults );

		return $args;
	}

	public function __construct() {
		$this->args       = $this->get_defaults();
		$this->admin_args = array(
			'id'          => 'hocwp_widget_banner',
			'name'        => 'HOCWP Banner',
			'class'       => 'hocwp-banner-widget',
			'description' => __( 'Display banner on sidebar.', 'hocwp-theme' ),
			'width'       => 400
		);
		$this->admin_args = apply_filters( 'hocwp_widget_banner_admin_args', $this->admin_args, $this );
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
		$title_text     = isset( $instance['title'] ) ? $instance['title'] : '';
		$first_char     = hocwp_get_first_char( $title_text );
		if ( '!' === $first_char ) {
			$title_text = ltrim( $title_text, '!' );
		}
		$banner_image = isset( $instance['banner_image'] ) ? $instance['banner_image'] : '';
		$banner_url   = isset( $instance['banner_url'] ) ? $instance['banner_url'] : '';
		$banner_image = hocwp_sanitize_media_value( $banner_image );
		$banner_image = $banner_image['url'];
		if ( ! empty( $banner_image ) ) {
			hocwp_widget_before( $args, $instance );
			$img = new HOCWP_HTML( 'img' );
			$img->set_image_src( $banner_image );
			$img->set_image_alt( $title_text );
			$img->set_class( 'hocwp-banner-image' );
			$html = $img->build();
			if ( ! empty( $banner_url ) ) {
				$a = new HOCWP_HTML( 'a' );
				$a->set_class( 'hocwp-banner-link' );
				$a->set_attribute( 'title', $title_text );
				$a->set_href( $banner_url );
				$a->set_text( $html );
				$nofollow = hocwp_get_value_by_key( $instance, 'nofollow', hocwp_get_value_by_key( $this->args, 'nofollow' ) );
				if ( (bool) $nofollow ) {
					$a->set_attribute( 'rel', 'nofollow' );
				}
				$html = $a->build();
			}
			$widget_html = apply_filters( 'hocwp_widget_banner_html', $html, $args, $instance, $this );
			echo $widget_html;
			hocwp_widget_after( $args, $instance );
		}
	}

	public function form( $instance ) {
		$this->instance = $instance;
		$title          = isset( $instance['title'] ) ? $instance['title'] : '';
		$banner_image   = isset( $instance['banner_image'] ) ? $instance['banner_image'] : '';
		$banner_url     = isset( $instance['banner_url'] ) ? $instance['banner_url'] : '';
		hocwp_field_widget_before( $this->admin_args['class'] );
		hocwp_widget_field_title( $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), $title );

		$args = array(
			'id'    => $this->get_field_id( 'banner_image' ),
			'name'  => $this->get_field_name( 'banner_image' ),
			'value' => $banner_image,
			'label' => __( 'Image url:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_media_upload', $args );

		$args = array(
			'id'    => $this->get_field_id( 'banner_url' ),
			'name'  => $this->get_field_name( 'banner_url' ),
			'value' => $banner_url,
			'label' => __( 'Image link:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_text', $args );

		$nofollow = hocwp_get_value_by_key( $instance, 'nofollow', hocwp_get_value_by_key( $this->args, 'nofollow' ) );
		$args     = array(
			'id'    => $this->get_field_id( 'nofollow' ),
			'name'  => $this->get_field_name( 'nofollow' ),
			'value' => $nofollow,
			'label' => __( 'Add rel nofollow for this link?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		hocwp_field_widget_after();
	}

	public function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['title']        = strip_tags( hocwp_get_value_by_key( $new_instance, 'title' ) );
		$instance['banner_image'] = hocwp_get_value_by_key( $new_instance, 'banner_image' );
		$instance['banner_url']   = hocwp_get_value_by_key( $new_instance, 'banner_url' );
		$instance['nofollow']     = hocwp_checkbox_post_data_value( $new_instance, 'nofollow' );

		return $instance;
	}
}