<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

function hocwp_debug_log_ajax_callback() {
	$object = hocwp_get_method_value( 'object' );
	$object = hocwp_json_string_to_array( $object );
	hocwp_debug_log( $object );
	exit;
}

add_action( 'wp_ajax_hocwp_debug_log', 'hocwp_debug_log_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_debug_log', 'hocwp_debug_log_ajax_callback' );

function hocwp_comment_likes_ajax_callback() {
	$result     = array();
	$likes      = isset( $_POST['likes'] ) ? absint( $_POST['likes'] ) : 0;
	$comment_id = isset( $_POST['comment_id'] ) ? absint( $_POST['comment_id'] ) : 0;
	$likes ++;
	update_comment_meta( $comment_id, 'likes', $likes );
	$result['likes']                                 = hocwp_number_format( $likes );
	$_SESSION[ 'comment_' . $comment_id . '_likes' ] = 1;
	echo json_encode( $result );
	die();
}

add_action( 'wp_ajax_hocwp_comment_likes', 'hocwp_comment_likes_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_comment_likes', 'hocwp_comment_likes_ajax_callback' );

function hocwp_comment_report_ajax_callback() {
	$result = array();
	echo json_encode( $result );
	die();
}

add_action( 'wp_ajax_hocwp_comment_report', 'hocwp_comment_report_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_comment_report', 'hocwp_comment_report_ajax_callback' );

function hocwp_fetch_plugin_license_ajax_callback() {
	$result  = array(
		'customer_email' => '',
		'license_code'   => ''
	);
	$use_for = isset( $_POST['use_for'] ) ? $_POST['use_for'] : '';
	if ( ! empty( $use_for ) ) {
		$use_for_key    = md5( $use_for );
		$option         = get_option( 'hocwp_plugin_licenses' );
		$customer_email = hocwp_get_value_by_key( $option, array( $use_for_key, 'customer_email' ) );
		if ( is_array( $customer_email ) || ! is_email( $customer_email ) ) {
			$customer_email = '';
		}
		$license_code = hocwp_get_value_by_key( $option, array( $use_for_key, 'license_code' ) );
		if ( is_array( $license_code ) || strlen( $license_code ) < 5 ) {
			$license_code = '';
		}
		$result['customer_email'] = $customer_email;
		$result['license_code']   = $license_code;
		update_option( 'test', $result );
	}
	echo json_encode( $result );
	die();
}

add_action( 'wp_ajax_hocwp_fetch_plugin_license', 'hocwp_fetch_plugin_license_ajax_callback' );

function hocwp_change_captcha_image_ajax_callback() {
	$result  = array(
		'success' => false
	);
	$captcha = new HOCWP_Captcha();
	$url     = $captcha->generate_image();
	if ( ! empty( $url ) ) {
		$result['success']           = true;
		$result['captcha_image_url'] = $url;
	} else {
		$result['message'] = __( 'Sorry, cannot generate captcha image, please try again or contact administrator!', 'hocwp-theme' );
	}
	echo json_encode( $result );
	die();
}

add_action( 'wp_ajax_hocwp_change_captcha_image', 'hocwp_change_captcha_image_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_change_captcha_image', 'hocwp_change_captcha_image_ajax_callback' );

function hocwp_vote_post_ajax_callback() {
	$result  = array(
		'success' => false
	);
	$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : '';
	$post_id = absint( $post_id );
	if ( $post_id > 0 ) {
		$type         = isset( $_POST['type'] ) ? $_POST['type'] : hocwp_get_method_value( 'vote_type' );
		$session_name = 'hocwp_vote_' . $type . '_post_' . $post_id;
		if ( ! isset( $_SESSION[ $session_name ] ) || 1 != $_SESSION[ $session_name ] ) {
			$value = isset( $_POST['value'] ) ? $_POST['value'] : '';
			$value = absint( $value );
			$value ++;
			if ( 'up' == $type || 'like' == $type ) {
				update_post_meta( $post_id, 'likes', $value );
				do_action( 'hocwp_add_trending_post', $post_id, 'like' );
			} elseif ( 'down' == $type || 'dislike' == $type ) {
				update_post_meta( $post_id, 'dislikes', $value );
			}
			$result['value']           = $value;
			$result['type']            = $type;
			$result['post_id']         = $post_id;
			$result['value_html']      = number_format( $value );
			$_SESSION[ $session_name ] = 1;
			$result['success']         = true;
		}
	}
	echo json_encode( $result );
	die();
}

add_action( 'wp_ajax_hocwp_vote_post', 'hocwp_vote_post_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_vote_post', 'hocwp_vote_post_ajax_callback' );

function hocwp_favorite_post_ajax_callback() {
	$result  = array(
		'html_data' => '',
		'success'   => false,
		'remove'    => false
	);
	$post_id = hocwp_get_method_value( 'post_id' );
	if ( hocwp_id_number_valid( $post_id ) && is_user_logged_in() ) {
		$user = wp_get_current_user();
		$type = hocwp_get_method_value( 'type' );
		if ( empty( $type ) ) {
			$type = 'favorite';
		}
		$action = hocwp_get_method_value( 'data_action' );
		if ( empty( $action ) ) {
			$action = 'do';
		}
		if ( 'favorite' == $type ) {
			$favorites = get_user_meta( $user->ID, 'favorite_posts', true );
			if ( ! is_array( $favorites ) ) {
				$favorites = array();
			}
			if ( ! in_array( $post_id, $favorites ) ) {
				$favorites[] = $post_id;
			} else {
				unset( $favorites[ array_search( $post_id, $favorites ) ] );
				$result['remove'] = true;
			}
			$updated = update_user_meta( $user->ID, 'favorite_posts', $favorites );
			if ( $updated ) {
				$result['success'] = true;
				$saved             = false;
				if ( ! $result['remove'] ) {
					$saved = true;
				}
				$params              = array(
					'post_id' => $post_id,
					'saved'   => $saved,
					'echo'    => false
				);
				$text                = hocwp_favorite_post_button_text( $params );
				$result['html_data'] = $text;
			}
		} elseif ( 'save' == $type ) {
			$result['success'] = hocwp_update_user_saved_posts( $user->ID, $post_id );
			$saved             = false;
			if ( 'do' == $action ) {
				$saved = true;
			}
			$params              = array(
				'post_id' => $post_id,
				'saved'   => $saved,
				'echo'    => false
			);
			$text                = hocwp_save_post_button_text( $params );
			$result['html_data'] = $text;
		}
		if ( 'undo' == $action ) {
			$result['remove'] = true;
		}
	}
	wp_send_json( $result );
}

add_action( 'wp_ajax_hocwp_favorite_post', 'hocwp_favorite_post_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_favorite_post', 'hocwp_favorite_post_ajax_callback' );
add_action( 'wp_ajax_hocwp_save_post', 'hocwp_favorite_post_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_save_post', 'hocwp_favorite_post_ajax_callback' );

function hocwp_sanitize_media_value_ajax_callback() {
	$id     = isset( $_POST['id'] ) ? $_POST['id'] : 0;
	$url    = isset( $_POST['url'] ) ? $_POST['url'] : '';
	$result = array( 'id' => $id, 'url' => $url );
	$result = hocwp_sanitize_media_value( $result );
	echo json_encode( $result );
	exit;
}

add_action( 'wp_ajax_hocwp_sanitize_media_value', 'hocwp_sanitize_media_value_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_sanitize_media_value', 'hocwp_sanitize_media_value_ajax_callback' );

function hocwp_fetch_administrative_boundaries_ajax_callback() {
	$result  = array();
	$default = hocwp_get_method_value( 'default' );
	$default = str_replace( '\\', '', $default );
	//$type = hocwp_get_method_value('type');
	if ( empty( $default ) ) {

	}
	$html_data = $default;
	$parent    = hocwp_get_method_value( 'parent' );
	if ( hocwp_id_number_valid( $parent ) ) {
		$taxonomy = hocwp_get_method_value( 'taxonomy' );
		if ( ! empty( $taxonomy ) ) {
			$terms = hocwp_get_terms( $taxonomy, array( 'parent' => $parent, 'orderby' => 'NAME' ) );
			if ( hocwp_array_has_value( $terms ) ) {
				foreach ( $terms as $term ) {
					$option = hocwp_field_get_option( array( 'value' => $term->term_id, 'text' => $term->name ) );
					$html_data .= $option;
				}
			}
		}
	}
	$result['html_data'] = $html_data;
	wp_send_json( $result );
}

add_action( 'wp_ajax_hocwp_fetch_administrative_boundaries', 'hocwp_fetch_administrative_boundaries_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_fetch_administrative_boundaries', 'hocwp_fetch_administrative_boundaries_ajax_callback' );

function hocwp_get_term_ajax_callback() {
	$term_id = hocwp_get_method_value( 'term_id' );
	$result  = array(
		'term' => new WP_Error()
	);
	if ( hocwp_id_number_valid( $term_id ) ) {
		$taxonomy = hocwp_get_method_value( 'taxonomy' );
		if ( ! empty( $taxonomy ) ) {
			$result['term'] = get_term( $term_id, $taxonomy );
		}
	}
	wp_send_json( $result );
}

add_action( 'wp_ajax_hocwp_get_term', 'hocwp_get_term_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_get_term', 'hocwp_get_term_ajax_callback' );

function hocwp_get_term_administrative_boundaries_address_ajax_callback() {
	$result  = array(
		'address' => ''
	);
	$term_id = hocwp_get_method_value( 'term_id' );
	if ( hocwp_id_number_valid( $term_id ) ) {
		$taxonomy = hocwp_get_method_value( 'taxonomy' );
		if ( ! empty( $taxonomy ) ) {
			$term    = get_term( $term_id, $taxonomy );
			$address = $term->name;
			while ( $term->parent > 0 ) {
				$address .= ', ';
				$term = get_term( $term->parent, $taxonomy );
				$address .= $term->name;
			}
			$address           = rtrim( $address, ', ' );
			$result['address'] = $address;
		}
	}
	wp_send_json( $result );
}

add_action( 'wp_ajax_hocwp_get_term_administrative_boundaries_address', 'hocwp_get_term_administrative_boundaries_address_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_get_term_administrative_boundaries_address', 'hocwp_get_term_administrative_boundaries_address_ajax_callback' );

function hocwp_dashboard_widget_ajax_callback() {
	$result = array(
		'html_data' => ''
	);
	$widget = hocwp_get_method_value( 'widget' );
	if ( ! empty( $widget ) ) {
		$widgets = explode( '_', $widget );
		array_shift( $widgets );
		$widget   = implode( '_', $widgets );
		$callback = 'hocwp_theme_dashboard_widget_' . $widget;
		if ( hocwp_callback_exists( $callback ) ) {
			ob_start();
			call_user_func( $callback );
			$result['html_data'] = ob_get_clean();
		}
	}
	wp_send_json( $result );
}

add_action( 'wp_ajax_hocwp_dashboard_widget', 'hocwp_dashboard_widget_ajax_callback' );

function hocwp_social_login_facebook_ajax_callback() {
	$result  = array(
		'redirect_to' => '',
		'logged_in'   => false
	);
	$data    = hocwp_get_method_value( 'data' );
	$data    = hocwp_json_string_to_array( $data );
	$connect = (bool) hocwp_get_method_value( 'connect' );
	if ( hocwp_array_has_value( $data ) ) {
		$verified           = (bool) hocwp_get_value_by_key( $data, 'verified' );
		$allow_not_verified = apply_filters( 'hocwp_allow_social_user_signup_not_verified', true );
		if ( $verified || $allow_not_verified ) {
			$id                    = hocwp_get_value_by_key( $data, 'id' );
			$requested_redirect_to = hocwp_get_method_value( 'redirect_to' );
			$redirect_to           = home_url( '/' );
			$transient_name        = hocwp_build_transient_name( 'hocwp_social_login_facebook_%s', $id );
			$user_id               = get_transient( $transient_name );
			$user                  = get_user_by( 'ID', $user_id );
			if ( $connect && is_user_logged_in() ) {
				$user    = wp_get_current_user();
				$user_id = $user->ID;
			}
			$find_users = get_users( array( 'meta_key' => 'facebook', 'meta_value' => $id ) );
			if ( hocwp_array_has_value( $find_users ) ) {
				$user    = $find_users[0];
				$user_id = $user->ID;
			}
			if ( false === $user_id || ! hocwp_id_number_valid( $user_id ) || ! is_a( $user, 'WP_User' ) || $connect ) {
				$avatar = hocwp_get_value_by_key( $data, array( 'picture', 'data', 'url' ) );
				if ( $connect ) {
					update_user_meta( $user_id, 'facebook', $id );
					update_user_meta( $user_id, 'facebook_data', $data );
					update_user_meta( $user_id, 'avatar', $avatar );
					$result['redirect_to'] = get_edit_profile_url( $user_id );
					$result['logged_in']   = true;
				} else {
					$email = hocwp_get_value_by_key( $data, 'email' );
					if ( is_email( $email ) ) {
						$name       = hocwp_get_value_by_key( $data, 'name' );
						$first_name = hocwp_get_value_by_key( $data, 'first_name' );
						$last_name  = hocwp_get_value_by_key( $data, 'last_name' );

						$password = wp_generate_password();
						$user_id  = null;
						if ( username_exists( $email ) ) {
							$user    = get_user_by( 'login', $email );
							$user_id = $user->ID;
						} elseif ( email_exists( $email ) ) {
							$user    = get_user_by( 'email', $email );
							$user_id = $user->ID;
						}
						$old_user = true;
						if ( ! hocwp_id_number_valid( $user_id ) ) {
							$user_data = array(
								'username' => $email,
								'email'    => $email,
								'password' => $password
							);
							$user_id   = hocwp_add_user( $user_data );
							if ( hocwp_id_number_valid( $user_id ) ) {
								$old_user = false;
							}
						}
						if ( hocwp_id_number_valid( $user_id ) ) {
							$user        = get_user_by( 'id', $user_id );
							$redirect_to = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );
							if ( ! $old_user ) {
								update_user_meta( $user_id, 'facebook', $id );
								$user_data = array(
									'ID'           => $user_id,
									'display_name' => $name,
									'first_name'   => $first_name,
									'last_name'    => $last_name
								);
								wp_update_user( $user_data );
								update_user_meta( $user_id, 'avatar', $avatar );
								update_user_meta( $user_id, 'facebook_data', $data );
							}
							hocwp_user_force_login( $user_id );
							$result['redirect_to'] = $redirect_to;
							$result['logged_in']   = true;
							set_transient( $transient_name, $user_id, DAY_IN_SECONDS );
						}
					}
				}
			} else {
				update_user_meta( $user_id, 'facebook_data', $data );
				$user        = get_user_by( 'id', $user_id );
				$redirect_to = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );
				hocwp_user_force_login( $user_id );
				$result['redirect_to'] = $redirect_to;
				$result['logged_in']   = true;
			}
		}
	}
	wp_send_json( $result );
}

add_action( 'wp_ajax_hocwp_social_login_facebook', 'hocwp_social_login_facebook_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_social_login_facebook', 'hocwp_social_login_facebook_ajax_callback' );

function hocwp_social_login_google_ajax_callback() {
	$result  = array(
		'redirect_to' => '',
		'logged_in'   => false
	);
	$data    = hocwp_get_method_value( 'data' );
	$data    = hocwp_json_string_to_array( $data );
	$connect = hocwp_get_method_value( 'connect' );
	if ( hocwp_array_has_value( $data ) ) {
		$verified           = (bool) hocwp_get_value_by_key( $data, 'verified' );
		$allow_not_verified = apply_filters( 'hocwp_allow_social_user_signup_not_verified', true );
		if ( $verified || $allow_not_verified ) {
			$id                    = hocwp_get_value_by_key( $data, 'id' );
			$requested_redirect_to = hocwp_get_method_value( 'redirect_to' );
			$redirect_to           = home_url( '/' );
			$transient_name        = hocwp_build_transient_name( 'hocwp_social_login_google_%s', $id );
			$user_id               = get_transient( $transient_name );
			$user                  = get_user_by( 'id', $user_id );
			if ( $connect && is_user_logged_in() ) {
				$user    = wp_get_current_user();
				$user_id = $user->ID;
			}
			$find_users = get_users( array( 'meta_key' => 'google', 'meta_value' => $id ) );
			if ( hocwp_array_has_value( $find_users ) ) {
				$user    = $find_users[0];
				$user_id = $user->ID;
			}
			if ( false === $user_id || ! hocwp_id_number_valid( $user_id ) || ! is_a( $user, 'WP_User' ) || $connect ) {
				$avatar = hocwp_get_value_by_key( $data, array( 'image', 'url' ) );
				if ( $connect ) {
					update_user_meta( $user_id, 'google', $id );
					update_user_meta( $user_id, 'avatar', $avatar );
					update_user_meta( $user_id, 'google_data', $data );
					$result['redirect_to'] = get_edit_profile_url( $user_id );
					$result['logged_in']   = true;
				} else {
					$email = hocwp_get_value_by_key( $data, array( 'emails', 0, 'value' ) );
					if ( is_email( $email ) ) {
						$name       = hocwp_get_value_by_key( $data, 'displayName' );
						$first_name = hocwp_get_value_by_key( $data, array( 'name', 'givenName' ) );
						$last_name  = hocwp_get_value_by_key( $data, array( 'name', 'familyName' ) );
						$password   = wp_generate_password();
						$user_id    = null;
						if ( username_exists( $email ) ) {
							$user    = get_user_by( 'login', $email );
							$user_id = $user->ID;
						} elseif ( email_exists( $email ) ) {
							$user    = get_user_by( 'email', $email );
							$user_id = $user->ID;
						}
						$old_user = true;
						if ( ! hocwp_id_number_valid( $user_id ) ) {
							$user_data = array(
								'username' => $email,
								'email'    => $email,
								'password' => $password
							);
							$user_id   = hocwp_add_user( $user_data );
							if ( hocwp_id_number_valid( $user_id ) ) {
								$old_user = false;
							}
						}
						if ( hocwp_id_number_valid( $user_id ) ) {
							$user        = get_user_by( 'id', $user_id );
							$redirect_to = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );
							if ( ! $old_user ) {
								update_user_meta( $user_id, 'google', $id );
								$user_data = array(
									'ID'           => $user_id,
									'display_name' => $name,
									'first_name'   => $first_name,
									'last_name'    => $last_name
								);
								wp_update_user( $user_data );
								update_user_meta( $user_id, 'avatar', $avatar );
								update_user_meta( $user_id, 'google_data', $data );
							}
							hocwp_user_force_login( $user_id );
							$result['redirect_to'] = $redirect_to;
							$result['logged_in']   = true;
							set_transient( $transient_name, $user_id, DAY_IN_SECONDS );
						}
					}
				}
			} else {
				update_user_meta( $user_id, 'google_data', $data );
				$user        = get_user_by( 'id', $user_id );
				$redirect_to = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );
				hocwp_user_force_login( $user_id );
				$result['redirect_to'] = $redirect_to;
				$result['logged_in']   = true;
			}
		}
	}
	wp_send_json( $result );
}

add_action( 'wp_ajax_hocwp_social_login_google', 'hocwp_social_login_google_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_social_login_google', 'hocwp_social_login_google_ajax_callback' );

function hocwp_disconnect_social_account_ajax_callback() {
	$social  = hocwp_get_method_value( 'social' );
	$user_id = hocwp_get_method_value( 'user_id' );
	if ( hocwp_id_number_valid( $user_id ) ) {
		switch ( $social ) {
			case 'facebook':
				delete_user_meta( $user_id, 'facebook' );
				delete_user_meta( $user_id, 'facebook_data' );
				break;
			case 'google':
				delete_user_meta( $user_id, 'google' );
				delete_user_meta( $user_id, 'google_data' );
				break;
		}
	}
	exit;
}

add_action( 'wp_ajax_hocwp_disconnect_social_account', 'hocwp_disconnect_social_account_ajax_callback' );

function hocwp_compress_style_and_script_ajax_callback() {
	$result         = array();
	$type           = hocwp_get_method_value( 'type' );
	$type           = hocwp_json_string_to_array( $type );
	$force_compress = hocwp_get_method_value( 'force_compress' );
	$force_compress = hocwp_string_to_bool( $force_compress );
	$compress_core  = hocwp_get_method_value( 'compress_core' );
	$compress_core  = hocwp_string_to_bool( $compress_core );
	$args           = array(
		'type'           => $type,
		'force_compress' => $force_compress,
		'compress_core'  => $compress_core
	);
	hocwp_compress_style_and_script( $args );
	wp_send_json( $result );
}

add_action( 'wp_ajax_hocwp_compress_style_and_script', 'hocwp_compress_style_and_script_ajax_callback' );

function hocwp_update_post_temperature_ajax_callback() {
	$result  = array();
	$post_id = hocwp_get_method_value( 'post_id' );
	if ( hocwp_id_number_valid( $post_id ) ) {
		$type = hocwp_get_method_value( 'type' );
		hocwp_update_post_temperature( $type, $post_id );
	}
	wp_send_json( $result );
}

add_action( 'wp_ajax_hocwp_update_post_temperature', 'hocwp_update_post_temperature_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_update_post_temperature', 'hocwp_update_post_temperature_ajax_callback' );

function hocwp_notification_posts_ajax_callback() {
	$lists = get_option( 'hocwp_notification_posts' );
	$lists = hocwp_to_array( $lists );
	if ( hocwp_array_has_value( $lists ) ) {
		$query = hocwp_get_post_by_meta( 'subscriber_verified', 1, array( 'post_type' => 'hocwp_subscriber' ) );
		if ( $query->have_posts() ) {
			$subscribers    = $query->posts;
			$date           = hocwp_get_current_date();
			$transient_name = hocwp_build_transient_name( 'hocwp_notification_posts_table_%s', $date );
			if ( false === ( $table_content = get_transient( $transient_name ) ) ) {
				$table = new HOCWP_HTML( 'table' );
				$table->set_attribute( 'align', 'center' );
				$table->set_attribute( 'width', '100%' );
				$table->set_attribute( 'cellspacing', 0 );
				$table->set_attribute( 'border', 0 );
				$table->set_attribute( 'cellpadding', 0 );
				$table->set_attribute( 'bgcolor', '#ffffff' );
				$table_content = '';
				foreach ( $lists as $post_id ) {
					$obj       = get_post( $post_id );
					$post_type = get_post_type_object( $obj->post_type );
					if ( ! is_object( $post_type ) || ! $post_type->public ) {
						continue;
					}
					$notified = (bool) hocwp_get_post_meta( 'hocwp_notification_posts_sent', $post_id );
					if ( $notified ) {
						continue;
					}
					$inner_table = $table;
					$tbody       = new HOCWP_HTML( 'tbody' );
					$tr          = new HOCWP_HTML( 'tr' );
					$td          = new HOCWP_HTML( 'td' );
					$td->set_attribute( 'style', 'font-size:1px;line-height:1px' );
					$tr->set_text( $td );
					$tr2 = new HOCWP_HTML( 'tr' );
					$td2 = new HOCWP_HTML( 'td' );
					$td2->set_attribute( 'style', 'border-bottom:2px dotted #808285;padding-bottom:12px' );
					$a = new HOCWP_HTML( 'a' );
					$a->set_attribute( 'target', '_blank' );
					$a->set_href( get_permalink( $obj ) );
					$a->set_attribute( 'style', 'font-family: Helvetica,arial,sans-serif; font-size: 20px; color: rgb(22, 63, 144); text-align: left; font-weight: 500; text-decoration: none; line-height: 27px;' );
					$a->set_text( get_the_title( $obj ) );
					$p       = new HOCWP_HTML( 'p' );
					$excerpt = get_the_excerpt( $obj );
					if ( empty( $excerpt ) ) {
						$excerpt = $obj->post_content;
						$excerpt = strip_tags( $excerpt );
						$excerpt = apply_filters( 'excerpt_length', $excerpt, 150 );
					}
					$p->set_text( $excerpt );
					$td2->set_text( $a->build() . $p->build() );
					$tr2->set_text( $td2 );
					$tbody->set_text( $tr->build() . $tr2->build() );
					$inner_table->set_text( $tbody );
					$table_content .= $inner_table->build();
					update_post_meta( $post_id, 'hocwp_notification_posts_sent', 1 );
				}
				if ( ! empty( $table_content ) ) {
					$tbody = new HOCWP_HTML( 'tbody' );
					$tr    = new HOCWP_HTML( 'tr' );
					$td    = new HOCWP_HTML( 'td' );
					$td->set_text( $table_content );
					$tr->set_text( $td );
					$tbody->set_text( $tr );
					$table->set_text( $tbody );
					$table_content = $table->build();
				}
			}
			if ( ! empty( $table_content ) ) {
				foreach ( $subscribers as $subscriber ) {
					$email = hocwp_get_post_meta( 'subscriber_email', $subscriber->ID );
					if ( is_email( $email ) ) {
						$transient_name = 'hocwp_notification_posts_to_user_' . md5( $email );
						$transient_name = hocwp_build_transient_name( $transient_name . '_%s', $table_content );
						if ( false === get_transient( $transient_name ) ) {
							$subject = '[' . get_bloginfo( 'name' ) . '] New content updated on ' . hocwp_get_current_date( hocwp_get_date_format() );
							$message = $table_content;
							$message = hocwp_mail_unsubscribe_link_footer( $message, $email );
							$sent    = hocwp_send_html_mail( $email, $subject, $message );
							if ( $sent ) {
								set_transient( $transient_name, 1, DAY_IN_SECONDS );
							}
						}
					}
				}
			}
		}
	}
	exit;
}

add_action( 'wp_ajax_hocwp_notification_posts', 'hocwp_notification_posts_ajax_callback' );
add_action( 'wp_ajax_nopriv_hocwp_notification_posts', 'hocwp_notification_posts_ajax_callback' );

function hocwp_generate_slider_sortable_item_ajax_callback() {
	$result      = array();
	$max_item_id = hocwp_get_method_value( 'max_item_id' );
	$max_item_id = absint( $max_item_id );
	$media_url   = hocwp_get_method_value( 'media_url' );
	$media_id    = hocwp_get_method_value( 'media_id' );
	$media_id    = absint( $media_id );
	$item_html   = '<li data-item="' . $max_item_id . '">';
	$item_html .= '<img class="item-image" src="' . $media_url . '">';
	$item_html .= '<div class="item-info">';
	$item_html .= '<input type="text" placeholder="' . __( 'Title', 'hocwp-theme' ) . '" value="" class="item-title" name="slider_items[items][' . $max_item_id . '][title]">';
	$item_html .= '<input type="url" placeholder="' . __( 'Link for this item', 'hocwp-theme' ) . '" value="" class="item-link" name="slider_items[items][' . $max_item_id . '][link]">';
	$item_html .= '<textarea class="item-description" name="slider_items[items][' . $max_item_id . '][description]"></textarea>';
	$item_html .= '</div>';
	ob_start();
	?>
	<div class="clear"></div>
	<div class="advance">
		<div class="dashicons dashicons-editor-expand"></div>
		<div class="box-content">
			<div class="settings">
				<div class="col-left col50 hocwp-col">
					<?php
					$field_args = array(
						'name'  => 'slider_items[items][' . $max_item_id . '][background_color]',
						'label' => __( 'Background Color', 'hocwp-theme' ),
						'value' => ''
					);
					hocwp_field_color_picker( $field_args );
					?>
				</div>
				<div class="col-right col50 hocwp-col">

				</div>
			</div>
		</div>
	</div>
	<?php
	$item_html .= ob_get_clean();
	$item_html .= '<input type="hidden" class="item-image-url" name="slider_items[items][' . $max_item_id . '][image_url]" value="' . $media_url . '">';
	$item_html .= '<input type="hidden" class="item-image-id" name="slider_items[items][' . $max_item_id . '][image_id]" value="' . $media_id . '">';
	$item_html .= '<span title="' . __( 'Delete this item', 'hocwp-theme' ) . '" class="item-icon icon-delete icon-sortable-ui"></span>';
	$item_html .= '<span title="' . __( 'Re-order this item', 'hocwp-theme' ) . '" class="item-icon icon-drag icon-sortable-ui"></span>';
	$item_html .= '<span title="' . __( 'Add child item', 'hocwp-theme' ) . '" class="item-icon icon-add icon-sortable-ui"></span>';
	$item_html .= '</li>';
	$result['html_data'] = $item_html;
	wp_send_json( $result );
}

add_action( 'wp_ajax_hocwp_generate_slider_sortable_item', 'hocwp_generate_slider_sortable_item_ajax_callback' );

function hocwp_remove_slider_item_ajax_callback() {
	$result  = array();
	$item_id = isset( $_POST['item_id'] ) ? $_POST['item_id'] : 0;
	if ( $item_id > 0 ) {
		$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;
		if ( $post_id > 0 ) {
			$value      = hocwp_get_slider_items( $post_id, true );
			$list_items = $value['items'];
			$item_order = $value['order'];
			unset( $list_items[ $item_id ] );
			$value['items'] = $list_items;
			$item_order     = hocwp_remove_array_item_by_value( $item_order, array( $item_id ) );
			$value['order'] = $item_order;
			update_post_meta( $post_id, 'slider_items', $value );
		}
	}
	wp_send_json( $result );
}

add_action( 'wp_ajax_hocwp_remove_slider_item', 'hocwp_remove_slider_item_ajax_callback' );