<?php
function hocwp_wrap_tag($text, $tag) {
    $html = new HOCWP_HTML($tag);
    $html->set_text($text);
    return $html->build();
}