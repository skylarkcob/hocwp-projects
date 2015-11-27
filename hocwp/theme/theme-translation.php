<?php
if(!function_exists('add_filter')) exit;
function hocwp_theme_translation_comments_title_text() {
    return __('Gửi bình luận của bạn', 'hocwp');
}
add_filter('hocwp_comments_title_text', 'hocwp_theme_translation_comments_title_text');

function hocwp_theme_translation_comments_title_count($text, $comments_number) {
    return sprintf(_nx('1 bình luận', '%d bình luận', $comments_number, 'tiêu đề bình luận', 'hocwp'), number_format_i18n($comments_number));
}
add_filter('hocwp_comments_title_count', 'hocwp_theme_translation_comments_title_count', 10, 2);

function hocwp_theme_translation_comment_form_defaults($defaults) {
    $commenter = wp_get_current_commenter();
    $user = wp_get_current_user();
    $user_identity = $user->exists() ? $user->display_name : '';
    $format = current_theme_supports('html5', 'comment-form') ? 'html5' : 'xhtml';
    $format = apply_filters('hocwp_comment_form_format', $format);
    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');
    $html_req = ($req ? " required='required'" : '');
    $required_text = sprintf(' ' . __('Những mục bắt buộc được đánh dấu %s', 'hocwp'), '<span class="required">*</span>');
    $html5 = 'html5' === $format;
    $defaults = array(
        'comment_field' => '<p class="comment-form-comment"><label for="comment">' . _x('Nội dung', 'noun') . '</label> <textarea id="comment" name="comment" cols="45" rows="8"  aria-required="true" required="required"></textarea></p>',
        'must_log_in' => '<p class="must-log-in">' . sprintf(__('Bạn phải <a href="%s">đăng nhập</a> trước khi có thể đăng bình luận.', 'hocwp'), wp_login_url(apply_filters('the_permalink', get_permalink(get_the_ID())))) . '</p>',
        'logged_in_as' => '<p class="logged-in-as">' . sprintf(__('Bạn đang đăng nhập với tài khoản <a href="%1$s">%2$s</a>. <a href="%3$s" title="Thoát khỏi tài khoản này">Thoát?</a>', 'hocwp'), get_edit_user_link(), $user_identity, wp_logout_url(apply_filters('the_permalink', get_permalink(get_the_ID())))) . '</p>',
        'comment_notes_before' => '<p class="comment-notes"><span id="email-notes">' . __('Địa chỉ email của bạn sẽ được giữ bí mật.', 'hocwp') . '</span>'. ($req ? $required_text : '') . '</p>',
        'title_reply' => '<span class="title-text">' . __('Gửi bình luận', 'hocwp') . '</span>',
        'title_reply_to' => __('Gửi trả lời cho %s', 'hocwp'),
        'cancel_reply_link' => __('Nhấn vào đây để hủy trả lời.', 'hocwp'),
        'label_submit' => __('Gửi bình luận', 'hocwp')
    );
    return $defaults;
}
add_filter('comment_form_defaults', 'hocwp_theme_translation_comment_form_defaults');

function hocwp_theme_translation_comments_list_callback($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    $comment_id = $comment->comment_ID;
    $style = isset($args['style']) ? $args['style'] : 'ol';
    $avatar_size = isset($args['avatar_size']) ? absint($args['avatar_size']) : 64;
    $max_depth = isset($args['max_depth']) ? absint($args['max_depth']) : '';
    $comment_permalink = get_comment_link($comment);
    if('div' == $style) {
        $tag = 'div';
        $add_below = 'comment';
    } else {
        $tag = 'li';
        $add_below = 'div-comment';
    }
    $comment_author = '<div class="comment-author vcard">' . get_avatar($comment, $avatar_size) . '<b class="fn">' . get_comment_author_link() . '</b> <span class="says">nói:</span></div>';
    $comment_metadata = '<div class="comment-metadata"><a href="' . $comment_permalink . '"><time datetime="' . get_comment_time('c') . '">' . hocwp_human_time_diff_to_now(get_comment_date('Y-m-d H:i:s', true)) . ' ' . __('trước', 'hocwp') . '</time></a> <a class="comment-edit-link" href="' . get_edit_comment_link($comment_id) . '">(' . __('Sửa', 'hocwp') . ')</a></div>';
    if($comment->comment_approved == '0') {
        $comment_metadata .= '<p class="comment-awaiting-moderation">' . __('Bình luận của bạn đang được chờ để xét duyệt.', 'hocwp') . '</p>';
    }
    $footer = new HOCWP_HTML('footer');
    $footer->set_class('comment-meta');
    $footer->set_text($comment_author . $comment_metadata);
    $comment_content = '<div class="comment-content">' . wpautop(get_comment_text($comment_id)) . '</div>';
    $reply = '<div class="reply comment-tools">';
    $reply .= get_comment_reply_link(array_merge($args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $max_depth)));
    $comment_tools_enabled = apply_filters('hocwp_comment_tools_enabled', true);
    if($comment_tools_enabled) {
        $class = 'comment-like comment-likes';
        $session_comment_liked_key = 'comment_' . $comment_id . '_likes';
        $liked = intval(isset($_SESSION[$session_comment_liked_key]) ? $_SESSION[$session_comment_liked_key] : '');
        if($liked == 1) {
            hocwp_add_string_with_space_before($class, 'disabled');
        }
        $a = new HOCWP_HTML('a');
        $a->set_class($class);
        $a->set_attribute('href', 'javascript:;');
        $a->set_attribute('data-session-likes-key', $session_comment_liked_key);
        $likes = hocwp_get_comment_likes($comment_id);
        $a->set_attribute('data-likes', $likes);
        $a->set_text('<span class="text">' . __('Thích', 'hocwp') . '</span> <i class="fa fa-thumbs-o-up"></i><span class="sep-dot">.</span> <span class="count">' . $likes . '</span>');
        $reply .= $a->build();
        $a->set_class('comment-report');
        $a->remove_attribute('data-session-liked-key');
        $a->set_text(__('Báo cáo vi phạm', 'hocwp') . '<i class="fa fa-flag"></i>');
        $reply .= $a->build();
        $a->set_class('comment-share');
        $share_text = '<span class="text">' . __('Chia sẻ', 'hocwp') . '<i class="fa fa-angle-down"></i></span>';
        $share_text .= '<span class="list-share">';
        $share_text .= '<i class="fa fa-facebook facebook" data-url="' . hocwp_get_social_share_url(array('social_name' => 'facebook', 'permalink' => $comment_permalink)) . '"></i>';
        $share_text .= '<i class="fa fa-google-plus google" data-url="' . hocwp_get_social_share_url(array('social_name' => 'googleplus', 'permalink' => $comment_permalink)) . '"></i>';
        $share_text .= '<i class="fa fa-twitter twitter" data-url="' . hocwp_get_social_share_url(array('social_name' => 'twitter', 'permalink' => $comment_permalink)) . '"></i>';
        $share_text .= '</span>';
        $a->set_text($share_text);
        $reply .= $a->build();
    }
    $reply .= '</div>';
    $article = new HOCWP_HTML('article');
    $article->set_attribute('id', 'div-comment-' . $comment_id);
    $article->set_class('comment-body');
    $article_text = $footer->build();
    $article_text .= $comment_content;
    $article_text .= $reply;
    $article->set_text($article_text);
    $html = new HOCWP_HTML($tag);
    $comment_class = get_comment_class(empty($args['has_children']) ? '' : 'parent');
    $comment_class = implode(' ', $comment_class);
    $html_atts = array(
        'class' => $comment_class,
        'id' => 'comment-' . $comment_id,
        'data-comment-id' => $comment_id
    );
    $html->set_attribute_array($html_atts);
    $html->set_text($article->build());
    $html->set_close(false);
    $html->output();
}

function hocwp_theme_translation_comment_form_default_fields($fields) {
    $commenter = wp_get_current_commenter();
    $user = wp_get_current_user();
    $user_identity = $user->exists() ? $user->display_name : '';
    $format = current_theme_supports('html5', 'comment-form') ? 'html5' : 'xhtml';
    $format = apply_filters('hocwp_comment_form_format', $format);
    $req = get_option('require_name_email');
    $aria_req = ($req ? "aria-required='true'" : '');
    $html_req = ($req ? "required='required'" : '');
    $require_attr = $aria_req . ' ' . $html_req;
    $html5 = 'html5' === $format;
    $fields = array(
        'author' => '<p class="comment-form-author">' . '<label for="author">' . __('Họ và tên', 'hocwp') . ($req ? ' <span class="required">*</span>' : '') . '</label> ' .
            '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" ' . $require_attr . ' /></p>',
        'email' => '<p class="comment-form-email"><label for="email">' . __('Địa chỉ email', 'hocwp') . ($req ? ' <span class="required">*</span>' : '') . '</label> ' .
            '<input id="email" name="email" ' . ($html5 ? 'type="email"' : 'type="text"') . ' value="' . esc_attr($commenter['comment_author_email']) . '" size="30" aria-describedby="email-notes" ' . $require_attr  . ' /></p>',
        'url' => '<p class="comment-form-url"><label for="url">' . __('Trang web', 'hocwp') . '</label> ' .
            '<input id="url" name="url" ' . ($html5 ? 'type="url"' : 'type="text"') . ' value="' . esc_attr($commenter['comment_author_url']) . '" size="30" /></p>',
    );
    return $fields;
}
add_filter('comment_form_default_fields', 'hocwp_theme_translation_comment_form_default_fields');

function hocwp_theme_translation_wp_list_comments_args($args) {
    $args['reply_text'] = '<i class="fa fa-reply"></i><span class="text">' . __('Trả lời', 'hocwp') . '</span>';
    $args['callback'] = 'hocwp_theme_translation_comments_list_callback';
    return $args;
}
add_filter('wp_list_comments_args', 'hocwp_theme_translation_wp_list_comments_args', 10);

function hocwp_theme_translation_gettext($translation, $text) {
    switch($text) {
        case 'Nothing Found':
            $translation = 'Không tìm thấy nội dung';
            break;
        case 'Ready to publish your first post? <a href="%1$s">Get started here</a>.':
            $translation = 'Bạn đã sẵn sàng viết bài? <a href="%1$s">Bắt đầu từ đây</a>.';
            break;
        case 'Sorry, but nothing matched your search terms. Please try again with some different keywords.':
            $translation = 'Xin lỗi, nhưng hệ thống không tìm thấy nội dung bạn đang tìm kiếm, có thể thử lại bằng cách dùng từ khóa khác.';
            break;
        case 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.':
            $translation = 'Hệ thống không tìm thấy nội dung bạn đang muốn xem. Bạn có thể thử dùng công cụ tìm kiếm để trợ giúp.';
            break;
        case 'It looks like nothing was found at this location. Maybe try a search?':
            $translation = 'Dường như không có gì được tìm thấy trong đường dẫn này. Bạn có thể thử công cụ tìm kiếm?';
            break;
        case 'Oops! That page can&rsquo;t be found.':
            $translation = 'Xin lỗi! Trang này không được tìm thấy';
            break;
    }
    return $translation;
}
add_filter('gettext', 'hocwp_theme_translation_gettext', 10, 2);

function hocwp_theme_translation_gettext_with_context($translations, $text, $context, $domain = 'default') {
    switch($text) {
        case 'Search for:':
            $translations = 'Tìm kiếm cho:';
            break;
        case 'Search &hellip;':
            $translations = 'Từ khóa&hellip;';
            break;
        case 'Search':
            $translations = 'Tìm kiếm';
            break;
    }
    return $translations;
}
add_filter('gettext_with_context', 'hocwp_theme_translation_gettext_with_context', 10, 3);

function hocwp_theme_translation_ngettext($translation, $single, $plural, $number, $domain = 'default') {
    $translations = get_translations_for_domain($domain);
    $translation = $translations->translate_plural($single, $plural, $number);
    switch($translation) {
        case '%s second':
        case '%s seconds':
            $translation = '%s giây';
            break;
        case '%s min':
        case '%s mins':
        case '%s minute':
        case '%s minutes':
            $translation = '%s phút';
            break;
        case '%s hour':
        case '%s hours':
            $translation = '%s giờ';
            break;
        case '%s day':
        case '%s days':
            $translation = '%s ngày';
            break;
        case '%s week':
        case '%s weeks':
            $translation = '%s tuần';
            break;
        case '%s month':
        case '%s months':
            $translation = '%s tháng';
            break;
        case '%s year':
        case '%s years':
            $translation = '%s năm';
            break;

    }
    return $translation;
}
add_filter('ngettext', 'hocwp_theme_translation_ngettext', 10, 4);

function hocwp_theme_translation_comment_list_class($classes) {
    $classes[] = 'custom';
    return $classes;
}
add_filter('hocwp_comment_list_class', 'hocwp_theme_translation_comment_list_class');