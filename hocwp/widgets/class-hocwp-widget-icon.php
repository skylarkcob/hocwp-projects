<?php
if(!function_exists('add_filter')) exit;
class HOCWP_Widget_Icon extends WP_Widget {
    public $args = array();

    private function get_defaults() {
        $defaults = array(
            'id' => 'hocwp_widget_icon',
            'name' => 'HOCWP Icon',
            'admin_width' => 400,
            'title_link' => 0
        );
        $defaults = apply_filters('hocwp_widget_icon_defaults', $defaults);
        $args = apply_filters('hocwp_widget_icon_args', array());
        $args = wp_parse_args($args, $defaults);
        return $args;
    }

    public function __construct() {
        $this->args = $this->get_defaults();
        parent::__construct($this->args['id'], $this->args['name'],
            array(
                'classname' => 'hocwp-icon-widget',
                'description' => __('Display widget with icon.', 'hocwp'),
            ),
            array(
                'width' => $this->args['admin_width']
            )
        );
    }

    public function widget($args, $instance) {
        $sidebar = hocwp_get_value_by_key($args, 'id', 'default');
        $title = hocwp_widget_title($args, $instance, false);
        $title_link = hocwp_get_value_by_key($instance, 'title_link', hocwp_get_value_by_key($this->args, 'title_link'));
        hocwp_widget_before($args, $instance, false);
        $icon = hocwp_get_value_by_key($instance, 'icon');
        $icon = hocwp_sanitize_media_value($icon);
        $icon_url = $icon['url'];
        $icon_hover = hocwp_get_value_by_key($instance, 'icon_hover');
        $icon_hover = hocwp_sanitize_media_value($icon_hover);
        $icon_hover_url = $icon_hover['url'];
        $link = hocwp_get_value_by_key($instance, 'link');
        $text = hocwp_get_value_by_key($instance, 'text');
        $widget_html = '';
        if(!empty($icon_url)) {
            $widget_html .= '<a href="' . $link . '"><img class="icon" src="' . $icon_url . '" alt="" data-hover="' . $icon_hover_url . '"></a>';
        }
        if((bool)$title_link) {
            $title = '<a href="' . $link . '">' . $title . '</a>';
            $title = apply_filters('hocwp_widget_icon_title_html', $title, $instance, $args, $sidebar);
        }
        $widget_html .= $title;
        $widget_html .= '<div class="text">' . apply_filters('the_content', $text) . '</div>';
        $widget_html = apply_filters($this->option_name . '_html', $widget_html, $instance, $widget_args = $args, $widget_number = $this->number, $sidebar_id = $sidebar);
        $widget_html = apply_filters($this->option_name . '_' . $sidebar . '_html', $widget_html, $instance, $widget_args = $args, $widget_number = $this->number);
        echo $widget_html;
        hocwp_widget_after($args, $instance);
    }

    public function form($instance) {
        $title = hocwp_get_value_by_key($instance, 'title');
        $icon = hocwp_get_value_by_key($instance, 'icon');
        $icon = hocwp_sanitize_media_value($icon);
        $icon_hover = hocwp_get_value_by_key($instance, 'icon_hover');
        $icon_hover = hocwp_sanitize_media_value($icon_hover);
        $link = hocwp_get_value_by_key($instance, 'link');
        $text = hocwp_get_value_by_key($instance, 'text');
        $title_link = hocwp_get_value_by_key($instance, 'title_link', hocwp_get_value_by_key($this->args, 'title_link'));
        hocwp_field_widget_before();
        hocwp_widget_field_title($this->get_field_id('title'), $this->get_field_name('title'), $title);

        $args = array(
            'id' => $this->get_field_id('icon'),
            'name' => $this->get_field_name('icon'),
            'value' => $icon['url'],
            'label' => __('Icon:', 'hocwp')
        );
        hocwp_widget_field('hocwp_field_media_upload', $args);

        $args = array(
            'id' => $this->get_field_id('icon_hover'),
            'name' => $this->get_field_name('icon_hover'),
            'value' => $icon_hover['url'],
            'label' => __('Icon hover:', 'hocwp')
        );
        hocwp_widget_field('hocwp_field_media_upload', $args);

        $args = array(
            'id' => $this->get_field_id('link'),
            'name' => $this->get_field_name('link'),
            'value' => $link,
            'label' => __('Link:', 'hocwp')
        );
        hocwp_widget_field('hocwp_field_input_text', $args);

        $args = array(
            'id' => $this->get_field_id('text'),
            'name' => $this->get_field_name('text'),
            'value' => $text,
            'label' => __('Text:', 'hocwp')
        );
        hocwp_widget_field('hocwp_field_textarea', $args);

        $args = array(
            'id' => $this->get_field_id('title_link'),
            'name' => $this->get_field_name('title_link'),
            'value' => $title_link,
            'label' => __('Display title as link?', 'hocwp')
        );
        hocwp_widget_field('hocwp_field_input_checkbox', $args);

        hocwp_field_widget_after();
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags(hocwp_get_value_by_key($new_instance, 'title'));
        $instance['icon'] = hocwp_get_value_by_key($new_instance, 'icon');
        $instance['icon_hover'] = hocwp_get_value_by_key($new_instance, 'icon_hover');
        $instance['link'] = esc_url(hocwp_get_value_by_key($new_instance, 'link'));
        $instance['title_link'] = hocwp_checkbox_post_data_value($new_instance, 'title_link');
        $instance['text'] = hocwp_get_value_by_key($new_instance, 'text');
        return $instance;
    }
}