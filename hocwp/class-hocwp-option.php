<?php
if(!function_exists('add_filter')) exit;
class HOCWP_Option {
    private $menu_title;
    private $page_title;
    private $menu_slug;

    private $capability;
    private $parent_slug;
    private $function;
    private $icon_url;
    private $position;

    private $option_group;
    private $option_name;
    private $sanitize_callback;

    private $section_id;
    private $section_title;
    private $section_callback;
    private $section_description;

    private $fields;
    private $sections;
    private $help_tabs;
    private $help_sidebar;
    private $use_style_and_script;
    private $use_media_upload;
    private $use_color_picker;
    private $use_jquery_ui;
    private $use_jquery_ui_sortable;

    private $exists;
    private $page;

    public function set_page($page) {
        $this->page = $page;
    }

    public function get_page() {
        return $this->page;
    }

    public function set_exists($exists) {
        $this->exists = $exists;
    }

    public function get_exists() {
        return $this->exists;
    }

    public function set_use_style_and_script($use_style_and_script) {
        $this->use_style_and_script = $use_style_and_script;
    }

    public function get_use_style_and_script() {
        return (bool)$this->use_style_and_script;
    }

    public function set_use_jquery_ui_sortable($use) {
        $this->use_jquery_ui_sortable = $use;
    }

    public function get_use_jquery_ui_sortable() {
        return $this->use_jquery_ui_sortable;
    }

    public function set_use_jquery_ui($use) {
        $this->use_jquery_ui = $use;
    }

    public function get_use_jquery_ui() {
        return (bool)$this->use_jquery_ui;
    }

    public function set_use_media_upload($use_media_upload) {
        $this->use_media_upload = $use_media_upload;
    }

    public function get_use_media_upload() {
        return (bool)$this->use_media_upload;
    }

    public function set_use_color_picker($use) {
        $this->use_color_picker = $use;
    }

    public function get_use_color_picker() {
        return (bool)$this->use_color_picker;
    }

    public function set_help_sidebar($help_sidebar) {
        $this->help_sidebar = $help_sidebar;
    }

    public function get_help_sidebar() {
        return $this->help_sidebar;
    }

    public function set_help_tabs($help_tabs) {
        $this->help_tabs = $help_tabs;
    }

    public function get_help_tabs() {
        return $this->help_tabs;
    }

    public function set_section_description($section_description) {
        $this->section_description = $section_description;
    }

    public function get_section_description() {
        return $this->section_description;
    }

    public function set_fields($fields) {
        $this->fields = $fields;
    }

    public function get_fields() {
        return $this->fields;
    }

    public function set_sections($sections) {
        $this->sections = $sections;
    }

    public function get_sections() {
        return $this->sections;
    }

    public function set_section_callback($section_callback) {
        $this->section_callback = $section_callback;
    }

    public function get_section_callback() {
        if(!hocwp_callback_exists($this->section_callback)) {
            $this->set_section_callback(array($this, 'default_section_callback'));
        }
        return $this->section_callback;
    }

    public function set_section_id($section_id) {
        $this->section_id = $section_id;
    }

    public function get_section_id() {
        return $this->section_id;
    }

    public function set_section_title($section_title) {
        $this->section_title = $section_title;
    }

    public function get_section_title() {
        return $this->section_title;
    }

    public function set_sanitize_callback($sanitize_callback) {
        $this->sanitize_callback = $sanitize_callback;
    }

    public function get_sanitize_callback() {
        return $this->sanitize_callback;
    }

    public function set_option_name($option_name) {
        $this->option_name = $option_name;
    }

    public function get_option_name() {
        if(empty($this->option_name)) {
            $this->option_name = $this->get_menu_slug();
        }
        return $this->option_name;
    }

    public function get_option_name_no_prefix() {
        $option_name = $this->get_option_name();
        $option_name = str_replace('hocwp_', '', $option_name);
        return $option_name;
    }

    public function set_option_group($option_group) {
        $this->option_group = $option_group;
    }

    public function get_option_group() {
        if(empty($this->option_group)) {
            $this->option_group = $this->get_menu_slug();
        }
        return $this->option_group;
    }

    public function set_menu_title($menu_title) {
        $this->menu_title = $menu_title;
    }

    public function get_menu_title() {
        return $this->menu_title;
    }

    public function set_menu_slug($menu_slug) {
        $this->menu_slug = $menu_slug;
    }

    public function get_menu_slug() {
        return $this->menu_slug;
    }

    public function set_page_title($page_title) {
        $this->page_title = $page_title;
    }

    public function get_page_title() {
        return $this->page_title;
    }

    public function set_capability($capability) {
        $this->capability = $capability;
    }

    public function get_capability() {
        return $this->capability;
    }

    public function set_parent_slug($parent_slug) {
        $this->parent_slug = $parent_slug;
    }

    public function get_parent_slug() {
        return $this->parent_slug;
    }

    public function set_function($function) {
        $this->function = $function;
    }

    public function get_function() {
        if(!hocwp_callback_exists($this->function)) {
            $this->function = array($this, 'default_setting_page_callback');
        }
        return $this->function;
    }

    public function set_icon_url($icon_url) {
        $this->icon_url = $icon_url;
    }

    public function get_icon_url() {
        return $this->icon_url;
    }

    public function set_position($position) {
        $this->position = $position;
    }

    public function get_position() {
        return $this->position;
    }

    public function __construct($menu_title, $menu_slug) {
        $this->set_menu_title($menu_title);
        $this->set_page_title($menu_title);
        $this->set_menu_slug($menu_slug);
        $this->set_sanitize_callback(array($this, 'sanitize'));
        $this->set_capability('manage_options');

        $this->set_parent_slug('options-general.php');

        $this->set_section_id('default');
        $this->set_section_title('');

        $this->set_sections(array());
        $this->set_fields(array());
        $this->set_help_tabs(array());

        if(empty($menu_title)) {
            $this->set_exists(true);
        }
        if($this->get_exists()) {
            $this->set_option_name('hocwp_' . hocwp_sanitize_id($menu_slug));
        }
    }

    public function is_this_page() {
        global $pagenow;
        $page = hocwp_get_current_admin_page();
        if($page == $this->get_menu_slug()) {
            if(($this->is_submenu() && $pagenow == $this->get_parent_slug()) || 'admin.php' == $pagenow) {
                return true;
            }
        } elseif($pagenow == $this->get_page()) {
            return true;
        }
        return false;
    }

    public function form() {
        ?>
        <form novalidate="novalidate" action="options.php" method="post">
            <?php
            settings_fields($this->get_option_group());
            do_settings_sections($this->get_menu_slug());
            submit_button();
            ?>
        </form>
        <?php
    }

    public function default_setting_page_callback() {
        $title = $this->get_menu_title();
        if(!hocwp_string_contain(strtolower($title), 'settings')) {
            hocwp_add_string_with_space_before($title, 'Settings');
        }
        ?>
        <div class="wrap hocwp">
            <h1 class="page-title"><?php echo esc_html($title); ?></h1>
            <?php
            if($this->is_this_page() && (isset($_REQUEST['submit']) || isset($_REQUEST['settings-updated']))) {
                do_action('hocwp_option_saved');
                if('options-general.php' != $this->get_parent_slug()) {
                    hocwp_admin_notice_setting_saved();
                }
                do_action($this->get_menu_slug() . '_option_saved', $this);
            }
            $this->form();
            ?>
        </div>
        <?php
    }

    public function init() {
        if($this->get_exists()) {
            $this->exists_page_init();
        } else {
            add_action('admin_menu', array($this, 'menu_init'));
            add_action('admin_init', array($this, 'setting_init'));
            if($this->is_this_page()) {
                add_action('admin_head', array($this, 'help_tab_init'));
                add_action('admin_enqueue_scripts', array($this, 'enqueue_style_and_script'));
            }
        }
    }

    public function enqueue_style_and_script() {
        if($this->get_use_style_and_script()) {
            add_filter('hocwp_use_admin_style_and_script', '__return_true');
        }
        if($this->get_use_color_picker()) {
            add_filter('hocwp_use_color_picker', '__return_true');
        }
        if($this->get_use_media_upload()) {
            add_filter('hocwp_wp_enqueue_media', '__return_true');
        }
        if($this->get_use_jquery_ui()) {
            add_filter('hocwp_use_jquery_ui', '__return_true');
        }
        if($this->get_use_jquery_ui_sortable()) {
            add_filter('hocwp_use_jquery_ui_sortable', '__return_true');
        }
        if(!hocwp_is_my_theme()) {
            add_action('admin_print_scripts', 'hocwp_admin_enqueue_scripts');
        }
    }

    public function help_tab_init() {
        $current_screen = get_current_screen();
        foreach($this->get_help_tabs() as $args) {
            $current_screen->add_help_tab($args);
        }
        $help_sidebar = $this->get_help_sidebar();
        $current_screen->set_help_sidebar($help_sidebar);
    }

    public function setting_init() {
        $this->register_setting();
        $this->section_init();
        $this->field_init();
    }

    public function exists_page_init() {
        add_action('admin_init', array($this, 'exists_setting_page_field'));
    }

    public function exists_setting_page_field() {
        $this->setting_init();
    }

    private function section_init() {
        if(!$this->get_exists()) {
            $this->add_settings_section_default();
        }
        foreach($this->get_sections() as $args) {
            $id = isset($args['id']) ? $args['id'] : '';
            $title = isset($args['title']) ? $args['title'] : '';
            $callback = isset($args['callback']) ? $args['callback'] : '';
            if(!hocwp_callback_exists($callback)) {
                $callback = array($this, 'default_section_callback');
            }
            $this->add_settings_section($id, $title, $callback);
        }
    }

    public function section_description($text) {
        $p = new HOCWP_HTML('p');
        $p->set_text($text);
        $p->output();
    }

    public function sanitize($input) {
        $input = apply_filters('hocwp_sanitize_option_' . $this->get_option_name_no_prefix(), $input);
        do_action('hocwp_sanitize_' . $this->get_option_name_no_prefix() . '_option', $input);
        $input = apply_filters('validate_options', $input);
        return $input;
    }

    public function field_init() {
        foreach($this->get_fields() as $args) {
            $id = isset($args['id']) ? $args['id'] : '';
            $title = isset($args['title']) ? $args['title'] : '';
            $callback = isset($args['callback']) ? $args['callback'] : '';
            $section = isset($args['section']) ? $args['section'] : $this->get_section_id();
            if(!hocwp_callback_exists($callback)) {
                $callback = array($this, 'default_field_callback');
            }
            if($this->get_exists()) {
                $class = isset($args['class']) ? $args['class'] : '';
                hocwp_add_string_with_space_before($class, 'hocwp');
                $args['class'] = $class;
            }
            $this->add_settings_field($id, $title, $callback, $section, $args);
        }
    }

    public function default_field_callback($args) {
        $callback = isset($args['field_callback']) ? $args['field_callback'] : 'hocwp_field_input';
        if(hocwp_callback_exists($callback)) {
            $name = isset($args['name']) ? hocwp_sanitize_array($args['name']) : array();
            if(!hocwp_array_has_value($name)) {
                $name = isset($args['id']) ? hocwp_sanitize_array($args['id']) : array();
            }
            if(!hocwp_array_has_value($name)) {
                _e('Please set name for this field.', 'hocwp');
                return;
            }
            $base_name = $name;
            unset($args['class']);
            if(!isset($args['value'])) {
                $args['value'] = $this->get_by_key($name, hocwp_get_value_by_key($args, 'default'));
            }
            $args['name'] = $this->get_field_name($name);
            $args['id'] = $this->get_field_id($name);
            $options = isset($args['options']) ? $args['options'] : array();
            if(hocwp_array_has_value($options)) {
                $tmp_options = array();
                foreach($options as $option) {
                    $name = isset($option['name']) ? hocwp_sanitize_array($option['name']) : array();
                    if(!hocwp_array_has_value($name)) {
                        if(!empty($base_name)) {
                            $name = hocwp_sanitize_array($base_name);
                        } else {
                            $name = isset($option['id']) ? hocwp_sanitize_array($option['id']) : array();
                        }
                    }
                    if(!hocwp_array_has_value($name)) {
                        continue;
                    }
                    if(!isset($option['value'])) {
                        $option['value'] = $this->get_by_key($name, hocwp_get_value_by_key($option, 'default'));
                    }
                    $option['name'] = $this->get_field_name($name);
                    $option['id'] = $this->get_field_id($name);
                    $tmp_options[] = $option;
                }
                $args['options'] = $tmp_options;
                unset($tmp_options);
            }
            call_user_func($callback, $args);
        } else {
            _e('Please set a valid callback for this field.', 'hocwp');
        }
    }

    public function get() {
        return get_option($this->get_option_name());
    }

    public function get_page_url() {
        $url = '';
        if($this->is_submenu()) {
            $url .= $this->get_parent_slug();
        } else {
            $url .= 'admin.php';
        }
        $url .= '?page=' . $this->get_menu_slug();
        return admin_url($url);
    }

    public function get_by_key($key, $default = '') {
        $result = '';
        $options = $this->get();
        if(is_array($options)) {
            $result = hocwp_get_value_by_key($options, $key, $default);
        }
        return $result;
    }

    public function get_field_name($field_name) {
        return hocwp_sanitize_field_name($this->get_option_name(), $field_name);
    }

    public function get_field_id($field_id) {
        $id = $this->get_field_name($field_id);
        $id = hocwp_sanitize_id($id);
        return $id;
    }

    private function get_section_by_id($id) {
        $section = array();
        $sections = $this->get_sections();
        foreach($sections as $args) {
            $section_id = isset($args['id']) ? $args['id'] : '';
            if($id == $section_id) {
                $section = $args;
                break;
            }
        }
        return $section;
    }

    public function default_section_callback($args) {
        $id = isset($args['id']) ? $args['id'] : '';
        $description = '';
        if($id == $this->get_section_id()) {
            $description = $this->get_section_description();
        } else {
            $section = $this->get_section_by_id($id);
            $description = isset($section['description']) ? $section['description'] : '';
        }
        if(!empty($description)) {
            $this->section_description($description);
        }
    }

    public function is_submenu() {
        $menu_parent = $this->get_parent_slug();
        if(!empty($menu_parent) && 'admin.php' != $menu_parent) {
            return true;
        }
        return false;
    }

    public function menu_init() {
        if($this->is_submenu()) {
            $this->add_submenu_page();
        } else {
            $this->add_menu_page();
        }
    }

    public function add_menu_page() {
        add_menu_page($this->get_page_title(), $this->get_menu_title(), $this->get_capability(), $this->get_menu_slug(), $this->get_function(), $this->get_icon_url(), $this->get_position());
    }

    public function add_submenu_page() {
        add_submenu_page($this->get_parent_slug(), $this->get_page_title(), $this->get_menu_title(), $this->get_capability(), $this->get_menu_slug(), $this->get_function());
    }

    public function register_setting() {
        register_setting($this->get_option_group(), $this->get_option_name(), $this->get_sanitize_callback());
    }

    public function add_settings_section($id, $title, $callback) {
        add_settings_section($id, $title, $callback, $this->get_menu_slug());
    }

    public function add_settings_section_default() {
        $this->add_settings_section($this->get_section_id(), $this->get_section_title(), $this->get_section_callback());
    }

    public function add_section($args = array()) {
        $this->sections[] = $args;
    }

    private function add_settings_field($id, $title, $callback, $section = 'default', $args = array()) {
        add_settings_field($id, $title, $callback, $this->get_menu_slug(), $section, $args);
    }

    public function add_field($args = array()) {
        $id = isset($args['id']) ? $args['id'] : '';
        $name = isset($args['name']) ? $args['name'] : '';
        $class = isset($args['class']) ? $args['class'] : '';
        if(!isset($args['field_class'])) {
            $args['field_class'] = $class;
        }
        hocwp_transmit_id_and_name($id, $name);
        $args['id'] = $id;
        $args['name'] = $name;
        $this->fields[] = $args;
    }

    public function do_settings_fields($section = 'default') {
        do_settings_fields($this->get_menu_slug(), $section);
    }

    public function add_help_tab($args) {
        $this->help_tabs[] = $args;
    }
}