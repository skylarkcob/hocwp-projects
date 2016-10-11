<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

global $hocwp_tos_tabs;
$parent_slug = 'hocwp_theme_option';

$option = new HOCWP_Option( __( 'System Information', 'hocwp-theme' ), 'hocwp_about' );
$option->set_parent_slug( $parent_slug );
$option->set_is_option_page( false );
$option->set_use_style_and_script( true );
$option->add_option_tab( $hocwp_tos_tabs );
$option->set_page_header_callback( 'hocwp_theme_option_form_before' );
$option->set_page_footer_callback( 'hocwp_theme_option_form_after' );
$option->set_page_sidebar_callback( 'hocwp_theme_option_sidebar_tab' );
$option->init();
hocwp_option_add_object_to_list( $option );

function hocwp_option_page_about_content() {
	global $wpdb;
	$current_theme = wp_get_theme();
	$themes        = wp_get_themes();
	?>
	<div id="dashboard-widgets-wrap" class="hocwp server-information">
		<div id="dashboard-widgets" class="metabox-holder">
			<div class="postbox-container">
				<?php ob_start(); ?>
				<table>
					<tbody>
					<tr>
						<td class="label">WordPress version</td>
						<td><?php echo hocwp_get_wp_version(); ?></td>
					</tr>
					<tr>
						<td class="label">Home URL</td>
						<td><?php echo home_url(); ?></td>
					</tr>
					<tr>
						<td class="label">Site URL</td>
						<td><?php bloginfo( 'url' ); ?></td>
					</tr>
					<tr>
						<td class="label">Admin email</td>
						<td><?php bloginfo( 'admin_email' ); ?></td>
					</tr>
					<tr>
						<td class="label">Home dir</td>
						<td><?php echo htmlspecialchars( ABSPATH ); ?></td>
					</tr>
					<tr>
						<td class="label">Content dir</td>
						<td><?php echo htmlspecialchars( WP_CONTENT_DIR ); ?></td>
					</tr>
					<tr>
						<td class="label">Plugin dir</td>
						<td><?php echo htmlspecialchars( WP_PLUGIN_DIR ); ?></td>
					</tr>
					<tr>
						<td class="label">Table prefix</td>
						<td><?php echo hocwp_get_table_prefix(); ?></td>
					</tr>
					<tr>
						<td class="label">Active plugins</td>
						<td><?php echo count( (array) get_option( 'active_plugins' ) ); ?></td>
					</tr>
					<tr>
						<td class="label">Total users</td>
						<td><?php echo hocwp_count_user(); ?></td>
					</tr>
					</tbody>
				</table>
				<?php
				$content = ob_get_clean();
				$args    = array(
					'title'   => __( 'Your Site', 'hocwp-theme' ),
					'content' => $content
				);
				hocwp_field_admin_postbox( $args );
				?>
				<?php ob_start(); ?>
				<table>
					<tbody>
					<tr>
						<td class="label">Current theme</td>
						<td><?php echo $current_theme->get( 'Name' ); ?></td>
					</tr>
					<tr>
						<td class="label">Current theme author</td>
						<td><?php echo $current_theme->get( 'Author' ); ?></td>
					</tr>
					<tr>
						<td class="label">Current theme URL</td>
						<td><?php echo $current_theme->get( 'AuthorURI' ); ?></td>
					</tr>
					<tr>
						<td class="label">Installed</td>
						<td><?php echo count( $themes ); ?></td>
					</tr>
					<tr>
						<td class="label">Core version</td>
						<td><?php echo HOCWP_VERSION; ?></td>
					</tr>
					<tr>
						<td class="label">Theme core version</td>
						<td><?php echo HOCWP_THEME_CORE_VERSION; ?></td>
					</tr>
					</tbody>
				</table>
				<?php
				$content = ob_get_clean();
				$args    = array(
					'title'   => __( 'Theme', 'hocwp-theme' ),
					'content' => $content
				);
				hocwp_field_admin_postbox( $args );
				?>
			</div>
			<div class="postbox-container">
				<?php ob_start(); ?>
				<table>
					<tbody>
					<tr>
						<td class="label">PHP version</td>
						<td><?php echo phpversion(); ?></td>
					</tr>
					<tr>
						<td class="label">MySQL version</td>
						<td><?php echo $wpdb->db_version(); ?></td>
					</tr>
					<tr>
						<td class="label">Server software</td>
						<td><?php echo hocwp_get_web_server(); ?></td>
					</tr>
					<tr>
						<td class="label">Server OS</td>
						<td><?php echo implode( ' ', hocwp_get_computer_info() ); ?></td>
					</tr>
					<tr>
						<td class="label">Peak memory usage</td>
						<td><?php echo hocwp_size_converter( hocwp_get_peak_memory_usage() ); ?></td>
					</tr>
					<tr>
						<td class="label">Current memory usage</td>
						<td><?php echo hocwp_size_converter( hocwp_get_memory_usage() ); ?></td>
					</tr>
					<tr>
						<td class="label">Memory limit</td>
						<td><?php echo hocwp_get_memory_limit(); ?></td>
					</tr>
					<tr>
						<td class="label">Curl version</td>
						<td><?php echo hocwp_get_curl_version(); ?></td>
					</tr>
					</tbody>
				</table>
				<hr>
				<a onclick="window.open('<?php echo HOCWP_THEME_INC_URL; ?>/views/phpinfo.php', 'PHPInfo', 'width=800, height=600, scrollbars=1'); return false;"
				   href="#" class="button-primary">PHP Info</a>
				<?php
				$content = ob_get_clean();
				$args    = array(
					'title'   => __( 'Server Info', 'hocwp-theme' ),
					'content' => $content
				);
				hocwp_field_admin_postbox( $args );
				?>
				<?php ob_start(); ?>
				<table>
					<tbody>
					<tr>
						<td class="label">Browser</td>
						<td><?php echo hocwp_uppercase_first_char_words( hocwp_get_browser() ); ?></td>
					</tr>
					<tr>
						<td class="label">User Agent</td>
						<td><?php echo $_SERVER['HTTP_USER_AGENT']; ?></td>
					</tr>
					<tr>
						<td class="label">IP Address</td>
						<td><?php echo hocwp_get_ip_address(); ?></td>
					</tr>
					</tbody>
				</table>
				<?php
				$content = ob_get_clean();
				$args    = array(
					'title'   => __( 'Client Info', 'hocwp-theme' ),
					'content' => $content
				);
				hocwp_field_admin_postbox( $args );
				?>
			</div>
		</div>
	</div>
	<?php
}

add_action( 'hocwp_option_page_' . $option->get_option_name_no_prefix() . '_content', 'hocwp_option_page_about_content' );