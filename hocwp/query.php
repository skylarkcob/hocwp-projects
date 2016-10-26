<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}
function hocwp_query( $args = array() ) {
	if ( ! isset( $args['post_type'] ) ) {
		$args['post_type'] = 'post';
	}
	if ( ! isset( $args['paged'] ) && isset( $_REQUEST['paged'] ) ) {
		$args['paged'] = hocwp_get_paged();
	}
	$defaults = array(
		'order'   => 'desc',
		'orderby' => 'date'
	);
	$args     = wp_parse_args( $args, $defaults );
	$cache    = isset( $args['cache'] ) ? $args['cache'] : false;
	if ( false !== $cache ) {
		$transient_name = hocwp_build_transient_name( 'hocwp_query_cache_%s', $args );
		if ( false === ( $query = get_transient( $transient_name ) ) ) {
			$query = new WP_Query( $args );
			if ( $query->have_posts() ) {
				if ( ! is_numeric( $cache ) ) {
					$cache = WEEK_IN_SECONDS;
				}
				set_transient( $transient_name, $query, $cache );
			}
		}

		return $query;
	}

	return new WP_Query( $args );
}

function hocwp_query_images( $args = array() ) {
	$mimes    = array( 'image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/tiff' );
	$mimes    = apply_filters( 'hocwp_image_mime_type', $mimes );
	$defaults = array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'post_mime_type' => $mimes
	);
	$args     = wp_parse_args( $args, $defaults );

	return hocwp_query( $args );
}

function hocwp_query_trending( $args = array(), $fallback_args = array() ) {
	$post_type        = hocwp_get_value_by_key( $args, 'post_type' );
	$post_ids         = hocwp_get_all_treding_post_ids( $post_type );
	$args['post__in'] = $post_ids;
	$args['orderby']  = 'post__in';
	$query            = hocwp_query( $args );
	if ( ! $query->have_posts() && hocwp_array_has_value( $fallback_args ) ) {
		$query = hocwp_query( $fallback_args );
	}

	return $query;
}

function hocwp_query_product( $args = array() ) {
	$args['post_type'] = 'product';

	return hocwp_query( $args );
}

function hocwp_query_featured_product( $args = array() ) {
	$meta_item = array(
		'relation' => 'or',
		array(
			'key'   => '_featured',
			'value' => 'yes'
		),
		array(
			'key'   => 'featured',
			'value' => 1,
			'type'  => 'numeric'
		)
	);
	$args      = hocwp_query_sanitize_meta_query( $meta_item, $args );

	return hocwp_query_product( $args );
}

function hocwp_query_sale_product( $args = array() ) {
	$meta_item = array(
		'relation' => 'OR',
		array(
			'key'     => '_sale_price',
			'value'   => 0,
			'compare' => '>',
			'type'    => 'numeric'
		),
		array(
			'key'     => '_min_variation_sale_price',
			'value'   => 0,
			'compare' => '>',
			'type'    => 'numeric'
		)
	);
	$args      = hocwp_query_sanitize_meta_query( $meta_item, $args );

	return hocwp_query_product( $args );
}

function hocwp_query_post_by_category( $term, $args = array() ) {
	hocwp_query_sanitize_post_by_category( $term, $args );

	return hocwp_query( $args );
}

function hocwp_query_product_by_category( $term, $args = array() ) {
	hocwp_query_sanitize_post_by_category( $term, $args );

	return hocwp_query_product( $args );
}

function hocwp_query_sanitize_post_by_category( $term, &$args = array() ) {
	if ( is_array( $term ) ) {
		foreach ( $term as $aterm ) {
			if ( is_a( $aterm, 'WP_Term' ) ) {
				$tax_item = array(
					'taxonomy' => $aterm->taxonomy,
					'field'    => 'id',
					'terms'    => $aterm->term_id
				);
				hocwp_query_sanitize_tax_query( $tax_item, $args );
				$args['tax_query']['relation'] = 'OR';
			}
		}
	} else {
		if ( is_a( $term, 'WP_Term' ) ) {
			$tax_item = array(
				'taxonomy' => $term->taxonomy,
				'field'    => 'id',
				'terms'    => $term->term_id
			);
			hocwp_query_sanitize_tax_query( $tax_item, $args );
		}
	}

	return $args;
}

function hocwp_query_post_by_meta( $meta_key, $meta_value, $args = array(), $meta_type = '', $compare = '=' ) {
	$meta_item = array(
		'key'   => $meta_key,
		'value' => $meta_value
	);
	if ( ! empty( $meta_type ) ) {
		$meta_item['type'] = $meta_item;
	}
	if ( ! empty( $compare ) ) {
		$meta_item['compare'] = $compare;
	}
	$args = hocwp_query_sanitize_meta_query( $meta_item, $args );
	unset( $meta_item );

	return hocwp_query( $args );
}

function hocwp_query_sanitize_tax_query( $tax_item, &$args ) {
	if ( is_array( $args ) ) {
		if ( ! isset( $args['tax_query']['relation'] ) ) {
			$args['tax_query']['relation'] = 'OR';
		}
		if ( isset( $args['tax_query'] ) ) {
			array_push( $args['tax_query'], $tax_item );
		} else {
			$args['tax_query'] = array( $tax_item );
		}
	}

	return $args;
}

function hocwp_query_featured( $args = array() ) {
	$args = hocwp_query_sanitize_featured_args( $args );

	return hocwp_query( $args );
}

function hocwp_query_sanitize_featured_args( &$args = array() ) {
	$meta_item = array(
		'key'   => 'featured',
		'value' => 1,
		'type'  => 'NUMERIC'
	);
	hocwp_query_sanitize_meta_query( $meta_item, $args );
	$args['meta_key']   = 'featured';
	$args['meta_value'] = 1;

	return $args;
}

function hocwp_query_sanitize_meta_query( $item, &$args ) {
	if ( is_array( $args ) ) {
		if ( ! isset( $args['meta_query']['relation'] ) ) {
			$args['meta_query']['relation'] = 'OR';
		}
		if ( isset( $args['meta_query'] ) ) {
			array_push( $args['meta_query'], $item );
		} else {
			$args['meta_query'] = array( $item );
		}
	}

	return $args;
}

function hocwp_query_sanitize_date_query_args( $item, &$args = array() ) {
	$date_query = hocwp_get_value_by_key( $args, 'date_query' );
	if ( ! is_array( $date_query ) ) {
		$date_query = array();
	}
	if ( ! is_array( $item ) ) {
		$today = getdate();
		switch ( $item ) {
			case 'today':
			case 'daily':
			case 'day':
				$item = array(
					'year'  => $today['year'],
					'month' => $today['mon'],
					'day'   => $today['mday'],
				);
				break;
			case 'yesterday':
				$item = array(
					'column' => 'post_date_gmt',
					'after'  => '1 day ago'
				);
				break;
			case 'this_week':
				$item = array(
					'year' => date( 'Y' ),
					'week' => date( 'W' ),
				);
				break;
			case 'last_week':
			case 'weekly':
			case 'week':
				$item = array(
					'column' => 'post_date_gmt',
					'after'  => '1 week ago'
				);
				break;
			case 'this_month':
				$item = array(
					'year'  => $today['year'],
					'month' => $today['mon']
				);
				break;
			case 'last_month':
			case 'monthly':
			case 'month':
				$item = array(
					'column' => 'post_date_gmt',
					'after'  => '1 month ago'
				);
				break;
			case 'this_year':
				$item = array(
					'year' => $today['year']
				);
				break;
			case 'last_year':
			case 'yearly':
			case 'year':
				$item = array(
					'column' => 'post_date_gmt',
					'after'  => '1 year ago'
				);
				break;
		}
	}
	if ( is_array( $item ) ) {
		$date_query[] = $item;
	}
	$args['date_query'] = $date_query;

	return $args;
}

function hocwp_query_build_binary_meta_args( $meta_key, $args = array() ) {
	$meta_item = array(
		'relation' => 'AND',
		array(
			'key'     => $meta_key,
			'compare' => 'EXISTS'
		),
		array(
			'key'   => $meta_key,
			'value' => 1,
			'type'  => 'NUMERIC'
		),
	);
	if ( isset( $args['meta_query'] ) ) {
		hocwp_query_sanitize_meta_query( $meta_item, $args );
	} else {
		$defaults = array(
			'meta_query' => array(
				$meta_item
			)
		);
		$args     = wp_parse_args( $args, $defaults );
	}

	return $args;
}

function hocwp_query_post_by_binary_meta( $meta_key, $args = array() ) {
	$args = hocwp_query_build_binary_meta_args( $meta_key, $args );

	return hocwp_query( $args );
}

function hocwp_query_post_by_format( $format, $args = array() ) {
	if ( is_string( $format ) && ! hocwp_string_contain( $format, 'post-format-' ) ) {
		$format = 'post-format-' . $format;
	}
	if ( ! is_array( $format ) ) {
		$format = array( $format );
	}
	$item = array(
		'taxonomy' => 'post_format',
		'field'    => 'slug',
		'terms'    => $format
	);
	hocwp_query_sanitize_tax_query( $item, $args );

	return hocwp_query( $args );
}

function hocwp_query_modified_post( $args = array() ) {
	$args['orderby'] = 'modified';

	return hocwp_query( $args );
}

function hocwp_query_most_viewed_post( $args = array() ) {
	$args['meta_key'] = 'views';
	$args['orderby']  = 'meta_value_num';
	$args             = hocwp_query_build_binary_meta_args( 'views', $args );

	return hocwp_query( $args );
}

function hocwp_query_random_post( $args = array() ) {
	$args['orderby'] = 'rand';

	return hocwp_query( $args );
}

function hocwp_query_most_comment_post( $args = array() ) {
	$args['orderby'] = 'comment_count';

	return hocwp_query( $args );
}

function hocwp_query_by( $by, $args = array(), $interval = '' ) {
	switch ( $by ) {
		case 'updated':
		case 'update':
		case 'modified':
		case 'modify':
			$args['orderby'] = 'modified';
			break;
		case 'rand':
		case 'random':
			$args['orderby'] = 'rand';
			break;
		case 'most_commented':
		case 'most_comment':
		case 'comment_count':
		case 'comment':
			$args['orderby'] = 'comment_count';
			break;
		case 'featured':
			hocwp_query_sanitize_featured_args( $args );
			break;
		case 'most_likes':
		case 'most_liked':
		case 'most_like':
		case 'likes':
		case 'liked':
		case 'like':
			$args['meta_key'] = 'likes';
			$args['orderby']  = 'meta_value_num';
			break;
		case 'most_views':
		case 'most_viewed':
		case 'most_view':
		case 'views':
		case 'viewed':
		case 'view':
			$args['meta_key'] = 'views';
			$args['orderby']  = 'meta_value_num';
			break;
	}
	if ( ! empty( $interval ) ) {
		hocwp_query_sanitize_date_query_args( $interval, $args );
	}

	return hocwp_query( $args );
}

function hocwp_query_related_post( $args = array() ) {
	$post_id = absint( isset( $args['post_id'] ) ? $args['post_id'] : get_the_ID() );
	if ( $post_id < 1 ) {
		return new WP_Query();
	}
	$current_post = get_post( $post_id );
	if ( ! isset( $args['post_type'] ) ) {
		$args['post_type'] = $current_post->post_type;
	}
	$keep_current   = (bool) hocwp_get_value_by_key( $args, 'keep_current' );
	$posts_per_page = hocwp_get_value_by_key( $args, 'posts_per_page', hocwp_get_posts_per_page() );
	$transient_name = 'hocwp_cache_post_' . $post_id . '_related_%s';
	$transient_name = hocwp_build_transient_name( $transient_name, $args );
	$cache          = isset( $args['cache'] ) ? $args['cache'] : true;
	$query          = new WP_Query();
	if ( ! $cache || ( $cache && false === ( $query = get_transient( $transient_name ) ) ) ) {
		$taxonomies = get_post_taxonomies( $post_id );
		$defaults   = array();
		foreach ( $taxonomies as $taxonomy ) {
			$tax = get_taxonomy( $taxonomy );
			if ( (bool) $tax->hierarchical ) {
				continue;
			}
			$term_ids = wp_get_post_terms( $post_id, $taxonomies, array( 'fields' => 'ids' ) );
			if ( hocwp_array_has_value( $term_ids ) ) {
				$tax_item = array(
					'taxonomy' => $taxonomy,
					'field'    => 'id',
					'terms'    => $term_ids
				);
				$defaults = hocwp_query_sanitize_tax_query( $tax_item, $defaults );
			}
		}
		if ( hocwp_array_has_value( $defaults ) ) {
			if ( ! $keep_current ) {
				$defaults['post__not_in'] = array( $post_id );
			}
			$defaults['tax_query']['relation'] = 'OR';
			$query_args                        = wp_parse_args( $args, $defaults );
			$query                             = hocwp_query( $query_args );
		}
		if ( ! is_a( $query, 'WP_Query' ) ) {
			$query = new WP_Query();
		}
		if ( $query->post_count < $posts_per_page ) {
			$missing  = $posts_per_page - $query->post_count;
			$defaults = array();
			foreach ( $taxonomies as $taxonomy ) {
				$tax = get_taxonomy( $taxonomy );
				if ( ! (bool) $tax->hierarchical ) {
					continue;
				}
				$term_ids = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
				if ( hocwp_array_has_value( $term_ids ) ) {
					$tax_item = array(
						'taxonomy' => $taxonomy,
						'field'    => 'id',
						'terms'    => $term_ids
					);
					$defaults = hocwp_query_sanitize_tax_query( $tax_item, $defaults );
				}
			}
			if ( ! $keep_current ) {
				$defaults['post__not_in'] = array( $post_id );
			}
			$defaults['tax_query']['relation'] = 'OR';
			$defaults['posts_per_page']        = $missing;
			unset( $args['tax_query'] );
			$args      = wp_parse_args( $args, $defaults );
			$cat_query = hocwp_query( $args );
			$post_ids  = array();
			if ( hocwp_array_has_value( $query->posts ) ) {
				foreach ( $query->posts as $post ) {
					array_push( $post_ids, $post->ID );
				}
			}
			if ( hocwp_array_has_value( $cat_query->posts ) ) {
				foreach ( $cat_query->posts as $post ) {
					array_push( $post_ids, $post->ID );
				}
			}
			$args['posts_per_page'] = $posts_per_page;
			$args['post__in']       = $post_ids;
			$args['orderby']        = 'post__in';
			$query                  = hocwp_query( $args );
		}
		if ( ! $query->have_posts() ) {
			unset( $args['post__in'] );
			unset( $args['tax_query'] );
			$args['orderby'] = 'date';
			$args['s']       = $current_post->post_title;
			$query           = hocwp_query( $args );
		}
		$cache_days = apply_filters( 'hocwp_related_post_cache_days', 3 );
		if ( ! $query->have_posts() ) {
			$cache_days = 1;
		}
		if ( ( $cache || hocwp_is_positive_number( $cache ) ) && $query->have_posts() ) {
			if ( ! hocwp_is_positive_number( $cache ) ) {
				$cache = $cache_days * DAY_IN_SECONDS;
			}
			set_transient( $transient_name, $query, $cache );
		}
	}

	return $query;
}

function hocwp_query_post_in_same_category( $args = array() ) {
	$post_id = absint( isset( $args['post_id'] ) ? $args['post_id'] : get_the_ID() );
	$query   = new WP_Query();
	if ( ! hocwp_id_number_valid( $post_id ) ) {
		return $query;
	}
	$cache          = (bool) hocwp_get_value_by_key( $args, 'cache', true );
	$transient_name = 'hocwp_query_post_' . $post_id . '_in_same_category_%s';
	$transient_name = hocwp_build_transient_name( $transient_name, $args );
	$query          = get_transient( $transient_name );
	if ( ! $cache || false === $query ) {
		$taxonomies = get_post_taxonomies( $post_id );
		foreach ( $taxonomies as $key => $tax_name ) {
			$taxonomy = get_taxonomy( $tax_name );
			if ( ! $taxonomy->hierarchical ) {
				unset( $taxonomies[ $key ] );
			}
		}
		if ( hocwp_array_has_value( $taxonomies ) ) {
			$defaults = array();
			foreach ( $taxonomies as $taxonomy ) {
				$term_ids = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
				if ( hocwp_array_has_value( $term_ids ) ) {
					$tax_item = array(
						'taxonomy' => $taxonomy,
						'field'    => 'id',
						'terms'    => $term_ids
					);
					$defaults = hocwp_query_sanitize_tax_query( $tax_item, $defaults );
				}
			}
			$args                          = wp_parse_args( $args, $defaults );
			$args['tax_query']['relation'] = 'OR';
			$query                         = hocwp_query( $args );
			if ( $cache && $query->have_posts() ) {
				set_transient( $transient_name, $query, DAY_IN_SECONDS );
			}
		}
	}

	return $query;
}

function hocwp_query_all( $post_type ) {
	$args = array(
		'post_type'      => $post_type,
		'posts_per_page' => - 1
	);
	if ( 'page' == $post_type ) {
		$args['orderby'] = 'title';
		$args['order']   = 'ASC';
	}

	return hocwp_query( $args );
}