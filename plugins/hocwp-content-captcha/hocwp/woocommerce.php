<?php
function hocwp_get_wc_version() {
    if(defined('WOOCOMMERCE_VERSION')) {
        return WOOCOMMERCE_VERSION;
    }
    return '';
}

function hcowp_wc_installed() {
    return defined('WOOCOMMERCE_VERSION');
}