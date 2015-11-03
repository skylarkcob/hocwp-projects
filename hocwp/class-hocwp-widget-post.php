<?php
class HOCWP_Widget_Post extends WP_Widget {
    public $args = array();

    private function get_defaults() {
        $defaults = array(
            'admin_width' => 400,
            'bys' => array(
                'recent' => __('Recent posts', 'hocwp'),
                'random' => __('Random posts', 'hocwp'),
                'comment' => __('Most comment posts', 'hocwp'),
                'category' => __('Posts by category', 'hocwp'),
                'like' => __('Most likes posts', 'hocwp'),
                'view' => __('Most views posts', 'hocwp'),
                'favorite' => __('Most favorite posts', 'hocwp'),
                'rate' => __('Most rate posts', 'hocwp')
            ),
            'post_type' => array('post'),
            'by' => 'recent',
            'number' => 5,
            'category' => array(),
            'exerpt_length' => 75,
            'thumbnail_size' => array(64, 64),
            'title_length' => 50,
            'show_author' => 0,
            'show_date' => 0,
            'show_comment_count' => 0,
            'only_thumbnail' => 0,
            'hide_thumbnail' => 0,
            'post_full_widths' => array(
                'none' => __('None', 'hocwp'),
                'first' => __('First post', 'hocwp'),
                'last' => __('Last post', 'hocwp'),
                'first_last' => __('First post and last post', 'hocwp'),
                'odd' => __('Odd posts', 'hocwp'),
                'even' => __('Even posts', 'hocwp'),
                'all' => __('All posts', 'hocwp')
            ),
            'post_full_width' => 'none',
            'times' => array(
                'all' => __('All time', 'hocwp'),
                'daily' => __('Daily', 'hocwp'),
                'weekly' => __('Weekly', 'hocwp'),
                'monthly' => __('Monthly', 'hocwp'),
                'yearly' => __('Yearly', 'hocwp')
            ),
            'time' => 'all',
            'orders' => array(
                'desc' => __('DESC', 'hocwp'),
                'asc' => __('ASC', 'hocwp')
            ),
            'order' => 'desc',
            'orderbys' => array(
                'title' => __('Title', 'hocwp'),
                'date' => __('Post date', 'hocwp')
            ),
            'orderby' => 'date'
        );
        $args = apply_filters('hocwp_widget_post_args', array());
        $args = wp_parse_args($args, $defaults);
        return $args;
    }

    public function __construct() {
        $this->args = $this->get_defaults();
        parent::__construct('hocwp_widget_post', 'HocWP Post',
            array(
                'classname' => 'hocwp-widget-post',
                'description' => __('Your site’s most recent Posts and more.', 'hocwp'),
            ),
            array(
                'width' => $this->args['admin_width']
            )
        );
    }

    public function widget($args, $instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $title  = apply_filters('widget_title', $instance['title']);
        $post_type = isset($instance['post_type']) ? $instance['post_type'] : $this->args['post_type'];
        $number = isset($instance['number']) ? $instance['number'] : $this->args['number'];
        $by = isset($instance['by']) ? $instance['by'] : $this->args['by'];
        $category = isset($instance['category']) ? $instance['category'] : $this->args['category'];
        $widget_html = $args['before_widget'];
        if(!empty($title)) {
            $widget_html .= $args['before_title'] . $title . $args['after_title'];
        }
        $query_args = array(
            'posts_per_page' => $number
        );
        $widget_html .= $args['after_widget'];
        $sidebar = isset($args['id']) ? $args['id'] : 'default';
        $widget_html = apply_filters('hocwp_widget_html', $widget_html, $instance, $args, $this->option_name, $widget_number = $this->number, $sidebar_id = $sidebar);
        $widget_html = apply_filters($this->option_name . '_html', $widget_html, $instance, $args, $widget_number = $this->number, $sidebar_id = $sidebar);
        $widget_html = apply_filters($this->option_name . '_' . $sidebar . '_html', $widget_html, $instance, $args, $widget_number = $this->number);
        echo $widget_html;
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $post_type = isset($instance['post_type']) ? $instance['post_type'] : json_encode($this->args['post_type']);
        $post_type = hocwp_json_string_to_array($post_type);
        $number = isset($instance['number']) ? $instance['number'] : $this->args['number'];
        $by = isset($instance['by']) ? $instance['by'] : $this->args['by'];
        $category = isset($instance['category']) ? $instance['category'] : json_encode($this->args['category']);
        $category = hocwp_json_string_to_array($category);
        hocwp_field_widget_before();
        hocwp_widget_field_title($this->get_field_id('title'), $this->get_field_name('title'), $title);
        $lists = get_post_types(array('_builtin' => false, 'public' => true), 'objects');
        if(!array_key_exists('post', $lists)) {
            $lists[] = get_post_type_object('post');
        }
        $all_option = '';
        foreach($lists as $lvalue) {
            $selected = '';
            if(!hocwp_array_has_value($post_type)) {
                $post_type[] = array('value' => 'post');
            }
            foreach($post_type as $ptvalue) {
                $ptype = isset($ptvalue['value']) ? $ptvalue['value'] : '';
                if($lvalue->name == $ptype) {
                    $selected = $lvalue->name;
                    break;
                }
            }
            $all_option .= hocwp_field_get_option(array('value' => $lvalue->name, 'text' => $lvalue->labels->singular_name, 'selected' => $selected));
        }
        $args = array(
            'id' => $this->get_field_id('post_type'),
            'name' => $this->get_field_name('post_type'),
            'all_option' => $all_option,
            'value' => $post_type,
            'label' => __('Post type:', 'hocwp'),
            'placeholder' => __('Choose post types', 'hocwp'),
            'multiple' => true
        );
        hocwp_widget_field('hocwp_field_select_chosen', $args);
        $args = array(
            'id' => $this->get_field_id('number'),
            'name' => $this->get_field_name('number'),
            'value' => $number,
            'label' => __('Number posts:', 'hocwp')
        );
        hocwp_widget_field('hocwp_field_input_number', $args);
        $lists = $this->args['bys'];
        $all_option = '';
        foreach($lists as $lkey => $lvalue) {
            $all_option .= hocwp_field_get_option(array('value' => $lkey, 'text' => $lvalue, 'selected' => $by));
        }
        $args = array(
            'id' => $this->get_field_id('by'),
            'name' => $this->get_field_name('by'),
            'value' => $by,
            'all_option' => $all_option,
            'label' => __('Get by:', 'hocwp')
        );
        hocwp_widget_field('hocwp_field_select', $args);
        $all_option = '';
        $taxonomies = hocwp_get_hierarchical_taxonomies();
        foreach($taxonomies as $tkey => $tax) {
            $lists = hocwp_get_hierarchical_terms(array($tkey));
            if(hocwp_array_has_value($lists)) {
                $all_option .= '<optgroup label="' . $tax->labels->singular_name . '">';
                foreach($lists as $lvalue) {
                    $selected = '';
                    if(hocwp_array_has_value($category)) {
                        foreach($category as $cvalue) {
                            $term_id = isset($cvalue['value']) ? $cvalue['value'] : 0;
                            if($lvalue->term_id == $term_id) {
                                $selected = $lvalue->term_id;
                                break;
                            }
                        }
                    }
                    $all_option .= hocwp_field_get_option(array('value' => $lvalue->term_id, 'text' => $lvalue->name, 'selected' => $selected, 'attributes' => array('data-taxonomy' => $tkey)));
                }
                $all_option .= '</optgroup>';
            }
        }
        $args = array(
            'id' => $this->get_field_id('category'),
            'name' => $this->get_field_name('category'),
            'all_option' => $all_option,
            'value' => $category,
            'label' => __('Category:', 'hocwp'),
            'placeholder' => __('Choose terms', 'hocwp'),
            'multiple' => true
        );
        hocwp_widget_field('hocwp_field_select_chosen', $args);
        hocwp_field_widget_after();
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = isset($new_instance['title']) ? strip_tags($new_instance['title']) : '';
        $instance['post_type'] = isset($new_instance['post_type']) ? $new_instance['post_type'] : json_encode($this->args['post_type']);
        $instance['number'] = isset($new_instance['number']) ? $new_instance['number'] : $this->args['number'];
        $instance['by'] = isset($new_instance['by']) ? $new_instance['by'] : $this->args['by'];
        $instance['category'] = isset($new_instance['category']) ? $new_instance['category'] : json_encode($this->args['category']);
        return $instance;
    }
}