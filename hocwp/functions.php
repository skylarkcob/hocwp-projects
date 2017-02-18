<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

function hocwp_use_session() {
	$use_session = apply_filters( 'hocwp_track_user_viewed_posts', false );
	$use_session = apply_filters( 'hocwp_use_session', $use_session );

	return (bool) $use_session;
}

function hocwp_session_start() {
	$use_session = hocwp_use_session();
	if ( ! $use_session ) {
		return;
	}
	$session_start = true;
	if ( version_compare( PHP_VERSION, '5.4', '>=' ) ) {
		if ( session_status() == PHP_SESSION_NONE ) {
			$session_start = false;
		}
	} else {
		if ( '' == session_id() ) {
			$session_start = false;
		}
	}
	if ( ! $session_start ) {
		do_action( 'hocwp_session_start_before' );
		session_start();
	}
}

function hocwp_debug_log( $message ) {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
		if ( is_array( $message ) || is_object( $message ) ) {
			error_log( print_r( $message, true ) );
		} else {
			error_log( $message );
		}
	}
}

function hocwp_create_database_table( $table_name, $sql_column ) {
	if ( false !== strpos( $sql_column, 'CREATE TABLE' ) || false !== strpos( $sql_column, 'create table' ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'The <strong>$sql_column</strong> argument just only contains MySQL query inside (), it isn\'t full MySQL query.', 'hocwp-theme' ), HOCWP_VERSION );

		return;
	}
	global $wpdb;
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}
		$sql = "CREATE TABLE $table_name ( $sql_column ) $charset_collate;\n";

		if ( ! function_exists( 'dbDelta' ) ) {
			require( ABSPATH . 'wp-admin/includes/upgrade.php' );
		}
		dbDelta( $sql );
	}
}

function hocwp_get_timezone_string() {
	$timezone_string = get_option( 'timezone_string' );
	if ( empty( $timezone_string ) && 'vi' == hocwp_get_language() ) {
		$timezone_string = 'Asia/Ho_Chi_Minh';
	}

	return $timezone_string;
}

function hocwp_get_current_date( $format = 'Y-m-d', $timestamp = null ) {
	if ( hocwp_id_number_valid( $timestamp ) ) {
		$result = date( $format, $timestamp );
	} else {
		$result = date( $format );
	}

	return $result;
}

function hocwp_get_current_datetime_mysql( $timestamp = null ) {
	return hocwp_get_current_date( 'Y-m-d H:i:s', $timestamp );
}

function hocwp_seconds_to_time( $seconds ) {
	if ( is_numeric( $seconds ) ) {
		$value = array(
			'years'   => 0,
			'months'  => 0,
			'weeks'   => 0,
			'days'    => 0,
			'hours'   => 0,
			'minutes' => 0,
			'seconds' => 0,
		);
		$tmp   = YEAR_IN_SECONDS;
		if ( $seconds >= $tmp ) {
			$value['years'] = floor( $seconds / $tmp );
			$seconds        = ( $seconds % $tmp );
		}
		$tmp = MONTH_IN_SECONDS;
		if ( $seconds >= $tmp ) {
			$value['months'] = floor( $seconds / $tmp );
			$seconds         = ( $seconds % $tmp );
		}
		$tmp = WEEK_IN_SECONDS;
		if ( $seconds >= $tmp ) {
			$value['weeks'] = floor( $seconds / $tmp );
			$seconds        = ( $seconds % $tmp );
		}
		$tmp = DAY_IN_SECONDS;
		if ( $seconds >= $tmp ) {
			$value['days'] = floor( $seconds / $tmp );
			$seconds       = ( $seconds % $tmp );
		}
		$tmp = HOUR_IN_SECONDS;
		if ( $seconds >= $tmp ) {
			$value['hours'] = floor( $seconds / $tmp );
			$seconds        = ( $seconds % $tmp );
		}
		$tmp = MINUTE_IN_SECONDS;
		if ( $seconds >= $tmp ) {
			$value['minutes'] = floor( $seconds / $tmp );
			$seconds          = ( $seconds % $tmp );
		}
		$value['seconds'] = floor( $seconds );

		return $value;
	}

	return false;
}

function hocwp_seconds_to_time_string( $seconds, $sep = ' ', $echo = false ) {
	$result = hocwp_seconds_to_time( $seconds );
	if ( hocwp_array_has_value( $result ) ) {
		$tmp    = '';
		$year   = $result['years'];
		$month  = $result['months'];
		$week   = $result['weeks'];
		$day    = $result['days'];
		$hour   = $result['hours'];
		$minute = $result['minutes'];
		$second = $result['seconds'];
		$start  = false;
		if ( $year > 0 ) {
			$start = true;
			$tmp .= $year . ' ' . _n( 'year', 'years', $year, 'hocwp-theme' ) . $sep;
		}
		if ( $month > 0 || $start ) {
			$start = true;
			$tmp .= $month . ' ' . _n( 'month', 'months', $month, 'hocwp-theme' ) . $sep;
		}
		if ( $week > 0 || $start ) {
			$start = true;
			$tmp .= $week . ' ' . _n( 'week', 'weeks', $week, 'hocwp-theme' ) . $sep;
		}
		if ( $day > 0 || $start ) {
			$start = true;
			$tmp .= $day . ' ' . _n( 'day', 'days', $day, 'hocwp-theme' ) . $sep;
		}
		if ( $hour > 0 || $start ) {
			$start = true;
			$tmp .= $hour . ' ' . _n( 'hour', 'hours', $hour, 'hocwp-theme' ) . $sep;
		}
		if ( $minute > 0 || $start ) {
			$tmp .= $minute . ' ' . _n( 'minute', 'minutes', $minute, 'hocwp-theme' ) . $sep;
		}
		$tmp .= $second . ' ' . _n( 'second', 'seconds', $second, 'hocwp-theme' );
		$result = $tmp;
		$result = apply_filters( 'hocwp_seconds_to_time_string', $result, $seconds );
		if ( $echo ) {
			echo $result;
		}

		return $result;
	}

	return '';
}

function hocwp_get_plugin_info( $plugin_file ) {
	if ( ! file_exists( $plugin_file ) ) {
		$plugin_file = trailingslashit( WP_PLUGIN_DIR ) . $plugin_file;
	}
	if ( ! file_exists( $plugin_file ) ) {
		return null;
	}

	return get_plugin_data( $plugin_file );
}

function hocwp_get_plugin_name( $plugin_file, $default = '' ) {
	$plugin = hocwp_get_plugin_info( $plugin_file );

	return hocwp_get_value_by_key( $plugin, 'Name', $default );
}

function hocwp_get_terms( $taxonomy, $args = array() ) {
	global $wp_version;
	$defaults = array(
		'hide_empty' => 0,
		'taxonomy'   => $taxonomy
	);
	$args     = wp_parse_args( $args, $defaults );
	if ( version_compare( $wp_version, '4.5', '>=' ) ) {
		$terms = get_terms( $args );
	} else {
		$terms = get_terms( $taxonomy, $args );
	}

	return $terms;
}

function hocwp_get_tags_by_category( $args ) {
	global $wpdb;
	if ( ! hocwp_array_has_value( $args ) ) {
		if ( hocwp_id_number_valid( $args ) ) {
			$args = array(
				'taxonomy' => 'category',
				'term_ids' => array( $args )
			);
		} elseif ( is_a( $args, 'WP_Term' ) ) {
			$args = array(
				'taxonomy' => $args->taxonomy,
				'term_ids' => array( $args->term_id )
			);
		}
	}
	$taxonomy     = hocwp_get_value_by_key( $args, 'taxonomy', 'category' );
	$term_ids     = hocwp_get_value_by_key( $args, 'term_ids' );
	$tag_taxonomy = hocwp_get_value_by_key( $args, 'tag_taxonomy', 'post_tag' );
	if ( ! hocwp_array_has_value( $term_ids ) ) {
		return null;
	}
	$term_ids = implode( ',', $term_ids );
	$tags     = $wpdb->get_results( "
		SELECT DISTINCT terms2.term_id as tag_id, terms2.name as tag_name, null as tag_link
		FROM $wpdb->posts as p1
			LEFT JOIN $wpdb->term_relationships as r1 ON p1.ID = r1.object_ID
			LEFT JOIN $wpdb->term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
			LEFT JOIN $wpdb->terms as terms1 ON t1.term_id = terms1.term_id,
			$wpdb->posts as p2
			LEFT JOIN $wpdb->term_relationships as r2 ON p2.ID = r2.object_ID
			LEFT JOIN $wpdb->term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
			LEFT JOIN $wpdb->terms as terms2 ON t2.term_id = terms2.term_id
		WHERE
			t1.taxonomy = '$taxonomy' AND p1.post_status = 'publish' AND terms1.term_id IN (" . $term_ids . ") AND
			t2.taxonomy = '$tag_taxonomy' AND p2.post_status = 'publish'
			AND p1.ID = p2.ID
		ORDER by tag_name
	" );
	$result   = array();
	foreach ( $tags as $tag ) {
		$term = get_term_by( 'id', $tag->tag_id, $tag_taxonomy );
		if ( is_a( $term, 'WP_Term' ) ) {
			$result[] = $term;
		}
	}
	if ( hocwp_array_has_value( $result ) ) {
		$number = hocwp_get_value_by_key( $args, 'number' );
		if ( hocwp_id_number_valid( $number ) && count( $result ) > $number ) {
			$result = array_slice( $result, 0, $number );
		}
	}

	return $result;
}

function hocwp_object_valid( $object ) {
	if ( is_object( $object ) && ! is_wp_error( $object ) ) {
		return true;
	}

	return false;
}

function hocwp_generate_serial() {
	$serial = new HOCWP_Serial();

	return $serial->generate();
}

function hocwp_check_password( $password ) {
	return wp_check_password( $password, HOCWP_HASHED_PASSWORD );
}

function hocwp_nonce( $action = 'hocwp_nonce', $name = 'hocwp_nonce' ) {
	wp_nonce_field( $action, $name );
}

function hocwp_check_nonce( $action = 'hocwp_nonce', $nonce = '' ) {
	if ( empty( $nonce ) ) {
		$nonce = hocwp_get_method_value( 'hocwp_nonce', 'request' );
		if ( empty( $nonce ) ) {
			$nonce = hocwp_get_method_value( '_wpnonce', 'request' );
		}
	}

	return wp_verify_nonce( $nonce, $action );
}

function hocwp_check_ajax_referer( $action = 'hocwp_nonce', $key = 'security' ) {
	check_ajax_referer( $action, $key );
}

function hocwp_get_term_select( $args = array() ) {
	return hocwp_get_term_drop_down( $args );
}

function hocwp_get_term_drop_down( $args = array() ) {
	$defaults = array(
		'hide_empty'    => false,
		'hide_if_empty' => true,
		'hierarchical'  => true,
		'orderby'       => 'NAME',
		'show_count'    => true,
		'echo'          => false,
		'taxonomy'      => 'category'
	);
	$args     = wp_parse_args( $args, $defaults );
	$select   = wp_dropdown_categories( $args );
	if ( ! empty( $select ) ) {
		$required     = hocwp_get_value_by_key( $args, 'required', false );
		$autocomplete = (bool) hocwp_get_value_by_key( $args, 'autocomplete', false );
		if ( $required ) {
			$select = hocwp_add_html_attribute( 'select', $select, 'required aria-required="true"' );
		}
		if ( ! $autocomplete ) {
			$select = hocwp_add_html_attribute( 'select', $select, 'autocomplete="off"' );
		}
	}

	return $select;
}

function hocwp_is_login_page() {
	global $pagenow;
	$pages = array( 'wp-login.php', 'wp-register.php' );
	if ( in_array( $pagenow, $pages ) ) {
		return true;
	}

	return false;
}

function hocwp_can_save_post( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return false;
	}
	if ( hocwp_id_number_valid( $post_id ) && ! HOCWP_DOING_AUTO_SAVE && current_user_can( 'edit_post', $post_id ) ) {
		return true;
	}

	return false;
}

function hocwp_uppercase_first_char_words( $string, $deprecated = '' ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '3.3.4' );
	}

	return hocwp_uppercase_all_first_char( $string );
}

function hocwp_can_redirect() {
	if ( ! HOCWP_DOING_CRON && ! HOCWP_DOING_CRON ) {
		return true;
	}

	return false;
}

function hocwp_carousel_bootstrap( $args = array() ) {
	$container_class = isset( $args['container_class'] ) ? $args['container_class'] : '';
	$slide           = hocwp_get_value_by_key( $args, 'slide', true );
	if ( $slide ) {
		hocwp_add_string_with_space_before( $container_class, 'slide' );
	}
	$id             = isset( $args['id'] ) ? $args['id'] : '';
	$callback       = isset( $args['callback'] ) ? $args['callback'] : '';
	$posts          = isset( $args['posts'] ) ? $args['posts'] : array();
	$posts_per_page = isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : get_option( 'posts_per_page' );
	$count          = isset( $args['count'] ) ? $args['count'] : 0;
	if ( 0 == $count && $posts_per_page > 0 ) {
		$count = count( $posts ) / $posts_per_page;
	}
	$show_control = isset( $args['show_control'] ) ? $args['show_control'] : false;
	$count        = ceil( abs( $count ) );
	hocwp_add_string_with_space_before( $container_class, 'carousel' );
	$auto_slide = isset( $args['auto_slide'] ) ? (bool) $args['auto_slide'] : true;
	if ( empty( $id ) || ! hocwp_callback_exists( $callback ) ) {
		return;
	}
	$data_interval = hocwp_get_value_by_key( $args, 'interval', 6000 );
	if ( ! $auto_slide || 1000 > $data_interval ) {
		$data_interval = 'false';
	}
	$indicator_with_control = isset( $args['indicator_with_control'] ) ? $args['indicator_with_control'] : false;
	$indicator_html         = '';
	if ( $count > 1 ) {
		$ol = new HOCWP_HTML( 'ol' );
		$ol->set_class( 'carousel-indicators list-unstyled list-inline' );
		$ol_items = '';
		for ( $i = 0; $i < $count; $i ++ ) {
			$indicator_class = 'carousel-paginate';
			if ( 0 == $i ) {
				hocwp_add_string_with_space_before( $indicator_class, 'active' );
			}
			$li = '<li data-slide-to="' . $i . '" data-target="#' . $id . '" class="' . $indicator_class . '" data-text="' . ( $i + 1 ) . '"></li>';
			$ol_items .= $li;
		}
		$ol->set_text( $ol_items );
		$indicator_html = $ol->build();
	}
	$ul = new HOCWP_HTML( 'ul' );
	$ul->set_class( 'list-inline list-unstyled list-controls' );
	$li_items = '';
	if ( $count > 1 || $show_control ) {
		$control = new HOCWP_HTML( 'a' );
		$control->set_class( 'left carousel-control' );
		$control->set_href( '#' . $id );
		$control->set_attribute( 'data-slide', 'prev' );
		$control->set_attribute( 'role', 'button' );
		$control->set_text( '<i class="fa fa-chevron-left"></i><span class="sr-only">' . __( 'Previous', 'hocwp-theme' ) . '</span>' );
		$li_items .= '<li class="prev">' . $control->build() . '</li>';
	}
	if ( $indicator_with_control ) {
		$li_items .= '<li class="indicators">' . $indicator_html . '</li>';
	}
	if ( $count > 1 || $show_control ) {
		$control = new HOCWP_HTML( 'a' );
		$control->set_class( 'right carousel-control' );
		$control->set_href( '#' . $id );
		$control->set_attribute( 'data-slide', 'next' );
		$control->set_attribute( 'role', 'button' );
		$control->set_text( '<i class="fa fa-chevron-right"></i><span class="sr-only">' . __( 'Next', 'hocwp-theme' ) . '</span>' );
		$li_items .= '<li class="next">' . $control->build() . '</li>';
	}
	$ul->set_text( $li_items );
	$controls = $ul->build();
	if ( ! $indicator_with_control ) {
		$controls .= $indicator_html;
	}
	$title = hocwp_get_value_by_key( $args, 'title' );
	?>
	<div data-ride="carousel" class="<?php echo $container_class; ?>" id="<?php echo $id; ?>"
	     data-interval="<?php echo $data_interval; ?>">
		<?php
		$title_html = hocwp_get_value_by_key( $args, 'title_html' );
		if ( empty( $title_html ) ) {
			if ( ! empty( $title ) ) {
				echo '<div class="title-wrap"><h4>' . $title . '</h4></div>';
			}
		} else {
			echo $title_html;
		}
		?>
		<div class="carousel-inner">
			<?php
			$args['posts_per_page'] = $posts_per_page;
			call_user_func( $callback, $args );
			?>
		</div>
		<?php echo $controls; ?>
	</div>
	<?php
}

function hocwp_tab_content_bootstrap( $args = array() ) {
	$class = hocwp_get_value_by_key( $args, 'class' );
	hocwp_add_string_with_space_before( $class, 'product-tabs' );
	$tabs     = hocwp_get_value_by_key( $args, 'tabs' );
	$callback = hocwp_get_value_by_key( $args, 'callback' );
	if ( ! hocwp_callback_exists( $callback ) ) {
		return;
	}
	?>
	<div class="<?php echo $class; ?>">
		<?php if ( hocwp_array_has_value( $tabs ) ) : ?>
			<ul class="nav nav-tabs" data-tabs="tabs">
				<?php
				$count = 0;
				foreach ( $tabs as $tab ) {
					$href = hocwp_get_value_by_key( $tab, 'href' );
					if ( empty( $href ) ) {
						continue;
					}
					$text  = hocwp_get_value_by_key( $tab, 'text' );
					$class = 'tab-item';
					if ( 0 === $count ) {
						hocwp_add_string_with_space_before( $class, 'active' );
					}
					$custom_link = hocwp_get_value_by_key( $tab, 'custom_link' );
					$data_toggle = 'tab';
					if ( ! empty( $custom_link ) ) {
						$href        = $custom_link;
						$data_toggle = '';
					} else {
						$href = '#' . $href;
					}
					?>
					<li class="<?php echo $class; ?>">
						<a href="<?php echo $href; ?>"
						   data-toggle="<?php echo $data_toggle; ?>"><?php echo $text; ?></a>
					</li>
					<?php
					$count ++;
				}
				?>
			</ul>
		<?php endif; ?>
		<?php
		$after_nav_tabs = hocwp_get_value_by_key( $args, 'after_nav_tabs' );
		if ( ! empty( $after_nav_tabs ) ) {
			echo '<div class="after-nav-tabs">';
			hocwp_the_custom_content( $after_nav_tabs );
			echo '</div>';
		}
		?>
		<div class="tab-content">
			<?php call_user_func( $callback, $args ); ?>
		</div>
	</div>
	<?php
}

function hocwp_modal_bootstrap( $args = array() ) {
	$id              = hocwp_get_value_by_key( $args, 'id' );
	$title           = hocwp_get_value_by_key( $args, 'title' );
	$container_class = hocwp_get_value_by_key( $args, 'container_class' );
	$callback        = hocwp_get_value_by_key( $args, 'callback' );
	$buttons         = hocwp_get_value_by_key( $args, 'buttons', array() );
	$close_text      = hocwp_get_value_by_key( $args, 'close_text', hocwp_get_value_by_key( $args, 'close_button_text', __( 'Close', 'hocwp-theme' ) ) );
	hocwp_add_string_with_space_before( $container_class, 'modal fade' );
	$container_class = trim( $container_class );
	if ( empty( $id ) || empty( $title ) || empty( $callback ) ) {
		return;
	}
	?>
	<div class="<?php echo $container_class; ?>" id="<?php echo $id; ?>" tabindex="-1" role="dialog"
	     aria-labelledby="<?php echo $id; ?>" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content clearfix">
				<div class="modal-header text-left">
					<button type="button" class="close" data-dismiss="modal"><span
							aria-hidden="true">&times;</span><span class="sr-only"><?php echo $close_text; ?></span>
					</button>
					<h4 class="modal-title"><?php echo $title; ?></h4>
				</div>
				<div class="modal-body">
					<?php call_user_func( $callback, $args ); ?>
				</div>
				<div class="modal-footer">
					<?php foreach ( $buttons as $button ) : ?>
						<?php
						$ajax_loading = '';
						if ( isset( $button['loading_image'] ) && (bool) $button['loading_image'] ) {
							$ajax_loading = hocwp_get_image_url( 'icon-loading-circle-16.gif' );
						}
						?>
						<button type="button"
						        class="btn <?php echo isset( $button['class'] ) ? $button['class'] : ''; ?>"><span
								class="text"><?php echo isset( $button['text'] ) ? $button['text'] : ''; ?></span><?php echo $ajax_loading; ?>
						</button>
					<?php endforeach; ?>
					<button type="button" class="btn btn-default"
					        data-dismiss="modal"><?php echo $close_text; ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function hocwp_get_copyright_text() {
	$text = '&copy; ' . date( 'Y' ) . ' ' . get_bloginfo( 'name' ) . '. All rights reserved.';

	return apply_filters( 'hocwp_copyright_text', $text );
}

function hocwp_sanitize( $data, $type ) {
	switch ( $type ) {
		case 'media':
			return hocwp_sanitize_media_value( $data );
		case 'text':
			return sanitize_text_field( trim( $data ) );
		case 'email':
			return sanitize_email( trim( $data ) );
		case 'file_name':
			return hocwp_sanitize_file_name( $data );
		case 'html_class':
			$data = hocwp_remove_vietnamese( $data );
			$data = hocwp_sanitize_id( $data );
			$data = str_replace( '_', '-', $data );

			return $data;
		case 'key':
			return sanitize_key( $data );
		case 'mime_type':
			return sanitize_mime_type( $data );
		case 'sql_orderby':
			return sanitize_sql_orderby( $data );
		case 'slug':
			return sanitize_title( $data );
		case 'title_for_query':
			return sanitize_title_for_query( $data );
		case 'html_id':
			return hocwp_sanitize_id( $data );
		case 'array':
			return hocwp_sanitize_array( $data );
		default:
			return $data;
	}
}

function hocwp_sanitize_html_class( $class ) {
	return hocwp_sanitize( $class, 'html_class' );
}

function hocwp_vietnamese_currency() {
	return apply_filters( 'hocwp_vietnamese_currency', '₫' );
}

function hocwp_number_format( $number ) {
	if ( 'vi' == hocwp_get_language() ) {
		return hocwp_number_format_vietnamese( $number );
	}

	return number_format( $number, 0 );
}

function hocwp_number_format_vietnamese_currency( $number ) {
	return hocwp_number_format_vietnamese( $number ) . hocwp_vietnamese_currency();
}

function hocwp_sanitize_form_post( $key, $type = 'default' ) {
	switch ( $type ) {
		case 'checkbox':
			return hocwp_get_method_value( $key );
		case 'datetime':
			return isset( $_POST[ $key ] ) ? strtotime( hocwp_string_to_datetime( $_POST[ $key ] ) ) : '';
		case 'timestamp':
			$value = isset( $_POST[ $key ] ) ? $_POST[ $key ] : '';
			$value = strtotime( $value );

			return $value;
		default:
			return isset( $_POST[ $key ] ) ? hocwp_sanitize( $_POST[ $key ], $type ) : '';
	}
}

function hocwp_sanitize_array( $arr, $deprecated = '', $deprecated = '' ) {
	if ( is_bool( $deprecated ) || '' !== $deprecated ) {
		_deprecated_argument( __FUNCTION__, '3.3.3' );
	}
	$arr = hocwp_to_array( $arr );

	return $arr;
}

function hocwp_sanitize_product_price( $regular, $sale, $id = '' ) {
	$regular_price = '';
	if ( hocwp_is_positive_number( $regular ) ) {
		$regular_price = $regular;
	}
	$sale_price = '';
	if ( hocwp_is_positive_number( $sale ) && $sale < $regular_price ) {
		$sale_price = $sale;
	}
	$price = $regular_price;
	if ( hocwp_is_positive_number( $sale_price ) ) {
		$price = $sale_price;
	}
	$result = array(
		'regular_price' => $regular_price,
		'sale_price'    => $sale_price,
		'price'         => $price
	);
	$result = apply_filters( 'hocwp_sanitize_product_price', $result, $regular, $sale, $id );

	return $result;
}

function hocwp_sanitize_size( $size ) {
	if ( is_string( $size ) ) {
		$size = explode( ',', $size );
	}
	$size = (array) $size;
	if ( isset( $size['size'] ) ) {
		$type = $size['size'];
		switch ( $type ) {
			case 'small':
				$width  = absint( get_option( 'thumbnail_size_w' ) );
				$height = absint( get_option( 'thumbnail_size_h' ) );
				if ( 0 != $width && 0 != $height ) {
					return array( $width, $height );
				}
				break;
			case 'medium':
				$width  = absint( get_option( 'medium_size_w' ) );
				$height = absint( get_option( 'medium_size_h' ) );
				if ( 0 != $width && 0 != $height ) {
					return array( $width, $height );
				}
				break;
			case 'large':
				$width  = absint( get_option( 'large_size_w' ) );
				$height = absint( get_option( 'large_size_h' ) );
				if ( 0 != $width && 0 != $height ) {
					return array( $width, $height );
				}
				break;
		}
	}
	$width = intval( isset( $size[0] ) ? $size[0] : 0 );
	if ( 0 == $width && isset( $size['width'] ) ) {
		$width = $size['width'];
	}
	$height = intval( isset( $size[1] ) ? $size[1] : $width );
	if ( 0 != $width && ( 0 == $height || $height == $width ) && isset( $size['height'] ) ) {
		$height = $size['height'];
	}

	return array( $width, $height );
}

function hocwp_sanitize_callback( $args ) {
	$callback = isset( $args['func'] ) ? $args['func'] : '';
	if ( empty( $callback ) ) {
		$callback = isset( $args['callback'] ) ? $args['callback'] : '';
	}

	return $callback;
}

function hocwp_sanitize_callback_args( $args ) {
	$func = isset( $args['func_args'] ) ? $args['func_args'] : '';
	if ( empty( $func ) ) {
		$func = isset( $args['callback_args'] ) ? $args['callback_args'] : '';
	}

	return $func;
}

function hocwp_get_browser() {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone, $is_winIE, $is_macIE;
	$user_agent = strtolower( $_SERVER['HTTP_USER_AGENT'] );
	$browser    = 'unknown';
	if ( $is_lynx ) {
		$browser = 'lynx';
	} elseif ( $is_gecko ) {
		$browser = 'gecko';
		if ( false !== strpos( $user_agent, 'firefox' ) ) {
			$browser = 'firefox';
		}
	} elseif ( $is_opera ) {
		$browser = 'opera';
	} elseif ( $is_NS4 ) {
		$browser = 'ns4';
	} elseif ( $is_safari ) {
		$browser = 'safari';
	} elseif ( $is_chrome ) {
		$browser = 'chrome';
		if ( false !== strpos( $user_agent, 'edge/' ) ) {
			$browser = 'edge';
		}
	} elseif ( $is_winIE ) {
		$browser = 'win-ie';
	} elseif ( $is_macIE ) {
		$browser = 'mac-ie';
	} elseif ( $is_IE ) {
		$browser = 'ie';
	} elseif ( $is_iphone ) {
		$browser = 'iphone';
	}
	if ( 'unknown' == $browser ) {
		if ( false !== strpos( $user_agent, 'edge/' ) ) {
			$browser = 'edge';
		}
	}

	return $browser;
}

function hocwp_get_datetime_ago( $ago, $datetime = '' ) {
	if ( empty( $datetime ) ) {
		$datetime = hocwp_get_current_datetime_mysql();
	}

	return date( 'Y-m-d H:i:s', strtotime( $ago, strtotime( $datetime ) ) );
}

function hocwp_get_current_url() {
	global $wp;
	$request = $wp->request;
	if ( empty( $request ) ) {
		$uri        = basename( $_SERVER['REQUEST_URI'] );
		$first_char = hocwp_get_first_char( $uri );
		if ( '?' === $first_char ) {
			$uri = hocwp_remove_first_char( $uri, '?' );
		} else {
			$parts = explode( '?', $uri );
			if ( count( $parts ) == 2 ) {
				$uri = $parts[1];
			}
		}
		$request     = '?' . $uri;
		$current_url = trailingslashit( home_url() ) . $request;
	} else {
		$current_url = trailingslashit( home_url( $request ) );
	}
	$current_url = apply_filters( 'hocwp_current_url', $current_url );

	return $current_url;
}

function hocwp_get_current_visitor_location() {
	$result = array();
	$title  = __( 'Unknown location', 'hocwp-theme' );
	$url    = hocwp_get_current_url();
	if ( is_home() ) {
		$title = __( 'Viewing index', 'hocwp-theme' );
	} elseif ( is_archive() ) {
		$title = sprintf( __( 'Viewing %s', 'hocwp-theme' ), get_the_archive_title() );
	} elseif ( is_singular() ) {
		$title = sprintf( __( 'Viewing %s', 'hocwp-theme' ), get_the_title() );
	} elseif ( is_search() ) {
		$title = __( 'Viewing search result', 'hocwp-theme' );
	} elseif ( is_404() ) {
		$title = __( 'Viewing 404 page not found', 'hocwp-theme' );
	}
	$result['object'] = get_queried_object();
	$result['url']    = $url;
	$result['title']  = $title;

	return $result;
}

function hocwp_human_time_diff_to_now( $from ) {
	if ( ! is_int( $from ) ) {
		$from = strtotime( $from );
	}

	return human_time_diff( $from, strtotime( hocwp_get_current_datetime_mysql() ) );
}

function hocwp_get_safe_captcha_characters() {
	$characters = hocwp_get_safe_characters();
	$excludes   = array(
		'b',
		'd',
		'e',
		'i',
		'j',
		'l',
		'o',
		'w',
		'B',
		'D',
		'E',
		'I',
		'J',
		'L',
		'O',
		'W',
		'0',
		'1',
		'2',
		'8'
	);
	$excludes   = apply_filters( 'hocwp_exclude_captcha_characters', $excludes );
	$characters = str_replace( $excludes, '', $characters );

	return $characters;
}

function hocwp_check_captcha( $captcha_code = '' ) {
	if ( empty( $captcha_code ) ) {
		$captcha_code = hocwp_get_method_value( 'captcha', 'request' );
	}
	$captcha = new HOCWP_Captcha();
	if ( $captcha->check( $captcha_code ) ) {
		return true;
	}

	return false;
}

function hocwp_is_mobile_domain_blog() {
	return hocwp_is_mobile_domain( get_bloginfo( 'url' ) );
}

function hocwp_is_site_domain( $domain ) {
	$site_domain = hocwp_get_root_domain_name( home_url() );
	$domain      = hocwp_get_root_domain_name( $domain );
	if ( $domain == $site_domain ) {
		return true;
	}

	return false;
}

function hocwp_empty_database_table( $table ) {
	global $wpdb;

	return $wpdb->query( "TRUNCATE TABLE $table" );
}

function hocwp_build_widget_class( $widget_id ) {
	$widget_class = explode( '-', $widget_id );
	array_pop( $widget_class );
	if ( is_array( $widget_class ) ) {
		$widget_class = implode( '-', $widget_class );
	} else {
		$widget_class = (string) $widget_class;
	}
	$widget_class = trim( trim( trim( $widget_class, '_' ), '-' ) );
	$widget_class = 'widget_' . $widget_class;

	return $widget_class;
}

function hocwp_get_current_post_type() {
	global $post_type;
	$result = $post_type;
	if ( empty( $result ) ) {
		if ( isset( $_GET['post_type'] ) ) {
			$result = $_GET['post_type'];
		} else {
			$action  = isset( $_GET['action'] ) ? $_GET['action'] : '';
			$post_id = isset( $_GET['post'] ) ? $_GET['post'] : 0;
			if ( 'edit' == $action && is_numeric( $post_id ) && $post_id > 0 ) {
				$post   = get_post( $post_id );
				$result = $post->post_type;
			}
		}
	}

	return $result;
}

function hocwp_get_current_new_post() {
	global $pagenow;
	$result = null;
	if ( 'post-new.php' == $pagenow ) {
		$query_args = array(
			'post_status'    => 'auto-draft',
			'orderby'        => 'date',
			'order'          => 'desc',
			'posts_per_page' => 1,
			'cache'          => false
		);
		$post_type  = hocwp_get_current_post_type();
		if ( ! empty( $post_type ) ) {
			$query_args['post_type'] = $post_type;
		}
		$query  = hocwp_query( $query_args );
		$result = array_shift( $query->posts );
	}

	return $result;
}

function hocwp_get_post_types( $args = array() ) {
	$defaults = array( '_builtin' => false, 'public' => true );
	$args     = wp_parse_args( $args, $defaults );

	return get_post_types( $args, 'objects' );
}

function hocwp_register_sidebar( $sidebar_id, $sidebar_name, $sidebar_description = '', $html_tag = 'aside' ) {
	$widget_class = apply_filters( 'hocwp_widget_class', '', $sidebar_id );
	hocwp_add_string_with_space_before( $widget_class, 'widget' );
	$before_widget = apply_filters( 'hocwp_before_widget', '<' . $html_tag . ' id="%1$s" class="' . $widget_class . ' %2$s">' );
	$before_widget = apply_filters( 'hocwp_sidebar_' . $sidebar_id . '_before_widget', $before_widget );
	$after_widget  = apply_filters( 'hocwp_after_widget', '</' . $html_tag . '>' );
	$after_widget  = apply_filters( 'hocwp_sidebar_' . $sidebar_id . '_after_widget', $after_widget );
	$before_title  = apply_filters( 'hocwp_widget_before_title', '<h4 class="widget-title widgettitle">' );
	$before_title  = apply_filters( 'hocwp_sidebar_' . $sidebar_id . '_widget_before_title', $before_title );
	$after_title   = apply_filters( 'hocwp_widget_after_title', '</h4>' );
	$after_title   = apply_filters( 'hocwp_sidebar_' . $sidebar_id . '_widget_after_title', $after_title );
	$sidebar_args  = array(
		'name'          => $sidebar_name,
		'id'            => $sidebar_id,
		'description'   => $sidebar_description,
		'before_widget' => $before_widget,
		'after_widget'  => $after_widget,
		'before_title'  => $before_title,
		'after_title'   => $after_title,
	);
	$sidebar_args  = apply_filters( 'hocwp_sidebar_args', $sidebar_args );
	$sidebar_args  = apply_filters( 'hocwp_sidebar_' . $sidebar_id . '_args', $sidebar_args );
	register_sidebar( $sidebar_args );
}

function hocwp_register_widget( $class_name ) {
	if ( class_exists( $class_name ) ) {
		register_widget( $class_name );
	}
}

function hocwp_register_post_type_normal( $args ) {
	$defaults = array(
		'supports'          => array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
			'custom-fields',
			'comments',
			'revisions'
		),
		'show_in_nav_menus' => true,
		'show_in_admin_bar' => true
	);
	$args     = wp_parse_args( $args, $defaults );
	hocwp_register_post_type( $args );
}

function hocwp_register_post_type( $args = array() ) {
	$args                = apply_filters( 'hocwp_post_type_args', $args );
	$name                = isset( $args['name'] ) ? $args['name'] : '';
	$singular_name       = isset( $args['singular_name'] ) ? $args['singular_name'] : '';
	$menu_name           = hocwp_get_value_by_key( $args, 'menu_name', $name );
	$supports            = isset( $args['supports'] ) ? $args['supports'] : array();
	$hierarchical        = isset( $args['hierarchical'] ) ? $args['hierarchical'] : false;
	$public              = isset( $args['public'] ) ? $args['public'] : true;
	$show_ui             = isset( $args['show_ui'] ) ? $args['show_ui'] : true;
	$show_in_menu        = isset( $args['show_in_menu'] ) ? $args['show_in_menu'] : true;
	$show_in_nav_menus   = isset( $args['show_in_nav_menus'] ) ? $args['show_in_nav_menus'] : false;
	$show_in_admin_bar   = isset( $args['show_in_admin_bar'] ) ? $args['show_in_admin_bar'] : false;
	$menu_position       = isset( $args['menu_position'] ) ? $args['menu_position'] : 6;
	$can_export          = isset( $args['can_export'] ) ? $args['can_export'] : true;
	$has_archive         = isset( $args['has_archive'] ) ? $args['has_archive'] : true;
	$exclude_from_search = isset( $args['exclude_from_search'] ) ? $args['exclude_from_search'] : false;
	$publicly_queryable  = isset( $args['publicly_queryable'] ) ? $args['publicly_queryable'] : true;
	$capability_type     = isset( $args['capability_type'] ) ? $args['capability_type'] : 'post';
	$taxonomies          = isset( $args['taxonomies'] ) ? $args['taxonomies'] : array();
	$menu_icon           = isset( $args['menu_icon'] ) ? $args['menu_icon'] : 'dashicons-admin-post';
	$slug                = isset( $args['slug'] ) ? $args['slug'] : '';
	$with_front          = isset( $args['with_front'] ) ? $args['with_front'] : true;
	$pages               = isset( $args['pages'] ) ? $args['pages'] : true;
	$feeds               = isset( $args['feeds'] ) ? $args['feeds'] : true;
	$query_var           = isset( $args['query_var'] ) ? $args['query_var'] : '';
	$capabilities        = isset( $args['capabilities'] ) ? $args['capabilities'] : array();
	$custom_labels       = hocwp_get_value_by_key( $args, 'labels' );
	$custom_labels       = hocwp_sanitize_array( $custom_labels );
	$show_in_rest        = hocwp_get_value_by_key( $args, 'show_in_rest', true );

	if ( empty( $singular_name ) ) {
		$singular_name = $name;
	}
	if ( empty( $name ) || ! is_array( $supports ) || empty( $slug ) || post_type_exists( $slug ) ) {
		return;
	}
	if ( ! in_array( 'title', $supports ) ) {
		array_push( $supports, 'title' );
	}
	$post_type = isset( $args['post_type'] ) ? $args['post_type'] : $slug;
	$post_type = hocwp_sanitize_id( $post_type );
	if ( post_type_exists( $post_type ) ) {
		return;
	}
	$labels = array(
		'name'               => $name,
		'singular_name'      => $singular_name,
		'menu_name'          => $menu_name,
		'name_admin_bar'     => isset( $args['name_admin_bar'] ) ? $args['name_admin_bar'] : $singular_name,
		'all_items'          => sprintf( __( 'All %s', 'hocwp-theme' ), $name ),
		'add_new'            => __( 'Add New', 'hocwp-theme' ),
		'add_new_item'       => sprintf( __( 'Add New %s', 'hocwp-theme' ), $singular_name ),
		'edit_item'          => sprintf( __( 'Edit %s', 'hocwp-theme' ), $singular_name ),
		'new_item'           => sprintf( __( 'New %s', 'hocwp-theme' ), $singular_name ),
		'view_item'          => sprintf( __( 'View %s', 'hocwp-theme' ), $singular_name ),
		'search_items'       => sprintf( __( 'Search %s', 'hocwp-theme' ), $singular_name ),
		'not_found'          => __( 'Not found', 'hocwp-theme' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'hocwp-theme' ),
		'parent_item_colon'  => sprintf( __( 'Parent %s:', 'hocwp-theme' ), $singular_name ),
		'parent_item'        => sprintf( __( 'Parent %s', 'hocwp-theme' ), $singular_name ),
		'update_item'        => sprintf( __( 'Update %s', 'hocwp-theme' ), $singular_name )
	);
	$labels = wp_parse_args( $custom_labels, $labels );

	$rewrite_slug     = str_replace( '_', '-', $slug );
	$rewrite_slug     = apply_filters( 'hocwp_post_type_slug', $rewrite_slug, $post_type );
	$rewrite_slug     = apply_filters( 'hocwp_post_type_' . $post_type . '_slug', $rewrite_slug, $args );
	$rewrite_defaults = array(
		'slug'       => $rewrite_slug,
		'with_front' => $with_front,
		'pages'      => $pages,
		'feeds'      => $feeds
	);
	$rewrite          = isset( $args['rewrite'] ) ? $args['rewrite'] : array();
	$rewrite          = wp_parse_args( $rewrite, $rewrite_defaults );
	if ( ! $public ) {
		$rewrite   = false;
		$query_var = false;
	}
	$description = isset( $args['description'] ) ? $args['description'] : '';
	$args        = array(
		'labels'              => $labels,
		'description'         => $description,
		'supports'            => $supports,
		'taxonomies'          => $taxonomies,
		'hierarchical'        => $hierarchical,
		'public'              => $public,
		'show_ui'             => $show_ui,
		'show_in_menu'        => $show_in_menu,
		'show_in_nav_menus'   => $show_in_nav_menus,
		'show_in_admin_bar'   => $show_in_admin_bar,
		'menu_position'       => $menu_position,
		'menu_icon'           => $menu_icon,
		'can_export'          => $can_export,
		'has_archive'         => $has_archive,
		'exclude_from_search' => $exclude_from_search,
		'publicly_queryable'  => $publicly_queryable,
		'query_var'           => $query_var,
		'rewrite'             => $rewrite,
		'capability_type'     => $capability_type
	);
	if ( $show_in_rest ) {
		$rest_base = $rewrite_slug;
		if ( 'api' != $rest_base ) {
			$rest_base .= '-api';
		}
		$args['show_in_rest']          = true;
		$args['rest_base']             = $rest_base;
		$args['rest_controller_class'] = 'WP_REST_Posts_Controller';
	}
	if ( count( $capabilities ) > 0 ) {
		$args['capabilities'] = $capabilities;
	}
	register_post_type( $post_type, $args );
}

function hocwp_register_taxonomy( $args = array() ) {
	$old_args          = $args;
	$name              = isset( $args['name'] ) ? $args['name'] : '';
	$singular_name     = isset( $args['singular_name'] ) ? $args['singular_name'] : '';
	$menu_name         = hocwp_get_value_by_key( $args, 'menu_name', $name );
	$hierarchical      = isset( $args['hierarchical'] ) ? $args['hierarchical'] : true;
	$public            = isset( $args['public'] ) ? $args['public'] : true;
	$show_ui           = isset( $args['show_ui'] ) ? $args['show_ui'] : true;
	$show_admin_column = isset( $args['show_admin_column'] ) ? $args['show_admin_column'] : true;
	$show_in_nav_menus = isset( $args['show_in_nav_menus'] ) ? $args['show_in_nav_menus'] : true;
	$show_tagcloud     = isset( $args['show_tagcloud'] ) ? $args['show_tagcloud'] : ( ( $hierarchical === true ) ? false : true );
	$post_types        = isset( $args['post_types'] ) ? $args['post_types'] : array();
	if ( ! is_array( $post_types ) ) {
		$post_types = array( $post_types );
	}
	if ( ! hocwp_array_has_value( $post_types ) ) {
		$post_type  = hocwp_get_value_by_key( $args, 'post_type' );
		$post_types = array( $post_type );
	}
	$slug    = isset( $args['slug'] ) ? $args['slug'] : '';
	$private = isset( $args['private'] ) ? $args['private'] : false;
	if ( empty( $singular_name ) ) {
		$singular_name = $name;
	}
	if ( empty( $slug ) ) {
		$slug = $singular_name;
	}
	if ( empty( $name ) || empty( $slug ) || taxonomy_exists( $slug ) ) {
		return;
	}
	$taxonomy = isset( $args['taxonomy'] ) ? $args['taxonomy'] : $slug;
	$taxonomy = hocwp_sanitize_id( $taxonomy );
	if ( taxonomy_exists( $taxonomy ) ) {
		return;
	}
	$labels          = array(
		'name'                       => $name,
		'singular_name'              => $singular_name,
		'menu_name'                  => $menu_name,
		'all_items'                  => sprintf( __( 'All %s', 'hocwp-theme' ), $name ),
		'edit_item'                  => sprintf( __( 'Edit %s', 'hocwp-theme' ), $singular_name ),
		'view_item'                  => sprintf( __( 'View %s', 'hocwp-theme' ), $singular_name ),
		'update_item'                => sprintf( __( 'Update %s', 'hocwp-theme' ), $singular_name ),
		'add_new_item'               => sprintf( __( 'Add New %s', 'hocwp-theme' ), $singular_name ),
		'new_item_name'              => sprintf( __( 'New %s Name', 'hocwp-theme' ), $singular_name ),
		'parent_item'                => sprintf( __( 'Parent %s', 'hocwp-theme' ), $singular_name ),
		'parent_item_colon'          => sprintf( __( 'Parent %s:', 'hocwp-theme' ), $singular_name ),
		'search_items'               => sprintf( __( 'Search %s', 'hocwp-theme' ), $name ),
		'popular_items'              => sprintf( __( 'Popular %s', 'hocwp-theme' ), $name ),
		'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'hocwp-theme' ), hocwp_strtolower( $name ) ),
		'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'hocwp-theme' ), $name ),
		'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', 'hocwp-theme' ), $name ),
		'not_found'                  => __( 'Not Found', 'hocwp-theme' ),
	);
	$rewrite         = isset( $args['rewrite'] ) ? $args['rewrite'] : array();
	$rewrite_slug    = str_replace( '_', '-', $slug );
	$rewrite_slug    = apply_filters( 'hocwp_taxonomy_slug', $rewrite_slug, $taxonomy );
	$rewrite_slug    = apply_filters( 'hocwp_taxonomy_' . $taxonomy . '_slug', $rewrite_slug, $args );
	$rewrite['slug'] = $rewrite_slug;
	if ( $private ) {
		$public  = false;
		$rewrite = false;
	}
	$update_count_callback = isset( $args['update_count_callback'] ) ? $args['update_count_callback'] : '_update_post_term_count';
	$capabilities          = isset( $args['capabilities'] ) ? $args['capabilities'] : array( 'manage_terms' );
	$show_in_rest          = hocwp_get_value_by_key( $args, 'show_in_rest', true );
	$args                  = array(
		'labels'                => $labels,
		'hierarchical'          => $hierarchical,
		'public'                => $public,
		'show_ui'               => $show_ui,
		'show_admin_column'     => $show_admin_column,
		'show_in_nav_menus'     => $show_in_nav_menus,
		'show_tagcloud'         => $show_tagcloud,
		'query_var'             => true,
		'rewrite'               => $rewrite,
		'update_count_callback' => $update_count_callback,
		'capabilities'          => $capabilities
	);
	if ( $show_in_rest ) {
		$args['show_in_rest']          = true;
		$args['rest_base']             = $rewrite_slug . '-api';
		$args['rest_controller_class'] = 'WP_REST_Terms_Controller';
	}
	$args = wp_parse_args( $old_args, $args );
	unset( $args['name'] );
	unset( $args['singular_name'] );
	register_taxonomy( $taxonomy, $post_types, $args );
}

function hocwp_register_taxonomy_private( $args = array() ) {
	$args['exclude_from_search'] = true;
	$args['show_in_quick_edit']  = false;
	$args['show_in_nav_menus']   = false;
	$args['show_admin_column']   = false;
	$args['show_in_admin_bar']   = false;
	$args['menu_position']       = 9999999;
	$args['show_tagcloud']       = false;
	$args['has_archive']         = false;
	$args['query_var']           = false;
	$args['rewrite']             = false;
	$args['public']              = false;
	$args['feeds']               = false;
	hocwp_register_taxonomy( $args );
}

function hocwp_register_post_type_private( $args = array() ) {
	global $hocwp_private_post_types;
	$args['public']              = false;
	$args['exclude_from_search'] = true;
	$args['show_in_nav_menus']   = false;
	$args['show_in_admin_bar']   = false;
	$args['menu_position']       = 9999999;
	$args['has_archive']         = false;
	$args['query_var']           = false;
	$args['rewrite']             = false;
	$args['feeds']               = false;
	$slug                        = isset( $args['slug'] ) ? $args['slug'] : '';
	if ( ! empty( $slug ) ) {
		$hocwp_private_post_types   = hocwp_sanitize_array( $hocwp_private_post_types );
		$hocwp_private_post_types[] = $slug;
	}
	hocwp_register_post_type( $args );
}

function hocwp_is_debugging() {
	return ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? true : false;
}

function hocwp_the_posts_navigation() {
	the_posts_pagination( array(
		'prev_text'          => esc_html__( 'Previous page', 'hocwp-theme' ),
		'next_text'          => esc_html__( 'Next page', 'hocwp-theme' ),
		'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'hocwp-theme' ) . ' </span>'
	) );
}

function hocwp_comments_template( $args = array() ) {
	$post_id = hocwp_get_value_by_key( $args, 'post_id', get_the_ID() );
	$cpost   = get_post( $post_id );
	if ( ! is_a( $cpost, 'WP_Post' ) ) {
		return;
	}
	if ( comments_open( $post_id ) || get_comments_number( $post_id ) ) {
		$comment_system = hocwp_theme_get_option( 'comment_system', 'discussion' );
		$tabs           = hocwp_get_value_by_key( $args, 'tabs' );
		if ( 'tabs' == $comment_system || hocwp_array_has_value( $tabs ) ) {
			if ( ! isset( $args['callback'] ) ) {
				$args['callback'] = 'hocwp_comment_tabs_callback';
			}
			if ( 'tabs' == $comment_system && ! isset( $args['tabs'] ) ) {
				$tabs         = array(
					array(
						'href' => 'facebook',
						'text' => 'Facebook'
					),
					array(
						'href' => 'google',
						'text' => 'Google+'
					),
					array(
						'href' => 'wordpress',
						'text' => 'WordPress'
					),
					array(
						'href' => 'disqus',
						'text' => 'Disqus'
					)
				);
				$tabs         = apply_filters( 'hocwp_comment_tabs', $tabs );
				$args['tabs'] = $tabs;
			}
			hocwp_tab_content_bootstrap( $args );
		} else {
			if ( 'facebook' == $comment_system ) {
				hocwp_facebook_comment();
			} else {
				if ( 'default_and_facebook' == $comment_system ) {
					hocwp_facebook_comment();
				}
				comments_template();
			}
		}
	}
}

function hocwp_comment_tabs_callback( $args ) {
	$tabs = hocwp_get_value_by_key( $args, 'tabs' );
	if ( hocwp_array_has_value( $tabs ) ) {
		$count = 0;
		foreach ( $tabs as $tab ) {
			$class = 'tab-pane';
			if ( 0 == $count ) {
				hocwp_add_string_with_space_before( $class, 'active' );
			}
			$href = hocwp_get_value_by_key( $tab, 'href' );
			if ( empty( $href ) ) {
				continue;
			}
			echo '<div id="' . $href . '" class="' . $class . '">';
			switch ( $href ) {
				case 'facebook':
					hocwp_facebook_comment();
					break;
				case 'google_plus':
				case 'gplus':
				case 'google':
					hocwp_google_comment();
					break;
				case 'disqus':
					hocwp_disqus_comment();
					break;
				default:
					comments_template();
			}
			echo '</div>';
			$count ++;
		}
	}
}

function hocwp_wp_link_pages() {
	wp_link_pages( array(
		'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'hocwp-theme' ) . '</span>',
		'after'       => '</div>',
		'link_before' => '<span>',
		'link_after'  => '</span>',
		'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'hocwp-theme' ) . ' </span>%',
		'separator'   => '<span class="screen-reader-text">, </span>',
	) );
}

function hocwp_comment_nav() {
	if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
		?>
		<nav class="navigation comment-navigation">
			<h2 class="screen-reader-text"><?php echo apply_filters( 'hocwp_comment_navigation_text', __( 'Comment navigation', 'hocwp-theme' ) ); ?></h2>

			<div class="nav-links">
				<?php
				if ( $prev_link = get_previous_comments_link( apply_filters( 'hocwp_comment_navigation_prev_text', esc_html__( 'Older Comments', 'hocwp-theme' ) ) ) ) {
					printf( '<div class="nav-previous">%s</div>', $prev_link );
				}
				if ( $next_link = get_next_comments_link( apply_filters( 'hocwp_comment_navigation_next_text', esc_html__( 'Newer Comments', 'hocwp-theme' ) ) ) ) {
					printf( '<div class="nav-next">%s</div>', $next_link );
				}
				?>
			</div>
			<!-- .nav-links -->
		</nav><!-- .comment-navigation -->
		<?php
	endif;
}

function hocwp_get_current_weekday( $format = 'd/m/Y H:i:s', $args = array() ) {
	$weekday   = hocwp_get_current_date( 'l' );
	$separator = isset( $args['separator'] ) ? $args['separator'] : ', ';
	$weekday   = hocwp_convert_day_name_to_vietnamese( $weekday );

	return $weekday . $separator . hocwp_get_current_date( $format );
}

function hocwp_current_weekday( $format = 'd/m/Y H:i:s', $args = array() ) {
	echo hocwp_get_current_weekday( $format, $args );
}

function hocwp_the_social_share_buttons( $args = array() ) {
	$socials = hocwp_get_value_by_key( $args, 'socials' );
	if ( ! hocwp_array_has_value( $socials ) ) {
		$socials = array(
			'facebook'   => 'Facebook',
			'twitter'    => 'Twitter',
			'googleplus' => 'Google+',
			'pinterest'  => 'Pinterest',
			'email'      => 'Email'
		);
		$socials = apply_filters( 'hocwp_social_share_buttons', $socials );
	}
	?>
	<div class="social-share">
		<ul class="list-inline list-unstyled list-share-buttons">
			<?php
			foreach ( $socials as $social_name => $text ) {
				$font_awesome = 'fa-' . $social_name;
				$btn_class    = 'btn-' . $social_name;
				switch ( $social_name ) {
					case 'email':
						hocwp_add_string_with_space_before( $font_awesome, 'fa-envelope' );
						break;
					case 'googleplus':
					case 'gplus':
						hocwp_add_string_with_space_before( $font_awesome, 'fa-google-plus' );
						hocwp_add_string_with_space_before( $btn_class, 'btn-google-plus' );
						break;
				}
				echo '<li><a target="_blank" href="' . hocwp_get_social_share_url( array( 'social_name' => $social_name ) ) . '" class="btn btn-social ' . $btn_class . '"><i class="fa ' . $font_awesome . ' icon-left"></i> ' . $text . '</a></li>';
			}
			?>
		</ul>
	</div>
	<?php
}

function hocwp_get_social_share_url( $args = array() ) {
	$result          = '';
	$title           = hocwp_get_value_by_key( $args, 'title', get_the_title() );
	$permalink       = hocwp_get_value_by_key( $args, 'permalink', get_the_permalink() );
	$social_name     = hocwp_get_value_by_key( $args, 'social_name' );
	$thumbnail       = hocwp_get_value_by_key( $args, 'thumbnail' );
	$excerpt         = hocwp_get_value_by_key( $args, 'excerpt', get_the_excerpt() );
	$language        = hocwp_get_value_by_key( $args, 'language', hocwp_get_language() );
	$twitter_account = hocwp_get_value_by_key( $args, 'twitter_account', 'skylarkcob' );
	$permalink       = urlencode( $permalink );
	if ( empty( $twitter_account ) ) {
		$twitter_account = hocwp_get_wpseo_social_value( 'twitter_site' );
		$twitter_account = basename( $twitter_account );
	}
	switch ( $social_name ) {
		case 'email':
			$result = 'mailto:email@hocwp.net?subject=' . $title . '&amp;body=' . $permalink;
			break;
		case 'facebook':
			$url = 'https://www.facebook.com/sharer/sharer.php';
			$url = add_query_arg( 'u', $permalink, $url );
			if ( ! empty( $title ) ) {
				$url = add_query_arg( 't', $title, $url );
			}
			$result = $url;
			break;
		case 'gplus':
		case 'googleplus':
			$url    = 'http://plusone.google.com/_/+1/confirm';
			$url    = add_query_arg( 'hl', $language, $url );
			$url    = add_query_arg( 'url', $permalink, $url );
			$result = $url;
			break;
		case 'twitter':
			$url = 'http://twitter.com/share';
			$url = add_query_arg( 'url', $permalink, $url );
			if ( ! empty( $title ) ) {
				$url = add_query_arg( 'text', $title, $url );
			}
			$url    = add_query_arg( 'via', $twitter_account, $url );
			$result = $url;
			break;
		case 'pinterest':
			$url = 'http://www.pinterest.com/pin/create/button';
			if ( ! empty( $thumbnail ) ) {
				$url = add_query_arg( 'media', $thumbnail, $url );
			}
			$url = add_query_arg( 'url', $permalink, $url );
			if ( ! empty( $title ) ) {
				$url = add_query_arg( 'description', $title . ' ' . $permalink, $url );
			}
			$result = $url;
			break;
		case 'zingme':
			$url = 'http://link.apps.zing.vn/share';
			if ( ! empty( $title ) ) {
				$url = add_query_arg( 't', $title, $url );
			}
			$url = add_query_arg( 'u', $permalink, $url );
			if ( ! empty( $excerpt ) ) {
				$url = add_query_arg( 'desc', $excerpt, $url );
			}
			$result = $url;
			break;
	}

	return $result;
}

function hocwp_menu_page_exists( $slug ) {
	if ( empty( $GLOBALS['admin_page_hooks'][ $slug ] ) ) {
		return false;
	}

	return true;
}

function hocwp_get_menu_items_by_location( $location, $args = array() ) {
	$result = array();
	if ( $location && ( $locations = get_nav_menu_locations() ) && isset( $locations[ $location ] ) ) {
		$menu   = wp_get_nav_menu_object( $locations[ $location ] );
		$result = wp_get_nav_menu_items( $menu->term_id, $args );
	}

	return $result;
}

function hocwp_get_menu_items_by_type( $location, $type, $object, $object_id, $args = array() ) {
	$items  = hocwp_get_menu_items_by_location( $location, $args );
	$result = array();
	if ( hocwp_array_has_value( $items ) ) {
		foreach ( $items as $item ) {
			if ( $item->type == $type && $item->object == $object ) {
				if ( $object_id == $item->object_id ) {
					$result[] = $item;
				}
			}
		}
	}

	return $result;
}

function hocwp_get_menu_items_by_term( $location, $term, $args = array() ) {
	$result = hocwp_get_menu_items_by_type( $location, 'taxonomy', $term->taxonomy, $term->term_id, $args );

	return $result;
}

function hocwp_get_child_menu_items( $location, $parent, $args = array() ) {
	$result = array();
	if ( is_nav_menu_item( $parent ) ) {
		$items = hocwp_get_menu_items_by_location( $location, $args );
		if ( hocwp_array_has_value( $items ) ) {
			foreach ( $items as $item ) {
				if ( $item->menu_item_parent == $parent->ID ) {
					$result[] = $item;
				}
			}
		}
	}

	return $result;
}

function hocwp_get_parent_menu_item( $menu_item ) {
	$result = null;
	if ( hocwp_is_post( $menu_item ) && is_nav_menu_item( $menu_item->ID ) ) {
		if ( $menu_item->menu_item_parent && hocwp_id_number_valid( $menu_item->menu_item_parent ) ) {
			$parent = get_post( $menu_item->menu_item_parent );
			if ( is_nav_menu_item( $parent ) ) {
				$result = wp_setup_nav_menu_item( $parent );
			}
		}
	}

	return $result;
}

function hocwp_get_top_parent_menu_item( $menu_item ) {
	$item = hocwp_get_parent_menu_item( $menu_item );
	if ( is_nav_menu_item( $item ) ) {
		while ( $item->menu_item_parent && hocwp_id_number_valid( $item->menu_item_parent ) ) {
			$item = hocwp_get_parent_menu_item( $item );
		}
	}

	return $item;
}

function hocwp_get_current_admin_page() {
	return isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';
}

function hocwp_is_current_admin_page( $page ) {
	$admin_page = hocwp_get_current_admin_page();
	if ( ! empty( $admin_page ) && $admin_page == $page ) {
		return true;
	}

	return false;
}

function hocwp_get_plugins( $folder = '' ) {
	if ( ! function_exists( 'get_plugins' ) ) {
		require( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	return get_plugins( $folder );
}

function hocwp_get_my_plugins() {
	$result = array();
	$lists  = hocwp_get_plugins();
	foreach ( $lists as $file => $plugin ) {
		if ( hocwp_is_my_plugin( $plugin ) ) {
			$result[ $file ] = $plugin;
		}
	}

	return $result;
}

function hocwp_is_my_plugin( $plugin_data ) {
	$result     = false;
	$author_uri = isset( $plugin_data['AuthorURI'] ) ? $plugin_data['AuthorURI'] : '';
	if ( hocwp_get_root_domain_name( $author_uri ) == hocwp_get_root_domain_name( HOCWP_HOMEPAGE ) ) {
		$result = true;
	}

	return $result;
}

function hocwp_is_my_theme( $stylesheet = null, $theme_root = null ) {
	$result      = false;
	$theme       = wp_get_theme( $stylesheet, $theme_root );
	$theme_uri   = $theme->get( 'ThemeURI' );
	$text_domain = $theme->get( 'TextDomain' );
	$author_uri  = $theme->get( 'AuthorURI' );
	if ( ( hocwp_string_contain( $theme_uri, 'hocwp-theme' ) && hocwp_string_contain( $author_uri, 'hocwp-theme' ) ) || ( hocwp_string_contain( $text_domain, 'hocwp-theme' ) && hocwp_string_contain( $theme_uri, 'hocwp-theme' ) ) || ( hocwp_string_contain( $text_domain, 'hocwp-theme' ) && hocwp_string_contain( $author_uri, 'hocwp-theme' ) ) ) {
		$result = true;
	}

	return $result;
}

function hocwp_has_plugin() {
	$result  = false;
	$plugins = hocwp_get_plugins();
	foreach ( $plugins as $plugin ) {
		if ( hocwp_is_my_plugin( $plugin ) ) {
			$result = true;
			break;
		}
	}

	return $result;
}

function hocwp_has_plugin_activated() {
	$plugins = get_option( 'active_plugins' );
	if ( hocwp_array_has_value( $plugins ) ) {
		foreach ( $plugins as $base_name ) {
			if ( hocwp_string_contain( $base_name, 'hocwp-theme' ) ) {
				return true;
			}
		}
	}

	return false;
}

function hocwp_admin_notice( $args = array() ) {
	$class = isset( $args['class'] ) ? $args['class'] : '';
	hocwp_add_string_with_space_before( $class, 'updated notice' );
	$error      = isset( $args['error'] ) ? (bool) $args['error'] : false;
	$type       = isset( $args['type'] ) ? $args['type'] : 'default';
	$bs_callout = 'bs-callout-' . $type;
	hocwp_add_string_with_space_before( $class, $bs_callout );
	if ( $error ) {
		hocwp_add_string_with_space_before( $class, 'settings-error error' );
	}
	$dismissible = isset( $args['dismissible'] ) ? (bool) $args['dismissible'] : true;
	if ( $dismissible ) {
		hocwp_add_string_with_space_before( $class, 'is-dismissible' );
	}
	$id   = isset( $args['id'] ) ? $args['id'] : '';
	$id   = hocwp_sanitize_id( $id );
	$text = isset( $args['text'] ) ? $args['text'] : '';
	if ( empty( $text ) ) {
		return;
	}
	$title = isset( $args['title'] ) ? $args['title'] : '';
	if ( $error && empty( $title ) ) {
		$title = __( 'Error', 'hocwp-theme' );
	}
	if ( ! empty( $title ) ) {
		$text = '<strong>' . $title . ':</strong> ' . $text;
	}
	?>
	<div class="<?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $id ); ?>">
		<p><?php echo $text; ?></p>
	</div>
	<?php
}

function hocwp_admin_notice_setting_saved() {
	hocwp_admin_notice( array( 'text' => '<strong>' . __( 'Settings saved.', 'hocwp-theme' ) . '</strong>' ) );
}

function hocwp_sanitize_field_name( $base_name, $arr = array() ) {
	$name = '';
	if ( ! is_array( $arr ) ) {
		if ( hocwp_string_contain( $arr, $base_name ) ) {
			return $arr;
		}
		$arr = (array) $arr;
	}
	foreach ( $arr as $part ) {
		if ( ! is_array( $part ) && hocwp_string_contain( $part, $base_name ) ) {
			return array_shift( $arr );
		}
		$name .= '[' . $part . ']';
	}

	return $base_name . $name;
}

function hocwp_sanitize_field_args( &$args ) {
	if ( isset( $args['sanitized'] ) ) {
		return $args;
	}
	$field_class = isset( $args['field_class'] ) ? $args['field_class'] : '';
	$class       = isset( $args['class'] ) ? $args['class'] : '';
	hocwp_add_string_with_space_before( $field_class, $class );
	$widefat = isset( $args['widefat'] ) ? (bool) $args['widefat'] : true;
	$id      = isset( $args['id'] ) ? $args['id'] : '';
	$label   = isset( $args['label'] ) ? $args['label'] : '';
	$name    = isset( $args['name'] ) ? $args['name'] : '';
	hocwp_transmit_id_and_name( $id, $name );
	$value               = isset( $args['value'] ) ? $args['value'] : '';
	$description         = isset( $args['description'] ) ? $args['description'] : '';
	$args['class']       = $field_class;
	$args['field_class'] = $field_class;
	$args['id']          = $id;
	$args['label']       = $label;
	$args['name']        = $name;
	$args['value']       = $value;
	$args['description'] = $description;
	$args['widefat']     = $widefat;
	$args['sanitized']   = true;

	return $args;
}

function hocwp_is_image( $url, $id = 0 ) {
	if ( hocwp_id_number_valid( $id ) ) {
		return wp_attachment_is_image( $id );
	}

	return hocwp_is_image_url( $url );
}

function hocwp_return_media_url( $url, $media_id ) {
	if ( hocwp_id_number_valid( $media_id ) && hocwp_media_file_exists( $media_id ) ) {
		$url = hocwp_get_media_image_url( $media_id );
	}

	return $url;
}

function hocwp_sanitize_media_value( $value ) {
	$id   = 0;
	$url  = '';
	$icon = '';
	$size = '';
	if ( ! is_array( $value ) ) {
		if ( is_numeric( $value ) ) {
			$id = $value;
		} else {
			$url = $value;
		}
	} else {
		$url = isset( $value['url'] ) ? $value['url'] : '';
		$id  = isset( $value['id'] ) ? $value['id'] : '';
		$id  = absint( $id );
	}
	if ( ! hocwp_id_number_valid( $id ) ) {
		$id = hocwp_get_media_id( $url );
	}
	if ( hocwp_id_number_valid( $id ) ) {
		$url  = hocwp_return_media_url( $url, $id );
		$icon = wp_mime_type_icon( $id );
		$size = hocwp_get_media_size( $id );
	}
	$result = array(
		'id'          => $id,
		'url'         => $url,
		'type_icon'   => $icon,
		'is_image'    => hocwp_is_image( $url, $id ),
		'size'        => $size,
		'size_format' => hocwp_size_converter( $size ),
		'mime_type'   => get_post_mime_type( $id )
	);

	return $result;
}

function hocwp_get_media_path( $id ) {
	return get_attached_file( $id );
}

function hocwp_media_file_exists( $id ) {
	if ( file_exists( hocwp_get_media_path( $id ) ) ) {
		return true;
	}

	return false;
}

function hocwp_get_media_id( $url ) {
	global $wpdb;
	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $url ) );

	return isset( $attachment[0] ) ? $attachment[0] : 0;
}

function hocwp_get_media_image_detail( $id ) {
	return wp_get_attachment_image_src( $id, 'full' );
}

function hocwp_get_media_image_url( $id ) {
	$detail = hocwp_get_media_image_detail( $id );

	return isset( $detail[0] ) ? $detail[0] : '';
}

function hocwp_size_converter( $bytes, $decimals = 2 ) {
	$result = size_format( $bytes, $decimals );
	$result = strtoupper( $result );

	return $result;
}

function hocwp_get_media_size( $id ) {
	return filesize( get_attached_file( $id ) );
}

function hocwp_get_image_sizes( $id ) {
	$path = $id;
	if ( hocwp_id_number_valid( $id ) ) {
		$path = get_attached_file( $id );
	}
	if ( ! file_exists( $path ) ) {
		return null;
	}

	return getimagesize( $path );
}

function hocwp_get_media_option_url( $value ) {
	$value = hocwp_sanitize_media_value( $value );

	return $value['url'];
}

function hocwp_search_form( $args = array() ) {
	$echo  = isset( $args['echo'] ) ? (bool) $args['echo'] : true;
	$class = isset( $args['class'] ) ? $args['class'] : '';
	hocwp_add_string_with_space_before( $class, 'search-form' );
	$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : _x( 'Search &hellip;', 'placeholder', 'hocwp-theme' );
	$search_icon = isset( $args['search_icon'] ) ? $args['search_icon'] : false;
	$submit_text = _x( 'Search', 'submit button' );
	if ( $search_icon ) {
		hocwp_add_string_with_space_before( $class, 'use-icon-search' );
		$submit_text = '&#xf002;';
	}
	$icon_in = hocwp_get_value_by_key( $args, 'icon_in' );
	if ( (bool) $icon_in ) {
		hocwp_add_string_with_space_before( $class, 'icon-in' );
	}
	$action              = hocwp_get_value_by_key( $args, 'action', home_url( '/' ) );
	$action              = trailingslashit( $action );
	$name                = hocwp_get_value_by_key( $args, 'name', 's' );
	$before_search_field = apply_filters( 'hocwp_search_form_before_search_field', '', $args );
	$form                = '<form method="get" class="' . $class . '" action="' . esc_url( $action ) . '">
				<label>
					<span class="screen-reader-text">' . _x( 'Search for:', 'label', 'hocwp-theme' ) . '</span>';
	$form .= $before_search_field;
	$form .= '<input type="search" class="search-field" placeholder="' . esc_attr( $placeholder ) . '" value="' . get_search_query() . '" name="' . $name . '" title="' . esc_attr_x( 'Search for:', 'label' ) . '" />
				</label>
				<input type="submit" class="search-submit" value="' . esc_attr( $submit_text ) . '" />';
	$post_types = hocwp_get_value_by_key( $args, 'post_type' );
	if ( ! empty( $post_types ) && ! is_array( $post_types ) ) {
		$post_types = array( $post_types );
	}
	if ( hocwp_array_has_value( $post_types ) ) {
		foreach ( $post_types as $post_type ) {
			$form .= '<input type="hidden" name="post_type[]" value="' . $post_type . '" />';
		}
	}
	$form .= '</form>';
	if ( $echo ) {
		echo $form;
	}

	return $form;
}

function hocwp_feedburner_form( $args = array() ) {
	$name               = isset( $args['name'] ) ? $args['name'] : '';
	$locale             = isset( $args['locale'] ) ? $args['locale'] : 'en_US';
	$submit_button_text = isset( $args['submit_button_text'] ) ? $args['submit_button_text'] : '';
	if ( ! isset( $args['submit_button_text'] ) && isset( $args['button_text'] ) ) {
		$submit_button_text = $args['button_text'];
	}
	if ( empty( $submit_button_text ) ) {
		$submit_button_text = __( 'Subscribe', 'hocwp-theme' );
	}
	$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : __( 'Your email address...', 'hocwp-theme' );
	$button      = hocwp_get_value_by_key( $args, 'button' );
	if ( empty( $button ) ) {
		$button = '<input class="btn btn-submit" type="submit" value="' . $submit_button_text . '">';
	}
	?>
	<form class="feedburner-form" action="https://feedburner.google.com/fb/a/mailverify" method="post"
	      target="popupwindow"
	      onsubmit="window.open('https://feedburner.google.com/fb/a/mailverify?uri=<?php echo $name; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
		<?php do_action( 'hocwp_feedburner_before' ); ?>
		<input class="email-field" type="text" placeholder="<?php echo $placeholder; ?>" name="email"
		       autocomplete="off">
		<input type="hidden" value="<?php echo $name; ?>" name="uri">
		<input type="hidden" name="loc" value="<?php echo $locale; ?>">
		<?php
		echo $button;
		do_action( 'hocwp_feedburner_after' );
		?>
	</form>
	<?php
}

function hocwp_get_sidebars() {
	return $GLOBALS['wp_registered_sidebars'];
}

function hocwp_get_sidebar_by( $key, $value ) {
	$sidebars = hocwp_get_sidebars();
	foreach ( $sidebars as $id => $sidebar ) {
		switch ( $key ) {
			default:
				if ( $id == $value ) {
					return $sidebar;
				}
		}
	}

	return array();
}

function hocwp_sidebar_has_widget( $sidebar, $widget ) {
	$sidebar_name = $sidebar;
	$sidebars     = hocwp_get_sidebars();
	$sidebar      = isset( $sidebars[ $sidebar ] ) ? $sidebars[ $sidebar ] : '';
	if ( ! empty( $sidebar ) ) {
		$widgets = hocwp_get_sidebar_widgets( $sidebar_name );
		foreach ( $widgets as $widget_name ) {
			if ( hocwp_string_contain( $widget_name, $widget ) ) {
				return true;
			}
		}
	}

	return false;
}

function hocwp_get_sidebar_widgets( $sidebar ) {
	$widgets = wp_get_sidebars_widgets();
	$widgets = isset( $widgets[ $sidebar ] ) ? $widgets[ $sidebar ] : null;

	return $widgets;
}

function hocwp_supported_languages() {
	$languages = array(
		'vi' => __( 'Vietnamese', 'hocwp-theme' ),
		'en' => __( 'English', 'hocwp-theme' )
	);

	return apply_filters( 'hocwp_supported_languages', $languages );
}

function hocwp_get_language() {
	global $hocwp_language;
	if ( empty( $hocwp_language ) || ! is_string( $hocwp_language ) ) {
		$lang = hocwp_option_get_value( 'theme_setting', 'language' );
		if ( empty( $lang ) ) {
			$lang = 'vi';
		}
		$hocwp_language = $lang;
	}
	$hocwp_language = apply_filters( 'hocwp_language', $hocwp_language );

	return $hocwp_language;
}

function hocwp_register_core_style_and_script() {
	wp_register_style( 'hocwp-style', HOCWP_URL . '/css/hocwp' . HOCWP_CSS_SUFFIX, array(), HOCWP_VERSION );
	wp_register_script( 'hocwp', HOCWP_URL . '/js/hocwp' . HOCWP_JS_SUFFIX, array( 'jquery' ), HOCWP_VERSION, true );
}

function hocwp_default_script_localize_object() {
	$datepicker_icon = apply_filters( 'hocwp_datepicker_icon', HOCWP_URL . '/images/icon-datepicker-calendar.gif' );
	$shortcodes      = hocwp_get_all_shortcodes();
	$args            = array(
		'ajax_url'        => admin_url( 'admin-ajax.php' ),
		'security'        => wp_create_nonce( 'hocwp_nonce' ),
		'datepicker_icon' => $datepicker_icon,
		'shortcodes'      => $shortcodes,
		'logged_in'       => hocwp_bool_to_int( is_user_logged_in() ),
		'i18n'            => array(
			'jquery_undefined_error'      => __( 'HocWP\'s JavaScript requires jQuery', 'hocwp-theme' ),
			'jquery_version_error'        => sprintf( __( 'HocWP\'s JavaScript requires jQuery version %s or higher', 'hocwp-theme' ), HOCWP_MINIMUM_JQUERY_VERSION ),
			'insert_media_title'          => __( 'Insert media', 'hocwp-theme' ),
			'insert_media_button_text'    => __( 'Use this media', 'hocwp-theme' ),
			'insert_media_button_texts'   => __( 'Use these medias', 'hocwp-theme' ),
			'confirm_message'             => __( 'Are you sure?', 'hocwp-theme' ),
			'disconnect_confirm_message'  => __( 'Are you sure you want to disconnect?', 'hocwp-theme' ),
			'delete_confirm_message'      => __( 'Are you sure you want to delete this?', 'hocwp-theme' ),
			'processing_text'             => __( 'Processing...', 'hocwp-theme' ),
			'max_file_item_select_error'  => __( 'You can not select more than %s files.', 'hocwp-theme' ),
			'there_was_an_error_occurred' => __( 'There was an error occurred, please try again.', 'hocwp-theme' ),
			'password_not_match'          => __( 'Passwords do not match.', 'hocwp-theme' )
		),
		'ajax_loading'    => '<p class="ajax-wrap"><img class="ajax-loading" src="' . hocwp_get_image_url( 'icon-loading-circle-light-full.gif' ) . '" alt=""></p>'
	);

	return apply_filters( 'hocwp_default_script_object', $args );
}

function hocwp_enqueue_jquery_ui_style() {
	$version = HOCWP_JQUERY_LATEST_VERSION;
	$version = apply_filters( 'hocwp_jquery_ui_version', $version );
	$theme   = apply_filters( 'hocwp_jquery_ui_theme', 'smoothness' );
	wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $version . '/themes/' . $theme . '/jquery-ui.css' );
}

function hocwp_enqueue_jquery_ui_datepicker() {
	wp_enqueue_script( 'jquery-ui-datepicker' );
	hocwp_enqueue_jquery_ui_style();
}

function hocwp_get_recaptcha_language() {
	$lang = apply_filters( 'hocwp_recaptcha_language', hocwp_get_language() );

	return $lang;
}

function hocwp_enqueue_recaptcha() {
	$lang     = hocwp_get_recaptcha_language();
	$url      = 'https://www.google.com/recaptcha/api.js';
	$url      = add_query_arg( array( 'hl' => $lang ), $url );
	$multiple = apply_filters( 'hocwp_multiple_recaptcha', false );
	if ( $multiple ) {
		$url = add_query_arg( array( 'onload' => 'CaptchaCallback', 'render' => 'explicit' ), $url );
	}
	wp_enqueue_script( 'recaptcha', $url, array(), false, true );
}

function hocwp_recaptcha_response( $secret_key ) {
	$result   = false;
	$response = @file_get_contents( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $_POST['g-recaptcha-response'] );
	$response = json_decode( $response, true );
	if ( true === $response['success'] ) {
		$result = true;
	}

	return $result;
}

function hocwp_admin_enqueue_scripts() {
	global $pagenow;
	$current_page = hocwp_get_current_admin_page();
	$use          = apply_filters( 'hocwp_use_jquery_ui', false );
	if ( $use || ( 'themes.php' == $pagenow && 'hocwp_theme_setting' == $current_page ) ) {
		wp_enqueue_script( 'jquery-ui-core' );
	}
	$use = apply_filters( 'hocwp_use_jquery_ui_sortable', false );
	if ( $use ) {
		wp_enqueue_script( 'jquery-ui-sortable' );
	}
	$use = apply_filters( 'hocwp_use_color_picker', false );
	if ( $use ) {
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
	}
	$use = apply_filters( 'hocwp_wp_enqueue_media', false );
	if ( $use || 'link.php' == $pagenow || 'link-add.php' == $pagenow ) {
		wp_enqueue_media();
	}
	$datetime_picker = apply_filters( 'hocwp_admin_jquery_datetime_picker', false );
	if ( $datetime_picker ) {
		hocwp_enqueue_jquery_ui_datepicker();
	}
	hocwp_register_core_style_and_script();
	wp_register_style( 'hocwp-admin-style', HOCWP_URL . '/css/hocwp-admin' . HOCWP_CSS_SUFFIX, array( 'hocwp-style' ), HOCWP_VERSION );
	wp_register_script( 'hocwp-admin', HOCWP_URL . '/js/hocwp-admin' . HOCWP_JS_SUFFIX, array(
		'jquery',
		'hocwp'
	), HOCWP_VERSION, true );
	wp_localize_script( 'hocwp', 'hocwp', hocwp_default_script_localize_object() );
	$use = apply_filters( 'hocwp_use_admin_style_and_script', false );
	if ( 'link-manager.php' == $pagenow || 'link-add.php' == $pagenow ) {
		$use = true;
	}
	if ( $use || 'post-new.php' == $pagenow || 'post.php' == $pagenow || 'link.php' == $pagenow || 'link-add.php' == $pagenow ) {
		wp_enqueue_style( 'hocwp-admin-style' );
		wp_enqueue_script( 'hocwp-admin' );
	} elseif ( 'wpsupercache' == $current_page ) {
		wp_enqueue_style( 'hocwp-admin-style' );
	}
}

function hocwp_get_admin_email() {
	return get_bloginfo( 'admin_email' );
}

function hocwp_google_plus_client_script() {
	wp_enqueue_script( 'google-client', 'https://plus.google.com/js/client:platform.js', array(), false, true );
}

function hocwp_facebook_javascript_sdk( $args = array() ) {
	$language = isset( $args['language'] ) ? $args['language'] : 'vi_VN';
	$language = apply_filters( 'hocwp_facebook_javascript_sdk_language', $language );
	$app_id   = isset( $args['app_id'] ) ? $args['app_id'] : '';
	$app_id   = apply_filters( 'hocwp_facebook_javascript_sdk_app_id', $app_id );
	if ( empty( $app_id ) ) {
		return;
	}
	$version = isset( $args['version'] ) ? $args['version'] : HOCWP_FACEBOOK_GRAPH_API_VERSION;
	$version = apply_filters( 'hocwp_facebook_javascript_sdk_version', $version );
	$use     = hocwp_use_facebook_javascript_sdk();
	if ( ! (bool) $use ) {
		return;
	}
	?>
	<div id="fb-root"></div>
	<script>
		(function (d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s);
			js.id = id;
			js.src = "//connect.facebook.net/<?php echo $language; ?>/sdk.js#xfbml=1&version=v<?php echo $version; ?>&appId=<?php echo $app_id; ?>";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	</script>
	<?php
}

function hocwp_use_full_mce_toolbar() {
	$use = false;
	global $pagenow;
	if ( 'post-new.php' == $pagenow || 'post.php' == $pagenow ) {
		$use = true;
	}

	return apply_filters( 'hocwp_use_full_mce_toolbar', $use );
}

function hocwp_use_facebook_javascript_sdk() {
	$result = apply_filters( 'hocwp_use_facebook_javascript_sdk', false );

	return $result;
}

function hocwp_update_permalink_struct( $struct ) {
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( $struct );
	update_option( 'permalink_structure', $struct );
	flush_rewrite_rules();
}

function hocwp_flush_rewrite_rules_after_site_url_changed() {
	$old_url     = get_option( 'hocwp_site_url' );
	$defined_url = ( defined( 'WP_SITEURL' ) ) ? WP_SITEURL : get_option( 'siteurl' );
	if ( empty( $old_url ) || $old_url != $defined_url ) {
		update_option( 'hocwp_site_url', $defined_url );
		flush_rewrite_rules();
	}
}

function hocwp_the_footer_logo() {
	$footer_logo = hocwp_get_footer_logo_url();
	if ( ! empty( $footer_logo ) ) {
		$a = new HOCWP_HTML( 'a' );
		$a->set_attribute( 'href', home_url( '/' ) );
		$img = new HOCWP_HTML( 'img' );
		$img->set_attribute( 'src', $footer_logo );
		$a->set_text( $img->build() );
		$a->output();
	}
}

function hocwp_pretty_permalinks_enabled() {
	$permalink_structure = get_option( 'permalink_structure' );

	return ( empty( $permalink_structure ) ) ? false : true;
}

function hocwp_exclude_special_taxonomies( &$taxonomies ) {
	unset( $taxonomies['nav_menu'] );
	unset( $taxonomies['link_category'] );
	unset( $taxonomies['post_format'] );
}

function hocwp_exclude_special_post_types( &$post_types ) {
	unset( $post_types['attachment'] );
	unset( $post_types['page'] );
}

function hocwp_icon_circle_ajax( $post_id, $meta_key ) {
	$div = new HOCWP_HTML( 'div' );
	$div->set_attribute( 'style', 'text-align: center' );
	$div->set_class( 'hocwp-switcher-ajax' );
	$span         = new HOCWP_HTML( 'span' );
	$circle_class = 'icon-circle';
	$result       = get_post_meta( $post_id, $meta_key, true );
	if ( 1 == $result ) {
		$circle_class .= ' icon-circle-success';
	}
	$span->set_attribute( 'data-id', $post_id );
	$span->set_attribute( 'data-value', $result );
	$span->set_attribute( 'data-key', $meta_key );
	$span->set_class( $circle_class );
	$div->set_text( $span->build() );
	$div->output();
}

function hocwp_get_posts_per_page() {
	return get_option( 'posts_per_page' );
}

function hocwp_delete_transient_with_condition( $transient_name, $condition = '', $blog_id = '' ) {
	global $wpdb;
	if ( ! empty( $blog_id ) ) {
		$wpdb->set_blog_id( $blog_id );
	}
	$last_char = hocwp_get_last_char( $transient_name );
	if ( '_' == $last_char ) {
		$transient_name = hocwp_remove_last_char( $transient_name, $last_char );
	}
	$query_root = "DELETE FROM $wpdb->options WHERE option_name like %s" . $condition;
	$key_1      = '_transient_';
	$key_2      = '_transient_timeout_';
	if ( ! empty( $transient_name ) ) {
		$key_1 .= $transient_name;
		$key_2 .= $transient_name;
	}
	$wpdb->query( $wpdb->prepare( $query_root, $key_1 ) );
	$wpdb->query( $wpdb->prepare( $query_root, $key_2 ) );
}

function hocwp_delete_transient( $transient_name, $blog_id = '' ) {
	hocwp_delete_transient_with_condition( $transient_name, $blog_id );
}

function hocwp_delete_transient_license_valid( $blog_id = '' ) {
	$transient_name = 'hocwp_check_license';
	hocwp_delete_transient( $transient_name, $blog_id );
}

function hocwp_get_wp_version() {
	global $wp_version;

	return $wp_version;
}

function hocwp_get_upload_folder_details() {
	$upload = wp_upload_dir();
	$dir    = isset( $upload['basedir'] ) ? $upload['basedir'] : '';
	$url    = isset( $upload['baseurl'] ) ? $upload['baseurl'] : '';
	if ( empty( $dir ) ) {
		$dir = WP_CONTENT_DIR . '/uploads';
	}
	if ( empty( $url ) ) {
		$url = content_url( 'uploads' );
	}

	return array( 'path' => $dir, 'url' => $url );
}

function hocwp_upload( $args = array() ) {
	$name             = isset( $args['name'] ) ? $args['name'] : '';
	$path             = isset( $args['path'] ) ? $args['path'] : '';
	$size             = isset( $args['size'] ) ? $args['size'] : 0;
	$max_size         = isset( $args['max_size'] ) ? $args['max_size'] : - 1;
	$is_image         = isset( $args['is_image'] ) ? $args['is_image'] : false;
	$extensions       = isset( $args['extensions'] ) ? $args['extensions'] : array();
	$tmp_name         = isset( $args['tmp_name'] ) ? $args['tmp_name'] : '';
	$duplicate_exists = isset( $args['duplicate_exists'] ) ? $args['duplicate_exists'] : true;
	$result           = array(
		'success' => false
	);
	if ( $is_image ) {
		$result['image_base64'] = hocwp_image_base64( $tmp_name );
	}
	$name      = strtolower( $name );
	$basename  = basename( $name );
	$basename  = hocwp_sanitize_file_name( $basename );
	$file_path = $path . '/' . $basename;
	$file_type = pathinfo( $file_path, PATHINFO_EXTENSION );
	if ( $is_image && ! empty( $tmp_name ) ) {
		$check = getimagesize( $tmp_name );
		if ( $check === false ) {
			$result['message'][] = sprintf( __( 'File %s is not a picture.', 'hocwp-theme' ), $name );

			return $result;
		}
	}
	if ( file_exists( $file_path ) ) {
		if ( $duplicate_exists ) {
			$path_info = pathinfo( $file_path );
			$name      = $path_info['filename'] . '-' . hocwp_random_string() . '.' . $file_type;
			$name      = strtolower( $name );
			$basename  = basename( $name );
			$basename  = hocwp_sanitize_file_name( $basename );
			$file_path = $path . '/' . $basename;
		} else {
			$result['message'][] = sprintf( __( 'File %s already exists', 'hocwp-theme' ), $name );

			return $result;
		}
	}
	if ( $max_size > 0 && $size > $max_size ) {
		$result['message'][] = sprintf( __( 'File size should not exceed %s', 'hocwp-theme' ), $max_size );

		return $result;
	}
	if ( count( $extensions ) > 0 && ! in_array( $file_type, $extensions ) ) {
		$result['message'][] = sprintf( __( 'You are not allowed to upload files with extension %s', 'hocwp-theme' ), $file_type );

		return $result;
	}
	$file_path = strtolower( $file_path );
	if ( move_uploaded_file( $tmp_name, $file_path ) ) {
		$result['success'] = true;
	} else {
		$result['message'][] = __( 'There was an error occurred, file is not uploaded.', 'hocwp-theme' );
	}
	$result['name'] = $name;
	$result['path'] = $file_path;

	return $result;
}

function hocwp_execute_upload( $args = array() ) {
	$files = isset( $args['files'] ) ? $args['files'] : array();
	unset( $args['files'] );
	$upload_path = isset( $args['upload_path'] ) ? $args['upload_path'] : '';
	unset( $args['upload_path'] );
	$upload_url = isset( $args['upload_url'] ) ? $args['upload_url'] : '';
	unset( $args['upload_url'] );
	if ( empty( $upload_path ) ) {
		$upload_dir = hocwp_get_upload_folder_details();
		$target_dir = untrailingslashit( $upload_dir['path'] ) . '/hocwp';
		$upload_url = untrailingslashit( $upload_dir['url'] ) . '/hocwp';
		if ( ! file_exists( $target_dir ) ) {
			wp_mkdir_p( $target_dir );
		}
		$upload_path = $target_dir;
	}
	$file_names = isset( $files['name'] ) ? $files['name'] : array();
	$list_files = array();
	if ( hocwp_array_has_value( $file_names ) ) {
		$file_count = count( $file_names );
		for ( $i = 0; $i < $file_count; $i ++ ) {
			$name         = isset( $files['name'][ $i ] ) ? $files['name'][ $i ] : '';
			$type         = isset( $files['type'][ $i ] ) ? $files['type'][ $i ] : '';
			$tmp_name     = isset( $files['tmp_name'][ $i ] ) ? $files['tmp_name'][ $i ] : '';
			$error        = isset( $files['error'][ $i ] ) ? $files['error'][ $i ] : '';
			$size         = isset( $files['size'][ $i ] ) ? $files['size'][ $i ] : '';
			$file_item    = array(
				'name'     => $name,
				'type'     => $type,
				'tmp_name' => $tmp_name,
				'error'    => $error,
				'size'     => $size
			);
			$list_files[] = $file_item;
		}
	} else {
		$list_files[] = $files;
	}

	$list_results = array();
	foreach ( $list_files as $key => $file ) {
		$file['path'] = $upload_path;
		$file         = wp_parse_args( $args, $file );
		$result       = hocwp_upload( $file );
		if ( $result['success'] ) {
			$file_name  = $file['name'];
			$file_path  = untrailingslashit( $upload_path ) . '/' . hocwp_sanitize_file_name( $file_name );
			$file_url   = untrailingslashit( $upload_url ) . '/' . hocwp_sanitize_file_name( basename( $result['name'] ) );
			$attachment = array(
				'guid' => $file_url
			);
			hocwp_insert_attachment( $attachment, $file_path );
			$result['url'] = $file_url;
		}
		$list_results[] = $result;
	}

	return $list_results;
}

function hocwp_insert_attachment( $attachment, $file_path, $parent_post_id = 0 ) {
	if ( ! file_exists( $file_path ) ) {
		return 0;
	}
	$file_type                    = wp_check_filetype( basename( $file_path ), null );
	$attachment['post_mime_type'] = $file_type['type'];
	if ( ! isset( $attachment['guid'] ) ) {
		return 0;
	}
	$attachment['post_status'] = isset( $attachment['post_status'] ) ? $attachment['post_status'] : 'inherit';
	if ( ! isset( $attachment['post_title'] ) ) {
		$attachment['post_title'] = preg_replace( '/\.[^.]+$/', '', basename( $file_path ) );
	}
	$attach_id = wp_insert_attachment( $attachment, $file_path, $parent_post_id );
	if ( $attach_id > 0 ) {
		hocwp_update_attachment_meta( $attach_id, $file_path );
		if ( $parent_post_id > 0 ) {
			hocwp_set_thumbnail( $parent_post_id, $attach_id );
		}
	}

	return $attach_id;
}

function hocwp_update_attachment_meta( $attach_id, $file_path ) {
	if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
	}
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
	wp_update_attachment_metadata( $attach_id, $attach_data );
}

function hocwp_set_thumbnail( $post_id, $attach_id ) {
	return set_post_thumbnail( $post_id, $attach_id );
}