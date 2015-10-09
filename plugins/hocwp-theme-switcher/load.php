<?php
require_once(HOCWP_THEME_SWITCHER_INC_PATH . '/hocwp-plugin-pre-hook.php');

function hocwp_theme_switcher_missing_core_notice() {
    ?>
    <div class="updated notice settings-error error">
        <p><strong><?php _e('Error:', 'hocwp'); ?></strong> <?php _e('Plugin HocWP Theme Switcher cannot be run properly because of missing core.', 'hocwp'); ?></p>
    </div>
    <?php
}

if(!defined('HOCWP_PATH')) {
    $path = get_template_directory() . '/hocwp/load.php';

    if(!file_exists($path)) {
        $path = HOCWP_THEME_SWITCHER_PATH . '/hocwp/load.php';
    }

    if(!file_exists($path)) {
        add_action('admin_notices', 'hocwp_theme_switcher_missing_core_notice');
        return;
    }

    require_once($path);
}

require_once(HOCWP_THEME_SWITCHER_INC_PATH . '/hocwp-plugin-setup.php');

require_once(HOCWP_THEME_SWITCHER_INC_PATH . '/hocwp-plugin-functions.php');

require_once(HOCWP_THEME_SWITCHER_INC_PATH . '/hocwp-plugin-admin.php');

require_once(HOCWP_THEME_SWITCHER_INC_PATH . '/hocwp-plugin-hook.php');

require_once(HOCWP_THEME_SWITCHER_INC_PATH . '/hocwp-plugin-ajax.php');