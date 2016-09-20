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

	return new WP_Query( $args );
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
			$tax_item = array(
				'taxonomy' => $aterm->taxonomy,
				'field'    => 'id',
				'terms'    => $aterm->term_id
			);
			hocwp_query_sanitize_tax_query( $tax_item, $args );
			$args['tax_query']['relation'] = 'OR';
		}
	} else {
		$tax_item = array(
			'taxonomy' => $term->taxonomy,
			'field'    => 'id',
			'terms'    => $term->term_id
		);
		hocwp_query_sanitize_tax_query( $tax_item, $args );
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

function hocwp_query_post_by_format( $format, $args = array() ) {
	$meta_item = array(
		'key'   => 'post_format',
		'value' => $format
	);
	$args      = hocwp_query_sanitize_meta_query( $meta_item, $args );

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
	$transient_name = 'hocwp_query_post_' . $post_id . '_related_' . md5( json_encode( $args ) );
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
		if ( ! $keep_current ) {
			$defaults['post__not_in'] = array( $post_id );
		}
		$defaults['tax_query']['relation'] = 'OR';
		$args                              = wp_parse_args( $args, $defaults );
		$query                             = hocwp_query( $args );
		$posts_per_page                    = isset( $query->query_vars['posts_per_page'] ) ? $query->query_vars['posts_per_page'] : hocwp_get_posts_per_page();
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
			foreach ( $query->posts as $post ) {
				array_push( $post_ids, $post->ID );
			}
			foreach ( $cat_query->posts as $post ) {
				array_push( $post_ids, $post->ID );
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
		if ( $cache && $query->have_posts() ) {
			set_transient( $transient_name, $query, $cache_days * DAY_IN_SECONDS );
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
	$transient_name = 'hocwp_query_post_' . $post_id . '_in_same_category_' . md5( json_encode( $args ) );
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