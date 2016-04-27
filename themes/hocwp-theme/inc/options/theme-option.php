<?php
if(!function_exists('add_filter')) exit;

global $hocwp_theme_option, $hocwp_tos_tabs;

$hocwp_theme_option = new HOCWP_Option(__('Theme Options', 'hocwp'), 'hocwp_theme_option');
$hocwp_theme_option->set_parent_slug('');
$hocwp_theme_option->set_icon_url('dashicons-admin-generic');
$hocwp_theme_option->set_position(61);
$hocwp_theme_option->set_use_style_and_script(true);
$hocwp_theme_option->init();

require(HOCWP_THEME_INC_PATH . '/options/setting-theme-setting.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-theme-home.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-theme-custom.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-theme-custom-css.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-theme-add-to-head.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-theme-add-to-footer.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-optimize.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-social.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-login.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-smtp-email.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-writing.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-reading.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-discussion.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-permalink.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-utilities.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-geo.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-theme-license.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-maintenance.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-recommend-plugin.php');
require(HOCWP_THEME_INC_PATH . '/options/setting-theme-about.php');