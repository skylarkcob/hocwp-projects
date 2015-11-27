<?php
if(!function_exists('add_filter')) exit;
function hocwp_use_session() {
    $use_session = apply_filters('hocwp_use_session', false);
    return (bool)$use_session;
}

function hocwp_session_start() {
    $use_session = hocwp_use_session();
    if(!$use_session) {
        return;
    }
    $session_start = true;
    if(version_compare(PHP_VERSION, '5.4', '>=')) {
        if(session_status() == PHP_SESSION_NONE) {
            $session_start = false;
        }
    } else {
        if('' == session_id()) {
            $session_start = false;
        }
    }
    if(!$session_start) {
        do_action('hocwp_session_start_before');
        session_start();
    }
}

function hocwp_create_file($path, $content = '') {
    if($fh = fopen($path, 'w')) {
        fwrite($fh, $content, 1024);
        fclose($fh);
    }
}

function hocwp_get_pc_ip() {
    $result = '';
    if(function_exists('getHostByName')) {
        if(version_compare(PHP_VERSION, '5.3', '<') && function_exists('php_uname')) {
            $result = getHostByName(php_uname('n'));
        } elseif(function_exists('getHostName')) {
            $result = getHostByName(getHostName());
        }
    }
    return $result;
}

function hocwp_get_timezone_string() {
    $timezone_string = get_option('timezone_string');
    if(empty($timezone_string)) {
        $timezone_string = 'Asia/Ho_Chi_Minh';
    }
    return $timezone_string;
}

function hocwp_get_current_date($format = 'Y-m-d') {
    date_default_timezone_set(hocwp_get_timezone_string());
    $result = date($format);
    return $result;
}

function hocwp_get_current_datetime_mysql() {
    return hocwp_get_current_date('Y-m-d H:i:s');
}

function hocwp_is_ip($ip) {
    if(filter_var($ip, FILTER_VALIDATE_IP)) {
        return true;
    }
    return false;
}

function hocwp_get_ipinfo($ip) {
    if(!hocwp_is_ip($ip)) {
        return '';
    }
    $json = file_get_contents('http://ipinfo.io/' . $ip);
    $details = json_decode($json);
    $details = (array)$details;
    return $details;
}

function hocwp_get_user_isp_ip() {
    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = $_SERVER['REMOTE_ADDR'];
    if(hocwp_is_ip($client)) {
        $ip = $client;
    } elseif(hocwp_is_ip($forward)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }
    return $ip;
}

function hocwp_array_has_value($arr) {
    if(is_array($arr) && count($arr) > 0) {
        return true;
    }
    return false;
}

function hocwp_get_plugin_info($plugin_file) {
    return get_plugin_data($plugin_file);
}

function hocwp_get_plugin_name($plugin_file, $default = '') {
    $plugin = hocwp_get_plugin_info($plugin_file);
    return hocwp_get_value_by_key($plugin, 'Name', $default);
}

function hocwp_get_value_by_key($arr, $key, $default = '') {
    $value = $default;
    $tmp = $arr;
    if(!is_array($key)) {
        $value = isset($arr[$key]) ? $arr[$key] : $default;
        return $value;
    }
    foreach($key as $child_key) {
        $tmp = (array)$tmp;
        if(is_array($child_key)) {
            continue;
        }
        $tmp = isset($tmp[$child_key]) ? $tmp[$child_key] : '';
        if(empty($tmp) || !is_array($tmp)) {
            break;
        }
    }
    if(!empty($tmp)) {
        $value = $tmp;
    }
    return $value;
}

function hocwp_array_unique($arr) {
    if(is_array($arr)) {
        $arr = array_map('unserialize', array_unique(array_map('serialize', $arr)));
    }
    return $arr;
}

function hocwp_get_terms($taxonomy, $args = array()) {
    $args['hide_empty'] = 0;
    return get_terms($taxonomy, $args);
}

function hocwp_remove_select_tag_keep_content($content) {
    $content = strip_tags($content, '<optgroup><option>');
    return $content;
}

function hocwp_object_valid($object) {
    if(is_object($object) && !is_wp_error($object)) {
        return true;
    }
    return false;
}

function hocwp_id_number_valid($id) {
    if(is_numeric($id) && $id > 0) {
        return true;
    }
    return false;
}

function hocwp_generate_serial() {
    $serial = new HOCWP_Serial();
    return $serial->generate();
}

function hocwp_check_password($password) {
    return wp_check_password($password, HOCWP_HASHED_PASSWORD);
}

function hocwp_get_term_drop_down($args = array()) {
    $defaults = array(
        'hide_empty' => false,
        'hierarchical' => true,
        'orderby' => 'NAME',
        'show_count' => true,
        'echo' => false,
        'taxonomy' => 'category'
    );
    $args = wp_parse_args($args, $defaults);
    $select = wp_dropdown_categories($args);
    return $select;
}

function hocwp_is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

function hocwp_can_save_post($post_id) {
    global $pagenow;
    if(!HOCWP_DOING_AUTO_SAVE && current_user_can('edit_post', $post_id) && 'edit.php' != $pagenow) {
        return true;
    }
    return false;
}

function hocwp_get_first_char($string, $encoding = 'UTF-8') {
    $result = '';
    if(!empty($string)) {
        $result = mb_substr($string, 0, 1, $encoding);
    }
    return $result;
}

function hocwp_remove_first_char($string, $char) {
    $string = ltrim($string, $char);
    return $string;
}

function hocwp_get_last_char($string, $encoding = 'UTF-8') {
    $result = '';
    if(!empty($string)) {
        $result = mb_substr($string, -1, 1, $encoding);
    }
    return $result;
}

function hocwp_remove_last_char($string, $char) {
    $string = rtrim($string, $char);
    return $string;
}

function hocwp_remove_first_char_and_last_char($string, $char) {
    $string = trim($string, $char);
    return $string;
}

function hocwp_uppercase($string, $encoding = 'utf-8') {
    return mb_strtoupper($string, $encoding);
}

function hocwp_uppercase_first_char($string, $encoding = 'utf-8') {
    $first_char = hocwp_get_first_char($string, $encoding);
    $len = mb_strlen($string, $encoding);
    $then = mb_substr($string, 1, $len - 1, $encoding);
    $first_char = hocwp_uppercase($first_char, $encoding);
    return $first_char . $then;
}

function hocwp_uppercase_first_char_only($string, $encoding = 'utf-8') {
    $string = hocwp_lowercase($string, $encoding);
    $string = hocwp_uppercase_first_char($string, $encoding);
    return $string;
}

function hocwp_lowercase($string, $encoding = 'utf-8') {
    return mb_strtolower($string, $encoding);
}

function hocwp_can_redirect() {
    if(!HOCWP_DOING_CRON && !HOCWP_DOING_CRON) {
        return true;
    }
    return false;
}

function hocwp_carousel_bootstrap($args = array()) {
    $container_class = isset($args['container_class']) ? $args['container_class'] : 'slide';
    $id = isset($args['id']) ? $args['id'] : '';
    $callback = isset($args['callback']) ? $args['callback'] : '';
    $posts = isset($args['posts']) ? $args['posts'] : array();
    $posts_per_page = isset($args['posts_per_page']) ? $args['posts_per_page'] : get_option('posts_per_page');
    $count = isset($args['count']) ? $args['count'] : 0;
    if(0 == $count && $posts_per_page > 0) {
        $count = count($posts) / $posts_per_page;
    }
    $show_control = isset($args['show_control']) ? $args['show_control'] : false;
    $count = ceil(abs($count));
    hocwp_add_string_with_space_before($container_class, 'carousel');
    $auto_slide = isset($args['auto_slide']) ? (bool)$args['auto_slide'] : true;
    if(empty($id) || !hocwp_callback_exists($callback)) {
        return;
    }
    $data_interval = '6000';
    if(!$auto_slide) {
        $data_interval = 'false';
    }
    $indicator_with_control = isset($args['indicator_with_control']) ? $args['indicator_with_control'] : false;
    $indicator_html = '';
    if($count > 1) {
        $ol = new HOCWP_HTML('ol');
        $ol->set_class('carousel-indicators list-unstyled list-inline');
        $ol_items = '';
        for($i = 0; $i < $count; $i++) {
            $indicator_class = 'carousel-paginate';
            if(0 == $i) {
                hocwp_add_string_with_space_before($indicator_class, 'active');
            }
            $li = '<li data-slide-to="' . $i . '" data-target="#' . $id . '" class="' . $indicator_class . '"></li>';
            $ol_items .= $li;
        }
        $ol->set_text($ol_items);
        $indicator_html = $ol->build();
    }
    $ul = new HOCWP_HTML('ul');
    $ul->set_class('list-inline list-unstyled list-controls');
    $li_items = '';
    if($count > 1 || $show_control) {
        $control = new HOCWP_HTML('a');
        $control->set_class('left carousel-control');
        $control->set_href('#' . $id);
        $control->set_attribute('data-slide', 'prev');
        $control->set_attribute('role', 'button');
        $control->set_text('<span class="fa fa-chevron-left"></span><span class="sr-only">' . __('Previous', 'hocwp') . '</span>');
        $li_items .= '<li class="prev">' . $control->build() . '</li>';
    }
    if($indicator_with_control) {
        $li_items .= '<li class="indicators">' . $indicator_html . '</li>';
    }
    if($count > 1 || $show_control) {
        $control = new HOCWP_HTML('a');
        $control->set_class('right carousel-control');
        $control->set_href('#' . $id);
        $control->set_attribute('data-slide', 'next');
        $control->set_attribute('role', 'button');
        $control->set_text('<span class="fa fa-chevron-right"></span><span class="sr-only">' . __('Next', 'hocwp') . '</span>');
        $li_items .= '<li class="next">' . $control->build() . '</li>';
    }
    $ul->set_text($li_items);
    $controls = $ul->build();
    if(!$indicator_with_control) {
        $controls .= $indicator_html;
    }

    ?>
    <div data-ride="carousel" class="<?php echo $container_class; ?>" id="<?php echo $id; ?>" data-interval="<?php echo $data_interval; ?>">
        <div role="listbox" class="carousel-inner">
            <?php call_user_func($callback, $args); ?>
        </div>
        <?php echo $controls; ?>
    </div>
    <?php
}

function hocwp_get_copyright_text() {
    $text = '&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '. All rights reserved.';
    return apply_filters('hocwp_copyright_text', $text);
}

function hocwp_get_countries() {
    $countries = array(
        'AF' => array('name' => 'Afghanistan', 'nativetongue' => '‫افغانستان'),
        'AX' => array('name' => 'Åland Islands', 'nativetongue' => 'Åland'),
        'AL' => array('name' => 'Albania', 'nativetongue' => 'Shqipëri'),
        'DZ' => array('name' => 'Algeria', 'nativetongue' => '‫الجزائر'),
        'AS' => array('name' => 'American Samoa', 'nativetongue' => ''),
        'AD' => array('name' => 'Andorra', 'nativetongue' => ''),
        'AO' => array('name' => 'Angola', 'nativetongue' => ''),
        'AI' => array('name' => 'Anguilla', 'nativetongue' => ''),
        'AQ' => array('name' => 'Antarctica', 'nativetongue' => ''),
        'AG' => array('name' => 'Antigua and Barbuda', 'nativetongue' => ''),
        'AR' => array('name' => 'Argentina', 'nativetongue' => ''),
        'AM' => array('name' => 'Armenia', 'nativetongue' => 'Հայաստան'),
        'AW' => array('name' => 'Aruba', 'nativetongue' => ''),
        'AC' => array('name' => 'Ascension Island', 'nativetongue' => ''),
        'AU' => array('name' => 'Australia', 'nativetongue' => ''),
        'AT' => array('name' => 'Austria', 'nativetongue' => 'Österreich'),
        'AZ' => array('name' => 'Azerbaijan', 'nativetongue' => 'Azərbaycan'),
        'BS' => array('name' => 'Bahamas', 'nativetongue' => ''),
        'BH' => array('name' => 'Bahrain', 'nativetongue' => '‫البحرين'),
        'BD' => array('name' => 'Bangladesh', 'nativetongue' => 'বাংলাদেশ'),
        'BB' => array('name' => 'Barbados', 'nativetongue' => ''),
        'BY' => array('name' => 'Belarus', 'nativetongue' => 'Беларусь'),
        'BE' => array('name' => 'Belgium', 'nativetongue' => 'België'),
        'BZ' => array('name' => 'Belize', 'nativetongue' => ''),
        'BJ' => array('name' => 'Benin', 'nativetongue' => 'Bénin'),
        'BM' => array('name' => 'Bermuda', 'nativetongue' => ''),
        'BT' => array('name' => 'Bhutan', 'nativetongue' => 'འབྲུག'),
        'BO' => array('name' => 'Bolivia', 'nativetongue' => ''),
        'BA' => array('name' => 'Bosnia and Herzegovina', 'nativetongue' => 'Босна и Херцеговина'),
        'BW' => array('name' => 'Botswana', 'nativetongue' => ''),
        'BV' => array('name' => 'Bouvet Island', 'nativetongue' => ''),
        'BR' => array('name' => 'Brazil', 'nativetongue' => 'Brasil'),
        'IO' => array('name' => 'British Indian Ocean Territory','nativetongue' => ''),
        'VG' => array('name' => 'British Virgin Islands', 'nativetongue' => ''),
        'BN' => array('name' => 'Brunei', 'nativetongue' => ''),
        'BG' => array('name' => 'Bulgaria', 'nativetongue' => 'България'),
        'BF' => array('name' => 'Burkina Faso', 'nativetongue' => ''),
        'BI' => array('name' => 'Burundi', 'nativetongue' => 'Uburundi'),
        'KH' => array('name' => 'Cambodia', 'nativetongue' => 'កម្ពុជា'),
        'CM' => array('name' => 'Cameroon', 'nativetongue' => 'Cameroun'),
        'CA' => array('name' => 'Canada', 'nativetongue' => ''),
        'IC' => array('name' => 'Canary Islands', 'nativetongue' => 'islas Canarias'),
        'CV' => array('name' => 'Cape Verde', 'nativetongue' => 'Kabu Verdi'),
        'BQ' => array('name' => 'Caribbean Netherlands', 'nativetongue' => ''),
        'KY' => array('name' => 'Cayman Islands', 'nativetongue' => ''),
        'CF' => array('name' => 'Central African Republic','nativetongue' => 'République centrafricaine'),
        'EA' => array('name' => 'Ceuta and Melilla', 'nativetongue' => 'Ceuta y Melilla'),
        'TD' => array('name' => 'Chad', 'nativetongue' => 'Tchad'),
        'CL' => array('name' => 'Chile', 'nativetongue' => ''),
        'CN' => array('name' => 'China', 'nativetongue' => '中国'),
        'CX' => array('name' => 'Christmas Island', 'nativetongue' => ''),
        'CP' => array('name' => 'Clipperton Island', 'nativetongue' => ''),
        'CC' => array('name' => 'Cocos (Keeling) Islands', 'nativetongue' => 'Kepulauan Cocos (Keeling)'),
        'CO' => array('name' => 'Colombia', 'nativetongue' => ''),
        'KM' => array('name' => 'Comoros', 'nativetongue' => '‫جزر القمر'),
        'CD' => array('name' => 'Congo (DRC)', 'nativetongue' => 'Jamhuri ya Kidemokrasia ya Kongo'),
        'CG' => array('name' => 'Congo (Republic)', 'nativetongue' => 'Congo-Brazzaville'),
        'CK' => array('name' => 'Cook Islands', 'nativetongue' => ''),
        'CR' => array('name' => 'Costa Rica', 'nativetongue' => ''),
        'CI' => array('name' => 'Côte d’Ivoire', 'nativetongue' => ''),
        'HR' => array('name' => 'Croatia', 'nativetongue' => 'Hrvatska'),
        'CU' => array('name' => 'Cuba', 'nativetongue' => ''),
        'CW' => array('name' => 'Curaçao', 'nativetongue' => ''),
        'CY' => array('name' => 'Cyprus', 'nativetongue' => 'Κύπρος'),
        'CZ' => array('name' => 'Czech Republic', 'nativetongue' => 'Česká republika'),
        'DK' => array('name' => 'Denmark', 'nativetongue' => 'Danmark'),
        'DG' => array('name' => 'Diego Garcia', 'nativetongue' => ''),
        'DJ' => array('name' => 'Djibouti', 'nativetongue' => ''),
        'DM' => array('name' => 'Dominica', 'nativetongue' => ''),
        'DO' => array('name' => 'Dominican Republic', 'nativetongue' => 'República Dominicana'),
        'EC' => array('name' => 'Ecuador', 'nativetongue' => ''),
        'EG' => array('name' => 'Egypt', 'nativetongue' => '‫مصر'),
        'SV' => array('name' => 'El Salvador', 'nativetongue' => ''),
        'GQ' => array('name' => 'Equatorial Guinea','nativetongue' => 'Guinea Ecuatorial'),
        'ER' => array('name' => 'Eritrea', 'nativetongue' => ''),
        'EE' => array('name' => 'Estonia', 'nativetongue' => 'Eesti'),
        'ET' => array('name' => 'Ethiopia', 'nativetongue' => ''),
        'FK' => array('name' => 'Falkland Islands', 'nativetongue' => 'Islas Malvinas'),
        'FO' => array('name' => 'Faroe Islands', 'nativetongue' => 'Føroyar'),
        'FJ' => array('name' => 'Fiji', 'nativetongue' => ''),
        'FI' => array('name' => 'Finland', 'nativetongue' => 'Suomi'),
        'FR' => array('name' => 'France', 'nativetongue' => ''),
        'GF' => array('name' => 'French Guiana', 'nativetongue' => 'Guyane française'),
        'PF' => array('name' => 'French Polynesia', 'nativetongue' => 'Polynésie française'),
        'TF' => array('name' => 'French Southern Territories', 'nativetongue' => 'Terres australes françaises'),
        'GA' => array('name' => 'Gabon', 'nativetongue' => ''),
        'GM' => array('name' => 'Gambia', 'nativetongue' => ''),
        'GE' => array('name' => 'Georgia', 'nativetongue' => 'საქართველო'),
        'DE' => array('name' => 'Germany', 'nativetongue' => 'Deutschland'),
        'GH' => array('name' => 'Ghana', 'nativetongue' => 'Gaana'),
        'GI' => array('name' => 'Gibraltar', 'nativetongue' => ''),
        'GR' => array('name' => 'Greece', 'nativetongue' => 'Ελλάδα'),
        'GL' => array('name' => 'Greenland', 'nativetongue' => 'Kalaallit Nunaat'),
        'GD' => array('name' => 'Grenada', 'nativetongue' => ''),
        'GP' => array('name' => 'Guadeloupe', 'nativetongue' => ''),
        'GU' => array('name' => 'Guam', 'nativetongue' => ''),
        'GT' => array('name' => 'Guatemala', 'nativetongue' => ''),
        'GG' => array('name' => 'Guernsey', 'nativetongue' => ''),
        'GN' => array('name' => 'Guinea', 'nativetongue' => 'Guinée'),
        'GW' => array('name' => 'Guinea-Bissau', 'nativetongue' => 'Guiné Bissau'),
        'GY' => array('name' => 'Guyana', 'nativetongue' => ''),
        'HT' => array('name' => 'Haiti', 'nativetongue' => ''),
        'HM' => array('name' => 'Heard & McDonald Islands', 'nativetongue' => ''),
        'HN' => array('name' => 'Honduras', 'nativetongue' => ''),
        'HK' => array('name' => 'Hong Kong', 'nativetongue' => '香港'),
        'HU' => array('name' => 'Hungary', 'nativetongue' => 'Magyarország'),
        'IS' => array('name' => 'Iceland', 'nativetongue' => 'Ísland'),
        'IN' => array('name' => 'India', 'nativetongue' => 'भारत'),
        'ID' => array('name' => 'Indonesia', 'nativetongue' => ''),
        'IR' => array('name' => 'Iran', 'nativetongue' => '‫ایران'),
        'IQ' => array('name' => 'Iraq', 'nativetongue' => '‫العراق'),
        'IE' => array('name' => 'Ireland', 'nativetongue' => ''),
        'IM' => array('name' => 'Isle of Man', 'nativetongue' => ''),
        'IL' => array('name' => 'Israel', 'nativetongue' => '‫ישראל'),
        'IT' => array('name' => 'Italy', 'nativetongue' => 'Italia'),
        'JM' => array('name' => 'Jamaica', 'nativetongue' => ''),
        'JP' => array('name' => 'Japan', 'nativetongue' => '日本'),
        'JE' => array('name' => 'Jersey', 'nativetongue' => ''),
        'JO' => array('name' => 'Jordan', 'nativetongue' => '‫الأردن'),
        'KZ' => array('name' => 'Kazakhstan', 'nativetongue' => 'Казахстан'),
        'KE' => array('name' => 'Kenya', 'nativetongue' => ''),
        'KI' => array('name' => 'Kiribati', 'nativetongue' => ''),
        'XK' => array('name' => 'Kosovo', 'nativetongue' => 'Kosovë'),
        'KW' => array('name' => 'Kuwait', 'nativetongue' => '‫الكويت'),
        'KG' => array('name' => 'Kyrgyzstan', 'nativetongue' => 'Кыргызстан'),
        'LA' => array('name' => 'Laos', 'nativetongue' => 'ລາວ'),
        'LV' => array('name' => 'Latvia', 'nativetongue' => 'Latvija'),
        'LB' => array('name' => 'Lebanon', 'nativetongue' => '‫لبنان'),
        'LS' => array('name' => 'Lesotho', 'nativetongue' => ''),
        'LR' => array('name' => 'Liberia', 'nativetongue' => ''),
        'LY' => array('name' => 'Libya', 'nativetongue' => '‫ليبيا'),
        'LI' => array('name' => 'Liechtenstein', 'nativetongue' => ''),
        'LT' => array('name' => 'Lithuania', 'nativetongue' => 'Lietuva'),
        'LU' => array('name' => 'Luxembourg', 'nativetongue' => ''),
        'MO' => array('name' => 'Macau', 'nativetongue' => '澳門'),
        'MK' => array('name' => 'Macedonia (FYROM)','nativetongue' => 'Македонија'),
        'MG' => array('name' => 'Madagascar', 'nativetongue' => 'Madagasikara'),
        'MW' => array('name' => 'Malawi', 'nativetongue' => ''),
        'MY' => array('name' => 'Malaysia', 'nativetongue' => ''),
        'MV' => array('name' => 'Maldives', 'nativetongue' => ''),
        'ML' => array('name' => 'Mali', 'nativetongue' => ''),
        'MT' => array('name' => 'Malta', 'nativetongue' => ''),
        'MH' => array('name' => 'Marshall Islands', 'nativetongue' => ''),
        'MQ' => array('name' => 'Martinique', 'nativetongue' => ''),
        'MR' => array('name' => 'Mauritania', 'nativetongue' => '‫موريتانيا'),
        'MU' => array('name' => 'Mauritius', 'nativetongue' => 'Moris'),
        'YT' => array('name' => 'Mayotte', 'nativetongue' => ''),
        'MX' => array('name' => 'Mexico', 'nativetongue' => ''),
        'FM' => array('name' => 'Micronesia', 'nativetongue' => ''),
        'MD' => array('name' => 'Moldova', 'nativetongue' => 'Republica Moldova'),
        'MC' => array('name' => 'Monaco', 'nativetongue' => ''),
        'MN' => array('name' => 'Mongolia', 'nativetongue' => 'Монгол'),
        'ME' => array('name' => 'Montenegro', 'nativetongue' => 'Crna Gora'),
        'MS' => array('name' => 'Montserrat', 'nativetongue' => ''),
        'MA' => array('name' => 'Morocco', 'nativetongue' => '‫المغرب'),
        'MZ' => array('name' => 'Mozambique', 'nativetongue' => 'Moçambique'),
        'MM' => array('name' => 'Myanmar (Burma)', 'nativetongue' => 'မြန်မာ'),
        'NA' => array('name' => 'Namibia', 'nativetongue' => 'Namibië'),
        'NR' => array('name' => 'Nauru', 'nativetongue' => ''),
        'NP' => array('name' => 'Nepal', 'nativetongue' => 'नेपाल'),
        'NL' => array('name' => 'Netherlands', 'nativetongue' => 'Nederland'),
        'NC' => array('name' => 'New Caledonia', 'nativetongue' => 'Nouvelle-Calédonie'),
        'NZ' => array('name' => 'New Zealand', 'nativetongue' => ''),
        'NI' => array('name' => 'Nicaragua', 'nativetongue' => ''),
        'NE' => array('name' => 'Niger', 'nativetongue' => 'Nijar'),
        'NG' => array('name' => 'Nigeria', 'nativetongue' => ''),
        'NU' => array('name' => 'Niue', 'nativetongue' => ''),
        'NF' => array('name' => 'Norfolk Island', 'nativetongue' => ''),
        'MP' => array('name' => 'Northern Mariana Islands', 'nativetongue' => ''),
        'KP' => array('name' => 'North Korea', 'nativetongue' => '조선 민주주의 인민 공화국'),
        'NO' => array('name' => 'Norway', 'nativetongue' => 'Norge'),
        'OM' => array('name' => 'Oman', 'nativetongue' => '‫عُمان'),
        'PK' => array('name' => 'Pakistan', 'nativetongue' => '‫پاکستان'),
        'PW' => array('name' => 'Palau', 'nativetongue' => ''),
        'PS' => array('name' => 'Palestine', 'nativetongue' => '‫فلسطين'),
        'PA' => array('name' => 'Panama', 'nativetongue' => ''),
        'PG' => array('name' => 'Papua New Guinea', 'nativetongue' => ''),
        'PY' => array('name' => 'Paraguay', 'nativetongue' => ''),
        'PE' => array('name' => 'Peru', 'nativetongue' => 'Perú'),
        'PH' => array('name' => 'Philippines', 'nativetongue' => ''),
        'PN' => array('name' => 'Pitcairn Islands', 'nativetongue' => ''),
        'PL' => array('name' => 'Poland', 'nativetongue' => 'Polska'),
        'PT' => array('name' => 'Portugal', 'nativetongue' => ''),
        'PR' => array('name' => 'Puerto Rico', 'nativetongue' => ''),
        'QA' => array('name' => 'Qatar', 'nativetongue' => '‫قطر'),
        'RE' => array('name' => 'Réunion', 'nativetongue' => 'La Réunion'),
        'RO' => array('name' => 'Romania', 'nativetongue' => 'România'),
        'RU' => array('name' => 'Russia', 'nativetongue' => 'Россия'),
        'RW' => array('name' => 'Rwanda', 'nativetongue' => ''),
        'BL' => array('name' => 'Saint Barthélemy', 'nativetongue' => 'Saint-Barthélemy'),
        'SH' => array('name' => 'Saint Helena', 'nativetongue' => ''),
        'KN' => array('name' => 'Saint Kitts and Nevis', 'nativetongue' => ''),
        'LC' => array('name' => 'Saint Lucia', 'nativetongue' => ''),
        'MF' => array('name' => 'Saint Martin', 'nativetongue' => ''),
        'PM' => array('name' => 'Saint Pierre and Miquelon', 'nativetongue' => 'Saint-Pierre-et-Miquelon'),
        'WS' => array('name' => 'Samoa', 'nativetongue' => ''),
        'SM' => array('name' => 'San Marino', 'nativetongue' => ''),
        'ST' => array('name' => 'São Tomé and Príncipe', 'nativetongue' => 'São Tomé e Príncipe'),
        'SA' => array('name' => 'Saudi Arabia', 'nativetongue' => '‫المملكة العربية السعودية'),
        'SN' => array('name' => 'Senegal', 'nativetongue' => 'Sénégal'),
        'RS' => array('name' => 'Serbia', 'nativetongue' => 'Србија'),
        'SC' => array('name' => 'Seychelles', 'nativetongue' => ''),
        'SL' => array('name' => 'Sierra Leone', 'nativetongue' => ''),
        'SG' => array('name' => 'Singapore', 'nativetongue' => ''),
        'SX' => array('name' => 'Sint Maarten', 'nativetongue' => ''),
        'SK' => array('name' => 'Slovakia', 'nativetongue' => 'Slovensko'),
        'SI' => array('name' => 'Slovenia', 'nativetongue' => 'Slovenija'),
        'SB' => array('name' => 'Solomon Islands', 'nativetongue' => ''),
        'SO' => array('name' => 'Somalia', 'nativetongue' => 'Soomaaliya'),
        'ZA' => array('name' => 'South Africa', 'nativetongue' => ''),
        'GS' => array('name' => 'South Georgia & South Sandwich Islands', 'nativetongue' => ''),
        'KR' => array('name' => 'South Korea', 'nativetongue' => '대한민국'),
        'SS' => array('name' => 'South Sudan', 'nativetongue' => '‫جنوب السودان'),
        'ES' => array('name' => 'Spain', 'nativetongue' => 'España'),
        'LK' => array('name' => 'Sri Lanka', 'nativetongue' => 'ශ්‍රී ලංකාව'),
        'VC' => array('name' => 'St. Vincent & Grenadines', 'nativetongue' => ''),
        'SD' => array('name' => 'Sudan', 'nativetongue' => '‫السودان'),
        'SR' => array('name' => 'Suriname', 'nativetongue' => ''),
        'SJ' => array('name' => 'Svalbard and Jan Mayen', 'nativetongue' => 'Svalbard og Jan Mayen'),
        'SZ' => array('name' => 'Swaziland', 'nativetongue' => ''),
        'SE' => array('name' => 'Sweden', 'nativetongue' => 'Sverige'),
        'CH' => array('name' => 'Switzerland', 'nativetongue' => 'Schweiz'),
        'SY' => array('name' => 'Syria', 'nativetongue' => '‫سوريا'),
        'TW' => array('name' => 'Taiwan', 'nativetongue' => '台灣'),
        'TJ' => array('name' => 'Tajikistan', 'nativetongue' => ''),
        'TZ' => array('name' => 'Tanzania', 'nativetongue' => ''),
        'TH' => array('name' => 'Thailand', 'nativetongue' => 'ไทย'),
        'TL' => array('name' => 'Timor-Leste', 'nativetongue' => ''),
        'TG' => array('name' => 'Togo', 'nativetongue' => ''),
        'TK' => array('name' => 'Tokelau', 'nativetongue' => ''),
        'TO' => array('name' => 'Tonga', 'nativetongue' => ''),
        'TT' => array('name' => 'Trinidad and Tobago', 'nativetongue' => ''),
        'TA' => array('name' => 'Tristan da Cunha', 'nativetongue' => ''),
        'TN' => array('name' => 'Tunisia', 'nativetongue' => '‫تونس'),
        'TR' => array('name' => 'Turkey', 'nativetongue' => 'Türkiye'),
        'TM' => array('name' => 'Turkmenistan', 'nativetongue' => ''),
        'TC' => array('name' => 'Turks and Caicos Islands', 'nativetongue' => ''),
        'TV' => array('name' => 'Tuvalu', 'nativetongue' => ''),
        'UM' => array('name' => 'U.S. Outlying Islands', 'nativetongue' => ''),
        'VI' => array('name' => 'U.S. Virgin Islands', 'nativetongue' => ''),
        'UG' => array('name' => 'Uganda', 'nativetongue' => ''),
        'UA' => array('name' => 'Ukraine', 'nativetongue' => 'Україна'),
        'AE' => array('name' => 'United Arab Emirates', 'nativetongue' => '‫الإمارات العربية المتحدة'),
        'GB' => array('name' => 'United Kingdom', 'nativetongue' => ''),
        'US' => array('name' => 'United States', 'nativetongue' => ''),
        'UY' => array('name' => 'Uruguay', 'nativetongue' => ''),
        'UZ' => array('name' => 'Uzbekistan', 'nativetongue' => 'Oʻzbekiston'),
        'VU' => array('name' => 'Vanuatu', 'nativetongue' => ''),
        'VA' => array('name' => 'Vatican City', 'nativetongue' => 'Città del Vaticano'),
        'VE' => array('name' => 'Venezuela', 'nativetongue' => ''),
        'VN' => array('name' => 'Vietnam', 'nativetongue' => 'Việt Nam'),
        'WF' => array('name' => 'Wallis and Futuna', 'nativetongue' => ''),
        'EH' => array('name' => 'Western Sahara', 'nativetongue' => '‫الصحراء الغربية'),
        'YE' => array('name' => 'Yemen', 'nativetongue' => '‫اليمن'),
        'ZM' => array('name' => 'Zambia', 'nativetongue' => ''),
        'ZW' => array('name' => 'Zimbabwe', 'nativetongue' => '')
    );
    return $countries;
}

function hocwp_transmit_id_and_name(&$id, &$name) {
    if(empty($id) && !empty($name)) {
        $id = $name;
    }
    if(empty($name) && !empty($id)) {
        $name = $id;
    }
}

function hocwp_sanitize($data, $type) {
    switch($type) {
        case 'media':
            return hocwp_sanitize_media_value($data);
        case 'text':
            return sanitize_text_field(trim($data));
        case 'email':
            return sanitize_email(trim($data));
        case 'file_name':
            return hocwp_sanitize_file_name($data);
        case 'html_class':
            return sanitize_html_class($data);
        case 'key':
            return sanitize_key($data);
        case 'mime_type':
            return sanitize_mime_type($data);
        case 'sql_orderby':
            return sanitize_sql_orderby($data);
        case 'slug':
            return sanitize_title($data);
        case 'title_for_query':
            return sanitize_title_for_query($data);
        case 'html_id':
            return hocwp_sanitize_id($data);
        case 'array':
            return hocwp_sanitize_array($data);
        default:
            return $data;
    }
}

function hocwp_vietnamese_currency() {
    return apply_filters('hocwp_vietnamese_currency', '₫');
}

function hocwp_number_format($number) {
    if('vi' == hocwp_get_language()) {
        return hocwp_number_format_vietnamese($number);
    }
    return number_format($number, 0);
}

function hocwp_number_format_vietnamese_currency($number) {
    return hocwp_number_format_vietnamese($number) . hocwp_vietnamese_currency();
}

function hocwp_number_format_vietnamese($number) {
    $number = floatval($number);
    return number_format($number, 0, '.', ',');
}

function hocwp_to_array($needle, $filter_and_unique = true) {
    $result = $needle;
    if(!is_array($result)) {
        $result = (array)$result;
    }
    if($filter_and_unique) {
        $result = array_filter($result);
        $result = array_unique($result);
    }
    return $result;
}

function hocwp_string_to_array($delimiter, $text) {
    if(is_array($text)) {
        return $text;
    }
    if(empty($text)) {
        return array();
    }
    $result = explode($delimiter, $text);
    $result = array_filter($result);
    return $result;
}

function hocwp_paragraph_to_array($list_paragraph) {
    $list_paragraph = str_replace('</p>', '', $list_paragraph);
    $list_paragraph = explode('<p>', $list_paragraph);
    return array_filter($list_paragraph);
}

function hocwp_object_to_array($object) {
    return json_decode(json_encode($object), true);
}

function hocwp_std_object_to_array($object) {
    return hocwp_json_string_to_array(json_encode($object));
}

function hocwp_json_string_to_array($json_string) {
    if(!is_array($json_string)) {
        $json_string = stripslashes($json_string);
        $json_string = json_decode($json_string, true);
    }
    $json_string = hocwp_sanitize_array($json_string);
    return $json_string;
}

function hocwp_sanitize_form_post($key, $type) {
    switch($type) {
        case 'checkbox':
            return isset($_POST[$key]) ? 1 : 0;
        case 'datetime':
            return isset($_POST[$key]) ? hocwp_string_to_datetime($_POST[$key]) : '';
        case 'timestamp':
            return isset($_POST[$key]) ? strtotime(hocwp_string_to_datetime($_POST[$key])) : '';
        default:
            return isset($_POST[$key]) ? hocwp_sanitize($_POST[$key], $type) : '';
    }
}

function hocwp_sanitize_array($arr, $unique = true, $filter = true) {
    if(!is_array($arr)) {
        $arr = (array)$arr;
    }
    if($unique) {
        $arr = hocwp_array_unique($arr);
    }
    if($filter) {
        $arr = array_filter($arr);
    }
    return $arr;
}

function hocwp_sanitize_size($size) {
    $size = (array)$size;
    $width = intval(isset($size[0]) ? $size['0'] : 0);
    if(0 == $width && isset($size['width'])) {
        $width = $size['width'];
    }
    $height = intval(isset($size[1]) ? $size[1] : $width);
    if(0 != $width && (0 == $height || $height == $width) && isset($size['height'])) {
        $height = $size['height'];
    }
    return array($width, $height);
}

function hocwp_sanitize_callback($args) {
    $callback = isset($args['func']) ? $args['func'] : '';
    if(empty($callback)) {
        $callback = isset($args['callback']) ? $args['callback'] : '';
    }
    return $callback;
}

function hocwp_sanitize_callback_args($args) {
    $func = isset($args['func_args']) ? $args['func_args'] : '';
    if(empty($func)) {
        $func = isset($args['callback_args']) ? $args['callback_args'] : '';
    }
    return $func;
}

function hocwp_get_browser() {
    global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone, $is_winIE, $is_macIE;
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $browser = 'unknown';
    if($is_lynx) {
        $browser = 'lynx';
    } elseif($is_gecko) {
        $browser = 'gecko';
        if(false !== strpos($user_agent, 'firefox')) {
            $browser = 'firefox';
        }
    } elseif($is_opera) {
        $browser = 'opera';
    } elseif($is_NS4) {
        $browser = 'ns4';
    } elseif($is_safari) {
        $browser = 'safari';
    } elseif($is_chrome) {
        $browser = 'chrome';
        if(false !== strpos($user_agent, 'edge')) {
            $browser = 'edge';
        }
    } elseif($is_winIE) {
        $browser = 'win-ie';
    } elseif($is_macIE) {
        $browser = 'mac-ie';
    } elseif($is_IE) {
        $browser = 'ie';
    } elseif($is_iphone) {
        $browser = 'iphone';
    }
    return $browser;
}

function hocwp_get_datetime_ago($ago, $datetime = '') {
    if(empty($datetime)) {
        $datetime = hocwp_get_current_datetime_mysql();
    }
    return date('Y-m-d H:i:s', strtotime($ago, strtotime($datetime)));
}

function hocwp_get_current_url() {
    global $wp;
    $current_url = trailingslashit(home_url($wp->request));
    return $current_url;
}

function hocwp_get_current_visitor_location() {
    $result = array();
    $title = __('Unknown location', 'hocwp');
    $url = hocwp_get_current_url();
    if(is_home()) {
        $title = __('Viewing index', 'hocwp');
    } elseif(is_archive()) {
        $title = sprintf(__('Viewing %s', 'hocwp'), get_the_archive_title());
    } elseif(is_singular()) {
        $title = sprintf(__('Viewing %s', 'hocwp'), get_the_title());
    } elseif(is_search()) {
        $title = __('Viewing search result', 'hocwp');
    } elseif(is_404()) {
        $title = __('Viewing 404 page not found', 'hocwp');
    }
    $result['object'] = get_queried_object();
    $result['url'] = $url;
    $result['title'] = $title;
    return $result;
}

function hocwp_human_time_diff_to_now($from) {
    if(!is_int($from)) {
        $from = strtotime($from);
    }
    return human_time_diff($from, strtotime(hocwp_get_current_datetime_mysql()));
}

function hocwp_string_to_datetime($string, $format = '') {
    if(empty($format)) {
        $format = 'Y-m-d H:i:s';
    }
    $string = str_replace('/', '-', $string);
    $string = trim($string);
    return date($format, strtotime($string));
}

function hocwp_get_safe_characters($special_char = false) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if($special_char) {
        $characters .= '{}#,!_@^';
        $characters .= '():.|`$';
        $characters .= '[];?=+-*~%';
    }
    return $characters;
}

function hocwp_get_safe_captcha_characters() {
    $characters = hocwp_get_safe_characters();
    $excludes = array('b', 'd', 'e', 'i', 'j', 'l', 'o', 'w', 'B', 'D', 'E', 'I', 'J', 'L', 'O', 'W', '0', '1', '2', '8');
    $excludes = apply_filters('hocwp_exclude_captcha_characters', $excludes);
    $characters = str_replace($excludes, '', $characters);
    return $characters;
}

function hocwp_random_string($length = 10, $characters = '', $special_char = false) {
    if(empty($characters)) {
        $characters = hocwp_get_safe_characters($special_char);
    }
    $len = strlen($characters);
    $result = '';
    for($i = 0; $i < $length; $i++) {
        $random_char = $characters[rand(0, $len - 1)];
        $result .= $random_char;
    }
    return $result;
}

function hocwp_is_mobile_domain($domain) {
    $domain = hocwp_get_domain_name($domain);
    $chars = substr($domain, 0, 2);
    if('m.' == $chars) {
        return true;
    }
    return false;
}

function hocwp_is_mobile_domain_blog() {
    return hocwp_is_mobile_domain(get_bloginfo('url'));
}

function hocwp_get_force_mobile() {
    $mobile = isset($_GET['mobile']) ? $_GET['mobile'] : '';
    return $mobile;
}

function hocwp_is_force_mobile() {
    $mobile = hocwp_get_force_mobile();
    if('true' == $mobile || 1 == absint($mobile)) {
        return true;
    }
    return false;
}

function hocwp_is_force_mobile_session($session) {
    if(isset($_SESSION[$session]) && 'mobile' == $_SESSION[$session]) {
        return true;
    }
    return false;
}

function hocwp_is_force_mobile_cookie($cookie) {
    if(isset($_COOKIE[$cookie]) && 'mobile' == $_COOKIE[$cookie]) {
        return true;
    }
    return false;
}

function hocwp_get_domain_name($url) {
    $parse = parse_url($url);
    $result = isset($parse['host']) ? $parse['host'] : '';
    return $result;
}

function hocwp_get_domain_name_only($url) {
    $root = hocwp_get_root_domain_name($url);
    if(hocwp_is_ip($root)) {
        return $root;
    }
    $root = explode('.', $root);
    return array_shift($root);
}

function hocwp_get_root_domain_name($url) {
    $domain_name = hocwp_get_domain_name($url);
    if(hocwp_is_ip($domain_name)) {
        return $domain_name;
    }
    $data = explode('.', $domain_name);
    while(count($data) > 2) {
        array_shift($data);
    }
    $domain_name = implode('.', $data);
    $last = array_pop($data);
    if('localhost' == $last || strlen($last) > 6) {
        $domain_name = $last;
    }
    return $domain_name;
}

function hocwp_is_site_domain($domain) {
    $site_domain = hocwp_get_root_domain_name(home_url());
    $domain = hocwp_get_root_domain_name($domain);
    if($domain == $site_domain) {
        return true;
    }
    return false;
}

function hocwp_random_string_number($length = 6) {
    return hocwp_random_string($length, '0123456789');
}

function hocwp_url_valid($url) {
    if(!filter_var($url, FILTER_VALIDATE_URL) === false) {
        return true;
    }
    return false;
}

function hocwp_url_exists($url) {
    $file_headers = @get_headers($url);
    $result = true;
    if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
        $result = false;
    }
    return $result;
}

function hocwp_image_url_exists($image_url) {
    if(!@file_get_contents($image_url)) {
        return false;
    }
    return true;
}

function hocwp_empty_database_table($table) {
    global $wpdb;
    return $wpdb->query("TRUNCATE TABLE $table");
}

function hocwp_get_current_post_type() {
    global $post_type;
    $result = $post_type;
    if(empty($result)) {
        if(isset($_GET['post_type'])) {
            $result = $_GET['post_type'];
        } else {
            $action = isset($_GET['action']) ? $_GET['action'] : '';
            $post_id = isset($_GET['post']) ? $_GET['post'] : 0;
            if('edit' == $action && is_numeric($post_id) && $post_id > 0) {
                $post = get_post($post_id);
                $result = $post->post_type;
            }
        }
    }
    return $result;
}

function hocwp_register_sidebar($sidebar_id, $sidebar_name, $sidebar_description) {
    register_sidebar(array(
        'name' => $sidebar_name,
        'id' => $sidebar_id,
        'description' => $sidebar_description,
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ));
}

function hocwp_register_widget($class_name) {
    if(class_exists($class_name)) {
        register_widget($class_name);
    }
}

function hocwp_register_post_type_normal($args) {
    $defaults = array(
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions'),
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true
    );
    $args = wp_parse_args($args, $defaults);
    hocwp_register_post_type($args);
}

function hocwp_register_post_type($args = array()) {
    $name = isset($args['name']) ? $args['name'] : '';
    $singular_name = isset($args['singular_name']) ? $args['singular_name'] : '';
    $supports = isset($args['supports']) ? $args['supports'] : array();
    $hierarchical = isset($args['hierarchical']) ? $args['hierarchical'] : false;
    $public = isset($args['public']) ? $args['public'] : true;
    $show_ui = isset($args['show_ui']) ? $args['show_ui'] : true;
    $show_in_menu = isset($args['show_in_menu']) ? $args['show_in_menu'] : true;
    $show_in_nav_menus = isset($args['show_in_nav_menus']) ? $args['show_in_nav_menus'] : false;
    $show_in_admin_bar = isset($args['show_in_admin_bar']) ? $args['show_in_admin_bar'] : false;
    $menu_position = isset($args['menu_position']) ? $args['menu_position'] : 6;
    $can_export = isset($args['can_export']) ? $args['can_export'] : true;
    $has_archive = isset($args['has_archive']) ? $args['has_archive'] : true;
    $exclude_from_search = isset($args['exclude_from_search']) ? $args['exclude_from_search'] : false;
    $publicly_queryable = isset($args['publicly_queryable']) ? $args['publicly_queryable'] : true;
    $capability_type = isset($args['capability_type']) ? $args['capability_type'] : 'post';
    $taxonomies = isset($args['taxonomies']) ? $args['taxonomies'] : array();
    $menu_icon = isset($args['menu_icon']) ? $args['menu_icon'] : 'dashicons-admin-post';
    $slug = isset($args['slug']) ? $args['slug'] : '';
    $with_front = isset($args['with_front']) ? $args['with_front'] : true;
    $pages = isset($args['pages']) ? $args['pages'] : true;
    $feeds = isset($args['feeds']) ? $args['feeds'] : true;
    $query_var = isset($args['query_var']) ? $args['query_var'] : '';
    $capabilities = isset($args['capabilities']) ? $args['capabilities'] : array();

    if(empty($singular_name)) {
        $singular_name = $name;
    }
    if(empty($name) || !is_array($supports) || empty($slug) || post_type_exists($slug)) {
        return;
    }
    if(!in_array('title', $supports)) {
        array_push($supports, 'title');
    }
    $labels = array(
        'name' => $name,
        'singular_name' => $singular_name,
        'menu_name' => $name,
        'name_admin_bar' => isset($args['name_admin_bar']) ? $args['name_admin_bar'] : $singular_name,
        'all_items' => sprintf(__('All %s', 'hocwp'), $name),
        'add_new' => __('Add New', 'hocwp'),
        'add_new_item' => sprintf(__('Add New %s', 'hocwp'), $singular_name),
        'edit_item' => sprintf(__('Edit %s', 'hocwp'), $singular_name),
        'new_item' => sprintf(__('New %s', 'hocwp'), $singular_name),
        'view_item' => sprintf(__('View %s', 'hocwp'), $singular_name),
        'search_items' => sprintf(__('Search %s', 'hocwp'), $singular_name),
        'not_found' => __('Not found', 'hocwp'),
        'not_found_in_trash' => __('Not found in Trash', 'hocwp'),
        'parent_item_colon' => sprintf(__('Parent %s:', 'hocwp'), $singular_name),
        'parent_item' => sprintf(__('Parent %s', 'hocwp'), $singular_name),
        'update_item' => sprintf(__('Update %s', 'hocwp'), $singular_name)
    );
    $rewrite_slug = str_replace('_', '-', $slug);
    $rewrite_defaults = array(
        'slug' => $rewrite_slug,
        'with_front' => $with_front,
        'pages' => $pages,
        'feeds' => $feeds
    );
    $rewrite = isset($args['rewrite']) ? $args['rewrite'] : array();
    $rewrite = wp_parse_args($rewrite, $rewrite_defaults);
    $description = isset($args['description']) ? $args['description'] : '';
    $args = array(
        'labels' => $labels,
        'description' => $description,
        'supports' => $supports,
        'taxonomies' => $taxonomies,
        'hierarchical' => $hierarchical,
        'public' => $public,
        'show_ui' => $show_ui,
        'show_in_menu' => $show_in_menu,
        'show_in_nav_menus' => $show_in_nav_menus,
        'show_in_admin_bar' => $show_in_admin_bar,
        'menu_position' => $menu_position,
        'menu_icon' => $menu_icon,
        'can_export' => $can_export,
        'has_archive' => $has_archive,
        'exclude_from_search' => $exclude_from_search,
        'publicly_queryable' => $publicly_queryable,
        'query_var' => $query_var,
        'rewrite' => $rewrite,
        'capability_type' => $capability_type
    );
    if(count($capabilities) > 0) {
        $args['capabilities'] = $capabilities;
    }
    $post_type = isset($args['post_type']) ? $args['post_type'] : $slug;
    register_post_type($post_type, $args);
}

function hocwp_strtolower($str, $charset = 'UTF-8') {
    return mb_strtolower($str, $charset);
}

function hocwp_register_taxonomy($args = array()) {
    $name = isset($args['name']) ? $args['name'] : '';
    $singular_name = isset($args['singular_name']) ? $args['singular_name'] : '';
    $hierarchical = isset($args['hierarchical']) ? $args['hierarchical'] : true;
    $public = isset($args['public']) ? $args['public'] : true;
    $show_ui = isset($args['show_ui']) ? $args['show_ui'] : true;
    $show_admin_column = isset($args['show_admin_column']) ? $args['show_admin_column'] : true;
    $show_in_nav_menus = isset($args['show_in_nav_menus']) ? $args['show_in_nav_menus'] : true;
    $show_tagcloud = isset($args['show_tagcloud']) ? $args['show_tagcloud'] : (($hierarchical === true) ? false : true);
    $post_types = isset($args['post_types']) ? $args['post_types'] : array();
    $slug = isset($args['slug']) ? $args['slug'] : '';
    $private = isset($args['private']) ? $args['private'] : false;
    if(empty($singular_name)) {
        $singular_name = $name;
    }
    if(empty($name) || empty($slug) || taxonomy_exists($slug)) {
        return;
    }
    $labels = array(
        'name' => $name,
        'singular_name' => $singular_name,
        'menu_name' => $name,
        'all_items' => sprintf(__('All %s', 'hocwp'), $name),
        'edit_item' => sprintf(__('Edit %s', 'hocwp'), $singular_name),
        'view_item' => sprintf(__('View %s', 'hocwp'), $singular_name),
        'update_item' => sprintf(__('Update %s', 'hocwp'), $singular_name),
        'add_new_item' => sprintf(__('Add New %s', 'hocwp'), $singular_name),
        'new_item_name' => sprintf(__('New %s Name', 'hocwp'), $singular_name),
        'parent_item' => sprintf(__('Parent %s', 'hocwp'), $singular_name),
        'parent_item_colon' => sprintf(__('Parent %s:', 'hocwp'), $singular_name),
        'search_items' => sprintf(__('Search %s', 'hocwp'), $name),
        'popular_items' => sprintf(__('Popular %s', 'hocwp'), $name),
        'separate_items_with_commas' => sprintf(__('Separate %s with commas', 'hocwp'), hocwp_strtolower($name)),
        'add_or_remove_items' => sprintf(__('Add or remove %s', 'hocwp'), $name),
        'choose_from_most_used' => sprintf(__('Choose from the most used %s', 'hocwp'), $name),
        'not_found' => __('Not Found', 'hocwp'),
    );
    $rewrite = isset($args['rewrite']) ? $args['rewrite'] : array();
    $rewrite_slug = str_replace('_', '-', $slug);
    $rewrite['slug'] = $rewrite_slug;
    if($private) {
        $public = false;
        $rewrite = false;
    }
    $update_count_callback = isset($args['update_count_callback']) ? $args['update_count_callback'] : '_update_post_term_count';
    $capabilities = isset($args['capabilities']) ? $args['capabilities'] : array('manage_terms');
    $args = array(
        'labels' => $labels,
        'hierarchical' => $hierarchical,
        'public' => $public,
        'show_ui' => $show_ui,
        'show_admin_column' => $show_admin_column,
        'show_in_nav_menus' => $show_in_nav_menus,
        'show_tagcloud' => $show_tagcloud,
        'query_var' => true,
        'rewrite' => $rewrite,
        'update_count_callback' => $update_count_callback,
        'capabilities' => $capabilities
    );

    $taxonomy = isset($args['taxonomy']) ? $args['taxonomy'] : $slug;
    register_taxonomy($taxonomy, $post_types, $args);
}

function hocwp_register_post_type_private($args = array()) {
    global $hocwp_private_post_types;
    $args['public'] = false;
    $args['exclude_from_search'] = true;
    $args['show_in_nav_menus'] = false;
    $args['show_in_admin_bar'] = false;
    $args['menu_position'] = 107;
    $args['has_archive'] = false;
    $args['feeds'] = false;
    $slug = isset($args['slug']) ? $args['slug'] : '';
    if(!empty($slug)) {
        $hocwp_private_post_types = hocwp_sanitize_array($hocwp_private_post_types);
        $hocwp_private_post_types[] = $slug;
    }
    hocwp_register_post_type($args);
}

function hocwp_is_debugging() {
    return (defined('WP_DEBUG') && true === WP_DEBUG) ? true : false;
}

function hocwp_is_localhost() {
    $root_domain = hocwp_get_domain_name_only(get_bloginfo('url'));
    $result = false;
    if('localhost' == $root_domain || hocwp_is_ip($root_domain)) {
        $result = true;
    }
    return apply_filters('hocwp_is_localhost', $result);
}

function hocwp_string_contain($string, $needle) {
    if(false !== mb_strpos($string, $needle, null, 'UTF-8')) {
        return true;
    }
    return false;
}

function hocwp_build_css_rule($elements, $properties) {
    $elements = hocwp_sanitize_array($elements);
    $properties = hocwp_sanitize_array($properties);
    $before = '';
    foreach($elements as $element) {
        if(empty($element)) {
            continue;
        }
        $before .= $element . ',';
    }
    $before = trim($before, ',');
    $after = '';
    foreach($properties as $key => $property) {
        if(empty($key)) {
            continue;
        }
        $after .= $key . ':' . $property . ';';
    }
    $after = trim($after, ';');
    return $before . '{' . $after . '}';
}

function hocwp_shorten_hex_css($content) {
    $content = preg_replace('/(?<![\'"])#([0-9a-z])\\1([0-9a-z])\\2([0-9a-z])\\3(?![\'"])/i', '#$1$2$3', $content);
    return $content;
}

function hocwp_shorten_zero_css($content) {
    $before = '(?<=[:(, ])';
    $after = '(?=[ ,);}])';
    $units = '(em|ex|%|px|cm|mm|in|pt|pc|ch|rem|vh|vw|vmin|vmax|vm)';
    $content = preg_replace('/'.$before.'(-?0*(\.0+)?)(?<=0)'.$units.$after.'/', '\\1', $content);
    $content = preg_replace('/'.$before.'\.0+'.$after.'/', '0', $content);
    $content = preg_replace('/'.$before.'(-?[0-9]+)\.0+'.$units.'?'.$after.'/', '\\1\\2', $content);
    $content = preg_replace('/'.$before.'-?0+'.$after.'/', '0', $content);
    return $content;
}

function hocwp_strip_white_space_css($content) {
    $content = preg_replace('/^\s*/m', '', $content);
    $content = preg_replace('/\s*$/m', '', $content);
    $content = preg_replace('/\s+/', ' ', $content);
    $content = preg_replace('/\s*([\*$~^|]?+=|[{};,>~]|!important\b)\s*/', '$1', $content);
    $content = preg_replace('/([\[(:])\s+/', '$1', $content);
    $content = preg_replace('/\s+([\]\)])/', '$1', $content);
    $content = preg_replace('/\s+(:)(?![^\}]*\{)/', '$1', $content);
    $content = preg_replace('/\s*([+-])\s*(?=[^}]*{)/', '$1', $content);
    $content = preg_replace('/;}/', '}', $content);
    return trim($content);
}

function hocwp_minify_css($css_content) {
    $buffer = $css_content;
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    $buffer = str_replace(': ', ':', $buffer);
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    $buffer = hocwp_shorten_hex_css($buffer);
    $buffer = hocwp_shorten_zero_css($buffer);
    $buffer = hocwp_strip_white_space_css($buffer);
    return $buffer;
}

function hocwp_the_posts_navigation() {
    the_posts_pagination(array(
        'prev_text' => esc_html__('Previous page', 'hocwp'),
        'next_text' => esc_html__('Next page', 'hocwp'),
        'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__('Page', 'hocwp') . ' </span>'
    ));
}

function hocwp_wrap_class($classes = array()) {
    $classes = hocwp_sanitize_array($classes);
    $classes = apply_filters('hocwp_wrap_class', $classes);
    $classes[] = 'wrap';
    $classes[] = 'container';
    $classes[] = 'wrapper';
    $class = implode(' ', $classes);
    echo $class;
}

function hocwp_div_clear() {
    echo '<div class="clear"></div>';
}

function hocwp_change_image_source($img, $src) {
    $doc = new DOMDocument();
    $doc->loadHTML($img);
    $tags = $doc->getElementsByTagName('img');
    foreach($tags as $tag) {
        $tag->setAttribute('src', $src);
    }
    return $doc->saveHTML();
}

function hocwp_get_tag_source($tag_name, $html) {
    return hocwp_get_tag_attr($tag_name, 'src', $html);
}

function hocwp_get_tag_attr($tag_name, $attr, $html) {
    $doc = new DOMDocument();
    $doc->loadHTML($html);
    $tags = $doc->getElementsByTagName($tag_name);
    foreach($tags as $tag) {
        return $tag->getAttribute($attr);
    }
    return '';
}

function hocwp_get_first_image_source($content) {
    $doc = new DOMDocument();
    @$doc->loadHTML($content);
    $xpath = new DOMXPath($doc);
    $src = $xpath->evaluate('string(//img/@src)');
    return $src;
}

function hocwp_comments_template() {
    if(comments_open() || get_comments_number()) {
        $comment_system = hocwp_theme_get_option('comment_system', 'discussion');
        if('facebook' == $comment_system) {
            hocwp_facebook_comment();
        } else {
            if('default_and_facebook') {
                hocwp_facebook_comment();
            }
            comments_template();
        }
    }
}

function hocwp_wp_link_pages() {
    wp_link_pages(array(
        'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'hocwp') . '</span>',
        'after' => '</div>',
        'link_before' => '<span>',
        'link_after' => '</span>',
        'pagelink' => '<span class="screen-reader-text">' . esc_html__('Page', 'hocwp') . ' </span>%',
        'separator' => '<span class="screen-reader-text">, </span>',
    ));
}

function hocwp_comment_nav() {
    if(get_comment_pages_count() > 1 && get_option('page_comments')) :
        ?>
        <nav class="navigation comment-navigation" role="navigation">
            <h2 class="screen-reader-text"><?php echo apply_filters('hocwp_comment_navigation_text', __('Comment navigation', 'hocwp')); ?></h2>
            <div class="nav-links">
                <?php
                if($prev_link = get_previous_comments_link(apply_filters('hocwp_comment_navigation_prev_text', esc_html__('Older Comments', 'hocwp')))) {
                    printf('<div class="nav-previous">%s</div>', $prev_link);
                }
                if($next_link = get_next_comments_link(apply_filters('hocwp_comment_navigation_next_text', esc_html__('Newer Comments', 'hocwp')))) {
                    printf('<div class="nav-next">%s</div>', $next_link);
                }
                ?>
            </div><!-- .nav-links -->
        </nav><!-- .comment-navigation -->
        <?php
    endif;
}

function hocwp_convert_day_name_to_vietnamese($day_name) {
    $weekday = $day_name;
    switch($weekday) {
        case 'Monday':
            $weekday = 'Thứ hai';
            break;
        case 'Tuesday':
            $weekday = 'Thứ ba';
            break;
        case 'Wednesday':
            $weekday = 'Thứ tư';
            break;
        case 'Thursday':
            $weekday = 'Thứ năm';
            break;
        case 'Friday':
            $weekday = 'Thứ sáu';
            break;
        case 'Saturday':
            $weekday = 'Thứ bảy';
            break;
        case 'Sunday':
            $weekday = 'Chủ nhật';
            break;
    }
    return $weekday;
}

function hocwp_get_current_weekday($format = 'd/m/Y H:i:s', $args = array()) {
    $weekday = hocwp_get_current_date('l');
    $labels = isset($args['labels']) ? $args['labels'] : array();
    $separator = isset($args['separator'] ) ? $args['separator'] : ', ';
    $weekday = hocwp_convert_day_name_to_vietnamese($weekday);
    return $weekday . $separator . hocwp_get_current_date($format);
}

function hocwp_current_weekday($format = 'd/m/Y H:i:s', $args = array()) {
    echo hocwp_get_current_weekday($format, $args);
}

function hocwp_color_hex_to_rgb($color, $opacity = false) {
    $default = 'rgb(0,0,0)';
    if(empty($color)) {
        return $default;
    }
    if($color[0] == '#') {
        $color = substr($color, 1);
    }
    if(strlen($color) == 6) {
        $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
    } elseif(strlen($color) == 3) {
        $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
    } else {
        return $default;
    }
    $rgb = array_map('hexdec', $hex);
    if($opacity) {
        if(abs($opacity) > 1) {
            $opacity = 1.0;
        }
        $output = 'rgba(' . implode(',', $rgb) . ',' . $opacity . ')';
    } else {
        $output = 'rgb(' . implode(',', $rgb) . ')';
    }
    return $output;
}

function hocwp_get_social_share_url($args = array()) {
    $result = '';
    $title = get_the_title();
    $permalink = get_the_permalink();
    $url = $permalink;
    $social_name = '';
    $thumbnail = '';
    $excerpt = get_the_excerpt();
    $language = hocwp_get_language();
    $twitter_account = 'skylarkcob';
    extract($args, EXTR_OVERWRITE);
    $permalink = urlencode($permalink);
    if(empty($twitter_account)) {
        $twitter_account = hocwp_get_wpseo_social_value('twitter_site');
        $twitter_account = basename($twitter_account);
    }
    switch($social_name) {
        case 'email':
            $result = 'mailto:email@hocwp.net?subject=' . $title . '&amp;body=' . $permalink;
            break;
        case 'facebook':
            $url = 'https://www.facebook.com/sharer/sharer.php';
            $url = add_query_arg('u', $permalink, $url);
            if(!empty($title)) {
                $url = add_query_arg('t', $title, $url);
            }
            $result = $url;
            break;
        case 'googleplus':
            $url = 'http://plusone.google.com/_/+1/confirm';
            $url = add_query_arg('hl', $language, $url);
            $url = add_query_arg('url', $permalink, $url);
            $result = $url;
            break;
        case 'twitter':
            $url = 'http://twitter.com/share';
            $url = add_query_arg('url', $permalink, $url);
            if(!empty($title)) {
                $url = add_query_arg('text', $title, $url);
            }
            $url = add_query_arg('via', $twitter_account, $url);
            $result = $url;
            break;
        case 'pinterest':
            $url = 'http://www.pinterest.com/pin/create/button';
            if(!empty($thumbnail)) {
                $url = add_query_arg('media', $thumbnail, $url);
            }
            $url = add_query_arg('url', $permalink, $url);
            if(!empty($title)) {
                $url = add_query_arg('description', $title . ' ' . $permalink, $url);
            }
            $result = $url;
            break;
        case 'zingme':
            $url = 'http://link.apps.zing.vn/share';
            if(!empty($title)) {
                $url = add_query_arg('t', $title, $url);
            }
            $url = add_query_arg('u', $permalink, $url);
            if(!empty($excerpt)) {
                $url = add_query_arg('desc', $excerpt, $url);
            }
            $result = $url;
            break;
    }
    return $result;
}

function hocwp_remove_vietnamese($string) {
    $characters = array(
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd' => 'đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'D' => 'Đ',
        'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
        'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
    );
    foreach($characters as $key => $value) {
        $string = preg_replace("/($value)/i", $key, $string);
    }
    return $string;
}

function hocwp_sanitize_file_name($name) {
    $name = hocwp_remove_vietnamese($name);
    $name = strtolower($name);
    $name = str_replace('_', '-', $name);
    $name = str_replace(' ', '-', $name);
    $name = sanitize_file_name($name);
    return $name;
}

function hocwp_menu_page_exists($slug) {
    if(empty($GLOBALS['admin_page_hooks'][$slug])) {
        return false;
    }
    return true;
}

function hocwp_callback_exists($callback) {
    if(empty($callback) || (!is_array($callback) && !function_exists($callback)) || (is_array($callback) && count($callback) != 2) || (is_array($callback) && !method_exists($callback[0], $callback[1]))) {
        return false;
    }
    return true;
}

function hocwp_add_unique_string(&$string, $add, $tail = true) {
    if(empty($string)) {
        $string = $add;
    } elseif(!hocwp_string_contain($string, $add)) {
        if($tail) {
            $string .= $add;
        } else {
            $string = $add . $string;
        }
    }
    $string = trim($string);
    return $string;
}

function hocwp_add_string_with_space_before(&$string, $add) {
    $add = ' ' . $add;
    $string = trim(hocwp_add_unique_string($string, $add));
    return $string;
}

function hocwp_get_current_admin_page() {
    return isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
}

function hocwp_get_plugins() {
    if(!function_exists('get_plugins')) {
        require(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    return get_plugins();
}

function hocwp_get_my_plugins() {
    $result = array();
    $lists = hocwp_get_plugins();
    foreach($lists as $file => $plugin) {
        if(hocwp_is_my_plugin($plugin)) {
            $result[$file] = $plugin;
        }
    }
    return $result;
}

function hocwp_is_my_plugin($plugin_data) {
    $result = false;
    $author_uri = isset($plugin_data['AuthorURI']) ? $plugin_data['AuthorURI'] : '';
    if(hocwp_get_root_domain_name($author_uri) == hocwp_get_root_domain_name(HOCWP_HOMEPAGE)) {
        $result = true;
    }
    return $result;
}

function hocwp_is_my_theme($stylesheet = null, $theme_root = null) {
    $result = false;
    $theme = wp_get_theme($stylesheet, $theme_root);
    $theme_uri = $theme->get('ThemeURI');
    $text_domain = $theme->get('TextDomain');
    $author_uri = $theme->get('AuthorURI');
    if((hocwp_string_contain($theme_uri, 'hocwp') && hocwp_string_contain($author_uri, 'hocwp')) || (hocwp_string_contain($text_domain, 'hocwp') && hocwp_string_contain($theme_uri, 'hocwp')) || (hocwp_string_contain($text_domain, 'hocwp') && hocwp_string_contain($author_uri, 'hocwp'))) {
        $result = true;
    }
    return $result;
}

function hocwp_has_plugin() {
    $result = false;
    $plugins = hocwp_get_plugins();
    foreach($plugins as $plugin) {
        if(hocwp_is_my_plugin($plugin)) {
            $result = true;
            break;
        }
    }
    return $result;
}

function hocwp_has_plugin_activated() {
    $plugins = get_option('active_plugins');
    foreach($plugins as $base_name) {
        if(hocwp_string_contain($base_name, 'hocwp')) {
            return true;
        }
    }
    return false;
}

function hocwp_admin_notice($args = array()) {
    $class = isset($args['class']) ? $args['class'] : '';
    hocwp_add_string_with_space_before($class, 'updated notice');
    $error = isset($args['error']) ? (bool)$args['error'] : false;
    $type = isset($args['type']) ? $args['type'] : 'default';
    $bs_callout = 'bs-callout-' . $type;
    hocwp_add_string_with_space_before($class, $bs_callout);
    if($error) {
        hocwp_add_string_with_space_before($class, 'settings-error error');
    }
    $dismissible = isset($args['dismissible']) ? (bool)$args['dismissible'] : true;
    if($dismissible) {
        hocwp_add_string_with_space_before($class, 'is-dismissible');
    }
    $id = isset($args['id']) ? $args['id'] : '';
    $id = hocwp_sanitize_id($id);
    $text = isset($args['text']) ? $args['text'] : '';
    if(empty($text)) {
        return;
    }
    $title = isset($args['title']) ? $args['title'] : '';
    if(!empty($title)) {
        $text = '<strong>' . $title . ':</strong> ' . $text;
    }
    ?>
    <div class="<?php echo esc_attr($class); ?>" id="<?php echo esc_attr($id); ?>">
        <p><?php echo $text; ?></p>
    </div>
    <?php
}

function hocwp_sanitize_id($id) {
    if(is_array($id)) {
        $id = implode('@', $id);
    }
    $id = strtolower($id);
    $id = str_replace('][', '_', $id);
    $chars = array(
        '-',
        ' ',
        '[',
        ']',
        '@',
        '.'
    );
    $id = str_replace($chars, '_', $id);
    $id = trim($id, '_');
    return $id;
}

function hocwp_admin_notice_setting_saved() {
    hocwp_admin_notice(array('text' => '<strong>' . __('Settings saved.', 'hocwp') . '</strong>'));
}

function hocwp_sanitize_field_name($base_name, $arr = array()) {
    $name = '';
    if(!is_array($arr)) {
        if(hocwp_string_contain($arr, $base_name)) {
            return $arr;
        }
        $arr = (array)$arr;
    }
    foreach($arr as $part) {
        if(!is_array($part) && hocwp_string_contain($part, $base_name)) {
            return array_shift($arr);
        }
        $name .= '[' . $part . ']';
    }
    return $base_name . $name;
}

function hocwp_sanitize_field_args(&$args) {
    if(isset($args['sanitized'])) {
        return $args;
    }
    $field_class = isset($args['field_class']) ? $args['field_class'] : '';
    $class = isset($args['class']) ? $args['class'] : '';
    hocwp_add_string_with_space_before($field_class, $class);
    $widefat = isset($args['widefat']) ? (bool)$args['widefat'] : true;
    $id = isset($args['id']) ? $args['id'] : '';
    $label = isset($args['label']) ? $args['label'] : '';
    $name = isset($args['name']) ? $args['name'] : '';
    hocwp_transmit_id_and_name($id, $name);
    $value = isset($args['value']) ? $args['value'] : '';
    $description = isset($args['description']) ? $args['description'] : '';
    $args['class'] = $field_class;
    $args['field_class'] = $field_class;
    $args['id'] = $id;
    $args['label'] = $label;
    $args['name'] = $name;
    $args['value'] = $value;
    $args['description'] = $description;
    $args['widefat'] = $widefat;
    $args['sanitized'] = true;
    return $args;
}

function hocwp_sanitize_media_value($value) {
    $url = isset($value['url']) ? $value['url'] : '';
    $id = isset($value['id']) ? $value['id'] : '';
    $id = absint($id);
    if(0 < $id && hocwp_media_file_exists($id)) {
        $url = hocwp_get_media_image_url($id);
    }
    return array('id' => $id, 'url' => $url);
}

function hocwp_get_media_path($id) {
    return get_attached_file($id);
}

function hocwp_media_file_exists($id) {
    if(file_exists(hocwp_get_media_path($id))) {
        return true;
    }
    return false;
}

function hocwp_get_media_image_detail($id) {
    return wp_get_attachment_image_src($id, 'full');
}

function hocwp_get_media_image_url($id) {
    $detail = hocwp_get_media_image_detail($id);
    return isset($detail[0]) ? $detail[0] : '';
}

function hocwp_bool_to_int($value) {
    if($value) {
        return 1;
    }
    return 0;
}

function hocwp_int_to_bool($value) {
    $value = absint($value);
    if(0 < $value) {
        return true;
    }
    return false;
}

function hocwp_bool_to_string($value) {
    if($value) {
        return 'true';
    }
    return 'false';
}

function hocwp_string_to_bool($string) {
    $string = strtolower($string);
    if('true' == $string) {
        return true;
    }
    return false;
}

function hocwp_search_form($args = array()) {
    $echo = isset($args['echo']) ? (bool)$args['echo'] : true;
    $class = isset($args['class']) ? $args['class'] : '';
    hocwp_add_string_with_space_before($class, 'search-form');
    $placeholder = isset($args['placeholder']) ? $args['placeholder'] : _x('Search &hellip;', 'placeholder');
    $search_icon = isset($args['search_icon']) ? $args['search_icon'] : false;
    $submit_text = _x('Search', 'submit button');
    if($search_icon) {
        hocwp_add_string_with_space_before($class, 'use-icon-search');
        $submit_text = '&#xf002;';
    }
    $form = '<form role="search" method="get" class="' . $class . '" action="' . esc_url(home_url('/')) . '">
				<label>
					<span class="screen-reader-text">' . _x('Search for:', 'label') . '</span>
					<input type="search" class="search-field" placeholder="' . esc_attr($placeholder) . '" value="' . get_search_query() . '" name="s" title="' . esc_attr_x('Search for:', 'label') . '" />
				</label>
				<input type="submit" class="search-submit" value="'. esc_attr($submit_text) .'" />
			</form>';
    if($echo) {
        echo $form;
    }
    return $form;
}

function hocwp_get_sidebars() {
    return $GLOBALS['wp_registered_sidebars'];
}

function hocwp_sidebar_has_widget($sidebar, $widget) {
    $sidebar_name = $sidebar;
    $sidebars = hocwp_get_sidebars();
    $sidebar = isset($sidebars[$sidebar]) ? $sidebars[$sidebar] : '';
    if(!empty($sidebar)) {
        $widgets = hocwp_get_sidebar_widgets($sidebar_name);
        foreach($widgets as $widget_name) {
            if(hocwp_string_contain($widget_name, $widget)) {
                return true;
            }
        }
    }
    return false;
}

function hocwp_get_sidebar_widgets($sidebar) {
    $widgets = wp_get_sidebars_widgets();
    $widgets = isset($widgets[$sidebar]) ? $widgets[$sidebar] : null;
    return $widgets;
}

function hocwp_supported_languages() {
    $languages = array(
        'vi' => __('Vietnamese', 'hocwp'),
        'en' => __('English', 'hocwp')
    );
    return apply_filters('hocwp_supported_languages', $languages);
}

function hocwp_get_language() {
    $lang = hocwp_option_get_value('theme_setting', 'language');
    if(empty($lang)) {
        $lang = 'vi';
    }
    return apply_filters('hocwp_language', $lang);
}

function hocwp_register_core_style_and_script() {
    wp_register_style('hocwp-style', HOCWP_URL . '/css/hocwp' . HOCWP_CSS_SUFFIX);
    wp_register_script('hocwp', HOCWP_URL . '/js/hocwp' . HOCWP_JS_SUFFIX, array('jquery'), false, true);
}

function hocwp_default_script_localize_object() {
    $datepicker_icon = apply_filters('hocwp_datepicker_icon', HOCWP_URL . '/images/icon-datepicker-calendar.gif');
    $args = array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'datepicker_icon' => $datepicker_icon,
        'i18n' => array(
            'jquery_undefined_error' => __('HocWP\'s JavaScript requires jQuery', 'hocwp'),
            'jquery_version_error' => sprintf(__('HocWP\'s JavaScript requires jQuery version %s or higher', 'hocwp'), HOCWP_MINIMUM_JQUERY_VERSION),
            'insert_media_title' => __('Insert media', 'hocwp'),
            'insert_media_button_text' => __('Use this media', 'hocwp'),
            'insert_media_button_texts' => __('Use these medias', 'hocwp')
        )
    );
    return apply_filters('hocwp_default_script_object', $args);
}

function hocwp_enqueue_jquery_ui_style() {
    $version = HOCWP_JQUERY_LATEST_VERSION;
    $version = apply_filters('hocwp_jquery_ui_version', $version);
    $theme = apply_filters('hocwp_jquery_ui_theme', 'smoothness');
    wp_enqueue_style('jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $version . '/themes/' . $theme . '/jquery-ui.css');
}

function hocwp_enqueue_jquery_ui_datepicker() {
    wp_enqueue_script('jquery-ui-datepicker');
}

function hocwp_get_recaptcha_language() {
    $lang = apply_filters('hocwp_recaptcha_language', hocwp_get_language());
    return $lang;
}

function hocwp_enqueue_recaptcha() {
    $lang = hocwp_get_recaptcha_language();
    $url = 'https://www.google.com/recaptcha/api.js';
    $url = add_query_arg(array('hl' => $lang), $url);
    $multiple = apply_filters('hocwp_multiple_recaptcha', false);
    if($multiple) {
        $url = add_query_arg(array('onload' => 'CaptchaCallback', 'render' => 'explicit'), $url);
    }
    wp_enqueue_script('recaptcha', $url, array(), false, true);
}

function hocwp_recaptcha_response($secret_key) {
    $result = false;
    $response = @file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $_POST['g-recaptcha-response']);
    $response = json_decode($response, true);
    if(true === $response['success']) {
        $result = true;
    }
    return $result;
}

function hocwp_admin_enqueue_scripts() {
    global $pagenow;
    $current_page = hocwp_get_current_admin_page();
    $use = apply_filters('hocwp_use_jquery_ui', false);
    if($use || ('themes.php' == $pagenow && 'hocwp_theme_setting' == $current_page)) {
        wp_enqueue_script('jquery-ui-core');
    }
    $use = apply_filters('hocwp_use_jquery_ui_sortable', false);
    if($use) {
        wp_enqueue_script('jquery-ui-sortable');
    }
    $wp_enqueue_media = apply_filters('hocwp_wp_enqueue_media', false);
    if($wp_enqueue_media) {
        wp_enqueue_media();
    }
    hocwp_register_core_style_and_script();
    wp_register_style('hocwp-admin-style', get_template_directory_uri() . '/hocwp/css/hocwp-admin'. HOCWP_CSS_SUFFIX, array('hocwp-style'));
    wp_register_script('hocwp-admin', get_template_directory_uri() . '/hocwp/js/hocwp-admin' . HOCWP_JS_SUFFIX, array('jquery', 'hocwp'), false, true);
    wp_localize_script('hocwp', 'hocwp', hocwp_default_script_localize_object());
    $use = apply_filters('hocwp_use_admin_style_and_script', false);
    if($use) {
        wp_enqueue_style('hocwp-admin-style');
        wp_enqueue_script('hocwp-admin');
    }
}

function hocwp_get_admin_email() {
    return get_bloginfo('admin_email');
}

function hocwp_facebook_javascript_sdk($args = array()) {
    $language = isset($args['language']) ? $args['language'] : 'vi_VN';
    $language = apply_filters('hocwp_facebook_javascript_sdk_language', $language);
    $app_id = isset($args['app_id']) ? $args['app_id'] : '1425884427679175';
    $app_id = apply_filters('hocwp_facebook_javascript_sdk_app_id', $app_id);
    $version = isset($args['version']) ? $args['version'] : '2.4';
    $version = apply_filters('hocwp_facebook_javascript_sdk_version', $version);
    ?>
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/<?php echo $language; ?>/sdk.js#xfbml=1&version=v<?php echo $version; ?>&appId=<?php echo $app_id; ?>";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
    <?php
}

function hocwp_use_full_mce_toolbar() {
    return apply_filters('hocwp_use_full_mce_toolbar', true);
}

function hocwp_use_facebook_javascript_sdk() {
    $result = apply_filters('hocwp_use_facebook_javascript_sdk', false);
    return $result;
}

function hocwp_facebook_page_plugin($args = array()) {
    $href = isset($args['href']) ? $args['href'] : '';
    if(empty($href)) {
        $page_id = isset($args['page_id']) ? $args['page_id'] : 'hocwpnet';
        $href = 'https://www.facebook.com/' . $page_id;
    }
    if(empty($href)) {
        return;
    }
    $page_name = isset($args['page_name']) ? $args['page_name'] : '';
    $width = isset($args['width']) ? $args['width'] : 340;
    $height = isset($args['height']) ? $args['height'] : 500;
    $hide_cover = (bool)(isset($args['hide_cover']) ? $args['hide_cover'] : false);
    $hide_cover = hocwp_bool_to_string($hide_cover);
    $show_facepile = (bool)(isset($args['show_facepile']) ? $args['show_facepile'] : true);
    $show_facepile = hocwp_bool_to_string($show_facepile);
    $show_posts = (bool)(isset($args['show_posts']) ? $args['show_posts'] : false);
    $show_posts = hocwp_bool_to_string($show_posts);
    $hide_cta = (bool)(isset($args['hide_cta']) ? $args['hide_cta'] : false);
    $hide_cta = hocwp_bool_to_string($hide_cta);
    $small_header = (bool)(isset($args['small_header']) ? $args['small_header'] : false);
    $small_header = hocwp_bool_to_string($small_header);
    $adapt_container_width = (bool)(isset($args['adapt_container_width']) ? $args['adapt_container_width'] : true);
    $adapt_container_width = hocwp_bool_to_string($adapt_container_width);
    ?>
    <div class="fb-page" data-href="<?php echo $href; ?>" data-width="<?php echo $width; ?>" data-height="<?php echo $height; ?>" data-hide-cta="<?php echo $hide_cta; ?>" data-small-header="<?php echo $small_header; ?>" data-adapt-container-width="<?php echo $adapt_container_width; ?>" data-hide-cover="<?php echo $hide_cover; ?>" data-show-facepile="<?php echo $show_facepile; ?>" data-show-posts="<?php echo $show_posts; ?>">
        <div class="fb-xfbml-parse-ignore">
            <?php if(!empty($page_name)) : ?>
                <blockquote cite="<?php echo $href; ?>">
                    <a href="<?php echo $href; ?>"><?php echo $page_name; ?></a>
                </blockquote>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function hocwp_update_permalink_struct($struct) {
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure($struct);
    update_option('permalink_structure', $struct);
    flush_rewrite_rules();
}

function hocwp_flush_rewrite_rules_after_site_url_changed() {
    $old_url = get_option('hocwp_site_url');
    $defined_url = (defined('WP_SITEURL')) ? WP_SITEURL : get_option('siteurl');
    if(empty($old_url) || $old_url != $defined_url) {
        update_option('hocwp_site_url', $defined_url);
        flush_rewrite_rules();
    }
}

function hocwp_the_footer_logo() {
    $footer_logo = hocwp_get_footer_logo_url();
    if(!empty($footer_logo)) {
        $a = new HOCWP_HTML('a');
        $a->set_attribute('href', home_url('/'));
        $img = new HOCWP_HTML('img');
        $img->set_attribute('src', $footer_logo);
        $a->set_text($img->build());
        $a->output();
    }
}

function hocwp_remove_array_item_by_value($value, $array) {
    if(($key = array_search($value, $array)) !== false) {
        unset($array[$key]);
    }
    return $array;
}

function hocwp_find_valid_value_in_array($arr, $key) {
    $result = '';
    if(is_array($arr)) {
        if(isset($arr[$key])) {
            $result = $arr[$key];
        } else {
            $index = absint(count($arr)/2);
            if(isset($arr[$index])) {
                $result = $arr[$index];
            } else {
                $result = current($arr);
            }
        }
    }
    return $result;
}

function hocwp_get_last_part_in_url($url) {
    return substr(parse_url($url, PHP_URL_PATH), 1);
}

function hocwp_substr($str, $len, $more = '...', $charset = 'UTF-8') {
    $more = esc_html($more);
    $str = html_entity_decode($str, ENT_QUOTES, $charset);
    if(mb_strlen($str, $charset) > $len) {
        $arr = explode(' ', $str);
        $str = mb_substr($str, 0, $len, $charset);
        $arr_words = explode(' ', $str);
        $index = count($arr_words) - 1;
        $last = $arr[$index];
        unset($arr);
        if(strcasecmp($arr_words[$index], $last)) {
            unset($arr_words[$index]);
        }
        return implode(' ', $arr_words) . $more;
    }
    return $str;
}

function hocwp_icon_circle_ajax($post_id, $meta_key) {
    $div = new HOCWP_HTML('div');
    $div->set_attribute('style', 'text-align: center');
    $div->set_class('hocwp-switcher-ajax');
    $span = new HOCWP_HTML('span');
    $circle_class = 'icon-circle';
    $result = get_post_meta($post_id, $meta_key, true);
    if(1 == $result) {
        $circle_class .= ' icon-circle-success';
    }
    $span->set_attribute('data-id', $post_id);
    $span->set_attribute('data-value', $result);
    $span->set_attribute('data-key', $meta_key);
    $span->set_class($circle_class);
    $div->set_text($span->build());
    $div->output();
}

function hocwp_get_posts_per_page() {
    return get_option('posts_per_page');
}

function hocwp_delete_transient_with_condition($transient_name, $condition = '', $blog_id = '') {
    global $wpdb;
    if(!empty($blog_id)) {
        $wpdb->set_blog_id($blog_id);
    }
    $last_char = hocwp_get_last_char($transient_name);
    if('_' == $last_char) {
        $transient_name = hocwp_remove_last_char($transient_name, $last_char);
    }
    $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name like %s" . $condition, '_transient_' . $transient_name . '_%'));
    $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name like %s" . $condition, '_transient_timeout_' . $transient_name . '_%'));
}

function hocwp_delete_transient($transient_name, $blog_id = '') {
    hocwp_delete_transient_with_condition($transient_name, $blog_id);
}

function hocwp_delete_transient_license_valid($blog_id = '') {
    $transient_name = 'hocwp_check_license';
    hocwp_delete_transient($transient_name, $blog_id);
}