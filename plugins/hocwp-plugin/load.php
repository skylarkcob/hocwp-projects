<?php
$path = get_template_directory() . '/hocwp/load.php';

if(!defined('HOCWP_URL')) {
    if(file_exists($path)) {
        define('HOCWP_URL', untrailingslashit(get_template_directory_uri()) . '/hocwp');
    } else {
        define('HOCWP_URL', untrailingslashit(HOCWP_PLUGIN_DEFAULT_URL) . '/hocwp');
    }
}

require_once(HOCWP_PLUGIN_DEFAULT_INC_PATH . '/hocwp-plugin-pre-hook.php');

function hocwp_plugin_default_missing_core_notice() {
    $plugin_data = get_plugin_data(HOCWP_PLUGIN_DEFAULT_FILE);
    ?>
    <div class="updated notice settings-error error">
        <p><strong><?php _e('Error:', 'hocwp-plugin-default'); ?></strong> <?php printf(__('Plugin %s cannot be run properly because of missing core.', 'hocwp-plugin-default'), '<strong>' . $plugin_data['Name'] . '</strong>'); ?></p>
    </div>
    <?php
}

if(!defined('HOCWP_PATH')) {
    if(!file_exists($path)) {
        $path = HOCWP_PLUGIN_DEFAULT_PATH . '/hocwp/load.php';
    }

    if(!file_exists($path)) {
        add_action('admin_notices', 'hocwp_plugin_default_missing_core_notice');
        return;
    }

    require_once($path);
}

require_once(HOCWP_PATH . '/plugin-functions.php');

require_once(HOCWP_PLUGIN_DEFAULT_INC_PATH . '/hocwp-plugin-functions.php');

require_once(HOCWP_PLUGIN_DEFAULT_INC_PATH . '/hocwp-plugin-setup.php');

require_once(HOCWP_PLUGIN_DEFAULT_INC_PATH . '/hocwp-plugin-admin.php');

require_once(HOCWP_PLUGIN_DEFAULT_INC_PATH . '/hocwp-plugin-hook.php');

require_once(HOCWP_PLUGIN_DEFAULT_INC_PATH . '/hocwp-plugin-ajax.php');