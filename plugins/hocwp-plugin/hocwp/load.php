<?php
if(defined('HOCWP_PATH')) {
    return;
}

define('HOCWP_VERSION', '2.7.4');

define('HOCWP_PATH', dirname(__FILE__));

define('HOCWP_CONTENT_PATH', WP_CONTENT_DIR . '/hocwp');

define('HOCWP_NAME', 'HocWP');

define('HOCWP_EMAIL', 'hocwp.net@gmail.com');

define('HOCWP_HOMEPAGE', 'http://hocwp.net');

define('HOCWP_CSS_SUFFIX', ((defined('WP_DEBUG') && true === WP_DEBUG) ? '.css' : '.min.css'));

define('HOCWP_JS_SUFFIX', ((defined('WP_DEBUG') && true === WP_DEBUG) ? '.js' : '.min.js'));

define('HOCWP_DOING_AJAX', ((defined('DOING_AJAX') && true === DOING_AJAX) ? true : false));

define('HOCWP_DOING_CRON', ((defined('DOING_CRON') && true === DOING_CRON) ? true : false));

define('HOCWP_DOING_AUTO_SAVE', ((defined('DOING_AUTOSAVE') && true === DOING_AUTO_SAVE) ? true : false));

define('HOCWP_MINIMUM_JQUERY_VERSION', '1.9.1');

define('HOCWP_JQUERY_LATEST_VERSION', '1.11.4');

define('HOCWP_HASHED_PASSWORD', '$P$Bj8RQOu1MNcgkC3c3Vl9EOugiXdg951');

define('HOCWP_REQUIRED_HTML', '<span style="color:#FF0000">*</span>');

define('HOCWP_PLUGIN_LICENSE_OPTION_NAME', 'hocwp_plugin_licenses');

define('HOCWP_PLUGIN_LICENSE_ADMIN_URL', admin_url('plugins.php?page=hocwp_plugin_license'));

require_once(HOCWP_PATH . '/lib/bfi-thumb/BFI_Thumb.php');

require_once(HOCWP_PATH . '/functions.php');

require_once(HOCWP_PATH . '/setup.php');

function hocwp_autoload($class_name) {
    $base_path = HOCWP_PATH;
    $pieces = explode('_', $class_name);
    $pieces = array_filter($pieces);
    $first_piece = current($pieces);
    if('HOCWP' !== $class_name && 'HOCWP' !== $first_piece) {
        return;
    }
    $file = $base_path . '/class-' . hocwp_sanitize_file_name($class_name);
    $file .= '.php';
    if(file_exists($file)) {
        require_once($file);
    }
}

spl_autoload_register('hocwp_autoload');

require_once(HOCWP_PATH . '/utils.php');

require_once(HOCWP_PATH . '/query.php');

require_once(HOCWP_PATH . '/users.php');

require_once(HOCWP_PATH . '/mail.php');

require_once(HOCWP_PATH . '/html-field.php');

require_once(HOCWP_PATH . '/wordpress-seo.php');

require_once(HOCWP_PATH . '/woocommerce.php');

require_once(HOCWP_PATH . '/option.php');

if(hocwp_has_plugin_activated()) {
    require_once(HOCWP_PATH . '/options/plugin-option.php');
}

require_once(HOCWP_PATH . '/theme-switcher.php');

require_once(HOCWP_PATH . '/post.php');

require_once(HOCWP_PATH . '/video.php');

require_once(HOCWP_PATH . '/media.php');

require_once(HOCWP_PATH . '/shop.php');

require_once(HOCWP_PATH . '/statistics.php');

require_once(HOCWP_PATH . '/term.php');

require_once(HOCWP_PATH . '/meta.php');

require_once(HOCWP_PATH . '/term-meta.php');

require_once(HOCWP_PATH . '/login.php');

require_once(HOCWP_PATH . '/comment.php');

require_once(HOCWP_PATH . '/pagination.php');

require_once(HOCWP_PATH . '/front-end.php');

require_once(HOCWP_PATH . '/ajax.php');