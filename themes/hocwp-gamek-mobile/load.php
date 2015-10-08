<?php
if(!function_exists('add_filter')) exit;

if(!defined('HOCWP_REQUIRE_WP_VERSION')) {
    define('HOCWP_REQUIRE_WP_VERSION', '4.1');
}

if(version_compare($GLOBALS['wp_version'], HOCWP_REQUIRE_WP_VERSION, '<')) {
    require get_template_directory() . '/inc/back-compat.php';
    return;
}

define('HOCWP_THEME_VERSION', '2.3.1');

if(!defined('HOCWP_PATH')) {
    require_once(get_template_directory() . '/hocwp/load.php');
}

require_once(get_template_directory() . '/inc/hocwp-custom-pre-hook.php');

require_once(HOCWP_PATH . '/options/theme-option.php');

require_once(HOCWP_PATH . '/options/user-option.php');

require_once(HOCWP_PATH . '/options/general-option.php');

require_once(HOCWP_PATH . '/theme/theme-functions.php');

require_once(HOCWP_PATH . '/theme/setup-theme.php');

require get_template_directory() . '/inc/hocwp-custom-functions.php';

require get_template_directory() . '/inc/hocwp-custom-admin.php';

require get_template_directory() . '/inc/hocwp-custom-post-type-and-taxonomy.php';

require get_template_directory() . '/inc/hocwp-custom-meta.php';

require get_template_directory() . '/inc/hocwp-custom-hook.php';

require get_template_directory() . '/inc/hocwp-custom-ajax.php';

require_once(HOCWP_PATH . '/theme/setup-theme-after.php');