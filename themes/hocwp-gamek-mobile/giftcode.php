<?php
/*
 * Template Name: Giftcode
 */
get_header();
while(have_posts()) {
    the_post();
    hocwp_theme_get_template_page('giftcode');
}
get_footer();