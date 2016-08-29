<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

function hocwp_get_ads_positions() {
	global $hocwp_ads_positions;
	$hocwp_ads_positions = hocwp_sanitize_array( $hocwp_ads_positions );
	$defaults            = array(
		'leaderboard' => array(
			'id'          => 'leaderboard',
			'name'        => __( 'Leaderboard', 'hocwp-theme' ),
			'description' => __( 'Display beside logo in header area.', 'hocwp-theme' )
		)
	);
	$hocwp_ads_positions = wp_parse_args( $hocwp_ads_positions, $defaults );

	return apply_filters( 'hocwp_ads_positions', $hocwp_ads_positions );
}

function hocwp_add_ads_position( $args = array() ) {
	$positions                      = hocwp_get_ads_positions();
	$id                             = hocwp_get_value_by_key( $args, 'id' );
	$positions[ $id ]               = $args;
	$GLOBALS['hocwp_ads_positions'] = $positions;
}

function hocwp_show_ads( $args = array() ) {
	if ( ! is_array( $args ) ) {
		$args = array(
			'position' => $args
		);
	}
	$position = hocwp_get_value_by_key( $args, 'position' );
	if ( ! empty( $position ) ) {
		$random           = (bool) hocwp_get_value_by_key( $args, 'random' );
		$current_datetime = date( hocwp_get_date_format() );
		$current_datetime = strtotime( $current_datetime );
		$query_args       = array(
			'post_type'      => 'hocwp_ads',
			'posts_per_page' => 1,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'relation' => 'OR',
					array(
						'key'     => 'expire',
						'compare' => 'NOT EXISTS'
					),
					array(
						'key'     => 'expire',
						'value'   => '',
						'compare' => '='
					),
					array(
						'key'   => 'expire',
						'value' => 0,
						'type'  => 'numeric'
					),
					array(
						'key'     => 'expire',
						'value'   => $current_datetime,
						'type'    => 'numeric',
						'compare' => '>='
					)
				),
				array(
					'key'   => 'active',
					'value' => 1,
					'type'  => 'numeric'
				)
			)
		);
		if ( $random ) {
			$query_args['orderby'] = 'rand';
		}
		$ads = hocwp_get_post_by_meta( 'position', $position, $query_args );
		if ( $ads->have_posts() ) {
			$posts = $ads->posts;
			$ads   = array_shift( $posts );
			$ads   = hocwp_get_post_meta( 'code', $ads->ID );
			if ( ! empty( $ads ) ) {
				$class = hocwp_get_value_by_key( $args, 'class' );
				hocwp_add_string_with_space_before( $class, 'hocwp-ads text-center ads position-' . $position );
				$div = new HOCWP_HTML( 'div' );
				$div->set_class( $class );
				$div->set_text( $ads );
				$div->output();
			}
		}
	}
}