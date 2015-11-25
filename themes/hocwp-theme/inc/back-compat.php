<?php
if(!function_exists('add_filter')) exit;
function hocwp_switch_theme() {
	switch_theme(WP_DEFAULT_THEME, WP_DEFAULT_THEME);
	unset($_GET['activated']);
	add_action('admin_notices', 'hocwp_upgrade_notice');
}
add_action('after_switch_theme', 'hocwp_switch_theme');

function hocwp_require_wp_version_message() {
	$message = sprintf(__('HocWP requires at least WordPress version %1$s. You are running version %2$s. Please upgrade and try again.', 'hocwp'), HOCWP_REQUIRE_WP_VERSION, $GLOBALS['wp_version']);
	return $message;
}

function hocwp_upgrade_notice() {
	printf('<div class="error"><p>%s</p></div>', hocwp_require_wp_version_message());
}

function hocwp_customize() {
	wp_die(hocwp_require_wp_version_message(), '', array(
		'back_link' => true,
	));
}
add_action('load-customize.php', 'hocwp_customize');

function hocwp_preview() {
	if(isset($_GET['preview'])) {
		wp_die(hocwp_require_wp_version_message());
	}
}
add_action('template_redirect', 'hocwp_preview');
