<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

global $hocwp_tos_tabs;

$option = new HOCWP_Option( __( 'Discussion', 'hocwp-theme' ), 'hocwp_discussion' );
$option->set_parent_slug( 'hocwp_theme_option' );

$option->add_field(
	array(
		'id'             => 'allow_shortcode',
		'title'          => __( 'Shortcode', 'hocwp-theme' ),
		'field_callback' => 'hocwp_field_input_checkbox',
		'label'          => __( 'Allow user to post shortcode in comment.', 'hocwp-theme' )
	)
);
$option->add_section(
	array(
		'id'          => 'comment_form',
		'title'       => __( 'Comment Form', 'hocwp-theme' ),
		'description' => __( 'These options can help you to customize comment form on your site.', 'hocwp-theme' )
	)
);
$field_options = array(
	array(
		'id'           => 'comment_system_default',
		'label'        => __( 'Use WordPress default comment system.', 'hocwp-theme' ),
		'option_value' => 'default'
	),
	array(
		'id'           => 'comment_system_facebook',
		'label'        => __( 'Use Facebook comment system.', 'hocwp-theme' ),
		'option_value' => 'facebook'
	),
	array(
		'id'           => 'comment_system_default_and_facebook',
		'label'        => __( 'Display bold WordPress default comment system and Facebook comment system.', 'hocwp-theme' ),
		'option_value' => 'default_and_facebook'
	),
	array(
		'id'           => 'comment_system_tabs',
		'label'        => __( 'Using multiple comment system as tabs.', 'hocwp-theme' ),
		'option_value' => 'tabs'
	)
);
$option->add_field(
	array(
		'id'             => 'comment_system',
		'title'          => __( 'Comment System', 'hocwp-theme' ),
		'field_callback' => 'hocwp_field_input_radio',
		'options'        => $field_options,
		'section'        => 'comment_form'
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
		'default'        => 'warning',
		'section'        => 'comment_form'
	)
);
$field_options = array(
	array(
		'id'      => 'use_captcha',
		'label'   => __( 'Use captcha to validate human on comment form.', 'hocwp-theme' ),
		'default' => 0
	),
	array(
		'id'      => 'user_no_captcha',
		'label'   => __( 'Disable captcha if user is logged in.', 'hocwp-theme' ),
		'default' => 1
	)
);
$option->add_field(
	array(
		'id'             => 'captcha',
		'title'          => __( 'Captcha', 'hocwp-theme' ),
		'options'        => $field_options,
		'field_callback' => 'hocwp_field_input_checkbox',
		'section'        => 'comment_form'
	)
);

$option->add_option_tab( $hocwp_tos_tabs );
$option->set_page_header_callback( 'hocwp_theme_option_form_before' );
$option->set_page_footer_callback( 'hocwp_theme_option_form_after' );
$option->set_page_sidebar_callback( 'hocwp_theme_option_sidebar_tab' );

$option->init();

hocwp_option_add_object_to_list( $option );