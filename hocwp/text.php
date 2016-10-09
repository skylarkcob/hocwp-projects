<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

function hocwp_translate_text( $text, $echo = false ) {
	if ( hocwp_qtranslate_x_installed() ) {
		$tmp = $text;
		$mo  = new HOCWP_MO();
		$id  = $mo->get_id( $text );
		if ( hocwp_id_number_valid( $id ) ) {
			$post = get_post( $id );
			if ( is_a( $post, 'WP_Post' ) ) {
				if ( ! empty( $post->post_content ) ) {
					$text = $post->post_content;
				}
			}
		}
		$three_chars = substr( $text, - 3 );
		if ( '[:]' == $three_chars ) {
			$current_language = hocwp_get_current_language();
			$current_language = '[:' . $current_language . ']';
			if ( false !== strpos( $text, $current_language ) ) {
				$text = apply_filters( 'translate_text', $text );
			} else {
				$text = $tmp;
			}
		}
	}
	if ( $echo ) {
		echo $text;
	}

	return $text;
}

function hocwp_text( $vi, $en, $echo = true ) {
	$lang = hocwp_get_language();
	if ( function_exists( 'qtranxf_getLanguage' ) ) {
		$lang = qtranxf_getLanguage();
	}
	if ( 'vi' == $lang ) {
		$text = $vi;
	} else {
		$text = $en;
	}
	$text = apply_filters( 'hocwp_text', $text, $vi, $en, $echo );
	if ( $echo ) {
		echo $text;
	}

	return $text;
}

function hocwp_get_text( $lang, $args = array() ) {
	$text = apply_filters( 'hocwp_get_text', hocwp_get_value_by_key( $args, $lang ), $lang, $args );

	return $text;
}

function hocwp_text_error_default( $lang = 'vi' ) {
	$tmp  = __( 'There was an error occurred, please try again!', 'hocwp-theme' );
	$text = hocwp_get_text( $lang, array(
		'vi' => $tmp,
		'en' => $tmp
	) );

	return apply_filters( 'hocwp_text_error_default', $text, $lang );
}

function hocwp_text_error_email_exists( $lang = 'vi' ) {
	$tmp  = __( 'Email address already exists!', 'hocwp-theme' );
	$text = hocwp_get_text( $lang, array(
		'vi' => $tmp,
		'en' => $tmp
	) );

	return apply_filters( 'hocwp_text_error_email_exists', $text, $lang );
}

function hocwp_text_error_email_not_valid( $lang = 'vi' ) {
	$tmp  = __( 'The email address is not correct!', 'hocwp-theme' );
	$text = hocwp_get_text( $lang, array(
		'vi' => $tmp,
		'en' => $tmp
	) );

	return apply_filters( 'hocwp_text_error_email_not_valid', $text, $lang );
}

function hocwp_text_error_captcha_not_valid( $lang = 'vi' ) {
	$tmp  = __( 'The captcha code is not correct!', 'hocwp-theme' );
	$text = hocwp_get_text( $lang, array(
		'vi' => $tmp,
		'en' => $tmp
	) );

	return apply_filters( 'hocwp_text_error_captcha_not_valid', $text, $lang );
}

function hocwp_text_success_register_and_verify_email( $lang = 'vi' ) {
	$tmp  = __( 'You have successfully registered, please check your email for activation.', 'hocwp-theme' );
	$text = hocwp_get_text( $lang, array(
		'vi' => $tmp,
		'en' => $tmp
	) );

	return apply_filters( 'hocwp_text_success_register_and_verify_email', $text, $lang );
}

function hocwp_text_email_subject_verify_subscription( $lang = 'vi' ) {
	$tmp  = __( 'Activate your Email Subscription to: %s', 'hocwp-theme' );
	$text = hocwp_get_text( $lang, array(
		'vi' => $tmp,
		'en' => $tmp
	) );
	$text = sprintf( $text, get_bloginfo( 'name' ) );

	return apply_filters( 'hocwp_text_email_subject_verify_subscription', $text, $lang );
}

function hocwp_text_change_text_result( $text, $lang, $args ) {
	$lang_option = hocwp_get_language();
	if ( isset( $args, $lang_option ) ) {
		$text = $args[ $lang_option ];
	}

	return $text;
}

add_filter( 'hocwp_get_text', 'hocwp_text_change_text_result', 10, 3 );