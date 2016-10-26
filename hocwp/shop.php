<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}
function hocwp_register_post_type_product() {
	$slug = apply_filters( 'hocwp_post_type_product_slug', 'product' );
	$args = array(
		'name'          => __( 'Products', 'hocwp-theme' ),
		'singular_name' => __( 'Product', 'hocwp-theme' ),
		'post_type'     => 'product',
		'slug'          => $slug,
		'menu_icon'     => 'dashicons-products'
	);
	hocwp_register_post_type_normal( $args );
}

function hocwp_register_post_type_deal( $args = array() ) {
	$slug     = apply_filters( 'hocwp_post_type_deal_slug', 'deal' );
	$defaults = array(
		'name'          => __( 'Deals', 'hocwp-theme' ),
		'singular_name' => __( 'Deal', 'hocwp-theme' ),
		'post_type'     => 'deal',
		'slug'          => $slug
	);
	$args     = wp_parse_args( $defaults, $args );
	hocwp_register_post_type_normal( $args );
}

function hocwp_register_taxonomy_product_cat() {
	$slug = apply_filters( 'hocwp_taxonomy_product_cat_slug', 'product-cat' );
	$args = array(
		'name'          => __( 'Product Categories', 'hocwp-theme' ),
		'singular_name' => __( 'Product Category', 'hocwp-theme' ),
		'menu_name'     => __( 'Categories', 'hocwp-theme' ),
		'taxonomy'      => 'product_cat',
		'slug'          => $slug,
		'post_types'    => 'product'
	);
	hocwp_register_taxonomy( $args );
}

function hocwp_register_taxonomy_product_tag() {
	$slug = apply_filters( 'hocwp_taxonomy_product_tag_slug', 'product-tag' );
	$args = array(
		'name'          => __( 'Product Tags', 'hocwp-theme' ),
		'singular_name' => __( 'Product Tag', 'hocwp-theme' ),
		'menu_name'     => __( 'Tags', 'hocwp-theme' ),
		'taxonomy'      => 'product_tag',
		'slug'          => $slug,
		'post_types'    => 'product'
	);
	hocwp_register_taxonomy( $args );
}

function hocwp_shop_install_post_type_and_taxonomy() {
	if ( hocwp_wc_installed() ) {
		return;
	}
	hocwp_register_post_type_product();
	hocwp_register_taxonomy_product_cat();
	hocwp_register_taxonomy_product_tag();
}

function hocwp_query_best_selling_product( $args = array() ) {
	$args['meta_key'] = 'total_sales';
	$args['orderby']  = 'meta_value_num';
	$args['order']    = 'DESC';

	return hocwp_query_product( $args );
}

function hocwp_get_product_cat_base() {
	$base = get_option( 'woocommerce_permalinks' );
	$base = hocwp_get_value_by_key( $base, 'category_base' );
	if ( empty( $base ) ) {
		$base = 'product-category';
	}

	return $base;
}

function hocwp_get_product_tag_base() {
	$base = get_option( 'woocommerce_permalinks' );
	$base = hocwp_get_value_by_key( $base, 'tag_base' );
	if ( empty( $base ) ) {
		$base = 'product-tag';
	}

	return $base;
}

function hocwp_get_product_base() {
	$page = hocwp_wc_get_shop_page();
	$base = 'product';
	if ( is_a( $page, 'WP_Post' ) ) {
		$base = $page->post_name;
	}

	return $base;
}

function hocwp_is_shop_site() {
	$hocwp_shop_site = false;
	if ( hocwp_wc_installed() ) {
		$hocwp_shop_site = true;
	}
	$hocwp_shop_site = apply_filters( 'hocwp_shop_site', $hocwp_shop_site );

	return $hocwp_shop_site;
}

$hocwp_shop_site = hocwp_is_shop_site();

if ( ! (bool) $hocwp_shop_site ) {
	return;
}

function hocwp_shop_after_setup_theme() {
	if ( hocwp_wc_installed() ) {
		add_theme_support( 'woocommerce' );
	}
}

add_action( 'after_setup_theme', 'hocwp_shop_after_setup_theme' );

function hocwp_shop_pre_get_posts( $query ) {
	if ( $query->is_main_query() ) {
		if ( is_search() ) {
			$query->set( 'post_type', 'product' );
		}
	}

	return $query;
}

if ( ! is_admin() ) {
	add_action( 'pre_get_posts', 'hocwp_shop_pre_get_posts' );
}

function hocwp_get_product_posts_per_page() {
	$number = get_option( 'hocwp_product_posts_per_page' );
	if ( ! hocwp_is_positive_number( $number ) ) {
		$reading = get_option( 'hocwp_reading' );
		$number  = hocwp_get_value_by_key( $reading, 'products_per_page', hocwp_get_posts_per_page() );
	}
	$number = apply_filters( 'hocwp_product_posts_per_page', $number );

	return $number;
}

function hocwp_shop_product_meta_boxes( $post_type, $post ) {
	if ( 'product' == $post_type ) {
		$meta = new HOCWP_Meta( 'post' );
		$meta->set_title( __( 'Product Information', 'hocwp-theme' ) );
		$meta->set_id( 'hocwp_product_information' );
		$meta->add_post_type( 'product' );

		$args = array(
			'id'    => 'sku',
			'label' => __( 'SKU:', 'hocwp-theme' )
		);
		$meta->add_field( $args );

		$args = array(
			'id'             => 'regular_price',
			'label'          => __( 'Regular price:', 'hocwp-theme' ),
			'field_callback' => 'hocwp_field_input_number'
		);
		$meta->add_field( $args );

		$args = array(
			'id'             => 'sale_price',
			'label'          => __( 'Sale price:', 'hocwp-theme' ),
			'field_callback' => 'hocwp_field_input_number'
		);
		$meta->add_field( $args );

		$args = array(
			'id'             => 'out_of_stock',
			'label'          => __( 'Out of stock?', 'hocwp-theme' ),
			'field_callback' => 'hocwp_field_input_checkbox'
		);
		$meta->add_field( $args );

		$meta->init();

		$args = array(
			'post_type' => 'product',
			'field_id'  => 'short_description',
			'title'     => __( 'Short Description', 'hocwp-theme' )
		);
		hocwp_meta_box_editor( $args );

		$args = array(
			'post_type' => 'product',
			'field_id'  => 'gallery',
			'title'     => __( 'Gallery', 'hocwp-theme' )
		);
		hocwp_meta_box_editor_gallery( $args );
	}
}

function hocwp_shop_save_product_meta( $post_id ) {
	if ( ! hocwp_can_save_post( $post_id ) ) {
		return;
	}
	if ( ! hocwp_is_shop_site() || hocwp_wc_installed() ) {
		return;
	}
	if ( isset( $_POST['sku'] ) ) {
		update_post_meta( $post_id, 'sku', $_POST['sku'] );
	}
	$regular_price = hocwp_get_method_value( 'regular_price' );
	$sale_price    = hocwp_get_method_value( 'sale_price' );
	$prices        = hocwp_sanitize_product_price( $regular_price, $sale_price, $post_id );
	update_post_meta( $post_id, 'regular_price', $prices['regular_price'] );
	update_post_meta( $post_id, 'sale_price', $prices['sale_price'] );
	update_post_meta( $post_id, 'price', $prices['price'] );
	if ( isset( $_POST['short_description'] ) ) {
		update_post_meta( $post_id, 'short_description', $_POST['short_description'] );
	}
	$out_of_stock = hocwp_checkbox_post_data_value( $_POST, 'out_of_stock' );
	update_post_meta( $post_id, 'out_of_stock', $out_of_stock );
	if ( isset( $_POST['gallery'] ) ) {
		update_post_meta( $post_id, 'gallery', $_POST['gallery'] );
	}
}

add_action( 'save_post', 'hocwp_shop_save_product_meta' );

function hocwp_product_price( $post_id = null, $no_price = '', $args = array() ) {
	$post_id       = hocwp_return_post( $post_id, 'id' );
	$regular_price = hocwp_get_post_meta( 'regular_price', $post_id );
	$sale_price    = hocwp_get_post_meta( 'sale_price', $post_id );
	$prices        = hocwp_sanitize_product_price( $regular_price, $sale_price, $post_id );
	$price         = $prices['price'];
	$no_price_html = '<span class="no-price">' . __( 'Price', 'hocwp-theme' ) . ': <span class="amount">';
	$no_price_html .= __( 'Please call', 'hocwp-theme' ) . '</span></span>';
	$defaults      = array(
		'currency'          => '$',
		'currency_position' => 'left',
		'decimals'          => 2,
		'dec_point'         => '.',
		'thousands_sep'     => ',',
		'no_price_html'     => $no_price_html
	);
	$defaults      = apply_filters( 'hocwp_product_price_defaults', $defaults );
	$args          = wp_parse_args( $args, $defaults );
	$no_price_html = $args['no_price_html'];
	if ( empty( $no_price ) ) {
		$no_price = $no_price_html;
	}
	if ( ! hocwp_is_positive_number( $price ) ) {
		$price = $no_price;
	} else {
		$sale_price        = $prices['sale_price'];
		$regular_price     = $prices['regular_price'];
		$currency          = hocwp_get_value_by_key( $args, 'currency' );
		$currency          = apply_filters( 'hocwp_product_price_currency', $currency );
		$currency_position = hocwp_get_value_by_key( $args, 'currency_position', 'left' );
		$currency_position = apply_filters( 'hocwp_product_price_currency_position', $currency_position );
		$decimals          = hocwp_get_value_by_key( $args, 'decimals' );
		$dec_point         = hocwp_get_value_by_key( $args, 'dec_point' );
		$thousands_sep     = hocwp_get_value_by_key( $args, 'thousands_sep' );
		$number_format     = '%NUMBER%';
		if ( 'left' == $currency_position ) {
			$number_format = '%CURRENCY%' . $number_format;
		} else {
			$number_format .= '%CURRENCY%';
		}
		$number_format = str_replace( '%CURRENCY%', $currency, $number_format );
		$price         = number_format( $regular_price, $decimals, $dec_point, $thousands_sep );
		$price         = str_replace( '%NUMBER%', $price, $number_format );
		$price         = hocwp_wrap_tag( $price, 'span', 'amount' );
		if ( hocwp_is_positive_number( $sale_price ) ) {
			$sale_price = number_format( $sale_price, $decimals, $dec_point, $thousands_sep );
			$sale_price = str_replace( '%NUMBER%', $sale_price, $number_format );
			$sale_price = hocwp_wrap_tag( $sale_price, 'span', 'amount' );
			$sale_price = hocwp_wrap_tag( $sale_price, 'ins' );
			$price      = hocwp_wrap_tag( $price, 'del' );
			$price .= $sale_price;
		}
	}
	if ( ! empty( $price ) ) {
		$price = hocwp_wrap_tag( $price, 'p', 'price' );
		echo $price;
	}
}