<?php
function hocwp_coupon_store_base() {
	$option = get_option( 'hocwp_permalink' );
	$base   = hocwp_get_value_by_key( $option, 'coupon_store_base', 'store' );
	$base   = apply_filters( 'hocwp_coupon_store_base', $base );
	if ( empty( $base ) ) {
		$base = 'store';
	}

	return $base;
}

function hocwp_coupon_category_base() {
	$option = get_option( 'hocwp_permalink' );
	$base   = hocwp_get_value_by_key( $option, 'coupon_category_base', 'coupon-cat' );
	$base   = apply_filters( 'hocwp_coupon_category_base', $base );
	if ( empty( $base ) ) {
		$base = 'coupon-cat';
	}

	return $base;
}

function hocwp_coupon_tag_base() {
	$option = get_option( 'hocwp_permalink' );
	$base   = hocwp_get_value_by_key( $option, 'coupon_tag_base', 'coupon-tag' );
	$base   = apply_filters( 'hocwp_coupon_tag_base', $base );
	if ( empty( $base ) ) {
		$base = 'coupon-tag';
	}

	return $base;
}

function hocwp_coupon_type_base() {
	$option = get_option( 'hocwp_permalink' );
	$base   = hocwp_get_value_by_key( $option, 'coupon_type_base', 'coupon-type' );
	$base   = apply_filters( 'hocwp_coupon_type_base', $base );
	if ( empty( $base ) ) {
		$base = 'coupon-type';
	}

	return $base;
}

function hocwp_coupon_install_post_type_and_taxonomy() {
	$slug = apply_filters( 'hocwp_post_type_coupon_slug', 'coupon' );
	$args = array(
		'name'              => __( 'Coupons', 'hocwp-theme' ),
		'singular_name'     => __( 'Coupon', 'hocwp-theme' ),
		'supports'          => array( 'editor', 'comments', 'thumbnail', 'excerpt' ),
		'post_type'         => 'coupon',
		'slug'              => $slug,
		'taxonomies'        => array( 'store', 'coupon_cat', 'coupon_tag', 'coupon_type' ),
		'show_in_admin_bar' => true
	);
	hocwp_register_post_type( $args );

	$slug = apply_filters( 'hocwp_post_type_event_slug', 'event' );
	$args = array(
		'name'              => __( 'Events', 'hocwp-theme' ),
		'singular_name'     => __( 'Event', 'hocwp-theme' ),
		'supports'          => array( 'editor', 'comments', 'thumbnail' ),
		'post_type'         => 'event',
		'slug'              => $slug,
		'show_in_admin_bar' => true
	);
	hocwp_register_post_type( $args );

	$slug = apply_filters( 'hocwp_taxonomy_store_slug', hocwp_coupon_store_base() );
	$args = array(
		'name'          => __( 'Stores', 'hocwp-theme' ),
		'singular_name' => __( 'Store', 'hocwp-theme' ),
		'taxonomy'      => 'store',
		'slug'          => $slug,
		'post_types'    => array( 'coupon' )
	);
	hocwp_register_taxonomy( $args );

	$slug = apply_filters( 'hocwp_taxonomy_coupon_cat_slug', hocwp_coupon_category_base() );
	$args = array(
		'name'          => __( 'Coupon Categories', 'hocwp-theme' ),
		'singular_name' => __( 'Coupon Category', 'hocwp-theme' ),
		'menu_name'     => __( 'Categories', 'hocwp-theme' ),
		'slug'          => $slug,
		'taxonomy'      => 'coupon_cat',
		'post_types'    => array( 'coupon' )
	);
	hocwp_register_taxonomy( $args );

	$slug = apply_filters( 'hocwp_taxonomy_coupon_tag_slug', hocwp_coupon_tag_base() );
	$args = array(
		'name'          => __( 'Coupon Tags', 'hocwp-theme' ),
		'singular_name' => __( 'Coupon Tag', 'hocwp-theme' ),
		'menu_name'     => __( 'Tags', 'hocwp-theme' ),
		'slug'          => $slug,
		'taxonomy'      => 'coupon_tag',
		'hierarchical'  => false,
		'post_types'    => array( 'coupon' )
	);
	hocwp_register_taxonomy( $args );

	$slug = apply_filters( 'hocwp_taxonomy_coupon_type_slug', hocwp_coupon_type_base() );
	$args = array(
		'name'          => __( 'Coupon Types', 'hocwp-theme' ),
		'singular_name' => __( 'Coupon Type', 'hocwp-theme' ),
		'menu_name'     => __( 'Types', 'hocwp-theme' ),
		'slug'          => $slug,
		'taxonomy'      => 'coupon_type',
		'post_types'    => array( 'coupon' )
	);
	hocwp_register_taxonomy( $args );
}

function hocwp_get_coupon_url( $post_id = null ) {
	if ( ! hocwp_id_number_valid( $post_id ) ) {
		$out = get_query_var( 'out' );
		if ( ! empty( $out ) ) {
			if ( hocwp_id_number_valid( $out ) ) {
				$post_id = $out;
			} else {
				$post = hocwp_get_post_by_slug( $out );
				if ( is_a( $post, 'WP_Post' ) ) {
					$post_id = $post->ID;
				}
			}
		} else {
			$post = hocwp_get_post_by_slug( $post_id );
			if ( is_a( $post, 'WP_Post' ) ) {
				$post_id = $post->ID;
			} else {
				$post_id = get_the_ID();
			}
		}
	}
	$url = hocwp_get_coupon_meta( 'url', $post_id );
	if ( empty( $url ) ) {
		$store = hocwp_get_coupon_store( $post_id );
		if ( is_a( $store, 'WP_Term' ) ) {
			$url = hocwp_get_store_url( $store->term_id );
		}
	}

	return $url;
}

function hocwp_get_store_url( $id ) {
	return get_term_meta( $id, 'site', true );
}

function hocwp_get_top_store_by_coupon_count( $args = array() ) {
	return hocwp_term_get_by_count( 'store', $args );
}

function hocwp_get_top_category_by_coupon_count( $args = array() ) {
	return hocwp_term_get_by_count( 'coupon_cat', $args );
}

function hocwp_get_coupon_categories( $args = array() ) {
	return hocwp_get_terms( 'coupon_cat', $args );
}

function hocwp_get_coupon_stores( $args = array() ) {
	return hocwp_get_terms( 'store', $args );
}

function hocwp_get_coupon_hint( $post_id = null ) {
	$code = hocwp_get_coupon_code( $post_id );
	$len  = strlen( $code );
	if ( $len > 3 ) {
		$len = intval( $len / 2 );
	}
	if ( $len < 3 ) {
		$len = 3;
	}
	if ( $len > 10 ) {
		$len = 10;
	}
	$len  = - $len;
	$code = substr( $code, $len );

	return $code;
}

function hocwp_get_coupon_code( $post_id = null ) {
	if ( ! hocwp_id_number_valid( $post_id ) ) {
		$post_id = get_the_ID();
	}
	$code = hocwp_get_coupon_meta( 'coupon_code', $post_id );
	if ( empty( $code ) ) {
		$code = hocwp_get_coupon_meta( 'code', $post_id );
	}
	if ( empty( $code ) ) {
		$code = hocwp_get_coupon_meta( 'wpcf-coupon-code', $post_id );
	}

	return $code;
}

function hocwp_get_coupon_meta( $meta_key, $post_id = null ) {
	return hocwp_get_post_meta( $meta_key, $post_id );
}

function hocwp_get_coupon_percent_label( $post_id = null ) {
	return hocwp_get_coupon_meta( 'percent_label', $post_id );
}

function hocwp_get_coupon_text_label( $post_id = null ) {
	return hocwp_get_coupon_meta( 'text_label', $post_id );
}

function hocwp_get_coupon_expired_date( $post_id = null ) {
	return hocwp_get_coupon_meta( 'expired_date', $post_id );
}

function hocwp_get_coupon_type_term( $post_id = null ) {
	if ( ! hocwp_id_number_valid( $post_id ) ) {
		$post_id = get_the_ID();
	}
	$terms = wp_get_post_terms( $post_id, 'coupon_type' );
	$term  = array_shift( $terms );

	return $term;
}

function hocwp_get_coupon_type_object( $type = 'code' ) {
	//$term = new WP_Error();
	switch ( $type ) {
		case 'deal':
			$term = hocwp_get_term_by_slug( 'deal', 'coupon_type' );
			if ( ! is_a( $term, 'WP_Term' ) ) {
				$term = hocwp_get_term_by_slug( 'sales', 'coupon_type' );
			}
			if ( ! is_a( $term, 'WP_Term' ) ) {
				$term = hocwp_get_term_by_slug( 'promotion', 'coupon_type' );
			}
			break;
		default:
			$term = hocwp_get_term_by_slug( 'promo-codes', 'coupon_type' );
			if ( ! is_a( $term, 'WP_Term' ) ) {
				$term = hocwp_get_term_by_slug( 'promo-code', 'coupon_type' );
			}
			if ( ! is_a( $term, 'WP_Term' ) ) {
				$term = hocwp_get_term_by_slug( 'code', 'coupon_type' );
			}
			if ( ! is_a( $term, 'WP_Term' ) ) {
				$term = hocwp_get_term_by_slug( 'coupon-code', 'coupon_type' );
			}
			if ( ! is_a( $term, 'WP_Term' ) ) {
				$term = hocwp_get_term_by_slug( 'coupon-code', 'coupon_type' );
			}
	}

	return $term;
}

function hocwp_coupon_get_store_by_category( $category ) {
	$args   = array(
		'post_type'      => 'coupon',
		'posts_per_page' => - 1,
		'tax_query'      => array(
			array(
				'taxonomy' => $category->taxonomy,
				'field'    => 'id',
				'terms'    => array( $category->term_id )
			)
		)
	);
	$query  = hocwp_query( $args );
	$result = array();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$terms = wp_get_object_terms( get_the_ID(), 'store' );
			if ( hocwp_array_has_value( $terms ) ) {
				$result = array_merge( $result, $terms );
			}
		}
		wp_reset_postdata();
	}
	$result = array_unique( $result, SORT_REGULAR );

	return $result;
}

function hocwp_get_event_coupons( $event_id, $args = array() ) {
	$args['meta_key']       = 'event';
	$args['meta_value_num'] = $event_id;
	$args['post_type']      = 'coupon';

	return hocwp_query( $args );
}

function hocwp_coupon_build_expired_query_args( $args = array() ) {
	$timestamp            = strtotime( hocwp_get_current_datetime_mysql() );
	$args['meta_key']     = 'expired_date';
	$args['meta_value']   = $timestamp;
	$args['meta_compare'] = '<';
	$args['meta_type']    = 'numeric';
	$meta_item            = array(
		'realation' => 'AND',
		array(
			'key'     => 'expired_date',
			'value'   => $timestamp,
			'compare' => '<',
			'type'    => 'numeric'
		),
		array(
			'key'     => 'expired_date',
			'compare' => 'EXISTS'
		),
		array(
			'key'     => 'expired_date',
			'compare' => '>',
			'value'   => 0,
			'type'    => 'numeric'
		)
	);
	if ( isset( $args['meta_query'] ) ) {
		foreach ( $args['meta_query'] as $i => $meta ) {
			if ( hocwp_array_has_value( $meta ) ) {
				foreach ( $meta as $j => $child_meta ) {
					if ( is_array( $child_meta ) ) {
						foreach ( $child_meta as $k => $last ) {
							if ( ! is_array( $last ) && 'key' == $k && 'expired_date' == $last ) {
								unset( $args['meta_query'][ $i ] );
							}
						}
					} else {
						if ( 'key' == $j && 'expired_date' == $child_meta ) {
							unset( $args['meta_query'][ $i ] );
						}
					}
				}
			}
		}
	}
	$args                           = hocwp_query_sanitize_meta_query( $meta_item, $args );
	$meta_item                      = array(
		'key'     => 'expired_date',
		'compare' => 'EXISTS'
	);
	$args                           = hocwp_query_sanitize_meta_query( $meta_item, $args );
	$args['meta_query']['relation'] = 'AND';
	$args['expired_coupon']         = true;
	if ( ! isset( $args['post_type'] ) ) {
		$args['post_type'] = 'coupon';
	}

	return $args;
}

function hocwp_get_expired_coupons( $args = array() ) {
	$args = hocwp_coupon_build_expired_query_args( $args );

	return hocwp_query( $args );
}

function hocwp_get_coupon_type( $post_id = null ) {
	$term   = hocwp_get_coupon_type_term( $post_id );
	$result = array();
	if ( is_a( $term, 'WP_Term' ) ) {
		switch ( $term->slug ) {
			case 'deal':
			case 'online-deal':
			case 'sale':
			case 'sales':
				$type = 'deal';
				$text = 'Deal';
				break;
			case 'in-store-coupons':
			case 'in-store-coupon':
			case 'in-store':
			case 'print':
			case 'printable':
				$type = 'printable';
				$text = 'Printable';
				break;
			default:
				$type = 'code';
				$text = 'Coupon';
		}
		$result[ $type ] = $text;
	}

	return $result;
}

function hocwp_coupon_label_html( $args = array(), $text = '', $type = '' ) {
	if ( ! is_array( $args ) ) {
		$percent = $args;
	} else {
		$calculate_percentage = (bool) hocwp_get_value_by_key( $args, 'calculate_percentage', true );
		$calculate_percentage = apply_filters( 'hocwp_calculate_coupon_percentage', $calculate_percentage, $args );
		if ( empty( $text ) ) {
			$text = hocwp_get_value_by_key( $args, 'text' );
		}
		$post_id = hocwp_get_value_by_key( $args, 'post_id', get_the_ID() );
		$percent = hocwp_get_value_by_key( $args, 'percent' );
		if ( $calculate_percentage || empty( $percent ) ) {
			$price      = hocwp_get_post_meta( 'price', $post_id );
			$sale_price = hocwp_get_post_meta( 'sale_price', $post_id );
			if ( hocwp_is_positive_number( $price ) && hocwp_is_positive_number( $sale_price ) ) {
				$percentage = hocwp_percentage( $price, $sale_price );
				$percent    = $percentage . '%';
			}
		}
	}
	?>
	<div class="coupon-label-context text-center">
		<p class="percent"><?php echo $percent; ?></p>

		<p class="text"><?php echo $text; ?></p>
	</div>
	<?php
	if ( ! empty( $type ) ) {
		?>
		<div class="coupon-type text-center">
			<span><?php echo $type; ?></span>
		</div>
		<?php
	}
}

function hocwp_coupon_filter_bar_html( $args = array() ) {
	$term           = hocwp_get_value_by_key( $args, 'term' );
	$posts_per_page = hocwp_get_value_by_key( $args, 'posts_per_page', hocwp_get_posts_per_page() );
	$code_count     = absint( hocwp_get_value_by_key( $args, 'code_count' ) );
	$deal_count     = absint( hocwp_get_value_by_key( $args, 'deal_count' ) );
	?>
	<ul data-store="<?php echo $term->term_id; ?>" data-paged="<?php echo hocwp_get_paged(); ?>"
	    data-posts-per-page="<?php echo $posts_per_page; ?>" class="filter">
		<li>
			<a href="#" data-filter="all" class="active">All (<?php echo $term->count; ?>)</a>
		</li>
		<li>
			<a href="#" data-filter="coupon-code">Coupon Codes (<?php echo $code_count; ?>)</a>
		</li>
		<li>
			<a href="#" data-filter="promotion">Deals (<?php echo $deal_count; ?>)</a>
		</li>
	</ul>
	<?php
}

function hocwp_coupon_button_html( $args = array() ) {
	$post_id      = hocwp_get_value_by_key( $args, 'post_id', get_the_ID() );
	$type         = hocwp_get_value_by_key( $args, 'type', 'deal' );
	$code_hint    = hocwp_get_value_by_key( $args, 'code_hint' );
	$type_text    = hocwp_get_value_by_key( $args, 'type_text', $type );
	$out_url      = hocwp_get_value_by_key( $args, 'out_url', hocwp_get_coupon_out_url( $post_id ) );
	$cc_label     = sprintf( __( 'Get %s', 'hocwp-theme' ), $type_text );
	$class        = hocwp_get_value_by_key( $args, 'class' );
	$button_class = 'code type-' . $type;
	hocwp_add_string_with_space_before( $button_class, $class );
	if ( 'future' == get_post_status( $post_id ) ) {
		$cc_label = __( 'Coming soon', 'hocwp-theme' );
		hocwp_add_string_with_space_before( $button_class, 'disabled' );
	}
	if ( isset( $args['expired'] ) ) {
		$expired = (bool) $args['expired'];
		if ( $expired ) {
			$cc_label = __( 'Expired', 'hocwp-theme' );
			hocwp_add_string_with_space_before( $button_class, 'disabled' );
		}
	}
	?>
	<a href="#coupon_box_<?php echo $post_id; ?>" data-post-id="<?php echo $post_id; ?>"
	   class="<?php echo $button_class; ?>" data-out-url="<?php echo $out_url; ?>" data-toggle="modal">
		<span class="cc"><?php echo $code_hint; ?></span>
		<span class="cc-label"><?php echo $cc_label; ?></span>
	</a>
	<?php
}

function hocwp_coupon_button_code_html( $args = array() ) {
	$post_id = hocwp_get_value_by_key( $args, 'post_id', get_the_ID() );
	$code    = hocwp_get_value_by_key( $args, 'code' );
	if ( empty( $code ) && hocwp_id_number_valid( $post_id ) ) {
		$code = hocwp_get_coupon_code( $post_id );
	}
	if ( empty( $code ) ) {
		return;
	}
	$out_url      = hocwp_get_value_by_key( $args, 'out_url', hocwp_get_coupon_out_url( $post_id ) );
	$button_class = hocwp_get_value_by_key( $args, 'button_class' );
	hocwp_add_string_with_space_before( $button_class, 'copy-button' );
	$input_class = hocwp_get_value_by_key( $args, 'input_class' );
	hocwp_add_string_with_space_before( $input_class, 'text' );
	?>
	<div class="code clearfix">
		<input class="<?php echo $input_class; ?>" type="text" value="<?php echo $code; ?>" readonly>
		<a class="<?php echo $button_class; ?>" data-clipboard-text="<?php echo $code; ?>"
		   data-out-url="<?php echo $out_url; ?>"
		   data-copied-text="<?php _e( 'Copied', 'hocwp-theme' ); ?>"><?php _e( 'Copy', 'hocwp-theme' ); ?></a>
	</div>
	<?php
}

function hocwp_get_coupon_vote_percent( $post_id = null ) {
	$post_id  = hocwp_return_post( $post_id, 'id' );
	$likes    = hocwp_get_post_meta( 'likes', $post_id );
	$dislikes = hocwp_get_post_meta( 'dislikes', $post_id );
	$result   = hocwp_percentage( $likes, $dislikes );
	$result   = apply_filters( 'hocwp_coupon_rating_percentage', $result, $likes, $dislikes );

	return $result;
}

function hocwp_get_coupon_total_vote( $post_id = null ) {
	$post_id  = hocwp_return_post( $post_id, 'id' );
	$likes    = hocwp_get_post_meta( 'likes', $post_id );
	$dislikes = hocwp_get_post_meta( 'dislikes', $post_id );

	return absint( absint( $likes ) + absint( $dislikes ) );
}

function hocwp_coupon_vote_comment_html( $args = array() ) {
	$result  = hocwp_get_value_by_key( $args, 'result' );
	$post_id = hocwp_get_value_by_key( $args, 'post_id', get_the_ID() );
	if ( empty( $result ) ) {
		$result = hocwp_get_coupon_vote_percent( $post_id );
		$result .= '%';
	}
	?>
	<p class="vote-result" data-post-id="<?php the_ID(); ?>">
		<i class="fa fa-thumbs-o-up"></i>
		<span><?php printf( __( '%s Success', 'hocwp-theme' ), $result ); ?></span>
	</p>
	<?php
	if ( comments_open( $post_id ) || get_comments_number( $post_id ) ) {
		$class      = 'add-comment';
		$is_current = hocwp_get_value_by_key( $args, 'is_current' );
		if ( $is_current ) {
			hocwp_add_string_with_space_before( $class, 'current-post' );
		}
		?>
		<p class="<?php echo $class; ?>">
			<a href="#respond">
				<i class="fa fa-comments-o"></i> <?php _e( 'Add a Comment', 'hocwp-theme' ); ?>
			</a>
		</p>
		<?php
	}
}

function hocwp_get_coupon_store( $post_id = null ) {
	if ( ! hocwp_id_number_valid( $post_id ) ) {
		$post_id = get_the_ID();
	}
	$term = new WP_Error();
	if ( has_term( '', 'store', $post_id ) ) {
		$terms = wp_get_post_terms( $post_id, 'store' );
		$term  = current( $terms );
	}

	return $term;
}

function hocwp_get_store_out_link( $term ) {
	if ( hocwp_id_number_valid( $term ) ) {
		$term = get_term( $term, 'store' );
	}
	$url = '';
	if ( is_a( $term, 'WP_Term' ) ) {
		$url = home_url( 'go-store/' . $term->slug );
	}

	return $url;
}

function hocwp_get_coupon_out_url( $post_id ) {
	if ( is_a( $post_id, 'WP_Post' ) ) {
		$post_id = $post_id->ID;
	}
	$url = home_url( 'out/' . $post_id );

	return $url;
}

function hocwp_get_store_by_slug( $slug ) {
	return hocwp_get_term_by_slug( $slug, 'store' );
}

function hocwp_coupon_type_select( $taxonomy = 'coupon_type' ) {
	$types = hocwp_get_terms( $taxonomy );
	?>
	<select name="stype" class="form-control select-<?php echo hocwp_sanitize_html_class( $taxonomy ); ?>"
	        autocomplete="off">
		<option value=""><?php _e( 'All coupons', 'hocwp-theme' ); ?></option>
		<?php
		if ( hocwp_array_has_value( $types ) ) {
			$stype = hocwp_get_method_value( 'stype', 'request' );
			foreach ( $types as $type ) {
				$option = hocwp_field_get_option( array(
					'text'     => $type->name,
					'value'    => $type->term_id,
					'selected' => $stype
				) );
				echo $option;
			}
		}
		?>
	</select>
	<?php
}

function hocwp_query_upcoming_coupon( $args = array() ) {
	$defaults = array(
		'post_status' => 'future',
		'post_type'   => 'coupon',
		'order'       => 'asc'
	);
	$args     = wp_parse_args( $args, $defaults );
	$query    = hocwp_query( $args );
	if ( ! $query->have_posts() ) {
		$meta_item = array(
			array(
				'key'     => 'upcoming',
				'value'   => 1,
				'type'    => 'numeric',
				'compare' => '='
			)
		);
		hocwp_query_sanitize_meta_query( $meta_item, $args );
		$args['post_status'] = 'publish';
		$query               = hocwp_query( $args );
	}

	return hocwp_query( $args );
}

function hocwp_coupon_store_select( $taxonomy = 'store' ) {
	$stores = hocwp_get_terms( $taxonomy );
	?>
	<select name="sstore" class="form-control select-<?php echo hocwp_sanitize_html_class( $taxonomy ); ?>"
	        autocomplete="off">
		<option value=""><?php echo __( 'All stores', 'hocwp-theme' ); ?></option>
		<?php
		if ( hocwp_array_has_value( $stores ) ) {
			$sstore = hocwp_get_method_value( 'sstore', 'request' );
			foreach ( $stores as $term ) {
				$option = hocwp_field_get_option( array(
					'text'     => $term->name,
					'value'    => $term->term_id,
					'selected' => $sstore
				) );
				echo $option;
			}
		}
		?>
	</select>
	<?php
}

function hocwp_build_coupon_trending_args( $args = array() ) {
	$args['post_type'] = 'coupon';
	$trends            = hocwp_get_all_trending( 'coupon' );
	if ( hocwp_array_has_value( $trends ) ) {
		$post_ids = array();
		foreach ( $trends as $trend ) {
			$post_ids[] = $trend->post_id;
		}
		$args['post__in'] = $post_ids;
		$args['orderby']  = 'post__in';
		$args['trending'] = true;
	}

	return $args;
}

function hocwp_build_coupon_similar_args( $args = array(), $stores = array(), $categories = array(), $tags = array() ) {
	$args['post_type'] = 'coupon';
	if ( hocwp_array_has_value( $stores ) ) {
		$ids = array();
		foreach ( $stores as $child ) {
			$ids[] = $child->term_id;
		}
		$tax_item = array(
			'taxonomy' => 'store',
			'field'    => 'id',
			'terms'    => $ids
		);
		$args     = hocwp_query_sanitize_tax_query( $tax_item, $args );
	}
	if ( hocwp_array_has_value( $categories ) ) {
		$ids = array();
		foreach ( $categories as $child ) {
			$ids[] = $child->term_id;
		}
		$tax_item = array(
			'taxonomy' => 'coupon_cat',
			'field'    => 'id',
			'terms'    => $ids
		);
		$args     = hocwp_query_sanitize_tax_query( $tax_item, $args );
	}
	if ( hocwp_array_has_value( $tags ) ) {
		$ids = array();
		foreach ( $tags as $child ) {
			$ids[] = $child->term_id;
		}
		$tax_item = array(
			'taxonomy' => 'coupon_tag',
			'field'    => 'id',
			'terms'    => $ids
		);
		$args     = hocwp_query_sanitize_tax_query( $tax_item, $args );
	}
	unset( $args['term_id'] );
	unset( $args['taxonomy'] );
	$args['query_coupon'] = true;

	return $args;
}

$hocwp_coupon_site = apply_filters( 'hocwp_coupon_site', false );

if ( ! (bool) $hocwp_coupon_site ) {
	return;
}

global $pagenow;

if ( 'edit-tags.php' == $pagenow || 'term.php' == $pagenow ) {
	hocwp_term_meta_different_name_field( array( 'store', 'coupon_cat' ) );
	hocwp_term_meta_thumbnail_field( array( 'store' ) );
	$meta = new HOCWP_Meta( 'term' );
	$meta->set_taxonomies( array( 'store' ) );
	$meta->add_field( array( 'id' => 'site', 'label' => __( 'Store URL', 'hocwp-theme' ) ) );
	$meta->init();
}

function hocwp_coupon_meta_box_init( $post ) {
	global $pagenow;
	$post_type = $post->post_type;
	if ( 'post-new.php' == $pagenow || 'post.php' == $pagenow ) {
		hocwp_meta_box_post_attribute( array( 'coupon' ) );
		$meta = new HOCWP_Meta( 'post' );
		$meta->set_post_types( array( 'coupon' ) );
		$meta->set_id( 'hocwp_coupon_information' );
		$meta->set_title( __( 'Coupon Information', 'hocwp-theme' ) );
		$meta->add_field(
			array(
				'id'    => 'percent_label',
				'label' => __( 'Percent Label:', 'hocwp-theme' )
			)
		);
		$meta->add_field(
			array(
				'id'    => 'text_label',
				'label' => __( 'Text Label:', 'hocwp-theme' )
			)
		);
		$meta->add_field(
			array(
				'id'             => 'price',
				'label'          => __( 'Price:', 'hocwp-theme' ),
				'field_callback' => 'hocwp_field_input_number'
			)
		);
		$meta->add_field(
			array(
				'id'             => 'sale_price',
				'label'          => __( 'Sale Price:', 'hocwp-theme' ),
				'field_callback' => 'hocwp_field_input_number'
			)
		);
		$meta->add_field(
			array(
				'id'    => 'coupon_code',
				'label' => __( 'Code:', 'hocwp-theme' )
			)
		);
		$meta->add_field(
			array(
				'id'             => 'expired_date',
				'label'          => __( 'Expires:', 'hocwp-theme' ),
				'field_callback' => 'hocwp_field_datetime_picker',
				'data_type'      => 'timestamp',
				'min_date'       => 0,
				'date_format'    => 'm/d/Y'
			)
		);

		$meta->add_field(
			array(
				'id'    => 'url',
				'label' => __( 'URL:', 'hocwp-theme' )
			)
		);
		$args = array(
			'id'             => 'enable_countdown_timer',
			'label'          => 'Enable the countdown timer?',
			'field_callback' => 'hocwp_field_input_checkbox'
		);
		$meta->add_field( $args );
		$meta->init();
	}
}

add_action( 'hocwp_coupon_meta_boxes', 'hocwp_coupon_meta_box_init' );

function hocwp_coupon_on_save_post( $post_id ) {
	if ( ! hocwp_can_save_post( $post_id ) ) {
		return;
	}
	$current_post = get_post( $post_id );
	if ( ! has_term( '', 'coupon_type', $post_id ) && ! empty( $current_post->post_title ) ) {
		wp_set_object_terms( $post_id, 'Promo Codes', 'coupon_type' );
	}
	if ( 'coupon' == $current_post->post_type ) {
		$timestamp = '';
		if ( isset( $_POST['expired_date'] ) ) {
			$timestamp = strtotime( $_POST['expired_date'] );
			update_post_meta( $post_id, 'expired_date', $timestamp );
		}
		$event = hocwp_get_method_value( 'event' );
		update_post_meta( $post_id, 'event', $event );
		$expiry_interval = hocwp_get_method_value( 'expiry_interval' );
		if ( hocwp_id_number_valid( $expiry_interval ) || ! empty( $timestamp ) ) {
			$expiry_interval_saved     = hocwp_get_coupon_meta( 'expiry_interval', $post_id );
			$expiry_interval_timestamp = hocwp_get_coupon_meta( 'expiry_interval_timestamp', $post_id );
			if ( ! empty( $timestamp ) ) {
				$datetime  = hocwp_get_current_datetime_mysql( strtotime( "+$expiry_interval sec" ) );
				$timestamp = strtotime( $datetime );
			}
			if ( $expiry_interval != $expiry_interval_saved || empty( $expiry_interval_timestamp ) || $timestamp != $expiry_interval_timestamp ) {
				update_post_meta( $post_id, 'expiry_interval_timestamp', $timestamp );
			}
		}
		if ( isset( $_POST['price'] ) ) {
			$price      = hocwp_get_method_value( 'price' );
			$sale_price = hocwp_get_method_value( 'sale_price' );
			if ( ! hocwp_is_positive_number( $sale_price ) || ( hocwp_is_positive_number( $price ) && $sale_price >= $price ) ) {
				$sale_price = '';
			}
			update_post_meta( $post_id, 'price', $price );
			update_post_meta( $post_id, 'sale_price', $sale_price );
		}
		if ( isset( $_POST['percent_label'] ) ) {
			update_post_meta( $post_id, 'percent_label', $_POST['percent_label'] );
		}
		if ( isset( $_POST['text_label'] ) ) {
			update_post_meta( $post_id, 'text_label', $_POST['text_label'] );
		}
		if ( isset( $_POST['coupon_code'] ) ) {
			update_post_meta( $post_id, 'coupon_code', $_POST['coupon_code'] );
		}
		if ( isset( $_POST['url'] ) ) {
			update_post_meta( $post_id, 'url', $_POST['url'] );
		}
		$enable_countdown_timer = hocwp_get_method_value( 'enable_countdown_timer' );
		update_post_meta( $post_id, 'enable_countdown_timer', $enable_countdown_timer );
	}
}

add_action( 'save_post', 'hocwp_coupon_on_save_post', 99 );

function hocwp_coupon_update_post_class( $classes ) {
	global $post;
	if ( 'coupon' == $post->post_type ) {
		$post_id = $post->ID;
		$type    = hocwp_get_coupon_type( $post_id );
		$type    = array_search( current( $type ), $type );
		if ( ! empty( $type ) ) {
			$classes[] = 'coupon-type-' . $type;
			if ( 'code' == $type ) {
				$code = hocwp_get_coupon_code( $post_id );
				if ( empty( $code ) ) {
					$classes[] = 'coupon-no-code';
				}
			}
		}
	}

	return $classes;
}

add_filter( 'post_class', 'hocwp_coupon_update_post_class' );

function hocwp_coupon_on_init_hook() {
	add_rewrite_endpoint( 'go-store', EP_ALL );
	add_rewrite_endpoint( 'out', EP_ALL );
}

add_action( 'init', 'hocwp_coupon_on_init_hook' );

function hocwp_coupon_on_wp_hook() {
	$store = get_query_var( 'go-store' );
	if ( ! empty( $store ) ) {
		$term = hocwp_get_store_by_slug( $store );
		if ( is_a( $term, 'WP_Term' ) ) {
			$url = hocwp_get_store_url( $term->term_id );
			if ( ! empty( $url ) ) {
				wp_redirect( $url );
				exit;
			} else {
				wp_redirect( home_url( '/' ) );
				exit;
			}
		}
	}
	$out = get_query_var( 'out' );
	if ( ! empty( $out ) ) {
		$url = hocwp_get_coupon_url( $out );
		if ( ! empty( $url ) ) {
			wp_redirect( $url );
			exit;
		} else {
			wp_redirect( home_url( '/' ) );
			exit;
		}
	}
}

add_action( 'wp', 'hocwp_coupon_on_wp_hook' );

function hocwp_coupon_build_ongoing_deal_args( $args = array() ) {
	$meta_item                      = array(
		'relation' => 'OR',
		array(
			'key'     => 'expired_date',
			'compare' => 'NOT EXISTS'
		),
		array(
			'key'     => 'expired_date',
			'value'   => 0,
			'type'    => 'numeric',
			'compare' => '='
		)
	);
	$args                           = hocwp_query_sanitize_meta_query( $meta_item, $args );
	$args['post_type']              = 'coupon';
	$args['meta_query']['relation'] = 'AND';

	return $args;
}

function hocwp_coupon_build_daily_deal_args( $args = array() ) {
	$current_date                   = strtotime( hocwp_get_current_datetime_mysql() );
	$meta_item                      = array(
		'relation' => 'AND',
		array(
			'key'     => 'expiry_interval_timestamp',
			'compare' => 'EXISTS'
		),
		array(
			'key'     => 'expiry_interval_timestamp',
			'value'   => $current_date,
			'type'    => 'numeric',
			'compare' => '>'
		)
	);
	$args                           = hocwp_query_sanitize_meta_query( $meta_item, $args );
	$args['post_type']              = 'coupon';
	$args['meta_query']['relation'] = 'AND';

	return $args;
}

function hocwp_coupon_build_not_expired_query_args( $args = array() ) {
	$meta_query = hocwp_coupon_build_not_expired_meta_query();
	if ( isset( $args['meta_query'] ) ) {
		$meta_query = array(
			$meta_query,
			$args['meta_query']
		);

	}
	$args['meta_query'] = $meta_query;

	return $args;
}

function hocwp_coupon_build_not_expired_meta_query() {
	$timestamp  = strtotime( hocwp_get_current_datetime_mysql() );
	$meta_item  = array(
		'relation' => 'OR',
		array(
			'key'     => 'expired_date',
			'value'   => $timestamp,
			'type'    => 'numeric',
			'compare' => '>='
		),
		array(
			'key'     => 'expired_date',
			'compare' => 'NOT EXISTS'
		),
		array(
			'key'     => 'expired_date',
			'value'   => 0,
			'type'    => 'numeric',
			'compare' => '='
		)
	);
	$meta_query = array(
		$meta_item
	);

	return $meta_query;
}

function hocwp_coupon_is_expired_query( $query_vars ) {
	$expired_coupon = (bool) hocwp_get_value_by_key( $query_vars, 'expired_coupon' );
	if ( ! $expired_coupon ) {
		$meta_query = hocwp_get_value_by_key( $query_vars, 'meta_query' );
		if ( hocwp_array_has_value( $meta_query ) ) {
			foreach ( $meta_query as $meta ) {
				if ( hocwp_array_has_value( $meta ) ) {
					foreach ( $meta as $child_meta ) {
						if ( hocwp_array_has_value( $child_meta ) ) {
							$key     = hocwp_get_value_by_key( $child_meta, 'key' );
							$value   = hocwp_get_value_by_key( $child_meta, 'value' );
							$compare = hocwp_get_value_by_key( $child_meta, 'compare' );
							if ( 'expired_date' == $key && is_numeric( $value ) && '<' == $compare ) {
								$expired_coupon = true;
								break;
							}
						}
					}
				}
			}
		}
	}

	return (bool) $expired_coupon;
}

function hocwp_coupon_pre_get_posts( WP_Query $query ) {
	if ( $query->is_main_query() ) {
		if ( is_tax( 'store' ) ) {
			$posts_per_page = apply_filters( 'hocwp_archive_coupon_posts_per_page', 15 );
			$query->set( 'posts_per_page', $posts_per_page );
		} elseif ( is_search() ) {
			$query->set( 'post_type', 'coupon' );
			$stype     = hocwp_get_method_value( 'stype', 'request' );
			$tax_query = $query->get( 'tax_query' );
			if ( ! is_array( $tax_query ) ) {
				$tax_query = array();
			}
			if ( hocwp_id_number_valid( $stype ) ) {
				$tax_query['relation'] = 'AND';
				$tax_item              = array(
					'taxonomy' => 'coupon_type',
					'field'    => 'id',
					'terms'    => $stype
				);
				$tax_query[]           = $tax_item;
				$query->set( 'tax_query', $tax_query );
			}
			$sstore = hocwp_get_method_value( 'sstore', 'request' );
			if ( hocwp_id_number_valid( $sstore ) ) {
				$tax_query['relation'] = 'AND';
				$tax_item              = array(
					'taxonomy' => 'store',
					'field'    => 'id',
					'terms'    => $sstore
				);
				$tax_query[]           = $tax_item;
				$query->set( 'tax_query', $tax_query );
			}
		} elseif ( is_home() ) {
			$query->set( 'post_type', 'coupon' );
		}
		$coupon_list = false;
		if ( is_post_type_archive( 'coupon' ) || is_search() || is_tax( 'store' ) || is_tax( 'coupon_cat' ) || is_tax( 'coupon_tag' ) ) {
			$coupon_list = true;
		} elseif ( is_home() ) {
			$coupon_list = true;
		}
		if ( $coupon_list ) {
			$exclude_expired = apply_filters( 'hocwp_exclude_expired_coupon', false );
			if ( $exclude_expired ) {
				$query_vars     = $query->query_vars;
				$expired_coupon = hocwp_coupon_is_expired_query( $query_vars );
				if ( ! $expired_coupon ) {
					$args           = hocwp_coupon_build_not_expired_meta_query();
					$old_meta_query = $query->get( 'meta_query' );
					if ( is_array( $old_meta_query ) ) {
						$args = wp_parse_args( $args, $old_meta_query );
					}
					$query->set( 'meta_query', $args );
				}
			}
		}
	} else {
		$query_coupon = $query->get( 'query_coupon' );
		if ( (bool) $query_coupon ) {
			$exclude_expired = apply_filters( 'hocwp_exclude_expired_coupon', false );
			if ( $exclude_expired ) {
				$query_vars     = $query->query_vars;
				$expired_coupon = hocwp_coupon_is_expired_query( $query_vars );
				if ( ! $expired_coupon ) {
					$args           = hocwp_coupon_build_not_expired_meta_query();
					$old_meta_query = $query->get( 'meta_query' );
					if ( is_array( $old_meta_query ) ) {
						$args = wp_parse_args( $args, $old_meta_query );
					}
					$query->set( 'meta_query', $args );
				}
			}
		}
	}

	return $query;
}

if ( ! is_admin() ) {
	add_action( 'pre_get_posts', 'hocwp_coupon_pre_get_posts' );
}

function hocwp_coupon_filter_ajax_callback() {
	$result = array(
		'have_posts' => false
	);
	$term   = hocwp_get_method_value( 'term' );
	$filter = hocwp_get_method_value( 'filter' );
	if ( hocwp_id_number_valid( $term ) ) {
		$posts_per_page = hocwp_get_method_value( 'posts_per_page' );
		$paged          = hocwp_get_method_value( 'paged' );
		$args           = array(
			'post_type'      => 'coupon',
			'posts_per_page' => $posts_per_page,
			'paged'          => $paged,
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'store',
					'field'    => 'id',
					'terms'    => array( $term )
				)
			)
		);
		$type_object    = new WP_Error();
		switch ( $filter ) {
			case 'coupon-code';
				$type_object = hocwp_get_coupon_type_object();
				break;
			case 'promotion':
				$type_object = hocwp_get_coupon_type_object( 'deal' );
				break;
		}
		if ( is_a( $type_object, 'WP_Term' ) ) {
			$tax_item = array(
				'taxonomy' => 'coupon_type',
				'field'    => 'id',
				'terms'    => array( $type_object->term_id )
			);
			$args     = hocwp_query_sanitize_tax_query( $tax_item, $args );
		}
		$query                = hocwp_query( $args );
		$result['have_posts'] = $query->have_posts();
		if ( $query->have_posts() ) {
			$html_data = '';
			while ( $query->have_posts() ) {
				$query->the_post();
				ob_start();
				hocwp_theme_get_loop( 'archive-coupon' );
				$html_data .= ob_get_clean();
			}
			wp_reset_postdata();
			$result['html_data'] = $html_data;
		}
	}
	echo json_encode( $result );
	exit;
}

add_action( 'wp_ajax_hocwp_coupon_filter', 'hocwp_coupon_filter_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_coupon_filter', 'hocwp_coupon_filter_ajax_callback' );

function hocwp_coupon_attribute_meta_box_field( $meta ) {
	if ( ! is_object( $meta ) ) {
		return;
	}
	global $post;
	$meta_id = $post->post_type . '_attributes';
	$meta_id = hocwp_sanitize_id( $meta_id );
	if ( 'coupon' == $post->post_type && $meta->get_id() == $meta_id ) {
		$query      = hocwp_query( array( 'post_type' => 'event', 'posts_per_page' => - 1 ) );
		$all_option = '<option value=""></option>';
		$selected   = get_post_meta( $post->ID, 'event', true );
		foreach ( $query->posts as $qpost ) {
			$all_option .= hocwp_field_get_option( array(
				'value'    => $qpost->ID,
				'text'     => $qpost->post_title,
				'selected' => $selected
			) );
		}
		$args = array(
			'id'          => 'event_chosen',
			'name'        => 'event',
			'all_option'  => $all_option,
			'value'       => $selected,
			'class'       => 'widefat',
			'label'       => hocwp_uppercase_first_char_only( 'Event' ) . ':',
			'placeholder' => __( 'Choose parent post', 'hocwp-theme' )
		);
		hocwp_field_select_chosen( $args );
	}
}

add_action( 'hocwp_post_meta_box_field', 'hocwp_coupon_attribute_meta_box_field' );

if ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) {
	add_filter( 'hocwp_use_chosen_select', '__return_true' );
}

if ( 'options-permalink.php' == $pagenow || true ) {
	$data   = get_option( 'hocwp_permalink' );
	$option = new HOCWP_Option( '', 'permalink' );
	$option->set_parent_slug( 'options-permalink.php' );
	$option->set_update_option( true );
	$option->add_field( array(
		'value'       => hocwp_get_value_by_key( $data, 'coupon_store_base' ),
		'id'          => 'coupon_store_base',
		'title'       => __( 'Coupon store base', 'hocwp-theme' ),
		'section'     => 'optional',
		'placeholder' => hocwp_coupon_store_base()
	) );
	$option->add_field( array(
		'value'       => hocwp_get_value_by_key( $data, 'coupon_category_base' ),
		'id'          => 'coupon_category_base',
		'title'       => __( 'Coupon category base', 'hocwp-theme' ),
		'section'     => 'optional',
		'placeholder' => hocwp_coupon_category_base()
	) );
	$option->add_field( array(
		'value'       => hocwp_get_value_by_key( $data, 'coupon_tag_base' ),
		'id'          => 'coupon_tag_base',
		'title'       => __( 'Coupon tag base', 'hocwp-theme' ),
		'section'     => 'optional',
		'placeholder' => hocwp_coupon_tag_base()
	) );
	$option->add_field( array(
		'value'       => hocwp_get_value_by_key( $data, 'coupon_type_base' ),
		'id'          => 'coupon_type_base',
		'title'       => __( 'Coupon type base', 'hocwp-theme' ),
		'section'     => 'optional',
		'placeholder' => hocwp_coupon_type_base()
	) );
	$option->init();
}

function hocwp_coupon_filter_taxonomy_base( $base, $taxonomy ) {
	switch ( $taxonomy ) {
		case 'store':
			$base = 'store';
			break;
		case 'coupon_cat':
			$base = 'coupon_cat';
			break;
	}

	return $base;
}

add_filter( 'hocwp_remove_term_base_taxonomy_base', 'hocwp_coupon_filter_taxonomy_base', 10, 2 );

function hocwp_coupon_init() {
	hocwp_coupon_install_post_type_and_taxonomy();
}

add_action( 'init', 'hocwp_coupon_init' );

function hocwp_coupon_bulk_actions( $post_type ) {
	if ( 'coupon' == $post_type ) {
		hocwp_add_bulk_action( array( 'reset_rating' => __( 'Reset rating', 'hocwp-theme' ) ) );
	}
}

add_action( 'hocwp_admin_footer_edit', 'hocwp_coupon_bulk_actions' );

function hocwp_coupon_load_edit_bulk_action( $action ) {
	switch ( $action ) {
		case 'reset_rating':
			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_die( __( 'You are not allowed to reset rating of this post.', 'hocwp-theme' ) );
			}
			$reset_rating = 0;
			$post_ids     = hocwp_get_method_value( 'post', 'get' );
			if ( hocwp_array_has_value( $post_ids ) ) {
				foreach ( $post_ids as $post_id ) {
					update_post_meta( $post_id, 'likes', 0 );
					update_post_meta( $post_id, 'dislikes', 0 );
					$reset_rating ++;
				}
				$sendback = add_query_arg( array(
					'reset_rating' => $reset_rating,
					'ids'          => join( ',', $post_ids )
				), wp_get_referer() );
				wp_redirect( $sendback );
				exit;
			} else {
				wp_die( __( 'Please select post first.', 'hocwp-theme' ) );
			}
			break;
	}
}

add_action( 'hocwp_load_edit_bulk_action', 'hocwp_coupon_load_edit_bulk_action' );

function hocwp_coupon_bulk_action_admin_notices() {
	global $post_type, $pagenow;
	if ( 'edit.php' == $pagenow && 'coupon' == $post_type ) {
		$reset_rating = hocwp_get_method_value( 'reset_rating', 'request' );
		if ( hocwp_id_number_valid( $reset_rating ) ) {
			$message = hocwp_get_text_base_on_number( __( 'Post rating reseted.', 'hocwp-theme' ), __( '%s posts was reseted rating.', 'hocwp-theme' ), $reset_rating );
			hocwp_admin_notice( array( 'text' => $message ) );
		}
	}
}

add_action( 'admin_notices', 'hocwp_coupon_bulk_action_admin_notices' );

function hocwp_coupon_widget_post_bys( $bys ) {
	$bys['best_deal']     = __( 'Best deals', 'hocwp-theme' );
	$bys['trending_deal'] = __( 'Trending deals', 'hocwp-theme' );

	return $bys;
}

add_filter( 'hocwp_widget_post_query_bys', 'hocwp_coupon_widget_post_bys' );

function hocwp_coupon_widget_post_get_by_check_status( $status, $args, $instance, $widget ) {
	$by = trim( hocwp_get_value_by_key( $widget->instance, 'by' ) );
	if ( 'best_deal' == $by || 'trending_deal' == $by ) {
		$status = true;
	}

	return $status;
}

add_filter( 'hocwp_widget_post_get_by_check_status', 'hocwp_coupon_widget_post_get_by_check_status', 10, 4 );

function hocwp_coupon_widget_post_query_args( $query_args, $args, $instance, $widget ) {
	$by = trim( hocwp_get_value_by_key( $widget->instance, 'by' ) );
	if ( 'best_deal' == $by ) {
		$meta_item              = array(
			'key'  => 'likes',
			'type' => 'numeric'
		);
		$query_args             = hocwp_query_sanitize_meta_query( $meta_item, $query_args );
		$query_args['orderby']  = 'meta_value_num';
		$query_args['order']    = 'desc';
		$query_args['meta_key'] = 'likes';
	} elseif ( 'trending_deal' == $by ) {
		$query_args = hocwp_build_coupon_trending_args( $query_args );
	}

	return $query_args;
}

add_filter( 'hocwp_widget_post_query_args', 'hocwp_coupon_widget_post_query_args', 10, 4 );

function hocwp_coupon_wp_action() {
	if ( is_singular( 'coupon' ) ) {
		global $hocwp_reading_options;
		$reading  = $hocwp_reading_options;
		$trending = hocwp_get_value_by_key( $reading, 'trending' );
		if ( (bool) $trending ) {
			hocwp_insert_trending( get_the_ID() );
		}
	}
	if ( is_user_logged_in() ) {
		global $hocwp_user_saved_posts;
		$user                   = wp_get_current_user();
		$hocwp_user_saved_posts = hocwp_get_user_saved_posts( $user->ID );
		$hocwp_user_saved_posts = hocwp_sanitize_array( $hocwp_user_saved_posts );
		$hocwp_user_saved_posts = array_filter( $hocwp_user_saved_posts );
	}
}

add_action( 'wp', 'hocwp_coupon_wp_action' );

function hocwp_coupon_post_type_user_large_thumbnail( $types ) {
	$types[] = 'coupon';

	return $types;
}

add_filter( 'hocwp_post_type_user_large_thumbnail', 'hocwp_coupon_post_type_user_large_thumbnail' );

function hocwp_load_more_coupon_ajax_callback() {
	$result     = array( 'have_posts' => false, 'no_more' => false );
	$query_vars = hocwp_get_method_value( 'query_vars' );
	$query_vars = hocwp_json_string_to_array( $query_vars );
	if ( hocwp_array_has_value( $query_vars ) ) {
		$paged          = hocwp_get_value_by_key( $query_vars, 'paged' );
		$posts_per_page = hocwp_get_value_by_key( $query_vars, 'posts_per_page', hocwp_get_posts_per_page() );
		if ( ! is_numeric( $paged ) ) {
			$paged = 1;
		}
		$paged ++;
		$query_vars['paged']     = $paged;
		$query_vars['post_type'] = 'coupon';
		$query                   = hocwp_query( $query_vars );
		if ( $query->have_posts() ) {
			$result['have_posts'] = true;
			$result['html_data']  = apply_filters( 'hocwp_load_more_coupon_loop', '', $query );
		}
		$result['query_vars'] = json_encode( $query->query_vars );
		$no_more              = false;
		if ( $query->post_count < $posts_per_page || $query->found_posts <= $posts_per_page ) {
			$no_more = true;
		}
		$result['no_more'] = $no_more;
	}
	wp_send_json( $result );
}

add_action( 'wp_ajax_hocwp_load_more_coupon', 'hocwp_load_more_coupon_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_load_more_coupon', 'hocwp_load_more_coupon_ajax_callback' );