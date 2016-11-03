<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}
function hocwp_theme_switcher_enabled() {
	return apply_filters( 'hocwp_theme_switcher_enabled', defined( 'HOCWP_THEME_SWITCHER_VERSION' ) );
}

function hocwp_theme_switcher_default_mobile_theme_name() {
	$name = hocwp_option_get_value( 'theme_switcher', 'mobile_theme' );
	$name = apply_filters( 'hocwp_mobile_theme', $name );

	return $name;
}

function hocwp_theme_switcher_get_mobile_theme_name() {
	$name = hocwp_theme_switcher_default_mobile_theme_name();

	return $name;
}

function hocwp_theme_switcher_to_mobile_control( $name ) {
	$mobile_theme = hocwp_theme_switcher_get_mobile_theme_name();
	if ( ! empty( $mobile_theme ) ) {
		$name = $mobile_theme;
	}

	return $name;
}

function hocwp_theme_switcher_control( $name ) {
	$theme = hocwp_theme_switcher_get_current_theme();
	if ( ! empty( $theme ) ) {
		$name = $theme;
	}

	return $name;
}

function hocwp_theme_switcher_get_current_theme() {
	$theme = hocwp_get_method_value( 'theme', 'get' );
	if ( empty( $theme ) ) {
		if ( is_user_logged_in() ) {
			$theme = get_option( 'hocwp_user_' . get_current_user_id() . '_theme' );
			if ( empty( $theme ) ) {
				$theme = isset( $_SESSION['hocwp_current_theme'] ) ? $_SESSION['hocwp_current_theme'] : '';
				if ( empty( $theme ) ) {
					$theme = isset( $_COOKIE['hocwp_current_theme'] ) ? $_COOKIE['hocwp_current_theme'] : '';
				}
			}
		} else {
			$theme = isset( $_SESSION['hocwp_current_theme'] ) ? $_SESSION['hocwp_current_theme'] : '';
			if ( empty( $theme ) ) {
				$theme = isset( $_COOKIE['hocwp_current_theme'] ) ? $_COOKIE['hocwp_current_theme'] : '';
			}
		}
	}

	return apply_filters( 'hocwp_theme_switcher_current_theme', $theme );
}