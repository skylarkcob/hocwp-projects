<?php
if(!function_exists('add_filter')) exit;
function hocwp_get_request() {
    $request = remove_query_arg('paged');
    $home_root = parse_url(home_url());
    $home_root =(isset($home_root['path'])) ? $home_root['path'] : '';
    $home_root = preg_quote($home_root, '|');
    $request = preg_replace('|^'. $home_root . '|i', '', $request);
    $request = preg_replace('|^/+|', '', $request);
    return $request;
}

function hocwp_get_pagenum_link($args = array()) {
    $pagenum = isset($args['pagenum']) ? $args['pagenum'] : 1;
    $escape = isset($args['escape']) ? $args['escape'] : true;
    $request = isset($args['request']) ? $args['request'] : hocwp_get_request();
    if (!is_admin()) {
        return get_pagenum_link($pagenum, $escape);
    } else {
        global $wp_rewrite;
        $pagenum = (int)$pagenum;
        if(!$wp_rewrite->using_permalinks()) {
            $base = trailingslashit(get_bloginfo('url'));
            if($pagenum > 1) {
                $result = add_query_arg('paged', $pagenum, $base . $request);
            } else {
                $result = $base . $request;
            }
        } else {
            $qs_regex = '|\?.*?$|';
            preg_match($qs_regex, $request, $qs_match);
            if(!empty($qs_match[0])) {
                $query_string = $qs_match[0];
                $request = preg_replace($qs_regex, '', $request);
            } else {
                $query_string = '';
            }
            $request = preg_replace("|$wp_rewrite->pagination_base/\d+/?$|", '', $request);
            $request = preg_replace('|^' . preg_quote($wp_rewrite->index, '|') . '|i', '', $request);
            $request = ltrim($request, '/');
            $base = trailingslashit(get_bloginfo('url'));
            if($wp_rewrite->using_index_permalinks() &&($pagenum > 1 || '' != $request)) {
                $base .= $wp_rewrite->index . '/';
            }
            if($pagenum > 1) {
                $request =((!empty($request)) ? trailingslashit($request) : $request) . user_trailingslashit($wp_rewrite->pagination_base . "/" . $pagenum, 'paged');
            }
            $result = $base . $request . $query_string;
        }
        $result = apply_filters('get_pagenum_link', $result);
        if($escape) {
            return esc_url($result);
        }
        return esc_url_raw($result);
    }
}

function hocwp_get_query($args = array()) {
    global $wp_query;
    $query = isset($args['query']) ? $args['query'] : null;
    if(!hocwp_object_valid($query)) {
        $query = $wp_query;
    }
    return $query;
}

function hocwp_get_total_page($args = array()) {
    $query = hocwp_get_query($args);
    $posts_per_page = isset($query->query_vars['posts_per_page']) ? $query->query_vars['posts_per_page'] : get_option('posts_per_page');
    if(1 > $posts_per_page) {
        return 0;
    }
    $total_page = intval(ceil($query->found_posts / $posts_per_page));
    return $total_page;
}

function hocwp_has_paged($args = array()) {
    $total = hocwp_get_total_page($args);
    if($total > 1) {
        return true;
    }
    return false;
}

function hocwp_build_pagination($args = array()) {
    $label_text = __('Pages', 'hocwp');
    if('vi' == hocwp_get_language()) {
        $label_text = 'Trang';
    }
    $default_label = $label_text;
    $default_previous = '&laquo;';
    $default_next = '&raquo;';
    $label = $default_label;
    $previous = $default_previous;
    $next = $default_next;
    $request = isset($args['request']) ? $args['request'] : '';
    if(empty($request)) {
        $request = hocwp_get_request();
    }
    $query = hocwp_get_query($args);
    $total_page = hocwp_get_total_page($args);
    $current_page = isset($query->query_vars['paged']) ? $query->query_vars['paged'] : '0';
    if(1 > $current_page || $current_page > $total_page) {
        $current_page = hocwp_get_paged();
    }
    $args['current_page'] = $current_page;
    if(1 >= $total_page) {
        return '';
    }
    $args['total_page'] = $total_page;
    $result = '';
    $label = trim($label);
    if(!empty($label)) {
        $result .= '<span class="item label-item">' . $label . '</span>';
    }
    if($current_page > 1) {
        $link_href = hocwp_get_pagenum_link(array('pagenum' => ($current_page - 1), 'request' => $request));
        $result .= '<a class="item link-item previous-item" href="' . $link_href . '" data-paged="' . ($current_page - 1) . '">' . $previous . '</a>';
    }
    $result .= hocwp_loop_pagination_item($args);
    if($current_page < $total_page) {
        $link_href = hocwp_get_pagenum_link(array('pagenum' => ($current_page + 1), 'request' => $request));
        $result .= '<a href="' . $link_href . '" class="item next-item link-item" data-paged="' . ($current_page + 1) . '">' . $next . '</a>';
    }
    return $result;
}

function hocwp_show_pagination($args = array()) {
    $default_style = 'default';
    $default_border_radius = 'default';
    $style = $default_style;
    $border_radius = isset($args['border_radius']) ? $args['border_radius'] : $default_border_radius;

    $style .= '-style';
    $class = 'pagination loop-paginations hocwp-pagination';
    $class .= ' '.$style;
    switch($border_radius) {
        case 'circle':
            $class .= ' border-radius-circle';
            break;
        case 'default':
            break;
        case 'none':
            $class .= ' no-border-radius';
            break;
    }
    $class = trim($class);
    if(hocwp_has_paged($args)) {
        hocwp_add_string_with_space_before($class, 'has-paged');
    } else {
        hocwp_add_string_with_space_before($class, 'no-paged');
    }
    echo '<nav class="' . $class . '">';
    echo hocwp_build_pagination($args);
    echo '</nav>';
}

function hocwp_loop_pagination_item($args = array()) {
    // The number of page links to show before and after the current page.
    $default_range = 3;
    $range = $default_range;
    // The number of page links to show at beginning and end of pagination.
    $default_anchor = 1;
    $anchor = $default_anchor;
    // The minimum number of page links before ellipsis shows.
    $default_gap = 3;
    $gap = $default_gap;
    $current_page = isset($args['current_page']) ? $args['current_page'] : 1;
    $total_page = isset($args['total_page']) ? $args['total_page'] : 1;
    $request = isset($args['request']) ? $args['request'] : hocwp_get_request();

    $hidden_button = '<span class="item hidden-item">&hellip;</span>';
    $result = '';
    $hidden_before = false;
    $hidden_after = false;
    $before_current = $current_page - $range;
    $after_current = $current_page + $range;
    for($i = 1; $i <= $total_page; $i++) {
        if($current_page == $i) {
            $result .= '<span class="item current-item">' . $i .'</span>';
        } else {
            $count_hidden_button_before = $before_current - ($anchor + 1);
            $count_hidden_button_after = $total_page - ($after_current + 1);
            $show_hidden_button_before = ($i < $before_current && !$hidden_before && $count_hidden_button_before >= $gap) ? true : false;
            $show_hidden_button_after = ($i > $after_current && !$hidden_after && $count_hidden_button_after >= $gap) ? true : false;
            if(1 == $i || $total_page == $i || ($i <= $after_current && $i >= $before_current)) {
                $link_href = hocwp_get_pagenum_link(array('pagenum' => $i, 'request' => $request));
                $result .= '<a class="item link-item" href="' . $link_href . '" data-paged="' . $i . '">' . $i . '</a>';
            } else {
                if($show_hidden_button_before) {
                    $result .= $hidden_button;
                    $hidden_before = true;
                    $i = $before_current - 1;
                } elseif($i < $before_current) {
                    $link_href = hocwp_get_pagenum_link(array('pagenum' => $i, 'request' => $request));
                    $result .= '<a class="item link-item" href="' . $link_href . '" data-paged="' . $i . '">' . $i . '</a>';
                } elseif($show_hidden_button_after) {
                    $result .= $hidden_button;
                    $hidden_after = true;
                    $i = $total_page - 1;
                } else {
                    $link_href = hocwp_get_pagenum_link(array('pagenum' => $i, 'request' => $request));
                    $result .= '<a class="item link-item" href="' . $link_href . '" data-paged="' . $i . '">' . $i . '</a>';
                }
            }
        }
    }
    return $result;
}

function hocwp_get_paged() {
    return absint(get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
}

function hocwp_pagination($args = array()) {
    hocwp_show_pagination($args);
}