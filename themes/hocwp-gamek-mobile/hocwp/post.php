<?php
function hocwp_post_class($classes) {
    $classes[] = 'hocwp-post';
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
    return apply_filters('hocwp_excerpt_more', '&hellip; ' . $link);
}
add_filter('excerpt_more', 'hocwp_excerpt_more');

function hocwp_post_change_content_url($old_url, $new_url) {
    global $wpdb;
    return $wpdb->query("UPDATE $wpdb->posts SET post_content = (REPLACE (post_content, '$old_url', '$new_url'))");
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
    if(empty($result)) {
        $no_thumbnail = get_template_directory_uri() . '/hocwp/images/no-thumbnail.png';
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
    $thumbnail_url = hocwp_get_post_thumbnail_url($post_id);
    if(empty($thumbnail_url)) {
        return;
    }
    $bfi_thumb = isset($args['bfi_thumb']) ? $args['bfi_thumb'] : true;
    if($bfi_thumb) {
        $width = isset($args['width']) ? $args['width'] : '';
        $height = isset($args['height']) ? $args['height'] : '';
        $params = isset($args['params']) ? $args['params'] : array();
        if(is_numeric($width) && $width > 0) {
            $params['width'] = $width;
        }
        if(is_numeric($height) && $height > 0) {
            $params['height'] = $height;
        }
        $bfi_url = bfi_thumb($thumbnail_url, $params);
        if(!empty($bfi_url)) {
            $thumbnail_url = $bfi_url;
        }
    }
    $img = new HOCWP_HTML('img');
    $img->set_attribute('alt', get_the_title($post_id));
    $img->set_class('attachment-post-thumbnail wp-post-image');
    $img->set_attribute('src', $thumbnail_url);
    $loop = isset($args['loop']) ? $args['loop'] : true;
    if(is_singular() && !$loop) : ?>
        <div class="post-thumbnail">
            <?php $img->output(); ?>
        </div>
    <?php else : ?>
        <a class="post-thumbnail" href="<?php echo get_permalink($post_id); ?>" aria-hidden="true">
            <?php $img->output(); ?>
        </a>
    <?php endif;
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
    return hocwp_get_pages($args);
}

function hocwp_article_before($post_class = '') {
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class($post_class); ?> data-id="<?php the_ID(); ?>">
    <?php
}

function hocwp_article_after() {
    ?>
    </article><!-- #post-## -->
    <?php
}

function hocwp_post_title_link() {
    the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>');
}

function hocwp_post_title_single() {
    the_title('<h1 class="entry-title">', '</h1>');
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