<?php
if(!function_exists('add_filter')) exit;

global $pagenow;

if('edit-tags.php' == $pagenow || 'term.php' == $pagenow) {
    // Meta box for term
}

if('post-new.php' == $pagenow || 'post.php' == $pagenow) {
    // Meta box for post
}