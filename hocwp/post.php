<?php
if(!function_exists('add_filter')) exit;

function hocwp_post_class($classes) {
    $classes[] = 'hocwp-post';
    $classes[] = 'hentry';
    $classes[] = 'entry';
    return $classes;
}
add_filter('post_class', 'hocwp_post_class');

function hocwp_excerpt_more($more) {
    $read_more_text = apply_filters('hocwp_read_more_text', __('Continue reading', 'hocwp'));
    $read_more_text = apply_filters('hocwp_excerpt_more_text', $read_more_text);
    $link = sprintf('<a href="%1$s" class="more-link">%2$s</a>',
        esc_url(get_permalink(get_the_ID())),
        sprintf($read_more_text . '%s', '<span class="screen-reader-text">' . get_the_title(get_the_ID()) . '</span>')
    );
    $link = apply_filters('hocwp_excerpt_continue_reading_link', $link);
    return apply_filters('hocwp_excerpt_more', '&hellip; ' . $link);
}
add_filter('excerpt_more', 'hocwp_excerpt_more');

function hocwp_post_change_content_url($old_url, $new_url) {
    global $wpdb;
    $sql = "UPDATE $wpdb->posts SET post_content = (REPLACE (post_content, '$old_url', '$new_url'))";
    return $wpdb->query($sql);
}

function hocwp_get_post_views($post_id = null) {
    if(!is_numeric($post_id)) {
        $post_id = get_the_ID();
    }
    $result = get_post_meta($post_id, 'views', true);
    $result = absint($result);
    if(is_single() && $result < 1) {
        $result = 1;
        update_post_meta($post_id, 'views', 1);
    }
    return $result;
}

function hocwp_get_post_likes($post_id = null) {
    if(!is_numeric($post_id)) {
        $post_id = get_the_ID();
    }
    $result = get_post_meta($post_id, 'likes', true);
    $result = absint($result);
    return $result;
}

function hocwp_get_post_dislikes($post_id = null) {
    if(!is_numeric($post_id)) {
        $post_id = get_the_ID();
    }
    $result = get_post_meta($post_id, 'dislikes', true);
    $result = absint($result);
    return $result;
}

function hocwp_get_post_thumbnail_url($post_id = '', $size = 'full') {
    $result = '';
    if(empty($post_id)) {
        $post_id = get_the_ID();
    }
    if(has_post_thumbnail($post_id)) {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        if(hocwp_media_file_exists($thumbnail_id)) {
            $image_attributes = wp_get_attachment_image_src($thumbnail_id, $size);
            if($image_attributes) {
                $result = $image_attributes[0];
            }
        }
    }
    if(empty($result)) {
        $result = get_post_meta($post_id, 'thumbnail_url', true);
    }
    if(empty($result)) {
        $post = get_post($post_id);
        if(hocwp_object_valid($post)) {
            $result = hocwp_get_first_image_source($post->post_content);
        }
        if(empty($result)) {
            $thumbnail = hocwp_option_get_value('writing', 'default_post_thumbnail');
            $thumbnail = hocwp_sanitize_media_value($thumbnail);
            $result = $thumbnail['url'];
        }
    }
    $result = apply_filters('hocwp_post_pre_post_thumbnail', $result, $post_id);
    if(empty($result)) {
        $no_thumbnail = HOCWP_URL . '/images/no-thumbnail.png';
        $no_thumbnail = apply_filters('hocwp_no_thumbnail_url', $no_thumbnail);
        $result = $no_thumbnail;
    }
    $result = apply_filters('hocwp_post_thumbnail', $result, $post_id);
    return $result;
}

function hocwp_post_thumbnail($args = array()) {
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';
    if(empty($post_id)) {
        $post_id = get_the_ID();
    }
    if(post_password_required($post_id) || is_attachment()) {
        return;
    }
    $thumbnail_url = hocwp_get_value_by_key($args, 'thumbnail_url');
    if(empty($thumbnail_url)) {
        $thumbnail_url = get_post_meta($post_id, 'large_thumbnail', true);
        $thumbnail_url = hocwp_sanitize_media_value($thumbnail_url);
        $thumbnail_url = $thumbnail_url['url'];
        if(empty($thumbnail_url)) {
            $thumbnail_url = hocwp_get_post_thumbnail_url($post_id);
        }
    }
    if(empty($thumbnail_url)) {
        return;
    }
    $bfi_thumb = isset($args['bfi_thumb']) ? $args['bfi_thumb'] : true;
    $bfi_thumb = apply_filters('hocwp_use_bfi_thumb', $bfi_thumb, $post_id);
    $size = hocwp_sanitize_size($args);
    $width = $size[0];
    $height = $size[1];
    if($bfi_thumb) {
        $params = isset($args['params']) ? $args['params'] : array();
        if(is_numeric($width) && $width > 0) {
            $params['width'] = $width;
        }
        if(is_numeric($height) && $height > 0) {
            $params['height'] = $height;
        }
        $bfi_url = apply_filters('hocwp_pre_bfi_thumb', '', $thumbnail_url, $params);
        if(empty($bfi_url)) {
            $bfi_url = bfi_thumb($thumbnail_url, $params);
        }
        if(!empty($bfi_url)) {
            $thumbnail_url = $bfi_url;
        }
    }
    $img = new HOCWP_HTML('img');
    if(is_numeric($width) && $width > 0) {
        $img->set_attribute('width', $size[0]);
    }
    if(is_numeric($height) && $height > 0) {
        $img->set_attribute('height', $size[1]);
    }
    $permalink = hocwp_get_value_by_key($args, 'permalink', get_permalink($post_id));
    $lazyload = hocwp_get_value_by_key($args, 'lazyload', false);
    $before = hocwp_get_value_by_key($args, 'before');
    $after = hocwp_get_value_by_key($args, 'after');
    $img->set_attribute('alt', get_the_title($post_id));
    $img->set_class('attachment-post-thumbnail wp-post-image img-responsive');
    $img->set_attribute('src', $thumbnail_url);
    $bk_img = '';
    if((bool)$lazyload) {
        $img->set_wrap_tag('noscript');
        $bk_img = $img->build();
        $img->set_wrap_tag('');
        $loading_icon = hocwp_get_value_by_key($args, 'loading_icon');
        if(!hocwp_is_image($loading_icon)) {
            $loading_icon = hocwp_get_image_url('transparent.gif');
        }
        $img->set_image_src($loading_icon);
        $img->set_attribute('data-original', $thumbnail_url);
        $img->add_class('lazyload');
    }
    $loop = isset($args['loop']) ? $args['loop'] : true;
    $custom_html = isset($args['custom_html']) ? $args['custom_html'] : '';
    $cover = hocwp_get_value_by_key($args, 'cover');
    echo $before;
    if(is_singular() && !$loop) : ?>
        <div class="post-thumbnail entry-thumb"<?php hocwp_html_tag_attributes('div', 'entry_thumb'); ?>>
            <?php
            $img->output();
            if((bool)$lazyload) {
                echo $bk_img;
            }
            echo $custom_html;
            if(current_theme_supports('hocwp-schema')) {
                ?>
                <meta itemprop="url" content="<?php echo $thumbnail_url; ?>">
                <meta itemprop="width" content="<?php echo $width; ?>">
                <meta itemprop="height" content="<?php echo $height; ?>">
                <?php
            }
            ?>
        </div>
    <?php else : ?>
        <?php if(!empty($custom_html)) : ?>
            <div class="thumbnail-wrap">
        <?php endif; ?>
        <a class="post-thumbnail-loop entry-thumb" href="<?php echo $permalink; ?>" aria-hidden="true"<?php hocwp_html_tag_attributes('a', 'entry_thumb'); ?>>
            <?php
            $img->output();
            if((bool)$lazyload) {
                echo $bk_img;
            }
            if($cover) {
                echo '<span class="cover"></span>';
            }
            if(current_theme_supports('hocwp-schema')) {
                ?>
                <meta itemprop="url" content="<?php echo $thumbnail_url; ?>">
                <meta itemprop="width" content="<?php echo $width; ?>">
                <meta itemprop="height" content="<?php echo $height; ?>">
                <?php
            }
            ?>
        </a>
        <?php echo $custom_html; ?>
        <?php if(!empty($custom_html)) : ?>
            </div>
        <?php endif; ?>
    <?php
    endif;
    echo $after;
}

function hocwp_post_type_no_featured_field() {
    return apply_filters('hocwp_post_type_no_featured_field', array('page'));
}

function hocwp_get_pages($args = array()) {
    return get_pages($args);
}

function hocwp_get_pages_by_template($template_name, $args = array()) {
    $args['meta_key'] = '_wp_page_template';
    $args['meta_value'] = $template_name;
    $result = hocwp_get_pages($args);
    $output = strtoupper(hocwp_get_value_by_key($args, 'output'));
    if(OBJECT === $output && hocwp_array_has_value($result)) {
        $result = array_shift($result);
    }
    return $result;
}

function hocwp_get_page_by_template($template_name) {
    return hocwp_get_pages_by_template($template_name, array('output' => 'object'));
}

function hocwp_article_before($post_class = '') {
    if(current_theme_supports('hocwp-schema')) {
        ?>
        <article <?php post_class($post_class); ?> data-id="<?php the_ID(); ?>"<?php hocwp_html_tag_attributes('article', 'post'); ?>>
        <?php
    } else {
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class($post_class); ?> data-id="<?php the_ID(); ?>"<?php hocwp_html_tag_attributes('article', 'post'); ?>>
        <?php
    }
}

function hocwp_article_after() {
    ?>
    </article><!-- #post-## -->
    <?php
}

function hocwp_post_title_link($args = array()) {
    $title = hocwp_get_value_by_key($args, 'title');
    $permalink = hocwp_get_value_by_key($args, 'permalink', get_permalink());
    if(empty($title)) {
        the_title(sprintf('<h2 class="entry-title post-title" itemprop="headline"><a href="%s" rel="bookmark">', esc_url($permalink)), '</a></h2>');
    } else {
        $title = sprintf('<h2 class="entry-title post-title" itemprop="headline"><a href="%s" rel="bookmark">', esc_url($permalink)) . $title . '</a></h2>';
        echo $title;
    }
}

function hocwp_post_title_single($args = array()) {
    $class = hocwp_get_value_by_key($args, 'class');
    hocwp_add_string_with_space_before($class, 'entry-title post-title');
    $tag = hocwp_get_value_by_key($args, 'tag', 'h1');
    the_title('<' . $tag . ' class="' . $class . '" itemprop="headline">', '</' . $tag . '>');
}

function hocwp_article_header($args = array()) {
    $loop = isset($args['loop']) ? $args['loop'] : false;
    $entry_meta = isset($args['entry_meta']) ? $args['entry_meta'] : true;
    ?>
    <header class="entry-header">
        <?php
        if(!$loop && (is_single() || is_singular())) {
            hocwp_post_title_single();
        } else {
            hocwp_post_title_link();
        }
        if(!is_page() && $entry_meta) {
            ?>
            <div class="entry-meta">
                <?php hocwp_entry_meta(); ?>
            </div><!-- .entry-meta -->
            <?php
        }
        ?>
    </header><!-- .entry-header -->
    <?php
}

function hocwp_article_content() {
    ?>
    <div class="entry-content">
        <?php the_content(); ?>
    </div>
    <?php
}

function hocwp_article_footer($args = array()) {
    $entry_meta = isset($args['entry_meta']) ? $args['entry_meta'] : true;
    ?>
    <footer class="entry-footer">
        <?php if($entry_meta) : ?>
            <div class="entry-meta">
                <?php hocwp_entry_meta(); ?>
                <?php edit_post_link( __( 'Edit', 'hocwp' ), '<span class="edit-link">', '</span>' ); ?>
            </div>
        <?php endif; ?>
    </footer><!-- .entry-footer -->
    <?php
}

function hocwp_post_author_box() {
    if((is_single() || is_singular()) && get_the_author_meta('description')) {
        get_template_part('hocwp/theme/biography');
    }
}

function hocwp_post_get_taxonomies($object, $output = 'names') {
    return get_object_taxonomies($object, $output);
}

function hocwp_post_get_top_parent_terms($post, $taxonomy = 'any') {
    $result = array();
    $taxonomies = array($taxonomy);
    if('any' === $taxonomy) {
        $taxonomies = hocwp_post_get_taxonomies($post);
    }
    foreach($taxonomies as $tax) {
        $terms = wp_get_post_terms($post->ID, $tax);
        foreach($terms as $term) {
            if($term->parent != 0) {
                $term = hocwp_term_get_top_most_parent($term);
            }
            $result[] = $term;
        }
    }
    return $result;
}

function hocwp_post_get_first_term($post_id = null, $taxonomy = 'category') {
    if(!hocwp_id_number_valid($post_id)) {
        $post_id = get_the_ID();
    }
    $terms = wp_get_post_terms($post_id, $taxonomy);
    if(hocwp_array_has_value($terms)) {
        $terms = current($terms);
    }
    return $terms;
}

function hocwp_insert_post($args = array()) {
    $post_title = '';
    $post_content = '';
    $post_status = 'pending';
    $post_type = 'post';
    $post_author = 1;
    $first_admin = hocwp_get_first_admin();
    if($first_admin) {
        $post_author = $first_admin->ID;
    }
    $defaults = array(
        'post_title' => $post_title,
        'post_content' => $post_content,
        'post_status' => $post_status,
        'post_type' => $post_type,
        'post_author' => $post_author,
        'ping_status' => get_option('default_ping_status'),
        'post_parent' => 0,
        'menu_order' => 0,
        'to_ping' =>  '',
        'pinged' => '',
        'post_password' => '',
        'guid' => '',
        'post_content_filtered' => '',
        'post_excerpt' => '',
        'import_id' => 0
    );
    $args = wp_parse_args($args, $defaults);
    $args['post_title'] = wp_strip_all_tags($args['post_title']);
    $post_id = wp_insert_post($args);
    return $post_id;
}

function hocwp_get_post_by_meta($key, $value, $args = array()) {
    $defaults = array(
        'post_type' => 'any',
        'posts_per_page' => -1,
        'meta_key' => $key,
        'meta_value' => $value
    );
    $args = wp_parse_args($args, $defaults);
    return hocwp_query($args);
}

function hocwp_get_post_by_column($column_name, $column_value, $output = 'OBJECT', $args = array()) {
    global $wpdb;
    $post_type = hocwp_get_value_by_key($args, 'post_type');
    $post_types = hocwp_sanitize_array($post_type);
    $output = strtoupper($output);
    $sql = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE $column_name = %s", $column_value);
    $count_type = 0;
    foreach($post_types as $post_type) {
        if(0 == $count_type) {
            $sql .= " AND post_type = '$post_type'";
        } else {
            $sql .= " OR post_type = '$post_type'";
        }
        $count_type++;
    }
    $post_id = $wpdb->get_var($sql);
    $result = '';
    switch($output) {
        case OBJECT:
            if(hocwp_id_number_valid($post_id)) {
                $result = get_post($post_id);
            }
            break;
        default:
            $result = $post_id;
    }
    return $result;
}

function hocwp_get_post_id_by_slug($slug) {
    return hocwp_get_post_by_column('post_name', $slug, 'post_id');
}

function hocwp_get_post_permalink_by_slug($slug, $default = 'home') {
    $post = hocwp_get_post_by_slug($slug);
    if(is_a($post, 'WP_Post')) {
        return get_permalink($post);
    }
    if('home' == $default) {
        $default = get_home_url();
    }
    return $default;
}

function hocwp_get_post_by_slug($slug) {
    return hocwp_get_post_by_column('post_name', $slug);
}

function hocwp_get_author_posts_url() {
    global $authordata;
    if(!hocwp_object_valid($authordata)) {
        return '';
    }
    return get_author_posts_url($authordata->ID, $authordata->user_nicename);
}

function hocwp_get_post_meta($meta_key, $post_id = null) {
    if(!hocwp_id_number_valid($post_id)) {
		$post_id = get_the_ID();
	}
	return get_post_meta($post_id, $meta_key, true);
}

function hocwp_get_post_comment_count($post_id = null, $status = 'approved') {
    if(!hocwp_id_number_valid($post_id)) {
        $post_id = get_the_ID();
    }
    $comments = get_comment_count($post_id);
    return hocwp_get_value_by_key($comments, $status);
}