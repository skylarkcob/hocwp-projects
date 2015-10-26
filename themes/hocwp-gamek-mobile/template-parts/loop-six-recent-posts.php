<?php
hocwp_article_before('label-top-left icon-play-medium with-text');
$args = array('width' => 492, 'height' => 429);
if(!wp_is_mobile()) {
    $args['bfi_thumb'] = false;
}
hocwp_post_thumbnail($args);
hocwp_post_title_link();
hocwp_theme_custom_post_label();
hocwp_theme_custom_post_icon_play();
hocwp_article_after();