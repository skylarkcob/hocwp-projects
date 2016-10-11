<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

global $hocwp_tos_tabs;
$parent_slug = 'hocwp_theme_option';

$lat_lng = hocwp_get_default_lat_long();

$option = new HOCWP_Option( __( 'Geo', 'hocwp-theme' ), 'hocwp_geo' );
$option->set_parent_slug( $parent_slug );

$option->add_field( array( 'id'      => 'default_lat',
                           'title'   => __( 'Default Latitude', 'hocwp-theme' ),
                           'default' => $lat_lng['lat']
) );
$option->add_field( array( 'id'      => 'default_lng',
                           'title'   => __( 'Default Longitude', 'hocwp-theme' ),
                           'default' => $lat_lng['lng']
) );

$option->add_option_tab( $hocwp_tos_tabs );
$option->set_page_header_callback( 'hocwp_theme_option_form_before' );
$option->set_page_footer_callback( 'hocwp_theme_option_form_after' );
$option->set_page_sidebar_callback( 'hocwp_theme_option_sidebar_tab' );

$option->init();

hocwp_option_add_object_to_list( $option );