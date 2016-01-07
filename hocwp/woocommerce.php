<?php
if(!function_exists('add_filter')) exit;
function hocwp_get_wc_version() {
    if(defined('WOOCOMMERCE_VERSION')) {
        return WOOCOMMERCE_VERSION;
    }
    return '';
}

function hocwp_wc_installed() {
    return defined('WOOCOMMERCE_VERSION');
}