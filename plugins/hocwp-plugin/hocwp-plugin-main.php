<?php
/*
Plugin Name: HocWP Plugin Default
Plugin URI: http://hocwp.net/
Description: This plugin is created by HocWP.
Author: HocWP
Version: 1.4.2
Author URI: http://hocwp.net/
Text Domain: hocwp-plugin-default
Domain Path: /languages/
*/
if(!function_exists('add_filter')) exit;
define('HOCWP_PLUGIN_DEFAULT_VERSION', '1.4.2');

define('HOCWP_PLUGIN_DEFAULT_FILE', __FILE__);

define('HOCWP_PLUGIN_DEFAULT_PATH', untrailingslashit(plugin_dir_path(HOCWP_PLUGIN_DEFAULT_FILE)));

define('HOCWP_PLUGIN_DEFAULT_URL', plugins_url('', HOCWP_PLUGIN_DEFAULT_FILE));

define('HOCWP_PLUGIN_DEFAULT_INC_PATH', HOCWP_PLUGIN_DEFAULT_PATH . '/inc');

define('HOCWP_PLUGIN_DEFAULT_BASENAME', plugin_basename(HOCWP_PLUGIN_DEFAULT_FILE));

define('HOCWP_PLUGIN_DEFAULT_DIRNAME', dirname(HOCWP_PLUGIN_DEFAULT_BASENAME));

define('HOCWP_PLUGIN_DEFAULT_OPTION_NAME', 'hocwp_plugin_default');

define('HOCWP_PLUGIN_DEFAULT_SETTINGS_URL', 'options-general.php?page=' . HOCWP_PLUGIN_DEFAULT_OPTION_NAME);

require_once(HOCWP_PLUGIN_DEFAULT_PATH . '/load.php');