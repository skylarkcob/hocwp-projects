<?php
/*
 * Template Name: Experience
 */
get_header();
while(have_posts()) {
    the_post();
    hocwp_theme_get_template_page('experience');
}
get_footer();