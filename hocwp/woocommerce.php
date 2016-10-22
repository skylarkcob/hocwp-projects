<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}
$lang = hocwp_get_language();

function hocwp_get_wc_version() {
	if ( defined( 'WOOCOMMERCE_VERSION' ) ) {
		return WOOCOMMERCE_VERSION;
	}

	return '';
}

function hocwp_wc_installed() {
	return defined( 'WOOCOMMERCE_VERSION' );
}

function hocwp_wc_get_product_price( $post_id = null ) {
	if ( ! hocwp_id_number_valid( $post_id ) ) {
		$post_id = get_the_ID();
	}
	global $product;
	$h_product = $product;
	if ( ! is_a( $h_product, 'WC_Product' ) ) {
		$h_product = new WC_Product( $post_id );
	}

	return $h_product->get_price();
}

function hocwp_wc_product_price( $post_id = null, $show_full = false ) {
	if ( $show_full ) {
		if ( ! hocwp_id_number_valid( $post_id ) ) {
			$post_id = get_the_ID();
		}
		$product = new WC_Product( $post_id );
		$html    = $product->get_price_html();
		$html    = hocwp_wrap_tag( $html, 'p', 'prices price' );
		echo $html;
	} else {
		$price = hocwp_wc_get_product_price( $post_id );
		echo hocwp_wc_format_price( $price );
	}
}

function hocwp_wc_format_price( $price ) {
	return wc_price( $price );
}

function hocwp_wc_get_product_total_sales( $post_id = null ) {
	if ( ! hocwp_id_number_valid( $post_id ) ) {
		$post_id = get_the_ID();
	}

	return absint( hocwp_get_post_meta( 'total_sales', $post_id ) );
}

function hocwp_wc_is_sale( $post_id = null ) {
	$post_id = hocwp_return_post( $post_id, 'id' );
	if ( hocwp_id_number_valid( $post_id ) ) {
		$pro = new WC_Product( $post_id );

		return $pro->is_on_sale();
	}
	global $product;

	return $product->is_on_sale();
}

function hocwp_wc_onsale_flash_html( $post_id = null, $percentage = false ) {
	if ( hocwp_wc_is_sale( $post_id ) ) {
		if ( $percentage ) {
			global $product;
			$tmp = $product;
			if ( hocwp_id_number_valid( $post_id ) ) {
				$tmp = new WC_Product( $post_id );
			}
			echo '<span class="onsale">-' . hocwp_percentage( $tmp->regular_price, $tmp->sale_price ) . '%</span>';
		} else {
			echo '<span class="onsale">' . __( 'Sale!', 'hocwp-theme' ) . '</span>';
		}
	}
}

function hocwp_wc_get_shop_page() {
	$id = get_option( 'woocommerce_shop_page_id' );

	return get_post( $id );
}

function hocwp_wc_get_cart_url() {
	global $woocommerce;

	return $woocommerce->cart->get_cart_url();
}

function hocwp_wc_get_checkout_url() {
	return wc_get_checkout_url();
}

function hocwp_wc_count_cart() {
	global $woocommerce;

	return $woocommerce->cart->cart_contents_count;
}

function hocwp_wc_get_cart_total_formatted() {
	global $woocommerce;

	return $woocommerce->cart->get_cart_total();
}

function hocwp_wc_get_cart_total() {
	global $woocommerce;

	return $woocommerce->cart->total;
}

function hocwp_wc_is_variable( WC_Product $product ) {
	return $product->is_type( 'variable' );
}

function hocwp_wc_get_cart_items() {
	global $woocommerce;
	$items = $woocommerce->cart->get_cart();

	return $items;
}

function hocwp_wc_get_add_to_cart( $args = array() ) {
	$post_id = isset( $args['post_id'] ) ? absint( $args['post_id'] ) : get_the_ID();
	$product = wc_get_product( $post_id );
	if ( ! $product->is_type( 'simple' ) ) {
		return '';
	}
	$sku             = isset( $args['sku'] ) ? $args['sku'] : $product->get_sku();
	$style           = isset( $args['style'] ) ? $args['style'] : '';
	$price           = $product->get_price();
	$container_class = isset( $args['container_class'] ) ? $args['container_class'] : '';
	hocwp_add_string_with_space_before( $container_class, 'custom-add-to-cart hocwp-add-to-cart' );
	if ( 0 == $price ) {
		hocwp_add_string_with_space_before( $container_class, 'please-call' );
	}
	$field_class = isset( $args['field_class'] ) ? $args['field_class'] : '';
	$quantity    = isset( $args['quantity'] ) ? absint( $args['quantity'] ) : 1;
	$show_price  = isset( $args['show_price'] ) ? (bool) $args['show_price'] : true;
	$show_price  = ( $show_price ) ? 'true' : 'false';
	$shortcode   = do_shortcode( '[add_to_cart id="' . $post_id . '" sku="' . $sku . '" style="' . $style . '" class="' . $field_class . '" show_price="' . $show_price . '" quantity="' . $quantity . '"]' );

	return '<div class="' . $container_class . '">' . $shortcode . '</div>';
}

function hocwp_wc_sort_by() {
	woocommerce_catalog_ordering();
}

function hocwp_wc_add_to_cart( $args = array() ) {
	echo hocwp_wc_get_add_to_cart( $args );
}

function hocwp_wc_filter_product_by_sort_type() {
	woocommerce_catalog_ordering();
}

function hocwp_wc_filter_product_by_price() {
	the_widget( 'WC_Widget_Price_Filter' );
}

function hocwp_wc_add_post_type_product_to_search_url() {
	if ( is_search() ) {
		$post_type = hocwp_get_method_value( 'post_type', 'request' );
		if ( 'product' != $post_type ) {
			$url = hocwp_get_current_url();
			$url = add_query_arg( array( 'post_type' => 'product' ), $url );
			wp_redirect( $url );
			exit;
		}
	}
}

function hocwp_wc_insert_order( $data ) {
	$post_id = hocwp_get_value_by_key( $data, 'post_id' );
	if ( hocwp_id_number_valid( $post_id ) ) {
		$post = get_post( $post_id );
		if ( is_a( $post, 'WP_Post' ) && 'product' == $post->post_type ) {
			$product          = wc_get_product( $post_id );
			$variable_product = new WC_Product_Variable( $product );
			$variations       = $variable_product->get_available_variations();
			$variation_args   = array();
			$variation_id     = null;
			foreach ( $variations as $variation ) {
				$variation_id                = $variation['variation_id'];
				$variation_args['variation'] = $variation['attributes'];
			}
			$name       = hocwp_get_value_by_key( $data, 'name' );
			$phone      = hocwp_get_value_by_key( $data, 'phone' );
			$email      = hocwp_get_value_by_key( $data, 'email' );
			$address    = hocwp_get_value_by_key( $data, 'address' );
			$message    = hocwp_get_value_by_key( $data, 'message' );
			$name       = hocwp_sanitize_first_and_last_name( $name );
			$attributes = hocwp_get_value_by_key( $data, 'attributes' );
			$addresses  = array(
				'first_name' => $name['first_name'],
				'last_name'  => $name['last_name'],
				'email'      => $email,
				'phone'      => $phone,
				'address_1'  => $address
			);
			$args       = array(
				'customer_note' => $message,
				'created_via'   => 'programmatically'
			);
			if ( is_user_logged_in() ) {
				$current             = wp_get_current_user();
				$args['customer_id'] = $current->ID;
			}
			$order    = wc_create_order( $args );
			$gateway  = WC_Payment_Gateways::instance();
			$gateways = $gateway->get_available_payment_gateways();
			if ( hocwp_array_has_value( $gateways ) ) {
				$gateway = current( $gateways );
				$order->set_payment_method( $gateway );
			}
			$order->set_address( $addresses );
			$order->set_address( $addresses, 'shipping' );

			if ( hocwp_array_has_value( $attributes ) && hocwp_id_number_valid( $variation_id ) ) {
				foreach ( $attributes as $attribute ) {
					$attribute_name  = hocwp_get_value_by_key( $attribute, 'name' );
					$attribute_value = hocwp_get_value_by_key( $attribute, 'value' );
					if ( ! empty( $attribute_name ) && ! empty( $attribute_value ) ) {
						if ( isset( $variation_args['variation'][ $attribute_name ] ) ) {
							$variation_args['variation'][ $attribute_name ] = $attribute_value;
						}
					}
				}
				$variation_product = new WC_Product_Variation( $variation_id );
				$order->add_product( $variation_product, 1, $variation_args );
			} else {
				$order->add_product( $product );
			}
			$order->record_product_sales();
			$order->calculate_totals();
			$order->payment_complete();

			return $order;
		}
	}

	return false;
}

function hocwp_wc_get_cart_preview_html() {
	$cart_preview = '';
	$cart_items   = hocwp_wc_get_cart_items();
	$cart_preview .= '<ul class="cart-preview list-unstyled">';
	$cart_preview .= '<li class="title">' . __( 'Your cart', 'hocwp-theme' ) . '</li>';
	if ( hocwp_array_has_value( $cart_items ) ) {
		$cart_preview .= '<li class="cart-items"><ul class="list-unstyled list-products">';
		foreach ( $cart_items as $item ) {
			$post_id = hocwp_get_value_by_key( $item, 'product_id' );
			if ( ! hocwp_id_number_valid( $post_id ) ) {
				continue;
			}
			$quantity = absint( hocwp_get_value_by_key( $item, 'quantity' ) );
			$data     = hocwp_get_value_by_key( $item, 'data' );
			$data     = hocwp_object_to_array( $data );
			$price    = floatval( hocwp_get_value_by_key( $data, 'price' ) );
			$li       = new HOCWP_HTML( 'li' );
			$li->set_class( hocwp_get_post_class( $post_id, 'clearfix' ) );
			ob_start();
			hocwp_post_thumbnail( array( 'width' => 44, 'height' => 44, 'post_id' => $post_id ) );
			hocwp_post_title_link( array(
				'title'     => get_the_title( $post_id ),
				'permalink' => get_permalink( $post_id )
			) );
			echo '<p class="info">';
			echo '<span class="price">' . sprintf( __( 'Price: %s', 'hocwp-theme' ), hocwp_wc_format_price( $price ) ) . '</span>';
			echo '<span class="quantity">' . sprintf( __( 'Quantity: %s', 'hocwp-theme' ), number_format( $quantity ) ) . '</span>';
			echo '</p>';
			echo '<i class="fa fa-remove" data-id="' . $post_id . '"></i>';
			$li_html = ob_get_clean();
			$li->set_text( $li_html );
			$cart_preview .= $li->build();
		}
		$cart_preview .= '</ul></li>';
		$cart_preview .= '<li class="bottom">';
		$cart_preview .= '<span class="total">' . __( 'Total', 'hocwp-theme' ) . ': <strong>' . hocwp_wc_get_cart_total_formatted() . '</strong></span>';
		$cart_preview .= '<a class="btn-clickable orange go-page" href="' . hocwp_wc_get_checkout_url() . '">' . __( 'Checkout', 'hocwp-theme' ) . '</a>';
		$cart_preview .= '</li>';
	} else {
		$cart_preview .= '<li class="no-item-message">' . __( 'Your cart is empty now.', 'hocwp-theme' ) . '</li>';
	}
	$cart_preview .= '</ul>';
	$cart_preview = apply_filters( 'hocwp_wc_cart_preview_html', $cart_preview );

	return $cart_preview;
}

function hocwp_wc_get_cart( $args = array() ) {
	$lang         = hocwp_get_language();
	$title        = isset( $args['title'] ) ? $args['title'] : __( 'View your shopping cart', 'hocwp-theme' );
	$show_item    = isset( $args['show_item'] ) ? (bool) $args['show_item'] : true;
	$show_price   = isset( $args['show_price'] ) ? (bool) $args['show_price'] : true;
	$show_icon    = isset( $args['show_icon'] ) ? (bool) $args['show_icon'] : true;
	$show_preview = isset( $args['show_preview'] ) ? (bool) $args['show_preview'] : true;
	$cart         = '<div class="hocwp-cart-contents">';
	$title        = apply_filters( 'hocwp_cart_title', $title, $args );
	$cart .= '<a class="cart-content" href="' . hocwp_wc_get_cart_url() . '" title="' . $title . '">';
	$format = hocwp_get_value_by_key( $args, 'format' );
	if ( empty( $format ) ) {
		if ( $show_icon ) {
			$cart .= '<i class="fa fa-shopping-cart icon-left"></i>';
		}
		if ( $show_item ) {
			$count_cart = hocwp_wc_count_cart();
			$item_text  = sprintf( _n( '%d item', '%d items', $count_cart, 'hocwp-theme' ), $count_cart );
			$cart .= '<span class="product-number">' . $item_text . '</span>';
			if ( $show_price ) {
				if ( isset( $args['separator'] ) ) {
					if ( ! empty( $args['separator'] ) ) {
						$cart .= '<span class="sep"> ' . $args['separator'] . ' </span>';
					}
				} else {
					$cart .= '<span class="sep"> - </span>';
				}
			}
		}
		if ( $show_price ) {
			$cart .= hocwp_wc_get_cart_total_formatted();
		}
		if ( $show_preview ) {
			$cart .= '<i class="fa fa-angle-down icon-right"></i>';
		}
	} else {
		$count_cart = hocwp_wc_count_cart();
		$cart_total = hocwp_wc_get_cart_total_formatted();
		$format     = str_replace( '%COUNT_CART%', $count_cart, $format );
		$format     = str_replace( '%CART_TOTAL%', $cart_total, $format );
		$cart .= $format;
	}
	$cart .= '</a>';
	if ( $show_preview ) {
		$cart .= hocwp_wc_get_cart_preview_html();
	}
	$cart .= '</div>';

	return apply_filters( 'hocwp_wc_cart', $cart, $args );
}

function hocwp_wc_cart( $args = array() ) {
	$before = hocwp_get_value_by_key( $args, 'before' );
	echo $before;
	do_action( 'hocwp_wc_cart_before' );
	echo hocwp_wc_get_cart( $args );
	do_action( 'hocwp_wc_cart_after' );
	if ( ! empty( $before ) ) {
		$after = hocwp_get_value_by_key( $args, 'after' );
		echo $after;
	}
}

function hocwp_wc_the_cart( $args = array() ) {
	echo '<div id="hocwpCart" class="hocwp-cart wc-cart">';
	hocwp_wc_cart( $args );
	echo '</div>';
}

function hocwp_wc_get_content_single_product() {
	wc_get_template_part( 'content', 'single-product' );
}

function hocwp_wc_use_fast_buy_button() {
	$use = apply_filters( 'hocwp_wc_use_fast_buy_button', true );

	return $use;
}

function hocwp_wc_custom_quantity_input() {
	return apply_filters( 'hocwp_wc_custom_quantity_input', false );
}

$hocwp_shop_site = apply_filters( 'hocwp_shop_site', false );

if ( ! (bool) $hocwp_shop_site ) {
	return;
}

function hocwp_wc_after_single_product_title_hook() {
	do_action( 'hocwp_wc_after_single_product_title' );
}

add_action( 'woocommerce_single_product_summary', 'hocwp_wc_after_single_product_title_hook', 6 );

function hocwp_wc_after_single_product_short_description_hook() {
	do_action( 'hocwp_wc_after_single_product_short_description' );
}

add_action( 'woocommerce_single_product_summary', 'hocwp_wc_after_single_product_short_description_hook', 21 );

function hocwp_wc_after_single_product_add_to_cart_button() {
	do_action( 'hocwp_wc_after_single_product_add_to_cart_button' );
}

add_action( 'woocommerce_single_product_summary', 'hocwp_wc_after_single_product_add_to_cart_button', 31 );

function hocwp_wc_after_product_thumbnails() {
	do_action( 'hocwp_wc_after_product_thumbnails' );
}

add_action( 'woocommerce_product_thumbnails', 'hocwp_wc_after_product_thumbnails', 30 );

function hocwp_wc_product_fast_buy_button( $args = array() ) {
	$post_id            = hocwp_get_value_by_key( $args, 'post_id' );
	$post_id            = hocwp_return_post( $post_id, 'id' );
	$button_text        = hocwp_get_value_by_key( $args, 'button_text', __( 'Buy now', 'hocwp-theme' ) );
	$button_text        = apply_filters( 'hocwp_wc_fast_buy_button_text', $button_text );
	$button_description = hocwp_get_value_by_key( $args, 'button_description', __( 'Fast order, without adding products to cart.', 'hocwp-theme' ) );
	$button_description = apply_filters( 'hocwp_wc_fast_buy_button_description', $button_description );
	?>
	<button data-target="#productBuy<?php echo $post_id; ?>" data-toggle="modal"
	        class="btn-clickable orange fast-buy"
	        type="button">
		<?php
		echo $button_text;
		if ( ! empty( $button_description ) ) {
			$button_description = hocwp_wrap_tag( $button_description, 'span' );
			echo $button_description;
		}
		?>
	</button>
	<?php
}

function hocwp_wc_single_product_fast_buy_button( $args = array() ) {
	$use = hocwp_wc_use_fast_buy_button();
	if ( $use ) {
		global $product;
		$backup      = $product;
		$post_id     = hocwp_get_value_by_key( $args, 'post_id' );
		$post_id     = hocwp_return_post( $post_id, 'id' );
		$tmp         = new WC_Product( $product );
		$product     = $tmp;
		$show_button = (bool) hocwp_get_value_by_key( $args, 'show_button', true );
		hocwp_div_clear();
		do_action( 'hocwp_wc_before_fast_buy_button' );
		if ( $show_button ) {
			$args['post_id'] = $post_id;
			hocwp_wc_product_fast_buy_button( $args );
		}
		?>
		<div id="productBuy<?php echo $post_id; ?>" role="dialog" tabindex="-1" class="modal fade product-fast-buy">
			<div class="modal-dialog">
				<div class="modal-content clearfix">
					<div class="modal-header">
						<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span
								aria-hidden="true">×</span></button>
						<h4 class="modal-title"><?php _e( 'Fast order', 'hocwp-theme' ); ?></h4>
					</div>
					<div class="modal-body">
						<div class="row row-medium">
							<div class="col-xs-12 col-md-6 info-column">
								<div class="product-info">
									<?php
									hocwp_post_thumbnail( array( 'bfi_thumb' => false, 'loop' => false ) );
									hocwp_post_title_single( array( 'tag' => 'h2' ) );
									$get_variations = sizeof( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
									$attributes     = array();
									if ( hocwp_wc_is_variable( $product ) ) {
										$tmp        = new WC_Product_Variable( $product );
										$product    = $tmp;
										$attributes = $product->get_variation_attributes();
									}
									$attribute_keys = array_keys( $attributes );
									//$selected_attributes = $product->get_variation_default_attributes();
									$available_variations = false;
									if ( hocwp_wc_is_variable( $product ) ) {
										$available_variations = $get_variations ? $product->get_available_variations() : false;
									}
									if ( empty( $available_variations ) && false !== $available_variations ) : ?>
										<p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'hocwp-theme' ); ?></p>
									<?php else : ?>
										<?php if ( hocwp_array_has_value( $attributes ) ) : ?>
											<form class="variations_form cart attributes-form" method="post">
												<table class="variations" cellspacing="0">
													<tbody>
													<?php foreach ( $attributes as $attribute_name => $options ) : ?>
														<tr>
															<td class="label"><label
																	for="<?php echo sanitize_title( $attribute_name ); ?>"><?php echo wc_attribute_label( $attribute_name ); ?></label>
															</td>
															<td class="value">
																<?php
																$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $product->get_variation_default_attribute( $attribute_name );
																wc_dropdown_variation_attribute_options( array(
																	'options'   => $options,
																	'attribute' => $attribute_name,
																	'product'   => $product,
																	'selected'  => $selected
																) );
																echo end( $attribute_keys ) === $attribute_name ? apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . __( 'Clear', 'hocwp-theme' ) . '</a>' ) : '';
																?>
															</td>
														</tr>
													<?php endforeach; ?>
													</tbody>
												</table>
											</form>
										<?php endif; ?>
									<?php endif;
									hocwp_wc_product_price( null, true );
									?>
								</div>
							</div>
							<div class="col-xs-12 col-md-6 customer-column">
								<div class="customer-info">
									<?php
									$name    = '';
									$email   = '';
									$phone   = '';
									$address = '';
									if ( is_user_logged_in() ) {
										$current   = wp_get_current_user();
										$name      = get_user_meta( $current->ID, 'billing_first_name', true );
										$last_name = get_user_meta( $current->ID, 'billing_last_name', true );
										if ( ! empty( $last_name ) ) {
											$name = $last_name . ' ' . $name;
										}
										$name = trim( $name );
										if ( empty( $name ) ) {
											$name = $current->display_name;
										}
										$email = get_user_meta( $current->ID, 'billing_email', true );
										if ( ! is_email( $email ) ) {
											$email = $current->user_email;
										}
										$phone   = get_user_meta( $current->ID, 'billing_phone', true );
										$address = get_user_meta( $current->ID, 'billing_address_1', true );
									}
									?>
									<form class="order-form" method="post">
										<div class="form-group">
											<p><?php echo sprintf( __( 'Required fields are marked as %s', 'hocwp-theme' ), HOCWP_REQUIRED_HTML ); ?></p>
										</div>
										<div class="form-group">
											<input type="text" required aria-required="true"
											       value="<?php echo $name; ?>" class="full-name form-control"
											       placeholder="<?php _e( 'Full name *', 'hocwp-theme' ); ?>"
											       name="fullname">
										</div>
										<div class="form-group">
											<input type="text" class="phone form-control" value="<?php echo $phone; ?>"
											       placeholder="<?php _e( 'Phone', 'hocwp-theme' ); ?>" name="phone">
										</div>
										<div class="form-group">
											<input type="text" required aria-required="true"
											       value="<?php echo $email; ?>" class="email form-control"
											       placeholder="<?php _e( 'Email *', 'hocwp-theme' ); ?>" name="email">
										</div>
										<div class="form-group">
											<input type="text" class="address form-control"
											       value="<?php echo $address; ?>"
											       placeholder="<?php _e( 'Address', 'hocwp-theme' ); ?>"
											       name="address">
										</div>
										<div class="form-group">
											<label for="message"><?php _e( 'Note:', 'hocwp-theme' ); ?></label>
											<textarea id="message" name="message"
											          class="message form-control"></textarea>
										</div>
										<div class="form-group">
											<button class="btn-clickable orange" data-id="<?php echo $post_id; ?>">
												<span><?php _e( 'Put order', 'hocwp-theme' ); ?></span>
											</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		$product = $backup;
	}
}

add_action( 'hocwp_wc_after_single_product_add_to_cart_button', 'hocwp_wc_single_product_fast_buy_button' );

function hocwp_wc_after_single_product_summary() {
	do_action( 'hocwp_wc_after_single_product_summary' );
}

add_action( 'woocommerce_after_single_product_summary', 'hocwp_wc_after_single_product_summary', 0 );

function hocwp_wc_add_vietnam_dong_currency( $currencies ) {
	$currencies['VNDU'] = __( 'Vietnamese Dong', 'hocwp-theme' );

	return $currencies;
}

if ( 'vi' == $lang ) {
	add_filter( 'woocommerce_currencies', 'hocwp_wc_add_vietnam_dong_currency' );
}

function hocwp_wc_vietnam_dong_currency_symbol( $currency_symbol, $currency ) {
	switch ( $currency ) {
		case 'VNDU':
			$currency_symbol = 'Đ';
			break;
	}

	return $currency_symbol;
}

if ( 'vi' == $lang ) {
	add_filter( 'woocommerce_currency_symbol', 'hocwp_wc_vietnam_dong_currency_symbol', 10, 2 );
}

function hocwp_wc_single_add_to_cart_button_text() {
	$text = __( 'Add to cart', 'hocwp-theme' );
	$text = apply_filters( 'hocwp_wc_single_add_to_cart_button_text', $text );

	return $text;
}

if ( 'vi' == $lang ) {
	add_filter( 'woocommerce_product_single_add_to_cart_text', 'hocwp_wc_single_add_to_cart_button_text', 99 );
}

function hocwp_wc_product_add_to_cart_text() {
	$text = __( 'Add to cart', 'hocwp-theme' );
	$text = apply_filters( 'hocwp_wc_add_to_cart_button_text', $text );

	return $text;
}

if ( 'vi' == $lang ) {
	add_filter( 'woocommerce_product_add_to_cart_text', 'hocwp_wc_product_add_to_cart_text' );
}

add_filter( 'hocwp_track_user_viewed_posts', '__return_true' );

function hocwp_wc_add_to_cart_fragments( $fragments ) {
	$args = apply_filters( 'hocwp_wc_cart_content_ajax_args', array(), $fragments );
	ob_start();
	hocwp_wc_cart( $args );
	$cart_contents                        = ob_get_clean();
	$cart_contents                        = apply_filters( 'hocwp_wc_cart_content_ajax', $cart_contents, $fragments );
	$fragments['div.hocwp-cart-contents'] = $cart_contents;

	return $fragments;
}

add_filter( 'woocommerce_add_to_cart_fragments', 'hocwp_wc_add_to_cart_fragments' );

function hocwp_wc_remove_cart_item_ajax_callback() {
	$result  = array(
		'updated' => false
	);
	$post_id = hocwp_get_method_value( 'post_id' );
	if ( hocwp_id_number_valid( $post_id ) ) {
		$WC      = WC();
		$updated = false;
		foreach ( $WC->cart->get_cart() as $cart_item_key => $cart_item ) {
			$prod_id = $cart_item['product_id'];
			if ( $post_id == $prod_id ) {
				$WC->cart->set_quantity( $cart_item_key, 0, true );
				$updated = true;
				break;
			}
		}
		if ( $updated ) {
			$cart_contents = hocwp_wc_add_to_cart_fragments( array() );
			$cart_contents = hocwp_get_value_by_key( $cart_contents, 'div.hocwp-cart-contents' );
			if ( empty( $cart_contents ) ) {
				$updated = false;
			} else {
				$result['cart_contents'] = $cart_contents;
			}
		}
		$result['updated'] = $updated;
	}
	wp_send_json( $result );
}

add_action( 'wp_ajax_hocwp_wc_remove_cart_item', 'hocwp_wc_remove_cart_item_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_wc_remove_cart_item', 'hocwp_wc_remove_cart_item_ajax_callback' );

function hocwp_wc_order_item_ajax_callback() {
	$result  = array(
		'success'   => false,
		'html_data' => '<p class="alert alert-danger">' . __( 'There was an error occurred, please try again.', 'hocwp-theme' ) . '</p>'
	);
	$post_id = hocwp_get_method_value( 'post_id' );
	if ( hocwp_id_number_valid( $post_id ) ) {
		$post = get_post( $post_id );
		if ( is_a( $post, 'WP_Post' ) && 'product' == $post->post_type ) {
			$name       = hocwp_get_method_value( 'name' );
			$phone      = hocwp_get_method_value( 'phone' );
			$email      = hocwp_get_method_value( 'email' );
			$address    = hocwp_get_method_value( 'address' );
			$message    = hocwp_get_method_value( 'message' );
			$attributes = hocwp_get_method_value( 'attributes' );
			$order      = hocwp_wc_insert_order( array(
				'post_id'    => $post_id,
				'name'       => $name,
				'email'      => $email,
				'phone'      => $phone,
				'address'    => $address,
				'message'    => $message,
				'attributes' => $attributes
			) );
			if ( false !== $order ) {
				$result['success']   = true;
				$result['html_data'] = '<p class="alert alert-success">' . __( 'Your order has been successfully saved, we will reply to you as soon as possible.', 'hocwp-theme' ) . '</p>';
			}
		}
	}
	wp_send_json( $result );
}

add_action( 'wp_ajax_hocwp_wc_order_item', 'hocwp_wc_order_item_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_wc_order_item', 'hocwp_wc_order_item_ajax_callback' );

function hocwp_wc_after_cart_table() {
	$page      = hocwp_wc_get_shop_page();
	$permalink = apply_filters( 'hocwp_return_shop_url', get_permalink( $page ) );
	?>
	<a title="" href="<?php echo $permalink; ?>" class="btn-grey hocwp-button return-shop"><i
			class="fa fa-angle-left icon-left"></i> <?php _e( 'Continue shopping', 'hocwp-theme' ); ?>
	</a>
	<?php
	do_action( 'hocwp_wc_after_return_shop_button' );
}

add_action( 'woocommerce_after_cart_table', 'hocwp_wc_after_cart_table' );

function hocwp_wc_after_single_product_related() {
	do_action( 'hocwp_wc_after_single_product_related' );
}

add_action( 'woocommerce_after_single_product_summary', 'hocwp_wc_after_single_product_related', 21 );

function hocwp_wc_disable_related_product( $args ) {
	$disable = apply_filters( 'hocwp_wc_disable_related_product', false );
	if ( $disable ) {
		$args = array();
	}

	return $args;
}

add_filter( 'woocommerce_related_products_args', 'hocwp_wc_disable_related_product', 10 );

function hocwp_wc_checkout_fields( $fields ) {
	if ( 'vi' == hocwp_get_language() ) {
		unset( $fields['billing']['billing_postcode'] );
		unset( $fields['billing']['billing_country'] );
		unset( $fields['billing']['billing_company'] );
		unset( $fields['billing']['billing_address_2'] );
		unset( $fields['billing']['billing_city'] );
	}

	return $fields;
}

add_filter( 'woocommerce_checkout_fields', 'hocwp_wc_checkout_fields' );

function hocwp_wc_before_single_variation_quantity() {
	do_action( 'hocwp_wc_before_single_variation_quantity' );
}

add_action( 'woocommerce_single_variation', 'hocwp_wc_before_single_variation_quantity', 19 );

function hocwp_wc_after_single_product_meta() {
	do_action( 'hocwp_wc_after_single_product_meta' );
}

add_action( 'woocommerce_single_product_summary', 'hocwp_wc_after_single_product_meta', 41 );

function hocwp_wc_after_add_to_cart_button() {
	do_action( 'hocwp_wc_after_add_to_cart_button' );
}

add_action( 'woocommerce_after_add_to_cart_button', 'hocwp_wc_after_add_to_cart_button' );

function hocwp_wc_on_product_updated( $meta_id, $object_id, $meta_key, $meta_value ) {
	if ( hocwp_id_number_valid( $meta_id ) ) {
		$post = get_post( $object_id );
		if ( 'product' == $post->post_type ) {
			if ( '_featured' == $meta_key ) {
				if ( 'yes' == $meta_value ) {
					update_post_meta( $object_id, 'featured', 1 );
				} else {
					update_post_meta( $object_id, 'featured', 0 );
				}
			} elseif ( 'featured' == $meta_key ) {
				if ( 1 == $meta_value ) {
					update_post_meta( $object_id, '_featured', 'yes' );
				} else {
					update_post_meta( $object_id, '_featured', 'no' );
				}
			}
		}
	}
}

add_action( 'updated_postmeta', 'hocwp_wc_on_product_updated', 10, 4 );

function hocwp_wc_body_classes( $classes ) {
	if ( hocwp_wc_custom_quantity_input() ) {
		$classes[] = 'hocwp-custom-quantity';
	}
	$classes[] = 'hocwp-shop-site';

	if ( is_singular( 'product' ) ) {
		$left_gallery = hocwp_wc_single_product_gallery_left();
		if ( $left_gallery ) {
			$classes[] = 'gallery-on-left';
		}
		unset( $left_gallery );

		$thumbnail_on_gallery = hocwp_wc_single_product_thumbnail_on_gallery();
		if ( $thumbnail_on_gallery ) {
			$classes[] = 'thumbnail-on-gallery';
		}
		unset( $thumbnail_on_gallery );

		$thumbnail_zooming = hocwp_wc_single_product_thumbnail_zooming();
		if ( $thumbnail_zooming ) {
			$classes[] = 'thumbnail-zooming';
		}
		unset( $thumbnail_zooming );

		$thumbnail_preview = hocwp_wc_single_product_gallery_preview();
		if ( $thumbnail_preview ) {
			$classes[] = 'thumbnail-preview';
			$classes[] = 'gallery-preview';
		}
		unset( $thumbnail_preview );
	}
	if ( is_woocommerce() ) {
		$classes[] = 'archive-product';
	}

	return $classes;
}

add_filter( 'body_class', 'hocwp_wc_body_classes' );

$custom_quantity_input = hocwp_wc_custom_quantity_input();

if ( $custom_quantity_input ) {
	function woocommerce_quantity_input( $args = array(), $product = null, $echo = true ) {
		global $product;
		$defaults       = array(
			'input_name'  => 'quantity',
			'input_value' => '1',
			'max_value'   => apply_filters( 'woocommerce_quantity_input_max', '', $product ),
			'min_value'   => apply_filters( 'woocommerce_quantity_input_min', '', $product ),
			'step'        => apply_filters( 'woocommerce_quantity_input_step', 1, $product ),
			'style'       => apply_filters( 'woocommerce_quantity_style', 'float:left; margin-right:10px;', $product ),
			'size'        => apply_filters( 'woocommerce_quantity_input_size', 4, $product ),
			'label'       => apply_filters( 'woocommerce_quantity_input_label', '', $product )
		);
		$args           = apply_filters( 'woocommerce_quantity_input_args', $args, $product );
		$args           = apply_filters( 'hocwp_wc_quantity_input_args', $args, $product );
		$args           = wp_parse_args( $args, $defaults );
		$quantity_input = apply_filters( 'hocwp_wc_quantity_input_pre', '', $product, $args );
		if ( empty( $quantity_input ) ) {
			$input_name  = $args['input_name'];
			$input_value = $args['input_value'];
			$label       = $args['label'];
			$min         = hocwp_get_value_by_key( $args, 'min', 1 );
			$max         = hocwp_get_value_by_key( $args, 'max', 20 );
			$step        = hocwp_get_value_by_key( $args, 'step', 1 );
			$size        = hocwp_get_value_by_key( $args, 'size', 4 );
			$input       = new HOCWP_HTML( 'input' );
			$input->set_attribute( 'type', 'number' );
			$input->set_attribute( 'size', $size );
			$input->set_class( 'input-text qty text input-number' );
			$input->set_attribute( 'title', __( 'Qty', 'hocwp-theme' ) );
			$input->set_attribute( 'value', $input_value );
			$input->set_attribute( 'name', $input_name );
			$input->set_attribute( 'max', $max );
			$input->set_attribute( 'min', $min );
			$input->set_attribute( 'step', $step );
			$container_class = 'quantity hocwp-custom-quantity';
			if ( ! empty( $label ) ) {
				hocwp_add_string_with_space_before( $container_class, 'with-label has-label' );
			}
			ob_start();
			?>
			<div class="<?php echo $container_class; ?>">
				<?php
				if ( ! empty( $label ) ) {
					$lb = new HOCWP_HTML( 'label' );
					$lb->set_text( $label );
					$lb->output();
				}
				?>
				<input type="button" class="minus qty-control btn-down number-down" value="-">
				<?php $input->output(); ?>
				<input type="button" class="plus qty-control btn-up number-up" value="+">
			</div>
			<?php
			$quantity_input = ob_get_clean();
		}
		$quantity_input = apply_filters( 'hocwp_wc_quantity_input', $quantity_input, $product, $args );
		if ( $echo ) {
			echo $quantity_input;
		}

		return $quantity_input;
	}
}

function hocwp_wc_is_price_filter_active( $use ) {
	return apply_filters( 'hocwp_wc_use_price_filter', $use );
}

add_filter( 'woocommerce_is_price_filter_active', 'hocwp_wc_is_price_filter_active' );

function hocwp_wc_woocommerce_account_menu_item_classes( $classes, $endpoint ) {
	return $classes;
}

add_filter( 'woocommerce_account_menu_item_classes', 'hocwp_wc_woocommerce_account_menu_item_classes', 10, 2 );

function hocwp_wc_get_thumbnail_size() {
	$size   = get_option( 'shop_thumbnail_image_size' );
	$size   = hocwp_sanitize_size( $size );
	$width  = $size[0];
	$height = $size[1];
	if ( $width < 1 ) {
		$width = 180;
	}
	if ( $height < 1 ) {
		$height = $width;
	}

	return array( $width, $height );
}

function hocwp_wc_get_template( $slug ) {
	if ( ! strpos( $slug, '.php' ) ) {
		$slug .= '.php';
	}
	wc_get_template( $slug );
}

function hocwp_wc_pre_get_posts( WP_Query $query ) {
	if ( $query->is_main_query() ) {
		if ( is_home() ) {
			$query->set( 'post_type', 'product' );
		}
	}

	return $query;
}

if ( ! is_admin() ) {
	add_action( 'pre_get_posts', 'hocwp_wc_pre_get_posts' );
}

function hocwp_wc_post_type_user_large_thumbnail( $types ) {
	$types[] = 'product';

	return $types;
}

add_filter( 'hocwp_post_type_user_large_thumbnail', 'hocwp_wc_post_type_user_large_thumbnail' );

function hocwp_wc_before_container() {
	do_action( 'hocwp_wc_before_container' );
}

add_action( 'woocommerce_before_main_content', 'hocwp_wc_before_container', 1 );

function hocwp_wc_after_sidebar() {
	do_action( 'hocwp_wc_after_sidebar' );
	do_action( 'hocwp_wc_after_container' );
}

add_action( 'woocommerce_sidebar', 'hocwp_wc_after_sidebar', 99 );

function hocwp_wc_add_settings_field( &$settings, $item, $section = 'catalog_options' ) {
	if ( is_array( $settings ) ) {
		$count = 0;
		foreach ( $settings as $key => $setting ) {
			$type = hocwp_get_value_by_key( $setting, 'type' );
			if ( 'sectionend' == $type ) {
				$id = hocwp_get_value_by_key( $setting, 'id' );
				if ( $section == $id ) {
					break;
				}
			}
			$count ++;
		}
		hocwp_array_insert( $settings, $count, $item );
	}
}

function hocwp_wc_add_product_display_catalog_settings( &$settings, $item ) {
	hocwp_wc_add_settings_field( $settings, $item );
}

function hocwp_wc_product_settings_page( $settings ) {
	$item = array(
		'title'   => __( 'Thumbnail on gallery', 'hocwp-theme' ),
		'desc'    => __( 'Auto add current product thumbnail into it\'s gallery', 'hocwp-theme' ),
		'id'      => 'hocwp_single_product_thumbnail_on_gallery',
		'default' => 'no',
		'type'    => 'checkbox'
	);
	hocwp_wc_add_settings_field( $settings, $item, 'image_options' );

	$item = array(
		'title'   => __( 'Thumbnail zooming', 'hocwp-theme' ),
		'desc'    => __( 'Add function allow user can zoom current product image on single page', 'hocwp-theme' ),
		'id'      => 'hocwp_single_product_thumbnail_zoom',
		'default' => 'no',
		'type'    => 'checkbox'
	);
	hocwp_wc_add_settings_field( $settings, $item, 'image_options' );

	$item = array(
		'title'   => __( 'Gallery on left', 'hocwp-theme' ),
		'desc'    => __( 'Display gallery on the left of single product main image', 'hocwp-theme' ),
		'id'      => 'hocwp_single_product_gallery_left',
		'default' => 'no',
		'type'    => 'checkbox'
	);
	hocwp_wc_add_settings_field( $settings, $item, 'image_options' );

	$item = array(
		'title'   => __( 'Gallery preview', 'hocwp-theme' ),
		'desc'    => __( 'Auto show gallery image in current product thumbnail on mouse hover', 'hocwp-theme' ),
		'id'      => 'hocwp_single_product_gallery_preview',
		'default' => 'yes',
		'type'    => 'checkbox'
	);
	hocwp_wc_add_settings_field( $settings, $item, 'image_options' );

	$item = array(
		'title' => __( 'Additional Settings', 'hocwp-theme' ),
		'type'  => 'title',
		'desc'  => __( 'You can change number products to show on archive page.', 'hocwp-theme' ),
		'id'    => 'hocwp_additional_settings'
	);
	hocwp_wc_add_settings_field( $settings, $item, '' );

	$item = array(
		'title'             => __( 'Posts per page', 'hocwp-theme' ),
		'id'                => 'hocwp_product_posts_per_page',
		'type'              => 'number',
		'custom_attributes' => array(
			'min'  => 1,
			'step' => 1
		),
		'css'               => 'width: 80px;',
		'default'           => hocwp_get_posts_per_page(),
		'autoload'          => false
	);
	hocwp_wc_add_settings_field( $settings, $item, 'hocwp_additional_settings' );

	$item = array(
		'title'   => __( 'Percentage sale flash', 'hocwp-theme' ),
		'desc'    => __( 'Display product sale flash as percentage instead of text', 'hocwp-theme' ),
		'id'      => 'hocwp_product_percentage_sale_flash',
		'default' => 'no',
		'type'    => 'checkbox'
	);
	hocwp_wc_add_settings_field( $settings, $item, 'hocwp_additional_settings' );

	$item = array(
		'type' => 'sectionend',
		'id'   => 'hocwp_additional_settings'
	);
	hocwp_wc_add_settings_field( $settings, $item, '' );

	return $settings;
}

add_filter( 'woocommerce_product_settings', 'hocwp_wc_product_settings_page' );

function hocwp_wc_sale_flash_filter( $html, $post, $product ) {
	$use = (bool) get_option( 'hocwp_product_percentage_sale_flash' );
	$use = apply_filters( 'hocwp_wc_product_percentage_sale_flash', $use, $post, $product );
	if ( $use && hocwp_wc_is_sale( $product->id ) && hocwp_is_positive_number( $product->sale_price ) ) {
		$html = '<span class="onsale">-' . hocwp_percentage( $product->regular_price, $product->sale_price ) . '%</span>';
	}
	$html = apply_filters( 'hocwp_wc_sale_flash', $html, $post, $product );

	return $html;
}

add_filter( 'woocommerce_sale_flash', 'hocwp_wc_sale_flash_filter', 10, 3 );

function hocwp_wc_on_wp_hook() {

}

add_action( 'woocommerce_before_single_product_summary', 'hocwp_wc_on_wp_hook', 10 );

function hocwp_wc_review_order_before_submit() {

}

add_action( 'woocommerce_review_order_before_submit', 'hocwp_wc_review_order_before_submit' );

function hocwp_wc_review_order_after_submit() {
	$url    = hocwp_wc_get_cart_url();
	$button = new HOCWP_HTML( 'a' );
	$button->set_href( $url );
	$button->set_text( __( 'Back to cart page', 'hocwp-theme' ) );
	$button->add_class( 'btn navigation-link pull-right' );
	$button->output();
}

add_action( 'woocommerce_review_order_after_submit', 'hocwp_wc_review_order_after_submit' );

function hocwp_wc_breadcrumb_defaults( $args ) {
	$args['delimiter'] = '<span class="delimiter sep separator"><i class="fa fa-angle-right"></i></span>';

	return $args;
}

add_filter( 'woocommerce_breadcrumb_defaults', 'hocwp_wc_breadcrumb_defaults' );

function hocwp_wc_single_product_image_html( $html, $post_id ) {
	$thumbnail_zoom = hocwp_wc_single_product_thumbnail_zooming();
	if ( $thumbnail_zoom ) {
		global $post, $product;
		if ( has_post_thumbnail() ) {
			$image_ids = $product->get_gallery_attachment_ids();
			$count     = count( $image_ids );
			$gallery   = $count > 0 ? '[product-gallery]' : '';
			$thumb_id  = get_post_thumbnail_id();
			$props     = wc_get_product_attachment_props( $thumb_id, $post );
			$image_url = $props['url'];
			$size      = get_option( 'shop_single_image_size' );
			$size      = hocwp_sanitize_size( $size );
			$size      = apply_filters( 'single_product_large_thumbnail_size', $size );
			$args      = array(
				'title' => $props['title'],
				'alt'   => $props['alt']
			);
			$image     = get_the_post_thumbnail( $post, 'full', $args );
			$html      = sprintf(
				'<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s" data-rel="prettyPhoto%s">%s</a>',
				esc_url( $image_url ),
				esc_attr( $props['caption'] ),
				$gallery,
				$image
			);
			$html      = apply_filters( 'hocwp_wc_single_product_image_html', $html, $post->ID );
		}
	}

	return $html;
}

add_filter( 'woocommerce_single_product_image_html', 'hocwp_wc_single_product_image_html', 10, 2 );

function hocwp_wc_single_product_thumbnail_on_gallery() {
	$thumbnail_on_gallery = get_option( 'hocwp_single_product_thumbnail_on_gallery' );
	$thumbnail_on_gallery = hocwp_string_to_bool( $thumbnail_on_gallery );

	return apply_filters( 'hocwp_wc_single_product_thumbnail_on_gallery', $thumbnail_on_gallery );
}

function hocwp_wc_single_product_thumbnail_zooming() {
	$result = get_option( 'hocwp_single_product_thumbnail_zoom' );
	$result = hocwp_string_to_bool( $result );

	return apply_filters( 'hocwp_wc_single_product_thumbnail_zooming', $result );
}

function hocwp_wc_single_product_gallery_preview() {
	$result = get_option( 'hocwp_single_product_gallery_preview' );
	$result = hocwp_string_to_bool( $result );

	return apply_filters( 'hocwp_wc_single_product_gallery_preview', $result );
}

function hocwp_wc_single_product_gallery_left() {
	$result = get_option( 'hocwp_single_product_gallery_left' );
	$result = hocwp_string_to_bool( $result );

	return apply_filters( 'hocwp_wc_single_product_gallery_left', $result );
}

function hocwp_wc_product_gallery_attachment_ids( $ids, $product ) {
	$thumbnail_on_gallery = hocwp_wc_single_product_thumbnail_on_gallery();
	if ( $thumbnail_on_gallery && hocwp_array_has_value( $ids ) ) {
		if ( has_post_thumbnail() ) {
			$thumb_id = get_post_thumbnail_id();
			array_unshift( $ids, $thumb_id );
		}
	}

	return $ids;
}

add_filter( 'woocommerce_product_gallery_attachment_ids', 'hocwp_wc_product_gallery_attachment_ids', 10, 2 );