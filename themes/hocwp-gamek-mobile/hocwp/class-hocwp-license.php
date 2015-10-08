<?php
class HOCWP_License {
    private $hashed_password;
    private $key;
    private $key_map;
    private $customer_name;
    private $customer_email;
    private $customer_phone;
    private $customer_identity;
    private $customer_url;
    private $code;
    private $hashed_code;
    private $use_for;
    private $domain;
    private $password;
    private $option;
    private $type;
    private $option_name;
    private $valid;
    private $generation;
    private $generated;

    public function set_generated($generated) {
        $this->generated = $generated;
    }

    public function get_generated() {
        return $this->generated;
    }

    public function set_generation($generation) {
        $this->generation = $generation;
    }

    public function get_generation() {
        return $this->generation;
    }

    public function set_valid($valid) {
        $this->valid = $valid;
    }

    public function get_valid() {
        return $this->valid;
    }

    public function set_option_name($option_name) {
        $this->option_name = $option_name;
    }

    public function get_option_name() {
        return $this->option_name;
    }

    public function set_type($type) {
        $this->type = $type;
    }

    public function get_type() {
        return $this->type;
    }

    public function set_option(HOCWP_Option $option) {
        $this->option = $option;
    }

    public function get_option() {
        return $this->option;
    }

    public function set_password($password) {
        $this->password = $password;
    }

    public function get_password() {
        return $this->password;
    }

    public function set_domain($domain) {
        $domain = esc_url($domain);
        $domain = hocwp_get_root_domain_name($domain);
        $this->domain = $domain;
    }

    public function get_domain() {
        return $this->domain;
    }

    public function set_use_for($use_for) {
        $this->use_for = $use_for;
    }

    public function get_use_for() {
        return $this->use_for;
    }

    public function set_hashed_code($hashed_code) {
        $this->hashed_code = $hashed_code;
    }

    public function get_hashed_code() {
        return $this->hashed_code;
    }

    public function set_code($code) {
        $this->code =  $code;
    }

    public function get_code() {
        return $this->code;
    }

    public function set_customer_url($customer_url) {
        $customer_url = trailingslashit($customer_url);
        $this->customer_url = $customer_url;
    }

    public function get_customer_url() {
        return $this->customer_url;
    }

    public function set_customer_identity($customer_identity) {
        $this->customer_identity = $customer_identity;
    }

    public function get_customer_identity() {
        return $this->customer_identity;
    }

    public function set_customer_phone($customer_phone) {
        $this->customer_phone = $customer_phone;
    }

    public function get_customer_phone() {
        return $this->customer_phone;
    }

    public function set_customer_email($customer_email) {
        $this->customer_email = $customer_email;
    }

    public function get_customer_email() {
        return $this->customer_email;
    }

    public function set_customer_name($customer_name) {
        $this->customer_name = $customer_name;
    }

    public function get_customer_name() {
        return $this->customer_name;
    }

    public function set_hashed_password($hashed_password) {
        $this->hashed_password = $hashed_password;
    }

    public function get_hashed_password() {
        return $this->hashed_password;
    }

    public function set_key($key) {
        $this->key = $key;
    }

    public function get_key() {
        return $this->key;
    }

    public function set_key_map($key_map) {
        $this->key_map = $key_map;
    }

    public function get_key_map() {
        return $this->key_map;
    }

    public function __construct() {
        $this->set_hashed_password(HOCWP_HASHED_PASSWORD);
        $this->set_key('');
        $this->set_type('theme');
        $this->set_use_for(get_option('template'));
        $this->set_domain(home_url());
        $this->set_customer_url(home_url('/'));
        $this->set_customer_email(get_option('admin_email'));
        $option = hocwp_option_get_object_from_list('theme_license');
        $this->set_option($option);
        if(hocwp_object_valid($option)) {
            $this->set_option_name($option->get_option_name());
        }
    }

    public function build_key_map() {
        $pieces = array();
        $email = $this->get_customer_email();
        $phone = $this->get_customer_phone();
        $identity = $this->get_customer_identity();
        if(empty($email) && empty($phone) && empty($identity)) {
            return $pieces;
        }
        if(!empty($email)) {
            $pieces[] = 'email';
        }
        if(!empty($phone)) {
            $pieces[] = 'phone';
        }
        if(!empty($identity)) {
            $pieces[] = 'identity';
        }
        $pieces[] = 'code';
        $pieces[] = 'domain';
        $pieces[] = 'use_for';
        shuffle($pieces);
        $pieces[] = 'hashed_password';
        $pieces = hocwp_sanitize_array($pieces);
        $this->set_key_map($pieces);
        return $pieces;
    }

    public function get_map_key_value($key) {
        $value = '';
        switch($key) {
            case 'email':
                $value = $this->get_customer_email();
                break;
            case 'phone':
                $value = $this->get_customer_phone();
                break;
            case 'identity':
                $value = $this->get_customer_identity();
                break;
            case 'code':
                $value = $this->get_code();
                break;
            case 'domain':
                $value = $this->get_domain();
                break;
            case 'use_for':
                $value = $this->get_use_for();
                break;
            default:
                if(!$this->get_generation() || $this->compare_password()) {
                    $value = $this->get_hashed_password();
                }
                break;
        }
        return $value;
    }

    public function get_saved_license_data() {
        return get_option($this->get_option_name());
    }

    public function get_saved_generated_data() {
        $data = get_option('hocwp_license');
        $value = hocwp_get_value_by_key($data, array($this->get_type(), md5($this->get_use_for())));
        return $value;
    }

    public function create_key() {
        if($this->get_generation()) {
            $pieces = $this->build_key_map();
        } else {
            $pieces = $this->get_key_map();
            if(!hocwp_array_has_value($pieces)) {
                $data = $this->get_saved_generated_data();
                $pieces = hocwp_get_value_by_key($data, 'key_map');
            }
        }
        $result = '';
        if(count($pieces) >= 3) {
            foreach($pieces as $piece) {
                if(!empty($piece)) {
                    $value = $this->get_map_key_value($piece);
                    if(!empty($value)) {
                        $result .= $value . '_';
                    }
                }
            }
        }
        $result = trim($result, '_');
        $this->set_key($result);
        return $result;
    }

    public function compare_password() {
        return wp_check_password($this->get_password(), $this->get_hashed_password());
    }

    public function generate() {
        $result = array();
        if(!$this->compare_password()) {
            $result = new WP_Error('set_hocwp_password', __('Please set default HocWP password first!', 'hocwp'));
        } else {
            $this->set_generation(true);
            $this->create_key();
            $this->set_hashed_code(wp_hash_password($this->get_key()));
            $url = $this->get_customer_url();
            if(!empty($url)) {
                $url = add_query_arg(array(
                    'hashed' => $this->get_hashed_code(),
                    'key_map' => $this->get_key_map(),
                    'type' => $this->get_type(),
                    'use_for' => $this->get_use_for(),
                    'hocwp_password' => $this->get_password()
                ), $url);
                $result['url'] = $url;
            }
            $result['code'] = $this->get_code();
            $result['customer_email'] = $this->get_customer_email();
        }
        $this->set_generated($result);
        return $result;
    }

    public function check_valid() {
        $valid = false;
        $data = $this->get_saved_generated_data();
        $hashed_license = hocwp_get_value_by_key($data, 'hashed');
        if(!empty($hashed_license)) {
            $key_map = hocwp_get_value_by_key($data, 'key_map');
            $this->set_key_map($key_map);
            $license_info = $this->get_saved_license_data();
            $code = hocwp_get_value_by_key($license_info, 'license_code');
            $this->set_code($code);
            $email = hocwp_get_value_by_key($license_info, 'customer_email');
            $this->set_customer_email($email);
            $this->create_key();
            $key = $this->get_key();
            if(wp_check_password($key, $hashed_license)) {
                $valid = true;
            }
        }
        $this->set_valid($valid);
        return $valid;
    }
}