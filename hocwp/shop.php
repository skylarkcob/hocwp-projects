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

$hocwp_shop_site = apply_filters( 'hocwp_shop_site', false );

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