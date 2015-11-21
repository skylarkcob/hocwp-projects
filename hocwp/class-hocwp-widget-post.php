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
                'related' => __('Related posts', 'hocwp'),
                'like' => __('Most likes posts', 'hocwp'),
                'view' => __('Most views posts', 'hocwp'),
                'favorite' => __('Most favorite posts', 'hocwp'),
                'rate' => __('Most rate posts', 'hocwp')
            ),
            'post_type' => array(array('value' => 'post')),
            'by' => 'recent',
            'number' => 5,
            'category' => array(),
            'excerpt_length' => 75,
            'thumbnail_size' => array(64, 64),
            'title_length' => 50,
            'show_author' => 0,
            'show_date' => 0,
            'show_comment_count' => 0,
            'only_thumbnail' => 0,
            'hide_thumbnail' => 0,
            'full_width_posts' => array(
                'none' => __('None', 'hocwp'),
                'first' => __('First post', 'hocwp'),
                'last' => __('Last post', 'hocwp'),
                'first_last' => __('First post and last post', 'hocwp'),
                'odd' => __('Odd posts', 'hocwp'),
                'even' => __('Even posts', 'hocwp'),
                'all' => __('All posts', 'hocwp')
            ),
            'full_width_post' => 'none',
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

    private function get_post_type_from_instance($instance) {
        $post_type = isset($instance['post_type']) ? $instance['post_type'] : json_encode($this->args['post_type']);
        $post_type = hocwp_json_string_to_array($post_type);
        if(!hocwp_array_has_value($post_type)) {
            $post_type = array(
                array(
                    'value' => apply_filters('hocwp_widget_post_default_post_type', 'post')
                )
            );
        }
        return $post_type;
    }

    public function widget($args, $instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $title  = apply_filters('widget_title', $instance['title']);
        $post_type = $this->get_post_type_from_instance($instance);
        $post_types = array();
        foreach($post_type as $fvdata) {
            $ptvalue = isset($fvdata['value']) ? $fvdata['value'] : '';
            if(!empty($ptvalue)) {
                $post_types[] = $ptvalue;
            }
        }
        $number = isset($instance['number']) ? $instance['number'] : $this->args['number'];
        $by = isset($instance['by']) ? $instance['by'] : $this->args['by'];
        $category = isset($instance['category']) ? $instance['category'] : json_encode($this->args['category']);
        $category = hocwp_json_string_to_array($category);
        $excerpt_length = isset($instance['excerpt_length']) ? $instance['excerpt_length'] : $this->args['excerpt_length'];
        $thumbnail_size = isset($instance['thumbnail_size']) ? $instance['thumbnail_size'] : $this->args['thumbnail_size'];
        $thumbnail_size = hocwp_sanitize_size($thumbnail_size);
        $full_width_post = hocwp_get_value_by_key($instance, 'full_width_post', $this->args['full_width_post']);
        $sidebar = isset($args['id']) ? $args['id'] : 'default';
        $widget_html = $args['before_widget'];
        if(!empty($title)) {
            $widget_html .= $args['before_title'] . $title . $args['after_title'];
        }
        $widget_html .= '<div class="widget-content">';
        $query_args = array(
            'posts_per_page' => $number,
            'post_type' => $post_types
        );
        $w_query = new WP_Query();
        $get_by = false;
        switch($by) {
            case 'recent':
                $get_by = true;
                break;
            case 'random':
                $get_by = true;
                $query_args['orderby'] = 'rand';
                break;
            case 'comment':
                $get_by = true;
                $query_args['orderby'] = 'comment_count';
                break;
            case 'category':
                foreach($category as $cvalue) {
                    $term_id = isset($cvalue['value']) ? $cvalue['value'] : '';
                    $term_id = absint($term_id);
                    if($term_id > 0) {
                        $taxonomy = isset($cvalue['taxonomy']) ? $cvalue['taxonomy'] : '';
                        if(!empty($taxonomy)) {
                            $tax_item = array(
                                'taxonomy' => $taxonomy,
                                'field' => 'term_id',
                                'terms' => $term_id
                            );
                            $query_args = hocwp_query_sanitize_tax_query($tax_item, $query_args);
                            $get_by = true;
                        }
                    }
                }
                break;
            case 'related':
                $get_by = true;
                break;
            case 'like':
                $get_by = true;
                $query_args['meta_key'] = 'likes';
                $query_args['orderby'] = 'meta_value_num';
                break;
            case 'view':
                $get_by = true;
                $query_args['meta_key'] = 'views';
                $query_args['orderby'] = 'meta_value_num';
                break;
            case 'favorite':
                break;
            case 'rate':
                break;
        }
        if($get_by) {
            $query_args = apply_filters('hocwp_sidebar_' . $sidebar . '_widget_post_query_args', $query_args, $instance, $widget_args = $args);
            if('related' == $by) {
                $w_query = hocwp_query_related_post($query_args);
            } else {
                $w_query = hocwp_query($query_args);
            }
        }
        if($w_query->have_posts()) {
            $list_class = 'list-unstyled';
            foreach($post_types as $ptvalue) {
                hocwp_add_string_with_space_before($list_class, 'list-' . $ptvalue . 's');
            }
            $list_class = apply_filters('hocwp_widget_post_list_class', $list_class);
            $widget_html .= '<ul class="' . $list_class . '">';
            $loop_html = apply_filters('hocwp_sidebar_' . $sidebar . '_widget_post_loop_html', '', $w_query, $data = $instance);
            if(empty($loop_html)) {
                $count = 0;
                ob_start();
                while($w_query->have_posts()) {
                    $w_query->the_post();
                    $class = 'a-widget-post';
                    $full_width = false;
                    if('all' == $full_width_post) {
                        $full_width = true;
                    } elseif('first' == $full_width_post && 0 == $count) {
                        $full_width = true;
                    } elseif('last' == $full_width_post && $count == $w_query->post_count) {
                        $full_width = true;
                    } elseif('first_last' == $full_width_post && (0 == $count || $count == $w_query->post_count)) {
                        $full_width = true;
                    } elseif('odd' == $full_width_post && ($count % 2) != 0) {
                        $full_width = true;
                    } elseif('even' == $full_width_post && ($count % 2) == 0) {
                        $full_width = true;
                    }
                    if($full_width) {
                        hocwp_add_string_with_space_before($class, 'full-width');
                    }
                    ?>
                    <li <?php post_class($class); ?>>
                        <?php
                        hocwp_post_thumbnail(array('width' => $thumbnail_size[0], 'height' => $thumbnail_size[1]));
                        hocwp_post_title_link();
                        ?>
                    </li>
                    <?php
                    $count++;
                }
                wp_reset_postdata();
                $loop_html .= ob_get_clean();
            }
            $widget_html .= $loop_html;
            $widget_html .= '</ul>';
        } else {
            $widget_html .= '<p class="nothing-found">' . __('Nothing found!', 'hocwp') . '</p>';
        }
        $widget_html .= '</div>';
        $widget_html .= $args['after_widget'];
        $widget_html = apply_filters('hocwp_widget_html', $widget_html, $instance, $query = $w_query, $widget_args = $args, $option_name = $this->option_name, $widget_number = $this->number, $sidebar_id = $sidebar);
        $widget_html = apply_filters($this->option_name . '_html', $widget_html, $instance, $query = $w_query, $widget_args = $args, $widget_number = $this->number, $sidebar_id = $sidebar);
        $widget_html = apply_filters($this->option_name . '_' . $sidebar . '_html', $widget_html, $instance, $query = $w_query, $widget_args = $args, $widget_number = $this->number);
        echo $widget_html;
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $post_type = $this->get_post_type_from_instance($instance);
        $number = isset($instance['number']) ? $instance['number'] : $this->args['number'];
        $by = isset($instance['by']) ? $instance['by'] : $this->args['by'];
        $category = isset($instance['category']) ? $instance['category'] : json_encode($this->args['category']);
        $category = hocwp_json_string_to_array($category);
        $thumbnail_size = hocwp_get_value_by_key($instance, 'thumbnail_size', $this->args['thumbnail_size']);
        $full_width_post = hocwp_get_value_by_key($instance, 'full_width_post', $this->args['full_width_post']);

        hocwp_field_widget_before('hocwp-widget-post');

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
            'label' => __('Get by:', 'hocwp'),
            'class' => 'get-by'
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
            'multiple' => true,
            'class' => 'select-category'
        );
        if('category' != $by) {
            $args['hidden'] = true;
        }
        hocwp_widget_field('hocwp_field_select_chosen', $args);

        $args = array(
            'id_width' => $this->get_field_id('thumbnail_size_width'),
            'name_width' => $this->get_field_name('thumbnail_size_width'),
            'id_height' => $this->get_field_id('thumbnail_size_height'),
            'name_height' => $this->get_field_name('thumbnail_size_height'),
            'value' => $thumbnail_size,
            'label' => __('Thumbnail size:', 'hocwp')
        );
        hocwp_widget_field('hocwp_field_size', $args);

        $lists = $this->args['full_width_posts'];
        $all_option = '';
        foreach($lists as $lkey => $lvalue) {
            $all_option .= hocwp_field_get_option(array('value' => $lkey, 'text' => $lvalue, 'selected' => $full_width_post));
        }
        $args = array(
            'id' => $this->get_field_id('full_width_post'),
            'name' => $this->get_field_name('full_width_post'),
            'value' => $by,
            'all_option' => $all_option,
            'label' => __('Full width posts:', 'hocwp'),
            'class' => 'full-width-post'
        );
        hocwp_widget_field('hocwp_field_select', $args);

        hocwp_field_widget_after();
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = isset($new_instance['title']) ? strip_tags($new_instance['title']) : '';
        $instance['post_type'] = isset($new_instance['post_type']) ? $new_instance['post_type'] : json_encode($this->args['post_type']);
        $instance['number'] = isset($new_instance['number']) ? $new_instance['number'] : $this->args['number'];
        $instance['by'] = isset($new_instance['by']) ? $new_instance['by'] : $this->args['by'];
        $instance['category'] = isset($new_instance['category']) ? $new_instance['category'] : json_encode($this->args['category']);
        $instance['full_width_post'] = hocwp_get_value_by_key($new_instance, 'full_width_post', $this->args['full_width_post']);
        $width = hocwp_get_value_by_key($new_instance, 'thumbnail_size_width', $this->args['thumbnail_size'][0]);
        $height = hocwp_get_value_by_key($new_instance, 'thumbnail_size_height', $this->args['thumbnail_size'][1]);
        $instance['thumbnail_size'] = array($width, $height);
        return $instance;
    }
}