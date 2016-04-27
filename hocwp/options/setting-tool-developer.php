<?php
if(!function_exists('add_filter')) exit;

global $pagenow;

$parent_slug = 'tools.php';

$option = new HOCWP_Option(__('Developers', 'hocwp'), 'hocwp_developers');
$option->set_parent_slug($parent_slug);
$option->disable_sidebar();

if(HOCWP_DEVELOPING && hocwp_is_localhost()) {
	$option->init();
}

hocwp_option_add_object_to_list($option);