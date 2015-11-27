<?php
if(!function_exists('add_filter')) exit;
function hocwp_breadcrumb($args = array()) {
    $before = hocwp_get_value_by_key($args, 'before');
    $after = hocwp_get_value_by_key($args, 'after');
    if(function_exists('yoast_breadcrumb') && hocwp_wpseo_breadcrumb_enabled()) {
        yoast_breadcrumb('<div class="hocwp-breadcrumb breadcrumb yoast">' . $before, $after . '</div>');
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

function hocwp_entry_meta() {
    if ( is_sticky() && is_home() && ! is_paged() ) {
        printf( '<span class="sticky-post">%s</span>', __( 'Featured', 'hocwp' ) );
    }

    $format = get_post_format();
    if ( current_theme_supports( 'post-formats', $format ) ) {
        printf( '<span class="entry-format">%1$s<a href="%2$s">%3$s</a></span>',
            sprintf( '<span class="screen-reader-text">%s </span>', _x( 'Format', 'Used before post format.', 'hocwp' ) ),
            esc_url( get_post_format_link( $format ) ),
            get_post_format_string( $format )
        );
    }

    if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf( $time_string,
            esc_attr( get_the_date( 'c' ) ),
            get_the_date(),
            esc_attr( get_the_modified_date( 'c' ) ),
            get_the_modified_date()
        );

        printf( '<span class="posted-on"><span class="screen-reader-text">%1$s </span><a href="%2$s" rel="bookmark">%3$s</a></span>',
            _x( 'Posted on', 'Used before publish date.', 'hocwp' ),
            esc_url( get_permalink() ),
            $time_string
        );
    }

    if ( 'post' == get_post_type() ) {
        if ( is_singular() || is_multi_author() ) {
            printf( '<span class="byline"><span class="author vcard"><span class="screen-reader-text">%1$s </span><a class="url fn n" href="%2$s">%3$s</a></span></span>',
                _x( 'Author', 'Used before post author name.', 'hocwp' ),
                esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                get_the_author()
            );
        }

        $categories_list = get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'hocwp' ) );
        if ( $categories_list ) {
            printf( '<span class="cat-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
                _x( 'Categories', 'Used before category names.', 'hocwp' ),
                $categories_list
            );
        }
    }

    if ( is_attachment() && wp_attachment_is_image() ) {
        // Retrieve attachment metadata.
        $metadata = wp_get_attachment_metadata();

        printf( '<span class="full-size-link"><span class="screen-reader-text">%1$s </span><a href="%2$s">%3$s &times; %4$s</a></span>',
            _x( 'Full size', 'Used before full size attachment link.', 'hocwp' ),
            esc_url( wp_get_attachment_url() ),
            $metadata['width'],
            $metadata['height']
        );
    }

    if (!is_page() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
        echo '<span class="comments-link">';
        /* translators: %s: post title */
        comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'hocwp' ), get_the_title() ) );
        echo '</span>';
    }
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