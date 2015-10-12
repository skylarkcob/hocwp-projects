<?php
$path = get_template_directory() . '/hocwp/load.php';

if(!defined('HOCWP_URL')) {
    if(file_exists($path)) {
        define('HOCWP_URL', untrailingslashit(get_template_directory_uri()) . '/hocwp');
    } else {
        define('HOCWP_URL', untrailingslashit(HOCWP_DAT_VE_TAU_URL) . '/hocwp');
    }
}

require_once(HOCWP_DAT_VE_TAU_INC_PATH . '/hocwp-plugin-pre-hook.php');

function hocwp_dat_ve_tau_missing_core_notice() {
    ?>
    <div class="updated notice settings-error error">
        <p><strong><?php _e('Error:', 'hocwp-dat-ve-tau'); ?></strong> <?php _e('Plugin HocWP Đặt Vé Tàu cannot be run properly because of missing core.', 'hocwp-dat-ve-tau'); ?></p>
    </div>
    <?php
}

if(!defined('HOCWP_PATH')) {
    if(!file_exists($path)) {
        $path = HOCWP_DAT_VE_TAU_PATH . '/hocwp/load.php';
    }

    if(!file_exists($path)) {
        add_action('admin_notices', 'hocwp_dat_ve_tau_missing_core_notice');
        return;
    }

    require_once($path);
}

require_once(HOCWP_PATH . '/plugin-functions.php');

require_once(HOCWP_DAT_VE_TAU_INC_PATH . '/hocwp-plugin-functions.php');

require_once(HOCWP_DAT_VE_TAU_INC_PATH . '/hocwp-plugin-setup.php');

require_once(HOCWP_DAT_VE_TAU_INC_PATH . '/hocwp-plugin-admin.php');

require_once(HOCWP_DAT_VE_TAU_INC_PATH . '/hocwp-plugin-hook.php');

require_once(HOCWP_DAT_VE_TAU_INC_PATH . '/hocwp-plugin-ajax.php');