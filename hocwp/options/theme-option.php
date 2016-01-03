<?php
if(!function_exists('add_filter')) exit;

global $hocwp_theme_option, $hocwp_tos_tabs;

$hocwp_theme_option = new HOCWP_Option(__('Theme Options', 'hocwp'), 'hocwp_theme_option');
$hocwp_theme_option->set_parent_slug('');
$hocwp_theme_option->set_icon_url('dashicons-admin-generic');
$hocwp_theme_option->set_position(61);
$hocwp_theme_option->set_use_style_and_script(true);
$hocwp_theme_option->init();

require(HOCWP_PATH . '/options/setting-theme-setting.php');
require(HOCWP_PATH . '/options/setting-theme-custom.php');
require(HOCWP_PATH . '/options/setting-theme-custom-css.php');
require(HOCWP_PATH . '/options/setting-theme-add-to-head.php');
require(HOCWP_PATH . '/options/setting-optimize.php');
require(HOCWP_PATH . '/options/setting-social.php');
require(HOCWP_PATH . '/options/setting-login.php');
require(HOCWP_PATH . '/options/setting-smtp-email.php');
require(HOCWP_PATH . '/options/setting-writing.php');
require(HOCWP_PATH . '/options/setting-reading.php');
require(HOCWP_PATH . '/options/setting-discussion.php');
require(HOCWP_PATH . '/options/setting-theme-license.php');
require(HOCWP_PATH . '/options/setting-maintenance.php');
require(HOCWP_PATH . '/options/setting-recommend-plugin.php');
require(HOCWP_PATH . '/options/setting-theme-about.php');

function hocwp_theme_option_form_before() {
    global $hocwp_theme_option;
    $theme = wp_get_theme();
    $name = $theme->get('Name');
    if(empty($name)) {
        $name = __('Unknown', 'hocwp');
    }
    $version = $theme->get('Version');
    if(empty($version)) {
        $version = '1.0.0';
    }
    ?>
    <div class="page-header">
        <h2 class="theme-name"><?php echo $name; ?></h2>
        <span class="theme-version"><?php printf(__('Version: %s', 'hocwp'), $version); ?></span>
    </div>
    <?php
}

function hocwp_theme_option_form_after() {
    global $hocwp_theme_option;
    $theme = wp_get_theme();
    $name = $theme->get('Name');
    if(empty($name)) {
        $name = __('Unknown', 'hocwp');
    }
    $version = $theme->get('Version');
    if(empty($version)) {
        $version = '1.0.0';
    }
    $hocwp_root_domain = hocwp_get_root_domain_name(HOCWP_HOMEPAGE);
    ?>
    <div class="page-footer">
        <p>Created by <?php echo $hocwp_root_domain; ?>. If you have any questions, please send us an email via address: <em><?php echo HOCWP_EMAIL; ?></em></p>
    </div>
    <div class="copyright">
        <p>&copy; 2008 - <?php echo date('Y'); ?> <a target="_blank" href="<?php echo HOCWP_HOMEPAGE; ?>"><?php echo $hocwp_root_domain; ?></a>. All Rights Reserved.</p>
    </div>
    <?php
}

function hocwp_theme_option_sidebar_tab() {
    global $hocwp_tos_tabs;
    if(hocwp_array_has_value($hocwp_tos_tabs)) {
        $current_page = hocwp_get_current_admin_page();
        ?>
        <ul class="list-tabs">
            <?php foreach($hocwp_tos_tabs as $key => $value) : ?>
                <?php
                $admin_url = admin_url('admin.php');
                $admin_url = add_query_arg(array('page' => $key), $admin_url);
                $item_class = hocwp_sanitize_html_class($key);
                if($key == $current_page) {
                    hocwp_add_string_with_space_before($item_class, 'active');
                    $admin_url = 'javascript:;';
                }
                $text = hocwp_get_value_by_key($value, 'text');
                if(empty($text)) {
                    continue;
                }
                ?>
                <li class="<?php echo $item_class; ?>"><a href="<?php echo $admin_url; ?>"><span><?php echo $text; ?></span></a></li>
            <?php endforeach; ?>
        </ul>
        <?php
    }
}