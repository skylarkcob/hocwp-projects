<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

class HOCWP_Widget_Tabber extends WP_Widget {
	public $args = array();
	public $admin_args;
	public $instance;

	private function get_defaults() {
		$defaults = array();
		$defaults = apply_filters( 'hocwp_widget_tabber_defaults', $defaults, $this );
		$args     = apply_filters( 'hocwp_widget_tabber_args', array(), $this );
		$args     = wp_parse_args( $args, $defaults );

		return $args;
	}

	public function __construct() {
		$this->args       = $this->get_defaults();
		$this->admin_args = array(
			'id'          => 'hocwp_widget_tabber',
			'name'        => 'HOCWP Tabber',
			'class'       => 'hocwp-tabber-widget',
			'description' => __( 'Display widgets as tabber on sidebar.', 'hocwp' ),
			'width'       => 400
		);
		$this->admin_args = apply_filters( 'hocwp_widget_tabber_admin_args', $this->admin_args, $this );
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

	public function dynamic_sidebar_params( $params ) {
		$widget_id                  = $params[0]['widget_id'];
		$widget_class               = hocwp_build_widget_class( $widget_id );
		$params[0]['before_widget'] = '<div id="' . $widget_id . '" class="tab-item tab-pane ' . $widget_class . '">';
		$params[0]['after_widget']  = '</div>';
		$params[0]['before_title']  = '<a href="#" class="tab-title" data-toggle="tab">';
		$params[0]['after_title']   = '</a>';

		return $params;
	}

	public function widget( $args, $instance ) {
		$this->instance = $instance;
		add_filter( 'dynamic_sidebar_params', array( $this, 'dynamic_sidebar_params' ) );
		$sidebar = hocwp_get_value_by_key( $instance, 'sidebar' );
		hocwp_widget_before( $args, $instance, false );
		if ( empty( $sidebar ) ) {
			echo '<p>' . __( 'Xin vui lòng chọn sidebar chứa các tab widget trước.', 'hocwp' ) . '</p>';
		} elseif ( $args['id'] != $sidebar ) { ?>
			<div class="hocwp-tab-content">
				<ul class="nav nav-tabs list-tab hocwp-tabs"></ul>
				<div class="tab-content hocwp-tab-container">
					<?php
					if ( is_active_sidebar( $sidebar ) ) {
						dynamic_sidebar( $sidebar );
					} else {
						$sidebar_tmp  = hocwp_get_sidebar_by( 'id', $sidebar );
						$sidebar_name = '';
						if ( $sidebar_tmp ) {
							$sidebar_name = $sidebar_tmp['name'];
						}
						?>
						<p><?php printf( __( 'Xin vui lòng kéo các widget cần hiển thị vào sidebar %s.', 'hocwp' ), $sidebar_name ); ?></p>
						<?php
					}
					?>
				</div>
			</div>
		<?php }
		hocwp_widget_after( $args, $instance );
		remove_filter( 'dynamic_sidebar_params', array( $this, 'dynamic_sidebar_params' ) );
	}

	public function form( $instance ) {
		$this->instance = $instance;
		$title          = isset( $instance['title'] ) ? $instance['title'] : '';
		$sidebar        = hocwp_get_value_by_key( $instance, 'sidebar' );
		hocwp_field_widget_before( $this->admin_args['class'] );
		hocwp_widget_field_title( $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), $title );

		$args = array(
			'id'    => $this->get_field_id( 'sidebar' ),
			'name'  => $this->get_field_name( 'sidebar' ),
			'value' => $sidebar,
			'label' => __( 'Sidebar:', 'hocwp' )
		);
		hocwp_widget_field( 'hocwp_field_select_sidebar', $args );

		hocwp_field_widget_after();
	}

	public function update( $new_instance, $old_instance ) {
		$instance            = $old_instance;
		$instance['title']   = strip_tags( hocwp_get_value_by_key( $new_instance, 'title' ) );
		$instance['sidebar'] = hocwp_get_value_by_key( $new_instance, 'sidebar' );

		return $instance;
	}
}