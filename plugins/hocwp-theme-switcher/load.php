<?php
global $hocwp_plugin_theme_switcher;
$plugin = $hocwp_plugin_theme_switcher;

require( $plugin->custom_path . '/hocwp-plugin-functions.php' );

require( $plugin->custom_path . '/hocwp-plugin-shortcode.php' );

require( $plugin->custom_path . '/hocwp-plugin-admin.php' );

require( $plugin->custom_path . '/hocwp-plugin-post-type-and-taxonomy.php' );

require( $plugin->custom_path . '/hocwp-plugin-meta.php' );

require( $plugin->custom_path . '/hocwp-plugin-hook.php' );

require( $plugin->custom_path . '/hocwp-plugin-ajax.php' );

require( $plugin->custom_path . '/hocwp-plugin-translation.php' );

unset( $plugin );