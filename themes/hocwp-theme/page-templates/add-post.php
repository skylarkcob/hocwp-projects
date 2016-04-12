<?php
/*
 * Template Name: Add Post
 */
if(!function_exists('add_filter')) exit;
get_header();
while(have_posts()) {
	the_post();
	hocwp_theme_get_template_page('add-post');
}
get_footer();