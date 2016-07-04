<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

function hocwp_get_administrators( $args = array() ) {
	$args['role'] = 'administrator';

	return get_users( $args );
}

function hocwp_is_subscriber( $user = null ) {
	$user = hocwp_return_user( $user );
	if ( ! is_wp_error( $user ) ) {
		$role = hocwp_get_user_role( $user );
		if ( 'subscriber' == $role ) {
			return true;
		}
	}

	return false;
}

function hocwp_get_first_admin( $args = array() ) {
	$users = hocwp_get_administrators( $args );
	$user  = new WP_User();
	foreach ( $users as $value ) {
		$user = $value;
		break;
	}

	return $user;
}

function hocwp_is_admin( $user = null ) {
	$user = hocwp_return_user( $user );
	if ( ! is_a( $user, 'WP_User' ) ) {
		return current_user_can( 'manage_options' );
	}
	if ( array_intersect( $user->roles, array( 'administrator' ) ) ) {
		return true;
	}

	return false;
}

function hocwp_count_user( $role = 'total_users' ) {
	$count  = count_users();
	$result = hocwp_get_value_by_key( $count, $role, $count['total_users'] );

	return $result;
}

function hocwp_remove_all_user_role( $user ) {
	foreach ( $user->roles as $role ) {
		$user->remove_role( $role );
	}
}

function hocwp_add_user( $args = array() ) {
	$result   = 0;
	$password = isset( $args['password'] ) ? $args['password'] : '';
	$role     = isset( $args['role'] ) ? $args['role'] : '';
	$username = isset( $args['username'] ) ? $args['username'] : '';
	$email    = isset( $args['email'] ) ? $args['email'] : '';
	if ( ! empty( $password ) && ! empty( $username ) && ! empty( $email ) && ! username_exists( $username ) && ! email_exists( $email ) ) {
		$user_id = wp_create_user( $username, $password, $email );
		$user    = get_user_by( 'id', $user_id );
		hocwp_remove_all_user_role( $user );
		if ( empty( $role ) ) {
			$role = get_option( 'default_role' );
			if ( empty( $role ) ) {
				$role = 'subscriber';
			}
			$role = apply_filters( 'hocwp_new_user_role', $role, $args );
		}
		$user->add_role( $role );
		$result = $user_id;
	}

	return $result;
}

function hocwp_add_user_admin( $args = array() ) {
	$args['role'] = 'administrator';
	hocwp_add_user( $args );
}

function hocwp_get_user_roles( $user = null ) {
	$roles = array();
	$user  = hocwp_return_user( $user );
	if ( is_a( $user, 'WP_User' ) ) {
		$roles = (array) $user->roles;
	}

	return $roles;
}

function hocwp_get_user_role( $user = null ) {
	$roles = hocwp_get_user_roles( $user );

	return current( $roles );
}

function hocwp_current_user_can_use_rich_editor() {
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) && get_user_option( 'rich_editing' ) == 'true' ) {
		return false;
	}

	return true;
}

function hocwp_return_user( $user_or_id = null, $output = OBJECT ) {
	if ( ( 'id' == strtolower( $output ) && hocwp_id_number_valid( $user_or_id ) ) || ( 'email' == strtolower( $output ) && is_email( $user_or_id ) ) ) {
		return $user_or_id;
	}
	if ( is_a( $user_or_id, 'WP_User' ) ) {
		$user = $user_or_id;
	} elseif ( hocwp_id_number_valid( $user_or_id ) ) {
		$user = get_user_by( 'id', $user_or_id );
	} elseif ( is_email( $user_or_id ) ) {
		$user = get_user_by( 'email', $user_or_id );
	} elseif ( ! empty( $user_or_id ) ) {
		$user = get_user_by( 'login', $user_or_id );
	} else {
		$user = wp_get_current_user();
	}
	if ( ! is_a( $user, 'WP_User' ) ) {
		return new WP_Error();
	}
	if ( OBJECT == strtoupper( $output ) ) {
		return $user;
	} else {
		$output = strtolower( $output );
		if ( 'id' == $output ) {
			return $user->ID;
		} elseif ( 'email' == $output ) {
			return $user->user_email;
		} elseif ( 'username' == $output || 'user_login' == $output ) {
			return $user->user_login;
		}
	}

	return $user->ID;
}

function hocwp_get_user_viewed_posts( $user_id = null ) {
	$user_id = hocwp_return_user( $user_id, 'id' );
	if ( hocwp_id_number_valid( $user_id ) ) {
		$viewed_posts = get_user_meta( $user_id, 'viewed_posts', true );
		$viewed_posts = hocwp_sanitize_array( $viewed_posts );
	} else {
		$viewed_posts = isset( $_SESSION['viewed_posts'] ) ? $_SESSION['viewed_posts'] : '';
		if ( ! empty( $viewed_posts ) ) {
			$viewed_posts = hocwp_json_string_to_array( $viewed_posts );
		}
		$viewed_posts = hocwp_sanitize_array( $viewed_posts );
	}

	return $viewed_posts;
}

function hocwp_track_user_viewed_posts() {
	$use = apply_filters( 'hocwp_track_user_viewed_posts', false );

	return $use;
}

function hocwp_get_user_favorite_posts( $user_id ) {
	$favorite_posts = get_user_meta( $user_id, 'favorite_posts', true );
	$favorite_posts = hocwp_sanitize_array( $favorite_posts );

	return $favorite_posts;
}

function hocwp_check_user_password( $password, $user ) {
	if ( ! is_a( $user, 'WP_User' ) ) {
		return false;
	}

	return wp_check_password( $password, $user->user_pass, $user->ID );
}

function hocwp_get_user_meta( $key, $user_id = null, $single = true ) {
	$user_id = hocwp_return_user( $user_id, 'id' );

	return get_user_meta( $user_id, $key, $single );
}

function hocwp_get_user_saved_posts( $user_id = null ) {
	$user_id = hocwp_return_user( $user_id, 'id' );
	$saved   = array();
	if ( hocwp_id_number_valid( $user_id ) ) {
		$saved = hocwp_get_user_meta( 'saved_posts', $user_id );
		$saved = hocwp_sanitize_array( $saved );
	}

	return $saved;
}

function hocwp_update_user_saved_posts( $user_id = null, $post_id = null ) {
	$user_id = hocwp_return_user( $user_id, 'id' );
	if ( hocwp_id_number_valid( $user_id ) ) {
		$post_id = hocwp_return_post( $post_id, 'id' );
		if ( hocwp_id_number_valid( $post_id ) ) {
			$saved = hocwp_get_user_saved_posts( $user_id );
			$saved = hocwp_sanitize_array( $saved );
			if ( in_array( $post_id, $saved ) ) {
				unset( $saved[ array_search( $post_id, $saved ) ] );
			} else {
				array_push( $saved, $post_id );
			}

			return update_user_meta( $user_id, 'saved_posts', $saved );
		}
	}

	return false;
}

function hocwp_user_viewed_posts_hook() {
	$use = hocwp_track_user_viewed_posts();
	if ( $use && is_singular() ) {
		$expired_interval = HOUR_IN_SECONDS;
		$expired_interval = apply_filters( 'hocwp_track_user_viewed_posts_expired_interval', $expired_interval );
		$now              = time();
		if ( is_user_logged_in() ) {
			$user                     = wp_get_current_user();
			$viewed_posts             = get_user_meta( $user->ID, 'viewed_posts', true );
			$viewed_posts             = hocwp_sanitize_array( $viewed_posts );
			$post_id                  = get_the_ID();
			$viewed_posts[ $post_id ] = $now;
			foreach ( $viewed_posts as $post_id => $time ) {
				$dif = $now - $time;
				if ( $expired_interval < $dif ) {
					unset( $viewed_posts[ $post_id ] );
				}
			}
			update_user_meta( $user->ID, 'viewed_posts', $viewed_posts );
		} else {
			$viewed_posts = isset( $_SESSION['viewed_posts'] ) ? $_SESSION['viewed_posts'] : '';
			if ( ! empty( $viewed_posts ) ) {
				$viewed_posts = hocwp_json_string_to_array( $viewed_posts );
			}
			$viewed_posts             = hocwp_sanitize_array( $viewed_posts );
			$post_id                  = get_the_ID();
			$viewed_posts[ $post_id ] = $now;
			foreach ( $viewed_posts as $post_id => $time ) {
				$dif = $now - $time;
				if ( $expired_interval < $dif ) {
					unset( $viewed_posts[ $post_id ] );
				}
			}
			$_SESSION['viewed_posts'] = json_encode( $viewed_posts );
		}
	}
}

add_action( 'wp', 'hocwp_user_viewed_posts_hook' );

function hocwp_allow_role_upload_media( $roles ) {
	$roles = hocwp_sanitize_array( $roles );
	$caps  = array(
		'upload_files',
		'publish_pages',
		'edit_published_pages',
		'edit_others_pages'
	);
	foreach ( $roles as $role ) {
		$role = get_role( $role );
		if ( is_a( $role, 'WP_Role' ) ) {
			foreach ( $caps as $cap ) {
				$role->add_cap( $cap );
			}
		}
	}
}