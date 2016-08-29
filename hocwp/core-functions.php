<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

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
		$result = mb_substr( $string, 0, 1, $encoding );
	}

	return $result;
}

function hocwp_remove_first_char( $string, $char ) {
	$string = ltrim( $string, $char );

	return $string;
}

function hocwp_get_last_char( $string, $encoding = 'UTF-8' ) {
	$result = '';
	if ( ! empty( $string ) ) {
		$result = mb_substr( $string, - 1, 1, $encoding );
	}

	return $result;
}

function hocwp_remove_last_char( $string, $char ) {
	$string = rtrim( $string, $char );

	return $string;
}

function hocwp_remove_first_char_and_last_char( $string, $char ) {
	$string = trim( $string, $char );

	return $string;
}

function hocwp_uppercase( $string, $encoding = 'utf-8' ) {
	return mb_strtoupper( $string, $encoding );
}

function hocwp_uppercase_first_char( $string, $encoding = 'utf-8' ) {
	$first_char = hocwp_get_first_char( $string, $encoding );
	$len        = mb_strlen( $string, $encoding );
	$then       = mb_substr( $string, 1, $len - 1, $encoding );
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
	return mb_strtolower( $string, $encoding );
}

function hocwp_string_contain( $string, $needle ) {
	if ( false !== mb_strpos( $string, $needle, null, 'UTF-8' ) ) {
		return true;
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

function hocwp_get_countries() {
	$countries = array(
		'AF' => array( 'name' => 'Afghanistan', 'nativetongue' => '‫افغانستان' ),
		'AX' => array( 'name' => 'Åland Islands', 'nativetongue' => 'Åland' ),
		'AL' => array( 'name' => 'Albania', 'nativetongue' => 'Shqipëri' ),
		'DZ' => array( 'name' => 'Algeria', 'nativetongue' => '‫الجزائر' ),
		'AS' => array( 'name' => 'American Samoa', 'nativetongue' => '' ),
		'AD' => array( 'name' => 'Andorra', 'nativetongue' => '' ),
		'AO' => array( 'name' => 'Angola', 'nativetongue' => '' ),
		'AI' => array( 'name' => 'Anguilla', 'nativetongue' => '' ),
		'AQ' => array( 'name' => 'Antarctica', 'nativetongue' => '' ),
		'AG' => array( 'name' => 'Antigua and Barbuda', 'nativetongue' => '' ),
		'AR' => array( 'name' => 'Argentina', 'nativetongue' => '' ),
		'AM' => array( 'name' => 'Armenia', 'nativetongue' => 'Հայաստան' ),
		'AW' => array( 'name' => 'Aruba', 'nativetongue' => '' ),
		'AC' => array( 'name' => 'Ascension Island', 'nativetongue' => '' ),
		'AU' => array( 'name' => 'Australia', 'nativetongue' => '' ),
		'AT' => array( 'name' => 'Austria', 'nativetongue' => 'Österreich' ),
		'AZ' => array( 'name' => 'Azerbaijan', 'nativetongue' => 'Azərbaycan' ),
		'BS' => array( 'name' => 'Bahamas', 'nativetongue' => '' ),
		'BH' => array( 'name' => 'Bahrain', 'nativetongue' => '‫البحرين' ),
		'BD' => array( 'name' => 'Bangladesh', 'nativetongue' => 'বাংলাদেশ' ),
		'BB' => array( 'name' => 'Barbados', 'nativetongue' => '' ),
		'BY' => array( 'name' => 'Belarus', 'nativetongue' => 'Беларусь' ),
		'BE' => array( 'name' => 'Belgium', 'nativetongue' => 'België' ),
		'BZ' => array( 'name' => 'Belize', 'nativetongue' => '' ),
		'BJ' => array( 'name' => 'Benin', 'nativetongue' => 'Bénin' ),
		'BM' => array( 'name' => 'Bermuda', 'nativetongue' => '' ),
		'BT' => array( 'name' => 'Bhutan', 'nativetongue' => 'འབྲུག' ),
		'BO' => array( 'name' => 'Bolivia', 'nativetongue' => '' ),
		'BA' => array( 'name' => 'Bosnia and Herzegovina', 'nativetongue' => 'Босна и Херцеговина' ),
		'BW' => array( 'name' => 'Botswana', 'nativetongue' => '' ),
		'BV' => array( 'name' => 'Bouvet Island', 'nativetongue' => '' ),
		'BR' => array( 'name' => 'Brazil', 'nativetongue' => 'Brasil' ),
		'IO' => array( 'name' => 'British Indian Ocean Territory', 'nativetongue' => '' ),
		'VG' => array( 'name' => 'British Virgin Islands', 'nativetongue' => '' ),
		'BN' => array( 'name' => 'Brunei', 'nativetongue' => '' ),
		'BG' => array( 'name' => 'Bulgaria', 'nativetongue' => 'България' ),
		'BF' => array( 'name' => 'Burkina Faso', 'nativetongue' => '' ),
		'BI' => array( 'name' => 'Burundi', 'nativetongue' => 'Uburundi' ),
		'KH' => array( 'name' => 'Cambodia', 'nativetongue' => 'កម្ពុជា' ),
		'CM' => array( 'name' => 'Cameroon', 'nativetongue' => 'Cameroun' ),
		'CA' => array( 'name' => 'Canada', 'nativetongue' => '' ),
		'IC' => array( 'name' => 'Canary Islands', 'nativetongue' => 'islas Canarias' ),
		'CV' => array( 'name' => 'Cape Verde', 'nativetongue' => 'Kabu Verdi' ),
		'BQ' => array( 'name' => 'Caribbean Netherlands', 'nativetongue' => '' ),
		'KY' => array( 'name' => 'Cayman Islands', 'nativetongue' => '' ),
		'CF' => array( 'name' => 'Central African Republic', 'nativetongue' => 'République centrafricaine' ),
		'EA' => array( 'name' => 'Ceuta and Melilla', 'nativetongue' => 'Ceuta y Melilla' ),
		'TD' => array( 'name' => 'Chad', 'nativetongue' => 'Tchad' ),
		'CL' => array( 'name' => 'Chile', 'nativetongue' => '' ),
		'CN' => array( 'name' => 'China', 'nativetongue' => '中国' ),
		'CX' => array( 'name' => 'Christmas Island', 'nativetongue' => '' ),
		'CP' => array( 'name' => 'Clipperton Island', 'nativetongue' => '' ),
		'CC' => array( 'name' => 'Cocos (Keeling) Islands', 'nativetongue' => 'Kepulauan Cocos (Keeling)' ),
		'CO' => array( 'name' => 'Colombia', 'nativetongue' => '' ),
		'KM' => array( 'name' => 'Comoros', 'nativetongue' => '‫جزر القمر' ),
		'CD' => array( 'name' => 'Congo (DRC)', 'nativetongue' => 'Jamhuri ya Kidemokrasia ya Kongo' ),
		'CG' => array( 'name' => 'Congo (Republic)', 'nativetongue' => 'Congo-Brazzaville' ),
		'CK' => array( 'name' => 'Cook Islands', 'nativetongue' => '' ),
		'CR' => array( 'name' => 'Costa Rica', 'nativetongue' => '' ),
		'CI' => array( 'name' => 'Côte d’Ivoire', 'nativetongue' => '' ),
		'HR' => array( 'name' => 'Croatia', 'nativetongue' => 'Hrvatska' ),
		'CU' => array( 'name' => 'Cuba', 'nativetongue' => '' ),
		'CW' => array( 'name' => 'Curaçao', 'nativetongue' => '' ),
		'CY' => array( 'name' => 'Cyprus', 'nativetongue' => 'Κύπρος' ),
		'CZ' => array( 'name' => 'Czech Republic', 'nativetongue' => 'Česká republika' ),
		'DK' => array( 'name' => 'Denmark', 'nativetongue' => 'Danmark' ),
		'DG' => array( 'name' => 'Diego Garcia', 'nativetongue' => '' ),
		'DJ' => array( 'name' => 'Djibouti', 'nativetongue' => '' ),
		'DM' => array( 'name' => 'Dominica', 'nativetongue' => '' ),
		'DO' => array( 'name' => 'Dominican Republic', 'nativetongue' => 'República Dominicana' ),
		'EC' => array( 'name' => 'Ecuador', 'nativetongue' => '' ),
		'EG' => array( 'name' => 'Egypt', 'nativetongue' => '‫مصر' ),
		'SV' => array( 'name' => 'El Salvador', 'nativetongue' => '' ),
		'GQ' => array( 'name' => 'Equatorial Guinea', 'nativetongue' => 'Guinea Ecuatorial' ),
		'ER' => array( 'name' => 'Eritrea', 'nativetongue' => '' ),
		'EE' => array( 'name' => 'Estonia', 'nativetongue' => 'Eesti' ),
		'ET' => array( 'name' => 'Ethiopia', 'nativetongue' => '' ),
		'FK' => array( 'name' => 'Falkland Islands', 'nativetongue' => 'Islas Malvinas' ),
		'FO' => array( 'name' => 'Faroe Islands', 'nativetongue' => 'Føroyar' ),
		'FJ' => array( 'name' => 'Fiji', 'nativetongue' => '' ),
		'FI' => array( 'name' => 'Finland', 'nativetongue' => 'Suomi' ),
		'FR' => array( 'name' => 'France', 'nativetongue' => '' ),
		'GF' => array( 'name' => 'French Guiana', 'nativetongue' => 'Guyane française' ),
		'PF' => array( 'name' => 'French Polynesia', 'nativetongue' => 'Polynésie française' ),
		'TF' => array( 'name' => 'French Southern Territories', 'nativetongue' => 'Terres australes françaises' ),
		'GA' => array( 'name' => 'Gabon', 'nativetongue' => '' ),
		'GM' => array( 'name' => 'Gambia', 'nativetongue' => '' ),
		'GE' => array( 'name' => 'Georgia', 'nativetongue' => 'საქართველო' ),
		'DE' => array( 'name' => 'Germany', 'nativetongue' => 'Deutschland' ),
		'GH' => array( 'name' => 'Ghana', 'nativetongue' => 'Gaana' ),
		'GI' => array( 'name' => 'Gibraltar', 'nativetongue' => '' ),
		'GR' => array( 'name' => 'Greece', 'nativetongue' => 'Ελλάδα' ),
		'GL' => array( 'name' => 'Greenland', 'nativetongue' => 'Kalaallit Nunaat' ),
		'GD' => array( 'name' => 'Grenada', 'nativetongue' => '' ),
		'GP' => array( 'name' => 'Guadeloupe', 'nativetongue' => '' ),
		'GU' => array( 'name' => 'Guam', 'nativetongue' => '' ),
		'GT' => array( 'name' => 'Guatemala', 'nativetongue' => '' ),
		'GG' => array( 'name' => 'Guernsey', 'nativetongue' => '' ),
		'GN' => array( 'name' => 'Guinea', 'nativetongue' => 'Guinée' ),
		'GW' => array( 'name' => 'Guinea-Bissau', 'nativetongue' => 'Guiné Bissau' ),
		'GY' => array( 'name' => 'Guyana', 'nativetongue' => '' ),
		'HT' => array( 'name' => 'Haiti', 'nativetongue' => '' ),
		'HM' => array( 'name' => 'Heard & McDonald Islands', 'nativetongue' => '' ),
		'HN' => array( 'name' => 'Honduras', 'nativetongue' => '' ),
		'HK' => array( 'name' => 'Hong Kong', 'nativetongue' => '香港' ),
		'HU' => array( 'name' => 'Hungary', 'nativetongue' => 'Magyarország' ),
		'IS' => array( 'name' => 'Iceland', 'nativetongue' => 'Ísland' ),
		'IN' => array( 'name' => 'India', 'nativetongue' => 'भारत' ),
		'ID' => array( 'name' => 'Indonesia', 'nativetongue' => '' ),
		'IR' => array( 'name' => 'Iran', 'nativetongue' => '‫ایران' ),
		'IQ' => array( 'name' => 'Iraq', 'nativetongue' => '‫العراق' ),
		'IE' => array( 'name' => 'Ireland', 'nativetongue' => '' ),
		'IM' => array( 'name' => 'Isle of Man', 'nativetongue' => '' ),
		'IL' => array( 'name' => 'Israel', 'nativetongue' => '‫ישראל' ),
		'IT' => array( 'name' => 'Italy', 'nativetongue' => 'Italia' ),
		'JM' => array( 'name' => 'Jamaica', 'nativetongue' => '' ),
		'JP' => array( 'name' => 'Japan', 'nativetongue' => '日本' ),
		'JE' => array( 'name' => 'Jersey', 'nativetongue' => '' ),
		'JO' => array( 'name' => 'Jordan', 'nativetongue' => '‫الأردن' ),
		'KZ' => array( 'name' => 'Kazakhstan', 'nativetongue' => 'Казахстан' ),
		'KE' => array( 'name' => 'Kenya', 'nativetongue' => '' ),
		'KI' => array( 'name' => 'Kiribati', 'nativetongue' => '' ),
		'XK' => array( 'name' => 'Kosovo', 'nativetongue' => 'Kosovë' ),
		'KW' => array( 'name' => 'Kuwait', 'nativetongue' => '‫الكويت' ),
		'KG' => array( 'name' => 'Kyrgyzstan', 'nativetongue' => 'Кыргызстан' ),
		'LA' => array( 'name' => 'Laos', 'nativetongue' => 'ລາວ' ),
		'LV' => array( 'name' => 'Latvia', 'nativetongue' => 'Latvija' ),
		'LB' => array( 'name' => 'Lebanon', 'nativetongue' => '‫لبنان' ),
		'LS' => array( 'name' => 'Lesotho', 'nativetongue' => '' ),
		'LR' => array( 'name' => 'Liberia', 'nativetongue' => '' ),
		'LY' => array( 'name' => 'Libya', 'nativetongue' => '‫ليبيا' ),
		'LI' => array( 'name' => 'Liechtenstein', 'nativetongue' => '' ),
		'LT' => array( 'name' => 'Lithuania', 'nativetongue' => 'Lietuva' ),
		'LU' => array( 'name' => 'Luxembourg', 'nativetongue' => '' ),
		'MO' => array( 'name' => 'Macau', 'nativetongue' => '澳門' ),
		'MK' => array( 'name' => 'Macedonia (FYROM)', 'nativetongue' => 'Македонија' ),
		'MG' => array( 'name' => 'Madagascar', 'nativetongue' => 'Madagasikara' ),
		'MW' => array( 'name' => 'Malawi', 'nativetongue' => '' ),
		'MY' => array( 'name' => 'Malaysia', 'nativetongue' => '' ),
		'MV' => array( 'name' => 'Maldives', 'nativetongue' => '' ),
		'ML' => array( 'name' => 'Mali', 'nativetongue' => '' ),
		'MT' => array( 'name' => 'Malta', 'nativetongue' => '' ),
		'MH' => array( 'name' => 'Marshall Islands', 'nativetongue' => '' ),
		'MQ' => array( 'name' => 'Martinique', 'nativetongue' => '' ),
		'MR' => array( 'name' => 'Mauritania', 'nativetongue' => '‫موريتانيا' ),
		'MU' => array( 'name' => 'Mauritius', 'nativetongue' => 'Moris' ),
		'YT' => array( 'name' => 'Mayotte', 'nativetongue' => '' ),
		'MX' => array( 'name' => 'Mexico', 'nativetongue' => '' ),
		'FM' => array( 'name' => 'Micronesia', 'nativetongue' => '' ),
		'MD' => array( 'name' => 'Moldova', 'nativetongue' => 'Republica Moldova' ),
		'MC' => array( 'name' => 'Monaco', 'nativetongue' => '' ),
		'MN' => array( 'name' => 'Mongolia', 'nativetongue' => 'Монгол' ),
		'ME' => array( 'name' => 'Montenegro', 'nativetongue' => 'Crna Gora' ),
		'MS' => array( 'name' => 'Montserrat', 'nativetongue' => '' ),
		'MA' => array( 'name' => 'Morocco', 'nativetongue' => '‫المغرب' ),
		'MZ' => array( 'name' => 'Mozambique', 'nativetongue' => 'Moçambique' ),
		'MM' => array( 'name' => 'Myanmar (Burma)', 'nativetongue' => 'မြန်မာ' ),
		'NA' => array( 'name' => 'Namibia', 'nativetongue' => 'Namibië' ),
		'NR' => array( 'name' => 'Nauru', 'nativetongue' => '' ),
		'NP' => array( 'name' => 'Nepal', 'nativetongue' => 'नेपाल' ),
		'NL' => array( 'name' => 'Netherlands', 'nativetongue' => 'Nederland' ),
		'NC' => array( 'name' => 'New Caledonia', 'nativetongue' => 'Nouvelle-Calédonie' ),
		'NZ' => array( 'name' => 'New Zealand', 'nativetongue' => '' ),
		'NI' => array( 'name' => 'Nicaragua', 'nativetongue' => '' ),
		'NE' => array( 'name' => 'Niger', 'nativetongue' => 'Nijar' ),
		'NG' => array( 'name' => 'Nigeria', 'nativetongue' => '' ),
		'NU' => array( 'name' => 'Niue', 'nativetongue' => '' ),
		'NF' => array( 'name' => 'Norfolk Island', 'nativetongue' => '' ),
		'MP' => array( 'name' => 'Northern Mariana Islands', 'nativetongue' => '' ),
		'KP' => array( 'name' => 'North Korea', 'nativetongue' => '조선 민주주의 인민 공화국' ),
		'NO' => array( 'name' => 'Norway', 'nativetongue' => 'Norge' ),
		'OM' => array( 'name' => 'Oman', 'nativetongue' => '‫عُمان' ),
		'PK' => array( 'name' => 'Pakistan', 'nativetongue' => '‫پاکستان' ),
		'PW' => array( 'name' => 'Palau', 'nativetongue' => '' ),
		'PS' => array( 'name' => 'Palestine', 'nativetongue' => '‫فلسطين' ),
		'PA' => array( 'name' => 'Panama', 'nativetongue' => '' ),
		'PG' => array( 'name' => 'Papua New Guinea', 'nativetongue' => '' ),
		'PY' => array( 'name' => 'Paraguay', 'nativetongue' => '' ),
		'PE' => array( 'name' => 'Peru', 'nativetongue' => 'Perú' ),
		'PH' => array( 'name' => 'Philippines', 'nativetongue' => '' ),
		'PN' => array( 'name' => 'Pitcairn Islands', 'nativetongue' => '' ),
		'PL' => array( 'name' => 'Poland', 'nativetongue' => 'Polska' ),
		'PT' => array( 'name' => 'Portugal', 'nativetongue' => '' ),
		'PR' => array( 'name' => 'Puerto Rico', 'nativetongue' => '' ),
		'QA' => array( 'name' => 'Qatar', 'nativetongue' => '‫قطر' ),
		'RE' => array( 'name' => 'Réunion', 'nativetongue' => 'La Réunion' ),
		'RO' => array( 'name' => 'Romania', 'nativetongue' => 'România' ),
		'RU' => array( 'name' => 'Russia', 'nativetongue' => 'Россия' ),
		'RW' => array( 'name' => 'Rwanda', 'nativetongue' => '' ),
		'BL' => array( 'name' => 'Saint Barthélemy', 'nativetongue' => 'Saint-Barthélemy' ),
		'SH' => array( 'name' => 'Saint Helena', 'nativetongue' => '' ),
		'KN' => array( 'name' => 'Saint Kitts and Nevis', 'nativetongue' => '' ),
		'LC' => array( 'name' => 'Saint Lucia', 'nativetongue' => '' ),
		'MF' => array( 'name' => 'Saint Martin', 'nativetongue' => '' ),
		'PM' => array( 'name' => 'Saint Pierre and Miquelon', 'nativetongue' => 'Saint-Pierre-et-Miquelon' ),
		'WS' => array( 'name' => 'Samoa', 'nativetongue' => '' ),
		'SM' => array( 'name' => 'San Marino', 'nativetongue' => '' ),
		'ST' => array( 'name' => 'São Tomé and Príncipe', 'nativetongue' => 'São Tomé e Príncipe' ),
		'SA' => array( 'name' => 'Saudi Arabia', 'nativetongue' => '‫المملكة العربية السعودية' ),
		'SN' => array( 'name' => 'Senegal', 'nativetongue' => 'Sénégal' ),
		'RS' => array( 'name' => 'Serbia', 'nativetongue' => 'Србија' ),
		'SC' => array( 'name' => 'Seychelles', 'nativetongue' => '' ),
		'SL' => array( 'name' => 'Sierra Leone', 'nativetongue' => '' ),
		'SG' => array( 'name' => 'Singapore', 'nativetongue' => '' ),
		'SX' => array( 'name' => 'Sint Maarten', 'nativetongue' => '' ),
		'SK' => array( 'name' => 'Slovakia', 'nativetongue' => 'Slovensko' ),
		'SI' => array( 'name' => 'Slovenia', 'nativetongue' => 'Slovenija' ),
		'SB' => array( 'name' => 'Solomon Islands', 'nativetongue' => '' ),
		'SO' => array( 'name' => 'Somalia', 'nativetongue' => 'Soomaaliya' ),
		'ZA' => array( 'name' => 'South Africa', 'nativetongue' => '' ),
		'GS' => array( 'name' => 'South Georgia & South Sandwich Islands', 'nativetongue' => '' ),
		'KR' => array( 'name' => 'South Korea', 'nativetongue' => '대한민국' ),
		'SS' => array( 'name' => 'South Sudan', 'nativetongue' => '‫جنوب السودان' ),
		'ES' => array( 'name' => 'Spain', 'nativetongue' => 'España' ),
		'LK' => array( 'name' => 'Sri Lanka', 'nativetongue' => 'ශ්‍රී ලංකාව' ),
		'VC' => array( 'name' => 'St. Vincent & Grenadines', 'nativetongue' => '' ),
		'SD' => array( 'name' => 'Sudan', 'nativetongue' => '‫السودان' ),
		'SR' => array( 'name' => 'Suriname', 'nativetongue' => '' ),
		'SJ' => array( 'name' => 'Svalbard and Jan Mayen', 'nativetongue' => 'Svalbard og Jan Mayen' ),
		'SZ' => array( 'name' => 'Swaziland', 'nativetongue' => '' ),
		'SE' => array( 'name' => 'Sweden', 'nativetongue' => 'Sverige' ),
		'CH' => array( 'name' => 'Switzerland', 'nativetongue' => 'Schweiz' ),
		'SY' => array( 'name' => 'Syria', 'nativetongue' => '‫سوريا' ),
		'TW' => array( 'name' => 'Taiwan', 'nativetongue' => '台灣' ),
		'TJ' => array( 'name' => 'Tajikistan', 'nativetongue' => '' ),
		'TZ' => array( 'name' => 'Tanzania', 'nativetongue' => '' ),
		'TH' => array( 'name' => 'Thailand', 'nativetongue' => 'ไทย' ),
		'TL' => array( 'name' => 'Timor-Leste', 'nativetongue' => '' ),
		'TG' => array( 'name' => 'Togo', 'nativetongue' => '' ),
		'TK' => array( 'name' => 'Tokelau', 'nativetongue' => '' ),
		'TO' => array( 'name' => 'Tonga', 'nativetongue' => '' ),
		'TT' => array( 'name' => 'Trinidad and Tobago', 'nativetongue' => '' ),
		'TA' => array( 'name' => 'Tristan da Cunha', 'nativetongue' => '' ),
		'TN' => array( 'name' => 'Tunisia', 'nativetongue' => '‫تونس' ),
		'TR' => array( 'name' => 'Turkey', 'nativetongue' => 'Türkiye' ),
		'TM' => array( 'name' => 'Turkmenistan', 'nativetongue' => '' ),
		'TC' => array( 'name' => 'Turks and Caicos Islands', 'nativetongue' => '' ),
		'TV' => array( 'name' => 'Tuvalu', 'nativetongue' => '' ),
		'UM' => array( 'name' => 'U.S. Outlying Islands', 'nativetongue' => '' ),
		'VI' => array( 'name' => 'U.S. Virgin Islands', 'nativetongue' => '' ),
		'UG' => array( 'name' => 'Uganda', 'nativetongue' => '' ),
		'UA' => array( 'name' => 'Ukraine', 'nativetongue' => 'Україна' ),
		'AE' => array( 'name' => 'United Arab Emirates', 'nativetongue' => '‫الإمارات العربية المتحدة' ),
		'GB' => array( 'name' => 'United Kingdom', 'nativetongue' => '' ),
		'US' => array( 'name' => 'United States', 'nativetongue' => '' ),
		'UY' => array( 'name' => 'Uruguay', 'nativetongue' => '' ),
		'UZ' => array( 'name' => 'Uzbekistan', 'nativetongue' => 'Oʻzbekiston' ),
		'VU' => array( 'name' => 'Vanuatu', 'nativetongue' => '' ),
		'VA' => array( 'name' => 'Vatican City', 'nativetongue' => 'Città del Vaticano' ),
		'VE' => array( 'name' => 'Venezuela', 'nativetongue' => '' ),
		'VN' => array( 'name' => 'Vietnam', 'nativetongue' => 'Việt Nam' ),
		'WF' => array( 'name' => 'Wallis and Futuna', 'nativetongue' => '' ),
		'EH' => array( 'name' => 'Western Sahara', 'nativetongue' => '‫الصحراء الغربية' ),
		'YE' => array( 'name' => 'Yemen', 'nativetongue' => '‫اليمن' ),
		'ZM' => array( 'name' => 'Zambia', 'nativetongue' => '' ),
		'ZW' => array( 'name' => 'Zimbabwe', 'nativetongue' => '' )
	);

	return $countries;
}

function hocwp_transmit_id_and_name( &$id, &$name ) {
	if ( empty( $id ) && ! empty( $name ) ) {
		$id = $name;
	}
	if ( empty( $name ) && ! empty( $id ) ) {
		$name = $id;
	}
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
	if ( hocwp_is_image_url( $url ) || filter_var( $url, FILTER_VALIDATE_URL ) !== false ) {
		return true;
	}

	return false;
}

function hocwp_color_valid( $color ) {
	if ( preg_match( '/^#[a-f0-9]{6}$/i', $color ) ) {
		return true;
	}

	return false;
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
	$string = strtolower( $string );
	if ( 'true' == $string ) {
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

function hocwp_substr( $str, $len, $more = '...', $charset = 'UTF-8' ) {
	if ( 1 > $len ) {
		return $str;
	}
	$more = esc_html( $more );
	$str  = html_entity_decode( $str, ENT_QUOTES, $charset );
	if ( mb_strlen( $str, $charset ) > $len ) {
		$arr       = explode( ' ', $str );
		$str       = mb_substr( $str, 0, $len, $charset );
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
	$transient_name = 'hocwp_exchange_rate_vietcombank';
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
	$transient_name = 'hocwp_exchange_rate_sjc';
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

function hocwp_percentage( $val1, $val2, $precision = 0 ) {
	$total = $val1 + $val2;
	if ( 0 == $total ) {
		return 0;
	}
	$val1 /= $total;
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
	return mb_strtolower( $str, $charset );
}

function hocwp_is_localhost() {
	$site_url    = get_bloginfo( 'url' );
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