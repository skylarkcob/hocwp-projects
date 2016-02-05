<?php
if(!function_exists('add_filter')) exit;
class HOCWP_Widget_FeedBurner extends WP_Widget {
	public $args = array();

	private function get_defaults() {
		$defaults = array(
			'admin_width' => 400,
			'button_text' => __('Subscribe', 'hocwp'),
			'placeholder' => __('Enter your email', 'hocwp')
		);
		$defaults = apply_filters('hocwp_widget_feedburner_defaults', $defaults);
		$args = apply_filters('hocwp_widget_feedburner_args', array());
		$args = wp_parse_args($args, $defaults);
		return $args;
	}

	public function __construct() {
		$this->args = $this->get_defaults();
		parent::__construct('hocwp_widget_feedburner', 'HOCWP FeedBurner ',
			array(
				'classname' => 'hocwp-feedburner-widget',
				'description' => __('Display FeedBurner subscription box on sidebar.', 'hocwp'),
			),
			array(
				'width' => $this->args['admin_width']
			)
		);
	}

	public function widget($args, $instance) {
		$feedburner_name = hocwp_get_value_by_key($instance, 'feedburner_name');
		if(!empty($feedburner_name)) {
			$button_text = hocwp_get_value_by_key($instance, 'button_text', hocwp_get_value_by_key($this->args, 'button_text'));
			$placeholder = hocwp_get_value_by_key($instance, '$placeholder', hocwp_get_value_by_key($this->args, '$placeholder'));
			hocwp_widget_before($args, $instance);
			$fb_args = array(
				'button_text' => $button_text,
				'name' => $feedburner_name,
				'placeholder' => $placeholder
			);
			hocwp_feedburner_form($fb_args);
			hocwp_widget_after($args, $instance);
		}
	}

	public function form($instance) {
		$title = isset($instance['title']) ? $instance['title'] : '';
		$feedburner_name = hocwp_get_value_by_key($instance, 'feedburner_name');
		$button_text = hocwp_get_value_by_key($instance, 'button_text', hocwp_get_value_by_key($this->args, 'button_text'));
		$placeholder = hocwp_get_value_by_key($instance, 'placeholder', hocwp_get_value_by_key($this->args, 'placeholder'));
		hocwp_field_widget_before();
		hocwp_widget_field_title($this->get_field_id('title'), $this->get_field_name('title'), $title);

		$args = array(
			'id' => $this->get_field_id('feedburner_name'),
			'name' => $this->get_field_name('feedburner_name'),
			'value' => $feedburner_name,
			'label' => __('Name:', 'hocwp')
		);
		hocwp_widget_field('hocwp_field_input_text', $args);

		$args = array(
			'id' => $this->get_field_id('button_text'),
			'name' => $this->get_field_name('button_text'),
			'value' => $button_text,
			'label' => __('Button text:', 'hocwp')
		);
		hocwp_widget_field('hocwp_field_input_text', $args);

		$args = array(
			'id' => $this->get_field_id('placeholder'),
			'name' => $this->get_field_name('placeholder'),
			'value' => $placeholder,
			'label' => __('Placeholder:', 'hocwp')
		);
		hocwp_widget_field('hocwp_field_input_text', $args);

		hocwp_field_widget_after();
	}

	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags(hocwp_get_value_by_key($new_instance, 'title'));
		$instance['feedburner_name'] = hocwp_get_value_by_key($new_instance, 'feedburner_name');
		$instance['button_text'] = hocwp_get_value_by_key($new_instance, 'button_text', hocwp_get_value_by_key($this->args, 'button_text'));
		$instance['placeholder'] = hocwp_get_value_by_key($new_instance, 'placeholder', hocwp_get_value_by_key($this->args, 'placeholder'));
		return $instance;
	}
}