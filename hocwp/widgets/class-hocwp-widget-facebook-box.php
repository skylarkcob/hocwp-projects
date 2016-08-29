<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

class HOCWP_Widget_Facebook_Box extends WP_Widget {
	public $args = array();
	public $admin_args;
	public $instance;

	private function get_defaults() {
		$defaults = array(
			'width'                 => 340,
			'height'                => 500,
			'hide_cover'            => false,
			'show_facepile'         => true,
			'show_posts'            => false,
			'hide_cta'              => false,
			'small_header'          => false,
			'adapt_container_width' => true
		);
		$defaults = apply_filters( 'hocwp_widget_facebook_box_defaults', $defaults, $this );
		$args     = apply_filters( 'hocwp_widget_facebook_box_args', array(), $this );
		$args     = wp_parse_args( $args, $defaults );

		return $args;
	}

	public function __construct() {
		$this->args       = $this->get_defaults();
		$this->admin_args = array(
			'id'          => 'hocwp_widget_facebook_box',
			'name'        => 'HocWP Facebook Box',
			'class'       => 'hocwp-facebook-box hocwp-widget-facebook-box',
			'description' => __( 'Facebook fanpage box widget.', 'hocwp-theme' ),
			'width'       => 400
		);
		$this->admin_args = apply_filters( 'hocwp_widget_facebook_box_admin_args', $this->admin_args, $this );
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
		add_filter( 'hocwp_use_facebook_javascript_sdk', '__return_true' );
		$this->instance        = $instance;
		$page_name             = isset( $instance['page_name'] ) ? $instance['page_name'] : '';
		$href                  = isset( $instance['href'] ) ? $instance['href'] : '';
		$width                 = isset( $instance['width'] ) ? $instance['width'] : $this->args['width'];
		$height                = isset( $instance['height'] ) ? $instance['height'] : $this->args['height'];
		$hide_cover            = (bool) ( isset( $instance['hide_cover'] ) ? $instance['hide_cover'] : $this->args['hide_cover'] );
		$show_facepile         = (bool) ( isset( $instance['show_facepile'] ) ? $instance['show_facepile'] : $this->args['show_facepile'] );
		$show_posts            = (bool) ( isset( $instance['show_posts'] ) ? $instance['show_posts'] : $this->args['show_posts'] );
		$hide_cta              = (bool) ( isset( $instance['hide_cta'] ) ? $instance['hide_cta'] : $this->args['hide_cta'] );
		$small_header          = (bool) ( isset( $instance['small_header'] ) ? $instance['small_header'] : $this->args['small_header'] );
		$adapt_container_width = (bool) ( isset( $instance['adapt_container_width'] ) ? $instance['adapt_container_width'] : $this->args['adapt_container_width'] );
		hocwp_widget_before( $args, $instance );
		$fanpage_args = array(
			'page_name'             => $page_name,
			'href'                  => $href,
			'width'                 => $width,
			'height'                => $height,
			'hide_cover'            => $hide_cover,
			'show_facepile'         => $show_facepile,
			'show_posts'            => $show_posts,
			'hide_cta'              => $hide_cta,
			'small_header'          => $small_header,
			'adapt_container_width' => $adapt_container_width
		);
		ob_start();
		hocwp_facebook_page_plugin( $fanpage_args );
		$widget_html = ob_get_clean();
		$widget_html = apply_filters( 'hocwp_widget_facebook_box_html', $widget_html, $args, $instance, $this );
		echo $widget_html;
		hocwp_widget_after( $args, $instance );
	}

	public function form( $instance ) {
		$this->instance        = $instance;
		$title                 = isset( $instance['title'] ) ? $instance['title'] : '';
		$page_name             = isset( $instance['page_name'] ) ? $instance['page_name'] : '';
		$href                  = isset( $instance['href'] ) ? $instance['href'] : '';
		$width                 = isset( $instance['width'] ) ? $instance['width'] : $this->args['width'];
		$height                = isset( $instance['height'] ) ? $instance['height'] : $this->args['height'];
		$hide_cover            = (bool) ( isset( $instance['hide_cover'] ) ? $instance['hide_cover'] : $this->args['hide_cover'] );
		$show_facepile         = (bool) ( isset( $instance['show_facepile'] ) ? $instance['show_facepile'] : $this->args['show_facepile'] );
		$show_posts            = (bool) ( isset( $instance['show_posts'] ) ? $instance['show_posts'] : $this->args['show_posts'] );
		$hide_cta              = (bool) ( isset( $instance['hide_cta'] ) ? $instance['hide_cta'] : $this->args['hide_cta'] );
		$small_header          = (bool) ( isset( $instance['small_header'] ) ? $instance['small_header'] : $this->args['small_header'] );
		$adapt_container_width = (bool) ( isset( $instance['adapt_container_width'] ) ? $instance['adapt_container_width'] : $this->args['adapt_container_width'] );
		hocwp_field_widget_before( $this->admin_args['class'] );
		hocwp_widget_field_title( $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), $title );

		$args = array(
			'id'    => $this->get_field_id( 'page_name' ),
			'name'  => $this->get_field_name( 'page_name' ),
			'value' => $page_name,
			'label' => __( 'Page name:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input', $args );

		$args = array(
			'id'    => $this->get_field_id( 'href' ),
			'name'  => $this->get_field_name( 'href' ),
			'value' => $href,
			'label' => __( 'Page url:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input', $args );

		$args = array(
			'id_width'    => $this->get_field_id( 'width' ),
			'name_width'  => $this->get_field_name( 'width' ),
			'id_height'   => $this->get_field_id( 'height' ),
			'name_height' => $this->get_field_name( 'height' ),
			'value'       => array( $width, $height ),
			'label'       => __( 'Size:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_size', $args );

		$args = array(
			'id'    => $this->get_field_id( 'hide_cover' ),
			'name'  => $this->get_field_name( 'hide_cover' ),
			'value' => $hide_cover,
			'label' => __( 'Hide cover photo in the header?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'show_facepile' ),
			'name'  => $this->get_field_name( 'show_facepile' ),
			'value' => $show_facepile,
			'label' => __( 'Show profile photos when friends like this?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'show_posts' ),
			'name'  => $this->get_field_name( 'show_posts' ),
			'value' => $show_posts,
			'label' => __( 'Show posts from the Page\'s timeline?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'hide_cta' ),
			'name'  => $this->get_field_name( 'hide_cta' ),
			'value' => $hide_cta,
			'label' => __( 'Hide the custom call to action button?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'small_header' ),
			'name'  => $this->get_field_name( 'small_header' ),
			'value' => $small_header,
			'label' => __( 'Use the small header instead?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'adapt_container_width' ),
			'name'  => $this->get_field_name( 'adapt_container_width' ),
			'value' => $adapt_container_width,
			'label' => __( 'Try to fit inside the container width?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		hocwp_field_widget_after();
	}

	public function update( $new_instance, $old_instance ) {
		$instance                          = $old_instance;
		$instance['title']                 = strip_tags( hocwp_get_value_by_key( $new_instance, 'title' ) );
		$instance['page_name']             = hocwp_get_value_by_key( $new_instance, 'page_name' );
		$instance['href']                  = hocwp_get_value_by_key( $new_instance, 'href' );
		$instance['width']                 = hocwp_get_value_by_key( $new_instance, 'width', $this->args['width'] );
		$instance['height']                = hocwp_get_value_by_key( $new_instance, 'height', $this->args['height'] );
		$instance['hide_cover']            = hocwp_checkbox_post_data_value( $new_instance, 'hide_cover', hocwp_bool_to_int( $this->args['hide_cover'] ) );
		$instance['show_facepile']         = hocwp_checkbox_post_data_value( $new_instance, 'show_facepile', hocwp_bool_to_int( $this->args['show_facepile'] ) );
		$instance['show_posts']            = hocwp_checkbox_post_data_value( $new_instance, 'show_posts', hocwp_bool_to_int( $this->args['show_posts'] ) );
		$instance['hide_cta']              = hocwp_checkbox_post_data_value( $new_instance, 'hide_cta', hocwp_bool_to_int( $this->args['hide_cta'] ) );
		$instance['small_header']          = hocwp_checkbox_post_data_value( $new_instance, 'small_header', hocwp_bool_to_int( $this->args['small_header'] ) );
		$instance['adapt_container_width'] = hocwp_checkbox_post_data_value( $new_instance, 'adapt_container_width', hocwp_bool_to_int( $this->args['adapt_container_width'] ) );

		return $instance;
	}
}