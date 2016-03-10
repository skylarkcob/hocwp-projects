<?php
if(!function_exists('add_filter')) exit;

global $pagenow;

if('edit-tags.php' == $pagenow) {
    // Meta box for term
}

if('edit.php' == $pagenow || 'post.php' == $pagenow) {
    // Meta box for post
}