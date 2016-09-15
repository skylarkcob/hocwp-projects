<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

class HOCWP_Widget_Statistics extends WP_Widget {
	public $args = array();
	public $admin_args;
	public $instance;

	private function get_defaults() {
		$defaults = array(
			'nofollow' => 0
		);
		$defaults = apply_filters( 'hocwp_widget_statistics_defaults', $defaults, $this );
		$args     = apply_filters( 'hocwp_widget_statistics_args', array(), $this );
		$args     = wp_parse_args( $args, $defaults );

		return $args;
	}

	public function __construct() {
		$this->args       = $this->get_defaults();
		$this->admin_args = array(
			'id'          => 'hocwp_widget_statistics',
			'name'        => 'HOCWP Statistics',
			'class'       => 'hocwp-statistics-widget',
			'description' => __( 'Display statistics on sidebar.', 'hocwp-theme' ),
			'width'       => 400
		);
		$this->admin_args = apply_filters( 'hocwp_widget_statistics_admin_args', $this->admin_args, $this );
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
		hocwp_widget_before( $args, $instance );
		$total_text  = 'Tổng truy cập:';
		$today_text  = 'Truy cập hôm nay:';
		$online_text = 'Đang truy cập:';
		$lang        = hocwp_get_language();
		if ( 'vi' != $lang ) {
			$total_text  = __( 'Total:', 'hocwp-theme' );
			$today_text  = __( 'Visit Today:', 'hocwp-theme' );
			$online_text = __( 'Online:', 'hocwp-theme' );
		}
		ob_start();
		?>
		<ul class="list-unstyled">
			<li><p><?php echo $total_text; ?> <span class="count"><?php echo hocwp_statistics_total(); ?></span></p>
			</li>
			<li><p><?php echo $today_text; ?> <span class="count"><?php echo hocwp_statistics_today(); ?></span></p>
			</li>
			<li><p><?php echo $online_text; ?> <span class="count"><?php echo hocwp_statistics_online(); ?></span></p>
			</li>
		</ul>
		<?php
		$html        = ob_get_clean();
		$widget_html = apply_filters( 'hocwp_widget_statistics_html', $html, $args, $instance, $this );
		echo $widget_html;
		hocwp_widget_after( $args, $instance );
	}

	public function form( $instance ) {
		$this->instance = $instance;
		$title          = isset( $instance['title'] ) ? $instance['title'] : '';
		hocwp_field_widget_before( $this->admin_args['class'] );
		hocwp_widget_field_title( $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), $title );
		hocwp_field_widget_after();
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( hocwp_get_value_by_key( $new_instance, 'title' ) );

		return $instance;
	}
}