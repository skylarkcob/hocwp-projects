<?php
/*
Plugin Name: HocWP Đặt Vé Tàu
Plugin URI: http://hocwp.net/
Description: This plugin is created by HocWP.
Author: HocWP
Version: 1.3.4
Author URI: http://hocwp.net/
Text Domain: hocwp-dat-ve-tau
Domain Path: /languages/
*/
define('HOCWP_DAT_VE_TAU_VERSION', '1.3.4');

define('HOCWP_DAT_VE_TAU_FILE', __FILE__);

define('HOCWP_DAT_VE_TAU_PATH', untrailingslashit(plugin_dir_path(HOCWP_DAT_VE_TAU_FILE)));

define('HOCWP_DAT_VE_TAU_URL', plugins_url('', HOCWP_DAT_VE_TAU_FILE));

define('HOCWP_DAT_VE_TAU_INC_PATH', HOCWP_DAT_VE_TAU_PATH . '/inc');

define('HOCWP_DAT_VE_TAU_BASENAME', plugin_basename(HOCWP_DAT_VE_TAU_FILE));

define('HOCWP_DAT_VE_TAU_DIRNAME', dirname(HOCWP_DAT_VE_TAU_BASENAME));

require_once(HOCWP_DAT_VE_TAU_PATH . '/load.php');