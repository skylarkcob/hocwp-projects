<?php
/**
 * Author: HocWP.
 * Version: 1.0.0
 * Created: 27/03/2016
 * Updated: 95/04/2016
 */

function hocwp_taxonomy_province_base() {
	$lang    = hocwp_get_language();
	$default = 'province';
	if ( 'vi' == $lang ) {
		$default = 'tinh-thanh';
	}
	$option = get_option( 'hocwp_permalink' );
	$base   = hocwp_get_value_by_key( $option, 'province_base', $default );
	$base   = apply_filters( 'hocwp_taxonomy_province_base', $base );
	if ( empty( $base ) ) {
		$base = $default;
	}

	return $base;
}

function hocwp_taxonomy_district_base() {
	$lang    = hocwp_get_language();
	$default = 'district';
	if ( 'vi' == $lang ) {
		$default = 'quan-huyen';
	}
	$option = get_option( 'hocwp_permalink' );
	$base   = hocwp_get_value_by_key( $option, 'district_base', $default );
	$base   = apply_filters( 'hocwp_taxonomy_district_base', $base );
	if ( empty( $base ) ) {
		$base = $default;
	}

	return $base;
}

function hocwp_taxonomy_ward_base() {
	$lang    = hocwp_get_language();
	$default = 'ward';
	if ( 'vi' == $lang ) {
		$default = 'phuong-xa';
	}
	$option = get_option( 'hocwp_permalink' );
	$base   = hocwp_get_value_by_key( $option, 'ward_base', $default );
	$base   = apply_filters( 'hocwp_taxonomy_ward_base', $base );
	if ( empty( $base ) ) {
		$base = $default;
	}

	return $base;
}

function hocwp_taxonomy_hamlet_base() {
	$lang    = hocwp_get_language();
	$default = 'hamlet';
	if ( 'vi' == $lang ) {
		$default = 'thon-xom';
	}
	$option = get_option( 'hocwp_permalink' );
	$base   = hocwp_get_value_by_key( $option, 'hamlet_base', $default );
	$base   = apply_filters( 'hocwp_taxonomy_hamlet_base', $base );
	if ( empty( $base ) ) {
		$base = $default;
	}

	return $base;
}

function hocwp_taxonomy_street_base() {
	$lang    = hocwp_get_language();
	$default = 'street';
	if ( 'vi' == $lang ) {
		$default = 'duong-pho';
	}
	$option = get_option( 'hocwp_permalink' );
	$base   = hocwp_get_value_by_key( $option, 'street_base', $default );
	$base   = apply_filters( 'hocwp_taxonomy_street_base', $base );
	if ( empty( $base ) ) {
		$base = $default;
	}

	return $base;
}

function hocwp_taxonomy_price_base() {
	$lang    = hocwp_get_language();
	$default = 'price';
	if ( 'vi' == $lang ) {
		$default = 'muc-gia';
	}
	$option = get_option( 'hocwp_permalink' );
	$base   = hocwp_get_value_by_key( $option, 'price_base', $default );
	$base   = apply_filters( 'hocwp_taxonomy_price_base', $base );
	if ( empty( $base ) ) {
		$base = $default;
	}

	return $base;
}

function hocwp_taxonomy_acreage_base() {
	$lang    = hocwp_get_language();
	$default = 'acreage';
	if ( 'vi' == $lang ) {
		$default = 'dien-tich';
	}
	$option = get_option( 'hocwp_permalink' );
	$base   = hocwp_get_value_by_key( $option, 'acreage_base', $default );
	$base   = apply_filters( 'hocwp_taxonomy_acreage_base', $base );
	if ( empty( $base ) ) {
		$base = $default;
	}

	return $base;
}

function hocwp_taxonomy_classifieds_type_base() {
	$lang    = hocwp_get_language();
	$default = 'type';
	if ( 'vi' == $lang ) {
		$default = 'the-loai';
	}
	$option = get_option( 'hocwp_permalink' );
	$base   = hocwp_get_value_by_key( $option, 'classifieds_type_base', $default );
	$base   = apply_filters( 'hocwp_taxonomy_classifieds_type_base', $base );
	if ( empty( $base ) ) {
		$base = $default;
	}

	return $base;
}

function hocwp_taxonomy_classifieds_object_base() {
	$lang    = hocwp_get_language();
	$default = 'object';
	if ( 'vi' == $lang ) {
		$default = 'doi-tuong';
	}
	$option = get_option( 'hocwp_permalink' );
	$base   = hocwp_get_value_by_key( $option, 'classifieds_object_base', $default );
	$base   = apply_filters( 'hocwp_taxonomy_classifieds_object_base', $base );
	if ( empty( $base ) ) {
		$base = $default;
	}

	return $base;
}

function hocwp_taxonomy_salary_base() {
	$lang    = hocwp_get_language();
	$default = 'salary';
	if ( 'vi' == $lang ) {
		$default = 'muc-luong';
	}
	$option = get_option( 'hocwp_permalink' );
	$base   = hocwp_get_value_by_key( $option, 'salary_base', $default );
	$base   = apply_filters( 'hocwp_taxonomy_salary_base', $base );
	if ( empty( $base ) ) {
		$base = $default;
	}

	return $base;
}

function hocwp_administrative_boundaries_post_types() {
	$types = array( 'post' );
	$types = apply_filters( 'hocwp_administrative_boundaries_post_types', $types );

	return $types;
}

function hocwp_register_taxonomy_administrative_boundaries( $post_type = null ) {
	if ( ! is_array( $post_type ) ) {
		$post_type = hocwp_administrative_boundaries_post_types();
	}
	$lang     = hocwp_get_language();
	$name     = __( 'Provinces', 'hocwp-theme' );
	$singular = __( 'Province', 'hocwp-theme' );
	if ( 'vi' == $lang ) {
		$name     = 'Tỉnh / Thành phố';
		$singular = $name;
	}
	$args = array(
		'name'          => $name,
		'singular_name' => $singular,
		'slug'          => hocwp_taxonomy_province_base(),
		'taxonomy'      => 'province',
		'post_types'    => $post_type
	);
	hocwp_register_taxonomy( $args );
	$name     = __( 'Districts', 'hocwp-theme' );
	$singular = __( 'District', 'hocwp-theme' );
	if ( 'vi' == $lang ) {
		$name     = 'Quận / Huyện';
		$singular = $name;
	}
	$args = array(
		'name'              => $name,
		'singular_name'     => $singular,
		'slug'              => hocwp_taxonomy_district_base(),
		'taxonomy'          => 'district',
		'show_admin_column' => false,
		'post_types'        => $post_type
	);
	hocwp_register_taxonomy( $args );
	$name     = __( 'Wards', 'hocwp-theme' );
	$singular = __( 'Ward', 'hocwp-theme' );
	if ( 'vi' == $lang ) {
		$name     = 'Phường / Xã';
		$singular = $name;
	}
	$args = array(
		'name'              => $name,
		'singular_name'     => $singular,
		'slug'              => hocwp_taxonomy_ward_base(),
		'taxonomy'          => 'ward',
		'show_admin_column' => false,
		'post_types'        => $post_type
	);
	hocwp_register_taxonomy( $args );
	$hamlet = apply_filters( 'hocwp_administrative_boundaries_hamlet', false );
	if ( $hamlet ) {
		$name     = __( 'Hamlets', 'hocwp-theme' );
		$singular = __( 'Hamlet', 'hocwp-theme' );
		if ( 'vi' == $lang ) {
			$name     = 'Thôn / Xóm';
			$singular = $name;
		}
		$args = array(
			'name'              => $name,
			'singular_name'     => $singular,
			'slug'              => hocwp_taxonomy_hamlet_base(),
			'taxonomy'          => 'hamlet',
			'show_admin_column' => false,
			'post_types'        => $post_type
		);
		hocwp_register_taxonomy( $args );
	}
	$name     = __( 'Streets', 'hocwp-theme' );
	$singular = __( 'Street', 'hocwp-theme' );
	if ( 'vi' == $lang ) {
		$name     = 'Đường / Phố';
		$singular = $name;
	}
	$args = array(
		'name'              => $name,
		'singular_name'     => $singular,
		'slug'              => hocwp_taxonomy_street_base(),
		'taxonomy'          => 'street',
		'show_admin_column' => false,
		'post_types'        => $post_type
	);
	hocwp_register_taxonomy( $args );
}

function hocwp_classifieds_get_saved_posts_page() {
	$result = hocwp_get_option_page( 'saved_posts_page', 'tin-da-luu', 'hocwp-theme-setting', 'page-templates/saved-posts.php' );
	if ( ! is_a( $result, 'WP_Post' ) ) {
		$result = hocwp_get_page_by_template( 'page-templates/favorite-posts.php' );
	}

	return apply_filters( 'hocwp_classifieds_get_saved_posts_page', $result );
}

function hocwp_classifieds_get_add_post_page() {
	$result = hocwp_get_option_page( 'add_post_page', 'dang-tin', 'hocwp-theme-setting', 'page-templates/add-post.php' );

	return apply_filters( 'hocwp_classifieds_get_add_post_page', $result );
}

function hocwp_classifieds_get_manage_profile_page() {
	$result = hocwp_get_option_page( 'manage_profile_page', 'thong-tin-ca-nhan', 'hocwp-theme-setting', 'page-templates/manage-profile.php' );

	return apply_filters( 'hocwp_classifieds_get_manage_profile_page', $result );
}

function hocwp_classifieds_get_price( $post_id = null ) {
	$price = hocwp_get_post_meta( 'price', $post_id );
	if ( empty( $price ) ) {
		$price = hocwp_post_get_first_term( $post_id, 'price' );
		if ( is_a( $price, 'WP_Term' ) ) {
			$price = $price->name;
		} else {
			$price = 'Thỏa thuận';
		}
	}

	return $price;
}

function hocwp_classifieds_get_administrative_boundary( $post_id = null, $only_province = false ) {
	if ( ! hocwp_id_number_valid( $post_id ) ) {
		$post_id = get_the_ID();
	}
	$terms  = wp_get_post_terms( $post_id, 'category' );
	$result = '';
	if ( hocwp_array_has_value( $terms ) ) {
		$childs = array();
		foreach ( $terms as $term ) {
			if ( $term->parent > 0 ) {
				$childs[] = $term;
				break;
			}
		}
		if ( hocwp_array_has_value( $childs ) ) {
			$child  = array_shift( $childs );
			$parent = get_category( $child->parent );
			while ( $parent->parent > 0 ) {
				$child  = $parent;
				$parent = get_category( $child->parent );
			}
			if ( $only_province ) {
				$result = $parent->name;
			} else {
				$result = $child->name . ', ' . $parent->name;
			}
		} else {
			$term   = array_shift( $terms );
			$result = $term->name;
		}
	}

	return $result;
}

$use = apply_filters( 'hocwp_classifieds_site', false );

if ( ! $use ) {
	return;
}

function hocwp_classifieds_post_type_and_taxonomy() {
	$lang     = hocwp_get_language();
	$name     = __( 'Types', 'hocwp-theme' );
	$singular = __( 'Type', 'hocwp-theme' );
	if ( 'vi' == $lang ) {
		$name     = 'Thể loại';
		$singular = $name;
	}
	$slug = apply_filters( 'hocwp_taxonomy_classifieds_type_slug', hocwp_taxonomy_classifieds_type_base() );
	$args = array(
		'name'          => $name,
		'singular_name' => $singular,
		'slug'          => $slug,
		'taxonomy'      => 'classifieds_type',
		'post_types'    => array( 'post' )
	);
	hocwp_register_taxonomy( $args );

	$custom_taxonomy = apply_filters( 'hocwp_classifieds_custom_taxonomy', false );
	if ( $custom_taxonomy ) {
		hocwp_register_taxonomy_administrative_boundaries();
	} else {

	}
	$name     = __( 'Prices', 'hocwp-theme' );
	$singular = __( 'Price', 'hocwp-theme' );
	if ( 'vi' == $lang ) {
		$name     = 'Mức giá';
		$singular = $name;
	}
	$args = array(
		'name'              => $name,
		'singular_name'     => $singular,
		'slug'              => hocwp_taxonomy_price_base(),
		'taxonomy'          => 'price',
		'show_admin_column' => false,
		'post_types'        => array( 'post' )
	);
	hocwp_register_taxonomy( $args );
	$name     = __( 'Acreages', 'hocwp-theme' );
	$singular = __( 'Acreage', 'hocwp-theme' );
	if ( 'vi' == $lang ) {
		$name     = 'Diện tích';
		$singular = $name;
	}
	$args = array(
		'name'              => $name,
		'singular_name'     => $singular,
		'slug'              => hocwp_taxonomy_acreage_base(),
		'taxonomy'          => 'acreage',
		'show_admin_column' => false,
		'post_types'        => array( 'post' )
	);
	hocwp_register_taxonomy( $args );
	$use = apply_filters( 'hocwp_use_taxonomy_classifieds_object', false );
	if ( $use ) {
		$name     = __( 'Objects', 'hocwp-theme' );
		$singular = __( 'Object', 'hocwp-theme' );
		if ( 'vi' == $lang ) {
			$name     = 'Đối tượng';
			$singular = $name;
		}
		$args = array(
			'name'              => $name,
			'singular_name'     => $singular,
			'slug'              => hocwp_taxonomy_classifieds_object_base(),
			'taxonomy'          => 'classifieds_object',
			'show_admin_column' => false,
			'post_types'        => array( 'post' )
		);
		hocwp_register_taxonomy( $args );
	}

	$use = apply_filters( 'hocwp_use_taxonomy_salary', false );
	if ( $use ) {
		$name     = __( 'Salaries', 'hocwp-theme' );
		$singular = __( 'Salary', 'hocwp-theme' );
		if ( 'vi' == $lang ) {
			$name     = 'Mức lương';
			$singular = $name;
		}
		$args = array(
			'name'              => $name,
			'singular_name'     => $singular,
			'slug'              => hocwp_taxonomy_salary_base(),
			'taxonomy'          => 'salary',
			'show_admin_column' => false,
			'post_types'        => array( 'post' )
		);
		hocwp_register_taxonomy( $args );
	}

	$name     = __( 'Units', 'hocwp-theme' );
	$singular = __( 'Unit', 'hocwp-theme' );
	if ( 'vi' == $lang ) {
		$name     = 'Đơn vị';
		$singular = $name;
	}
	$args = array(
		'name'              => $name,
		'singular_name'     => $singular,
		'slug'              => 'currency_unit',
		'taxonomy'          => 'currency_unit',
		'show_admin_column' => false,
		'post_types'        => array( 'post' )
	);
	hocwp_register_taxonomy_private( $args );

	hocwp_register_post_type_news();
}

add_action( 'init', 'hocwp_classifieds_post_type_and_taxonomy', 10 );

if ( 'post.php' == $GLOBALS['pagenow'] || 'post-new.php' == $GLOBALS['pagenow'] ) {
	$current_user = wp_get_current_user();
	$meta         = new HOCWP_Meta( 'post' );
	$meta->add_post_type( 'post' );
	$meta->set_title( __( 'General Information', 'hocwp-theme' ) );
	$meta->set_id( 'classifieds_general_information' );
	$meta->add_field(
		array(
			'id'      => 'address',
			'label'   => __( 'Address:', 'hocwp-theme' ),
			'class'   => 'hocwp-geo-address',
			'default' => get_user_meta( $current_user->ID, 'address', true )
		)
	);
	$meta->add_field(
		array(
			'id'    => 'price',
			'label' => __( 'Price:', 'hocwp-theme' )
		)
	);
	$meta->add_field(
		array(
			'id'      => 'phone',
			'label'   => __( 'Phone:', 'hocwp-theme' ),
			'default' => get_user_meta( $current_user->ID, 'phone', true )
		)
	);
	$meta->add_field(
		array(
			'id'      => 'email',
			'label'   => __( 'Email:', 'hocwp-theme' ),
			'default' => $current_user->user_email
		)
	);
	$meta->add_field(
		array(
			'id'    => 'acreage',
			'label' => __( 'Acreage:', 'hocwp-theme' )
		)
	);
	$meta->init();
	hocwp_meta_box_editor_gallery( array( 'post_type' => 'post' ) );
	hocwp_meta_box_google_maps();
}

function hocwp_classifieds_filter_taxonomy_base( $base, $taxonomy ) {
	switch ( $taxonomy ) {
		case 'classifieds_type':
			$base = hocwp_taxonomy_classifieds_type_base();
			break;
	}

	return $base;
}

add_filter( 'hocwp_remove_term_base_taxonomy_base', 'hocwp_classifieds_filter_taxonomy_base', 99, 2 );

function hocwp_classifieds_scripts() {
	if ( is_single() ) {
		hocwp_register_lib_google_maps();
	} elseif ( is_page() ) {
		$post_id       = get_the_ID();
		$add_post_page = hocwp_classifieds_get_add_post_page();
		if ( is_a( $add_post_page, 'WP_Post' ) ) {
			if ( $post_id == $add_post_page->ID ) {
				hocwp_register_lib_google_maps();
			}
		}
	}
}

add_action( 'wp_enqueue_scripts', 'hocwp_classifieds_scripts' );

function hocwp_classifieds_admin_scripts() {
	global $pagenow;
	if ( 'post-new.php' == $pagenow || 'post.php' == $pagenow ) {
		hocwp_register_lib_google_maps();
	}
}

add_action( 'admin_enqueue_scripts', 'hocwp_classifieds_admin_scripts' );

function hocwp_classifieds_admin_body_class( $classes ) {
	global $pagenow;
	hocwp_add_string_with_space_before( $classes, 'classifieds' );
	if ( 'post-new.php' == $pagenow || 'post.php' == $pagenow ) {
		hocwp_add_string_with_space_before( $classes, 'hocwp-google-maps' );
	}

	return $classes;
}

add_filter( 'admin_body_class', 'hocwp_classifieds_admin_body_class' );

function hocwp_classifieds_admin_class( $classes ) {
	$classes[] = 'classifieds';
	if ( is_single() ) {
		$classes[] = 'hocwp-google-maps';
	} elseif ( is_page() ) {
		$post_id       = get_the_ID();
		$add_post_page = hocwp_classifieds_get_add_post_page();
		if ( is_a( $add_post_page, 'WP_Post' ) ) {
			if ( $post_id == $add_post_page->ID ) {
				$classes[] = 'hocwp-google-maps';
			}
		}
	}

	return $classes;
}

add_filter( 'body_class', 'hocwp_classifieds_admin_class' );

function hocwp_classifieds_pre_post_thumbnail( $url, $post_id ) {
	if ( empty( $url ) ) {
		$gallery = hocwp_get_post_meta( 'gallery', $post_id );
		$url     = hocwp_get_first_image_source( $gallery );
	}

	return $url;
}

add_filter( 'hocwp_post_pre_post_thumbnail', 'hocwp_classifieds_pre_post_thumbnail', 10, 2 );

function hocwp_classifieds_widget_post_after_post( $args, $instance, $widget ) {
	global $post;
	if ( is_a( $post, 'WP_Post' ) ) {
		if ( 'post' == $post->post_type ) {
			$modified = get_post_modified_time( 'U', false, $post );
			$salary   = hocwp_post_get_first_term( $post->ID, 'salary' );
			?>
			<div class="metas">
				<div class="pull-left">
					<?php
					if ( is_a( $salary, 'WP_Term' ) ) {
						?>
						<div class="meta price">
							<span><strong><?php echo $salary->name; ?></strong></span>
						</div>
						<?php
					} else {
						?>
						<div class="meta price">
							<span><strong><?php echo hocwp_classifieds_get_price(); ?></strong></span>
						</div>
						<?php
					}
					?>
				</div>
				<div class="pull-right">
					<div class="meta modified">
						<?php echo hocwp_human_time_diff_to_now( $modified ) . ' trước'; ?>
					</div>
				</div>
			</div>
			<?php
		}
	}
}

add_action( 'hocwp_widget_post_after_post', 'hocwp_classifieds_widget_post_after_post', 10, 3 );

function hocwp_classifieds_pre_get_posts( WP_Query $query ) {
	if ( $query->is_main_query() ) {
		if ( is_search() ) {
			$type      = hocwp_get_value_by_key( $_REQUEST, 'type' );
			$province  = hocwp_get_value_by_key( $_REQUEST, 'province' );
			$district  = hocwp_get_value_by_key( $_REQUEST, 'district' );
			$ward      = hocwp_get_value_by_key( $_REQUEST, 'ward' );
			$street    = hocwp_get_value_by_key( $_REQUEST, 'street' );
			$price     = hocwp_get_value_by_key( $_REQUEST, 'price' );
			$acreage   = hocwp_get_value_by_key( $_REQUEST, 'acreage' );
			$object    = hocwp_get_value_by_key( $_REQUEST, 'object' );
			$salary    = hocwp_get_value_by_key( $_REQUEST, 'salary' );
			$tax_query = array(
				'relation' => 'AND'
			);
			if ( hocwp_id_number_valid( $type ) ) {
				$tax_item = array(
					'taxonomy' => 'classifieds_type',
					'field'    => 'id',
					'terms'    => $type
				);
				hocwp_query_sanitize_tax_query( $tax_item, $tax_query );
			}
			if ( hocwp_id_number_valid( $province ) ) {
				$tax_item = array(
					'taxonomy' => 'category',
					'field'    => 'id',
					'terms'    => $province
				);
				hocwp_query_sanitize_tax_query( $tax_item, $tax_query );
			}
			if ( hocwp_id_number_valid( $district ) ) {
				$tax_item = array(
					'taxonomy' => 'category',
					'field'    => 'id',
					'terms'    => $district
				);
				hocwp_query_sanitize_tax_query( $tax_item, $tax_query );
			}
			if ( hocwp_id_number_valid( $ward ) ) {
				$tax_item = array(
					'taxonomy' => 'category',
					'field'    => 'id',
					'terms'    => $ward
				);
				hocwp_query_sanitize_tax_query( $tax_item, $tax_query );
			}
			if ( hocwp_id_number_valid( $street ) ) {
				$tax_item = array(
					'taxonomy' => 'category',
					'field'    => 'id',
					'terms'    => $street
				);
				hocwp_query_sanitize_tax_query( $tax_item, $tax_query );
			}
			unset( $query->query['price'] );
			unset( $query->query_vars['price'] );
			if ( hocwp_id_number_valid( $price ) ) {
				$tax_item = array(
					'taxonomy' => 'price',
					'field'    => 'id',
					'terms'    => $price
				);
				hocwp_query_sanitize_tax_query( $tax_item, $tax_query );
			}
			unset( $query->query['acreage'] );
			unset( $query->query_vars['acreage'] );
			if ( hocwp_id_number_valid( $acreage ) ) {
				$tax_item = array(
					'taxonomy' => 'acreage',
					'field'    => 'id',
					'terms'    => $acreage
				);
				hocwp_query_sanitize_tax_query( $tax_item, $tax_query );
			}
			if ( hocwp_id_number_valid( $object ) ) {
				$tax_item = array(
					'taxonomy' => 'classifieds_object',
					'field'    => 'id',
					'terms'    => $object
				);
				hocwp_query_sanitize_tax_query( $tax_item, $tax_query );
			}
			unset( $query->query['salary'] );
			unset( $query->query_vars['salary'] );
			if ( hocwp_id_number_valid( $salary ) ) {
				$tax_item = array(
					'taxonomy' => 'salary',
					'field'    => 'id',
					'terms'    => $salary
				);
				hocwp_query_sanitize_tax_query( $tax_item, $tax_query );
			}
			$tax_query             = hocwp_get_value_by_key( $tax_query, 'tax_query', $tax_query );
			$tax_query['relation'] = 'AND';
			$query->set( 'tax_query', $tax_query );
			$query->set( 'post_type', 'post' );
		}
	}

	return $query;
}

if ( ! is_admin() ) {
	add_action( 'pre_get_posts', 'hocwp_classifieds_pre_get_posts' );
}

function hocwp_classifieds_admin_pre_get_posts( $query ) {
	global $pagenow, $post_type;
	$user = wp_get_current_user();
	if ( 'edit.php' == $pagenow && ! hocwp_is_admin() ) {
		$query->set( 'author', $user->ID );
	}

	return $query;
}

if ( is_admin() && hocwp_prevent_author_see_another_post() ) {
	add_action( 'pre_get_posts', 'hocwp_classifieds_admin_pre_get_posts' );
}

function hocwp_classifieds_save_post( $post_id ) {
	if ( ! hocwp_can_save_post( $post_id ) ) {
		return;
	}
	global $post_type;
	if ( empty( $post_type ) ) {
		$post_type = hocwp_get_current_post_type();
	}
	if ( empty( $post_type ) || 'post' == $post_type ) {
		if ( hocwp_is_subscriber() ) {
			if ( get_post_status( $post_id ) == 'publish' ) {
				$post_data = array(
					'ID'          => $post_id,
					'post_status' => 'pending'
				);
				wp_update_post( $post_data );
			}
		}
		if ( is_admin() && ! HOCWP_DOING_AJAX ) {
			$taxonomies         = hocwp_post_get_taxonomies( get_post( $post_id ), 'objects' );
			$custom_taxonomies  = array();
			$salary             = hocwp_get_value_by_key( $taxonomies, 'salary' );
			$acreage            = hocwp_get_value_by_key( $taxonomies, 'acreage' );
			$price              = hocwp_get_value_by_key( $taxonomies, 'price' );
			$classifieds_object = hocwp_get_value_by_key( $taxonomies, 'classifieds_object' );
			if ( hocwp_object_valid( $salary ) ) {
				//$custom_taxonomies[$salary->name] = $salary;
			}
			if ( hocwp_object_valid( $price ) ) {
				//$custom_taxonomies[$price->name] = $price;
			}
			if ( hocwp_object_valid( $acreage ) ) {
				//$custom_taxonomies[$acreage->name] = $acreage;
			}
			if ( hocwp_object_valid( $classifieds_object ) ) {
				//$custom_taxonomies[$classifieds_object->name] = $classifieds_object;
			}
			unset( $taxonomies['salary'] );
			unset( $taxonomies['acreage'] );
			unset( $taxonomies['price'] );
			unset( $taxonomies['classifieds_object'] );
			$errors = array();
			if ( hocwp_array_has_value( $taxonomies ) ) {
				foreach ( $taxonomies as $taxonomy ) {
					if ( $taxonomy->hierarchical ) {
						$terms = wp_get_post_terms( $post_id, $taxonomy->name );
						if ( ! hocwp_array_has_value( $terms ) ) {
							//$errors[] = sprintf(__('Please set %s for this post.', 'hocwp-theme'), '<strong>' . $taxonomy->labels->singular_name . '</strong>');
						}
					}
				}
			}
			$acreages = ( hocwp_object_valid( $acreage ) ) ? wp_get_post_terms( $post_id, $acreage->name ) : '';
			$prices   = ( hocwp_object_valid( $price ) ) ? wp_get_post_terms( $post_id, $price->name ) : '';
			$salaries = ( hocwp_object_valid( $salary ) ) ? wp_get_post_terms( $post_id, $salary->name ) : '';
			$objects  = ( hocwp_object_valid( $classifieds_object ) ) ? wp_get_post_terms( $post_id, $classifieds_object->name ) : '';
			$terms    = array();
			foreach ( $custom_taxonomies as $taxonomy ) {
				if ( $taxonomy->hierarchical ) {
					$post_terms = wp_get_post_terms( $post_id, $taxonomy->name );
					if ( hocwp_array_has_value( $post_terms ) ) {
						$terms = array_merge( $terms, $post_terms );
					}
				}
			}
			if ( ! hocwp_array_has_value( $errors ) && ! hocwp_array_has_value( $terms ) ) {
				//$errors[] = __('Please set term for this post in right way.', 'hocwp-theme');
			}
			if ( hocwp_array_has_value( $errors ) ) {
				if ( get_post_status( $post_id ) == 'publish' ) {
					$post_data = array(
						'ID'          => $post_id,
						'post_status' => 'pending'
					);
					wp_update_post( $post_data );
				}
				set_transient( 'hocwp_save_classifieds_post_' . $post_id . '_error', $errors );
			}
		}
	}
}

add_action( 'save_post', 'hocwp_classifieds_save_post', 99 );

function hocwp_classifieds_admin_notice() {
	$post_id = hocwp_get_method_value( 'post', 'request' );
	if ( hocwp_id_number_valid( $post_id ) ) {
		$transient_name = hocwp_build_transient_name( 'hocwp_save_classifieds_post_%s_error', $post_id );
		$errors         = get_transient( $transient_name );
		if ( false !== $errors ) {
			foreach ( $errors as $error ) {
				hocwp_admin_notice( array( 'text' => $error, 'error' => true ) );
			}
			delete_transient( $transient_name );
		}
	}
}

add_action( 'admin_notices', 'hocwp_classifieds_admin_notice' );

function hocwp_classifieds_admin_menu() {
	$role = hocwp_get_user_role( wp_get_current_user() );
	if ( 'subscriber' == $role ) {
		$post_types = get_post_types();
		unset( $post_types['post'] );
		foreach ( $post_types as $post_type ) {
			remove_menu_page( 'edit.php?post_type=' . $post_type );
		}
		remove_menu_page( 'edit-comments.php' );
		remove_menu_page( 'tools.php' );
	}
}

add_action( 'admin_menu', 'hocwp_classifieds_admin_menu', 99 );

function hocwp_classifieds_admin_init() {
	global $pagenow;
	$role = get_role( 'subscriber' );
	if ( hocwp_object_valid( $role ) ) {
		$role->add_cap( 'publish_posts' );
		$role->add_cap( 'edit_posts' );
	}
	if ( 'post-new.php' == $pagenow ) {
		if ( hocwp_is_subscriber() ) {
			$post_type = hocwp_get_current_post_type();
			if ( ! empty( $post_type ) && 'post' !== $post_type ) {
				wp_redirect( admin_url() );
				exit;
			}
		}
	}
}

add_action( 'admin_init', 'hocwp_classifieds_admin_init', 0 );

add_filter( 'hocwp_use_addthis', '__return_true' );

function hocwp_classifieds_on_wp_run() {
	if ( ! is_user_logged_in() ) {
		if ( is_page_template( 'page-templates/favorite-posts.php' ) || is_page_template( 'page-templates/add-post.php' ) || is_page_template( 'page-templates/account.php' ) ) {
			wp_redirect( wp_login_url() );
			exit;
		}
	}
}

add_action( 'wp', 'hocwp_classifieds_on_wp_run' );