<?php
/*
Plugin Name: Theme Switcher by HocWP
Plugin URI: http://hocwp.net/
Description: This plugin is created by HocWP.
Author: HocWP
Version: 2.0.0
Author URI: http://hocwp.net/
Text Domain: hocwp-theme-switcher
Domain Path: /languages/
*/
function hocwp_theme_switcher_missing_core_notice() {
	$plugin_data = get_plugin_data( __FILE__ );
	?>
	<div class="updated notice settings-error error">
		<p>
			<strong><?php _e( 'Error:', 'hocwp-theme-switcher' ); ?></strong> <?php printf( __( 'Plugin %s cannot be run properly because of missing core.', 'hocwp-theme-switcher' ), '<strong>' . $plugin_data['Name'] . '</strong>' ); ?>
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
		add_action( 'admin_notices', 'hocwp_theme_switcher_missing_core_notice' );

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

class HOCWP_Theme_Switcher extends HOCWP_Plugin {
	public $name = 'hocwp_theme_switcher';
	public $textdomain = 'hocwp-theme-switcher';
	public $version = '2.0.0';
	public $file = __FILE__;
	public $use_session = true;

	public function __construct() {
		$this->option_name = $this->name;
		parent::__construct();
	}

	public function license_data() {
		$data = array(
			'hashed'  => '$P$BjvwYxdMItE7VDTZCeFw3Jy564HUZj.',
			'key_map' => 'a:5:{i:0;s:7:"use_for";i:1;s:5:"email";i:2;s:4:"code";i:3;s:6:"domain";i:4;s:15:"hashed_password";}',
			'domain'  => ''
		);

		return $data;
	}
}

global $hocwp_plugin_theme_switcher;
$hocwp_plugin_theme_switcher = new HOCWP_Theme_Switcher();

require $hocwp_plugin_theme_switcher->path . '/load.php';