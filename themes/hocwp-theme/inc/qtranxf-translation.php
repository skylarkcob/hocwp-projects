<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

if ( ! hocwp_qtranslate_x_installed() ) {
	return;
}

function hocwp_theme_translate_core_text() {
	$args = array(
		'string'  => 'Get %s',
		'context' => 'HocWP Core'
	);
	hocwp_register_string_language( $args );
	$args['string'] = 'Copied';
	hocwp_register_string_language( $args );
	$args['string'] = 'Copy';
	hocwp_register_string_language( $args );
	$args['string'] = 'Add a Comment';
	hocwp_register_string_language( $args );
	$args['string'] = 'year';
	hocwp_register_string_language( $args );
	$args['string'] = 'years';
	hocwp_register_string_language( $args );
	$args['string'] = 'month';
	hocwp_register_string_language( $args );
	$args['string'] = 'months';
	hocwp_register_string_language( $args );
	$args['string'] = 'week';
	hocwp_register_string_language( $args );
	$args['string'] = 'weeks';
	hocwp_register_string_language( $args );
	$args['string'] = 'day';
	hocwp_register_string_language( $args );
	$args['string'] = 'days';
	hocwp_register_string_language( $args );
	$args['string'] = 'hour';
	hocwp_register_string_language( $args );
	$args['string'] = 'hours';
	hocwp_register_string_language( $args );
	$args['string'] = 'minute';
	hocwp_register_string_language( $args );
	$args['string'] = 'minutes';
	hocwp_register_string_language( $args );
	$args['string'] = 'second';
	hocwp_register_string_language( $args );
	$args['string'] = 'seconds';
	hocwp_register_string_language( $args );
	$args['string'] = 'Captcha Invalid';
	hocwp_register_string_language( $args );
	$args['string'] = 'ERROR:';
	hocwp_register_string_language( $args );
	$args['string'] = 'Please enter a valid captcha.';
	hocwp_register_string_language( $args );
	$args['string'] = 'You are a robot, if not please check JavaScript enabled on your browser.';
	hocwp_register_string_language( $args );
	$args['string'] = 'Username or email';
	hocwp_register_string_language( $args );
	$args['string'] = 'Password';
	hocwp_register_string_language( $args );
	$args['string'] = 'One free account gets you into everything %s.';
	hocwp_register_string_language( $args );
	$args['string'] = 'Password Lost and Found';
	hocwp_register_string_language( $args );
	$args['string'] = 'Lost your password?';
	hocwp_register_string_language( $args );
	$args['string'] = 'Register';
	hocwp_register_string_language( $args );
	$args['string'] = 'Email';
	hocwp_register_string_language( $args );
	$args['string'] = 'Confirm your password';
	hocwp_register_string_language( $args );
	$args['string'] = 'Phone';
	hocwp_register_string_language( $args );
	$args['string'] = 'There was an error occurred, please try again.';
	hocwp_register_string_language( $args );
	$args['string'] = 'Please enter your complete registration information.';
	hocwp_register_string_language( $args );
	$args['string'] = 'The email address is not correct.';
	hocwp_register_string_language( $args );
	$args['string'] = 'Password is incorrect.';
	hocwp_register_string_language( $args );
	$args['string'] = 'Account already exists.';
	hocwp_register_string_language( $args );
	$args['string'] = 'The email address already exists.';
	hocwp_register_string_language( $args );
	$args['string'] = 'The security code is incorrect.';
	hocwp_register_string_language( $args );
	$args['string'] = 'Your account has been successfully created.';
	hocwp_register_string_language( $args );
	$args['string'] = 'Your account has been successfully created.';
	hocwp_register_string_language( $args );
	$args['string'] = 'Registration';
	hocwp_register_string_language( $args );
	$args['string'] = 'Username';
	hocwp_register_string_language( $args );
	$args['string'] = 'Password';
	hocwp_register_string_language( $args );
	$args['string'] = 'Login';
	hocwp_register_string_language( $args );
	$args['string'] = 'Please enter your account name or email address.';
	hocwp_register_string_language( $args );
	$args['string'] = 'The security code is incorrect.';
	hocwp_register_string_language( $args );
	$args['string'] = 'Username or email is not exists.';
	hocwp_register_string_language( $args );
	$args['string'] = 'There was an error occurred, please try again or contact the administrator.';
	hocwp_register_string_language( $args );
	$args['string'] = 'Someone has requested a password reset for the following account:';
	hocwp_register_string_language( $args );
	$args['string'] = 'Username: %s';
	hocwp_register_string_language( $args );
	$args['string'] = 'If this was a mistake, just ignore this email and nothing will happen.';
	hocwp_register_string_language( $args );
	$args['string'] = 'To reset your password, visit the following address:';
	hocwp_register_string_language( $args );
	$args['string'] = 'The email could not be sent. Possible reason: your host may have disabled the mail() function.';
	hocwp_register_string_language( $args );
	$args['string'] = 'Password recovery information has been sent, please check your mailbox.';
	hocwp_register_string_language( $args );
	$args['string'] = 'Password recovery information has been sent, please check your mailbox.';
	hocwp_register_string_language( $args );
	$args['string'] = 'Reset password';
	hocwp_register_string_language( $args );
	$args['string'] = 'Username or Email';
	hocwp_register_string_language( $args );
	$args['string'] = 'Previous';
	hocwp_register_string_language( $args );
	$args['string'] = 'Next';
	hocwp_register_string_language( $args );
	$args['string'] = 'Nothing found!';
}

add_action( 'hocwp_register_string_translation', 'hocwp_theme_translate_core_text' );