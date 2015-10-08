<?php
/*
 * Template Name: Video
 */
get_header();
while(have_posts()) {
    the_post();
    hocwp_theme_get_template_page('video');
}
get_footer();