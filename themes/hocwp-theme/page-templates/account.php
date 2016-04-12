<?php
/*
 * Template Name: Account
 */
if(!function_exists('add_filter')) exit;
get_header();
while(have_posts()) {
	the_post();
	hocwp_theme_get_template_page('account');
}
get_footer();