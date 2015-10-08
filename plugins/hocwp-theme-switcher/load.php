<?php
require_once(HOCWP_THEME_SWITCHER_INC_PATH . '/hocwp-plugin-pre-hook.php');

if(!defined('HOCWP_PATH')) {
    $path = get_template_directory() . '/hocwp/load.php';

    if(!file_exists($path)) {
        $path = HOCWP_THEME_SWITCHER_PATH . '/hocwp/load.php';
    }

    require_once($path);
}

require_once(HOCWP_THEME_SWITCHER_INC_PATH . '/hocwp-plugin-setup.php');

require_once(HOCWP_THEME_SWITCHER_INC_PATH . '/hocwp-plugin-functions.php');

require_once(HOCWP_THEME_SWITCHER_INC_PATH . '/hocwp-plugin-admin.php');

require_once(HOCWP_THEME_SWITCHER_INC_PATH . '/hocwp-plugin-hook.php');

require_once(HOCWP_THEME_SWITCHER_INC_PATH . '/hocwp-plugin-ajax.php');