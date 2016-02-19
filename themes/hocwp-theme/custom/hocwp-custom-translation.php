<?php
if(!function_exists('add_filter')) exit;
$theme_options = get_option('hocwp_theme_setting');
$footer_text = hocwp_get_value_by_key($theme_options, 'footer_text');
$sub_promo = hocwp_get_value_by_key($theme_options, 'sub_promo');
hocwp_theme_register_translation_text('hocwp_footer_text', $footer_text, true);
hocwp_theme_register_translation_text('hocwp_sub_promo', $sub_promo, true);
hocwp_theme_register_translation_text('hocwp_my_account', 'My Account');
hocwp_theme_register_translation_text('hocwp_logout', 'Logout');
hocwp_theme_register_translation_text('hocwp_my_account_login', 'My Account / Login');
hocwp_theme_register_translation_text('hocwp_register', 'Register');