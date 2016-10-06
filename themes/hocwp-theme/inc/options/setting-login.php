<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

global $hocwp_tos_tabs;
$parent_slug = 'hocwp_theme_option';

$option = new HOCWP_Option( __( 'Login settings', 'hocwp-theme' ), 'hocwp_user_login' );
$option->set_parent_slug( $parent_slug );
$option->set_use_style_and_script( true );
$option->set_use_media_upload( true );

$option->add_field(
	array(
		'id'             => 'logo',
		'title'          => __( 'Logo', 'hocwp-theme' ),
		'field_callback' => 'hocwp_field_media_upload'
	)
);
$option->add_field(
	array(
		'id'             => 'login_with_email',
		'title'          => __( 'Login With Email', 'hocwp-theme' ),
		'label'          => __( 'Allow user to login with registered email address?', 'hocwp-theme' ),
		'field_callback' => 'hocwp_field_input_checkbox'
	)
);
$option->add_field(
	array(
		'id'             => 'users_can_register',
		'title'          => __( 'Membership', 'hocwp-theme' ),
		'label'          => __( 'Anyone can register', 'hocwp-theme' ),
		'field_callback' => 'hocwp_field_input_checkbox',
		'value'          => hocwp_users_can_register()
	)
);
$option->add_field(
	array(
		'id'             => 'use_captcha',
		'title'          => __( 'Captcha', 'hocwp-theme' ),
		'label'          => __( 'Protect your site against bots by using captcha', 'hocwp-theme' ),
		'field_callback' => 'hocwp_field_input_checkbox'
	)
);
$option->add_field(
	array(
		'id'             => 'button_style',
		'title'          => __( 'Button Style', 'hocwp-theme' ),
		'field_callback' => 'hocwp_field_select',
		'field_args'     => array(
			'options' => hocwp_bootstrap_color_select_options()
		),
		'default'        => 'warning'
	)
);

$option->add_section(
	array(
		'id'          => 'social_login',
		'title'       => __( 'Social Login', 'hocwp-theme' ),
		'description' => __( 'Allow user to login with social account.', 'hocwp-theme' )
	)
);
$option->add_field(
	array(
		'id'             => 'login_with_facebook',
		'title'          => __( 'Facebook', 'hocwp-theme' ),
		'label'          => __( 'Check here to allow user connects with Facebook account.', 'hocwp-theme' ),
		'field_callback' => 'hocwp_field_input_checkbox',
		'section'        => 'social_login'
	)
);
$option->add_field(
	array(
		'id'             => 'login_with_google',
		'title'          => __( 'Google', 'hocwp-theme' ),
		'label'          => __( 'Check here to allow user connects with Google account.', 'hocwp-theme' ),
		'field_callback' => 'hocwp_field_input_checkbox',
		'section'        => 'social_login'
	)
);

$option->add_option_tab( $hocwp_tos_tabs );
$option->set_page_header_callback( 'hocwp_theme_option_form_before' );
$option->set_page_footer_callback( 'hocwp_theme_option_form_after' );
$option->set_page_sidebar_callback( 'hocwp_theme_option_sidebar_tab' );
$option->init();
hocwp_option_add_object_to_list( $option );

function hocwp_users_can_register() {
	$result = (bool) get_option( 'users_can_register' );

	return $result;
}

function hocwp_option_user_login_update( $input ) {
	$users_can_register = hocwp_checkbox_post_data_value( $input, 'users_can_register' );
	if ( (bool) $users_can_register ) {
		update_option( 'users_can_register', 1 );
	} else {
		update_option( 'users_can_register', 0 );
	}
}

add_action( 'hocwp_sanitize_' . $option->get_option_name_no_prefix() . '_option', 'hocwp_option_user_login_update' );

function hocwp_allow_user_login_with_email() {
	$options = get_option( 'hocwp_user_login' );

	return apply_filters( 'hocwp_allow_user_login_with_email', (bool) hocwp_get_value_by_key( $options, 'login_with_email' ) );
}