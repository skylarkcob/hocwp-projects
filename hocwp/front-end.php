<?php
if(!function_exists('add_filter')) exit;

function hocwp_breadcrumb($args = array()) {
    $before = hocwp_get_value_by_key($args, 'before');
    $after = hocwp_get_value_by_key($args, 'after');
    if(function_exists('yoast_breadcrumb') && hocwp_wpseo_breadcrumb_enabled()) {
        yoast_breadcrumb('<nav class="hocwp-breadcrumb breadcrumb yoast">' . $before, $after . '</nav>');
        return;
    }
    global $post, $wp_query;
    $separator = isset($args['separator']) ? $args['separator'] : '/';
    $breadcrums_id = isset($args['id']) ? $args['id'] : 'hocwp_breadcrumbs';
    $home_title = __('Home', 'hocwp');
    $custom_taxonomy = 'product_cat';
    $class = isset($args['class']) ? $args['class'] : '';
    $class = hocwp_add_string_with_space_before($class, 'list-inline list-unstyled breadcrumbs');
    if(!is_front_page()) {
        echo '<div class="hocwp-breadcrumb breadcrumb yoast">';
        echo '<ul id="' . $breadcrums_id . '" class="' . $class . '">';
        echo '<li class="item-home"><a class="bread-link bread-home" href="' . get_home_url() . '" title="' . $home_title . '">' . $home_title . '</a></li>';
        echo '<li class="separator separator-home"> ' . $separator . ' </li>';
        if(is_archive() && !is_tax() && !is_category()) {
            echo '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . post_type_archive_title('', false) . '</strong></li>';

        } elseif(is_archive() && is_tax() && !is_category()) {
            $post_type = get_post_type();
            if($post_type != 'post') {
                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);
                if(is_object($post_type_object)) {
                    echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
                    echo '<li class="separator"> ' . $separator . ' </li>';
                }
            }
            $custom_tax_name = get_queried_object()->name;
            echo '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . $custom_tax_name . '</strong></li>';

        } elseif(is_single()) {
            $post_type = get_post_type();
            if($post_type != 'post') {
                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);
                echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
                echo '<li class="separator"> ' . $separator . ' </li>';
            }
            $category = get_the_category();
            $array_values = array_values($category);
            $last_category = end($array_values);
            $get_cat_parents = '';
            if(is_object($last_category)) {
                $get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','), ',');
            }
            $cat_parents = explode(',', $get_cat_parents);
            $cat_display = '';
            foreach($cat_parents as $parents) {
                $cat_display .= '<li class="item-cat">' . $parents . '</li>';
                $cat_display .= '<li class="separator"> ' . $separator . ' </li>';
            }
            $taxonomy_exists = taxonomy_exists($custom_taxonomy);
            if(empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {
                $taxonomy_terms = get_the_terms($post->ID, $custom_taxonomy);
                $cat_id = $taxonomy_terms[0]->term_id;
                $cat_nicename = $taxonomy_terms[0]->slug;
                $cat_link = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
                $cat_name = $taxonomy_terms[0]->name;

            }
            if(!empty($last_category)) {
                echo $cat_display;
                echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
            } elseif(!empty($cat_id)) {
                echo '<li class="item-cat item-cat-' . $cat_id . ' item-cat-' . $cat_nicename . '"><a class="bread-cat bread-cat-' . $cat_id . ' bread-cat-' . $cat_nicename . '" href="' . $cat_link . '" title="' . $cat_name . '">' . $cat_name . '</a></li>';
                echo '<li class="separator"> ' . $separator . ' </li>';
                echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';

            } else {
                echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
            }

        } elseif(is_category()) {
            echo '<li class="item-current item-cat"><strong class="bread-current bread-cat">' . single_cat_title('', false) . '</strong></li>';

        } elseif(is_page()) {
            if($post->post_parent) {
                $anc = get_post_ancestors($post->ID);
                $anc = array_reverse($anc);
                $anc = array_reverse($anc);
                $parents = '';
                foreach($anc as $ancestor) {
                    $parents .= '<li class="item-parent item-parent-' . $ancestor . '"><a class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a></li>';
                    $parents .= '<li class="separator separator-' . $ancestor . '"> ' . $separator . ' </li>';
                }
                echo $parents;
                echo '<li class="item-current item-' . $post->ID . '"><strong title="' . get_the_title() . '"> ' . get_the_title() . '</strong></li>';
            } else {
                echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '"> ' . get_the_title() . '</strong></li>';
            }

        } elseif(is_tag()) {
            $term_id = get_query_var('tag_id');
            $taxonomy = 'post_tag';
            $args ='include=' . $term_id;
            $terms = get_terms($taxonomy, $args);
            echo '<li class="item-current item-tag-' . $terms[0]->term_id . ' item-tag-' . $terms[0]->slug . '"><strong class="bread-current bread-tag-' . $terms[0]->term_id . ' bread-tag-' . $terms[0]->slug . '">' . $terms[0]->name . '</strong></li>';
        } elseif(is_day()) {
            echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
            echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';
            echo '<li class="item-month item-month-' . get_the_time('m') . '"><a class="bread-month bread-month-' . get_the_time('m') . '" href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</a></li>';
            echo '<li class="separator separator-' . get_the_time('m') . '"> ' . $separator . ' </li>';
            echo '<li class="item-current item-' . get_the_time('j') . '"><strong class="bread-current bread-' . get_the_time('j') . '"> ' . get_the_time('jS') . ' ' . get_the_time('M') . ' Archives</strong></li>';
        } elseif(is_month()) {
            echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
            echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';
            echo '<li class="item-month item-month-' . get_the_time('m') . '"><strong class="bread-month bread-month-' . get_the_time('m') . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</strong></li>';
        } elseif(is_year()) {
            echo '<li class="item-current item-current-' . get_the_time('Y') . '"><strong class="bread-current bread-current-' . get_the_time('Y') . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</strong></li>';
        } elseif(is_author()) {
            global $author;
            $userdata = get_userdata($author);
            echo '<li class="item-current item-current-' . $userdata->user_nicename . '"><strong class="bread-current bread-current-' . $userdata->user_nicename . '" title="' . $userdata->display_name . '">' . 'Author: ' . $userdata->display_name . '</strong></li>';
        } elseif(get_query_var('paged')) {
            echo '<li class="item-current item-current-' . get_query_var('paged') . '"><strong class="bread-current bread-current-' . get_query_var('paged') . '" title="Page ' . get_query_var('paged') . '">'.__('Page') . ' ' . get_query_var('paged') . '</strong></li>';
        } elseif(is_search()) {
            echo '<li class="item-current item-current-' . get_search_query() . '"><strong class="bread-current bread-current-' . get_search_query() . '" title="Search results for: ' . get_search_query() . '">Search results for: ' . get_search_query() . '</strong></li>';
        } elseif(is_404()) {
            echo '<li>' . __('Error 404', 'hocwp') . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}

function hocwp_entry_meta($args = array()) {
    $post_id = hocwp_get_value_by_key($args, 'post_id', get_the_ID());
    $class = hocwp_get_value_by_key($args, 'class');
    $cpost = get_post($post_id);
    if(!is_a($cpost, 'WP_Post')) {
        return;
    }
    $author_url = hocwp_get_author_posts_url();
    $comment_count = hocwp_get_post_comment_count($post_id);
    $comment_text = $comment_count . ' Bình luận';
    hocwp_add_string_with_space_before($class, 'entry-meta');
    ?>
    <p class="<?php echo $class; ?>">
        <time datetime="<?php the_time('c'); ?>" itemprop="datePublished" class="entry-time published date post-date"><?php echo get_the_date(); ?></time>
        <time datetime="<?php the_modified_time('c'); ?>" itemprop="dateModified" class="entry-modified-time date modified post-date"><?php the_modified_date(); ?></time>
        <span itemtype="http://schema.org/Person" itemscope itemprop="author" class="entry-author vcard author post-author">
            <span class="fn">
                <a rel="author" itemprop="url" class="entry-author-link" href="<?php echo $author_url; ?>"><span itemprop="name" class="entry-author-name"><?php the_author(); ?></span></a>
            </span>
        </span>
        <?php if(comments_open($post_id)) : ?>
            <span class="entry-comments-link">
                <a href="<?php the_permalink(); ?>#comments"><?php echo $comment_text; ?></a>
            </span>
        <?php endif; ?>
        <?php if(current_theme_supports('hocwp-schema')) : ?>
            <?php
            global $authordata;
            $author_id = 0;
            $author_name = '';
            $author_avatar = '';
            if(hocwp_object_valid($authordata)) {
                $author_id = $authordata->ID;
                $author_name = $authordata->display_name;
                $author_avatar = get_avatar_url($author_id, array('size' => 128));
            }
            $logo_url = apply_filters('hocwp_publisher_logo_url', '');
            ?>
            <span itemprop="publisher" itemscope itemtype="https://schema.org/Organization" class="small hidden">
                <span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                    <img alt="" src="<?php echo $logo_url; ?>">
                    <meta itemprop="url" content="<?php echo $logo_url; ?>">
                    <meta itemprop="width" content="600">
                    <meta itemprop="height" content="60">
                </span>
                <meta itemprop="name" content="<?php echo $author_name; ?>">
            </span>
        <?php endif; ?>
    </p>
    <?php
}

function hocwp_rel_canonical() {
    if(!is_singular() || has_action('wp_head', 'rel_canonical')) {
        return;
    }
    global $wp_the_query;
    if(!$id = $wp_the_query->get_queried_object_id()) {
        return;
    }
    $link = get_permalink($id);
    if($page = get_query_var('cpage')) {
        $link = get_comments_pagenum_link($page);
    }
    $link = apply_filters('hocwp_head_rel_canonical', $link, $id);
    echo "<link rel='canonical' href='$link' />\n";
}

function hocwp_posts_pagination($args = array()) {
    $defaults = array(
        'prev_text' => __('Trước', 'hocwp'),
        'next_text' => __('Tiếp theo', 'hocwp'),
        'screen_reader_text' => __('Phân trang', 'hocwp')
    );
    $args = wp_parse_args($args, $defaults);
    the_posts_pagination($args);
}

function hocwp_entry_content($content = '') {
    ?>
    <div class="entry-content" itemprop="text">
        <?php
        if(!empty($content)) {
            echo wpautop($content);
        } else {
            the_content();
        }
        ?>
    </div>
    <?php
}

function hocwp_entry_summary() {
    echo '<div class="entry-summary" itemprop="text">';
    the_excerpt();
    echo '</div>';
}

function hocwp_entry_tags() {
    echo '<div class="entry-tags">';
    the_tags('<span class="tag-label"><i class="fa fa-tag icon-left"></i><span class="text">Tags:</span></span>&nbsp;', ' ', '');
    echo '</div>';
}