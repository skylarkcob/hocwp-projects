<?php
if(!function_exists('add_filter')) exit;

global $hocwp_tos_tabs;
$parent_slug = 'hocwp_theme_option';

$option = new HOCWP_Option(__('Recommended Plugins', 'hocwp-theme'), 'hocwp_recommended_plugin');
$option->set_parent_slug($parent_slug);
$option->set_is_option_page(false);
$option->set_menu_title(__('Plugins', 'hocwp-theme'));
$option->set_use_style_and_script(true);
$option->add_option_tab($hocwp_tos_tabs);
$option->set_page_header_callback('hocwp_theme_option_form_before');
$option->set_page_footer_callback('hocwp_theme_option_form_after');
$option->set_page_sidebar_callback('hocwp_theme_option_sidebar_tab');
$option->init();
hocwp_option_add_object_to_list($option);

function hocwp_option_page_recommended_plugin_content() {
    $base_url = 'admin.php?page=hocwp_recommended_plugin';
    $current_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 'installed';
    $tabs = array(
        'installed' => __('Installed', 'hocwp-theme'),
        'activated' => __('Activated', 'hocwp-theme'),
        'required' => __('Required', 'hocwp-theme'),
        'recommended' => __('Recommended', 'hocwp-theme')
    );
    $plugins = array();
    switch($current_tab) {
        case 'required':
            $defaults = hocwp_recommended_plugins();
            $lists = hocwp_get_value_by_key($defaults, 'required');
            foreach($lists as $key => $data) {
                $slug = hocwp_get_plugin_slug_from_file_path($data);
                $plugins[$slug] = $data;
            }
            break;
        case 'installed':
            $lists = hocwp_get_installed_plugins();
            foreach($lists as $key => $data) {
                $slug = hocwp_get_plugin_slug_from_file_path($key);
                $plugins[$slug] = $key;
            }
            break;
        case 'activated':
            $lists = get_option('active_plugins');
            foreach($lists as $key => $data) {
                $slug = hocwp_get_plugin_slug_from_file_path($data);
                $plugins[$slug] = $data;
            }
            break;
        case 'recommended':
            $defaults = hocwp_recommended_plugins();
            $lists = hocwp_get_value_by_key($defaults, 'recommended');
            foreach($lists as $key => $data) {
                $slug = hocwp_get_plugin_slug_from_file_path($data);
                $plugins[$slug] = $data;
            }
            break;
    }
    ?>
    <div class="wp-filter">
        <ul class="filter-links">
            <?php foreach($tabs as $id => $text) : ?>
                <?php
                $url = add_query_arg(array('tab' => $id), $base_url);
                $link_class = '';
                if($id == $current_tab) {
                    hocwp_add_string_with_space_before($link_class, 'current');
                }
                ?>
                <li class="plugin-install-<?php echo $id; ?>">
                    <a class="<?php echo $link_class; ?>" data-tab="<?php echo $id; ?>" href="<?php echo $url ?>"><?php echo $text; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <br class="clear">
    <p>Plugins extend and expand the functionality of WordPress. You may automatically install plugins from the <a href="https://wordpress.org/plugins/">WordPress Plugin Directory</a> or upload a plugin in .zip format via <a href="<?php echo admin_url('plugin-install.php?tab=upload'); ?>">this page</a>.</p>
    <div id="the-list" class="widefat">
        <?php
        $plugin_items = array();
        foreach($plugins as $key => $data) {
            $plugin_information = hocwp_plugins_api_get_information(array('slug' => $key));
            $plugin_items[$data] = $plugin_information;
        }
        $plugins_allowedtags = array(
            'a' => array('href' => array(),'title' => array(), 'target' => array()),
            'abbr' => array('title' => array()),'acronym' => array('title' => array()),
            'code' => array(), 'pre' => array(), 'em' => array(), 'strong' => array(),
            'ul' => array(), 'ol' => array(), 'li' => array(), 'p' => array(), 'br' => array()
        );
        ?>
        <?php
        foreach($plugin_items as $key => $plugin) {
            hocwp_loop_plugin_card($plugin, $plugins_allowedtags, $key);
        }
        ?>
    </div>
    <?php
}
add_action('hocwp_option_page_' . $option->get_option_name_no_prefix() . '_content', 'hocwp_option_page_recommended_plugin_content');