<?php
if(!function_exists('add_filter')) exit;
if(defined('HOCWP_HTML_VERSION')) {
    return;
}
define('HOCWP_HTML_VERSION', '1.0.0');
class HOCWP_HTML {
    private $self_closers = array();
    public $name = null;
    public $attributes = array();
    public $break_line = true;
    public $close = true;
    public $only_text = false;

    public function set_close($close) {
        $this->close = $close;
    }

    public function get_close() {
        return $this->close;
    }

    public function __construct($name) {
        $this->set_name($name);
    }

    public function get_name() {
        return $this->name;
    }

    public function set_name($name) {
        $this->name = strtolower($name);
    }

    public function use_only_text() {
        $this->only_text = true;
    }

    public function get_self_closers() {
        $self_closers = array('input', 'img', 'hr', 'br', 'meta', 'link');
        $this->set_self_closers($self_closers);
        return $this->self_closers;
    }

    private function set_self_closers($self_closers) {
        $this->self_closers = $self_closers;
    }

    public function get_attribute($attribute_name) {
        if($this->is_attribute_exists($attribute_name)) {
            return $this->attributes[$attribute_name];
        }
        return null;
    }

    public function set_attribute($attribute_name, $value) {
        if(!empty($value) || is_numeric($value)) {
            $this->attributes[$attribute_name] = $value;
        }
    }

    public function set_class($class) {
        $this->set_attribute('class', $class);
    }

    public function set_href($href) {
        $this->set_attribute('href', $href);
    }

    public function set_html($value) {
        $this->set_attribute('text', $value);
    }

    public function set_text($value) {
        $this->set_html($value);
    }

    public function set_attribute_array($attributes) {
        if(is_array($attributes)) {
            $this->attributes = wp_parse_args($attributes, $this->attributes);
        }
    }

    public function remove_attribute($attribute_name) {
        if($this->is_attribute_exists($attribute_name)) {
            unset($this->attributes[$attribute_name]);
        }
    }

    public function text_exsits() {
        $text = $this->get_attribute('text');
        if(!empty($text)) {
            return true;
        }
        return false;
    }

    public function remove_all_attribute() {
        $this->attributes = array();
    }

    private function make_outlink_nofollow() {
        if('a' == $this->get_name()) {
            $href = $this->get_attribute('href');
            if(!empty($href)) {
                if(!hocwp_is_site_domain($href)) {
                    $this->set_attribute('rel', 'external nofollow');
                    $this->set_attribute('target', '_blank');
                }
            }
        }
    }

    private function check_html() {
        $this->make_outlink_nofollow();
    }

    public function build() {
        if($this->only_text) {
            return $this->get_attribute('text');
        }
        $this->check_html();
        $html_name = $this->get_name();
        $result = '<' . $html_name;
        foreach($this->attributes as $key => $value) {
            if($key != 'text') {
                $result .= sprintf(' %1$s="%2$s"', $key, trim(esc_attr($value)));
            }
        }
        $result .= '>';
        if(!in_array($html_name, $this->get_self_closers())) {
            $text = $this->get_attribute('text');
            $result .= $text;
        }
        if($this->get_close()) {
            $result .= sprintf('</%s>', $html_name);
        }
        return $result;
    }

    public function set_break_line($break_line) {
        $this->break_line = $break_line;
    }

    public function get_break_line() {
        return $this->break_line;
    }

    public function output() {
        $html = $this->build();
        if($this->get_break_line()) {
            $html .= PHP_EOL;
        }
        echo $html;
    }

    public function is_attribute_exists($attribute_name) {
        return array_key_exists($attribute_name, $this->attributes);
    }
}