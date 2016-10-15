<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

include HOCWP_PATH . '/php-data.php';

function hocwp_permutation( &$a, &$b ) {
	$tmp = $a;
	$a   = $b;
	$b   = $tmp;
}

function hocwp_create_file( $path, $content = '' ) {
	if ( $fh = fopen( $path, 'w' ) ) {
		fwrite( $fh, $content, 1024 );
		fclose( $fh );
	}
}

function hocwp_get_pc_ip() {
	$result = '';
	if ( function_exists( 'getHostByName' ) ) {
		if ( version_compare( PHP_VERSION, '5.3', '<' ) && function_exists( 'php_uname' ) ) {
			$result = getHostByName( php_uname( 'n' ) );
		} elseif ( function_exists( 'getHostName' ) ) {
			$result = getHostByName( getHostName() );
		}
	}

	return $result;
}

function hocwp_get_alphabetical_chars() {
	$result = '#ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$result = str_split( $result );

	return $result;
}

function hocwp_is_ip( $ip ) {
	if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
		return true;
	}

	return false;
}

function hocwp_get_ipinfo( $ip ) {
	if ( ! hocwp_is_ip( $ip ) ) {
		return '';
	}
	$json    = @file_get_contents( 'http://ipinfo.io/' . $ip );
	$details = json_decode( $json );
	$details = (array) $details;

	return $details;
}

function hocwp_get_user_isp_ip() {
	$client  = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = $_SERVER['REMOTE_ADDR'];
	if ( hocwp_is_ip( $client ) ) {
		$ip = $client;
	} elseif ( hocwp_is_ip( $forward ) ) {
		$ip = $forward;
	} else {
		$ip = $remote;
	}

	return $ip;
}

function hocwp_array_has_value( $arr ) {
	if ( is_array( $arr ) && count( $arr ) > 0 ) {
		return true;
	}

	return false;
}

function hocwp_array_insert( &$array, $position, $insert ) {
	if ( is_int( $position ) ) {
		if ( is_array( $insert ) ) {
			$insert = array( $insert );
		}
		array_splice( $array, $position, 0, $insert );
	} else {
		$pos    = array_search( $position, array_keys( $array ) );
		$firsts = array_slice( $array, 0, $pos );
		$lasts  = array_slice( $array, $pos );
		$array  = $firsts + $insert + $lasts;
	}
}

function hocwp_string_empty( $string ) {
	if ( '' === $string ) {
		return true;
	}

	return false;
}

function hocwp_get_value_by_key( $arr, $key, $default = '' ) {
	if ( is_object( $key ) || is_object( $arr ) || hocwp_string_empty( $key ) ) {
		return $default;
	}
	$has_key = false;
	$arr     = hocwp_to_array( $arr );
	$result  = '';
	if ( hocwp_array_has_value( $arr ) ) {
		if ( is_array( $key ) ) {
			if ( count( $key ) == 1 ) {
				$key = array_shift( $key );
				if ( isset( $arr[ $key ] ) ) {
					return $arr[ $key ];
				}
			} else {
				$tmp = $arr;
				if ( is_array( $tmp ) ) {
					$has_value = false;
					$level     = 0;
					foreach ( $key as $index => $child_key ) {
						if ( is_array( $child_key ) ) {
							if ( count( $child_key ) == 1 ) {
								$child_key = array_shift( $child_key );
							}
							$result = hocwp_get_value_by_key( $tmp, $child_key );
						} else {
							if ( isset( $tmp[ $child_key ] ) ) {
								$tmp       = $tmp[ $child_key ];
								$has_value = true;
								$level ++;
								$has_key = true;
							}
						}
					}
					if ( ! $has_value ) {
						reset( $key );
						$first_key = current( $key );
						if ( hocwp_array_has_value( $arr ) ) {
							$tmp = hocwp_get_value_by_key( $arr, $first_key );
							if ( hocwp_array_has_value( $tmp ) ) {
								$result = hocwp_get_value_by_key( $tmp, $key );
							}
						}
					}
					if ( $has_value && hocwp_string_empty( $result ) ) {
						$result = $tmp;
					}
				}
			}
		} else {
			if ( isset( $arr[ $key ] ) ) {
				$result  = $arr[ $key ];
				$has_key = true;
			} else {
				foreach ( $arr as $index => $value ) {
					if ( is_array( $value ) ) {
						$result = hocwp_get_value_by_key( $value, $key );
					} else {
						if ( $key === $index ) {
							$has_key = true;
							$result  = $value;
						}
					}
				}
			}
		}
	}
	if ( ! $has_key ) {
		$result = $default;
	}

	return $result;
}

function hocwp_get_method_value( $key, $method = 'post', $default = '' ) {
	$method = strtoupper( $method );
	switch ( $method ) {
		case 'POST':
			$result = hocwp_get_value_by_key( $_POST, $key, $default );
			break;
		case 'GET':
			$result = hocwp_get_value_by_key( $_GET, $key, $default );
			break;
		default:
			$result = hocwp_get_value_by_key( $_REQUEST, $key, $default );
	}

	return $result;
}

function hocwp_array_unique( $arr ) {
	if ( is_array( $arr ) ) {
		$arr = array_map( 'unserialize', array_unique( array_map( 'serialize', $arr ) ) );
	}

	return $arr;
}

function hocwp_remove_select_tag_keep_content( $content ) {
	$content = strip_tags( $content, '<optgroup><option>' );

	return $content;
}

function hocwp_id_number_valid( $id ) {
	return hocwp_is_positive_number( $id );
}

function hocwp_is_positive_number( $number ) {
	if ( is_numeric( $number ) && $number > 0 ) {
		return true;
	}

	return false;
}

function hocwp_in_array( $needle, $haystack ) {
	if ( ! is_array( $haystack ) || is_array( $needle ) ) {
		return false;
	}
	if ( in_array( $needle, $haystack ) ) {
		return true;
	}
	foreach ( $haystack as $element ) {
		if ( is_array( $element ) && hocwp_in_array( $needle, $element ) ) {
			return true;
		} elseif ( $element == $needle ) {
			return true;
		}
	}

	return false;
}

function hocwp_get_first_char( $string, $encoding = 'UTF-8' ) {
	$result = '';
	if ( ! empty( $string ) ) {
		if ( function_exists( 'mb_substr' ) ) {
			$result = mb_substr( $string, 0, 1, $encoding );
		} else {
			$result = substr( $string, 0, 1 );
		}
	}

	return $result;
}

function hocwp_remove_first_char( $string, $char = '' ) {
	if ( ! empty( $char ) ) {
		$string = ltrim( $string, $char );
	} else {
		$len    = strlen( $string );
		$string = substr( $string, 1, $len - 1 );
	}

	return $string;
}

function hocwp_get_last_char( $string, $encoding = 'UTF-8' ) {
	$result = '';
	if ( ! empty( $string ) ) {
		if ( function_exists( 'mb_substr' ) ) {
			$result = mb_substr( $string, - 1, 1, $encoding );
		} else {
			$result = substr( $string, - 1 );
		}
	}

	return $result;
}

function hocwp_remove_last_char( $string, $char = '' ) {
	if ( empty( $char ) ) {
		$len    = strlen( $string );
		$string = substr( $string, 0, $len - 1 );
	} else {
		$string = rtrim( $string, $char );
	}

	return $string;
}

function hocwp_remove_first_char_and_last_char( $string, $char = '' ) {
	if ( empty( $char ) ) {
		$string = hocwp_remove_first_char( $string );
		$string = hocwp_remove_last_char( $string );
	} else {
		$string = trim( $string, $char );
	}

	return $string;
}

function hocwp_uppercase( $string, $encoding = 'utf-8' ) {
	if ( function_exists( 'mb_strtoupper' ) ) {
		return mb_strtoupper( $string, $encoding );
	}

	return strtoupper( $string );
}

function hocwp_uppercase_first_char( $string, $encoding = 'utf-8' ) {
	$first_char = hocwp_get_first_char( $string, $encoding );
	if ( function_exists( 'mb_strlen' ) ) {
		$len  = mb_strlen( $string, $encoding );
		$then = mb_substr( $string, 1, $len - 1, $encoding );
	} else {
		$len  = strlen( $string );
		$then = substr( $string, 1, $len - 1 );
	}
	$first_char = hocwp_uppercase( $first_char, $encoding );

	return $first_char . $then;
}

function hocwp_uppercase_all_first_char( $string ) {
	$words = explode( ' ', $string );
	$words = array_map( 'hocwp_uppercase_first_char', $words );

	return implode( ' ', $words );
}

function hocwp_uppercase_first_char_only( $string, $encoding = 'utf-8' ) {
	$string = hocwp_lowercase( $string, $encoding );
	$string = hocwp_uppercase_first_char( $string, $encoding );

	return $string;
}

function hocwp_lowercase( $string, $encoding = 'utf-8' ) {
	if ( function_exists( 'mb_strtolower' ) ) {
		return mb_strtolower( $string, $encoding );
	}

	return strtolower( $string );
}

function hocwp_string_contain( $string, $needle ) {
	if ( function_exists( 'mb_strpos' ) ) {
		if ( false !== mb_strpos( $string, $needle, null, 'UTF-8' ) ) {
			return true;
		}
	} else {
		if ( false !== strpos( $string, $needle ) ) {
			return true;
		}
	}

	return false;
}

function hocwp_get_href( $link ) {
	if ( hocwp_string_contain( $link, '</a>' ) ) {
		$a = new SimpleXMLElement( $link );
		if ( isset( $a['href'] ) ) {
			$href = (array) $a['href'];
			$href = array_shift( $href );

			return $href;
		}
	}

	return '';
}

function hocwp_transmit_value( &$value1, &$value2 ) {
	if ( ! empty( $value1 ) || ! empty( $value2 ) ) {
		if ( empty( $value1 ) && ! empty( $value2 ) ) {
			$value1 = $value2;
		}
		if ( empty( $value2 ) && ! empty( $value1 ) ) {
			$value2 = $value1;
		}
	}
}

function hocwp_transmit_id_and_name( &$id, &$name ) {
	hocwp_transmit_value( $id, $name );
}

function hocwp_number_format_vietnamese( $number ) {
	$number = floatval( $number );

	return number_format( $number, 0, '.', ',' );
}

function hocwp_to_array( $needle, $filter_and_unique = false ) {
	$result = $needle;
	if ( ! is_array( $result ) ) {
		$result = (array) $result;
	}
	if ( $filter_and_unique ) {
		$result = array_filter( $result );
		$result = array_unique( $result );
	}

	return $result;
}

function hocwp_string_to_array( $delimiter, $text ) {
	if ( is_array( $text ) ) {
		return $text;
	}
	if ( empty( $text ) ) {
		return array();
	}
	$result = explode( $delimiter, $text );
	$result = hocwp_to_array( $result );

	return $result;
}

function hocwp_trim_array_item( $item ) {
	if ( is_string( $item ) ) {
		$item = trim( $item );
	}

	return $item;
}

function hocwp_remove_empty_array_item( $arr, $remove_zero = false ) {
	if ( is_array( $arr ) ) {
		foreach ( $arr as $key => $item ) {
			if ( is_array( $item ) ) {
				$arr[ $key ] = hocwp_remove_empty_array_item( $item );
			} elseif ( hocwp_string_empty( $item ) || ( $remove_zero && 0 === $item ) ) {
				unset( $arr[ $key ] );
			}
		}
	}

	return $arr;
}

function hocwp_paragraph_to_array( $list_paragraph ) {
	$list_paragraph = str_replace( '</p>', '', $list_paragraph );
	$list_paragraph = explode( '<p>', $list_paragraph );

	return hocwp_to_array( $list_paragraph );
}

function hocwp_object_to_array( $object ) {
	$result = json_decode( json_encode( $object ), true );

	return hocwp_to_array( $result );
}

function hocwp_std_object_to_array( $object ) {
	return hocwp_json_string_to_array( json_encode( $object ) );
}

function hocwp_json_string_to_array( $json_string ) {
	if ( ! is_array( $json_string ) ) {
		$json_string = stripslashes( $json_string );
		$json_string = json_decode( $json_string, true );
	}
	$json_string = hocwp_to_array( $json_string );

	return $json_string;
}

function hocwp_string_to_datetime( $string, $format = '' ) {
	if ( empty( $format ) ) {
		$format = 'Y-m-d H:i:s';
	}
	$string = str_replace( '/', '-', $string );
	$string = trim( $string );

	return date( $format, strtotime( $string ) );
}

function hocwp_get_safe_characters( $special_char = false ) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	if ( $special_char ) {
		$characters .= '{}#,!_@^';
		$characters .= '():.|`$';
		$characters .= '[];?=+-*~%';
	}

	return $characters;
}

function hocwp_random_string( $length = 10, $characters = '', $special_char = false ) {
	if ( empty( $characters ) ) {
		$characters = hocwp_get_safe_characters( $special_char );
	}
	$len    = strlen( $characters );
	$result = '';
	for ( $i = 0; $i < $length; $i ++ ) {
		$random_char = $characters[ rand( 0, $len - 1 ) ];
		$result .= $random_char;
	}

	return $result;
}

function hocwp_is_mobile_domain( $domain ) {
	$domain = hocwp_get_domain_name( $domain );
	$chars  = substr( $domain, 0, 2 );
	if ( 'm.' == $chars ) {
		return true;
	}

	return false;
}

function hocwp_get_force_mobile() {
	$mobile = hocwp_get_method_value( 'mobile', 'get' );

	return $mobile;
}

function hocwp_is_force_mobile() {
	$mobile = hocwp_get_force_mobile();
	if ( 'true' == $mobile || 1 == absint( $mobile ) ) {
		return true;
	}

	return false;
}

function hocwp_is_force_mobile_session( $session ) {
	if ( isset( $_SESSION[ $session ] ) && 'mobile' == $_SESSION[ $session ] ) {
		return true;
	}

	return false;
}

function hocwp_is_force_mobile_cookie( $cookie ) {
	if ( isset( $_COOKIE[ $cookie ] ) && 'mobile' == $_COOKIE[ $cookie ] ) {
		return true;
	}

	return false;
}

function hocwp_get_domain_name( $url ) {
	if ( is_object( $url ) || is_array( $url ) ) {
		return '';
	}
	$url    = strval( $url );
	$parse  = parse_url( $url );
	$result = isset( $parse['host'] ) ? $parse['host'] : '';

	return $result;
}

function hocwp_get_domain_name_only( $url ) {
	$root = hocwp_get_root_domain_name( $url );
	if ( hocwp_is_ip( $root ) ) {
		return $root;
	}
	$root = explode( '.', $root );

	return array_shift( $root );
}

function hocwp_get_root_domain_name( $url ) {
	$domain_name = hocwp_get_domain_name( $url );
	if ( hocwp_is_ip( $domain_name ) ) {
		return $domain_name;
	}
	$data     = explode( '.', $domain_name );
	$parts    = $data;
	$last     = array_pop( $parts );
	$sub_last = array_pop( $parts );
	$keep     = 2;
	if ( 2 == strlen( $last ) ) {
		switch ( $sub_last ) {
			case 'net':
			case 'info':
			case 'org':
			case 'com':
				$keep = 3;
				break;
		}
	}
	while ( count( $data ) > $keep ) {
		array_shift( $data );
	}
	$domain_name = implode( '.', $data );
	$last        = array_pop( $data );
	if ( 'localhost' == $last || strlen( $last ) > 6 ) {
		$domain_name = $last;
	}

	return $domain_name;
}

function hocwp_random_string_number( $length = 6 ) {
	return hocwp_random_string( $length, '0123456789' );
}

function hocwp_is_image_url( $url ) {
	$img_formats = array( 'png', 'jpg', 'jpeg', 'gif', 'tiff', 'bmp', 'ico' );
	$path_info   = pathinfo( $url );
	$extension   = isset( $path_info['extension'] ) ? $path_info['extension'] : '';
	$extension   = trim( strtolower( $extension ) );
	if ( in_array( $extension, $img_formats ) ) {
		return true;
	}

	return false;
}

function hocwp_url_valid( $url ) {
	if ( ! empty( $url ) && is_string( $url ) ) {
		if ( hocwp_is_image_url( $url ) || filter_var( $url, FILTER_VALIDATE_URL ) !== false ) {
			return true;
		}
	}

	return false;
}

function hocwp_is_url( $url ) {
	return hocwp_url_valid( $url );
}

function hocwp_get_string_between_chars( $string, $char_start, $char_end ) {
	preg_match( "/\\$char_start(.*)\\$char_end/", $string, $matches );
	$result = '';
	if ( isset( $matches[1] ) ) {
		$result = $matches[1];
	}

	return $result;
}

function hocwp_is_hex_color( $color ) {
	return preg_match( '/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i', $color );
}

function hocwp_is_rgb_color( $color ) {
	$result = false;
	$color  = strtolower( $color );
	$parts  = hocwp_get_string_between_chars( $color, '(', ')' );
	$parts  = explode( ',', $parts );
	if ( hocwp_array_has_value( $parts ) ) {
		$sub   = substr( $color, 0, 4 );
		$count = count( $parts );
		if ( 'rgba' === $sub && 4 == $count ) {
			$result = true;
		} else {
			$sub = substr( $color, 0, 3 );
			if ( 'rgb' === $sub && 3 == $count ) {
				$result = true;
			}
		}
		if ( $result ) {
			foreach ( $parts as $rgb ) {
				if ( ! is_numeric( $rgb ) ) {
					$result = false;
					break;
				}
			}
		}
	}

	return $result;
}

function hocwp_is_color_name( $color ) {
	$rgb = hocwp_color_name_to_rgb( $color );
	if ( ! empty( $rgb ) ) {
		return $rgb;
	}

	return false;
}

function hocwp_color_name_to_rgb( $color ) {
	$colors = hocwp_get_colors();
	$color  = strtolower( $color );
	$color  = ucfirst( $color );
	if ( isset( $colors[ $color ] ) ) {
		return $colors[ $color ];
	}

	return '';
}

function hocwp_color_valid( $color ) {
	$result = false;
	if ( ! empty( $color ) && is_string( $color ) ) {
		if ( hocwp_is_hex_color( $color ) || hocwp_is_rgb_color( $color ) || hocwp_is_color_name( $color ) ) {
			$result = true;
		}
	}

	return $result;
}

function hocwp_is_color( $color ) {
	return hocwp_color_valid( $color );
}

function hocwp_is_last_item( $count, $column ) {
	if ( $count % $column == 0 ) {
		return true;
	}

	return false;
}

function hocwp_is_first_item( $count, $column ) {
	if ( 1 == $count ) {
		return true;
	}
	$tmp = $count - 1;
	if ( hocwp_is_last_item( $tmp, $column ) ) {
		return true;
	}

	return false;
}

function hocwp_column_width_percentage( $column ) {
	$width = '100';
	if ( hocwp_is_positive_number( $column ) ) {
		$width = round( 100 / $column, 2 );
	}
	if ( $width > 100 ) {
		$width = 100;
	}
	$width .= '%';

	return $width;
}

function hocwp_url_exists( $url ) {
	$file_headers = @get_headers( $url );
	$result       = true;
	if ( $file_headers[0] == 'HTTP/1.1 404 Not Found' ) {
		$result = false;
	}

	return $result;
}

function hocwp_get_all_image_from_string( $data, $output = 'img' ) {
	$output = trim( $output );
	preg_match_all( '/<img[^>]+>/i', $data, $matches );
	$matches = isset( $matches[0] ) ? $matches[0] : array();
	if ( ! hocwp_array_has_value( $matches ) && ! empty( $data ) ) {
		if ( false !== strpos( $data, '//' ) && ( false !== strpos( $data, '.jpg' ) || false !== strpos( $data, '.png' ) || false !== strpos( $data, '.gif' ) ) ) {
			$sources = explode( PHP_EOL, $data );
			if ( hocwp_array_has_value( $sources ) ) {
				foreach ( $sources as $src ) {
					if ( hocwp_is_image( $src ) ) {
						if ( 'img' == $output ) {
							$matches[] = '<img src="' . $src . '" alt="">';
						} else {
							$matches[] = $src;
						}
					}
				}

			}
		}
	} elseif ( 'img' != $output && hocwp_array_has_value( $matches ) ) {
		$tmp = array();
		foreach ( $matches as $img ) {
			$src   = hocwp_get_first_image_source( $img );
			$tmp[] = $src;
		}
		$matches = $tmp;
	}

	return $matches;
}

function hocwp_image_url_exists( $image_url ) {
	if ( ! @file_get_contents( $image_url ) ) {
		return false;
	}

	return true;
}

function hocwp_bool_to_int( $value ) {
	if ( $value ) {
		return 1;
	}

	return 0;
}

function hocwp_int_to_bool( $value ) {
	$value = absint( $value );
	if ( 0 < $value ) {
		return true;
	}

	return false;
}

function hocwp_bool_to_string( $value ) {
	if ( $value ) {
		return 'true';
	}

	return 'false';
}

function hocwp_string_to_bool( $string ) {
	$string = trim( $string );
	$string = strtolower( $string );
	if ( 'true' == $string || 'yes' == $string ) {
		return true;
	}

	return false;
}

function hocwp_is_rss_feed_url( $url ) {
	if ( false !== strpos( $url, '/feed' ) || false !== strpos( $url, '.rss' ) ) {
		return true;
	}

	return false;
}

function hocwp_remove_array_item_by_value( $value, $array ) {
	if ( ( $key = array_search( $value, $array ) ) !== false ) {
		unset( $array[ $key ] );
	}

	return $array;
}

function hocwp_find_valid_value_in_array( $arr, $key ) {
	$result = '';
	if ( is_array( $arr ) ) {
		if ( isset( $arr[ $key ] ) ) {
			$result = $arr[ $key ];
		} else {
			$index = absint( count( $arr ) / 2 );
			if ( isset( $arr[ $index ] ) ) {
				$result = $arr[ $index ];
			} else {
				$result = current( $arr );
			}
		}
	}

	return $result;
}

function hocwp_get_last_part_in_url( $url ) {
	return substr( parse_url( $url, PHP_URL_PATH ), 1 );
}

function hocwp_substr( $str, $len, $more = '...', $charset = 'UTF-8', $offset = 0 ) {
	if ( 1 > $len ) {
		return $str;
	}
	$more = esc_html( $more );
	$str  = html_entity_decode( $str, ENT_QUOTES, $charset );
	if ( function_exists( 'mb_strlen' ) ) {
		$length = mb_strlen( $str, $charset );
	} else {
		$length = strlen( $str );
	}
	if ( $length > $len ) {
		$arr = explode( ' ', $str );
		if ( function_exists( 'mb_substr' ) ) {
			$str = mb_substr( $str, $offset, $len, $charset );
		} else {
			$str = substr( $str, $offset, $len );
		}
		$arr_words = explode( ' ', $str );
		$index     = count( $arr_words ) - 1;
		$last      = $arr[ $index ];
		unset( $arr );
		if ( strcasecmp( $arr_words[ $index ], $last ) ) {
			unset( $arr_words[ $index ] );
		}

		return implode( ' ', $arr_words ) . $more;
	}

	return $str;
}

function hocwp_get_ip_address() {
	return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
}

function hocwp_image_base64( $file ) {
	$image_data = @file_get_contents( $file );

	return 'data:image/png;base64,' . base64_encode( $image_data );
}

function hocwp_print_r( $value ) {
	echo '<pre>';
	print_r( $value );
	echo '</pre>';
}

function hocwp_parse_xml( $args = array() ) {
	$result = null;
	if ( function_exists( 'simplexml_load_file' ) ) {
		if ( ! is_array( $args ) ) {
			$url = $args;
		} else {
			$url = hocwp_get_value_by_key( $args, 'url' );
		}
		if ( ! empty( $url ) ) {
			$result = simplexml_load_file( $url );
		}
	}

	return $result;
}

function hocwp_parse_vietcombank_exchange_rate( $url = '' ) {
	$result = null;
	if ( empty( $url ) ) {
		$url = 'https://www.vietcombank.com.vn/exchangerates/ExrateXML.aspx';
	}
	$transient_name = hocwp_build_transient_name( 'hocwp_exchange_rate_vietcombank_%s', '' );
	if ( false === ( $result = get_transient( $transient_name ) ) ) {
		$xml = hocwp_parse_xml( $url );
		if ( is_object( $xml ) ) {
			$updated = (array) $xml->DateTime;
			$data    = array(
				'datetime' => array_shift( $updated )
			);
			$exrates = $xml->Exrate;
			foreach ( $exrates as $rate ) {
				$currency_code                                                                     = (array) $rate['CurrencyCode'];
				$currency_code                                                                     = array_shift( $currency_code );
				$currency_name                                                                     = (array) $rate['CurrencyName'];
				$buy                                                                               = (array) $rate['Buy'];
				$sell                                                                              = (array) $rate['Sell'];
				$transfer                                                                          = (array) $rate['Transfer'];
				$data['exrate'][ hocwp_sanitize_id( hocwp_sanitize_file_name( $currency_code ) ) ] = array(
					'currency_code' => $currency_code,
					'currency_name' => array_shift( $currency_name ),
					'buy'           => array_shift( $buy ),
					'sell'          => array_shift( $sell ),
					'transfer'      => array_shift( $transfer )
				);
			}
			$result     = $data;
			$expiration = apply_filters( 'hocwp_vietcombank_exchange_rate_expiration', 30 * MINUTE_IN_SECONDS );
			set_transient( $transient_name, $result, $expiration );
		}
	}

	return $result;
}

function hocwp_parse_sjc_exchange_rate( $url = '' ) {
	$result = null;
	if ( empty( $url ) ) {
		$url = 'http://www.sjc.com.vn/xml/tygiavang.xml';
	}
	$transient_name = hocwp_build_transient_name( 'hocwp_exchange_rate_sjc_%s', '' );
	if ( false === ( $result = get_transient( $transient_name ) ) ) {
		$xml = hocwp_parse_xml( $url );
		if ( is_object( $xml ) ) {
			$updated = (array) $xml->ratelist['updated'];
			$unit    = (array) $xml->ratelist['unit'];
			$data    = array(
				'updated' => array_shift( $updated ),
				'unit'    => array_shift( $unit )
			);
			$cities  = $xml->ratelist->city;
			$lists   = array();
			foreach ( $cities as $city ) {
				$name   = (array) $city['name'];
				$name   = array_shift( $name );
				$items  = $city->item;
				$tmp    = array(
					'name' => $name
				);
				$childs = array();
				foreach ( $items as $item ) {
					$buy      = (array) $item['buy'];
					$sell     = (array) $item['sell'];
					$type     = (array) $item['type'];
					$childs[] = array(
						'buy'  => array_shift( $buy ),
						'sell' => array_shift( $sell ),
						'type' => array_shift( $type )
					);
				}
				$tmp['item']                                                     = $childs;
				$lists[ hocwp_sanitize_id( hocwp_sanitize_file_name( $name ) ) ] = $tmp;
			}
			$data['city'] = $lists;
			$result       = $data;
			$expiration   = apply_filters( 'hocwp_sjc_exchange_rate_expiration', HOUR_IN_SECONDS );
			set_transient( $transient_name, $result, $expiration );
		}
	}

	return $result;
}

function hocwp_sanitize_id( $id ) {
	if ( is_array( $id ) ) {
		$id = implode( '@', $id );
	}
	$id    = strtolower( $id );
	$id    = str_replace( '][', '_', $id );
	$chars = array(
		'-',
		' ',
		'[',
		']',
		'@',
		'.'
	);
	$id    = str_replace( $chars, '_', $id );
	$id    = trim( $id, '_' );

	return $id;
}

function hocwp_remove_vietnamese( $string ) {
	$characters = array(
		'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
		'd' => 'đ',
		'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
		'i' => 'í|ì|ỉ|ĩ|ị',
		'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
		'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
		'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
		'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
		'D' => 'Đ',
		'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
		'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
		'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
		'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
		'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
	);
	foreach ( $characters as $key => $value ) {
		$string = preg_replace( "/($value)/i", $key, $string );
	}

	return $string;
}

function hocwp_sanitize_file_name( $name ) {
	$name = hocwp_remove_vietnamese( $name );
	$name = strtolower( $name );
	$name = str_replace( '_', '-', $name );
	$name = str_replace( ' ', '-', $name );
	$name = sanitize_file_name( $name );

	return $name;
}

function hocwp_change_tag_attribute( $tag, $attr, $value ) {
	$tag = preg_replace( '/' . $attr . '="(.*?)"/i', $attr . '="' . $value . '"', $tag );

	return $tag;
}

function hocwp_add_html_attribute( $tag, $html, $attribute ) {
	$html = preg_replace( '^' . preg_quote( '<' . $tag . ' ' ) . '^', '<' . $tag . ' ' . $attribute . ' ', $html );

	return $html;
}

function hocwp_attribute_to_string( $atts ) {
	if ( is_array( $atts ) ) {
		$temp = array();
		foreach ( $atts as $key => $value ) {
			$att    = $key . '="' . $value . '"';
			$temp[] = $att;
		}
		$atts = implode( ' ', $temp );
	}
	if ( ! empty( $atts ) ) {
		$atts = trim( $atts );
	}

	return $atts;
}

function hocwp_add_class_to_string( $tag = '', $html, $class ) {
	$search = 'class="';
	if ( empty( $tag ) ) {
		$parts = explode( ' ', $html );
		$tag   = array_shift( $parts );
		$tag   = str_replace( '<', '', $tag );
	}
	if ( ! hocwp_string_contain( $html, $search ) ) {
		$class = 'class="' . $class . '"';
		$html  = hocwp_add_html_attribute( $tag, $html, $class );
	} else {
		$class = 'class="' . $class . ' ';
		$html  = str_replace( $search, $class, $html );
	}

	return $html;
}

function hocwp_add_more_class( $class, $add ) {
	if ( ! is_array( $class ) ) {
		$class = explode( ' ', $class );
	}
	$class = array_map( 'trim', $class );
	$add   = trim( $add );
	if ( ! hocwp_in_array( $class, $add ) ) {
		$class[] = $add;
	}
	$class = array_map( 'hocwp_sanitize_html_class', $class );
	$class = implode( ' ', $class );

	return $class;
}

function hocwp_percentage( $val1, $val2, $precision = 0 ) {
	$total = $val1;
	if ( $total < $val2 ) {
		$total = $val2;
	}
	if ( 0 == $total ) {
		return 0;
	}
	$discount = abs( $val1 - $val2 );
	$val1     = $discount / $total;
	$val1 *= 100;

	return round( $val1, $precision );
}

function hocwp_get_computer_info() {
	$result = array(
		'operating_system_name' => php_uname( 's' ),
		'computer_name'         => php_uname( 'n' ),
		'release_name'          => php_uname( 'r' ),
		'version_information'   => php_uname( 'v' ),
		'machine_type'          => php_uname( 'm' )
	);

	return $result;
}

function hocwp_get_web_server() {
	return htmlspecialchars( $_SERVER['SERVER_SOFTWARE'] );
}

function hocwp_get_peak_memory_usage() {
	return memory_get_peak_usage( true );
}

function hocwp_get_memory_usage() {
	return memory_get_usage( true );
}

function hocwp_get_memory_limit() {
	return ini_get( 'memory_limit' );
}

function hocwp_convert_datetime_format_to_jquery( $php_format ) {
	$matched_symbols = array(
		// Day
		'd' => 'dd',
		'D' => 'D',
		'j' => 'd',
		'l' => 'DD',
		'N' => '',
		'S' => '',
		'w' => '',
		'z' => 'o',
		// Week
		'W' => '',
		// Month
		'F' => 'MM',
		'm' => 'mm',
		'M' => 'M',
		'n' => 'm',
		't' => '',
		// Year
		'L' => '',
		'o' => '',
		'Y' => 'yy',
		'y' => 'y',
		// Time
		'a' => '',
		'A' => '',
		'B' => '',
		'g' => '',
		'G' => '',
		'h' => '',
		'H' => '',
		'i' => '',
		's' => '',
		'u' => ''
	);
	$result          = '';
	$escaping        = false;
	for ( $i = 0; $i < strlen( $php_format ); $i ++ ) {
		$char = $php_format[ $i ];
		if ( isset( $matched_symbols[ $char ] ) ) {
			$result .= $matched_symbols[ $char ];
		} else {
			$result .= $char;
		}
	}
	if ( $escaping ) {
		$result = esc_attr( $result );
	}

	return $result;
}

function hocwp_temperature_class( $temperature ) {
	$class = 'temperature';
	if ( is_numeric( $temperature ) ) {
		$class .= '-';
		$number = 1;
		if ( $temperature >= 0 && $temperature < 6 ) {
			$number += 0;
		} elseif ( $temperature >= 6 && $temperature < 12 ) {
			$number += 1;
		} elseif ( $temperature >= 12 && $temperature < 18 ) {
			$number += 2;
		} elseif ( $temperature >= 18 && $temperature < 24 ) {
			$number += 3;
		} elseif ( $temperature >= 24 && $temperature < 30 ) {
			$number += 4;
		} elseif ( $temperature >= 30 && $temperature < 36 ) {
			$number += 5;
		} elseif ( $temperature >= 36 && $temperature < 42 ) {
			$number += 6;
		} elseif ( $temperature >= 42 && $temperature < 48 ) {
			$number += 7;
		} elseif ( $temperature >= 48 && $temperature < 54 ) {
			$number += 8;
		} elseif ( $temperature >= 54 && $temperature < 60 ) {
			$number += 9;
		} elseif ( $temperature >= 60 && $temperature < 66 ) {
			$number += 10;
		} elseif ( $temperature >= 66 && $temperature < 72 ) {
			$number += 11;
		} elseif ( $temperature >= 72 && $temperature < 78 ) {
			$number += 12;
		} elseif ( $temperature >= 78 && $temperature < 84 ) {
			$number += 13;
		} elseif ( $temperature >= 84 && $temperature < 90 ) {
			$number += 14;
		} elseif ( $temperature >= 90 && $temperature < 96 ) {
			$number += 15;
		} elseif ( $temperature >= 96 && $temperature < 102 ) {
			$number += 16;
		} elseif ( $temperature >= 102 && $temperature < 108 ) {
			$number += 17;
		} elseif ( $temperature >= 108 && $temperature < 114 ) {
			$number += 18;
		} elseif ( $temperature >= 114 && $temperature < 120 ) {
			$number += 19;
		} elseif ( $temperature >= 120 && $temperature < 126 ) {
			$number += 20;
		} elseif ( $temperature >= 126 && $temperature < 132 ) {
			$number += 21;
		} elseif ( $temperature >= 132 && $temperature < 138 ) {
			$number += 22;
		} elseif ( $temperature >= 138 && $temperature < 144 ) {
			$number += 23;
		} elseif ( $temperature >= 144 ) {
			$number += 24;
		}
		$class .= $number;
	}

	return $class;
}

function hocwp_strtolower( $str, $charset = 'UTF-8' ) {
	return hocwp_lowercase( $str, $charset );
}

function hocwp_is_localhost( $site_url = '' ) {
	if ( empty( $site_url ) ) {
		$site_url = get_bloginfo( 'url' );
	}
	$domain      = hocwp_get_domain_name( $site_url );
	$root_domain = hocwp_get_domain_name_only( $domain );
	if ( empty( $root_domain ) ) {
		$root_domain = $domain;
	}
	$result = false;
	$last   = substr( $domain, - 3 );
	if ( 'localhost' == $root_domain || hocwp_is_ip( $root_domain ) || 'dev' == $last ) {
		$result = true;
	}

	return apply_filters( 'hocwp_is_localhost', $result );
}

function hocwp_build_css_rule( $elements, $properties ) {
	$elements   = hocwp_to_array( $elements );
	$properties = hocwp_to_array( $properties );
	$before     = '';
	foreach ( $elements as $element ) {
		if ( empty( $element ) ) {
			continue;
		}
		$first_char = hocwp_get_first_char( $element );
		if ( '.' !== $first_char && strpos( $element, '.' ) === false ) {
			$element = '.' . $element;
		}
		$before .= $element . ',';
	}
	$before = trim( $before, ',' );
	$after  = '';
	foreach ( $properties as $key => $property ) {
		if ( empty( $key ) ) {
			continue;
		}
		$after .= $key . ':' . $property . ';';
	}
	$after = trim( $after, ';' );

	return $before . '{' . $after . '}';
}

function hocwp_shorten_hex_css( $content ) {
	$content = preg_replace( '/(?<![\'"])#([0-9a-z])\\1([0-9a-z])\\2([0-9a-z])\\3(?![\'"])/i', '#$1$2$3', $content );

	return $content;
}

function hocwp_shorten_zero_css( $content ) {
	$before  = '(?<=[:(, ])';
	$after   = '(?=[ ,);}])';
	$units   = '(em|ex|%|px|cm|mm|in|pt|pc|ch|rem|vh|vw|vmin|vmax|vm)';
	$content = preg_replace( '/' . $before . '(-?0*(\.0+)?)(?<=0)' . $units . $after . '/', '\\1', $content );
	$content = preg_replace( '/' . $before . '\.0+' . $after . '/', '0', $content );
	$content = preg_replace( '/' . $before . '(-?[0-9]+)\.0+' . $units . '?' . $after . '/', '\\1\\2', $content );
	$content = preg_replace( '/' . $before . '-?0+' . $after . '/', '0', $content );

	return $content;
}

function hocwp_strip_white_space_css( $content ) {
	$content = preg_replace( '/^\s*/m', '', $content );
	$content = preg_replace( '/\s*$/m', '', $content );
	$content = preg_replace( '/\s+/', ' ', $content );
	$content = preg_replace( '/\s*([\*$~^|]?+=|[{};,>~]|!important\b)\s*/', '$1', $content );
	$content = preg_replace( '/([\[(:])\s+/', '$1', $content );
	$content = preg_replace( '/\s+([\]\)])/', '$1', $content );
	$content = preg_replace( '/\s+(:)(?![^\}]*\{)/', '$1', $content );
	$content = preg_replace( '/\s*([+-])\s*(?=[^}]*{)/', '$1', $content );
	$content = preg_replace( '/;}/', '}', $content );

	return trim( $content );
}

function hocwp_minify_css( $css_content, $online = false ) {
	if ( $online ) {
		$buffer = hocwp_get_minified( 'https://cssminifier.com/raw', $css_content );
	} else {
		if ( file_exists( $css_content ) ) {
			$css_content = @file_get_contents( $css_content );
		}
		$buffer = $css_content;
		$buffer = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer );
		$buffer = str_replace( ': ', ':', $buffer );
		$buffer = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $buffer );
		$buffer = hocwp_shorten_hex_css( $buffer );
		$buffer = hocwp_shorten_zero_css( $buffer );
		$buffer = hocwp_strip_white_space_css( $buffer );
	}

	return $buffer;
}

function hocwp_minify_js( $js ) {
	return hocwp_get_minified( 'https://javascript-minifier.com/raw', $js );
}

function hocwp_get_minified( $url, $content ) {
	if ( file_exists( $content ) ) {
		$content = @file_get_contents( $content );
	}
	$postdata = array(
		'http' => array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/x-www-form-urlencoded',
			'content' => http_build_query(
				array(
					'input' => $content
				)
			)
		)
	);

	return @file_get_contents( $url, false, stream_context_create( $postdata ) );
}

function hocwp_get_max_number( $numbers = array() ) {
	if ( is_numeric( $numbers ) ) {
		return $numbers;
	}
	if ( ! is_array( $numbers ) ) {
		return 0;
	}
	$max = array_shift( $numbers );
	foreach ( $numbers as $number ) {
		if ( $number > $max ) {
			$max = $number;
		}
	}

	return $max;
}

function hocwp_get_first_divisible_of_divisor( $dividend, $divisor ) {
	$result = null;
	if ( is_numeric( $dividend ) && is_numeric( $divisor ) && $divisor !== 0 ) {
		$quotient = (int) ( $dividend / $divisor );
		$result   = $quotient * $divisor;
	}

	return $result;
}

function hocwp_wrap_class( $classes = array() ) {
	$classes   = hocwp_to_array( $classes );
	$classes   = apply_filters( 'hocwp_wrap_class', $classes );
	$classes[] = 'wrap';
	$classes[] = 'container';
	$classes[] = 'wrapper';
	$class     = implode( ' ', $classes );
	echo $class;
}

function hocwp_div_clear() {
	echo '<div class="clear"></div>';
}

function hocwp_change_image_source( $img, $src ) {
	$doc = new DOMDocument();
	$doc->loadHTML( $img );
	$tags = $doc->getElementsByTagName( 'img' );
	foreach ( $tags as $tag ) {
		$tag->setAttribute( 'src', $src );
	}

	return $doc->saveHTML();
}

function hocwp_get_tag_source( $tag_name, $html ) {
	return hocwp_get_tag_attr( $tag_name, 'src', $html );
}

function hocwp_get_tag_attr( $tag_name, $attr, $html ) {
	$doc = new DOMDocument();
	$doc->loadHTML( $html );
	$tags = $doc->getElementsByTagName( $tag_name );
	foreach ( $tags as $tag ) {
		return $tag->getAttribute( $attr );
	}

	return '';
}

function hocwp_get_first_image_source( $content ) {
	$doc = new DOMDocument();
	@$doc->loadHTML( $content );
	$xpath = new DOMXPath( $doc );
	$src   = $xpath->evaluate( 'string(//img/@src)' );

	return $src;
}

function hocwp_get_current_day_of_week( $full = true ) {
	$format = 'l';
	if ( ! $full ) {
		$format = 'D';
	}

	return date( $format );
}

function hocwp_convert_day_name_to_vietnamese( $day_name ) {
	$weekday = $day_name;
	switch ( $weekday ) {
		case 'Mon':
		case 'Monday':
			$weekday = 'Thứ hai';
			break;
		case 'Tue':
		case 'Tuesday':
			$weekday = 'Thứ ba';
			break;
		case 'Wed':
		case 'Wednesday':
			$weekday = 'Thứ tư';
			break;
		case 'Thur':
		case 'Thursday':
			$weekday = 'Thứ năm';
			break;
		case 'Fri':
		case 'Friday':
			$weekday = 'Thứ sáu';
			break;
		case 'Sat':
		case 'Saturday':
			$weekday = 'Thứ bảy';
			break;
		case 'Sun':
		case 'Sunday':
			$weekday = 'Chủ nhật';
			break;
	}

	return $weekday;
}

function hocwp_get_current_month_of_year( $full = true ) {
	$format = 'F';
	if ( ! $full ) {
		$format = 'M';
	}

	return date( $format );
}

function hocwp_convert_month_name_to_vietnamese( $month_full_name ) {
	switch ( $month_full_name ) {
		case 'Jan':
		case 'January':
			$month_full_name = 'Tháng một';
			break;
		case 'Feb':
		case 'February':
			$month_full_name = 'Tháng hai';
			break;
		case 'Mar';
		case 'March':
			$month_full_name = 'Tháng ba';
			break;
		case 'Apr':
		case 'April':
			$month_full_name = 'Tháng tư';
			break;
		case 'May':
			$month_full_name = 'Tháng năm';
			break;
		case 'Jun':
		case 'June':
			$month_full_name = 'Tháng sáu';
			break;
		case 'Jul':
		case 'July':
			$month_full_name = 'Tháng bảy';
			break;
		case 'Aug':
		case 'August':
			$month_full_name = 'Tháng tám';
			break;
		case 'Sep':
		case 'September':
			$month_full_name = 'Tháng chín';
			break;
		case 'Oct':
		case 'October':
			$month_full_name = 'Tháng mười';
			break;
		case 'Nov':
		case 'November':
			$month_full_name = 'Tháng mười một';
			break;
		case 'Dec':
		case 'December':
			$month_full_name = 'Tháng mười hai';
			break;
	}

	return $month_full_name;
}

function hocwp_is_phone_number( $number ) {
	$regex  = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i";
	$result = ( preg_match( $regex, $number ) ) ? true : false;
	if ( $result ) {
		$len = strlen( $number );
		if ( $len < 7 || $len > 20 ) {
			$result = false;
		}
	}

	return $result;
}

function hocwp_callback_exists( $callback ) {
	if ( empty( $callback ) || ( ! is_array( $callback ) && ! function_exists( $callback ) ) || ( is_array( $callback ) && count( $callback ) != 2 ) || ( is_array( $callback ) && ! method_exists( $callback[0], $callback[1] ) ) ) {
		return false;
	}
	if ( ! is_callable( $callback ) ) {
		return false;
	}

	return true;
}

function hocwp_add_unique_string( &$string, $add, $tail = true ) {
	if ( empty( $string ) ) {
		$string = $add;
	} elseif ( ! hocwp_string_contain( $string, $add ) ) {
		if ( $tail ) {
			$string .= $add;
		} else {
			$string = $add . $string;
		}
	}
	$string = trim( $string );

	return $string;
}

function hocwp_add_string_with_space_before( &$string, $add ) {
	return hocwp_add_string_with_string_before( $string, $add, ' ' );
}

function hocwp_add_string_with_string_before( &$string, $add_string, $string_before ) {
	$add    = $string_before . $add_string;
	$string = trim( hocwp_add_unique_string( $string, $add ) );

	return $string;
}

function hocwp_color_hex_to_rgb( $color, $opacity = false ) {
	$default = 'rgb(0,0,0)';
	if ( empty( $color ) ) {
		return $default;
	}
	if ( $color[0] == '#' ) {
		$color = substr( $color, 1 );
	}
	if ( strlen( $color ) == 6 ) {
		$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	} elseif ( strlen( $color ) == 3 ) {
		$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	} else {
		return $default;
	}
	$rgb = array_map( 'hexdec', $hex );
	if ( $opacity ) {
		if ( abs( $opacity ) > 1 ) {
			$opacity = 1.0;
		}
		$output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
	} else {
		$output = 'rgb(' . implode( ',', $rgb ) . ')';
	}

	return $output;
}

function hocwp_facebook_page_plugin( $args = array() ) {
	$href = hocwp_get_value_by_key( $args, 'href', hocwp_get_value_by_key( $args, 'url' ) );
	if ( empty( $href ) ) {
		$page_id = isset( $args['page_id'] ) ? $args['page_id'] : 'hocwpnet';
		if ( ! empty( $page_id ) ) {
			$href = 'https://www.facebook.com/' . $page_id;
		}
	}
	if ( ! hocwp_is_url( $href ) ) {
		$href = 'https://www.facebook.com/' . $href;
	}
	if ( empty( $href ) ) {
		return;
	}
	$page_name     = isset( $args['page_name'] ) ? $args['page_name'] : '';
	$width         = isset( $args['width'] ) ? $args['width'] : 340;
	$height        = isset( $args['height'] ) ? $args['height'] : 500;
	$hide_cover    = (bool) ( isset( $args['hide_cover'] ) ? $args['hide_cover'] : false );
	$hide_cover    = hocwp_bool_to_string( $hide_cover );
	$show_facepile = (bool) ( isset( $args['show_facepile'] ) ? $args['show_facepile'] : true );
	$show_facepile = hocwp_bool_to_string( $show_facepile );
	$show_posts    = (bool) ( isset( $args['show_posts'] ) ? $args['show_posts'] : false );
	$tabs          = hocwp_get_value_by_key( $args, 'tabs' );
	if ( ! is_array( $tabs ) ) {
		$tabs = explode( ',', $tabs );
	}
	$tabs = array_map( 'trim', $tabs );
	if ( $show_posts && ! hocwp_in_array( 'timeline', $tabs ) ) {
		$tabs[] = 'timeline';
	}
	$show_posts            = hocwp_bool_to_string( $show_posts );
	$hide_cta              = (bool) ( isset( $args['hide_cta'] ) ? $args['hide_cta'] : false );
	$hide_cta              = hocwp_bool_to_string( $hide_cta );
	$small_header          = (bool) ( isset( $args['small_header'] ) ? $args['small_header'] : false );
	$small_header          = hocwp_bool_to_string( $small_header );
	$adapt_container_width = (bool) ( isset( $args['adapt_container_width'] ) ? $args['adapt_container_width'] : true );
	$adapt_container_width = hocwp_bool_to_string( $adapt_container_width );
	?>
	<div class="fb-page" data-href="<?php echo $href; ?>" data-width="<?php echo $width; ?>"
	     data-height="<?php echo $height; ?>" data-hide-cta="<?php echo $hide_cta; ?>"
	     data-small-header="<?php echo $small_header; ?>"
	     data-adapt-container-width="<?php echo $adapt_container_width; ?>" data-hide-cover="<?php echo $hide_cover; ?>"
	     data-show-facepile="<?php echo $show_facepile; ?>" data-show-posts="<?php echo $show_posts; ?>"
	     data-tabs="<?php echo implode( ',', $tabs ) ?>">
		<div class="fb-xfbml-parse-ignore">
			<?php if ( ! empty( $page_name ) ) : ?>
				<blockquote cite="<?php echo $href; ?>">
					<a href="<?php echo $href; ?>"><?php echo $page_name; ?></a>
				</blockquote>
			<?php endif; ?>
		</div>
	</div>
	<?php
}