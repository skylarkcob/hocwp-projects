<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

function hocwp_get_term_link( $term ) {
	return '<a href="' . esc_url( get_term_link( $term ) ) . '" rel="category ' . hocwp_sanitize_html_class( $term->taxonomy ) . ' tag">' . $term->name . '</a>';
}

function hocwp_the_terms( $args = array() ) {
	$terms  = hocwp_get_value_by_key( $args, 'terms' );
	$before = hocwp_get_value_by_key( $args, 'before' );
	$sep    = hocwp_get_value_by_key( $args, 'separator', ', ' );
	$after  = hocwp_get_value_by_key( $args, 'after' );
	if ( hocwp_array_has_value( $terms ) ) {
		echo $before;
		$html = '';
		foreach ( $terms as $term ) {
			$html .= hocwp_get_term_link( $term ) . $sep;
		}
		$html = trim( $html, $sep );
		echo $html;
		echo $after;
	} else {
		$post_id    = hocwp_get_value_by_key( $args, 'post_id', get_the_ID() );
		$taxonomy   = hocwp_get_value_by_key( $args, 'taxonomy' );
		$taxonomies = hocwp_get_value_by_key( $args, 'taxonomies' );
		if ( ! is_array( $taxonomies ) ) {
			$taxonomies = array();
		}
		if ( ! empty( $taxonomy ) && ! in_array( $taxonomy, $taxonomies ) ) {
			$taxonomies[] = $taxonomy;
		}
		echo $before;
		ob_start();
		foreach ( $taxonomies as $taxonomy ) {
			the_terms( $post_id, $taxonomy, '', $sep, '' );
			echo $sep;
		}
		$html = ob_get_clean();
		$html = trim( $html, $sep );
		echo $html;
		echo $after;
	}
}

function hocwp_get_hierarchical_terms( $taxonomies, $args = array() ) {
	if ( ! hocwp_array_has_value( $taxonomies ) ) {
		$taxonomies = array( 'category' );
	}
	$args['hierarchical'] = true;

	return hocwp_get_terms( $taxonomies, $args );
}

function hocwp_get_taxonomies( $args = array() ) {
	return get_taxonomies( $args, 'objects' );
}

function hocwp_get_hierarchical_taxonomies( $args = array() ) {
	$args['hierarchical'] = true;

	return hocwp_get_taxonomies( $args );
}

function hocwp_get_term_meta( $key, $term_id ) {
	return get_term_meta( $term_id, $key, true );
}

function hocwp_term_name( $term ) {
	echo hocwp_term_get_name( $term );
}

function hocwp_term_get_name( $term ) {
	$name = '';
	if ( is_a( $term, 'WP_Term' ) ) {
		$name           = $term->name;
		$different_name = hocwp_get_term_meta( 'different_name', $term->term_id );
		if ( ! empty( $different_name ) ) {
			$name = strip_tags( $different_name );
		}
		$name = apply_filters( 'hocwp_term_name', $name, $term );
	}

	return $name;
}

function hocwp_term_link_html( $term ) {
	return hocwp_get_term_link( $term );
}

function hocwp_term_link_li_html( $term ) {
	$link = hocwp_term_link_html( $term );
	$link = hocwp_wrap_tag( $link, 'li' );

	return $link . PHP_EOL;
}

function hocwp_term_get_thumbnail_url_helper( $term, $from_parent = false ) {
	if ( ! is_a( $term, 'WP_Term' ) ) {
		return '';
	}
	$term_id = $term->term_id;
	$value   = get_term_meta( $term_id, 'thumbnail', true );
	$value   = hocwp_sanitize_media_value( $value );
	$value   = $value['url'];
	if ( empty( $value ) && $from_parent ) {
		$parent_id = $term->parent;
		while ( empty( $value ) && $parent_id > 0 ) {
			$parent    = get_term( $parent_id, $term->taxonomy );
			$value     = hocwp_term_get_thumbnail_url_helper( $parent, $from_parent );
			$parent_id = $parent->parent;
		}
	}

	return $value;
}

function hocwp_term_has_thumbnail( $term, $from_parent = false ) {
	$value = hocwp_term_get_thumbnail_url_helper( $term, $from_parent );
	if ( empty( $value ) ) {
		return false;
	}

	return true;
}

function hocwp_term_get_thumbnail_url( $args = array() ) {
	if ( hocwp_id_number_valid( $args ) ) {
		$term_id = $args;
	} else {
		$term_id = hocwp_get_value_by_key( $args, 'term_id' );
	}
	$term = hocwp_get_value_by_key( $args, 'term' );
	if ( ! hocwp_id_number_valid( $term_id ) ) {
		if ( is_a( $term, 'WP_Term' ) ) {
			$term_id = $term->term_id;
		}
	}
	if ( ! hocwp_id_number_valid( $term_id ) ) {
		$term_id = 0;
	}
	$value                      = get_term_meta( $term_id, 'thumbnail', true );
	$use_default_term_thumbnail = apply_filters( 'hocwp_use_default_term_thumbnail', hocwp_get_value_by_key( $args, 'use_default_thumbnail', true ) );
	$value                      = hocwp_sanitize_media_value( $value );
	$value                      = $value['url'];
	$icon                       = false;
	if ( empty( $value ) ) {
		$icon_url = hocwp_get_term_icon( $term_id );
		$value    = $icon_url;
		if ( ! empty( $value ) ) {
			$icon = true;
		}
	}
	if ( ! $icon ) {
		if ( empty( $value ) ) {
			$from_parent = hocwp_get_value_by_key( $args, 'from_parent' );
			if ( (bool) $from_parent ) {
				$value = hocwp_term_get_thumbnail_url_helper( $term, true );
			}
		}
		if ( empty( $value ) && (bool) $use_default_term_thumbnail ) {
			$value = hocwp_get_image_url( 'no-thumbnail.png' );
		}
		$bfi_thumb = hocwp_get_value_by_key( $args, 'bfi_thumb', true );
		if ( (bool) $bfi_thumb ) {
			$size   = hocwp_sanitize_size( $args );
			$params = array();
			$width  = $size[0];
			if ( hocwp_id_number_valid( $width ) ) {
				$params['width'] = $width;
			}
			$height = $size[1];
			if ( hocwp_id_number_valid( $height ) ) {
				$params['height'] = $height;
			}
			$crop           = hocwp_get_value_by_key( $args, 'crop', true );
			$params['crop'] = $crop;
			if ( $width > 0 || $height > 0 ) {
				$value = bfi_thumb( $value, $params );
			}
		}
	}

	return apply_filters( 'hocwp_term_thumbnail', $value, $term_id );
}

function hocwp_term_get_thumbnail_html( $args = array() ) {
	$thumb_url = hocwp_term_get_thumbnail_url( $args );
	$result    = '';
	$term      = hocwp_get_value_by_key( $args, 'term' );
	if ( ! empty( $thumb_url ) ) {
		$taxonomy = hocwp_get_value_by_key( $args, 'taxonomy' );
		if ( ! is_a( $term, 'WP_Term' ) ) {
			$term_id = hocwp_get_value_by_key( $args, 'term_id' );
			if ( hocwp_id_number_valid( $term_id ) && ! empty( $taxonomy ) ) {
				$term = get_term( $term_id, $taxonomy );
			}
		}
		if ( is_a( $term, 'WP_Term' ) ) {
			$taxonomy  = $term->taxonomy;
			$size      = hocwp_sanitize_size( $args );
			$link      = hocwp_get_value_by_key( $args, 'link', true );
			$show_name = hocwp_get_value_by_key( $args, 'show_name' );
			$img       = new HOCWP_HTML( 'img' );
			$img->set_image_src( $thumb_url );
			if ( $size[0] > 0 ) {
				$img->set_attribute( 'width', $size[0] );
			}
			if ( $size[1] > 0 ) {
				$img->set_attribute( 'height', $size[1] );
			}
			$class = 'img-responsive wp-term-image';
			$slug  = $term->taxonomy;
			hocwp_add_string_with_space_before( $class, hocwp_sanitize_html_class( $slug ) . '-thumb' );
			$img->set_class( $class );
			$link_text = $img->build();
			if ( (bool) $show_name ) {
				$link_text .= '<span class="term-name">' . $term->name . '</span>';
			}
			$a = new HOCWP_HTML( 'a' );
			$a->set_class( 'term-link ' . hocwp_sanitize_html_class( $taxonomy ) );
			$a->set_text( $link_text );
			$a->set_attribute( 'title', $term->name );
			$a->set_href( get_term_link( $term ) );
			if ( ! (bool) $link ) {
				$result = $img->build();
			} else {
				$result = $a->build();
			}
		}
	}

	return apply_filters( 'hocwp_term_thumbnail_html', $result, $term );
}

function hocwp_term_the_thumbnail( $args = array() ) {
	echo hocwp_term_get_thumbnail_html( $args );
}

function hocwp_term_get_banner_url( $args = array() ) {
	if ( hocwp_id_number_valid( $args ) ) {
		$args = array( 'term_id' => $args );
	}
	$term_id = hocwp_get_value_by_key( $args, 'term_id' );
	$key     = hocwp_get_value_by_key( $args, 'key', 'banner' );
	$media   = hocwp_get_term_meta( 'banner', $term_id );
	$media   = hocwp_sanitize_media_value( $media );
	$url     = $media['url'];
	if ( 'horizontal_banner' == $key || 'vertical_banner' == $key ) {
		$banner = hocwp_get_term_meta( $key, $term_id );
		$banner = hocwp_sanitize_media_value( $banner );
		if ( ! empty( $banner['url'] ) ) {
			$url = $banner['url'];
		}
	}

	return apply_filters( 'hocwp_term_banner_url', $url, $term_id, $args );
}

function hocwp_term_get_current() {
	return get_queried_object();
}

function hocwp_term_get_current_id() {
	return get_queried_object_id();
}

function hocwp_term_get_top_most_parent_ids( $term ) {
	$term_ids = array();
	if ( is_a( $term, 'WP_Term' ) ) {
		$term_ids = get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' );
	}

	return $term_ids;
}

function hocwp_term_get_top_most_parent( $term ) {
	$term_ids = hocwp_term_get_top_most_parent_ids( $term );
	$term_id  = array_shift( $term_ids );
	$parent   = '';
	if ( hocwp_id_number_valid( $term_id ) ) {
		$parent = get_term( $term_id, $term->taxonomy );
	}

	return $parent;
}

function hocwp_term_get_by_count( $taxonomy = 'category', $args = array() ) {
	$result          = array();
	$args['orderby'] = 'count';
	$args['order']   = 'DESC';
	$terms           = hocwp_get_terms( $taxonomy, $args );
	if ( hocwp_array_has_value( $terms ) ) {
		$result = $terms;
	}

	return $result;
}

function hocwp_get_term_by_slug( $slug, $taxonomy = 'category' ) {
	return get_term_by( 'slug', $slug, $taxonomy );
}

function hocwp_insert_term( $term, $taxonomy, $args = array() ) {
	$override = hocwp_get_value_by_key( $args, 'override', false );
	if ( ! $override ) {
		$exists = get_term_by( 'name', $term, $taxonomy );
		if ( is_a( $exists, 'WP_Term' ) ) {
			return;
		}
	}
	wp_insert_term( $term, $taxonomy, $args );
}

function hocwp_get_term_icon( $term_id ) {
	$icon = hocwp_get_term_meta( 'icon_html', $term_id );
	if ( empty( $icon ) ) {
		$icon = hocwp_get_term_meta( 'icon', $term_id );
		$icon = hocwp_sanitize_media_value( $icon );
		$icon = $icon['url'];
	}

	return $icon;
}

function hocwp_term_icon_html( $term_id, $default = '' ) {
	$icon = hocwp_get_term_icon( $term_id );
	$icon = hocwp_sanitize_media_value( $icon );
	$icon = $icon['url'];
	if ( empty( $icon ) ) {
		$icon = $default;
	}
	if ( hocwp_string_contain( $icon, 'fa' ) || hocwp_string_contain( $icon, '</i>' ) ) {
		echo $icon;
	} else {
		if ( ! empty( $icon ) ) {
			$img = new HOCWP_HTML( 'img' );
			$img->set_image_src( $icon );
			$img->output();
		}
	}
}

function hocwp_get_child_terms( $parent_id, $taxonomy, $args = array() ) {
	$args['child_of'] = $parent_id;
	$terms            = hocwp_get_terms( $taxonomy, $args );

	return $terms;
}

function hocwp_get_related_term( $taxonomy, $term_id = null ) {
	$term   = hocwp_return_term( $taxonomy, $term_id );
	$result = array();
	if ( is_a( $term, 'WP_Term' ) ) {
		$tmp = $term;
		while ( $tmp->parent > 0 ) {
			$aterm = get_term( $tmp->parent, $taxonomy );
			if ( ! isset( $result[ $aterm->term_id ] ) ) {
				$result[ $aterm->term_id ] = $aterm;
			}
			$tmp = $aterm;
		}
	}

	return $result;
}

function hocwp_return_term( $taxonomy, $term_id = null, $output = OBJECT ) {
	if ( 'id' == strtolower( $output ) && hocwp_id_number_valid( $term_id ) ) {
		return $term_id;
	}
	if ( is_a( $term_id, 'WP_Term' ) ) {
		$term = $term_id;
	} elseif ( hocwp_id_number_valid( $term_id ) ) {
		$term = get_term_by( 'id', $term_id, $taxonomy );
	} else {
		$term = hocwp_term_get_current();
	}
	if ( ! is_a( $term, 'WP_Term' ) ) {
		return new WP_Error();
	}
	if ( OBJECT == strtoupper( $output ) ) {
		return $term;
	}

	return $term->term_id;
}