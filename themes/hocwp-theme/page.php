<?php
if(!function_exists('add_filter')) exit;
get_header();
while(have_posts()) {
    the_post();
    hocwp_theme_get_template('page');
}
get_footer();