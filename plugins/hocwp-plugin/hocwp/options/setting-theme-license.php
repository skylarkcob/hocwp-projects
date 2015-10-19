<?php
$parent_slug = 'themes.php';

$option_theme_license = new HOCWP_Option(__('Theme license', 'hocwp'), 'hocwp_theme_license');
$option_theme_license->set_parent_slug($parent_slug);
$option_theme_license->add_field(array('id' => 'customer_email', 'title' => __('Customer email', 'hocwp')));
$option_theme_license->add_field(array('id' => 'license_code', 'title' => __('License code', 'hocwp')));
$option_theme_license->add_help_tab(array(
	'id' => 'overview',
	'title' => __('Overview', 'hocwp'),
	'content' => '<p>' . sprintf(__('Thank you for using WordPress theme by %s.', 'hocwp'), HOCWP_NAME) . '</p>' .
	             '<p>' . __('With each theme, you will receive a license code to activate it. Please enter your theme license information into the form below, if you do not have one, please contact the author to get new code.', 'hocwp') . '</p>'
));
$option_theme_license->set_help_sidebar(
	'<p><strong>' . __('For more information:', 'hocwp') . '</strong></p>' .
	'<p><a href="http://hocwp.net/quy-dinh-su-dung-ban-quyen-giao-dien/" target="_blank">' . __('Rules of using license', 'hocwp') . '</a></p>' .
	'<p><a href="http://hocwp.net/lien-he/" target="_blank">' . __('Contact Us', 'hocwp') . '</a></p>'
);
$option_theme_license->init();
hocwp_option_add_object_to_list($option_theme_license);