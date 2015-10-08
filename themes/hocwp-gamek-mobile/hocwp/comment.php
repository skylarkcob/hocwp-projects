<?php
function hocwp_comment_wp_insert_comment($comment_id, $comment_object) {

}
add_action('wp_insert_comment', 'hocwp_comment_wp_insert_comment', 10, 2);

function hocwp_comment_transition_comment_status($new_status, $old_status, $comment) {
    if($old_status != $new_status) {
        if('approved' === $new_status) {
            do_action('hocwp_comment_approved', $comment);
            $notify_me = get_comment_meta($comment->comment_ID, 'notify_me', true);
            if(!empty($notify_me)) {

            }
        }
    }
}
add_action('transition_comment_status', 'hocwp_comment_transition_comment_status', 10, 3);

function hocwp_comment_form_default_fields($fields) {
    $commenter = wp_get_current_commenter();
    $user = wp_get_current_user();
    $user_identity = $user->exists() ? $user->display_name : '';
    $format = current_theme_supports('html5', 'comment-form') ? 'html5' : 'xhtml';
    $format = apply_filters('hocwp_comment_form_format', $format);
    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');
    $html_req = ($req ? " required='required'" : '');
    $html5 = 'html5' === $format;
    $fields = array(
        'author' => '<p class="comment-form-author">' . '<label for="author">' . __('Name', 'hocwp') . ($req ? ' <span class="required">*</span>' : '') . '</label> ' .
            '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . $html_req . ' /></p>',
        'email' => '<p class="comment-form-email"><label for="email">' . __('Email', 'hocwp') . ($req ? ' <span class="required">*</span>' : '') . '</label> ' .
            '<input id="email" name="email" ' . ($html5 ? 'type="email"' : 'type="text"') . ' value="' . esc_attr($commenter['comment_author_email']) . '" size="30" aria-describedby="email-notes"' . $aria_req . $html_req  . ' /></p>',
        'url' => '<p class="comment-form-url"><label for="url">' . __('Website', 'hocwp') . '</label> ' .
            '<input id="url" name="url" ' . ($html5 ? 'type="url"' : 'type="text"') . ' value="' . esc_attr($commenter['comment_author_url']) . '" size="30" /></p>',
    );
    return $fields;
}
add_filter('comment_form_default_fields', 'hocwp_comment_form_default_fields');

function hocwp_comment_form_defaults($defaults) {
    $commenter = wp_get_current_commenter();
    $user = wp_get_current_user();
    $user_identity = $user->exists() ? $user->display_name : '';
    $format = current_theme_supports('html5', 'comment-form') ? 'html5' : 'xhtml';
    $format = apply_filters('hocwp_comment_form_format', $format);
    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');
    $html_req = ($req ? " required='required'" : '');
    $required_text = sprintf(' ' . __('Required fields are marked %s', 'hocwp'), '<span class="required">*</span>');
    $html5 = 'html5' === $format;
    $defaults = array(
        'comment_field' => '<p class="comment-form-comment"><label for="comment">' . _x('Comment', 'noun') . '</label> <textarea id="comment" name="comment" cols="45" rows="8"  aria-required="true" required="required"></textarea></p>',
        'must_log_in' => '<p class="must-log-in">' . sprintf(__('You must be <a href="%s">logged in</a> to post a comment.', 'hocwp'), wp_login_url(apply_filters('the_permalink', get_permalink(get_the_ID())))) . '</p>',
        'logged_in_as' => '<p class="logged-in-as">' . sprintf(__('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'hocwp'), get_edit_user_link(), $user_identity, wp_logout_url(apply_filters('the_permalink', get_permalink(get_the_ID())))) . '</p>',
        'comment_notes_before' => '<p class="comment-notes"><span id="email-notes">' . __('Your email address will not be published.', 'hocwp') . '</span>'. ($req ? $required_text : '') . '</p>',
        'comment_notes_after' => '',
        'id_form' => 'commentform',
        'id_submit' => 'submit',
        'class_submit' => 'submit',
        'name_submit' => 'submit',
        'title_reply' => '<span class="title-text">' . __('Leave a Reply', 'hocwp') . '</span>',
        'title_reply_to' => __('Leave a Reply to %s', 'hocwp'),
        'cancel_reply_link' => __('Click here to cancel reply.', 'hocwp'),
        'label_submit' => __('Post Comment', 'hocwp'),
        'submit_button' => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
        'submit_field' => '<p class="form-submit">%1$s %2$s</p>',
        'format' => 'html5'
    );
    return $defaults;
}
add_filter('comment_form_defaults', 'hocwp_comment_form_defaults');

function hocwp_wp_list_comments_args($args) {
    $args['reply_text'] = '<i class="fa fa-reply"></i><span class="text">' . __('Reply', 'hocwp') . '</span>';
    return $args;
}
add_filter('wp_list_comments_args', 'hocwp_wp_list_comments_args', 10);

function hocwp_get_comment_likes($comment_id) {
    $result = get_comment_meta($comment_id, 'likes', true);
    $result = absint($result);
    return $result;
}