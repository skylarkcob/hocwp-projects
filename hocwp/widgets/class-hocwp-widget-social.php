<?php
if(!function_exists('add_filter')) exit;
class HOCWP_Widget_Social extends WP_Widget {
	public $args = array();

	private function get_defaults() {
		$option_socials = hocwp_option_defaults()['social'];
		$defaults = array(
			'admin_width' => 400,
			'order' => $option_socials['order'],
			'option_names' => $option_socials['option_names'],
			'icons' => $option_socials['icons']
		);
		$defaults = apply_filters('hocwp_widget_social_defaults', $defaults);
		$args = apply_filters('hocwp_widget_social_args', array());
		$args = wp_parse_args($args, $defaults);
		return $args;
	}

	public function __construct() {
		$this->args = $this->get_defaults();
		parent::__construct('hocwp_widget_social', 'HOCWP Social',
			array(
				'classname' => 'hocwp-social-widget',
				'description' => __('Display list social icons on sidebar.', 'hocwp'),
			),
			array(
				'width' => $this->args['admin_width']
			)
		);
	}

	public function widget($args, $instance) {
		$order = hocwp_get_value_by_key($instance, 'order', hocwp_get_value_by_key($this->args, 'order'));
		$orders = explode(',', $order);
		$orders = array_map('trim', $orders);
		$orders = hocwp_sanitize_array($orders);
		$option_names = $this->args['option_names'];
		$options = hocwp_get_option('option_social');
		$icons = $this->args['icons'];
		hocwp_widget_before($args, $instance);
		if(hocwp_array_has_value($orders)) {
			foreach($orders as $social) {
				$option_name = hocwp_get_value_by_key($option_names, $social);
				$item = hocwp_get_value_by_key($options, $option_name);
				if(!empty($item)) {
					$icon = '<i class="fa ' . $icons[$social] . '"></i>';
					echo '<a href="' . $item . '" class="link-' . $social . '">' . $icon . '</a>';
				}
			}
		}
		hocwp_widget_after($args, $instance);
	}

	public function form($instance) {
		$title = hocwp_get_value_by_key($instance, 'title');
		$order = hocwp_get_value_by_key($instance, 'order', hocwp_get_value_by_key($this->args, 'order'));

		hocwp_field_widget_before();
		hocwp_widget_field_title($this->get_field_id('title'), $this->get_field_name('title'), $title);

		$args = array(
			'id' => $this->get_field_id('order'),
			'name' => $this->get_field_name('order'),
			'value' => $order,
			'label' => __('Order:', 'hocwp')
		);
		hocwp_widget_field('hocwp_field_input_text', $args);

		hocwp_field_widget_after();
	}

	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags(hocwp_get_value_by_key($new_instance, 'title'));
		$instance['order'] = hocwp_get_value_by_key($new_instance, 'order', hocwp_get_value_by_key($this->args, 'order'));
		return $instance;
	}
}