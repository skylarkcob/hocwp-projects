<?php
if(!function_exists('add_filter')) exit;

if(!hocwp_plugin_default_license_valid()) {
	return;
}