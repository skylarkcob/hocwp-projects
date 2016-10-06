<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

function hocwp_option_theme_custom_defaults() {
	$alls     = hocwp_option_defaults();
	$defaults = hocwp_get_value_by_key( $alls, 'theme_custom' );
	if ( ! hocwp_array_has_value( $defaults ) ) {
		$defaults = array(
			'background_lazyload' => 0
		);
	}

	return apply_filters( 'hocwp_option_theme_custom_defaults', $defaults );
}

function hocwp_option_theme_custom() {
	$defaults = hocwp_option_theme_custom_defaults();
	$options  = get_option( 'hocwp_theme_custom' );
	$options  = wp_parse_args( $options, $defaults );

	return apply_filters( 'hocwp_option_theme_custom', $options );
}

global $hocwp_tos_tabs;
$parent_slug = 'hocwp_theme_option';
$defaults    = hocwp_option_defaults();

$option  = new HOCWP_Option( __( 'Theme Custom', 'hocwp-theme' ), 'hocwp_theme_custom' );
$options = $option->get();
$option->set_parent_slug( $parent_slug );
$option->add_section( array(
	'id'          => 'music',
	'title'       => __( 'Music', 'hocwp-theme' ),
	'description' => __( 'Play music on your site as background music.', 'hocwp-theme' )
) );
$option->add_field( array(
	'id'             => 'background_music',
	'title'          => __( 'Embed Code', 'hocwp-theme' ),
	'class'          => 'widefat',
	'row'            => 3,
	'field_callback' => 'hocwp_field_textarea',
	'section'        => 'music'
) );
$lists      = hocwp_get_value_by_key( $defaults, array( 'theme_custom', 'background_music', 'play_ons' ) );
$play_on    = hocwp_get_value_by_key( $defaults, array( 'theme_custom', 'background_music', 'play_on' ) );
$all_option = '';
$value      = hocwp_get_value_by_key( $options, 'play_on' );

if ( empty( $value ) ) {
	$value = $play_on;
}

if ( hocwp_array_has_value( $lists ) ) {
	foreach ( $lists as $key => $item ) {
		$tmp_option = hocwp_field_get_option( array( 'value' => $key, 'text' => $item, 'selected' => $value ) );
		$all_option .= $tmp_option;
	}
}

$option->add_field( array(
	'id'             => 'play_on',
	'title'          => __( 'Play On', 'hocwp-theme' ),
	'field_callback' => 'hocwp_field_select',
	'section'        => 'music',
	'all_option'     => $all_option,
	'default'        => $play_on
) );
$option->add_section( array(
	'id'          => 'background',
	'title'       => __( 'Background', 'hocwp-theme' ),
	'description' => __( 'Custom background of your site.', 'hocwp-theme' )
) );
$option->add_field( array(
	'id'             => 'background_image',
	'title'          => __( 'Image', 'hocwp-theme' ),
	'field_callback' => 'hocwp_field_media_upload',
	'section'        => 'background'
) );
$option->add_field( array(
	'id'      => 'background_size',
	'title'   => __( 'Size', 'hocwp-theme' ),
	'section' => 'background'
) );
$option->add_field( array(
	'id'             => 'background_repeat',
	'title'          => __( 'Repeat', 'hocwp-theme' ),
	'label'          => __( 'Check here if you want background to be repeated.', 'hocwp-theme' ),
	'field_callback' => 'hocwp_field_input_checkbox',
	'section'        => 'background'
) );
$option->add_field( array(
	'id'      => 'background_position',
	'title'   => __( 'Position', 'hocwp-theme' ),
	'section' => 'background'
) );
$option->add_field( array(
	'id'             => 'background_color',
	'title'          => __( 'Color', 'hocwp-theme' ),
	'field_callback' => 'hocwp_field_color_picker',
	'section'        => 'background'
) );
$option->add_field( array(
	'id'      => 'background_attachment',
	'title'   => __( 'Attachment', 'hocwp-theme' ),
	'section' => 'background'
) );
$option->add_field( array(
	'id'             => 'background_lazyload',
	'title'          => __( 'Lazy Load', 'hocwp-theme' ),
	'label'          => __( 'Check here if you want to load background with Lazy Load.', 'hocwp-theme' ),
	'field_callback' => 'hocwp_field_input_checkbox',
	'default'        => 0,
	'section'        => 'background'
) );

$option->set_use_color_picker( true );
$option->set_use_media_upload( true );
$option->set_use_style_and_script( true );
$option->add_option_tab( $hocwp_tos_tabs );

$option->set_page_header_callback( 'hocwp_theme_option_form_before' );
$option->set_page_footer_callback( 'hocwp_theme_option_form_after' );
$option->set_page_sidebar_callback( 'hocwp_theme_option_sidebar_tab' );
$option->init();
hocwp_option_add_object_to_list( $option );