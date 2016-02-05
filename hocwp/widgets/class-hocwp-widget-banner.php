<?php
if(!function_exists('add_filter')) exit;
class HOCWP_Widget_Banner extends WP_Widget {
    public $args = array();

    private function get_defaults() {
        $defaults = array(
            'admin_width' => 400
        );
        $defaults = apply_filters('hocwp_widget_banner_defaults', $defaults);
        $args = apply_filters('hocwp_widget_banner_args', array());
        $args = wp_parse_args($args, $defaults);
        return $args;
    }

    public function __construct() {
        $this->args = $this->get_defaults();
        parent::__construct('hocwp_widget_banner', 'HOCWP Banner',
            array(
                'classname' => 'hocwp-banner-widget',
                'description' => __('Display banner on sidebar.', 'hocwp'),
            ),
            array(
                'width' => $this->args['admin_width']
            )
        );
    }

    public function widget($args, $instance) {
        $title_text = isset($instance['title']) ? $instance['title'] : '';
        $first_char = hocwp_get_first_char($title_text);
        if('!' === $first_char) {
            $title_text = ltrim($title_text, '!');
        }
        $banner_image = isset($instance['banner_image']) ? $instance['banner_image'] : '';
        $banner_url = isset($instance['banner_url']) ? $instance['banner_url'] : '';
        $banner_image = hocwp_sanitize_media_value($banner_image);
        $banner_image = $banner_image['url'];
        if(!empty($banner_image)) {
            hocwp_widget_before($args, $instance);
            if(!empty($banner_url)) {
                echo '<a class="hocwp-banner-link" title="' . $title_text . '" href="' . $banner_url . '">';
            }
            echo '<img class="hocwp-banner-image" alt="' . $title_text.'" src="' . $banner_image . '">';
            if(!empty($banner_url)) {
                echo '</a>';
            }
            hocwp_widget_after($args, $instance);
        }
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $banner_image = isset($instance['banner_image']) ? $instance['banner_image'] : '';
        $banner_url = isset($instance['banner_url']) ? $instance['banner_url'] : '';
        hocwp_field_widget_before();
        hocwp_widget_field_title($this->get_field_id('title'), $this->get_field_name('title'), $title);

        $args = array(
            'id' => $this->get_field_id('banner_image'),
            'name' => $this->get_field_name('banner_image'),
            'value' => $banner_image,
            'label' => __('Image url:', 'hocwp')
        );
        hocwp_widget_field('hocwp_field_media_upload', $args);

        $args = array(
            'id' => $this->get_field_id('banner_url'),
            'name' => $this->get_field_name('banner_url'),
            'value' => $banner_url,
            'label' => __('Image link:', 'hocwp')
        );
        hocwp_widget_field('hocwp_field_input_text', $args);

        hocwp_field_widget_after();
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags(hocwp_get_value_by_key($new_instance, 'title'));
        $instance['banner_image'] = hocwp_get_value_by_key($new_instance, 'banner_image');
        $instance['banner_url'] = hocwp_get_value_by_key($new_instance, 'banner_url');
        return $instance;
    }
}