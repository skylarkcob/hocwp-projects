<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

global $hocwp_tos_tabs;

$option = new HOCWP_Option( __( 'Writing', 'hocwp-theme' ), 'hocwp_writing' );
$option->set_parent_slug( 'hocwp_theme_option' );
$option->set_use_media_upload( true );
$option->add_field( array( 'id'             => 'default_post_thumbnail',
                           'title'          => __( 'Default post thumbnail', 'hocwp-theme' ),
                           'field_callback' => 'hocwp_field_media_upload'
) );

$option->add_option_tab( $hocwp_tos_tabs );
$option->set_page_header_callback( 'hocwp_theme_option_form_before' );
$option->set_page_footer_callback( 'hocwp_theme_option_form_after' );
$option->set_page_sidebar_callback( 'hocwp_theme_option_sidebar_tab' );
$option->init();
hocwp_option_add_object_to_list( $option );