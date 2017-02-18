<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

function hocwp_get_ads_positions() {
	global $hocwp_ads_positions;
	$hocwp_ads_positions = hocwp_sanitize_array( $hocwp_ads_positions );
	$defaults            = array(
		'leaderboard'           => array(
			'id'          => 'leaderboard',
			'name'        => __( 'Leaderboard', 'hocwp-theme' ),
			'description' => __( 'Display beside logo in header area.', 'hocwp-theme' )
		),
		'after_first_paragraph' => array(
			'id'   => 'after_first_paragraph',
			'name' => __( 'After first paragraph in post content', 'hocwp-theme' )
		),
		'middle_post_content'   => array(
			'id'   => 'middle_post_content',
			'name' => __( 'Middle post content', 'hocwp-theme' )
		),
		'before_last_paragraph' => array(
			'id'   => 'before_last_paragraph',
			'name' => __( 'Before last paragraph in post content', 'hocwp-theme' )
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

function hocwp_register_ads_position( $args = array() ) {
	hocwp_add_ads_position( $args );
}

function hocwp_show_ads( $args = array() ) {
	$ads      = $args;
	$position = '';
	if ( ! is_object( $args ) ) {
		if ( ! is_array( $args ) ) {
			$args = array(
				'position' => $args
			);
		}
		$position = hocwp_get_value_by_key( $args, 'position' );
		if ( ! empty( $position ) ) {
			$random           = (bool) hocwp_get_value_by_key( $args, 'random' );
			$random           = apply_filters( 'hocwp_show_ads_random', $random, $args );
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
			}
		}
	}
	if ( hocwp_is_post( $ads ) && 'hocwp_ads' == $ads->post_type ) {
		$device = get_post_meta( $ads->ID, 'device', true );
		if ( 'all' != $device ) {
			$detect = new Mobile_Detect();
			switch ( $device ) {
				case 'mobile':
					if ( ! $detect->isMobile() ) {
						return;
					}
					break;
				case 'tablet':
					if ( ! $detect->isTablet() ) {
						return;
					}
					break;
				case 'pc':
					if ( $detect->isMobile() || $detect->isTablet() ) {
						return;
					}
					break;
				case 'mobile_and_tablet':
					if ( ! $detect->isMobile() && ! $detect->isTablet() ) {
						return;
					}
					break;
			}
		}
		$in_post_types = get_post_meta( $ads->ID, 'in_post_types', true );
		$in_post_types = hocwp_json_string_to_array( $in_post_types );
		$in_post_types = hocwp_remove_empty_array_item( $in_post_types );
		if ( hocwp_array_has_value( $in_post_types ) ) {
			$in_type = false;
			foreach ( $in_post_types as $post_type ) {
				$value = isset( $post_type['value'] ) ? $post_type['value'] : '';
				if ( ! empty( $value ) && $value = $ads->post_type ) {
					$in_type = true;
					break;
				}
			}
			if ( ! $in_type ) {
				return;
			}
		}
		$code = hocwp_get_post_meta( 'code', $ads->ID );
		if ( empty( $code ) ) {
			$image = hocwp_get_post_meta( 'image', $ads->ID );
			$image = hocwp_sanitize_media_value( $image );
			$image = $image['url'];
			if ( ! empty( $image ) ) {
				$img = new HOCWP_HTML( 'img' );
				$img->set_image_src( $image );
				$url = hocwp_get_post_meta( 'url', $ads->ID );
				if ( ! empty( $url ) ) {
					$a = new HOCWP_HTML( 'a' );
					$a->set_href( $url );
					$a->set_text( $img );
					$code = $a->build();
				} else {
					$code = $img->build();
				}
			}
		}
		if ( ! empty( $code ) ) {
			$class = hocwp_get_value_by_key( $args, 'class' );
			hocwp_add_string_with_space_before( $class, 'hocwp-ads text-center ads' );
			if ( ! empty( $position ) ) {
				hocwp_add_string_with_space_before( $class, 'position-' . $position );
				$position = hocwp_sanitize_html_class( $position );
				$class    = hocwp_add_more_class( $class, $position );
			}
			hocwp_add_string_with_space_before( $class, $ads->post_name );
			$div = new HOCWP_HTML( 'div' );
			$div->set_class( $class );
			$div->set_text( $code );
			$html = $div->build();
			$html = apply_filters( 'hocwp_ads_html', $html, $ads_or_args = $args );
			echo $html;
		}
	}
}