<?php
if(defined('HOCWP_STATISTICS_VERSION')) {
    return;
}

define('HOCWP_STATISTICS_VERSION', '1.0.2');

define('HOCWP_COUNTER_TABLE_STATISTICS', 'hw_statistics');

define('HOCWP_COUNTER_TABLE_ONLINE', 'hw_statistics_online');

define('HOCWP_COUNTER_PATH', HOCWP_CONTENT_PATH . '/counter');

if(!file_exists(HOCWP_COUNTER_PATH)) {
    mkdir(HOCWP_COUNTER_PATH);
}

define('HOCWP_COUNTER_ONLINE_LOG', HOCWP_COUNTER_PATH . '/online.log');

if(!file_exists(HOCWP_COUNTER_ONLINE_LOG)) {
    hocwp_create_file(HOCWP_COUNTER_ONLINE_LOG);
}

define('HOCWP_COUNTER_IP_TXT', HOCWP_COUNTER_PATH . '/ip.txt');

if(!file_exists(HOCWP_COUNTER_IP_TXT)) {
    hocwp_create_file(HOCWP_COUNTER_IP_TXT);
}

define('HOCWP_COUNTER_COUNT_TXT', HOCWP_COUNTER_PATH . '/count.txt');

if(!file_exists(HOCWP_COUNTER_COUNT_TXT)) {
    hocwp_create_file(HOCWP_COUNTER_COUNT_TXT);
}

$use_statistics = apply_filters('hocwp_use_statistics', false);

function hocwp_statistics_table_init() {
    global $wpdb;
    $charset_collate = '';
    if(!empty($wpdb->charset)) {
        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
    }
    if(!empty($wpdb->collate)) {
        $charset_collate .= " COLLATE $wpdb->collate";
    }
    $table_statistics = $wpdb->prefix . HOCWP_COUNTER_TABLE_STATISTICS;
    $table_online = $wpdb->prefix . HOCWP_COUNTER_TABLE_ONLINE;
    $sql = "CREATE TABLE $table_statistics (
        ID bigint(20) unsigned NOT NULL auto_increment,
        user_id bigint(20) unsigned NOT NULL default '0',
        visited datetime NOT NULL default '0000-00-00 00:00:00',
        visited_timestamp int,
        ip text,
        pc_ip text,
        browser text,
        location text,
        user_agent longtext,
        PRIMARY KEY  (ID),
        KEY user_id (user_id)
    ) $charset_collate;
    CREATE TABLE $table_online (
        ID bigint(20) unsigned NOT NULL auto_increment,
        user_id bigint(20) unsigned NOT NULL default '0',
        visited datetime NOT NULL default '0000-00-00 00:00:00',
        visited_timestamp int,
        ip text,
        pc_ip text,
        browser text,
        location text,
        user_agent longtext,
        PRIMARY KEY  (ID),
        KEY user_id (user_id)
    ) $charset_collate;\n";

    if(!function_exists('dbDelta')) {
        require(ABSPATH . 'wp-admin/includes/upgrade.php');
    }
    dbDelta($sql);
}
add_action('after_switch_theme', 'hocwp_statistics_table_init');

function hocwp_statistics_reset_all_data() {
    hocwp_empty_database_table(HOCWP_COUNTER_TABLE_STATISTICS);
    hocwp_empty_database_table(HOCWP_COUNTER_TABLE_ONLINE);
}

function hocwp_statistics_add_row($table, $user_id, $visited, $ip, $pc_ip, $browser, $location, $user_agent) {
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . $table,
        array(
            'user_id' => $user_id,
            'visited' => $visited,
            'visited_timestamp' => strtotime($visited),
            'ip' => $ip,
            'pc_ip' => $pc_ip,
            'browser' => $browser,
            'location' => maybe_serialize($location),
            'user_agent' => $user_agent
        ),
        array(
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
        )
    );
    return $wpdb->insert_id;
}

function hocwp_statistics_track() {
    $hocwp_statistics_browser = isset($_SESSION['hocwp_statistics_browser']) ? $_SESSION['hocwp_statistics_browser'] : '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $browser = hocwp_get_browser();
    $online = hocwp_statistics_online_real();
    if($hocwp_statistics_browser != $browser || 0 == $online) {
        $current_datetime = hocwp_get_current_datetime_mysql();
        $user_id = 0;
        if(is_user_logged_in()) {
            $user = wp_get_current_user();
            $user_id = $user->ID;
        }
        $ip = hocwp_get_user_isp_ip();
        $pc_ip = hocwp_get_pc_ip();
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $location = hocwp_get_current_visitor_location();
        $statistics_id = hocwp_statistics_add_row(HOCWP_COUNTER_TABLE_STATISTICS, $user_id, $current_datetime, $ip, $pc_ip, $browser, $location, $user_agent);
        $online_id = hocwp_statistics_add_row(HOCWP_COUNTER_TABLE_ONLINE, $user_id, $current_datetime, $ip, $pc_ip, $browser, $location, $user_agent);
        $_SESSION['hocwp_statistics_user_online'] = $online_id;
        $_SESSION['hocwp_statistics_browser'] = $browser;
    }
}
if($use_statistics && !is_admin()) add_action('init', 'hocwp_statistics_track');

function hocwp_statistics_refresh_online_expire() {
    global $wpdb;
    $current_online = isset($_SESSION['hocwp_statistics_user_online']) ? $_SESSION['hocwp_statistics_user_online'] : 0;
    $current_online = absint($current_online);
    if($current_online > 0) {
        $table = $wpdb->prefix . HOCWP_COUNTER_TABLE_ONLINE;
        $wpdb->update(
            $table,
            array(
                'visited_timestamp' => strtotime(hocwp_get_current_datetime_mysql())
            ),
            array(
                'ID' => $current_online
            ),
            array(
                '%d'
            ),
            array(
                '%d'
            )
        );
    }
}
if($use_statistics) add_action('wp_head', 'hocwp_statistics_refresh_online_expire');

function hocwp_statistics_get_online_refresh_minute() {
    $minutes = apply_filters('hocwp_statistics_online_refresh_minute', 15);
    $minutes = absint($minutes);
    return $minutes;
}

function hocwp_statistics_delete_online_expired() {
    global $wpdb;
    $minutes = hocwp_statistics_get_online_refresh_minute();
    $transient_name = 'hocwp_statistics_delete_online';
    if(false === get_transient($transient_name)) {
        $table = $wpdb->prefix . HOCWP_COUNTER_TABLE_ONLINE;
        $interval = '-' . $minutes . ' minutes';
        $compare = hocwp_get_datetime_ago($interval);
        $compare = strtotime($compare);
        $wpdb->query("DELETE FROM $table WHERE visited_timestamp < $compare");
        set_transient($transient_name, 1, $minutes * MINUTE_IN_SECONDS);
    }
}
if($use_statistics) add_action('wp_head', 'hocwp_statistics_delete_online_expired');

function hocwp_statistics_online_detail() {
    global $wpdb;
    $result = array();
    $table = $wpdb->prefix . HOCWP_COUNTER_TABLE_ONLINE;
    $rows = $wpdb->get_results("SELECT * FROM $table");
    $online = $wpdb->num_rows;
    $online = absint($online);
    $count_user = 0;
    $items = array();
    foreach($rows as $item) {
        $user_name = __('Guest', 'hocwp');
        $last_activity = hocwp_human_time_diff_to_now($item->visited_timestamp) . ' ' . __('ago', 'hocwp');
        if($item->user_id > 0) {
            $count_user++;
            $user = get_user_by('id', $item->user_id);
            if(is_object($user) && !is_wp_error($user)) {
                $user_name = '<a href="' . get_author_posts_url($user->ID) . '">' . $user->user_login . '</a>';
            }
        }
        $items[] = array(
            'user_name' => $user_name,
            'last_activity' => $last_activity,
            'location' => maybe_unserialize($item->location)
        );
    }
    $most_user_online = get_option('hocwp_most_user_online');
    if(!is_array($most_user_online)) {
        $most_user_online = array();
    }
    if($most_user_online < $online) {
        $most_user_online['count'] = $online;
        $most_user_online['timestamp'] = strtotime(hocwp_get_current_datetime_mysql());
        update_option('hocwp_most_user_online', $most_user_online);
    }
    $result['count'] = $online;
    $result['items'] = $items;
    $result['user_count'] = $count_user;
    $result['guest_count'] = absint($online - $count_user);
    $result['most_user_online'] = $most_user_online;
    return $result;
}

function hocwp_statistics_online_real() {
    $detail = hocwp_statistics_online_detail();
    return $detail['count'];
}

function hocwp_statistics_online() {
    $online = hocwp_statistics_online_real();
    if($online < 1) {
        $online = 1;
    }
    return $online;
}

function hocwp_statistics_today() {
    global $wpdb;
    $table = $wpdb->prefix . HOCWP_COUNTER_TABLE_STATISTICS;
    $compare = strtotime(hocwp_get_datetime_ago('-1 day'));
    $day_in_seconds = DAY_IN_SECONDS;
    $wpdb->get_results("SELECT ID FROM $table WHERE ($compare - visited_timestamp) < $day_in_seconds");
    return $wpdb->num_rows;
}

function hocwp_statistics_yesterday() {
    global $wpdb;
    $transient_name = 'hocwp_statistics_yesterday';
    if(false === ($result = get_transient($transient_name))) {
        $table = $wpdb->prefix . HOCWP_COUNTER_TABLE_STATISTICS;
        $current_datetime = hocwp_get_current_datetime_mysql();
        $compare = strtotime(hocwp_get_datetime_ago('-1 day'));
        $day_in_seconds = DAY_IN_SECONDS;
        $wpdb->get_results("SELECT ID FROM $table WHERE ($compare - visited_timestamp) > $day_in_seconds AND ($compare - visited_timestamp) < (2 * $day_in_seconds)");
        $result = $wpdb->num_rows;
        $interval = apply_filters('hocwp_statistics_yesterday_refresh_hour', 12);
        set_transient($transient_name, $result, $interval * HOUR_IN_SECONDS);
    }
    return $result;
}

function hocwp_statistics_this_week() {
    global $wpdb;
    $table = $wpdb->prefix . HOCWP_COUNTER_TABLE_STATISTICS;
    $compare = strtotime(hocwp_get_datetime_ago('-1 week'));
    $in_seconds = WEEK_IN_SECONDS;
    $wpdb->get_results("SELECT ID FROM $table WHERE ($compare - visited_timestamp) < $in_seconds");
    return $wpdb->num_rows;
}

function hocwp_statistics_last_week() {
    global $wpdb;
    $transient_name = 'hocwp_statistics_last_week';
    if(false === ($result = get_transient($transient_name))) {
        $table = $wpdb->prefix . HOCWP_COUNTER_TABLE_STATISTICS;
        $current_datetime = hocwp_get_current_datetime_mysql();
        $compare = strtotime(hocwp_get_datetime_ago('-1 week'));
        $in_seconds = WEEK_IN_SECONDS;
        $wpdb->get_results("SELECT ID FROM $table WHERE ($compare - visited_timestamp) > $in_seconds AND ($compare - visited_timestamp) < (2 * $in_seconds)");
        $result = $wpdb->num_rows;
        $interval = apply_filters('hocwp_statistics_last_week_refresh_day', 3);
        set_transient($transient_name, $result, $interval * WEEK_IN_SECONDS);
    }
    return $result;
}

function hocwp_statistics_this_month() {
    global $wpdb;
    $table = $wpdb->prefix . HOCWP_COUNTER_TABLE_STATISTICS;
    $compare = strtotime(hocwp_get_datetime_ago('-1 month'));
    $in_seconds = 4 * WEEK_IN_SECONDS;
    $wpdb->get_results("SELECT ID FROM $table WHERE ($compare - visited_timestamp) < $in_seconds");
    return $wpdb->num_rows;
}

function hocwp_statistics_last_month() {
    global $wpdb;
    $transient_name = 'hocwp_statistics_last_month';
    if(false === ($result = get_transient($transient_name))) {
        $table = $wpdb->prefix . HOCWP_COUNTER_TABLE_STATISTICS;
        $current_datetime = hocwp_get_current_datetime_mysql();
        $compare = strtotime(hocwp_get_datetime_ago('-1 month'));
        $in_seconds = 4 * WEEK_IN_SECONDS;
        $wpdb->get_results("SELECT ID FROM $table WHERE ($compare - visited_timestamp) > $in_seconds AND ($compare - visited_timestamp) < (2 * $in_seconds)");
        $result = $wpdb->num_rows;
        $interval = apply_filters('hocwp_statistics_last_month_refresh_week', 2);
        set_transient($transient_name, $result, $interval * WEEK_IN_SECONDS);
    }
    return $result;
}

function hocwp_statistics_this_year() {
    global $wpdb;
    $table = $wpdb->prefix . HOCWP_COUNTER_TABLE_STATISTICS;
    $compare = strtotime(hocwp_get_datetime_ago('-1 year'));
    $in_seconds = 4 * YEAR_IN_SECONDS;
    $wpdb->get_results("SELECT ID FROM $table WHERE ($compare - visited_timestamp) < $in_seconds");
    return $wpdb->num_rows;
}

function hocwp_statistics_last_year() {
    global $wpdb;
    $transient_name = 'hocwp_statistics_last_year';
    if(false === ($result = get_transient($transient_name))) {
        $table = $wpdb->prefix . HOCWP_COUNTER_TABLE_STATISTICS;
        $current_datetime = hocwp_get_current_datetime_mysql();
        $compare = strtotime(hocwp_get_datetime_ago('-1 year'));
        $in_seconds = YEAR_IN_SECONDS;
        $wpdb->get_results("SELECT ID FROM $table WHERE ($compare - visited_timestamp) > $in_seconds AND ($compare - visited_timestamp) < (2 * $in_seconds)");
        $result = $wpdb->num_rows;
        $interval = apply_filters('hocwp_statistics_last_year_refresh_month', 6);
        set_transient($transient_name, $result, $interval * WEEK_IN_SECONDS);
    }
    return $result;
}

function hocwp_statistics_total() {
    global $wpdb;
    $table = $wpdb->prefix . HOCWP_COUNTER_TABLE_STATISTICS;
    $wpdb->get_results("SELECT ID FROM $table");
    return $wpdb->num_rows;
}

function hocwp_statistics_avg() {
    global $wpdb;
    $table = $wpdb->prefix . HOCWP_COUNTER_TABLE_STATISTICS;
    $results = $wpdb->get_results("SELECT visited_timestamp FROM $table ORDER BY ID LIMIT 1");
    $total_days = 1;
    if($wpdb->num_rows > 0) {
        $timestamp = $results[0]->visited_timestamp;
        $diff = strtotime(hocwp_get_current_datetime_mysql()) - $timestamp;
        $total_days = ceil($diff/DAY_IN_SECONDS);
    }
    $wpdb->get_results("SELECT ID FROM $table");
    return ceil($wpdb->num_rows/$total_days);
}

function hocwp_post_statistics() {
    $post_statistics = hocwp_option_get_value('reading', 'post_statistics');
    $post_statistics = apply_filters('hocwp_post_statistics', $post_statistics);
    return (bool)$post_statistics;
}

function hocwp_statistics_track_post_views() {
    if(is_single() || is_page() || is_singular()) {
        $post_id = get_the_ID();
        $session_key = 'hocwp_post_' . $post_id . '_views';
        if(!isset($_SESSION[$session_key]) || $_SESSION[$session_key] != 1) {
            $views = get_post_meta($post_id, 'views', true);
            $views = absint($views);
            $views++;
            update_post_meta($post_id, 'views', $views);
            $_SESSION[$session_key] = 1;
        }
    }
}
if(hocwp_post_statistics() && !is_admin()) add_action('hocwp_before_doctype', 'hocwp_statistics_track_post_views');