<?php
/*
Plugin Name: Content Captcha
Plugin URI: http://hocwp.net/
Description: This plugin is created by HocWP.
Author: HocWP
Version: 1.3.3
Author URI: http://hocwp.net/
Text Domain: hocwp-content-captcha
Domain Path: /languages/
*/
define('HOCWP_CONTENT_CAPTCHA_VERSION', '1.3.3');

define('HOCWP_CONTENT_CAPTCHA_FILE', __FILE__);

define('HOCWP_CONTENT_CAPTCHA_PATH', untrailingslashit(plugin_dir_path(HOCWP_CONTENT_CAPTCHA_FILE)));

define('HOCWP_CONTENT_CAPTCHA_URL', plugins_url('', HOCWP_CONTENT_CAPTCHA_FILE));

define('HOCWP_CONTENT_CAPTCHA_INC_PATH', HOCWP_CONTENT_CAPTCHA_PATH . '/inc');

define('HOCWP_CONTENT_CAPTCHA_BASENAME', plugin_basename(HOCWP_CONTENT_CAPTCHA_FILE));

define('HOCWP_CONTENT_CAPTCHA_DIRNAME', dirname(HOCWP_CONTENT_CAPTCHA_BASENAME));

define('HOCWP_CONTENT_CAPTCHA_SETTINGS_URL', 'options-general.php?page=hocwp_content_captcha');

require_once(HOCWP_CONTENT_CAPTCHA_PATH . '/load.php');