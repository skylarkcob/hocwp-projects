<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

function hocwp_maintenance_mode_default_settings() {
	$defaults = array(
		'title'   => __( 'Maintenance mode', 'hocwp-theme' ),
		'heading' => __( 'Maintenance mode', 'hocwp-theme' ),
		'text'    => __( '<p>Sorry for the inconvenience.<br />Our website is currently undergoing scheduled maintenance.<br />Thank you for your understanding.</p>', 'hocwp-theme' )
	);

	return apply_filters( 'hocwp_maintenance_mode_default_settings', $defaults );
}

function hocwp_newsletter_time_range() {
	$range = apply_filters( 'hocwp_newsletter_time_range', array( 17, 21 ) );
	if ( ! is_array( $range ) || count( $range ) != 2 ) {
		$range = array( 17, 21 );
	}

	return $range;
}

function hocwp_prevent_author_see_another_post() {
	$use = false;
	$use = apply_filters( 'hocwp_prevent_author_see_another_post', $use );

	return $use;
}

function hocwp_delete_old_file( $path, $interval ) {
	$files = scandir( $path );
	$now   = time();
	foreach ( $files as $file ) {
		$file = trailingslashit( $path ) . $file;
		if ( is_file( $file ) ) {
			$file_time = filemtime( $file );
			if ( ( $now - $file_time ) >= $interval ) {
				chmod( $file, 0777 );
				@unlink( $file );
			}
		}
	}
}

function hocwp_is_table_exists( $table_name ) {
	global $wpdb;
	if ( ! hocwp_string_contain( $table_name, $wpdb->prefix ) ) {
		$table_name = $wpdb->prefix . $table_name;
	}
	$result = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
	if ( empty( $result ) ) {
		return false;
	}

	return true;
}

function hocwp_get_url_params( $url = null ) {
	$params = array();
	if ( empty( $url ) ) {
		$url = $_SERVER['REQUEST_URI'];
	}
	$current_url = basename( $url );
	if ( ! empty( $current_url ) ) {
		$parts = explode( '&', $current_url );
		foreach ( $parts as $part ) {
			$p = explode( '=', $part );
			if ( isset( $p[0] ) && ! empty( $p[0] ) ) {
				$param = $p[0];
				$param = trim( $param, '?' );
				if ( false !== strpos( $param, '?' ) ) {
					$tmp   = explode( '?', $param );
					$param = array_pop( $tmp );
				}
				$params[ $param ] = isset( $p[1] ) ? $p[1] : '';
			}
		}
	}

	return $params;
}

function hocwp_form_hidden_params( $params = null, $skip_params = array() ) {
	if ( ! is_array( $params ) ) {
		$params = hocwp_get_url_params();
	}
	if ( is_array( $params ) ) {
		foreach ( $params as $key => $value ) {
			if ( in_array( $key, $skip_params ) ) {
				continue;
			}
			?>
			<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
			<?php
		}
	}
}

function hocwp_star_ratings( $post_id = null ) {
	if ( function_exists( 'kk_star_ratings' ) ) {
		$post_id = hocwp_return_post( $post_id, 'id' );
		echo kk_star_ratings( $post_id );
	}
}

function hocwp_star_rating_result( $args = array() ) {
	if ( ! function_exists( 'wp_star_rating' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/template.php' );
	}
	$votes = 0;
	if ( ! isset( $args['rating'] ) ) {
		$id    = hocwp_get_value_by_key( $args, 'post_id', get_the_ID() );
		$score = get_post_meta( $id, '_kksr_ratings', true ) ? get_post_meta( $id, '_kksr_ratings', true ) : 0;
		if ( ! isset( $args['number'] ) ) {
			$votes = get_post_meta( $id, '_kksr_casts', true ) ? get_post_meta( $id, '_kksr_casts', true ) : 0;
		}
		$args['number'] = $votes;
		if ( $votes != 0 ) {
			$avg   = (float) ( $score / $votes );
			$score = $score ? round( $avg, 2 ) : 0;
		}
		$args['rating'] = $score;
	}
	wp_star_rating( $args );
	$show_count = hocwp_get_value_by_key( $args, 'show_count', true );
	$number     = hocwp_get_value_by_key( $args, 'number' );
	$number     = absint( $number );
	if ( $show_count ) {
		echo '<span aria-hidden="true" class="num-ratings">(' . $number . ')</span>';
	}
}

function hocwp_bootstrap_color_select_options() {
	$options = array(
		'default' => __( 'Default', 'hocwp-theme' ),
		'primary' => __( 'Primary', 'hocwp-theme' ),
		'success' => __( 'Success', 'hocwp-theme' ),
		'info'    => __( 'Info', 'hocwp-theme' ),
		'warning' => __( 'Warning', 'hocwp-theme' ),
		'danger'  => __( 'Danger', 'hocwp-theme' )
	);
	$options = apply_filters( 'hocwp_bootstrap_color_select_options', $options );

	return $options;
}

function hocwp_newsletter_plugin_installed() {
	if ( class_exists( 'Newsletter' ) || class_exists( 'NewsletterModule' ) ) {
		return true;
	}

	return false;
}

function hocwp_add_to_newsletter_list( $args = array() ) {
	if ( hocwp_newsletter_plugin_installed() ) {
		global $newsletter;
		if ( ! isset( $newsletter ) ) {
			$newsletter = new Newsletter();
		}
		if ( isset( $newsletter->options['api_key'] ) && ! empty( $newsletter->options['api_key'] ) ) {
			$api_key = hocwp_get_value_by_key( $args, 'api_key' );
			if ( empty( $api_key ) ) {
				$api_key = $newsletter->options['api_key'];
			}
			$email = hocwp_get_value_by_key( $args, 'email' );
			if ( is_email( $email ) ) {
				$base_url     = NEWSLETTER_URL . '/api/add.php';
				$params       = array(
					'ne' => $email,
					'nk' => $api_key
				);
				$name         = hocwp_get_value_by_key( $args, 'name' );
				$surname      = hocwp_get_method_value( $args, 'surname' );
				$params['nn'] = $name;
				$params['ns'] = $surname;
				$base_url     = add_query_arg( $params, $base_url );
				$result       = @file_get_contents( $base_url );
			}
		}
	}
}

function hocwp_use_core_style() {
	return apply_filters( 'hocwp_use_core_style', true );
}

function hocwp_use_superfish_menu() {
	return apply_filters( 'hocwp_use_superfish_menu', true );
}

function hocwp_maintenance_mode_settings() {
	$defaults = hocwp_maintenance_mode_default_settings();
	$args     = get_option( 'hocwp_maintenance' );
	$args     = wp_parse_args( $args, $defaults );

	return apply_filters( 'hocwp_maintenance_mode_settings', $args );
}

function hocwp_google_login_script( $args = array() ) {
	$connect = hocwp_get_value_by_key( $args, 'connect' );
	if ( is_user_logged_in() && ! $connect ) {
		return;
	}
	$clientid = hocwp_get_value_by_key( $args, 'clientid', hocwp_get_google_client_id() );
	if ( empty( $clientid ) ) {
		hocwp_debug_log( __( 'Please set your Google Client ID first.', 'hocwp-theme' ) );

		return;
	}
	?>
	<script type="text/javascript">
		function hocwp_google_login() {
			var params = {
				clientid: '<?php echo $clientid; ?>',
				cookiepolicy: 'single_host_origin',
				callback: 'hocwp_google_login_on_signin',
				scope: 'email',
				theme: 'dark'
			};
			gapi.auth.signIn(params);
		}
		function hocwp_google_login_on_signin(response) {
			if (response['status']['signed_in'] && !response['_aa']) {
				gapi.client.load('plus', 'v1', hocwp_google_login_client_loaded);
			}
		}
		function hocwp_google_login_client_loaded(response) {
			var request = gapi.client.plus.people.get({userId: 'me'});
			request.execute(function (response) {
				hocwp_google_login_connected_callback(response);
			});
		}
		function hocwp_google_logout() {
			gapi.auth.signOut();
			location.reload();
		}
		function hocwp_google_login_connected_callback(response) {
			(function ($) {
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: hocwp.ajax_url,
					data: {
						action: 'hocwp_social_login_google',
						data: JSON.stringify(response),
						connect: <?php echo hocwp_bool_to_int($connect); ?>
					},
					success: function (response) {
						var href = window.location.href;
						if ($.trim(response.redirect_to)) {
							href = response.redirect_to;
						}
						if (response.logged_in) {
							window.location.href = href;
						}
					}
				});
			})(jQuery);
		}
	</script>
	<?php
}

function hocwp_facebook_login_script( $args = array() ) {
	$connect = hocwp_get_value_by_key( $args, 'connect' );
	if ( is_user_logged_in() && ! $connect ) {
		return;
	}
	$lang     = hocwp_get_language();
	$language = hocwp_get_value_by_key( $args, 'language' );
	if ( empty( $language ) && 'vi' === $lang ) {
		$language = 'vi_VN';
	}
	$app_id = hocwp_get_wpseo_social_facebook_app_id();
	if ( empty( $app_id ) ) {
		hocwp_debug_log( __( 'Please set your Facebook APP ID first.', 'hocwp-theme' ) );

		return;
	}
	?>
	<script type="text/javascript">
		window.hocwp = window.hocwp || {};
		function hocwp_facebook_login_status_callback(response) {
			if (response.status === 'connected') {
				hocwp_facebook_login_connected_callback();
			} else if (response.status === 'not_authorized') {

			} else {

			}
		}
		function hocwp_facebook_login() {
			FB.login(function (response) {
				hocwp_facebook_login_status_callback(response);
			}, {scope: 'email,public_profile,user_friends'});
		}
		window.fbAsyncInit = function () {
			FB.init({
				appId: '<?php echo $app_id; ?>',
				cookie: true,
				xfbml: true,
				version: 'v<?php echo HOCWP_FACEBOOK_GRAPH_API_VERSION; ?>'
			});
		};
		if (typeof FB === 'undefined') {
			(function (d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s);
				js.id = id;
				js.src = "//connect.facebook.net/<?php echo $language; ?>/sdk.js";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
		}
		function hocwp_facebook_login_connected_callback() {
			FB.api('/me', {fields: 'id,name,first_name,last_name,picture,verified,email'}, function (response) {
				(function ($) {
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: hocwp.ajax_url,
						data: {
							action: 'hocwp_social_login_facebook',
							data: JSON.stringify(response),
							connect: <?php echo hocwp_bool_to_int($connect); ?>
						},
						success: function (response) {
							var href = window.location.href;
							if ($.trim(response.redirect_to)) {
								href = response.redirect_to;
							}
							if (response.logged_in) {
								window.location.href = href;
							}
						}
					});
				})(jQuery);
			});
		}
	</script>
	<?php
}

function hocwp_is_bots() {
	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'] ) ) {
		return true;
	}

	return false;
}

function hocwp_get_default_lat_long() {
	$lat_long = array(
		'lat' => '37.42200662799378',
		'lng' => '-122.08403290000001'
	);
	$data     = get_option( 'hocwp_geo' );
	$lat      = hocwp_get_value_by_key( $data, 'default_lat' );
	$lng      = hocwp_get_value_by_key( $data, 'default_lng' );
	if ( ! empty( $lat ) && ! empty( $lng ) ) {
		$lat_long['lat'] = $lat;
		$lat_long['lng'] = $lng;
	} else {
		if ( 'vi' == hocwp_get_language() ) {
			$lat_long['lat'] = '21.003118';
			$lat_long['lng'] = '105.820141';
		}
	}

	return apply_filters( 'hocwp_default_lat_lng', $lat_long );
}

function hocwp_register_post_type_news( $args = array() ) {
	$lang = hocwp_get_language();
	$slug = 'news';
	if ( 'vi' == $lang ) {
		$slug = 'tin-tuc';
	}
	$slug     = apply_filters( 'hocwp_post_type_news_base_slug', $slug );
	$defaults = array(
		'name'              => __( 'News', 'hocwp-theme' ),
		'slug'              => $slug,
		'post_type'         => 'news',
		'show_in_admin_bar' => true,
		'supports'          => array( 'editor', 'thumbnail', 'comments' )
	);
	$args     = wp_parse_args( $args, $defaults );
	hocwp_register_post_type( $args );
	$slug = 'news-cat';
	if ( 'vi' == $lang ) {
		$slug = 'chuyen-muc';
	}
	$slug = apply_filters( 'hocwp_taxonomy_news_category_base_slug', $slug );
	$args = array(
		'name'          => __( 'News Categories', 'hocwp-theme' ),
		'singular_name' => __( 'News Category', 'hocwp-theme' ),
		'post_types'    => 'news',
		'menu_name'     => __( 'Categories', 'hocwp-theme' ),
		'slug'          => $slug,
		'taxonomy'      => 'news_cat'
	);
	hocwp_register_taxonomy( $args );
	$news_tag = apply_filters( 'hocwp_post_type_news_tag', false );
	if ( $news_tag ) {
		$slug = 'news-tag';
		if ( 'vi' == $lang ) {
			$slug = 'the';
		}
		$slug = apply_filters( 'hocwp_taxonomy_news_tag_base_slug', $slug );
		$args = array(
			'name'          => __( 'News Tags', 'hocwp-theme' ),
			'singular_name' => __( 'News Tag', 'hocwp-theme' ),
			'post_types'    => 'news',
			'menu_name'     => __( 'Tags', 'hocwp-theme' ),
			'slug'          => $slug,
			'hierarchical'  => false,
			'taxonomy'      => 'news_tag'
		);
		hocwp_register_taxonomy( $args );
	}
}

function hocwp_register_lib_google_maps( $api_key = null ) {
	if ( empty( $api_key ) ) {
		$options = get_option( 'hocwp_option_social' );
		$api_key = hocwp_get_value_by_key( $options, 'google_api_key' );
	}
	if ( empty( $api_key ) ) {
		return;
	}
	wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $api_key, array(), false, true );
}

function hocwp_register_lib_tinymce() {
	wp_enqueue_script( 'tinymce', '//cdn.tinymce.com/' . HOCWP_TINYMCE_VERSION . '/tinymce.min.js', array(), false, true );
}

function hocwp_inline_css( $elements, $properties ) {
	$css = hocwp_build_css_rule( $elements, $properties );
	if ( ! empty( $css ) ) {
		$style = new HOCWP_HTML( 'style' );
		$style->set_attribute( 'type', 'text/css' );
		$css = hocwp_minify_css( $css );
		$style->set_text( $css );
		if ( ! empty( $css ) ) {
			$style->output();
		}
	}
}

function hocwp_inline_script( $code ) {
	$script = new HOCWP_HTML( 'script' );
	$script->set_attribute( 'type', 'text/javascript' );
	$script->set_text( $code );
	$script->output();
}

function hocwp_favorite_post_button_text( $args = array() ) {
	if ( ! is_array( $args ) ) {
		$post_id = $args;
	} else {
		$post_id = hocwp_get_value_by_key( $args, 'post_id' );
	}
	$lang      = hocwp_get_language();
	$save_text = hocwp_get_value_by_key( $args, 'save_text', '' );
	if ( empty( $save_text ) ) {
		if ( 'vi' == $lang ) {
			$save_text = __( 'Lưu tin', 'hocwp-theme' );
		} else {
			$save_text = __( 'Favorite', 'hocwp-theme' );
		}
	}
	$unsave_text = hocwp_get_value_by_key( $args, 'unsave_text' );
	if ( empty( $unsave_text ) ) {
		if ( 'vi' == $lang ) {
			$unsave_text = __( 'Bỏ lưu', 'hocwp-theme' );
		} else {
			$unsave_text = __( 'Favorited', 'hocwp-theme' );
		}
	}
	if ( ! hocwp_id_number_valid( $post_id ) ) {
		$post_id = get_the_ID();
	}
	$text = '<i class="fa fa-heart-o"></i> ' . $save_text;
	if ( is_user_logged_in() ) {
		$user     = wp_get_current_user();
		$favorite = hocwp_get_user_favorite_posts( $user->ID );
		if ( in_array( $post_id, $favorite ) ) {
			$text = '<i class="fa fa-heart"></i> ' . $unsave_text;
		}
	}
	$text = apply_filters( 'hocwp_favorite_post_button_text', $text, $args );
	$echo = hocwp_get_value_by_key( $args, 'echo', true );
	if ( $echo ) {
		echo $text;
	}

	return $text;
}

function hocwp_save_post_button_text( $args = array() ) {
	if ( ! is_array( $args ) ) {
		$post_id = $args;
	} else {
		$post_id = hocwp_get_value_by_key( $args, 'post_id' );
	}
	$lang      = hocwp_get_language();
	$save_text = hocwp_get_value_by_key( $args, 'save_text', '' );
	if ( empty( $save_text ) ) {
		if ( 'vi' == $lang ) {
			$save_text = __( 'Lưu tin', 'hocwp-theme' );
		} else {
			$save_text = __( 'Save', 'hocwp-theme' );
		}
	}
	$unsave_text = hocwp_get_value_by_key( $args, 'unsave_text' );
	if ( empty( $unsave_text ) ) {
		if ( 'vi' == $lang ) {
			$unsave_text = __( 'Bỏ lưu', 'hocwp-theme' );
		} else {
			$unsave_text = __( 'Saved', 'hocwp-theme' );
		}
	}
	if ( ! hocwp_id_number_valid( $post_id ) ) {
		$post_id = get_the_ID();
	}
	$text  = '<i class="fa fa-heart-o"></i> ' . $save_text;
	$saved = hocwp_get_value_by_key( $args, 'saved' );
	if ( (bool) $saved ) {
		$text = '<i class="fa fa-heart"></i> ' . $unsave_text;
	}
	$text = apply_filters( 'hocwp_save_post_button_text', $text, $args );
	$echo = hocwp_get_value_by_key( $args, 'echo', true );
	if ( $echo ) {
		echo $text;
	}

	return $text;
}

function hocwp_get_geo_code( $args = array() ) {
	if ( ! is_array( $args ) && ! empty( $args ) ) {
		$args = array(
			'address' => $args
		);
	}
	$options  = get_option( 'hocwp_option_social' );
	$api_key  = hocwp_get_value_by_key( $options, 'google_api_key' );
	$defaults = array(
		'sensor' => false,
		'region' => 'Vietnam',
		'key'    => $api_key
	);
	$args     = wp_parse_args( $args, $defaults );
	$address  = hocwp_get_value_by_key( $args, 'address' );
	if ( empty( $address ) ) {
		return '';
	}
	$address         = str_replace( ' ', '+', $address );
	$args['address'] = $address;
	$transient_name  = 'hocwp_geo_code_' . md5( implode( '_', $args ) );
	if ( false === ( $results = get_transient( $transient_name ) ) ) {
		$base    = 'https://maps.googleapis.com/maps/api/geocode/json';
		$base    = add_query_arg( $args, $base );
		$json    = @file_get_contents( $base );
		$results = json_decode( $json );
		if ( 'OK' === $results->status ) {
			set_transient( $transient_name, $results, MONTH_IN_SECONDS );
		}
	}

	return $results;
}

function hocwp_generate_min_file( $file, $extension = 'js', $compress_min_file = false, $force_compress = false ) {
	$transient_name = 'hocwp_minified_' . md5( $file );
	if ( false === get_transient( $transient_name ) || $force_compress ) {
		if ( file_exists( $file ) ) {
			$extension = strtolower( $extension );
			if ( 'js' === $extension ) {
				$minified = hocwp_minify_js( $file );
			} else {
				$minified = hocwp_minify_css( $file, true );
			}
			if ( ! empty( $minified ) ) {
				if ( $compress_min_file ) {
					if ( ! file_exists( $file ) ) {
						$handler = fopen( $file, 'w' );
						fwrite( $handler, $minified );
						fclose( $handler );
					} else {
						@file_put_contents( $file, $minified );
					}
				} else {
					$info      = pathinfo( $file );
					$basename  = $info['basename'];
					$filename  = $info['filename'];
					$extension = $info['extension'];
					$min_name  = $filename;
					$min_name .= '.min';
					if ( ! empty( $extension ) ) {
						$min_name .= '.' . $extension;
					}
					$min_file = str_replace( $basename, $min_name, $file );
					$handler  = fopen( $min_file, 'w' );
					fwrite( $handler, $minified );
					fclose( $handler );
				}
				set_transient( $transient_name, 1, 15 * MINUTE_IN_SECONDS );
				hocwp_debug_log( sprintf( __( 'File %s is compressed successfully!', 'hocwp-theme' ), $file ) );
			}
		}
	}
}

function hocwp_compress_style( $dir, $compress_min_file = false, $force_compress = false ) {
	$files     = scandir( $dir );
	$my_files  = array();
	$min_files = array();
	foreach ( $files as $file ) {
		$info = pathinfo( $file );
		if ( isset( $info['extension'] ) && 'css' == $info['extension'] ) {
			$base_name = $info['basename'];
			if ( false !== strpos( $base_name, '.min' ) ) {
				if ( $compress_min_file ) {
					$min_files[] = trailingslashit( $dir ) . $file;
				}
				continue;
			}
			$my_files[] = trailingslashit( $dir ) . $file;
		}
	}
	if ( hocwp_array_has_value( $min_files ) || $compress_min_file ) {
		foreach ( $min_files as $file ) {
			hocwp_generate_min_file( $file, 'css', true, $force_compress );
		}

		return;
	}
	if ( hocwp_array_has_value( $my_files ) ) {
		foreach ( $my_files as $file ) {
			hocwp_generate_min_file( $file, 'css', false, $force_compress );
		}
	}
}

function hocwp_compress_script( $dir, $compress_min_file = false, $force_compress = false ) {
	$files     = scandir( $dir );
	$my_files  = array();
	$min_files = array();
	foreach ( $files as $file ) {
		$info = pathinfo( $file );
		if ( isset( $info['extension'] ) && 'js' == $info['extension'] ) {
			$base_name = $info['basename'];
			if ( false !== strpos( $base_name, '.min' ) ) {
				if ( $compress_min_file ) {
					$min_files[] = trailingslashit( $dir ) . $file;
				}
				continue;
			}
			$my_files[] = trailingslashit( $dir ) . $file;
		}
	}
	if ( hocwp_array_has_value( $min_files ) || $compress_min_file ) {
		foreach ( $min_files as $file ) {
			hocwp_generate_min_file( $file, 'js', true, $force_compress );
		}

		return;
	}
	if ( hocwp_array_has_value( $my_files ) ) {
		foreach ( $my_files as $file ) {
			hocwp_generate_min_file( $file, 'js', false, $force_compress );
		}
	}
}

function hocwp_compress_style_and_script( $args = array() ) {
	$type           = hocwp_get_value_by_key( $args, 'type' );
	$force_compress = hocwp_get_value_by_key( $args, 'force_compress' );
	$compress_core  = hocwp_get_value_by_key( $args, 'compress_core' );
	$recompress = false;
	if ( hocwp_array_has_value( $type ) ) {
		$compress_css = false;
		if ( in_array( 'css', $type ) ) {
			$compress_css = true;
			if ( $compress_core ) {
				$hocwp_css_path = HOCWP_PATH . '/css';
				hocwp_compress_style( $hocwp_css_path, false, $force_compress );
			}
			if ( defined( 'HOCWP_THEME_VERSION' ) ) {
				$hocwp_css_path = HOCWP_THEME_PATH . '/css';
				hocwp_compress_style( $hocwp_css_path, false, $force_compress );
				$min_file = $hocwp_css_path . '/hocwp-custom-front-end.min.css';
				if ( ! file_exists( $min_file ) ) {
					hocwp_create_file( $min_file );
				}
				$old_content = @file_get_contents( $min_file );
				$old_content = trim( $old_content );
				if ( empty( $old_content ) ) {
					$min_file = $hocwp_css_path . '/hocwp-custom-front-end.css';
					if ( file_exists( $min_file ) ) {
						$old_content = @file_get_contents( $min_file );
						$old_content = hocwp_minify_css( $old_content );
						$old_content = trim( $old_content );
					}
				}
				if ( ! empty( $old_content ) ) {
					$temp_file = HOCWP_PATH . '/css/hocwp-front-end.min.css';
					if ( file_exists( $temp_file ) ) {
						$temp_content = @file_get_contents( $temp_file );
						$temp_content = trim( $temp_content );
						$old_content  = $temp_content . $old_content;
					}
					$temp_file = HOCWP_PATH . '/css/hocwp.min.css';
					if ( file_exists( $temp_file ) ) {
						$temp_content = @file_get_contents( $temp_file );
						$temp_content = trim( $temp_content );
						$old_content  = $temp_content . $old_content;
					}
					$old_content = trim( $old_content );
				}
				@file_put_contents( $min_file, $old_content );
			}
		}
		$compress_js = false;
		if ( in_array( 'js', $type ) ) {
			$compress_js = true;
			if ( $compress_core ) {
				$hocwp_js_path = HOCWP_PATH . '/js';
				hocwp_compress_script( $hocwp_js_path, false, $force_compress );
			}
			if ( defined( 'HOCWP_THEME_VERSION' ) ) {
				$hocwp_js_path = HOCWP_THEME_PATH . '/js';
				hocwp_compress_script( $hocwp_js_path, false, $force_compress );
				$min_file = $hocwp_js_path . '/hocwp-custom-front-end.min.js';
				if ( ! file_exists( $min_file ) ) {
					hocwp_create_file( $min_file );
				}
				$old_content = @file_get_contents( $min_file );
				$old_content = trim( $old_content );
				if ( empty( $old_content ) ) {
					$min_file = $hocwp_js_path . '/hocwp-custom-front-end.js';
					if ( file_exists( $min_file ) ) {
						$old_content = @file_get_contents( $min_file );
						$old_content = hocwp_minify_js( $old_content );
						$old_content = trim( $old_content );
					}
				}
				if ( ! empty( $old_content ) ) {
					$temp_file = HOCWP_PATH . '/js/hocwp-front-end.min.js';
					if ( file_exists( $temp_file ) ) {
						$temp_content = @file_get_contents( $temp_file );
						$temp_content = trim( $temp_content );
						$old_content  = $temp_content . $old_content;
					}
					$temp_file = HOCWP_PATH . '/js/hocwp.min.js';
					if ( file_exists( $temp_file ) ) {
						$temp_content = @file_get_contents( $temp_file );
						$temp_content = trim( $temp_content );
						$old_content  = $temp_content . $old_content;
					}
					$old_content = trim( $old_content );
				}
				@file_put_contents( $min_file, $old_content );
			}
		}
		if ( $compress_css || $compress_js ) {
			unset( $type['recompress'] );
		}
		if ( in_array( 'recompress', $type ) ) {
			if ( defined( 'HOCWP_THEME_VERSION' ) ) {
				$hocwp_js_path = HOCWP_THEME_PATH . '/js';
				hocwp_compress_script( $hocwp_js_path, true, $force_compress );
				$hocwp_css_path = HOCWP_THEME_PATH . '/css';
				hocwp_compress_style( $hocwp_css_path, true, $force_compress );
			}
		}
		$compress_paths = apply_filters( 'hocwp_compress_paths', array() );
		foreach ( $compress_paths as $path ) {
			$css_path     = trailingslashit( $path ) . 'css';
			$js_path      = trailingslashit( $path ) . 'js';
			$compress_css = false;
			if ( in_array( 'css', $type ) ) {
				$compress_css = true;
				hocwp_compress_style( $css_path, false, $force_compress );
			}
			$compress_js = false;
			if ( in_array( 'js', $type ) ) {
				$compress_js = true;
				hocwp_compress_script( $js_path, false, $force_compress );
			}
			if ( $compress_css || $compress_js ) {
				unset( $type['recompress'] );
			}
			if ( in_array( 'recompress', $type ) ) {
				hocwp_compress_script( $js_path, true, $force_compress );
				hocwp_compress_style( $css_path, true, $force_compress );
			}
		}
	}
}

function hocwp_php_thumb() {

}

function hocwp_post_rating_ajax_callback() {
	$result  = array(
		'success' => false
	);
	$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;
	if ( hocwp_id_number_valid( $post_id ) ) {
		$score = isset( $_POST['score'] ) ? $_POST['score'] : 0;
		if ( is_numeric( $score ) && $score > 0 ) {
			$number      = isset( $_POST['number'] ) ? $_POST['number'] : 5;
			$number_max  = isset( $_POST['number_max'] ) ? $_POST['number_max'] : 5;
			$high_number = $number;
			if ( $number > $number_max ) {
				$high_number = $number_max;
			}
			$ratings_score = floatval( get_post_meta( $post_id, 'ratings_score', true ) );
			$ratings_score += $score;
			$ratings_users = absint( get_post_meta( $post_id, 'ratings_users', true ) );
			$ratings_users ++;
			$high_ratings_users = absint( get_post_meta( $post_id, 'high_ratings_users', true ) );
			if ( $score == $high_number ) {
				$high_ratings_users ++;
				update_post_meta( $post_id, 'high_ratings_users', $high_ratings_users );
			}
			$ratings_average = $score;
			update_post_meta( $post_id, 'ratings_users', $ratings_users );
			update_post_meta( $post_id, 'ratings_score', $ratings_score );
			if ( $ratings_users > 0 ) {
				$ratings_average = $ratings_score / $ratings_users;
			}
			update_post_meta( $post_id, 'ratings_average', $ratings_average );
			$result['success']        = true;
			$result['score']          = $ratings_average;
			$session_key              = 'hocwp_post_' . $post_id . '_rated';
			$_SESSION[ $session_key ] = 1;
			do_action( 'hocwp_post_rated', $score, $post_id );
		}
	}

	return $result;
}

function hocwp_change_url( $new_url, $old_url = '', $force_update = false ) {
	$transient_name = 'hocwp_update_data_after_url_changed';
	$site_url       = trailingslashit( get_bloginfo( 'url' ) );
	if ( ! empty( $old_url ) ) {
		$old_url = trailingslashit( $old_url );
		if ( $old_url != $site_url && ! $force_update ) {
			return;
		}
	} else {
		$old_url = $site_url;
	}
	$new_url = trailingslashit( $new_url );
	if ( $old_url == $new_url && ! $force_update ) {
		return;
	}
	if ( false === get_transient( $transient_name ) || $force_update ) {
		global $wpdb;
		$wpdb->query( "UPDATE $wpdb->options SET option_value = replace(option_value, '$old_url', '$new_url') WHERE option_name = 'home' OR option_name = 'siteurl'" );
		$wpdb->query( "UPDATE $wpdb->posts SET guid = (REPLACE (guid, '$old_url', '$new_url'))" );
		$wpdb->query( "UPDATE $wpdb->posts SET post_content = (REPLACE (post_content, '$old_url', '$new_url'))" );

		$wpdb->query( "UPDATE $wpdb->postmeta SET meta_value = (REPLACE (meta_value, '$old_url', '$new_url'))" );
		$wpdb->query( "UPDATE $wpdb->termmeta SET meta_value = (REPLACE (meta_value, '$old_url', '$new_url'))" );
		$wpdb->query( "UPDATE $wpdb->commentmeta SET meta_value = (REPLACE (meta_value, '$old_url', '$new_url'))" );
		$wpdb->query( "UPDATE $wpdb->usermeta SET meta_value = (REPLACE (meta_value, '$old_url', '$new_url'))" );
		if ( is_multisite() ) {
			$wpdb->query( "UPDATE $wpdb->sitemeta SET meta_value = (REPLACE (meta_value, '$old_url', '$new_url'))" );
		}
		set_transient( $transient_name, 1, 5 * MINUTE_IN_SECONDS );
	}
}

function hocwp_disable_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}

function hocwp_the_custom_content( $content ) {
	$content = apply_filters( 'hocwp_the_custom_content', $content );
	echo $content;
}