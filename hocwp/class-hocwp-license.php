<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

class HOCWP_License {
	private $hashed_password;
	private $key;
	private $key_map;
	private $customer_name;
	private $customer_email;
	private $customer_phone;
	private $customer_identity;
	private $customer_url;
	private $code;
	private $hashed_code;
	private $use_for;
	private $domain;
	private $password;
	private $option;
	private $type;
	private $option_name;
	private $valid;
	private $generation;
	private $generated;

	public function set_generated( $generated ) {
		$this->generated = $generated;
	}

	public function get_generated() {
		return $this->generated;
	}

	public function set_generation( $generation ) {
		$this->generation = $generation;
	}

	public function get_generation() {
		return $this->generation;
	}

	public function set_valid( $valid ) {
		$this->valid = $valid;
	}

	public function get_valid() {
		return $this->valid;
	}

	public function set_option_name( $option_name ) {
		$this->option_name = $option_name;
	}

	public function get_option_name() {
		return $this->option_name;
	}

	public function set_type( $type ) {
		$this->type = $type;
	}

	public function get_type() {
		return $this->type;
	}

	public function set_option( HOCWP_Option $option ) {
		$this->option = $option;
	}

	public function get_option() {
		return $this->option;
	}

	public function set_password( $password ) {
		$this->password = $password;
	}

	public function get_password() {
		return $this->password;
	}

	public function set_domain( $domain ) {
		$domain       = esc_url( $domain );
		$domain       = hocwp_get_root_domain_name( $domain );
		$this->domain = $domain;
	}

	public function get_domain() {
		return $this->domain;
	}

	public function set_use_for( $use_for ) {
		$this->use_for = $use_for;
	}

	public function get_use_for() {
		return $this->use_for;
	}

	public function set_hashed_code( $hashed_code ) {
		$this->hashed_code = $hashed_code;
	}

	public function get_hashed_code() {
		return $this->hashed_code;
	}

	public function set_code( $code ) {
		$this->code = $code;
	}

	public function get_code() {
		return $this->code;
	}

	public function set_customer_url( $customer_url ) {
		$customer_url       = trailingslashit( $customer_url );
		$this->customer_url = $customer_url;
	}

	public function get_customer_url() {
		return $this->customer_url;
	}

	public function set_customer_identity( $customer_identity ) {
		$this->customer_identity = $customer_identity;
	}

	public function get_customer_identity() {
		return $this->customer_identity;
	}

	public function set_customer_phone( $customer_phone ) {
		$this->customer_phone = $customer_phone;
	}

	public function get_customer_phone() {
		return $this->customer_phone;
	}

	public function set_customer_email( $customer_email ) {
		$this->customer_email = $customer_email;
	}

	public function get_customer_email() {
		return $this->customer_email;
	}

	public function set_customer_name( $customer_name ) {
		$this->customer_name = $customer_name;
	}

	public function get_customer_name() {
		return $this->customer_name;
	}

	public function set_hashed_password( $hashed_password ) {
		$this->hashed_password = $hashed_password;
	}

	public function get_hashed_password() {
		return $this->hashed_password;
	}

	public function set_key( $key ) {
		$this->key = $key;
	}

	public function get_key() {
		return $this->key;
	}

	public function set_key_map( $key_map ) {
		$this->key_map = $key_map;
	}

	public function get_key_map() {
		return $this->key_map;
	}

	public function __construct() {
		$this->set_hashed_password( HOCWP_HASHED_PASSWORD );
		$this->set_key( '' );
		$this->set_type( 'theme' );
		$this->set_use_for( get_option( 'template' ) );
		$this->set_domain( home_url() );
		$this->set_customer_url( home_url( '/' ) );
		$this->set_customer_email( get_option( 'admin_email' ) );
		$option = hocwp_option_get_object_from_list( 'theme_license' );
		if ( hocwp_object_valid( $option ) ) {
			$this->set_option( $option );
		}
		if ( hocwp_object_valid( $option ) ) {
			$this->set_option_name( $option->get_option_name() );
		}
	}

	public function build_key_map() {
		$pieces   = array();
		$email    = $this->get_customer_email();
		$phone    = $this->get_customer_phone();
		$identity = $this->get_customer_identity();
		if ( empty( $email ) && empty( $phone ) && empty( $identity ) ) {
			return $pieces;
		}
		if ( ! empty( $email ) ) {
			$pieces[] = 'email';
		}
		if ( ! empty( $phone ) ) {
			$pieces[] = 'phone';
		}
		if ( ! empty( $identity ) ) {
			$pieces[] = 'identity';
		}
		$pieces[] = 'code';
		$pieces[] = 'domain';
		$pieces[] = 'use_for';
		shuffle( $pieces );
		$pieces[] = 'hashed_password';
		$pieces   = hocwp_sanitize_array( $pieces );
		$this->set_key_map( $pieces );

		return $pieces;
	}

	public function get_map_key_value( $key ) {
		$value = '';
		switch ( $key ) {
			case 'email':
				$value = $this->get_customer_email();
				break;
			case 'phone':
				$value = $this->get_customer_phone();
				break;
			case 'identity':
				$value = $this->get_customer_identity();
				break;
			case 'code':
				$value = $this->get_code();
				break;
			case 'domain':
				$value = $this->get_domain();
				break;
			case 'use_for':
				$value = $this->get_use_for();
				break;
			default:
				if ( ! $this->get_generation() || $this->compare_password() ) {
					$value = $this->get_hashed_password();
				}
				break;
		}

		return $value;
	}

	public function get_saved_license_data() {
		return get_option( $this->get_option_name() );
	}

	public function get_saved_generated_data() {
		$data  = get_option( 'hocwp_license' );
		$value = hocwp_get_value_by_key( $data, array( $this->get_type(), md5( $this->get_use_for() ) ) );

		return $value;
	}

	public function create_key() {
		if ( $this->get_generation() ) {
			$pieces = $this->build_key_map();
		} else {
			$pieces = $this->get_key_map();
			if ( ! hocwp_array_has_value( $pieces ) ) {
				$data   = $this->get_saved_generated_data();
				$pieces = hocwp_get_value_by_key( $data, 'key_map' );
			}
		}
		$result = '';
		if ( count( $pieces ) >= 3 ) {
			foreach ( $pieces as $piece ) {
				if ( ! empty( $piece ) ) {
					$value = $this->get_map_key_value( $piece );
					if ( ! empty( $value ) ) {
						if ( is_array( $value ) ) {
							$value = implode( '_', $value );
						}
						$result .= $value . '_';
					}
				}
			}
		}
		$result = trim( $result, '_' );
		$this->set_key( $result );

		return $result;
	}

	public function compare_password() {
		return wp_check_password( $this->get_password(), $this->get_hashed_password() );
	}

	public function generate() {
		$result = array();
		if ( ! $this->compare_password() ) {
			$result = new WP_Error( 'set_hocwp_password', __( 'Please set default HocWP password first!', 'hocwp-theme' ) );
		} else {
			$this->set_generation( true );
			$this->create_key();
			$this->set_hashed_code( wp_hash_password( $this->get_key() ) );
			$url = $this->get_customer_url();
			if ( ! empty( $url ) ) {
				$url           = add_query_arg( array(
					'hashed'         => $this->get_hashed_code(),
					'key_map'        => $this->get_key_map(),
					'type'           => $this->get_type(),
					'use_for'        => $this->get_use_for(),
					'hocwp_password' => $this->get_password()
				), $url );
				$result['url'] = $url;
			}
			$result['code']           = $this->get_code();
			$result['customer_email'] = $this->get_customer_email();
			$result['hashed']         = $this->get_hashed_code();
			$result['key_map']        = $this->get_key_map();
		}
		$this->set_generated( $result );

		return $result;
	}

	public function for_theme() {
		if ( 'theme' == $this->get_type() ) {
			return true;
		}

		return false;
	}

	public function get_transient_name() {
		return hocwp_build_license_transient_name( $this->get_type(), $this->get_use_for() );
	}

	public function check_valid( $data = array() ) {
		$transient_name = $this->get_transient_name();
		if ( false === ( $valid = get_transient( $transient_name ) ) ) {
			$from_data_param = false;
			if ( hocwp_array_has_value( $data ) ) {
				$data_hashed  = hocwp_get_value_by_key( $data, 'hashed' );
				$data_key_map = hocwp_get_value_by_key( $data, 'key_map' );
				if ( ! empty( $data_hashed ) && ! empty( $data_key_map ) ) {
					$from_data_param = true;
				}
			}
			if ( ! $from_data_param ) {
				$data = $this->get_saved_generated_data();
			}
			$hashed_license = hocwp_get_value_by_key( $data, 'hashed' );
			if ( ! empty( $hashed_license ) ) {
				$key_map = maybe_unserialize( hocwp_get_value_by_key( $data, 'key_map' ) );
				$this->set_key_map( $key_map );
				$license_info = $this->get_saved_license_data();
				if ( $this->for_theme() ) {
					$code = hocwp_get_value_by_key( $license_info, 'license_code' );
					$this->set_code( $code );
					$email = hocwp_get_value_by_key( $license_info, 'customer_email' );
					$this->set_customer_email( $email );
				} else {
					$use_for     = $this->get_use_for();
					$use_for_key = md5( $use_for );
					$code        = hocwp_get_value_by_key( $license_info, array( $use_for_key, 'license_code' ) );
					$this->set_code( $code );
					$email = hocwp_get_value_by_key( $license_info, array( $use_for_key, 'customer_email' ) );
					$this->set_customer_email( $email );
				}
				$this->create_key();
				$key = $this->get_key();
				if ( ! function_exists( 'wp_check_password' ) ) {
					require( ABSPATH . WPINC . '/pluggable.php' );
				}
				if ( wp_check_password( $key, $hashed_license ) ) {
					$valid = true;
				}
			}
			$valid = (bool) $valid;
			$this->set_valid( $valid );
			if ( ! $valid && $from_data_param ) {
				$valid = $this->check_valid();
			}
			if ( $valid ) {
				set_transient( $transient_name, $valid, WEEK_IN_SECONDS );
			}
		}
		if ( ! $valid ) {
			$license_info = $this->get_saved_license_data();
			$code         = hocwp_get_value_by_key( $license_info, 'license_code' );
			$email        = hocwp_get_value_by_key( $license_info, 'customer_email' );
			$use_for      = $this->get_use_for();
			$valid        = $this->check_from_server( array(
				'customer_email' => $email,
				'license_code'   => $code,
				'use_for'        => $use_for
			) );
		}
		if ( $valid ) {
			set_transient( $transient_name, $valid, WEEK_IN_SECONDS );
		}

		return (bool) $valid;
	}

	public function check_from_server( $args = array() ) {
		$transient_name = md5( json_encode( $args ) );
		if ( false === ( $valid = get_transient( $transient_name ) ) ) {
			$customer_email = hocwp_get_value_by_key( $args, 'customer_email', hocwp_get_value_by_key( $args, 'email', hocwp_get_admin_email() ) );
			if ( ! is_email( $customer_email ) ) {
				$customer_email = hocwp_get_admin_email();
			}
			$code    = hocwp_get_value_by_key( $args, 'license_code', hocwp_get_value_by_key( $args, 'code' ) );
			$domain  = hocwp_get_value_by_key( $args, 'customer_domain', hocwp_get_value_by_key( $args, 'domain', home_url() ) );
			$use_for = hocwp_get_value_by_key( $args, 'use_for' );
			if ( empty( $domain ) ) {
				$domain = esc_url( hocwp_get_root_domain_name( home_url() ) );
			}
			$meta_item = array(
				'relation' => 'AND',
				array(
					'key'   => 'customer_domain',
					'value' => untrailingslashit( esc_url( hocwp_get_root_domain_name( $domain ) ) )
				),
				array(
					'key'   => 'forever_domain',
					'value' => 1,
					'type'  => 'numeric'
				)
			);
			if ( hocwp_is_localhost() ) {
				array_push( $meta_item, array(
					'key'   => 'customer_email',
					'value' => sanitize_email( $customer_email )
				) );
			}
			$data = hocwp_api_get_by_meta( $meta_item, 'license-api' );
			if ( hocwp_array_has_value( $data ) ) {
				$valid = true;
			} else {
				$meta_item = array(
					'relation' => 'AND',
					array(
						'key'   => 'customer_email',
						'value' => sanitize_email( $customer_email )
					),
					array(
						'key'   => 'forever_email',
						'value' => 1,
						'type'  => 'numeric'
					),
					array(
						'key'   => 'use_for',
						'value' => $use_for
					)
				);
				$data      = hocwp_api_get_by_meta( $meta_item, 'license-api' );
				if ( hocwp_array_has_value( $data ) ) {
					$valid = true;
				} else {
					$meta_item = array(
						'relation' => 'AND',
						array(
							'key'   => 'customer_domain',
							'value' => esc_url( untrailingslashit( $domain ) )
						),
						array(
							'key'   => 'customer_email',
							'value' => sanitize_email( $customer_email )
						),
						array(
							'key'   => 'license_code',
							'value' => $code
						),
						array(
							'key'   => 'use_for',
							'value' => $use_for
						)
					);
					$data      = hocwp_api_get_by_meta( $meta_item, 'license-api' );
					if ( hocwp_array_has_value( $data ) ) {
						$valid = true;
					}
				}
			}
			set_transient( $transient_name, $valid, 15 * MINUTE_IN_SECONDS );
		}
		$valid = (bool) $valid;

		return apply_filters( 'hocwp_check_license_on_server', $valid, $args );
	}
}