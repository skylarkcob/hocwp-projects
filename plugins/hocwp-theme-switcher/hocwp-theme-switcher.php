<?php
/*
Plugin Name: HocWP Theme Switcher
Plugin URI: http://hocwp.net/
Description: This plugin is created by HocWP.
Author: HocWP
Version: 1.6
Author URI: http://hocwp.net/
Text Domain: hocwp-theme-switcher
Domain Path: /languages/
*/
define('HOCWP_THEME_SWITCHER_VERSION', '1.6');

define('HOCWP_THEME_SWITCHER_FILE', __FILE__);

define('HOCWP_THEME_SWITCHER_PATH', untrailingslashit(plugin_dir_path(HOCWP_THEME_SWITCHER_FILE)));

define('HOCWP_THEME_SWITCHER_URL', plugins_url('', HOCWP_THEME_SWITCHER_FILE));

define('HOCWP_THEME_SWITCHER_INC_PATH', HOCWP_THEME_SWITCHER_PATH . '/inc');

define('HOCWP_THEME_SWITCHER_BASENAME', plugin_basename(HOCWP_THEME_SWITCHER_FILE));

define('HOCWP_THEME_SWITCHER_DIRNAME', dirname(HOCWP_THEME_SWITCHER_BASENAME));

require_once(HOCWP_THEME_SWITCHER_PATH . '/load.php');