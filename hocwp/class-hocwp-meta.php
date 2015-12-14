<?php
if(!function_exists('add_filter')) exit;
class HOCWP_Meta {
    private $type;
    private $fields;
    private $callback;
    private $id;
    private $post_types;
    private $title;
    private $context;
    private $priority;
    private $callback_args;
    private $show;
    private $taxonomies;
    private $add_callback;
    private $translation;
    private $column;
    private $edit_callback;

    public function set_edit_callback($edit_callback) {
        $this->edit_callback = $edit_callback;
    }

    public function get_edit_callback() {
        return $this->edit_callback;
    }

    public function set_column($column) {
        $this->column = $column;
    }

    public function get_column() {
        return $this->column;
    }

    public function set_translation($translation) {
        $this->translation = $translation;
    }

    public function get_translation() {
        return $this->translation;
    }

    public function set_add_callback($add_callback) {
        $this->add_callback = $add_callback;
    }

    public function get_add_callback() {
        return $this->add_callback;
    }

    public function set_taxonomies($taxonomies) {
        $this->taxonomies = $taxonomies;
    }

    public function get_taxonomies() {
        return $this->taxonomies;
    }

    public function add_taxonomy($taxonomy) {
        $this->taxonomies[] = $taxonomy;
        $this->set_taxonomies(hocwp_sanitize_array($this->get_taxonomies()));
    }

    public function set_show($show) {
        $this->show = $show;
    }

    public function get_show() {
        return $this->show;
    }

    public function set_callback_args($callback_args) {
        $this->callback_args = $callback_args;
    }

    public function get_callback_args() {
        return $this->callback_args;
    }

    public function set_priority($priority) {
        $this->priority = $priority;
    }

    public function get_priority() {
        return $this->priority;
    }

    public function set_context($context) {
        $this->context = $context;
    }

    public function get_context() {
        return $this->context;
    }

    public function set_callback($callback) {
        $this->callback = $callback;
    }

    public function get_callback() {
        return $this->callback;
    }

    public function set_id($id) {
        $this->id = $id;
    }

    public function get_id() {
        return $this->id;
    }

    public function set_post_types($post_types) {
        if(!is_array($post_types)) {
            $post_types = array($post_types);
        }
        $this->post_types = $post_types;
    }

    public function get_post_types() {
        return $this->post_types;
    }

    public function add_post_type($post_type) {
        $this->post_types[] = $post_type;
        $this->set_post_types(hocwp_sanitize_array($this->get_post_types()));
    }

    public function set_title($title) {
        $this->title = $title;
    }

    public function get_title() {
        return $this->title;
    }

    public function set_fields($fields) {
        $this->fields = $fields;
    }

    public function get_fields() {
        return $this->fields;
    }

    public function add_field($args) {
        $field_args = isset($args['field_args']) ? $args['field_args'] : $args;
        $this->sanitize_field_args($field_args);
        if(isset($args['options'])) {
            $field_args['options'] = $args['options'];
        }
        $args['field_args'] = $field_args;
        $this->fields[] = $args;
    }

    public function set_type($type) {
        $this->type = $type;
    }

    public function get_type() {
        return $this->type;
    }

    public function __construct($type) {
        $this->set_type($type);
        $this->set_post_types(array());
        $this->set_taxonomies(array());
        $this->set_fields(array());
        $this->set_title(__('Extra information', 'hocwp'));
        $this->set_id('hocwp_custom_meta');
        $this->set_context('normal');
        $this->set_priority('high');
    }

    public function init() {
        if($this->is_term_meta()) {
            $this->term_meta_init();
        } else {
            $this->post_meta_box_init();
        }
    }

    public function term_meta_init() {
        global $pagenow;
        if('edit-tags.php' == $pagenow) {
            add_filter('hocwp_wp_enqueue_media', '__return_true');
            add_filter('hocwp_use_admin_style_and_script', '__return_true');
        }
        foreach($this->get_taxonomies() as $taxonomy) {
            add_action($taxonomy . '_add_form_fields', array($this, 'term_field_add_page'));
            add_action($taxonomy . '_edit_form_fields', array($this, 'term_field_edit_page'));
            add_action('edited_' . $taxonomy, array($this, 'save_term_data'));
            add_action('created_' . $taxonomy, array($this, 'save_term_data'));
        }
    }

    public function term_field_add_page($taxonomy) {
        if(hocwp_callback_exists($this->get_add_callback())) {
            call_user_func($this->get_add_callback(), $taxonomy);
        } else {
            foreach($this->get_fields() as $field) {
                $on_add_page = isset($field['on_add_page']) ? $field['on_add_page'] : false;
                if($on_add_page) {
                    $callback = isset($field['field_callback']) ? $field['field_callback'] : 'hocwp_field_input';
                    if(hocwp_callback_exists($callback)) {
                        $field_args = isset($field['field_args']) ? $field['field_args'] : array();
                        $id = isset($field_args['id']) ? $field_args['id'] : '';
                        $name = isset($field_args['name']) ? $field_args['name'] : '';
                        hocwp_transmit_id_and_name($id, $name);
                        $class = 'term-' . $name . '-wrap';
                        $class = hocwp_sanitize_file_name($class);
                        hocwp_add_string_with_space_before($class, 'form-field hocwp');
                        ?>
                        <div class="<?php echo $class; ?>">
                            <?php call_user_func($callback, $field_args); ?>
                        </div>
                        <?php
                    }
                }
            }
        }
    }

    public function term_field_edit_page($term) {
        if(hocwp_callback_exists($this->get_edit_callback())) {
            call_user_func($this->get_edit_callback(), $term);
        } else {
            $term_id = $term->term_id;
            foreach($this->get_fields() as $field) {
                $field_args = isset($field['field_args']) ? $field['field_args'] : array();
                $callback = isset($field['field_callback']) ? $field['field_callback'] : 'hocwp_field_input';
                if(!isset($field_args['value'])) {
                    $field_args['value'] = hocwp_term_get_meta($term_id, $field_args['name']);
                }
                $label = isset($field_args['label']) ? $field_args['label'] : '';
                unset($field_args['label']);
                $id = isset($field_args['id']) ? $field_args['id'] : '';
                $name = isset($field_args['name']) ? $field_args['name'] : '';
                hocwp_transmit_id_and_name($id, $name);
                $class = 'term-' . $name . '-wrap';
                $class = hocwp_sanitize_file_name($class);
                hocwp_add_string_with_space_before($class, 'form-field hocwp');
                ?>
                <tr class="<?php echo $class; ?>">
                    <th scope="row"><label for="<?php echo esc_attr(hocwp_sanitize_id($id)); ?>"><?php echo $label; ?></label></th>
                    <td>
                        <?php
                        if(hocwp_callback_exists($callback)) {
                            call_user_func($callback, $field_args);
                        } else {
                            _e('Please set a valid callback for this field', 'hocwp');
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
        }
    }

    public function save_term_data($term_id) {
        foreach($this->get_fields() as $field) {
            $type = isset($field['type']) ? $field['type'] : 'default';
            $name = isset($field['field_args']['name']) ? $field['field_args']['name'] : '';
            if(empty($name)) {
                continue;
            }
            $value = hocwp_sanitize_form_post($name, $type);
            hocwp_term_update_meta($term_id, $name, $value);
        }
        return $term_id;
    }

    public function sanitize_field_args(&$args) {
        $id = isset($args['id']) ? $args['id'] : '';
        $name = isset($args['name']) ? $args['name'] : '';
        hocwp_transmit_id_and_name($id, $name);
        $args['id'] = $id;
        $args['name'] = $name;
        if($this->is_term_meta()) {

        } else {
            $args['before'] = '<div class="meta-row">';
            $args['after'] = '</div>';
        }
        return $args;
    }

    public function is_term_meta() {
        if('term' == $this->get_type()) {
            return true;
        }
        return false;
    }

    public function post_meta_box_init() {
        global $pagenow;
        if('post-new.php' == $pagenow) {
            add_filter('hocwp_wp_enqueue_media', '__return_true');
            add_filter('hocwp_use_admin_style_and_script', '__return_true');
        }
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save_post'));
    }

    public function add_meta_box() {
        $post_type = hocwp_get_current_post_type();
        if(in_array($post_type, $this->get_post_types())) {
            add_meta_box($this->get_id(), $this->get_title(), array($this, 'post_meta_box_callback'), $post_type, $this->get_context(), $this->get_priority(), $this->get_callback_args());
        }
    }

    public function post_meta_box_callback() {
        $class = 'hocwp-meta-box';
        hocwp_add_string_with_space_before($class, $this->get_context());
        hocwp_add_string_with_space_before($class, $this->get_priority());
        foreach($this->get_post_types() as $post_type) {
            hocwp_add_string_with_space_before($class, 'post-type-' . $post_type);
        }
        ?>
        <div class="<?php echo $class; ?>">
            <?php
            if(hocwp_callback_exists($this->get_callback())) {
                call_user_func($this->get_callback());
            } else {
                global $post;
                $post_id = $post->ID;
                foreach($this->get_fields() as $field) {
                    $field_args = isset($field['field_args']) ? $field['field_args'] : array();
                    $callback = isset($field['field_callback']) ? $field['field_callback'] : 'hocwp_field_input';
                    if(!isset($field_args['value'])) {
                        $field_args['value'] = get_post_meta($post_id, $field_args['name'], true);
                    }
                    if(hocwp_callback_exists($callback)) {
                        call_user_func($callback, $field_args);
                    } else {
                        echo '<p>' . sprintf(__('The callback function %s does not exists!', 'hocwp'), '<strong>' . $callback . '</strong>') . '</p>';
                    }
                }
            }
            do_action('hocwp_post_meta_box_field', $this);
            $current_post_type = hocwp_get_current_post_type();
            if(!empty($current_post_type)) {
                do_action('hocwp_' . $current_post_type . '_meta_box_field');
            }
            do_action('hocwp_meta_box_' . $this->get_id() . '_field');
            ?>
        </div>
        <?php
    }

    public function save_post($post_id) {
        if(!hocwp_can_save_post($post_id)) {
            return $post_id;
        }
        foreach($this->get_fields() as $field) {
            $type = isset($field['type']) ? $field['type'] : 'default';
            $name = isset($field['field_args']['name']) ? $field['field_args']['name'] : '';
            if(empty($name)) {
                continue;
            }
            $value = hocwp_sanitize_form_post($name, $type);
            update_post_meta($post_id, $name, $value);
        }
        return $post_id;
    }
}