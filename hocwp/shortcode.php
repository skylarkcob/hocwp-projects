<?php
function hocwp_get_all_shortcodes() {
	return $GLOBALS['shortcode_tags'];
}

function hocwp_get_all_sb_shortcodes() {
	$shortcodes = hocwp_get_all_shortcodes();
	$result     = array();
	foreach ( $shortcodes as $key => $function ) {
		if ( ( 'sb' == substr( $key, 0, 2 ) && 'sb' == substr( $function, 0, 2 ) ) || ( 'hocwp' == substr( $key, 0, 5 ) && 'hocwp' == substr( $function, 0, 5 ) ) ) {
			$result[ $key ] = $function;
		}
	}

	return $result;
}

function hocwp_get_my_shortcodes() {
	return hocwp_get_all_sb_shortcodes();
}

$use_shortcode = apply_filters( 'hocwp_add_tiny_mce_shortcode_button', false );
if ( $use_shortcode && is_admin() ) {
	new HOCWP_TinyMCE_Shortcode();
}

function hocwp_shortcode_before( $class = '', $attributes ) {
	$title = hocwp_get_value_by_key( $attributes, 'title' );
	hocwp_add_string_with_space_before( $class, 'hocwp-shortcode' );
	$class = hocwp_add_more_class( $class, 'module' );
	$class = hocwp_add_more_class( $class, 'clearfix' );
	$style = hocwp_get_value_by_key( $attributes, 'style' );
	if ( ! empty( $style ) ) {
		$style = hocwp_minify_css( $style );
		$style = rtrim( $style, ';' );
	}
	$max_width = hocwp_get_value_by_key( $attributes, 'max_width' );
	if ( ! empty( $max_width ) ) {
		$style .= ';max-width: ' . $max_width;
	}
	$column = hocwp_get_value_by_key( $attributes, 'column' );
	if ( ! empty( $column ) ) {
		hocwp_add_string_with_space_before( $class, 'column-' . $column );
	}
	$html = '<div class="' . $class . '" style="' . $style . '">';
	if ( ! empty( $title ) ) {
		$style        = '';
		$border_color = hocwp_get_value_by_key( $attributes, 'border_color' );
		if ( hocwp_is_color( $border_color ) ) {
			$color_name = hocwp_is_color_name( $border_color );
			if ( is_array( $color_name ) ) {
				$color_name = 'rgb(' . implode( ',', $color_name ) . ')';
				if ( hocwp_is_rgb_color( $color_name ) ) {
					$border_color = $color_name;
				}
			}
			$style .= 'border-color: ' . $border_color;
		}
		$html .= '<div class="module-header clearfix">';
		$html .= '<h4 class="module-name"><span style="' . $style . '">' . $title . '</span></h4>';
		$html .= '</div>';
	}
	$html .= '<div class="module-body clearfix">';

	return $html;
}

function hocwp_shortcode_after() {
	return '</div></div>';
}

function hocwp_shortcode_post_callback( $atts = array(), $content = null ) {
	$defaults       = array(
		'order'           => 'desc',
		'orderby'         => 'date',
		'by'              => 'related',
		'post_type'       => 'post',
		'title'           => __( 'Related posts', 'hocwp-theme' ),
		'offset'          => 0,
		'column'          => 4,
		'number'          => hocwp_get_posts_per_page(),
		'border_color'    => '',
		'exclude_current' => true,
		'display'         => 'left',
		'excerpt_length'  => 0,
		'style'           => '',
		'post'            => '',
		'posts'           => '',
		'thumbnail_size'  => '180,110',
		'interval'        => ''
	);
	$attributes     = shortcode_atts( $defaults, $atts );
	$transient_name = 'hocwp_shortcode_post_' . md5( json_encode( $attributes ) );
	if ( false === ( $html = get_transient( $transient_name ) ) ) {
		$order     = $attributes['order'];
		$orderby   = $attributes['orderby'];
		$by        = $attributes['by'];
		$post_type = hocwp_string_to_array( ',', $attributes['post_type'] );
		$post_type = array_map( 'trim', $post_type );
		$offset    = $attributes['offset'];
		$column    = $attributes['column'];
		if ( ! hocwp_is_positive_number( $column ) ) {
			$column = 1;
		}
		$number          = $attributes['number'];
		$posts_per_page  = hocwp_get_first_divisible_of_divisor( $number, $column );
		$exclude_current = (bool) $attributes['exclude_current'];
		$display         = $attributes['display'];
		$display         = trim( $display );
		$excerpt_length  = $attributes['excerpt_length'];
		$post            = $attributes['post'];
		$post            = hocwp_find_post( $post );
		$posts           = $attributes['posts'];
		$posts           = hocwp_string_to_array( ',', $posts );
		$thumbnail_size  = $attributes['thumbnail_size'];
		$thumbnail_size  = hocwp_sanitize_size( $thumbnail_size );
		$interval        = $attributes['interval'];

		if ( hocwp_is_post( $post ) ) {
			$display = 'full';
		}

		$class = 'hocwp-shortcode-post';
		hocwp_add_string_with_space_before( $class, $order );
		hocwp_add_string_with_space_before( $class, $by );
		hocwp_add_string_with_space_before( $class, hocwp_sanitize_html_class( $display ) );
		if ( '100%' == $display ) {
			$class = hocwp_add_more_class( $class, 'full-width' );
		}
		hocwp_add_string_with_space_before( $class, implode( ' ', $post_type ) );
		$query = null;
		if ( ! hocwp_is_post( $post ) ) {
			$args = array(
				'post_type'      => $post_type,
				'posts_per_page' => $posts_per_page,
				'offset'         => $offset,
				'order'          => $order,
				'orderby'        => $orderby
			);
			if ( ! empty( $interval ) ) {
				hocwp_query_sanitize_date_query_args( $interval, $args );
			}
			if ( $exclude_current && is_singular() ) {
				$post_id              = get_the_ID();
				$args['post__not_in'] = array( $post_id );
			}
			if ( hocwp_array_has_value( $posts ) ) {
				$args['post__in'] = $posts;
			}
			$query = hocwp_query_by( $by, $args );
		} else {
			if ( ! hocwp_is_positive_number( $excerpt_length ) ) {
				$excerpt_length = 200;
			}
		}
		if ( hocwp_is_post( $post ) || ( hocwp_object_valid( $query ) && $query->have_posts() ) ) {
			$html  = hocwp_shortcode_before( $class, $attributes );
			$width = $thumbnail_size[0];
			if ( hocwp_is_post( $post ) ) {
				global $post;
				setup_postdata( $post );
				ob_start();
				?>
				<div class="one-post">
					<div <?php post_class( '', $post ); ?>>
						<?php
						if ( $width > 0 ) {
							hocwp_post_thumbnail(
								array(
									'width'   => $thumbnail_size[0],
									'height'  => $thumbnail_size[1],
									'post_id' => $post->ID
								)
							);
						}
						hocwp_post_title_link(
							array(
								'title'     => $post->post_title,
								'permalink' => get_permalink( $post )
							)
						);
						if ( hocwp_is_positive_number( $excerpt_length ) ) {
							hocwp_entry_summary( $excerpt_length );
						}
						?>
					</div>
				</div>
				<?php
				$html .= ob_get_clean();
				wp_reset_postdata();
			} else {
				$html .= '<div class="in-loop row-small">';
				$loop = apply_filters( 'hocwp_shortcode_post_pre_loop', '', $query, $attributes );
				if ( empty( $loop ) ) {
					$count = 1;
					$style = '';
					if ( 'left' != $display && 'right' != $display ) {
						$style = 'width:' . hocwp_column_width_percentage( $column );
					}
					while ( $query->have_posts() ) {
						$query->the_post();
						$html_loop = apply_filters( 'hocwp_shortcode_post_loop', '', $attributes );
						if ( empty( $html_loop ) ) {
							$item_class = 'item';
							if ( hocwp_is_last_item( $count, $column ) ) {
								hocwp_add_string_with_space_before( $item_class, 'last-item' );
							} elseif ( hocwp_is_first_item( $count, $column ) ) {
								hocwp_add_string_with_space_before( $item_class, 'first-item' );
							}
							ob_start();
							?>
							<div class="<?php echo $item_class; ?>" style="<?php echo $style; ?>">
								<div <?php post_class(); ?>>
									<?php
									if ( $width > 0 ) {
										hocwp_post_thumbnail(
											array(
												'width'  => $thumbnail_size[0],
												'height' => $thumbnail_size[1]
											)
										);
									}
									hocwp_post_title_link();
									if ( hocwp_is_positive_number( $excerpt_length ) ) {
										hocwp_entry_summary( $excerpt_length );
									}
									?>
								</div>
							</div>
							<?php
							$html_loop = ob_get_clean();
						}
						$loop .= $html_loop;
						$count ++;
					}
					wp_reset_postdata();
				}
				$html .= $loop;
				$html .= '</div>';
			}
			$html .= hocwp_shortcode_after();
		} else {
			$html = '';
		}
		if ( ! empty( $html ) ) {
			set_transient( $transient_name, $html, WEEK_IN_SECONDS );
		}
	}

	return apply_filters( 'hocwp_shortcode_post', $html, $attributes, $content );
}

add_shortcode( 'hocwp_post', 'hocwp_shortcode_post_callback' );

function hocwp_shortcode_support_form_callback( $atts = array(), $content = null ) {
	$defaults   = array(
		'style'       => '',
		'name'        => 'full_name,email,phone,message',
		'label'       => array(
			__( 'Full name', 'hocwp-theme' ),
			__( 'Email', 'hocwp-theme' ),
			__( 'Phone', 'hocwp-theme' ),
			__( 'Message', 'hocwp-theme' )
		),
		'type'        => 'text,email,text,textarea',
		'placehoder'  => '',
		'captcha'     => false,
		'admin_email' => hocwp_get_admin_email()
	);
	$attributes = shortcode_atts( $defaults, $atts );

	$name  = hocwp_get_value_by_key( $attributes, 'name' );
	$names = explode( ',', $name );
	if ( hocwp_array_has_value( $names ) ) {
		$show_form = true;
		$label     = hocwp_get_value_by_key( $attributes, 'label' );
		if ( ! is_array( $label ) ) {
			$label = explode( ',', $label );
			$label = array_map( 'trim', $label );
		}
		$type        = hocwp_get_value_by_key( $attributes, 'type' );
		$type        = explode( ',', $type );
		$type        = array_map( 'trim', $type );
		$placeholder = hocwp_get_value_by_key( $attributes, 'placeholder' );
		if ( ! empty( $placeholder ) ) {
			$placeholder = explode( ',', $placeholder );
			$placeholder = array_map( 'trim', $placeholder );
		}
		$captcha = hocwp_get_value_by_key( $attributes, 'captcha' );
		$names   = array_map( 'trim', $names );
		$html    = hocwp_shortcode_before( 'support-form', $attributes );
		if ( isset( $_POST['submit'] ) ) {
			if ( hocwp_check_nonce( 'hocwp_support_form' ) ) {
				if ( hocwp_check_captcha() ) {
					$body = '';
					foreach ( $names as $index => $name ) {
						if ( empty( $name ) ) {
							continue;
						}
						if ( 'name' == $name ) {
							$name = 'full_name';
						}
						$field_type        = isset( $type[ $index ] ) ? $type[ $index ] : 'text';
						$field_label       = isset( $label[ $index ] ) ? $label[ $index ] : '';
						$field_placeholder = isset( $placeholder[ $index ] ) ? $placeholder[ $index ] : '';
						$row               = isset( $_POST[ $name ] ) ? $_POST[ $name ] : '';
						if ( ! empty( $field_label ) ) {
							$last_char = hocwp_get_last_char( $field_label );
							if ( ':' != $last_char ) {
								$field_label .= ':';
							}
							$row = $field_label . ' ' . $row;
						}
						if ( ! empty( $row ) ) {
							$body .= wpautop( $row );
						}
					}
					if ( empty( $body ) ) {
						$html .= hocwp_build_message( __( 'You must fill all required fields.', 'hocwp-theme' ), 'danger' );
					} else {
						$full_name = __( 'Guest', 'hocwp-theme' );
						if ( isset( $_POST['full_name'] ) ) {
							$full_name = $_POST['full_name'];
						}
						$subject     = sprintf( __( '[%1$s] Contact message from %2$s', 'hocwp-theme' ), get_bloginfo( 'name' ), $full_name );
						$admin_email = hocwp_get_value_by_key( $attributes, 'admin_email' );
						if ( ! is_email( $admin_email ) ) {
							$admin_email = hocwp_get_admin_email();
						}
						$sent = hocwp_send_html_mail( $admin_email, $subject, $body );
						if ( $sent ) {
							$show_form = false;
							$html .= hocwp_build_message( __( 'Your message has been sent, we will reply to you as soon as possible.', 'hocwp-theme' ), 'success' );
						} else {
							$html .= hocwp_build_message( __( 'There was an error occurred, please try again or contact administrators.', 'hocwp-theme' ), 'danger' );
						}
					}
				} else {
					$html .= hocwp_build_message( __( 'Captcha code is not valid.', 'hocwp-theme' ), 'danger' );
				}
			}
		}
		if ( $show_form ) {
			ob_start();
			?>
			<form class="hocwp-support-form" action="" method="post">
				<?php
				foreach ( $names as $index => $name ) {
					if ( empty( $name ) ) {
						continue;
					}
					if ( 'name' == $name ) {
						$name = 'full_name';
					}
					$field_type        = isset( $type[ $index ] ) ? $type[ $index ] : 'text';
					$field_label       = isset( $label[ $index ] ) ? $label[ $index ] : '';
					$field_placeholder = isset( $placeholder[ $index ] ) ? $placeholder[ $index ] : '';
					?>
					<div class="form-group form-row">
						<div class="row">
							<div class="col-sm-2">
								<label for="<?php echo esc_attr( $name ); ?>"><?php echo $field_label; ?></label>
							</div>
							<div class="col-sm-10">
								<?php
								$value = isset( $_POST[ $name ] ) ? $_POST[ $name ] : '';
								switch ( $field_type ) {
									case 'longtext':
									case 'textarea':
										?>
										<textarea id="<?php echo esc_attr( $name ); ?>"
										          name="<?php echo esc_attr( $name ); ?>"
										          class="form-control"
										          placeholder="<?php echo esc_attr( $field_placeholder ); ?>"><?php echo $value; ?></textarea>
										<?php
										break;
									default:
										$input_type = 'text';
										if ( 'email' == $field_type ) {
											$input_type = 'email';
										}
										?>
										<input id="<?php echo esc_attr( $name ); ?>"
										       placeholder="<?php echo esc_attr( $field_placeholder ); ?>"
										       value="<?php echo esc_attr( $value ); ?>"
										       name="<?php echo esc_attr( $name ); ?>"
										       type="<?php echo esc_attr( $input_type ); ?>">
										<?php
								}
								?>
							</div>
						</div>
					</div>
					<?php
				}
				if ( $captcha ) {
					echo '<div class="form-group form-row"><div class="row"><div class="col-sm-2"></div><div class="col-sm-10">';
					hocwp_field_captcha( array( 'input_width' => 180 ) );
					echo '</div></div></div>';
				}
				?>
				<div class="form-hidden-fields">
					<?php hocwp_nonce( 'hocwp_support_form' ); ?>
				</div>
				<div class="form-group form-row">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-10">
							<input type="submit" name="submit" class="btn btn-warning"
							       value="<?php _e( 'Send', 'hocwp-theme' ); ?>">
						</div>
					</div>
				</div>
			</form>
			<?php
			$html .= ob_get_clean();
		}
		$html .= hocwp_shortcode_after();
	}

	return apply_filters( 'hocwp_shortcode_support_form', $html, $attributes, $content );
}

add_shortcode( 'hocwp_support_form', 'hocwp_shortcode_support_form_callback' );