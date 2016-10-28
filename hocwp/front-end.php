<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

function hocwp_post_gallery( $args = array() ) {
	$galleries = hocwp_get_value_by_key( $args, 'galleries' );
	$id        = hocwp_get_value_by_key( $args, 'id' );
	$class     = hocwp_get_value_by_key( $args, 'class' );
	hocwp_add_string_with_space_before( $class, 'post-gallery module' );
	if ( ! empty( $id ) ) {
		$id = ' id="' . $id . '"';
	}
	$title = hocwp_get_value_by_key( $args, 'title' );
	if ( ! hocwp_array_has_value( $galleries ) ) {
		if ( is_string( $args ) ) {
			$galleries = hocwp_get_all_image_from_string( $args );
		} elseif ( is_string( $galleries ) && ! empty( $galleries ) ) {
			$galleries = hocwp_get_all_image_from_string( $galleries );
		} else {
			$galleries = $args;
		}
	}
	if ( hocwp_array_has_value( $galleries ) ) {
		?>
		<div<?php echo $id; ?> class="<?php echo $class; ?>">
			<?php
			if ( ! empty( $title ) ) {
				?>
				<div class="module-header">
					<h4><?php echo $title; ?></h4>
				</div>
				<?php
			}
			?>
			<div class="module-body">
				<div class="galleries">
					<ul class="gallery hocwp-gallery list-unstyled cS-hidden clearfix row">
						<?php
						$column = hocwp_get_value_by_key( $args, 'column' );
						if ( ! hocwp_is_positive_number( $column ) ) {
							$column = 2;
						}
						$pager = '';
						$count = 0;
						foreach ( $galleries as $img ) {
							$src = hocwp_get_first_image_source( $img );
							if ( ! hocwp_is_image( $src ) ) {
								continue;
							}
							$li = new HOCWP_HTML( 'li' );
							$li->set_text( $img );
							$li->set_attribute( 'data-thumb', $src );
							$li->add_class( 'col-xs-' . $column );
							$li->output();
						}
						?>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}
}

function hocwp_breadcrumb( $args = array() ) {
	if ( is_home() ) {
		return;
	}
	$before = hocwp_get_value_by_key( $args, 'before' );
	$after  = hocwp_get_value_by_key( $args, 'after' );
	if ( function_exists( 'yoast_breadcrumb' ) && hocwp_wpseo_breadcrumb_enabled() ) {
		yoast_breadcrumb( '<nav class="hocwp-breadcrumb breadcrumb yoast clearfix">' . $before, $after . '</nav>' );

		return;
	}
	if ( hocwp_wc_installed() ) {
		if ( empty( $before ) ) {
			$before = '<div class="hocwp-breadcrumb breadcrumb">';
			$after  = '</div>';
		}
		echo $before;
		woocommerce_breadcrumb( $args );
		echo $after;

		return;
	}
	global $post;
	$separator       = isset( $args['separator'] ) ? $args['separator'] : '/';
	$breadcrums_id   = isset( $args['id'] ) ? $args['id'] : 'hocwp_breadcrumbs';
	$home_title      = __( 'Home', 'hocwp-theme' );
	$custom_taxonomy = 'product_cat';
	$class           = isset( $args['class'] ) ? $args['class'] : '';
	$class           = hocwp_add_string_with_space_before( $class, 'list-inline list-unstyled breadcrumbs' );
	if ( ! is_front_page() ) {
		echo '<div class="hocwp-breadcrumb breadcrumb default clearfix">';
		echo '<ul id="' . $breadcrums_id . '" class="' . $class . '">';
		echo '<li class="item-home"><a class="bread-link bread-home" href="' . get_home_url() . '" title="' . $home_title . '">' . $home_title . '</a></li>';
		echo '<li class="separator separator-home"> ' . $separator . ' </li>';
		if ( is_post_type_archive() ) {
			echo '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . post_type_archive_title( '', false ) . '</strong></li>';
		} elseif ( is_archive() && is_tax() && ! is_category() ) {
			$post_type = get_post_type();
			if ( $post_type != 'post' ) {
				$post_type_object  = get_post_type_object( $post_type );
				$post_type_archive = get_post_type_archive_link( $post_type );
				if ( is_object( $post_type_object ) ) {
					echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
					echo '<li class="separator"> ' . $separator . ' </li>';
				}
			}
			if ( is_search() ) {
				echo '<li class="item-current item-current-' . get_search_query() . '"><strong class="bread-current bread-current-' . get_search_query() . '" title="Search results for: ' . get_search_query() . '">Search results for: ' . get_search_query() . '</strong></li>';
			} else {
				$custom_tax_name = get_queried_object()->name;
				echo '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . $custom_tax_name . '</strong></li>';
			}
		} elseif ( is_single() ) {
			$post_type = get_post_type();
			if ( $post_type != 'post' ) {
				$post_type_object  = get_post_type_object( $post_type );
				$post_type_archive = get_post_type_archive_link( $post_type );
				echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
				echo '<li class="separator"> ' . $separator . ' </li>';
			}
			$category        = get_the_category();
			$array_values    = array_values( $category );
			$last_category   = end( $array_values );
			$get_cat_parents = '';
			if ( is_object( $last_category ) ) {
				$get_cat_parents = rtrim( get_category_parents( $last_category->term_id, true, ',' ), ',' );
			}
			$cat_parents = explode( ',', $get_cat_parents );
			$cat_display = '';
			foreach ( $cat_parents as $parents ) {
				$cat_display .= '<li class="item-cat">' . $parents . '</li>';
				$cat_display .= '<li class="separator"> ' . $separator . ' </li>';
			}
			$taxonomy_exists = taxonomy_exists( $custom_taxonomy );
			if ( empty( $last_category ) && ! empty( $custom_taxonomy ) && $taxonomy_exists ) {
				$taxonomy_terms = get_the_terms( $post->ID, $custom_taxonomy );
				if ( isset( $taxonomy_terms[0] ) && is_a( $taxonomy_terms[0], 'WP_Term' ) ) {
					$cat_id       = $taxonomy_terms[0]->term_id;
					$cat_nicename = $taxonomy_terms[0]->slug;
					$cat_link     = get_term_link( $taxonomy_terms[0]->term_id, $custom_taxonomy );
					$cat_name     = $taxonomy_terms[0]->name;
				}
			}
			if ( ! empty( $last_category ) ) {
				echo $cat_display;
				echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
			} elseif ( ! empty( $cat_id ) ) {
				echo '<li class="item-cat item-cat-' . $cat_id . ' item-cat-' . $cat_nicename . '"><a class="bread-cat bread-cat-' . $cat_id . ' bread-cat-' . $cat_nicename . '" href="' . $cat_link . '" title="' . $cat_name . '">' . $cat_name . '</a></li>';
				echo '<li class="separator"> ' . $separator . ' </li>';
				echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';

			} else {
				echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
			}
		} elseif ( is_category() ) {
			echo '<li class="item-current item-cat"><strong class="bread-current bread-cat">' . single_cat_title( '', false ) . '</strong></li>';
		} elseif ( is_page() ) {
			if ( $post->post_parent ) {
				$anc     = get_post_ancestors( $post->ID );
				$anc     = array_reverse( $anc );
				$anc     = array_reverse( $anc );
				$parents = '';
				foreach ( $anc as $ancestor ) {
					$parents .= '<li class="item-parent item-parent-' . $ancestor . '"><a class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink( $ancestor ) . '" title="' . get_the_title( $ancestor ) . '">' . get_the_title( $ancestor ) . '</a></li>';
					$parents .= '<li class="separator separator-' . $ancestor . '"> ' . $separator . ' </li>';
				}
				echo $parents;
				echo '<li class="item-current item-' . $post->ID . '"><strong title="' . get_the_title() . '"> ' . get_the_title() . '</strong></li>';
			} else {
				echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '"> ' . get_the_title() . '</strong></li>';
			}
		} elseif ( is_tag() ) {
			$term_id  = get_query_var( 'tag_id' );
			$taxonomy = 'post_tag';
			$args     = 'include=' . $term_id;
			$terms    = hocwp_get_terms( $taxonomy, $args );
			if ( hocwp_array_has_value( $terms ) ) {
				echo '<li class="item-current item-tag-' . $terms[0]->term_id . ' item-tag-' . $terms[0]->slug . '"><strong class="bread-current bread-tag-' . $terms[0]->term_id . ' bread-tag-' . $terms[0]->slug . '">' . $terms[0]->name . '</strong></li>';
			}
		} elseif ( is_day() ) {
			echo '<li class="item-year item-year-' . get_the_time( 'Y' ) . '"><a class="bread-year bread-year-' . get_the_time( 'Y' ) . '" href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( 'Y' ) . '">' . get_the_time( 'Y' ) . ' Archives</a></li>';
			echo '<li class="separator separator-' . get_the_time( 'Y' ) . '"> ' . $separator . ' </li>';
			echo '<li class="item-month item-month-' . get_the_time( 'm' ) . '"><a class="bread-month bread-month-' . get_the_time( 'm' ) . '" href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '" title="' . get_the_time( 'M' ) . '">' . get_the_time( 'M' ) . ' Archives</a></li>';
			echo '<li class="separator separator-' . get_the_time( 'm' ) . '"> ' . $separator . ' </li>';
			echo '<li class="item-current item-' . get_the_time( 'j' ) . '"><strong class="bread-current bread-' . get_the_time( 'j' ) . '"> ' . get_the_time( 'jS' ) . ' ' . get_the_time( 'M' ) . ' Archives</strong></li>';
		} elseif ( is_month() ) {
			echo '<li class="item-year item-year-' . get_the_time( 'Y' ) . '"><a class="bread-year bread-year-' . get_the_time( 'Y' ) . '" href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( 'Y' ) . '">' . get_the_time( 'Y' ) . ' Archives</a></li>';
			echo '<li class="separator separator-' . get_the_time( 'Y' ) . '"> ' . $separator . ' </li>';
			echo '<li class="item-month item-month-' . get_the_time( 'm' ) . '"><strong class="bread-month bread-month-' . get_the_time( 'm' ) . '" title="' . get_the_time( 'M' ) . '">' . get_the_time( 'M' ) . ' Archives</strong></li>';
		} elseif ( is_year() ) {
			echo '<li class="item-current item-current-' . get_the_time( 'Y' ) . '"><strong class="bread-current bread-current-' . get_the_time( 'Y' ) . '" title="' . get_the_time( 'Y' ) . '">' . get_the_time( 'Y' ) . ' Archives</strong></li>';
		} elseif ( is_author() ) {
			global $author;
			$userdata = get_userdata( $author );
			echo '<li class="item-current item-current-' . $userdata->user_nicename . '"><strong class="bread-current bread-current-' . $userdata->user_nicename . '" title="' . $userdata->display_name . '">' . 'Author: ' . $userdata->display_name . '</strong></li>';
		} elseif ( get_query_var( 'paged' ) ) {
			echo '<li class="item-current item-current-' . get_query_var( 'paged' ) . '"><strong class="bread-current bread-current-' . get_query_var( 'paged' ) . '" title="Page ' . get_query_var( 'paged' ) . '">' . __( 'Page' ) . ' ' . get_query_var( 'paged' ) . '</strong></li>';
		} elseif ( is_search() ) {
			echo '<li class="item-current item-current-' . get_search_query() . '"><strong class="bread-current bread-current-' . get_search_query() . '" title="Search results for: ' . get_search_query() . '">Search results for: ' . get_search_query() . '</strong></li>';
		} elseif ( is_404() ) {
			echo '<li>' . __( 'Error 404', 'hocwp-theme' ) . '</li>';
		}
		echo '</ul>';
		echo '</div>';
	}
}

function hocwp_facebook_login_button() {
	$action = hocwp_get_method_value( 'action', 'request' );
	?>
	<button type="button" data-action="login-facebook" onclick="hocwp_facebook_login();"
	        class="btn-facebook btn-social-login btn btn-large">
		<svg class="flicon-facebook flip-icon" viewBox="0 0 256 448" height="448" width="256"
		     xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" version="1.1">
			<path
				d="M239.75 3v66h-39.25q-21.5 0-29 9t-7.5 27v47.25h73.25l-9.75 74h-63.5v189.75h-76.5v-189.75h-63.75v-74h63.75v-54.5q0-46.5 26-72.125t69.25-25.625q36.75 0 57 3z"/>
		</svg>
        <span>
            <?php
            if ( 'register' == $action ) {
	            _e( 'Register with Facebook', 'hocwp-theme' );
            } else {
	            _e( 'Login with Facebook', 'hocwp-theme' );
            }
            ?>
        </span>
	</button>
	<?php
}

function hocwp_google_login_button() {
	$action = hocwp_get_method_value( 'action', 'request' );
	?>
	<button type="button" data-action="login-google" onclick="hocwp_google_login();"
	        class="btn-google btn-social-login btn btn-large">
		<svg class="flicon-google flip-icon" viewBox="0 0 30 28" height="448" width="256"
		     xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" version="1.1">
			<path
				d="M 17.471,2c0,0-6.28,0-8.373,0C 5.344,2, 1.811,4.844, 1.811,8.138c0,3.366, 2.559,6.083, 6.378,6.083 c 0.266,0, 0.524-0.005, 0.776-0.024c-0.248,0.475-0.425,1.009-0.425,1.564c0,0.936, 0.503,1.694, 1.14,2.313 c-0.481,0-0.945,0.014-1.452,0.014C 3.579,18.089,0,21.050,0,24.121c0,3.024, 3.923,4.916, 8.573,4.916 c 5.301,0, 8.228-3.008, 8.228-6.032c0-2.425-0.716-3.877-2.928-5.442c-0.757-0.536-2.204-1.839-2.204-2.604 c0-0.897, 0.256-1.34, 1.607-2.395c 1.385-1.082, 2.365-2.603, 2.365-4.372c0-2.106-0.938-4.159-2.699-4.837l 2.655,0 L 17.471,2z M 14.546,22.483c 0.066,0.28, 0.103,0.569, 0.103,0.863c0,2.444-1.575,4.353-6.093,4.353 c-3.214,0-5.535-2.034-5.535-4.478c0-2.395, 2.879-4.389, 6.093-4.354c 0.75,0.008, 1.449,0.129, 2.083,0.334 C 12.942,20.415, 14.193,21.101, 14.546,22.483z M 9.401,13.368c-2.157-0.065-4.207-2.413-4.58-5.246 c-0.372-2.833, 1.074-5.001, 3.231-4.937c 2.157,0.065, 4.207,2.338, 4.58,5.171 C 13.004,11.189, 11.557,13.433, 9.401,13.368zM 26,8L 26,2L 24,2L 24,8L 18,8L 18,10L 24,10L 24,16L 26,16L 26,10L 32,10L 32,8 z"/>
		</svg>
        <span>
            <?php
            if ( 'register' == $action ) {
	            _e( 'Register with Google', 'hocwp-theme' );
            } else {
	            _e( 'Login with Google', 'hocwp-theme' );
            }
            ?>
        </span>
	</button>
	<?php
}

function hocwp_entry_meta_terms( $args = array() ) {
	$taxonomy = hocwp_get_value_by_key( $args, 'taxonomy', 'category' );
	if ( empty( $taxonomy ) ) {
		return;
	}
	$meta_class = 'entry-terms';
	hocwp_add_string_with_space_before( $meta_class, 'tax-' . hocwp_sanitize_html_class( $taxonomy ) );
	$icon      = hocwp_get_value_by_key( $args, 'icon', '<i class="fa fa-list-alt icon-left"></i>' );
	$before    = hocwp_get_value_by_key( $args, 'before', '<span class="' . $meta_class . '">' );
	$after     = hocwp_get_value_by_key( $args, 'after', '</span>' );
	$post_id   = hocwp_get_value_by_key( $args, 'post_id', get_the_ID() );
	$separator = hocwp_get_value_by_key( $args, 'separator', ', ' );
	if ( is_array( $taxonomy ) ) {
		foreach ( $taxonomy as $tax ) {
			the_terms( $post_id, $tax, $before . $icon, $separator, $after );
		}
	} else {
		the_terms( $post_id, $taxonomy, $before . $icon, $separator, $after );
	}
}

function hocwp_the_date() {
	?>
	<time datetime="<?php the_time( 'c' ); ?>" itemprop="datePublished"
	      class="entry-time published date post-date"><?php echo get_the_date(); ?></time>
	<?php
}

function hocwp_the_comment_link( $args = array() ) {
	$post_id = hocwp_get_value_by_key( $args, 'post_id', get_the_ID() );
	if ( hocwp_id_number_valid( $post_id ) && comments_open( $post_id ) ) {
		$comment_count = hocwp_get_post_comment_count( $post_id );
		$format        = hocwp_get_value_by_key( $args, 'format' );
		if ( empty( $format ) ) {
			$format = '%COUNT% ' . _n( 'Comment', 'Comments', $comment_count, 'hocwp-theme' );
		}
		$comment_text = str_replace( '%COUNT%', $comment_count, $format );
		?>
		<span class="entry-comments-link">
            <a href="<?php the_permalink(); ?>#comments"><?php echo $comment_text; ?></a>
        </span>
		<?php
	}
}

function hocwp_the_author( $args = array() ) {
	$before     = hocwp_get_value_by_key( $args, 'before' );
	$author_url = hocwp_get_author_posts_url();
	?>
	<span itemtype="http://schema.org/Person" itemscope itemprop="author" class="entry-author vcard author post-author">
        <?php if ( ! empty( $before ) ) : ?>
	        <span class="before-text"><?php echo $before; ?></span>
        <?php endif; ?>
		<span class="fn">
            <a rel="author" itemprop="url" class="entry-author-link" href="<?php echo $author_url; ?>"><span
		            itemprop="name" class="entry-author-name"><?php the_author(); ?></span></a>
        </span>
    </span>
	<?php
}

function hocwp_entry_meta( $args = array() ) {
	$post_id = hocwp_get_value_by_key( $args, 'post_id', get_the_ID() );
	$class   = hocwp_get_value_by_key( $args, 'class' );
	if ( ! isset( $args['taxonomy'] ) ) {
		$args['taxonomy'] = '';
	}
	$cpost = get_post( $post_id );
	if ( ! is_a( $cpost, 'WP_Post' ) ) {
		return;
	}
	$author_url    = hocwp_get_author_posts_url();
	$comment_count = hocwp_get_post_comment_count( $post_id );
	$comment_text  = $comment_count . ' ' . _n( 'Comment', 'Comments', $comment_count, 'hocwp-theme' );
	hocwp_add_string_with_space_before( $class, 'entry-meta' );
	$show_date    = hocwp_get_value_by_key( $args, 'show_date', true );
	$show_updated = hocwp_get_value_by_key( $args, 'show_updated', true );
	$show_author  = hocwp_get_value_by_key( $args, 'show_author', true );
	$show_term    = hocwp_get_value_by_key( $args, 'show_term', false );
	$show_comment = hocwp_get_value_by_key( $args, 'show_comment', true );
	$format       = hocwp_get_value_by_key( $args, 'format' );
	?>
	<p class="<?php echo $class; ?>">
		<?php
		if ( ! empty( $format ) ) {
			$format = str_replace( '%MODIFIED%', get_the_modified_date(), $format );
			$format = str_replace( '%AUTHOR%', get_the_author(), $format );
			echo $format;
		} else {
			if ( $show_date ) {
				hocwp_the_date();
			}
			if ( $show_updated ) {
				?>
				<time datetime="<?php the_modified_time( 'c' ); ?>" itemprop="dateModified"
				      class="entry-modified-time date modified post-date updated"><?php the_modified_date(); ?></time>
				<?php
			}
			if ( $show_term ) {
				$meta_term_args = $args;
				$term_icon      = hocwp_get_value_by_key( $args, 'term_icon' );
				if ( ! empty( $term_icon ) ) {
					$meta_term_args['icon'] = $term_icon;
				}
				hocwp_entry_meta_terms( $meta_term_args );
			}
			if ( $show_comment && comments_open( $post_id ) ) {
				hocwp_the_comment_link();
			}
		}
		if ( current_theme_supports( 'hocwp-schema' ) ) {
			global $authordata;
			$author_id     = 0;
			$author_name   = '';
			$author_avatar = '';
			if ( hocwp_object_valid( $authordata ) ) {
				$author_id     = $authordata->ID;
				$author_name   = $authordata->display_name;
				$author_avatar = get_avatar_url( $author_id, array( 'size' => 128 ) );
			}
			$logo_url = apply_filters( 'hocwp_publisher_logo_url', '' );
			?>
			<span itemprop="publisher" itemscope itemtype="https://schema.org/Organization" class="small hidden">
                <span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                    <img alt="" src="<?php echo $logo_url; ?>">
                    <meta itemprop="url" content="<?php echo $logo_url; ?>">
                    <meta itemprop="width" content="600">
                    <meta itemprop="height" content="60">
                </span>
                <meta itemprop="name" content="<?php echo $author_name; ?>">
            </span>
			<?php
		}
		?>
	</p>
	<?php
}

function hocwp_entry_meta_author_first( $args = array() ) {
	$post_id = hocwp_get_value_by_key( $args, 'post_id', get_the_ID() );
	$class   = hocwp_get_value_by_key( $args, 'class' );
	$cpost   = get_post( $post_id );
	if ( ! is_a( $cpost, 'WP_Post' ) ) {
		return;
	}
	$author_url    = hocwp_get_author_posts_url();
	$comment_count = hocwp_get_post_comment_count( $post_id );
	$comment_text  = $comment_count . ' ' . _n( 'Comment', 'Comments', $comment_count, 'hocwp-theme' );
	hocwp_add_string_with_space_before( $class, 'entry-meta' );
	?>
	<p class="<?php echo $class; ?>">
        <span itemtype="http://schema.org/Person" itemscope itemprop="author"
              class="entry-author vcard author post-author">
            <span class="fn">
                <a rel="author" itemprop="url" class="entry-author-link" href="<?php echo $author_url; ?>"><span
		                itemprop="name" class="entry-author-name"><?php the_author(); ?></span></a>
            </span>
        </span>
		<time datetime="<?php the_time( 'c' ); ?>" itemprop="datePublished"
		      class="entry-time published date post-date"><?php echo get_the_date(); ?></time>
		<time datetime="<?php the_modified_time( 'c' ); ?>" itemprop="dateModified"
		      class="entry-modified-time date modified post-date"><?php the_modified_date(); ?></time>
		<?php if ( comments_open( $post_id ) ) : ?>
			<span class="entry-comments-link">
                <a href="<?php the_permalink(); ?>#comments"><?php echo $comment_text; ?></a>
            </span>
		<?php endif; ?>
		<?php if ( current_theme_supports( 'hocwp-schema' ) ) : ?>
			<?php
			global $authordata;
			$author_id     = 0;
			$author_name   = '';
			$author_avatar = '';
			if ( hocwp_object_valid( $authordata ) ) {
				$author_id     = $authordata->ID;
				$author_name   = $authordata->display_name;
				$author_avatar = get_avatar_url( $author_id, array( 'size' => 128 ) );
			}
			$logo_url = apply_filters( 'hocwp_publisher_logo_url', '' );
			?>
			<span itemprop="publisher" itemscope itemtype="https://schema.org/Organization" class="small hidden">
                <span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                    <img alt="" src="<?php echo $logo_url; ?>">
                    <meta itemprop="url" content="<?php echo $logo_url; ?>">
                    <meta itemprop="width" content="600">
                    <meta itemprop="height" content="60">
                </span>
                <meta itemprop="name" content="<?php echo $author_name; ?>">
            </span>
		<?php endif; ?>
	</p>
	<?php
}

function hocwp_rel_canonical() {
	if ( ! is_singular() || has_action( 'wp_head', 'rel_canonical' ) ) {
		return;
	}
	global $wp_the_query;
	if ( ! $id = $wp_the_query->get_queried_object_id() ) {
		return;
	}
	$link = get_permalink( $id );
	if ( $page = get_query_var( 'cpage' ) ) {
		$link = get_comments_pagenum_link( $page );
	}
	$link = apply_filters( 'hocwp_head_rel_canonical', $link, $id );
	echo "<link rel='canonical' href='$link' />\n";
}

function hocwp_posts_pagination( $args = array() ) {
	$defaults = array(
		'prev_text'          => __( 'Prev', 'hocwp-theme' ),
		'next_text'          => __( 'Next', 'hocwp-theme' ),
		'screen_reader_text' => __( 'Pages', 'hocwp-theme' )
	);
	$args     = wp_parse_args( $args, $defaults );
	the_posts_pagination( $args );
}

function hocwp_entry_content( $content = '' ) {
	?>
	<div class="entry-content" itemprop="text">
		<?php
		if ( ! empty( $content ) ) {
			echo wpautop( $content );
		} else {
			the_content();
		}
		?>
	</div>
	<?php
}

function hocwp_entry_summary( $length = null, $class = '' ) {
	if ( is_numeric( $length ) && 1 > $length ) {
		return;
	}
	$class = hocwp_sanitize_html_class( $class );
	hocwp_add_string_with_space_before( $class, 'entry-summary' );
	$class = esc_attr( $class );
	echo '<div class="' . $class . '" itemprop="text">';
	if ( is_numeric( $length ) ) {
		echo wpautop( hocwp_substr( get_the_excerpt(), $length ) );
	} else {
		the_excerpt();
	}
	echo '</div>';
}

function hocwp_entry_author_avatar() {
	$author_avatar_size = apply_filters( 'hocwp_author_avatar_size', 49 );
	printf( '<span class="byline"><span class="author vcard">%1$s<span class="screen-reader-text">%2$s </span> <a class="url fn n" href="%3$s">%4$s</a></span></span>',
		get_avatar( get_the_author_meta( 'user_email' ), $author_avatar_size ),
		_x( 'Author', 'Used before post author name.', 'hocwp-theme' ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		get_the_author()
	);
}

function hocwp_entry_date() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		get_the_date(),
		esc_attr( get_the_modified_date( 'c' ) ),
		get_the_modified_date()
	);

	printf( '<span class="posted-on"><span class="screen-reader-text">%1$s </span><a href="%2$s" rel="bookmark">%3$s</a></span>',
		_x( 'Posted on', 'Used before publish date.', 'hocwp-theme' ),
		esc_url( get_permalink() ),
		$time_string
	);
}

function hocwp_entry_format() {
	$format = get_post_format();
	if ( current_theme_supports( 'post-formats', $format ) ) {
		printf( '<span class="entry-format">%1$s<a href="%2$s">%3$s</a></span>',
			sprintf( '<span class="screen-reader-text">%s </span>', _x( 'Format', 'Used before post format.', 'hocwp-theme' ) ),
			esc_url( get_post_format_link( $format ) ),
			get_post_format_string( $format )
		);
	}
}

function hocwp_entry_categories_and_tags() {
	$separator       = _x( ', ', 'Used between list items, there is a space after the comma.', 'hocwp-theme' );
	$categories_list = get_the_category_list( $separator );
	if ( $categories_list ) {
		printf( '<span class="cat-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
			_x( 'Categories', 'Used before category names.', 'hocwp-theme' ),
			$categories_list
		);
	}

	$tags_list = get_the_tag_list( '', $separator );
	if ( $tags_list ) {
		printf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
			_x( 'Tags', 'Used before tag names.', 'hocwp-theme' ),
			$tags_list
		);
	}
}

function hocwp_entry_comments_popup_link() {
	if ( comments_open() || get_comments_number() ) {
		echo '<span class="comments-link">';
		comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'hocwp-theme' ), get_the_title() ) );
		echo '</span>';
	}
}

function hocwp_entry_tags() {
	echo '<div class="entry-tags">';
	the_tags( '<span class="tag-label"><i class="fa fa-tag icon-left"></i><span class="text">Tags:</span></span>&nbsp;', ' ', '' );
	echo '</div>';
}

function hocwp_the_custom_logo() {
	if ( function_exists( 'the_custom_logo' ) ) {
		the_custom_logo();
	}
}

function hocwp_button_vote_group() {
	$post_id   = get_the_ID();
	$vote_up   = absint( get_post_meta( $post_id, 'likes', true ) );
	$vote_down = absint( hocwp_get_post_meta( 'dislikes', $post_id ) );
	?>
	<div class="text-center vote-buttons">
		<p class="vote btn-group" data-post-id="<?php the_ID(); ?>">
			<a class="btn btn-default vote-up vote-post" data-vote-type="up" data-vote="<?php echo $vote_up; ?>">
				<i class="fa fa-thumbs-o-up"></i>
			</a>
			<a class="btn btn-default vote-down vote-post" data-vote-type="down" data-vote="<?php echo $vote_down; ?>">
				<i class="fa fa-thumbs-o-down"></i>
			</a>
		</p>
	</div>
	<?php
}