<?php
if(!function_exists('add_filter')) exit;

if(!defined('HOCWP_REQUIRE_WP_VERSION')) {
    define('HOCWP_REQUIRE_WP_VERSION', '4.1');
}

if(version_compare($GLOBALS['wp_version'], HOCWP_REQUIRE_WP_VERSION, '<')) {
    require get_template_directory() . '/inc/back-compat.php';
    return;
}

define('HOCWP_THEME_CORE_VERSION', '5.0.7');

define('HOCWP_THEME_PATH', get_template_directory());

define('HOCWP_THEME_INC_PATH', HOCWP_THEME_PATH . '/inc');

define('HOCWP_THEME_URL', get_template_directory_uri());

define('HOCWP_THEME_INC_URL', HOCWP_THEME_URL . '/inc');

define('HOCWP_THEME_CUSTOM_PATH', HOCWP_THEME_PATH . '/custom');

if(!defined('HOCWP_URL')) {
    define('HOCWP_URL', untrailingslashit(get_template_directory_uri()) . '/hocwp');
}

function hocwp_theme_missing_core_notice() {
    ?>
    <div class="updated notice settings-error error">
        <p><strong><?php _e('Error:', 'hocwp'); ?></strong> <?php _e('Current theme cannot be run properly because of missing core.', 'hocwp'); ?></p>
    </div>
    <?php
}

if(!defined('HOCWP_PATH')) {
    if(!file_exists(HOCWP_THEME_PATH . '/hocwp/load.php')) {
        if(is_admin()) {
            add_action('admin_notices', 'hocwp_theme_missing_core_notice');
        } else {
            wp_die('<strong>' . __('Error:', 'hocwp') . '</strong> ' . __('Theme cannot be displayed because of missing core. Please contact administrator for assistance.', 'hocwp'), __('Missing Core', 'hocwp'));
            exit;
        }
        return;
    }
    require_once(HOCWP_THEME_PATH . '/hocwp/load.php');
}

require_once(HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-pre-hook.php');

require_once(HOCWP_THEME_INC_PATH . '/options/theme-option.php');

require_once(HOCWP_THEME_INC_PATH . '/theme-functions.php');

require_once(HOCWP_THEME_INC_PATH . '/setup-theme.php');

require HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-functions.php';

require HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-admin.php';

require HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-post-type-and-taxonomy.php';

require HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-meta.php';

require HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-hook.php';

require HOCWP_THEME_CUSTOM_PATH . '/hocwp-custom-ajax.php';

require_once(HOCWP_THEME_INC_PATH . '/setup-theme-after.php');