<?php
/*
 * Template Name: Review
 */
get_header();
while(have_posts()) {
    the_post();
    hocwp_theme_get_template_page('review');
}
get_footer();