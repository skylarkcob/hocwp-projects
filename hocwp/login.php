<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

function hocwp_login_body_class( $classes, $action ) {
	$classes[] = 'hocwp';
	if ( ! empty( $action ) ) {
		$classes[] = 'action-' . $action;
	}

	return $classes;
}

add_filter( 'login_body_class', 'hocwp_login_body_class', 10, 2 );

function hocwp_login_redirect_if_logged_in() {
	$action = isset( $_GET['action'] ) ? $_GET['action'] : '';
	if ( empty( $action ) && is_user_logged_in() ) {
		wp_redirect( home_url( '/' ) );
		exit;
	}
}

add_action( 'login_init', 'hocwp_login_redirect_if_logged_in' );

function hocwp_get_login_logo_url() {
	$user_login = hocwp_option_get_object_from_list( 'user_login' );
	$url        = '';
	if ( hocwp_object_valid( $user_login ) ) {
		$option = $user_login->get();
		$logo   = hocwp_get_value_by_key( $option, 'logo' );
		$logo   = hocwp_sanitize_media_value( $logo );
		$url    = $logo['url'];
	}
	if ( empty( $url ) ) {
		$theme_setting = hocwp_option_get_object_from_list( 'theme_setting' );
		if ( hocwp_object_valid( $theme_setting ) ) {
			$option = $theme_setting->get();
			$logo   = hocwp_get_value_by_key( $option, 'logo' );
			$logo   = hocwp_sanitize_media_value( $logo );
			$url    = $logo['url'];
		}
	}

	return $url;
}

function hocwp_use_captcha_for_login_page() {
	$options     = get_option( 'hocwp_user_login' );
	$use_captcha = hocwp_get_value_by_key( $options, 'use_captcha' );
	$use_captcha = apply_filters( 'hocwp_use_captcha_for_login_page', $use_captcha );

	return (bool) $use_captcha;
}

function hocwp_login_captcha_field() {
	echo hocwp_login_get_captcha_field();
}

function hocwp_login_get_captcha_field() {
	ob_start();
	$args = array(
		'before' => '<p>',
		'after'  => '</p>'
	);
	hocwp_field_captcha( $args );

	return ob_get_clean();
}

function hocwp_login_form_top() {
	ob_start();
	do_action( 'hocwp_login_form_before' );

	return ob_get_clean();
}

function hocwp_login_form_middle() {
	ob_start();
	do_action( 'login_form' );

	return ob_get_clean();
}

function hocwp_login_form_bottom() {
	ob_start();
	do_action( 'hocwp_login_form_after' );

	return ob_get_clean();
}

function hocwp_verify_login_captcha( $user, $password ) {
	if ( isset( $_POST['captcha'] ) ) {
		$captcha_code = $_POST['captcha'];
		$captcha      = new HOCWP_Captcha();
		if ( $captcha->check( $captcha_code ) ) {
			return $user;
		}

		return new WP_Error( hocwp_translate_text( 'Captcha Invalid' ), '<strong>' . hocwp_translate_text( 'ERROR:' ) . '</strong> ' . hocwp_translate_text( 'Please enter a valid captcha.' ) );
	}

	return new WP_Error( hocwp_translate_text( 'Captcha Invalid' ), '<strong>' . hocwp_translate_text( 'ERROR:' ) . '</strong> ' . hocwp_translate_text( 'You are a robot, if not please check JavaScript enabled on your browser.' ) );
}

function hocwp_verify_registration_captcha( $errors, $sanitized_user_login, $user_email ) {
	if ( isset( $_POST['captcha'] ) ) {
		$captcha_code = $_POST['captcha'];
		$captcha      = new HOCWP_Captcha();
		if ( ! $captcha->check( $captcha_code ) ) {
			$errors->add( hocwp_translate_text( 'Captcha Invalid' ), '<strong>' . hocwp_translate_text( 'ERROR:' ) . '</strong> ' . hocwp_translate_text( 'Please enter a valid captcha.' ) );
		}
	} else {
		$errors->add( hocwp_translate_text( 'Captcha Invalid' ), '<strong>' . hocwp_translate_text( 'ERROR:' ) . '</strong> ' . hocwp_translate_text( 'You are a robot, if not please check JavaScript enabled on your browser.' ) );
	}

	return $errors;
}

function hocwp_verify_lostpassword_captcha() {
	if ( isset( $_POST['captcha'] ) ) {
		$captcha_code = $_POST['captcha'];
		$captcha      = new HOCWP_Captcha();
		if ( ! $captcha->check( $captcha_code ) ) {
			wp_die( '<strong>' . hocwp_translate_text( 'ERROR:' ) . '</strong> ' . hocwp_translate_text( 'Please enter a valid captcha.' ), hocwp_translate_text( 'Captcha Invalid' ) );
		}
	} else {
		wp_die( '<strong>' . hocwp_translate_text( 'ERROR:' ) . '</strong> ' . hocwp_translate_text( 'You are a robot, if not please check JavaScript enabled on your browser.' ), hocwp_translate_text( 'Captcha Invalid' ) );
	}
}

if ( hocwp_use_captcha_for_login_page() ) {
	add_action( 'login_form', 'hocwp_login_captcha_field' );
	add_action( 'lostpassword_form', 'hocwp_login_captcha_field' );
	add_action( 'register_form', 'hocwp_login_captcha_field' );
	add_filter( 'wp_authenticate_user', 'hocwp_verify_login_captcha', 10, 2 );
	add_filter( 'registration_errors', 'hocwp_verify_registration_captcha', 10, 3 );
	add_action( 'lostpassword_post', 'hocwp_verify_lostpassword_captcha' );
}

add_filter( 'login_form_top', 'hocwp_login_form_top' );
add_filter( 'login_form_middle', 'hocwp_login_form_middle' );
add_filter( 'login_form_bottom', 'hocwp_login_form_bottom' );

function hocwp_get_account_url( $type = 'login', $action = '' ) {
	$url          = '';
	$page_account = hocwp_get_pages_by_template( 'page-templates/account.php', array( 'output' => 'object' ) );
	switch ( $type ) {
		case 'signup':
		case 'register':
			$page = hocwp_get_pages_by_template( 'page-templates/register.php', array( 'output' => 'object' ) );
			if ( is_a( $page, 'WP_Post' ) ) {
				$url = get_permalink( $page );
			} else {
				if ( is_a( $page_account, 'WP_Post' ) ) {
					$url = get_permalink( $page_account );
					$url = trailingslashit( $url );
					$url = add_query_arg( array( 'action' => 'register' ), $url );
				}
			}
			break;
		case 'lostpassword':
			if ( is_a( $page_account, 'WP_Post' ) ) {
				$url = get_permalink( $page_account );
				$url = trailingslashit( $url );
				$url = add_query_arg( array( 'action' => 'lostpassword' ), $url );
			}
			break;
		default:
			if ( empty( $type ) || 'account' === $type ) {
				if ( is_a( $page_account, 'WP_Post' ) ) {
					$url = get_permalink( $page_account );
				}
			} else {
				$page = hocwp_get_pages_by_template( 'page-templates/login.php', array( 'output' => 'object' ) );
				if ( is_a( $page, 'WP_Post' ) ) {
					$url = get_permalink( $page );
				} else {
					if ( is_a( $page_account, 'WP_Post' ) ) {
						$url = get_permalink( $page_account );
						$url = trailingslashit( $url );
						if ( empty( $action ) ) {
							$action = 'login';
						}
						$url = add_query_arg( array( 'action' => $action ), $url );
					}
				}
			}
	}

	return $url;
}

function hocwp_user_force_login( $user_id ) {
	wp_set_auth_cookie( $user_id, true );
}

function hocwp_user_login( $username, $password, $remember = true ) {
	$credentials                  = array();
	$credentials['user_login']    = $username;
	$credentials['user_password'] = $password;
	$credentials['remember']      = $remember;
	$user                         = wp_signon( $credentials, false );
	if ( hocwp_allow_user_login_with_email() && ! is_a( $user, 'WP_User' ) ) {
		if ( is_email( $username ) && email_exists( $username ) ) {
			$new_user = get_user_by( 'email', $username );
			if ( hocwp_check_user_password( $password, $new_user ) ) {
				$user = $new_user;
				hocwp_user_force_login( $new_user->ID );
			}
		}
	}

	return $user;
}

function hocwp_account_form_default_args() {
	$lang     = hocwp_get_language();
	$defaults = array(
		'placeholder_username'    => hocwp_translate_text( __( 'Username or email', 'hocwp-theme' ) ),
		'placeholder_password'    => hocwp_translate_text( __( 'Password', 'hocwp-theme' ) ),
		'slogan'                  => hocwp_translate_text( __( 'One free account gets you into everything %s.', 'hocwp-theme' ) ),
		'title_lostpassword_link' => hocwp_translate_text( __( 'Password Lost and Found', 'hocwp-theme' ) ),
		'text_lostpassword_link'  => hocwp_translate_text( __( 'Lost your password?', 'hocwp-theme' ) ),
		'text_register_link'      => hocwp_translate_text( __( 'Register', 'hocwp-theme' ) ),
		'label_email'             => hocwp_translate_text( __( 'Email', 'hocwp-theme' ) ),
		'label_confirm_password'  => hocwp_translate_text( __( 'Confirm your password', 'hocwp-theme' ) ),
		'label_phone'             => hocwp_translate_text( __( 'Phone', 'hocwp-theme' ) )
	);

	return apply_filters( 'hocwp_account_form_default_args', $defaults );
}

function hocwp_execute_register() {
	$http_post             = ( 'POST' == $_SERVER['REQUEST_METHOD'] );
	$user_login            = '';
	$user_email            = '';
	$pwd                   = '';
	$pwd_again             = '';
	$phone                 = '';
	$captcha               = '';
	$error                 = false;
	$message               = hocwp_translate_text( 'There was an error occurred, please try again.' );
	$inserted              = false;
	$user_id               = 0;
	$registration_redirect = hocwp_get_value_by_key( $_REQUEST, 'redirect_to' );
	$redirect_to           = apply_filters( 'registration_redirect', $registration_redirect );
	if ( is_user_logged_in() ) {
		if ( empty( $redirect_to ) ) {
			$redirect_to = home_url( '/' );
		}
		wp_redirect( $redirect_to );
		exit;
	}
	$transient = '';
	if ( $http_post ) {
		$action = hocwp_get_method_value( 'action' );
		if ( 'register' === $action ) {
			$user_login     = hocwp_get_method_value( 'user_login' );
			$user_email     = hocwp_get_method_value( 'user_email' );
			$pwd            = hocwp_get_method_value( 'pwd' );
			$pwd_again      = hocwp_get_method_value( 'pwd_again' );
			$phone          = hocwp_get_method_value( 'phone' );
			$captcha        = hocwp_get_method_value( 'captcha' );
			$user_login     = sanitize_user( $user_login, true );
			$user_email     = sanitize_email( $user_email );
			$transient_name = hocwp_build_transient_name( 'hocwp_register_user_%s', $user_email );
			if ( false === ( $transient = get_transient( $transient_name ) ) ) {
				if ( empty( $user_login ) || empty( $user_email ) || empty( $pwd ) || empty( $pwd_again ) || empty( $phone ) || empty( $captcha ) ) {
					$error   = true;
					$message = hocwp_translate_text( 'Please enter your complete registration information.' );
				} elseif ( ! is_email( $user_email ) ) {
					$error   = true;
					$message = hocwp_translate_text( 'The email address is not correct.' );
				} elseif ( $pwd !== $pwd_again ) {
					$error   = true;
					$message = hocwp_translate_text( 'Password is incorrect.' );
				} elseif ( username_exists( $user_login ) ) {
					$error   = true;
					$message = hocwp_translate_text( 'Account already exists.' );
				} elseif ( email_exists( $user_email ) ) {
					$error   = true;
					$message = hocwp_translate_text( 'The email address already exists.' );
				} else {
					if ( isset( $_POST['captcha'] ) ) {
						$capt = new HOCWP_Captcha();
						if ( ! $capt->check( $captcha ) ) {
							$error   = true;
							$message = hocwp_translate_text( 'The security code is incorrect.' );
						}
					}
				}
				if ( ! $error ) {
					$user_data = array(
						'username' => $user_login,
						'password' => $pwd,
						'email'    => $user_email
					);
					$user      = hocwp_add_user( $user_data );
					if ( hocwp_id_number_valid( $user ) ) {
						update_user_meta( $user, 'phone', $phone );
						$inserted = true;
						hocwp_user_force_login( $user );
						$message = hocwp_translate_text( 'Your account has been successfully created.' );
						$user_id = $user;
						set_transient( $transient_name, $user_id );
					}
				}
				if ( $inserted && ! empty( $redirect_to ) ) {
					wp_redirect( $redirect_to );
					exit;
				}
			} else {
				if ( hocwp_id_number_valid( $transient ) ) {
					$inserted = true;
					$message  = hocwp_translate_text( 'Your account has been successfully created.' );
				}
			}
		}
	}
	$result = array(
		'user_login'  => $user_login,
		'user_email'  => $user_email,
		'pwd'         => $pwd,
		'pwd_again'   => $pwd_again,
		'phone'       => $phone,
		'captcha'     => $captcha,
		'error'       => $error,
		'message'     => $message,
		'inserted'    => $inserted,
		'redirect_to' => $redirect_to,
		'user_id'     => $user_id,
		'transient'   => $transient
	);

	return $result;
}

function hocwp_register_form( $args = array() ) {
	if ( is_user_logged_in() ) {
		return;
	}
	$defaults    = hocwp_account_form_default_args();
	$args        = wp_parse_args( $args, $defaults );
	$data        = hocwp_execute_register();
	$user_login  = $data['user_login'];
	$user_email  = $data['user_email'];
	$pwd         = $data['pwd'];
	$pwd_again   = $data['pwd_again'];
	$phone       = $data['phone'];
	$error       = $data['error'];
	$message     = $data['message'];
	$inserted    = $data['inserted'];
	$redirect_to = $data['redirect_to'];
	$logo        = hocwp_get_value_by_key( $args, 'logo', hocwp_get_login_logo_url() );
	?>
	<div class="hocwp-login-box module">
		<div class="module-header text-center">
			<?php
			if ( ! empty( $logo ) ) {
				$a = new HOCWP_HTML( 'a' );
				$a->set_href( home_url( '/' ) );
				$a->set_class( 'logo' );
				$img = new HOCWP_HTML( 'img' );
				$img->set_image_alt( '' );
				$img->set_image_src( $logo );
				$a->set_text( $img->build() );
				$a->output();
			}
			$slogan = new HOCWP_HTML( 'p' );
			$slogan->set_class( 'slogan' );
			$slogan->set_text( sprintf( $args['slogan'], hocwp_get_root_domain_name( home_url( '/' ) ) ) );
			$slogan->output();
			if ( isset( $_REQUEST['error'] ) || $error ) {
				$message = hocwp_build_message( $message, 'danger' );
				echo $message;
			} elseif ( $inserted || hocwp_id_number_valid( $data['transient'] ) ) {
				$message = hocwp_build_message( $message, 'success' );
				echo $message;
				hocwp_auto_reload_script();
			}
			?>
		</div>
		<div class="module-body">
			<h4 class="form-title"><?php hocwp_translate_text( 'Registration', true ); ?></h4>

			<form name="registerform register-form signup-form" id="registerform" action="" method="post"
			      novalidate="novalidate">
				<p>
					<label
						for="user_login"><?php echo hocwp_get_value_by_key( $args, 'label_username', hocwp_translate_text( 'Username' ) ); ?>
						<br/>
						<input type="text" name="user_login" id="user_login" class="input"
						       value="<?php echo esc_attr( wp_unslash( $user_login ) ); ?>" size="20"/></label>
				</p>

				<p>
					<label for="user_email"><?php echo $args['label_email']; ?><br/>
						<input type="email" name="user_email" id="user_email" class="input"
						       value="<?php echo esc_attr( wp_unslash( $user_email ) ); ?>" size="25"/></label>
				</p>

				<p>
					<label
						for="user_pass"><?php echo hocwp_get_value_by_key( $args, 'label_password', hocwp_translate_text( 'Password' ) ); ?>
						<br/>
						<input type="password" name="pwd" id="user_pass" class="input" value="<?php echo $pwd; ?>"
						       size="20"/></label>
				</p>

				<p>
					<label for="user_pass_again"><?php echo $args['label_confirm_password']; ?><br/>
						<input type="password" name="pwd_again" id="user_pass_again" class="input"
						       value="<?php echo $pwd_again; ?>" size="20"/></label>
				</p>

				<p>
					<label for="phone"><?php echo $args['label_phone']; ?><br/>
						<input type="text" name="phone" id="phone" class="input" value="<?php echo $phone; ?>"
						       size="20"/></label>
				</p>
				<?php do_action( 'register_form' ); ?>
				<input type="hidden" name="action" value="register">
				<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>"/>

				<p class="submit"><input type="submit" name="wp-submit" id="wp-submit"
				                         class="button button-primary button-large"
				                         value="<?php echo $args['text_register_link']; ?>"/></p>
			</form>
		</div>
		<div class="module-footer">
			<div class="text-center">
				<p class="form-nav">
					<a href="<?php echo esc_url( wp_login_url() ); ?>"><?php echo hocwp_get_value_by_key( $args, 'label_log_in', hocwp_translate_text( 'Login' ) ); ?></a>
					<span class="sep">|</span>
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"
					   title="<?php echo $args['title_lostpassword_link']; ?>"><?php echo $args['text_lostpassword_link']; ?></a>
				</p>
			</div>
		</div>
	</div>
	<?php
}

function hocwp_login_form( $args = array() ) {
	if ( is_user_logged_in() ) {
		return;
	}
	$action = hocwp_get_method_value( 'action', 'get' );
	if ( 'register' == $action ) {
		hocwp_register_form( $args );

		return;
	}
	if ( 'lostpassword' == $action ) {
		hocwp_lostpassword_form( $args );

		return;
	}
	$defaults     = hocwp_account_form_default_args();
	$args         = wp_parse_args( $args, $defaults );
	$placeholder  = (bool) hocwp_get_value_by_key( $args, 'placeholder', false );
	$args['echo'] = false;
	$form         = wp_login_form( $args );
	if ( $placeholder ) {
		$form = str_replace( 'name="log"', 'name="log" placeholder="' . $args['placeholder_username'] . '"', $form );
		$form = str_replace( 'name="pwd"', 'name="pwd" placeholder="' . $args['placeholder_password'] . '"', $form );
	}
	$logo      = hocwp_get_value_by_key( $args, 'logo', hocwp_get_login_logo_url() );
	$hide_form = (bool) hocwp_get_value_by_key( $args, 'hide_form' );
	?>
	<div class="hocwp-login-box module">
		<div class="module-header text-center">
			<?php
			if ( ! empty( $logo ) ) {
				$a = new HOCWP_HTML( 'a' );
				$a->set_href( home_url( '/' ) );
				$a->set_class( 'logo' );
				$img = new HOCWP_HTML( 'img' );
				$img->set_image_alt( '' );
				$img->set_image_src( $logo );
				$a->set_text( $img->build() );
				$a->output();
			}
			$slogan = new HOCWP_HTML( 'p' );
			$slogan->set_class( 'slogan' );
			$slogan->set_text( sprintf( $args['slogan'], hocwp_get_root_domain_name( home_url( '/' ) ) ) );
			$slogan->output();
			if ( isset( $_REQUEST['error'] ) ) {
				echo '<p class="alert alert-danger">' . hocwp_translate_text( __( 'There was an error occurred, please try again.', 'hocwp-theme' ) ) . '</p>';
			}
			?>
		</div>
		<div class="module-body">
			<h4 class="form-title"><?php hocwp_translate_text( __( 'Login', 'hocwp-theme' ), true ); ?></h4>
			<?php
			if ( $hide_form ) {
				$login_form_top    = apply_filters( 'login_form_top', '', $args );
				$login_form_middle = apply_filters( 'login_form_middle', '', $args );
				$login_form_bottom = apply_filters( 'login_form_bottom', '', $args );
				$form              = $login_form_top . $login_form_middle . $login_form_bottom;
				$form              = hocwp_wrap_tag( $form, 'form', 'login-form hocwp-login-form' );
				echo $form;
			} else {
				echo $form;
			}
			?>
		</div>
		<div class="module-footer">
			<div class="text-center">
				<p class="form-nav">
					<?php
					$mails = array(
						'confirm',
						'newpass'
					);
					if ( ! isset( $_GET['checkemail'] ) || ! in_array( $_GET['checkemail'], $mails ) ) {
						if ( hocwp_users_can_register() ) {
							$registration_url = sprintf( '<a href="%s">%s</a>', esc_url( wp_registration_url() ), $args['text_register_link'] );
							echo apply_filters( 'register', $registration_url ) . '<span class="sep">|</span>';
						}
					}
					?>
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"
					   title="<?php echo $args['title_lostpassword_link']; ?>"><?php echo $args['text_lostpassword_link']; ?></a>
				</p>
			</div>
		</div>
	</div>
	<?php
}

function hocwp_execute_lostpassword() {
	$http_post   = ( 'POST' == $_SERVER['REQUEST_METHOD'] );
	$user        = null;
	$user_login  = '';
	$user_id     = '';
	$user_email  = '';
	$error       = false;
	$message     = hocwp_translate_text( 'There was an error occurred, please try again.' );
	$redirect    = hocwp_get_value_by_key( $_REQUEST, 'redirect_to' );
	$redirect_to = apply_filters( 'lostpassword_redirect', $redirect );
	if ( is_user_logged_in() ) {
		if ( empty( $redirect_to ) ) {
			$redirect_to = home_url( '/' );
		}
		wp_redirect( $redirect_to );
		exit;
	}
	$transient = '';
	$captcha   = hocwp_get_method_value( 'captcha' );
	if ( $http_post ) {
		$action = hocwp_get_method_value( 'action' );
		if ( 'lostpassword' === $action || 'retrievepassword' === $action ) {
			$user_login     = hocwp_get_method_value( 'user_login' );
			$transient_name = hocwp_build_transient_name( 'hocwp_lostpassword_user_%s', $user_login );
			if ( ( isset( $_POST['submit'] ) || isset( $_POST['wp-submit'] ) ) && false === ( $transient = get_transient( $transient_name ) ) ) {
				if ( empty( $user_login ) ) {
					$error   = true;
					$message = hocwp_translate_text( 'Please enter your account name or email address.' );
				} else {
					if ( isset( $_POST['captcha'] ) ) {
						$capt = new HOCWP_Captcha();
						if ( ! $capt->check( $captcha ) ) {
							$error   = true;
							$message = hocwp_translate_text( 'The security code is incorrect.' );
						}
					}
					if ( ! $error ) {
						$user = hocwp_return_user( $user_login );
						if ( ! is_a( $user, 'WP_User' ) ) {
							$error   = true;
							$message = hocwp_translate_text( 'Username or email is not exists.' );
						} else {
							$user_login = $user->user_login;
							$user_id    = $user->ID;
							$user_email = $user->user_email;
						}
					}
				}
				if ( ! $error && is_a( $user, 'WP_User' ) ) {
					$key = get_password_reset_key( $user );
					if ( is_wp_error( $key ) ) {
						$error   = true;
						$message = hocwp_translate_text( 'There was an error occurred, please try again or contact the administrator.' );
					} else {
						$message = wpautop( hocwp_translate_text( 'Someone has requested a password reset for the following account:' ) );
						$message .= wpautop( network_home_url( '/' ) );
						$message .= wpautop( sprintf( hocwp_translate_text( 'Username: %s' ), $user_login ) );
						$message .= wpautop( hocwp_translate_text( 'If this was a mistake, just ignore this email and nothing will happen.' ) );
						$message .= wpautop( hocwp_translate_text( 'To reset your password, visit the following address:' ) );
						$message .= wpautop( network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) );

						if ( is_multisite() ) {
							$blogname = $GLOBALS['current_site']->site_name;
						} else {
							$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
						}
						$title   = sprintf( __( '[%s] Password Reset' ), $blogname );
						$title   = apply_filters( 'retrieve_password_title', $title, $user_login, $user );
						$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user );
						if ( ! is_email( $user_email ) ) {
							$user_email = $user->user_email;
						}
						if ( $message && ! hocwp_send_html_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {
							$error   = true;
							$message = hocwp_translate_text( 'The email could not be sent. Possible reason: your host may have disabled the mail() function.' );
						} else {
							$error   = false;
							$message = hocwp_translate_text( 'Password recovery information has been sent, please check your mailbox.' );
							set_transient( $transient_name, $user_id, 15 * MINUTE_IN_SECONDS );
						}
					}
				}
			} else {
				if ( hocwp_id_number_valid( $transient ) ) {
					$error   = false;
					$message = hocwp_translate_text( 'Password recovery information has been sent, please check your mailbox.' );
				}
			}
		}
	}
	$result = array(
		'user_id'     => $user_id,
		'user_email'  => $user_email,
		'user_login'  => $user_login,
		'captcha'     => $captcha,
		'error'       => $error,
		'message'     => $message,
		'redirect_to' => $redirect_to,
		'transient'   => $transient
	);

	return $result;
}

function hocwp_lostpassword_form( $args = array() ) {
	$defaults    = hocwp_account_form_default_args();
	$args        = wp_parse_args( $args, $defaults );
	$data        = hocwp_execute_lostpassword();
	$user_login  = $data['user_login'];
	$error       = $data['error'];
	$message     = $data['message'];
	$redirect_to = hocwp_get_value_by_key( $args, 'redirect_to', hocwp_get_method_value( 'redirect_to', 'get' ) );
	$logo        = hocwp_get_value_by_key( $args, 'logo', hocwp_get_login_logo_url() );
	?>
	<div class="hocwp-login-box module">
		<div class="module-header text-center">
			<?php
			if ( ! empty( $logo ) ) {
				$a = new HOCWP_HTML( 'a' );
				$a->set_href( home_url( '/' ) );
				$a->set_class( 'logo' );
				$img = new HOCWP_HTML( 'img' );
				$img->set_image_alt( '' );
				$img->set_image_src( $logo );
				$a->set_text( $img->build() );
				$a->output();
			}
			$slogan = new HOCWP_HTML( 'p' );
			$slogan->set_class( 'slogan' );
			$slogan->set_text( sprintf( $args['slogan'], hocwp_get_root_domain_name( home_url( '/' ) ) ) );
			$slogan->output();
			if ( isset( $_POST['submit'] ) || isset( $_POST['wp-submit'] ) ) {
				if ( isset( $_REQUEST['error'] ) || $error ) {
					$message = hocwp_build_message( $message, 'danger' );
					echo $message;
				} else {
					if ( ( ! empty( $message ) && ! $error ) || ( isset( $_POST['submit'] ) && ! empty( $message ) ) ) {
						$message = hocwp_build_message( $message, 'success' );
						echo $message;
					}
				}
			}
			?>
		</div>
		<div class="module-body">
			<h4 class="form-title"><?php hocwp_translate_text( 'Reset password', true ); ?></h4>

			<form name="lostpasswordform" id="lostpasswordform" action="<?php echo esc_url( wp_lostpassword_url() ); ?>"
			      method="post">
				<p>
					<label><?php echo hocwp_get_value_by_key( $args, 'label_username', hocwp_translate_text( 'Username or Email' ) ); ?>
						<br>
						<input type="text" size="20" value="<?php echo esc_attr( $user_login ); ?>" class="input"
						       id="user_login" name="user_login"></label>
				</p>
				<input type="hidden" name="action" value="lostpassword">
				<input type="hidden" name="redirect_to" value="<?php echo $redirect_to; ?>">

				<p class="submit">
					<input type="submit" name="wp-submit" id="wp-submit" class="button-primary"
					       value="Get New Password" tabindex="100"></p>
			</form>
		</div>
		<div class="module-footer">
			<div class="text-center">
				<p class="form-nav">
					<a href="<?php echo esc_url( wp_login_url() ); ?>"><?php echo hocwp_get_value_by_key( $args, 'label_log_in', hocwp_translate_text( 'Login' ) ); ?></a>
					<span class="sep">|</span>
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"
					   title="<?php echo $args['title_lostpassword_link']; ?>"><?php echo $args['text_lostpassword_link']; ?></a>
				</p>
			</div>
		</div>
	</div>
	<?php
}

function hocwp_login_after_password_reset( $user, $new_pass ) {
	if ( ! empty( $new_pass ) ) {
		$transient_name = 'hocwp_lostpassword_user_' . md5( $user->user_login );
		delete_transient( $transient_name );
	}
}

add_action( 'after_password_reset', 'hocwp_login_after_password_reset', 10, 2 );