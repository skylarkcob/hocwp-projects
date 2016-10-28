<?php
/*
Plugin Name: Default Plugin by HocWP
Plugin URI: http://hocwp.net/
Description: This plugin is created by HocWP.
Author: HocWP
Version: 1.0.0
Author URI: http://hocwp.net/
Text Domain: hocwp-default-plugin
Domain Path: /languages/
*/
function hocwp_default_plugin_missing_core_notice() {
	$plugin_data = get_plugin_data( __FILE__ );
	?>
	<div class="updated notice settings-error error">
		<p>
			<strong><?php _e( 'Error:', 'hocwp-default-plugin' ); ?></strong> <?php printf( __( 'Plugin %s cannot be run properly because of missing core.', 'hocwp-default-plugin' ), '<strong>' . $plugin_data['Name'] . '</strong>' ); ?>
		</p>
	</div>
	<?php
}

$path = get_template_directory() . '/hocwp/load.php';

if ( ! defined( 'HOCWP_PATH' ) ) {
	$load = $path;
	if ( ! file_exists( $load ) ) {
		$load = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/hocwp/load.php';
	}

	if ( ! file_exists( $load ) ) {
		add_action( 'admin_notices', 'hocwp_default_plugin_missing_core_notice' );

		return;
	}

	require_once( $load );
}

if ( ! defined( 'HOCWP_URL' ) ) {
	if ( file_exists( $path ) ) {
		define( 'HOCWP_URL', untrailingslashit( get_template_directory_uri() ) . '/hocwp' );
	} else {
		define( 'HOCWP_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) . '/hocwp' );
	}
}

class HOCWP_Default_Plugin extends HOCWP_Plugin {
	public $name = 'hocwp_default_plugin';
	public $textdomain = 'hocwp-default-plugin';
	public $version = '1.0.0';
	public $file = __FILE__;

	public function __construct() {
		$this->option_name = 'hocwp_default_plugin';
		//$this->setting_url = 'admin.php?page=' . $this->option_name;
		parent::__construct();
	}

	public function license_data() {
		$data = array(
			'hashed'  => '',
			'key_map' => '',
			'domain'  => ''
		);

		return $data;
	}
}

global $hocwp_plugin_default_plugin;
$hocwp_plugin_default_plugin = new HOCWP_Default_Plugin();