<?php
if(!function_exists('add_filter')) exit;
class HOCWP_Widget_Subscribe extends WP_Widget {
	public $args = array();
	public $admin_args;

	private function get_defaults() {
		$defaults = array(
			'button_text' => __('Subscribe', 'hocwp'),
			'description' => '',
			'desc_position' => 'before',
			'desc_positions' => array(
				'before' => __('Before email field', 'hocwp'),
				'after' => __('After email field', 'hocwp')
			),
			'fields' => array(
				'email' => array(
					'label' => __('Email', 'hocwp'),
					'placeholder' => __('Enter your email', 'hocwp'),
					'required' => true
				),
				'name' => array(
					'label' => __('Name', 'hocwp'),
					'placeholder' => __('Enter your name', 'hocwp'),
					'required' => false
				),
				'phone' => array(
					'label' => __('Phone', 'hocwp'),
					'placeholder' => __('Enter your phone number', 'hocwp'),
					'required' => false
				)
			),
			'captcha' => false,
			'captcha_label' => __('Captcha', 'hocwp'),
			'captcha_placeholder' => __('Enter captcha code', 'hocwp'),
			'register' => false
		);
		$defaults = apply_filters('hocwp_widget_subscribe_defaults', $defaults);
		$args = apply_filters('hocwp_widget_subscribe_args', array());
		$args = wp_parse_args($args, $defaults);
		return $args;
	}

	public function __construct() {
		$this->args = $this->get_defaults();
		$this->admin_args = array(
			'id' => 'hocwp_widget_subscribe',
			'name' => 'HOCWP Subscribe',
			'class' => 'hocwp-subscribe-widget',
			'description' => __('Allow subscribe as user.', 'hocwp'),
			'width' => 400
		);
		$this->admin_args = apply_filters('hocwp_widget_subscribe_admin_args', $this->admin_args);
		parent::__construct($this->admin_args['id'], $this->admin_args['name'],
			array(
				'classname' => $this->admin_args['class'],
				'description' => $this->admin_args['description'],
			),
			array(
				'width' => $this->admin_args['width']
			)
		);
		add_filter('hocwp_allow_user_subscribe', '__return_true');
		add_action('wp_ajax_hocwp_widget_subscribe', array($this, 'hocwp_widget_subscribe_ajax_callback'));
		add_action('wp_ajax_nopriv_hocwp_widget_subscribe', array($this, 'hocwp_widget_subscribe_ajax_callback'));
	}

	function hocwp_widget_subscribe_ajax_callback() {
		$use_captcha = (bool)hocwp_get_method_value('use_captcha');
		$captcha_code = hocwp_get_method_value('captcha');
		$email = hocwp_get_method_value('email');
		$name = hocwp_get_method_value('name');
		$phone = hocwp_get_method_value('phone');
		$register = (bool)hocwp_get_method_value('register');
		$result = array(
			'success' => false,
			'message' => hocwp_build_message(hocwp_text_error_default(), 'danger')
		);
		$captcha_valid = true;
		if($use_captcha) {
			$captcha = new HOCWP_Captcha();
			$captcha_valid = $captcha->check($captcha_code);
		}
		if($captcha_valid) {
			if(is_email($email)) {
				if($register && email_exists($email)) {
					$result['message'] = hocwp_build_message(hocwp_text_error_email_exists(), 'danger');
				} else {
					$query = hocwp_get_post_by_meta('subscriber_email', $email, array('post_type' => 'hocwp_subscriber'));
					if($query->have_posts()) {
						$result['message'] = hocwp_build_message(hocwp_text_error_email_exists(), 'danger');
					} else {
						$post_title = '';
						if(!empty($name)) {
							$post_title .= $name;
						}
						if(empty($post_title)) {
							$post_title = $email;
						} else {
							$post_title .= ' - ' . $email;
						}
						$post_data = array(
							'post_type' => 'hocwp_subscriber',
							'post_title' => $post_title,
							'post_status' => 'publish'
						);
						$post_id = hocwp_insert_post($post_data);
						if(hocwp_id_number_valid($post_id)) {
							update_post_meta($post_id, 'subscriber_name', $name);
							update_post_meta($post_id, 'subscriber_email', $email);
							update_post_meta($post_id, 'subscriber_phone', $phone);
							update_post_meta($post_id, 'subscriber_verified', 0);
							$active_key = hocwp_generate_reset_key();
							update_post_meta($post_id, 'subscriber_active_key', $active_key);
							if($register) {
								$password = wp_generate_password();
								$user_data = array(
									'username' => $email,
									'email' => $email,
									'password' => $password
								);
								$user_id = hocwp_add_user($user_data);
								if(hocwp_id_number_valid($user_id)) {
									wp_send_new_user_notifications($user_id);
									update_post_meta($post_id, 'subscriber_user', $user_id);
									update_user_meta($user_id, 'subscriber_id', $post_id);
								}
							}
							$verify_link = hocwp_generate_verify_link($active_key);
							hocwp_send_mail_verify_email_subscription(hocwp_text_email_subject_verify_subscription(), $email, $verify_link);
							$result['success'] = true;
							$result['message'] = hocwp_build_message(hocwp_text_success_register_and_verify_email(), 'success');
						}
					}
				}
			} else {
				$result['message'] = hocwp_build_message(hocwp_text_error_email_not_valid(), 'danger');
			}
		} else {
			$result['message'] = hocwp_build_message(hocwp_text_error_captcha_not_valid(), 'danger');
		}
		wp_send_json($result);
	}

	public function widget($args, $instance) {
		$register = hocwp_get_value_by_key($instance, 'register', hocwp_get_value_by_key($this->args, 'register'));
		$button_text = hocwp_get_value_by_key($instance, 'button_text', hocwp_get_value_by_key($this->args, 'button_text'));
		$captcha = (bool)hocwp_get_value_by_key($instance, 'captcha', hocwp_get_value_by_key($this->args, 'captcha'));
		if($captcha) {
			add_filter('hocwp_use_session', '__return_true');
		}
		$description = hocwp_get_value_by_key($instance, 'description', hocwp_get_value_by_key($this->args, 'description'));
		$desc_position = hocwp_get_value_by_key($instance, 'desc_position', hocwp_get_value_by_key($this->args, 'desc_position'));
		$fields = $this->get_value_fields($instance);
		$all_fields = explode(',', $fields);
		hocwp_widget_before($args, $instance);
		ob_start();
		?>
		<form class="subscribe-form hocwp-subscribe-form" method="post" data-captcha="<?php echo hocwp_bool_to_int($captcha); ?>" data-register="<?php echo hocwp_bool_to_int($register); ?>">
			<?php
			echo '<div class="messages"></div>';
			if(!empty($description) && 'before' == $desc_position) {
				echo '<p class="description">' . $description . '</p>';
			}
			foreach($all_fields as $field_name) {
				$field = hocwp_get_value_by_key($this->args['fields'], $field_name);
				if(hocwp_array_has_value($field)) {
					$label = $this->get_value_field($instance, $field_name, 'label');
					$placeholder = $this->get_value_field($instance, $field_name, 'placeholder');
					$required = $this->get_value_field($instance, $field_name, 'required');
					$class = hocwp_sanitize_html_class($field_name);
					$field_args = array(
						'id' => $this->get_field_id('subscribe_' . $field_name),
						'name' => $this->get_field_name('subscribe_' . $field_name),
						'value' => '',
						'label' => $label,
						'placeholder' => $placeholder,
						'required' => $required,
						'class' => 'form-control input-' . $class,
						'before' => '<div class="form-group field-' . $class . '">',
						'after' => '</div>'
					);
					hocwp_field_input($field_args);
				}
			}
			if(!empty($description) && 'after' == $desc_position) {
				echo '<p class="description">' . $description . '</p>';
			}
			if($captcha) {
				$captcha_label = hocwp_get_value_by_key($instance, 'captcha_label', hocwp_get_value_by_key($this->args, 'captcha_label'));
				$captcha_placeholder = hocwp_get_value_by_key($instance, 'captcha_placeholder', hocwp_get_value_by_key($this->args, 'captcha_placeholder'));
				$field_args = array(
					'id' => $this->get_field_id('captcha'),
					'name' => $this->get_field_name('captcha'),
					'input_width' => '100%',
					'class' => 'form-control',
					'label' => $captcha_label,
					'placeholder' => $captcha_placeholder,
					'before' => '<div class="form-group field-captcha">',
					'after' => '</div>'
				);
				hocwp_field_captcha($field_args);
			}
			$field_args = array(
				'type' => 'submit',
				'name' => 'submit',
				'value' => $button_text,
				'class' => 'form-control',
				'before' => '<div class="form-group field-submit">',
				'after' => '</div>'
			);
			hocwp_field_input($field_args);
			hocwp_loading_image(array('name' => 'icon-loading-long.gif'));
			?>
		</form>
		<?php
		$widget_html = ob_get_clean();
		$widget_html = apply_filters('hocwp_widget_subscribe_html', $widget_html, $instance, $args, $this);
		echo $widget_html;
		hocwp_widget_after($args, $instance);
	}

	public function get_value_fields($instance) {
		$fields = hocwp_get_value_by_key($instance, 'fields', hocwp_get_value_by_key($this->args, 'fields'));
		if(hocwp_array_has_value($fields)) {
			$tmp = '';
			foreach($fields as $field_name => $field) {
				$tmp .= $field_name . ',';
			}
			$tmp = trim($tmp, ',');
			$fields = $tmp;
		}
		return $fields;
	}

	public function get_value_field($instance, $field_name, $key) {
		$real_name = 'subscribe_' . $field_name . '_' . $key;
		return hocwp_get_value_by_key($instance, $real_name, hocwp_get_value_by_key($this->args['fields'][$field_name], $key));
	}

	public function form($instance) {
		$title = isset($instance['title']) ? $instance['title'] : '';
		$register = hocwp_get_value_by_key($instance, 'register', hocwp_get_value_by_key($this->args, 'register'));
		$button_text = hocwp_get_value_by_key($instance, 'button_text', hocwp_get_value_by_key($this->args, 'button_text'));
		$captcha = hocwp_get_value_by_key($instance, 'captcha', hocwp_get_value_by_key($this->args, 'captcha'));
		$captcha_label = hocwp_get_value_by_key($instance, 'captcha_label', hocwp_get_value_by_key($this->args, 'captcha_label'));
		$captcha_placeholder = hocwp_get_value_by_key($instance, 'captcha_placeholder', hocwp_get_value_by_key($this->args, 'captcha_placeholder'));
		$description = hocwp_get_value_by_key($instance, 'description', hocwp_get_value_by_key($this->args, 'description'));
		$desc_position = hocwp_get_value_by_key($instance, 'desc_position', hocwp_get_value_by_key($this->args, 'desc_position'));
		$fields = $this->get_value_fields($instance);
		$all_fields = explode(',', $fields);
		hocwp_field_widget_before($this->admin_args['class']);
		hocwp_widget_field_title($this->get_field_id('title'), $this->get_field_name('title'), $title);

		$args = array(
			'id' => $this->get_field_id('fields'),
			'name' => $this->get_field_name('fields'),
			'value' => $fields,
			'label' => __('Fields:', 'hocwp')
		);
		hocwp_widget_field('hocwp_field_input_text', $args);

		foreach($all_fields as $field_name) {
			$field = hocwp_get_value_by_key($this->args['fields'], $field_name);
			if(hocwp_array_has_value($field)) {
				foreach($field as $key => $data) {
					$field_label = hocwp_uppercase_first_char($field_name);
					$field_label .= ' ' . strtolower($key);
					$field_callback = 'hocwp_field_input_text';
					if('required' == $key) {
						$field_label .= '?';
						$field_callback = 'hocwp_field_input_checkbox';
					} else {
						$field_label .= ':';
					}
					$args = array(
						'id' => $this->get_field_id('subscribe_' . $field_name . '_' . $key),
						'name' => $this->get_field_name('subscribe_' . $field_name . '_' . $key),
						'value' => $this->get_value_field($instance, $field_name, $key),
						'label' => $field_label
					);
					hocwp_widget_field($field_callback, $args);
				}
			}
		}

		$args = array(
			'id' => $this->get_field_id('button_text'),
			'name' => $this->get_field_name('button_text'),
			'value' => $button_text,
			'label' => __('Button text:', 'hocwp')
		);
		hocwp_widget_field('hocwp_field_input_text', $args);

		$args = array(
			'id' => $this->get_field_id('description'),
			'name' => $this->get_field_name('description'),
			'value' => $description,
			'label' => __('Description:', 'hocwp')
		);
		hocwp_widget_field('hocwp_field_textarea', $args);

		$lists = $this->args['desc_positions'];
		$all_option = '';
		foreach($lists as $lkey => $lvalue) {
			$all_option .= hocwp_field_get_option(array('value' => $lkey, 'text' => $lvalue, 'selected' => $desc_position));
		}
		$args = array(
			'id' => $this->get_field_id('desc_position'),
			'name' => $this->get_field_name('desc_position'),
			'value' => $desc_position,
			'all_option' => $all_option,
			'label' => __('Description position:', 'hocwp'),
			'class' => 'desc-position'
		);
		hocwp_widget_field('hocwp_field_select', $args);

		if($captcha) {
			$args = array(
				'id' => $this->get_field_id('captcha_label'),
				'name' => $this->get_field_name('captcha_label'),
				'value' => $captcha_label,
				'label' => __('Captcha label:', 'hocwp')
			);
			hocwp_widget_field('hocwp_field_input_text', $args);
			$args = array(
				'id' => $this->get_field_id('captcha_placeholder'),
				'name' => $this->get_field_name('captcha_placeholder'),
				'value' => $captcha_placeholder,
				'label' => __('Captcha placeholder:', 'hocwp')
			);
			hocwp_widget_field('hocwp_field_input_text', $args);
		}

		$args = array(
			'id' => $this->get_field_id('captcha'),
			'name' => $this->get_field_name('captcha'),
			'value' => $captcha,
			'label' => __('Using captcha in form?', 'hocwp')
		);
		hocwp_widget_field('hocwp_field_input_checkbox', $args);

		$args = array(
			'id' => $this->get_field_id('register'),
			'name' => $this->get_field_name('register'),
			'value' => $register,
			'label' => __('Add suscriber as a user?', 'hocwp')
		);
		hocwp_widget_field('hocwp_field_input_checkbox', $args);

		hocwp_field_widget_after();
	}

	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags(hocwp_get_value_by_key($new_instance, 'title'));
		$instance['fields'] = $this->get_value_fields($new_instance);
		$all_fields = explode(',', $instance['fields']);
		foreach($all_fields as $field_name) {
			$field = hocwp_get_value_by_key($this->args['fields'], $field_name);
			if(hocwp_array_has_value($field)) {
				foreach($field as $key => $data) {
					$real_name = 'subscribe_' . $field_name . '_' . $key;
					$instance[$real_name] = $this->get_value_field($new_instance, $field_name, $key);
				}
			}
		}
		$instance['button_text'] = hocwp_get_value_by_key($new_instance, 'button_text', hocwp_get_value_by_key($this->args, 'button_text'));
		$instance['description'] = hocwp_get_value_by_key($new_instance, 'description', hocwp_get_value_by_key($this->args, 'description'));
		$instance['desc_position'] = hocwp_get_value_by_key($new_instance, 'desc_position', $this->args['desc_position']);
		$instance['captcha'] = hocwp_checkbox_post_data_value($new_instance, 'captcha', hocwp_get_value_by_key($this->args, 'captcha'));
		$instance['captcha_label'] = hocwp_get_value_by_key($new_instance, 'captcha_label', hocwp_get_value_by_key($this->args, 'captcha_label'));
		$instance['captcha_placeholder'] = hocwp_get_value_by_key($new_instance, 'captcha_placeholder', hocwp_get_value_by_key($this->args, 'captcha_placeholder'));
		$instance['register'] = hocwp_checkbox_post_data_value($new_instance, 'register', hocwp_get_value_by_key($this->args, 'register'));
		return $instance;
	}
}