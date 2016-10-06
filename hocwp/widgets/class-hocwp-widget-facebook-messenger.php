<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

class HOCWP_Widget_Facebook_Messenger extends WP_Widget {
	public $args = array();
	public $admin_args;
	public $instance;

	private function get_defaults() {
		$defaults = array(
			'width'                 => 340,
			'height'                => 300,
			'hide_cover'            => false,
			'show_facepile'         => true,
			'only_link'             => false,
			'hide_cta'              => false,
			'small_header'          => false,
			'adapt_container_width' => true,
			'button_text'           => __( 'Send us a message on Facebook', 'hocwp-theme' ),
			'fixed'                 => true,
			'position'              => 'bottom_right',
			'positions'             => array(
				'left'         => __( 'Left', 'hocwp-theme' ),
				'right'        => __( 'Right', 'hocwp-theme' ),
				'bottom_left'  => __( 'Bottom left', 'hocwp-theme' ),
				'bottom_right' => __( 'Bottom right', 'hocwp-theme' )
			)
		);
		$defaults = apply_filters( 'hocwp_widget_facebook_messenger_defaults', $defaults, $this );
		$args     = apply_filters( 'hocwp_widget_facebook_messenger_args', array(), $this );
		$args     = wp_parse_args( $args, $defaults );

		return $args;
	}

	public function __construct() {
		$this->args       = $this->get_defaults();
		$this->admin_args = array(
			'id'          => 'hocwp_widget_facebook_messenger',
			'name'        => 'HocWP Facebook Messenger',
			'class'       => 'hocwp-facebook-messenger hocwp-widget-facebook-messenger',
			'description' => __( 'Embed Facebook Messenger.', 'hocwp-theme' ),
			'width'       => 400
		);
		$this->admin_args = apply_filters( 'hocwp_widget_facebook_messenger_admin_args', $this->admin_args, $this );
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
		$this->instance        = $instance;
		$page_name             = isset( $instance['page_name'] ) ? $instance['page_name'] : '';
		$href                  = isset( $instance['href'] ) ? $instance['href'] : '';
		$width                 = isset( $instance['width'] ) ? $instance['width'] : $this->args['width'];
		$height                = isset( $instance['height'] ) ? $instance['height'] : $this->args['height'];
		$hide_cover            = (bool) ( isset( $instance['hide_cover'] ) ? $instance['hide_cover'] : $this->args['hide_cover'] );
		$show_facepile         = (bool) ( isset( $instance['show_facepile'] ) ? $instance['show_facepile'] : $this->args['show_facepile'] );
		$hide_cta              = (bool) ( isset( $instance['hide_cta'] ) ? $instance['hide_cta'] : $this->args['hide_cta'] );
		$small_header          = (bool) ( isset( $instance['small_header'] ) ? $instance['small_header'] : $this->args['small_header'] );
		$adapt_container_width = (bool) ( isset( $instance['adapt_container_width'] ) ? $instance['adapt_container_width'] : $this->args['adapt_container_width'] );
		$fixed                 = hocwp_get_value_by_key( $instance, 'fixed', $this->args['fixed'] );
		$only_link             = hocwp_get_value_by_key( $instance, 'only_link', $this->args['only_link'] );
		$button_text           = hocwp_get_value_by_key( $instance, 'button_text', $this->args['button_text'] );

		$before_widget = hocwp_get_value_by_key( $args, 'before_widget' );
		$widget_class  = '';
		if ( $fixed ) {
			hocwp_add_string_with_space_before( $widget_class, 'fixed' );
			$position = hocwp_get_value_by_key( $instance, 'position', $this->args['position'] );
			hocwp_add_string_with_space_before( $widget_class, hocwp_sanitize_html_class( $position ) );
		}
		$before_widget         = hocwp_add_class_to_string( '', $before_widget, $widget_class );
		$args['before_widget'] = $before_widget;
		$img                   = new HOCWP_HTML( 'img' );
		$img->set_image_src( hocwp_get_image_url( 'icon-facebook-messenger-white-64.png' ) );
		$img->set_class( 'icon-messenger' );
		hocwp_widget_before( $args, $instance );
		if ( $only_link ) {
			if ( ! hocwp_is_url( $href ) ) {
				$href = 'https://m.me/' . $href;
			}
			$span = new HOCWP_HTML( 'span' );
			$span->set_text( $button_text );
			$link_text = $img->build();
			$link_text .= $span->build();
			$a = new HOCWP_HTML( 'a' );
			$a->add_class( 'button btn btn-facebook-messenger' );
			$a->set_text( $link_text );
			$a->set_href( $href );
			$widget_html = $a->build();
		} else {
			$app_id = hocwp_get_wpseo_social_facebook_app_id();
			if ( empty( $app_id ) ) {
				hocwp_debug_log( __( 'Please set your Facebook APP ID first.', 'hocwp-theme' ) );

				return;
			}
			add_filter( 'hocwp_use_facebook_javascript_sdk', '__return_true' );
			?>
			<script type="text/javascript">
				window.fbAsyncInit = function () {
					FB.init({
						appId: '<?php echo $app_id; ?>',
						cookie: true,
						xfbml: true,
						version: 'v<?php echo HOCWP_FACEBOOK_JAVASCRIPT_SDK_VERSION; ?>'
					});
				};
			</script>
			<?php
			$fanpage_args = array(
				'page_name'             => $page_name,
				'href'                  => $href,
				'width'                 => $width,
				'height'                => $height,
				'tabs'                  => 'messages',
				'hide_cover'            => $hide_cover,
				'show_facepile'         => $show_facepile,
				'hide_cta'              => $hide_cta,
				'small_header'          => $small_header,
				'adapt_container_width' => $adapt_container_width
			);
			ob_start();
			if ( $fixed ) {
				$fanpage_args['width'] = 300;
				?>
				<div class="messenger-box module">
					<div class="module-header heading btn-facebook-messenger" title="<?php echo $button_text; ?>">
						<?php $img->output(); ?>
						<label><?php echo $button_text; ?></label>
						<?php
						if ( 'left' == $position || 'right' == $position ) {
							echo '<i class="fa fa-times" aria-hidden="true"></i>';
							$span = new HOCWP_HTML( 'span' );
							$span->add_class( 'facebook-messenger-box-control' );
							$span->set_text( $img );
							$span->output();
						} else {
							echo '<i class="fa fa-angle-up" aria-hidden="true"></i>';
						}
						?>
					</div>
					<div class="module-body">
						<?php hocwp_facebook_page_plugin( $fanpage_args ); ?>
					</div>
				</div>
				<?php
			} else {
				hocwp_facebook_page_plugin( $fanpage_args );
			}
			$widget_html = ob_get_clean();
		}
		$widget_html = apply_filters( 'hocwp_widget_facebook_messenger_html', $widget_html, $args, $instance, $this );
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
		$hide_cta              = (bool) ( isset( $instance['hide_cta'] ) ? $instance['hide_cta'] : $this->args['hide_cta'] );
		$small_header          = (bool) ( isset( $instance['small_header'] ) ? $instance['small_header'] : $this->args['small_header'] );
		$adapt_container_width = (bool) ( isset( $instance['adapt_container_width'] ) ? $instance['adapt_container_width'] : $this->args['adapt_container_width'] );
		$fixed                 = hocwp_get_value_by_key( $instance, 'fixed', $this->args['fixed'] );
		$only_link             = hocwp_get_value_by_key( $instance, 'only_link', $this->args['only_link'] );
		$button_text           = hocwp_get_value_by_key( $instance, 'button_text', $this->args['button_text'] );

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
			'id'    => $this->get_field_id( 'button_text' ),
			'name'  => $this->get_field_name( 'button_text' ),
			'value' => $button_text,
			'label' => __( 'Button text:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input', $args );

		$position   = hocwp_get_value_by_key( $instance, 'position', $this->args['position'] );
		$lists      = $this->args['positions'];
		$all_option = '';
		foreach ( $lists as $lkey => $lvalue ) {
			$all_option .= hocwp_field_get_option( array(
				'value'    => $lkey,
				'text'     => $lvalue,
				'selected' => $position
			) );
		}
		$args = array(
			'id'         => $this->get_field_id( 'position' ),
			'name'       => $this->get_field_name( 'position' ),
			'value'      => $position,
			'all_option' => $all_option,
			'label'      => __( 'Position:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_select', $args );

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

		$args = array(
			'id'    => $this->get_field_id( 'only_link' ),
			'name'  => $this->get_field_name( 'only_link' ),
			'value' => $only_link,
			'label' => __( 'Display only link button to Messenger page?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'fixed' ),
			'name'  => $this->get_field_name( 'fixed' ),
			'value' => $fixed,
			'label' => __( 'Display Messenger box as fixed position?', 'hocwp-theme' )
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
		$instance['button_text']           = hocwp_get_value_by_key( $new_instance, 'button_text', $this->args['button_text'] );
		$instance['position']              = hocwp_get_value_by_key( $new_instance, 'position', $this->args['position'] );
		$instance['hide_cover']            = hocwp_checkbox_post_data_value( $new_instance, 'hide_cover' );
		$instance['show_facepile']         = hocwp_checkbox_post_data_value( $new_instance, 'show_facepile' );
		$instance['hide_cta']              = hocwp_checkbox_post_data_value( $new_instance, 'hide_cta' );
		$instance['small_header']          = hocwp_checkbox_post_data_value( $new_instance, 'small_header' );
		$instance['adapt_container_width'] = hocwp_checkbox_post_data_value( $new_instance, 'adapt_container_width' );
		$instance['fixed']                 = hocwp_checkbox_post_data_value( $new_instance, 'fixed' );
		$instance['only_link']             = hocwp_checkbox_post_data_value( $new_instance, 'only_link' );

		return $instance;
	}
}