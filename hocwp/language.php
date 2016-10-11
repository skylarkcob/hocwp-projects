<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

function hocwp_qtranslate_x_installed() {
	return defined( 'QTX_VERSION' );
}

function hocwp_qtranslate_x_admin_sections( $sections ) {
	$sections['hocwp_string_translation'] = __( 'String Translation', 'hocwp-theme' );

	return $sections;
}

function hocwp_qtranslate_x_admin_section_field() {
	qtranxf_admin_section_start( 'hocwp_string_translation' );
	echo '<br>';
	$table = new HOCWP_Table_String_Translation( hocwp_get_all_mo_posts() );
	$table->prepare_items();
	$table->search_box( __( 'Search translations', 'hocwp-theme' ), 'translations' );
	$table->display();
	hocwp_field_input_hidden( array( 'id' => 'hocwp_action', 'value' => 'string_translation' ) );
	qtranxf_admin_section_end( 'hocwp_string_translation' );
}

if ( hocwp_qtranslate_x_installed() ) {
	add_filter( 'qtranslate_admin_sections', 'hocwp_qtranslate_x_admin_sections' );
	add_action( 'qtranslate_configuration', 'hocwp_qtranslate_x_admin_section_field' );
}

function hocwp_get_all_mo_posts( $args = array() ) {
	$defaults = array(
		'post_type'      => 'hocwp_mo',
		'posts_per_page' => - 1
	);
	$args     = wp_parse_args( $args, $defaults );
	$query    = hocwp_query( $args );

	return $query->posts;
}

function hocwp_get_qtranslate_x_config() {
	return $GLOBALS['q_config'];
}

function hocwp_get_qtranslate_x_enabled_languages() {
	return qtranxf_getSortedLanguages();
}

function hocwp_get_registered_string_language() {
	$strings = get_option( 'hocwp_string_translations' );
	if ( ! is_array( $strings ) ) {
		$strings = array();
	}
	$strings = apply_filters( 'hocwp_registered_string_language', $strings );

	return $strings;
}

function hocwp_get_active_registered_string_language() {
	global $hocwp_active_registered_string_translations;
	if ( ! is_array( ( $hocwp_active_registered_string_translations ) ) ) {
		$hocwp_active_registered_string_translations = array();
	}

	return apply_filters( 'hocwp_active_registered_string_language', $hocwp_active_registered_string_translations );
}

function hocwp_register_string_language( $args = array() ) {
	if ( ! did_action( 'init' ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Please call this function in <strong>hocwp_register_string_translation</strong> hook.', 'hocwp-theme' ), HOCWP_VERSION );

		return;
	}
	if ( ! is_array( $args ) ) {
		$args = array(
			'string' => $args
		);
	}
	$name                                = hocwp_get_value_by_key( $args, 'name' );
	$string                              = hocwp_get_value_by_key( $args, 'string' );
	$context                             = hocwp_get_value_by_key( $args, 'context', 'HocWP' );
	$multiline                           = hocwp_get_value_by_key( $args, 'multiline' );
	$key                                 = md5( $string );
	$active_strings                      = hocwp_get_active_registered_string_language();
	$active_strings[ $key ]['name']      = $name;
	$active_strings[ $key ]['string']    = $string;
	$active_strings[ $key ]['context']   = $context;
	$active_strings[ $key ]['multiline'] = $multiline;
	global $hocwp_active_registered_string_translations;
	$hocwp_active_registered_string_translations = $active_strings;
	$transient_name                              = hocwp_build_transient_name( 'hocwp_string_translation_registered_%s', $args );
	if ( false === get_transient( $transient_name ) ) {
		$strings                      = hocwp_get_registered_string_language();
		$strings[ $key ]['name']      = $name;
		$strings[ $key ]['string']    = $string;
		$strings[ $key ]['context']   = $context;
		$strings[ $key ]['multiline'] = $multiline;
		update_option( 'hocwp_string_translations', $strings );
		$mo          = new HOCWP_MO();
		$translation = '';
		$object      = $mo->get_object( $string );
		if ( is_a( $object, 'WP_Post' ) ) {
			$translation = $object->post_content;
		}
		$post_id = $mo->export_to_db( $string, $translation );
		if ( hocwp_id_number_valid( $post_id ) ) {
			set_transient( $transient_name, $post_id, WEEK_IN_SECONDS );
		}
	}
}

function hocwp_translate_x_string_transaltion_update() {
	if ( isset( $_REQUEST['hocwp_action'] ) && 'string_translation' == $_REQUEST['hocwp_action'] ) {
		unset( $_REQUEST['hocwp_action'] );
		$search  = hocwp_get_method_value( 's', 'request' );
		$strings = hocwp_get_method_value( 'strings' );
		if ( hocwp_array_has_value( $strings ) ) {
			$mo            = new HOCWP_MO();
			$saved_strings = hocwp_get_registered_string_language();
			foreach ( $strings as $encrypted_string ) {
				unset( $saved_strings[ $encrypted_string ] );
				$mo->delete_from_db( $encrypted_string, true );
			}
			update_option( 'hocwp_string_translations', $saved_strings );
			hocwp_delete_transient( 'hocwp_string_translation_registered' );
		}
		$args = array_intersect_key( $_REQUEST, array_flip( array( 's', 'paged', 'group' ) ) );
		if ( ! empty( $search ) ) {
			$args['s'] = $search;
		}
		if ( ! empty( $args['s'] ) ) {
			$args['s'] = urlencode( $args['s'] );
		}
		$translations = hocwp_get_method_value( 'translation' );
		if ( hocwp_array_has_value( $translations ) ) {
			foreach ( $translations as $key => $value ) {
				if ( ! empty( $value ) ) {
					$mo = hocwp_get_post_by_column( 'post_title', 'hocwp_mo_' . $key, OBJECT, array( 'post_type' => 'hocwp_mo' ) );
					if ( is_a( $mo, 'WP_Post' ) ) {
						$obj = new HOCWP_MO( $mo->ID );
						$obj->export_to_db( $mo->post_excerpt, $value );
					}
				}
			}
		}
		$url = add_query_arg( $args, wp_get_referer() );
		wp_safe_redirect( $url );
		exit;
	}
}

add_action( 'admin_init', 'hocwp_translate_x_string_transaltion_update' );

function hocwp_language_register_hook() {
	do_action( 'hocwp_register_string_translation' );
}

add_action( 'wp_loaded', 'hocwp_language_register_hook' );

function hocwp_language_admin_enqueue_scripts( $hook ) {
	if ( 'settings_page_qtranslate-x' == $hook ) {
		add_filter( 'hocwp_use_admin_style_and_script', '__return_true' );
	}
}

add_action( 'admin_enqueue_scripts', 'hocwp_language_admin_enqueue_scripts' );