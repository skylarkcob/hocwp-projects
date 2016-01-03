<?php
if(!function_exists('add_filter')) exit;

global $hocwp_tos_tabs;
$parent_slug = 'hocwp_theme_option';

$option = new HOCWP_Option(__('Theme license', 'hocwp'), 'hocwp_theme_license');
$option->set_parent_slug($parent_slug);
$option->add_field(array('id' => 'customer_email', 'title' => __('Customer email', 'hocwp')));
$option->add_field(array('id' => 'license_code', 'title' => __('License code', 'hocwp')));
$option->add_help_tab(array(
	'id' => 'overview',
	'title' => __('Overview', 'hocwp'),
	'content' => '<p>' . sprintf(__('Thank you for using WordPress theme by %s.', 'hocwp'), HOCWP_NAME) . '</p>' .
	             '<p>' . __('With each theme, you will receive a license code to activate it. Please enter your theme license information into the form below, if you do not have one, please contact the author to get new code.', 'hocwp') . '</p>'
));
$option->set_help_sidebar(
	'<p><strong>' . __('For more information:', 'hocwp') . '</strong></p>' .
	'<p><a href="http://hocwp.net/quy-dinh-su-dung-ban-quyen-giao-dien/" target="_blank">' . __('Rules of using license', 'hocwp') . '</a></p>' .
	'<p><a href="http://hocwp.net/lien-he/" target="_blank">' . __('Contact Us', 'hocwp') . '</a></p>'
);
$option->add_option_tab($hocwp_tos_tabs);
$option->set_page_header_callback('hocwp_theme_option_form_before');
$option->set_page_footer_callback('hocwp_theme_option_form_after');
$option->set_page_sidebar_callback('hocwp_theme_option_sidebar_tab');
$option->init();
hocwp_option_add_object_to_list($option);

function hocwp_theme_license_option_saved($option) {
	if(is_a($option, 'HOCWP_Option')) {
		hocwp_delete_transient_license_valid();
	}
}
add_action($option->get_menu_slug() . '_option_saved', 'hocwp_theme_license_option_saved');