<?php
/*
 * Template Name: Search Page
 */
get_header();
while(have_posts()) {
    the_post();
    hocwp_theme_get_template_page('search');
}
get_footer();