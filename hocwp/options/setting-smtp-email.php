<?php
if(!function_exists('add_filter')) exit;
$parent_slug = 'options-general.php';

$option_smtp = new HOCWP_Option(__('SMTP Email', 'hocwp'), 'hocwp_option_smtp_email');
$option_smtp->set_parent_slug($parent_slug);
$option_smtp->add_section(array('id' => 'smtp_option', 'title' => __('SMTP Options', 'hocwp'), 'description' => __('These options only apply if you have chosen to send mail by SMTP above.', 'hocwp')));
$option_smtp->add_section(array('id' => 'testing', 'title' => __('Configuration Testing', 'hocwp'), 'description' => __('If you do not feel very confident with the above configuration, you can send a test mail to know the results.', 'hocwp')));
$option_smtp->add_field(array('id' => 'mail_from', 'title' => __('From Email', 'hocwp'), 'description' => __('You can specify the email address that emails should be sent from. If you leave this blank, the default email will be used.', 'hocwp')));
$option_smtp->add_field(array('id' => 'mail_from_name', 'title' => __('From Name', 'hocwp'), 'description' => __('You can specify the name that emails should be sent from. If you leave this blank, the emails will be sent from WordPress.', 'hocwp')));
$field_options = array(
	array(
		'id' => 'mailer_smtp',
		'label' => __('Send all WordPress emails via SMTP.', 'hocwp'),
		'option_value' => 'smtp'
	),
	array(
		'id' => 'mailer_mail',
		'label' => __('Use the PHP mail() function to send emails.', 'hocwp'),
		'option_value' => 'mail'
	)
);
$option_smtp->add_field(array('id' => 'mailer', 'title' => __('Mailer', 'hocwp'), 'field_callback' => 'hocwp_field_input_radio', 'options' => $field_options));
$field_options = array(
	array(
		'id' => 'mail_set_return_path',
		'label' => __('Set the return-path to match the From Email.', 'hocwp')
	)
);
$option_smtp->add_field(array('id' => 'mail_set_return_path', 'title' => __('Return Path', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'options' => $field_options));
$option_smtp->add_field(array('id' => 'smtp_host', 'title' => __('SMTP Host', 'hocwp'), 'default' => 'localhost', 'section' => 'smtp_option'));
$option_smtp->add_field(array('id' => 'smtp_port', 'title' => __('SMTP Port', 'hocwp'), 'default' => 25, 'section' => 'smtp_option'));
$field_options = array(
	array(
		'id' => 'smtp_ssl_none',
		'label' => __('No encryption.', 'hocwp'),
		'option_value' => 'none'
	),
	array(
		'id' => 'smtp_ssl_ssl',
		'label' => __('Use SSL encryption.', 'hocwp'),
		'option_value' => 'ssl'
	),
	array(
		'id' => 'smtp_ssl_tls',
		'label' => __('Use TLS encryption. This is not the same as STARTTLS. For most servers SSL is the recommended option.', 'hocwp'),
		'option_value' => 'tls'
	)
);
$option_smtp->add_field(array('id' => 'smtp_ssl', 'title' => __('Encryption', 'hocwp'), 'field_callback' => 'hocwp_field_input_radio', 'options' => $field_options, 'section' => 'smtp_option'));
$field_options = array(
	array(
		'id' => 'smtp_auth_true',
		'label' => __('Yes: Use SMTP authentication.', 'hocwp'),
		'option_value' => 'true'
	),
	array(
		'id' => 'smtp_auth_false',
		'label' => __('No: Do not use SMTP authentication.', 'hocwp'),
		'option_value' => 'false'
	)
);
$option_smtp->add_field(array('id' => 'smtp_auth', 'title' => __('Authentication', 'hocwp'), 'field_callback' => 'hocwp_field_input_radio', 'options' => $field_options, 'section' => 'smtp_option'));
$option_smtp->add_field(array('id' => 'smtp_user', 'title' => __('Username', 'hocwp'), 'section' => 'smtp_option'));
$option_smtp->add_field(array('id' => 'smtp_pass', 'title' => __('Password', 'hocwp'), 'section' => 'smtp_option', 'type' => 'password'));
$option_smtp->add_field(array('id' => 'to_email', 'title' => __('To', 'hocwp'), 'section' => 'testing', 'description' => __('Type an email address here and then click Send Test to generate a test email.', 'hocwp'), 'type' => 'email'));
$option_smtp->init();
hocwp_option_add_object_to_list($option_smtp);

function hocwp_sanitize_option_smtp_mail($input) {
	if(isset($input['to_email'])) {
		if(is_email($input['to_email'])) {
			set_transient('hocwp_test_smtp_email', $input['to_email']);
		}
	}
	unset($input['to_email']);
	return $input;
}
add_filter('hocwp_sanitize_option_' . $option_smtp->get_option_name_no_prefix(), 'hocwp_sanitize_option_smtp_mail');

function hocwp_option_smtp_mail_update($input) {
	return $input;
}
add_action('hocwp_sanitize_' . $option_smtp->get_option_name_no_prefix() . '_option', 'hocwp_option_smtp_mail_update');

function hocwp_option_smtp_email_testing() {
	if(false !== ($email = get_transient('hocwp_test_smtp_email'))) {
		if(is_email($email)) {
			unset($_GET['settings-updated']);
			$test_message = hocwp_mail_test_smtp_setting($email);
			set_transient('hocwp_test_smtp_email_message', $test_message);
			delete_transient('hocwp_test_smtp_email');
			add_action('admin_notices', 'hocwp_option_smtp_email_testing_message');
			unset($phpmailer);
		}
	}
}
add_action('admin_init', 'hocwp_option_smtp_email_testing');

function hocwp_option_smtp_email_testing_message() {
	if(false !== ($message = get_transient('hocwp_test_smtp_email_message'))) {
		hocwp_admin_notice(array('text' => $message));
		delete_transient('hocwp_test_smtp_email_message');
	}
}