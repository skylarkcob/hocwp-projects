<?php
if(post_password_required()) {
    return;
}
$comments_title = apply_filters('hocwp_comments_title_text', __('Leave your comment', 'hocwp'));
?>
<div id="comments" class="comments-area">
    <h3 class="comments-title">
        <span class="title-left"><?php echo $comments_title; ?></span>
        <?php if(have_comments()) : ?>
            <span class="count">
                <?php
                $comments_number = get_comments_number();
                $comments_count = apply_filters('hocwp_comments_title_count', sprintf(_nx('1 comment', '%d comments', $comments_number, 'comments title', 'hocwp'), number_format_i18n($comments_number)), $comments_number);
                echo $comments_count;
                ?>
            </span>
        <?php endif; ?>
    </h3>
    <?php
    if(have_comments()) {
        hocwp_comment_nav();
        $classes = apply_filters('hocwp_comment_list_class', array());
        $classes[] = 'comment-list';
        $classes[] = 'list-comments';
        hocwp_sanitize_array($classes);
        echo '<ol class="' . implode(' ', $classes) . '">';
        wp_list_comments();
        echo '</ol>';
        hocwp_comment_nav();
    }
    if(!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) {
        $no_comment_text = apply_filters('hocwp_comments_closed_text', __('Comments are closed.', 'hocwp'));
        ?>
        <p class="no-comments"><?php echo $no_comment_text; ?></p>
        <?php
    }
    comment_form();
    ?>
</div><!-- .comments-area -->
