<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

global $hocwp_tos_tabs;
$option = new HOCWP_Option( __( 'Permalink', 'hocwp-theme' ), 'hocwp_permalink' );
$option->set_parent_slug( 'hocwp_theme_option' );
$option->add_field(
	array(
		'id'             => 'nice_future_post_permalink',
		'title'          => __( 'Nice Future Post Permalink', 'hocwp-theme' ),
		'field_callback' => 'hocwp_field_input_checkbox',
		'label'          => __( 'Using nice permalink for scheduled posts instead of short link.', 'hocwp-theme' )
	)
);
$option->add_field(
	array(
		'id'             => 'remove_taxonomy_base',
		'title'          => __( 'Remove Taxonomy Base', 'hocwp-theme' ),
		'field_callback' => 'hocwp_field_sortable_taxonomy',
		'connect'        => true,
		'description'    => __( 'Drag and drop the taxonomy into right panel to remove it\'s base slug.', 'hocwp-theme' )
	)
);
$option->set_use_jquery_ui_sortable( true );
$option->set_use_style_and_script( true );
$option->set_parse_options( true );

$option->add_option_tab( $hocwp_tos_tabs );
$option->set_page_header_callback( 'hocwp_theme_option_form_before' );
$option->set_page_footer_callback( 'hocwp_theme_option_form_after' );
$option->set_page_sidebar_callback( 'hocwp_theme_option_sidebar_tab' );
$option->init();
hocwp_option_add_object_to_list( $option );

function hocwp_option_permalink_update( $input ) {
	flush_rewrite_rules();

	return $input;
}

add_action( 'hocwp_sanitize_' . $option->get_option_name_no_prefix() . '_option', 'hocwp_option_permalink_update' );