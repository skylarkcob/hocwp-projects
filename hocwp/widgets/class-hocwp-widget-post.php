<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

class HOCWP_Widget_Post extends WP_Widget {
	public $args = array();
	public $admin_args;
	public $instance;

	private function get_defaults() {
		$defaults = array(
			'bys'                        => array(
				'recent'          => __( 'Recent posts', 'hocwp-theme' ),
				'recent_modified' => __( 'Recent modified posts', 'hocwp-theme' ),
				'random'          => __( 'Random posts', 'hocwp-theme' ),
				'comment'         => __( 'Most comment posts', 'hocwp-theme' ),
				'category'        => __( 'Posts by category', 'hocwp-theme' ),
				'featured'        => __( 'Featured posts', 'hocwp-theme' ),
				'related'         => __( 'Related posts', 'hocwp-theme' ),
				'like'            => __( 'Most likes posts', 'hocwp-theme' ),
				'view'            => __( 'Most views posts', 'hocwp-theme' ),
				'favorite'        => __( 'Most favorite posts', 'hocwp-theme' ),
				'rate'            => __( 'Most rate posts', 'hocwp-theme' )
			),
			'post_type'                  => array( array( 'value' => 'post' ) ),
			'by'                         => 'recent',
			'number'                     => 5,
			'category'                   => array(),
			'excerpt_length'             => 75,
			'thumbnail_size'             => array( 64, 64 ),
			'title_length'               => 50,
			'show_author'                => 0,
			'show_date'                  => 0,
			'show_comment_count'         => 0,
			'only_thumbnail'             => 0,
			'hide_thumbnail'             => 0,
			'widget_title_link_category' => 0,
			'category_as_widget_title'   => 1,
			'full_width_posts'           => array(
				'none'       => __( 'None', 'hocwp-theme' ),
				'first'      => __( 'First post', 'hocwp-theme' ),
				'last'       => __( 'Last post', 'hocwp-theme' ),
				'first_last' => __( 'First post and last post', 'hocwp-theme' ),
				'odd'        => __( 'Odd posts', 'hocwp-theme' ),
				'even'       => __( 'Even posts', 'hocwp-theme' ),
				'all'        => __( 'All posts', 'hocwp-theme' )
			),
			'full_width_post'            => 'none',
			'times'                      => array(
				'all'     => __( 'All time', 'hocwp-theme' ),
				'daily'   => __( 'Daily', 'hocwp-theme' ),
				'weekly'  => __( 'Weekly', 'hocwp-theme' ),
				'monthly' => __( 'Monthly', 'hocwp-theme' ),
				'yearly'  => __( 'Yearly', 'hocwp-theme' )
			),
			'time'                       => 'all',
			'orders'                     => array(
				'desc' => __( 'DESC', 'hocwp-theme' ),
				'asc'  => __( 'ASC', 'hocwp-theme' )
			),
			'order'                      => 'desc',
			'orderbys'                   => array(
				'title' => __( 'Title', 'hocwp-theme' ),
				'date'  => __( 'Post date', 'hocwp-theme' )
			),
			'orderby'                    => 'date',
			'slider'                     => 0
		);
		$defaults = apply_filters( 'hocwp_widget_post_defaults', $defaults, $this );
		$args     = apply_filters( 'hocwp_widget_post_args', array(), $this );
		$args     = wp_parse_args( $args, $defaults );

		return $args;
	}

	public function __construct() {
		$this->args        = $this->get_defaults();
		$bys               = $this->args['bys'];
		$bys               = apply_filters( 'hocwp_widget_post_query_bys', $bys, $this );
		$this->args['bys'] = $bys;
		$this->admin_args  = array(
			'id'          => 'hocwp_widget_post',
			'name'        => 'HocWP Post',
			'class'       => 'hocwp-widget-post',
			'description' => __( 'Your site’s most recent Posts and more.', 'hocwp-theme' ),
			'width'       => 400
		);
		$this->admin_args  = apply_filters( 'hocwp_widget_post_admin_args', $this->admin_args, $this );
		parent::__construct( $this->admin_args['id'], $this->admin_args['name'],
			array(
				'classname'   => $this->admin_args['class'],
				'description' => $this->admin_args['description'],
			),
			array(
				'width' => $this->admin_args['width']
			)
		);
	}

	private function get_post_type_from_instance( $instance ) {
		$post_type = isset( $instance['post_type'] ) ? $instance['post_type'] : json_encode( $this->args['post_type'] );
		$post_type = hocwp_json_string_to_array( $post_type );
		if ( ! hocwp_array_has_value( $post_type ) ) {
			$post_type = array(
				array(
					'value' => apply_filters( 'hocwp_widget_post_default_post_type', 'post', $instance, $this )
				)
			);
		}

		return $post_type;
	}

	public function widget( $args, $instance ) {
		$this->instance = $instance;
		global $post;
		$title      = hocwp_widget_title( $args, $instance, false );
		$post_type  = $this->get_post_type_from_instance( $instance );
		$post_types = array();
		foreach ( $post_type as $fvdata ) {
			$ptvalue = isset( $fvdata['value'] ) ? $fvdata['value'] : '';
			if ( ! empty( $ptvalue ) ) {
				$post_types[] = $ptvalue;
			}
		}
		$number                     = isset( $instance['number'] ) ? $instance['number'] : $this->args['number'];
		$by                         = isset( $instance['by'] ) ? $instance['by'] : $this->args['by'];
		$category                   = isset( $instance['category'] ) ? $instance['category'] : json_encode( $this->args['category'] );
		$category                   = hocwp_json_string_to_array( $category );
		$excerpt_length             = isset( $instance['excerpt_length'] ) ? $instance['excerpt_length'] : $this->args['excerpt_length'];
		$content_class              = 'widget-content';
		$slider                     = hocwp_get_value_by_key( $instance, 'slider', hocwp_get_value_by_key( $this->args, 'slider' ) );
		$title_length               = hocwp_get_value_by_key( $instance, 'title_length', hocwp_get_value_by_key( $this->args, 'title_length' ) );
		$hide_thumbnail             = hocwp_get_value_by_key( $instance, 'hide_thumbnail', hocwp_get_value_by_key( $this->args, 'hide_thumbnail' ) );
		$widget_title_link_category = hocwp_get_value_by_key( $instance, 'widget_title_link_category', hocwp_get_value_by_key( $this->args, 'widget_title_link_category' ) );
		$category_as_widget_title   = hocwp_get_value_by_key( $instance, 'category_as_widget_title', hocwp_get_value_by_key( $this->args, 'category_as_widget_title' ) );
		if ( (bool) $slider ) {
			hocwp_add_string_with_space_before( $content_class, 'post-slider' );
		}
		if ( 'category' == $by && (bool) $widget_title_link_category ) {
			$cvalue  = current( $category );
			$term_id = isset( $cvalue['value'] ) ? $cvalue['value'] : '';
			$term_id = absint( $term_id );
			if ( $term_id > 0 ) {
				$taxonomy = isset( $cvalue['taxonomy'] ) ? $cvalue['taxonomy'] : '';
				$link     = new HOCWP_HTML( 'a' );
				$link->set_class( 'term-link' );
				$link->set_href( get_term_link( $term_id, $taxonomy ) );
				if ( (bool) $category_as_widget_title ) {
					$term  = get_term( $term_id, $taxonomy );
					$title = $term->name;
				} else {
					$title = hocwp_get_value_by_key( $instance, 'title' );
				}
				$link->set_text( $title );
				$title = hocwp_get_value_by_key( $args, 'before_title' )
				         . $link->build()
				         . hocwp_get_value_by_key( $args, 'after_title' );
			}
		}
		$query_args = array(
			'posts_per_page' => $number,
			'post_type'      => $post_types
		);
		$w_query    = new WP_Query();
		$get_by     = false;
		switch ( $by ) {
			case 'recent':
				$get_by = true;
				break;
			case 'recent_modified':
				$get_by                = true;
				$query_args['orderby'] = 'modified';
				$query_args['order']   = 'DESC';
				break;
			case 'random':
				$get_by                = true;
				$query_args['orderby'] = 'rand';
				break;
			case 'comment':
				$get_by                = true;
				$query_args['orderby'] = 'comment_count';
				break;
			case 'category':
				foreach ( $category as $cvalue ) {
					$term_id = isset( $cvalue['value'] ) ? $cvalue['value'] : '';
					$term_id = absint( $term_id );
					if ( $term_id > 0 ) {
						$taxonomy = isset( $cvalue['taxonomy'] ) ? $cvalue['taxonomy'] : '';
						if ( ! empty( $taxonomy ) ) {
							$tax_item   = array(
								'taxonomy' => $taxonomy,
								'field'    => 'term_id',
								'terms'    => $term_id
							);
							$query_args = hocwp_query_sanitize_tax_query( $tax_item, $query_args );
							$get_by     = true;
						}
					}
				}
				break;
			case 'related':
				$get_by = true;
				break;
			case 'like':
				$get_by                 = true;
				$query_args['meta_key'] = 'likes';
				$query_args['orderby']  = 'meta_value_num';
				break;
			case 'view':
				$get_by                 = true;
				$query_args['meta_key'] = 'views';
				$query_args['orderby']  = 'meta_value_num';
				break;
			case 'featured':
				$get_by = true;
				hocwp_query_sanitize_featured_args( $query_args );
				break;
			case 'favorite':
				break;
			case 'rate':
				break;
		}
		$get_by = apply_filters( 'hocwp_widget_post_get_by_check_status', $get_by, $args, $instance, $this );
		if ( $get_by ) {
			$query_args = apply_filters( 'hocwp_widget_post_query_args', $query_args, $args, $instance, $this );
			if ( 'related' == $by ) {
				$w_query = hocwp_query_related_post( $query_args );
			} else {
				$w_query = hocwp_query( $query_args );
			}
		}
		$widget_html = hocwp_get_value_by_key( $args, 'before_widget' ) . $title;
		$widget_html .= '<div class="' . $content_class . '">';
		$widget_post_pre_html = apply_filters( 'hocwp_widget_post_pre_html', '', $args, $instance, $this );
		if ( ! empty( $widget_post_pre_html ) ) {
			$widget_html .= $widget_post_pre_html;
		} else {
			if ( $w_query->have_posts() ) {
				$widget_html .= apply_filters( 'hocwp_widget_post_before_loop', '', $args, $instance, $this );
				if ( (bool) $slider ) {
					$four_posts     = array_slice( $w_query->posts, 0, 4 );
					$next_posts     = array_slice( $w_query->posts, 4, absint( $w_query->post_count - 4 ) );
					$widget_content = apply_filters( 'hocwp_widget_post_pre_slider_html', '', $args, $instance, $this );
					if ( empty( $widget_content ) ) {
						$carousel_id = $this->id;
						$carousel_id = hocwp_sanitize_id( $carousel_id );
						$count       = 0;
						ob_start();
						?>
						<div id="<?php echo $carousel_id; ?>" class="carousel slide" data-ride="carousel">
							<div class="carousel-inner" role="listbox">
								<?php
								foreach ( $four_posts as $post ) {
									setup_postdata( $post );
									$class = 'item';
									if ( 0 == $count ) {
										hocwp_add_string_with_space_before( $class, 'active' );
									}
									?>
									<div class="<?php echo $class; ?>">
										<?php
										hocwp_article_before();
										do_action( 'hocwp_widget_post_slider_before_post', $args, $instance, $this );
										if ( ! (bool) $hide_thumbnail ) {
											if ( 'coupon' == $post->post_type ) {
												$store = hocwp_get_coupon_store( $post->ID );
												echo '<div class="text-center store-logo">';
												hocwp_term_the_thumbnail(
													array(
														'term'   => $store,
														'width'  => 180,
														'height' => 110
													)
												);
												echo '</div>';
											} else {
												hocwp_post_thumbnail( array( 'width' => 300, 'height' => 200 ) );
											}
										}
										do_action( 'hocwp_widget_post_slider_before_post_title', $args, $instance, $this );
										hocwp_post_title_link( array( 'title' => hocwp_substr( get_the_title(), $title_length ) ) );
										do_action( 'hocwp_widget_post_slider_after_post_title', $args, $instance, $this );
										hocwp_entry_summary( $excerpt_length );
										do_action( 'hocwp_widget_post_slider_after_post', $args, $instance, $this );
										hocwp_article_after();
										?>
									</div>
									<?php
									$count ++;
								}
								wp_reset_postdata();
								?>
							</div>
							<ol class="carousel-indicators list-inline list-unstyled">
								<?php
								$count = count( $four_posts );
								for ( $i = 0; $i < $count; $i ++ ) {
									$indicator_class = 'indicator-item';
									if ( 0 == $i ) {
										hocwp_add_string_with_space_before( $indicator_class, 'active' );
									}
									?>
									<li data-target="#<?php echo $carousel_id; ?>" data-slide-to="<?php echo $i; ?>"
									    class="<?php echo $indicator_class; ?>">
										<span><?php echo( $i + 1 ); ?></span>
									</li>
									<?php
								}
								?>
							</ol>
							<a class="left carousel-control" href="#<?php echo $carousel_id; ?>" role="button"
							   data-slide="prev">
								<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
								<span class="sr-only"><?php hocwp_translate_text( 'Previous', true ); ?></span>
							</a>
							<a class="right carousel-control" href="#<?php echo $carousel_id; ?>" role="button"
							   data-slide="next">
								<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
								<span class="sr-only"><?php hocwp_translate_text( 'Next', true ); ?></span>
							</a>
						</div>
						<?php
						$widget_content = ob_get_clean();
						if ( hocwp_array_has_value( $next_posts ) ) {
							$w_query->posts      = $next_posts;
							$w_query->post_count = count( $next_posts );
							?>
							<div class="more-posts">
								<?php $widget_content .= $this->list_post_html( $args, $instance, $w_query ); ?>
							</div>
							<?php
						}
					}
					$widget_content = apply_filters( 'hocwp_widget_post_slider_html', $widget_content, $args, $instance, $this );
					$widget_html .= $widget_content;
				} else {
					$widget_html .= $this->list_post_html( $args, $instance, $w_query );
				}
				$widget_html .= apply_filters( 'hocwp_widget_post_after_loop', '', $args, $instance, $this );
			} else {
				$widget_html .= '<p class="nothing-found">' . hocwp_translate_text( 'Nothing found!' ) . '</p>';
			}
		}
		$widget_html = apply_filters( 'hocwp_widget_post_html', $widget_html, $args, $instance, $this );
		$widget_html .= '</div>';
		$widget_html .= hocwp_get_value_by_key( $args, 'after_widget' );
		echo $widget_html;
	}

	public function list_post_html( $args, $instance, WP_Query $query ) {
		$post_type  = $this->get_post_type_from_instance( $instance );
		$post_types = array();
		foreach ( $post_type as $fvdata ) {
			$ptvalue = isset( $fvdata['value'] ) ? $fvdata['value'] : '';
			if ( ! empty( $ptvalue ) ) {
				$post_types[] = $ptvalue;
			}
		}
		$full_width_post = hocwp_get_value_by_key( $instance, 'full_width_post', $this->args['full_width_post'] );
		$hide_thumbnail  = hocwp_get_value_by_key( $instance, 'hide_thumbnail', hocwp_get_value_by_key( $this->args, 'hide_thumbnail' ) );
		$thumbnail_size  = isset( $instance['thumbnail_size'] ) ? $instance['thumbnail_size'] : $this->args['thumbnail_size'];
		$thumbnail_size  = hocwp_sanitize_size( $thumbnail_size );
		$excerpt_length  = isset( $instance['excerpt_length'] ) ? $instance['excerpt_length'] : $this->args['excerpt_length'];
		$title_length    = hocwp_get_value_by_key( $instance, 'title_length', hocwp_get_value_by_key( $this->args, 'title_length' ) );
		$list_class      = 'list-unstyled';
		foreach ( $post_types as $ptvalue ) {
			hocwp_add_string_with_space_before( $list_class, 'list-' . $ptvalue . 's' );
		}
		$list_class  = apply_filters( 'hocwp_widget_post_list_class', $list_class, $args, $instance, $this );
		$widget_html = '<ul class="' . $list_class . '">';
		$loop_html   = apply_filters( 'hocwp_widget_post_loop_html', '', $args, $instance, $this );
		if ( empty( $loop_html ) ) {
			$count = 0;
			ob_start();
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id    = get_the_ID();
				$post       = get_post( $post_id );
				$class      = 'a-widget-post';
				$full_width = false;
				if ( 'all' == $full_width_post ) {
					$full_width = true;
				} elseif ( 'first' == $full_width_post && 0 == $count ) {
					$full_width = true;
				} elseif ( 'last' == $full_width_post && $count == $query->post_count ) {
					$full_width = true;
				} elseif ( 'first_last' == $full_width_post && ( 0 == $count || $count == $query->post_count ) ) {
					$full_width = true;
				} elseif ( 'odd' == $full_width_post && ( $count % 2 ) != 0 ) {
					$full_width = true;
				} elseif ( 'even' == $full_width_post && ( $count % 2 ) == 0 ) {
					$full_width = true;
				}
				if ( $full_width ) {
					hocwp_add_string_with_space_before( $class, 'full-width' );
				}
				if ( $hide_thumbnail ) {
					hocwp_add_string_with_space_before( $class, 'hide-thumbnail' );
				}
				if ( hocwp_is_positive_number( $excerpt_length ) ) {
					hocwp_add_string_with_space_before( $class, 'show-excerpt' );
				} else {
					hocwp_add_string_with_space_before( $class, 'hide-excerpt' );
				}
				?>
				<li <?php post_class( $class ); ?>>
					<?php
					do_action( 'hocwp_widget_post_before_post', $args, $instance, $this );
					if ( ! (bool) $hide_thumbnail ) {
						if ( 'coupon' == $post->post_type ) {
							echo '<div class="text-center code-data">';
							$percent   = hocwp_get_coupon_percent_label( $post_id );
							$text      = hocwp_get_coupon_text_label( $post_id );
							$type_text = 'Website Coupons';
							$type_term = hocwp_get_coupon_type_term( $post_id );
							if ( is_a( $type_term, 'WP_Term' ) ) {
								$type_text = $type_term->name;
							}
							$tmp = hocwp_get_coupon_type( $post_id );
							if ( is_array( $tmp ) ) {
								$tmp = array_shift( $tmp );
								if ( ! empty( $tmp ) ) {
									$type_text = $tmp;
								}
							}
							$price      = hocwp_get_post_meta( 'price', $post_id );
							$sale_price = hocwp_get_post_meta( 'sale_price', $post_id );
							if ( ! empty( $price ) && ! empty( $sale_price ) || empty( $percent ) ) {
								$percentage = hocwp_percentage( $price, $sale_price );
								$percent    = $percentage . '%';
								$text       = 'OFF';
							}
							?>
							<div class="txt"><?php echo $percent . ' ' . $text; ?></div>
							<div class="type"><?php echo $type_text; ?></div>
							<?php
							echo '</div>';
						} else {
							$thumbnail_args = array(
								'width'  => $thumbnail_size[0],
								'height' => $thumbnail_size[1]
							);
							if ( $full_width ) {
								unset( $thumbnail_args['width'] );
								unset( $thumbnail_args['height'] );
							}
							hocwp_post_thumbnail( $thumbnail_args );
						}
					}
					do_action( 'hocwp_widget_post_before_post_title', $args, $instance, $this );
					hocwp_post_title_link( array( 'title' => hocwp_substr( get_the_title(), $title_length ) ) );
					do_action( 'hocwp_widget_post_after_post_title', $args, $instance, $this );
					if ( 0 < $excerpt_length ) {
						$post_type = get_post_type( $post_id );
						if ( 'product' == $post_type && hocwp_wc_installed() ) {
							hocwp_wc_product_price( null, true );
						} else {
							hocwp_entry_summary( $excerpt_length );
						}
					}
					do_action( 'hocwp_widget_post_after_post', $args, $instance, $this );
					?>
				</li>
				<?php
				$count ++;
			}
			wp_reset_postdata();
			$loop_html .= ob_get_clean();
		}
		$widget_html .= $loop_html;
		$widget_html .= '</ul>';

		return $widget_html;
	}

	public function form( $instance ) {
		$this->instance  = $instance;
		$title           = hocwp_get_value_by_key( $instance, 'title' );
		$post_type       = $this->get_post_type_from_instance( $instance );
		$number          = hocwp_get_value_by_key( $instance, 'number', hocwp_get_value_by_key( $this->args, 'number' ) );
		$by              = hocwp_get_value_by_key( $instance, 'by', hocwp_get_value_by_key( $this->args, 'by' ) );
		$category        = hocwp_get_value_by_key( $instance, 'category', json_encode( hocwp_get_value_by_key( $this->args, 'category' ) ) );
		$category        = hocwp_json_string_to_array( $category );
		$thumbnail_size  = hocwp_get_value_by_key( $instance, 'thumbnail_size', hocwp_get_value_by_key( $this->args, 'thumbnail_size' ) );
		$full_width_post = hocwp_get_value_by_key( $instance, 'full_width_post', hocwp_get_value_by_key( $this->args, 'full_width_post' ) );

		$title_length               = hocwp_get_value_by_key( $instance, 'title_length', hocwp_get_value_by_key( $this->args, 'title_length' ) );
		$excerpt_length             = hocwp_get_value_by_key( $instance, 'excerpt_length', hocwp_get_value_by_key( $this->args, 'excerpt_length' ) );
		$hide_thumbnail             = hocwp_get_value_by_key( $instance, 'hide_thumbnail', hocwp_get_value_by_key( $this->args, 'hide_thumbnail' ) );
		$widget_title_link_category = hocwp_get_value_by_key( $instance, 'widget_title_link_category', hocwp_get_value_by_key( $this->args, 'widget_title_link_category' ) );
		$category_as_widget_title   = hocwp_get_value_by_key( $instance, 'category_as_widget_title', hocwp_get_value_by_key( $this->args, 'category_as_widget_title' ) );

		hocwp_field_widget_before( $this->admin_args['class'] );

		hocwp_widget_field_title( $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), $title );

		$lists = get_post_types( array( '_builtin' => false, 'public' => true ), 'objects' );
		if ( ! array_key_exists( 'post', $lists ) ) {
			$lists[] = get_post_type_object( 'post' );
		}
		$all_option = '';
		foreach ( $lists as $lvalue ) {
			$selected = '';
			if ( ! hocwp_array_has_value( $post_type ) ) {
				$post_type[] = array( 'value' => 'post' );
			}
			foreach ( $post_type as $ptvalue ) {
				$ptype = isset( $ptvalue['value'] ) ? $ptvalue['value'] : '';
				if ( $lvalue->name == $ptype ) {
					$selected = $lvalue->name;
					break;
				}
			}
			$all_option .= hocwp_field_get_option( array(
				'value'    => $lvalue->name,
				'text'     => $lvalue->labels->singular_name,
				'selected' => $selected
			) );
		}
		$args = array(
			'id'          => $this->get_field_id( 'post_type' ),
			'name'        => $this->get_field_name( 'post_type' ),
			'all_option'  => $all_option,
			'value'       => $post_type,
			'label'       => __( 'Post type:', 'hocwp-theme' ),
			'placeholder' => __( 'Choose post types', 'hocwp-theme' ),
			'multiple'    => true
		);
		hocwp_widget_field( 'hocwp_field_select_chosen', $args );

		$args = array(
			'id'    => $this->get_field_id( 'number' ),
			'name'  => $this->get_field_name( 'number' ),
			'value' => $number,
			'label' => __( 'Number posts:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_number', $args );

		$lists      = $this->args['bys'];
		$all_option = '';
		foreach ( $lists as $lkey => $lvalue ) {
			$all_option .= hocwp_field_get_option( array( 'value' => $lkey, 'text' => $lvalue, 'selected' => $by ) );
		}
		$args = array(
			'id'         => $this->get_field_id( 'by' ),
			'name'       => $this->get_field_name( 'by' ),
			'value'      => $by,
			'all_option' => $all_option,
			'label'      => __( 'Get by:', 'hocwp-theme' ),
			'class'      => 'get-by'
		);
		hocwp_widget_field( 'hocwp_field_select', $args );

		$all_option = '';
		$taxonomies = hocwp_get_hierarchical_taxonomies();
		foreach ( $taxonomies as $tkey => $tax ) {
			$lists = hocwp_get_hierarchical_terms( array( $tkey ) );
			if ( hocwp_array_has_value( $lists ) ) {
				$all_option .= '<optgroup label="' . $tax->labels->singular_name . '">';
				foreach ( $lists as $lvalue ) {
					$selected = '';
					if ( hocwp_array_has_value( $category ) ) {
						foreach ( $category as $cvalue ) {
							$term_id = isset( $cvalue['value'] ) ? $cvalue['value'] : 0;
							if ( $lvalue->term_id == $term_id ) {
								$selected = $lvalue->term_id;
								break;
							}
						}
					}
					$all_option .= hocwp_field_get_option( array(
						'value'      => $lvalue->term_id,
						'text'       => $lvalue->name,
						'selected'   => $selected,
						'attributes' => array( 'data-taxonomy' => $tkey )
					) );
				}
				$all_option .= '</optgroup>';
			}
		}
		$args = array(
			'id'          => $this->get_field_id( 'category' ),
			'name'        => $this->get_field_name( 'category' ),
			'all_option'  => $all_option,
			'value'       => $category,
			'label'       => __( 'Category:', 'hocwp-theme' ),
			'placeholder' => __( 'Choose terms', 'hocwp-theme' ),
			'multiple'    => true,
			'class'       => 'select-category'
		);
		if ( 'category' != $by ) {
			$args['hidden'] = true;
		}
		hocwp_widget_field( 'hocwp_field_select_chosen', $args );

		$args = array(
			'id_width'    => $this->get_field_id( 'thumbnail_size_width' ),
			'name_width'  => $this->get_field_name( 'thumbnail_size_width' ),
			'id_height'   => $this->get_field_id( 'thumbnail_size_height' ),
			'name_height' => $this->get_field_name( 'thumbnail_size_height' ),
			'value'       => $thumbnail_size,
			'label'       => __( 'Thumbnail size:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_size', $args );

		$lists      = $this->args['full_width_posts'];
		$all_option = '';
		foreach ( $lists as $lkey => $lvalue ) {
			$all_option .= hocwp_field_get_option( array(
				'value'    => $lkey,
				'text'     => $lvalue,
				'selected' => $full_width_post
			) );
		}
		$args = array(
			'id'         => $this->get_field_id( 'full_width_post' ),
			'name'       => $this->get_field_name( 'full_width_post' ),
			'value'      => $full_width_post,
			'all_option' => $all_option,
			'label'      => __( 'Full width posts:', 'hocwp-theme' ),
			'class'      => 'full-width-post'
		);
		hocwp_widget_field( 'hocwp_field_select', $args );

		$args = array(
			'id'    => $this->get_field_id( 'title_length' ),
			'name'  => $this->get_field_name( 'title_length' ),
			'value' => $title_length,
			'label' => __( 'Title length:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_number', $args );

		$args = array(
			'id'    => $this->get_field_id( 'excerpt_length' ),
			'name'  => $this->get_field_name( 'excerpt_length' ),
			'value' => $excerpt_length,
			'label' => __( 'Excerpt length:', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_number', $args );

		$slider = hocwp_get_value_by_key( $instance, 'slider', hocwp_get_value_by_key( $this->args, 'slider' ) );
		$args   = array(
			'id'    => $this->get_field_id( 'slider' ),
			'name'  => $this->get_field_name( 'slider' ),
			'value' => $slider,
			'label' => __( 'Display post as slider?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'hide_thumbnail' ),
			'name'  => $this->get_field_name( 'hide_thumbnail' ),
			'value' => $hide_thumbnail,
			'label' => __( 'Hide post thumbnail?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'widget_title_link_category' ),
			'name'  => $this->get_field_name( 'widget_title_link_category' ),
			'value' => $widget_title_link_category,
			'label' => __( 'Link widget title with category?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		$args = array(
			'id'    => $this->get_field_id( 'category_as_widget_title' ),
			'name'  => $this->get_field_name( 'category_as_widget_title' ),
			'value' => $category_as_widget_title,
			'label' => __( 'Display category name as widget title?', 'hocwp-theme' )
		);
		hocwp_widget_field( 'hocwp_field_input_checkbox', $args );

		hocwp_field_widget_after();
	}

	public function update( $new_instance, $old_instance ) {
		$instance                               = $old_instance;
		$instance['title']                      = strip_tags( hocwp_get_value_by_key( $new_instance, 'title' ) );
		$instance['post_type']                  = hocwp_get_value_by_key( $new_instance, 'post_type', json_encode( $this->args['post_type'] ) );
		$instance['number']                     = hocwp_get_value_by_key( $new_instance, 'number', $this->args['number'] );
		$instance['by']                         = hocwp_get_value_by_key( $new_instance, 'by', $this->args['by'] );
		$instance['category']                   = hocwp_get_value_by_key( $new_instance, 'category', json_encode( $this->args['category'] ) );
		$instance['full_width_post']            = hocwp_get_value_by_key( $new_instance, 'full_width_post', $this->args['full_width_post'] );
		$width                                  = hocwp_get_value_by_key( $new_instance, 'thumbnail_size_width', $this->args['thumbnail_size'][0] );
		$height                                 = hocwp_get_value_by_key( $new_instance, 'thumbnail_size_height', $this->args['thumbnail_size'][1] );
		$instance['thumbnail_size']             = array( $width, $height );
		$instance['slider']                     = hocwp_checkbox_post_data_value( $new_instance, 'slider' );
		$instance['title_length']               = hocwp_get_value_by_key( $new_instance, 'title_length', $this->args['title_length'] );
		$instance['excerpt_length']             = hocwp_get_value_by_key( $new_instance, 'excerpt_length', $this->args['excerpt_length'] );
		$instance['hide_thumbnail']             = hocwp_checkbox_post_data_value( $new_instance, 'hide_thumbnail' );
		$instance['widget_title_link_category'] = hocwp_checkbox_post_data_value( $new_instance, 'widget_title_link_category' );
		$instance['category_as_widget_title']   = hocwp_checkbox_post_data_value( $new_instance, 'category_as_widget_title' );

		return $instance;
	}
}