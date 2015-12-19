<?php
if(!function_exists('add_filter')) exit;
function hocwp_theme_register_lib_bootstrap() {
    wp_register_style('bootstrap-style', get_template_directory_uri() . '/lib/bootstrap/css/bootstrap.min.css');
    wp_register_style('bootstrap-theme-style', get_template_directory_uri() . '/lib/bootstrap/css/bootstrap-theme.min.css', array('bootstrap-style'));
    wp_register_script('bootstrap', get_template_directory_uri() . '/lib/bootstrap/js/bootstrap.min.js', array('jquery'), false, true);
}

function hocwp_theme_register_lib_superfish() {
    wp_register_style('superfish-style', get_template_directory_uri() . '/lib/superfish/css/superfish.min.css');
    wp_register_script('superfish', get_template_directory_uri() . '/lib/superfish/js/superfish.min.js', array('jquery'), false, true);
}

function hocwp_theme_register_lib_font_awesome() {
    wp_register_style('font-awesome-style', get_template_directory_uri() . '/lib/font-awesome/css/font-awesome.min.css');
}

function hocwp_theme_default_script_localize_object() {
    $defaults = hocwp_default_script_localize_object();
    $args = array(
        'login_logo_url' => hocwp_get_login_logo_url(),
        'mobile_menu_icon' => '<button class="menu-toggle mobile-menu-button" aria-expanded="false" aria-controls=""><i class="fa fa fa-bars"></i><span class="text">' . __('Menu', 'hocwp') . '</span></button>'
    );
    $args = wp_parse_args($args, $defaults);
    return apply_filters('hocwp_theme_default_script_object', $args);
}

function hocwp_theme_register_core_style_and_script() {
    hocwp_register_core_style_and_script();
}

function hocwp_theme_get_template($slug, $name = '') {
    $slug = 'template-parts/' . $slug;
    get_template_part($slug, $name);
}

function hocwp_theme_get_content_none() {
    hocwp_theme_get_template('content', 'none');
}

function hocwp_theme_get_template_page($name) {
    hocwp_theme_get_template('page', $name);
}

function hocwp_theme_get_module($name) {
    hocwp_theme_get_template('module', $name);
}

function hocwp_theme_get_loop($name) {
    hocwp_theme_get_template('loop', $name);
}

function hocwp_theme_get_image_url($name) {
    return get_template_directory_uri() . '/images/' . $name;
}

function hocwp_theme_get_option($key, $base = 'theme_setting') {
    return hocwp_option_get_value($base, $key);
}

function hocwp_theme_get_logo_url() {
    $logo = hocwp_theme_get_option('logo');
    $logo = hocwp_sanitize_media_value($logo);
    return $logo['url'];
}

function hocwp_theme_the_logo() {
    $logo_url = hocwp_theme_get_logo_url();
    $logo_class = 'hyperlink';
    if(empty($logo_url)) {
        $logo_url = get_bloginfo('name');
    } else {
        $logo_url = '<img alt="' . get_bloginfo('description') . '" src="' . $logo_url . '">';
        $logo_class = 'img-hyperlink';
    }
    ?>
    <div class="site-branding">
        <?php if(is_front_page() && is_home()) : ?>
            <h1 class="site-title"><a class="<?php echo $logo_class; ?>" title="<?php bloginfo('description'); ?>" href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php echo $logo_url; ?></a></h1>
        <?php else : ?>
            <p class="site-title"><a class="<?php echo $logo_class; ?>" title="<?php bloginfo('description'); ?>" href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php echo $logo_url; ?></a></p>
        <?php endif; ?>
        <p class="site-description"><?php bloginfo('description'); ?></p>
        <?php do_action('hocwp_theme_logo'); ?>
    </div><!-- .site-branding -->
    <?php
}

function hocwp_theme_the_menu($args = array()) {
    $items_wrap = '<ul id="%1$s" class="%2$s">%3$s</ul>';
    $theme_location = isset($args['theme_location']) ? $args['theme_location'] : 'primary';
    $menu_id = isset($args['menu_id']) ? $args['menu_id'] : $theme_location . '_menu';
    $menu_id = hocwp_sanitize_id($menu_id);
    $menu_class = isset($args['menu_class']) ? $args['menu_class'] : '';
    hocwp_add_string_with_space_before($menu_class , 'hocwp-menu');
    hocwp_add_string_with_space_before($menu_class , $theme_location);
    $superfish = isset($args['superfish']) ? $args['superfish'] : true;
    if($superfish) {
        hocwp_add_string_with_space_before($menu_class, 'hocwp-superfish-menu');
        $items_wrap = '<ul id="%1$s" class="sf-menu %2$s">%3$s</ul>';
    }
    $button_text = isset($args['button_text']) ? $args['button_text'] : __('Menu', 'hocwp');
    ?>
    <nav id="site-navigation" class="main-navigation">
        <?php
        $menu_args = array(
            'theme_location' => $theme_location,
            'menu_class' => $menu_class,
            'menu_id' => $menu_id,
            'items_wrap' => $items_wrap
        );
        wp_nav_menu($menu_args);
        ?>
    </nav><!-- #site-navigation -->
    <?php
}

function hocwp_theme_site_main_before() {
    ?>
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
    <?php
}

function hocwp_theme_site_main_after() {
    ?>
        </main>
    </div>
    <?php
}

function hocwp_theme_add_setting_field($args) {
    hocwp_option_add_setting_field('theme_setting', $args);
}

function hocwp_theme_add_setting_field_mobile_logo() {
    hocwp_theme_add_setting_field(array('id' => 'mobile_logo', 'title' => __('Mobile Logo', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload'));
}

function hocwp_theme_add_setting_field_footer_logo() {
    hocwp_theme_add_setting_field(array('title' => __('Footer Logo', 'hocwp'), 'id' => 'footer_logo', 'field_callback' => 'hocwp_field_media_upload'));
}

function hocwp_theme_add_setting_field_footer_text() {
    hocwp_theme_add_setting_field(array('title' => __('Footer Text', 'hocwp'), 'id' => 'footer_text', 'field_callback' => 'hocwp_field_editor'));
}

function hocwp_theme_add_setting_field_select_page($option_name, $title) {
    hocwp_theme_add_setting_field(array('title' => $title, 'id' => $option_name, 'field_callback' => 'hocwp_field_select_page'));
}

function hocwp_theme_add_setting_field_term_sortable($name, $title, $taxonomies = 'category', $only_parent = true) {
    $taxonomies = hocwp_sanitize_array($taxonomies);
    $term_args = array();
    if($only_parent) {
        $term_args['parent'] = 0;
    }
    $args = array(
        'id' => $name,
        'title' => $title,
        'field_callback' => 'hocwp_field_sortable_term',
        'connect' => true,
        'taxonomy' => $taxonomies,
        'term_args' => $term_args
    );
    hocwp_theme_add_setting_field($args);
}

function hocwp_theme_generate_license($password, $site_url = '', $domain = '') {
    if(empty($site_url)) {
        $site_url = get_bloginfo('url');
    }
    $license = new HOCWP_License();
    $license->set_password($password);
    $code = hocwp_generate_serial();
    $license->set_code($code);
    if(empty($domain)) {
        $domain = hocwp_get_root_domain_name($site_url);
    }
    $license->set_domain($domain);
    $license->set_customer_url($site_url);
    $license->generate();
    return $license->get_generated();
}

function hocwp_theme_invalid_license_redirect() {
    $option = hocwp_option_get_object_from_list('theme_license');
    if(hocwp_object_valid($option) && !$option->is_this_page()) {
        global $pagenow;
        $admin_page = hocwp_get_current_admin_page();
        if(('themes.php' != $pagenow || ('themes.php' == $pagenow && !empty($admin_page))) && hocwp_can_redirect()) {
            if(is_admin() || (!is_admin() && !is_user_logged_in())) {
                set_transient('hocwp_invalid_theme_license', 1);
                wp_redirect($option->get_page_url());
                exit;
            }
        } else {
            if(false === get_transient('hocwp_invalid_theme_license')) {
                add_action('admin_notices', 'hocwp_setup_theme_invalid_license_message');
            }
        }
    } else {
        if(false === get_transient('hocwp_invalid_theme_license')) {
            add_action('admin_notices', 'hocwp_setup_theme_invalid_license_message');
        }
    }
}

function hocwp_theme_license_valid($data = array()) {
    global $hocwp_theme_license;
    if(!hocwp_object_valid($hocwp_theme_license)) {
        $hocwp_theme_license = new HOCWP_License();
    }
    return $hocwp_theme_license->check_valid($data);
}

function hocwp_theme_get_license_defined_data() {
    global $hocwp_theme_license_data;
    $hocwp_theme_license_data = hocwp_sanitize_array($hocwp_theme_license_data);
    return apply_filters('hocwp_theme_license_defined_data', $hocwp_theme_license_data);
}