<?php
$parent_slug = 'options-general.php';

$option_social = new HOCWP_Option(__('Socials', 'hocwp'), 'hocwp_option_social');
$option_social->set_parent_slug($parent_slug);
$option_social->add_section(array('id' => 'account', 'title' => __('Account', 'hocwp'), 'description' => __('Your social accounts to config API on website.', 'hocwp')));
$option_social->add_section(array('id' => 'facebook', 'title' => __('Facebook', 'hocwp'), 'description' => __('All information about Facebook account and Facebook Insights Admins.', 'hocwp')));
$option_social->add_field(array('id' => 'facebook_site', 'title' => __('Facebook page URL', 'hocwp'), 'value' => hocwp_get_wpseo_social_value('facebook_site')));
$twitter_account = hocwp_get_wpseo_social_value('twitter_site');
if(!empty($twitter_account) && !hocwp_url_valid($twitter_account)) {
    $twitter_account = 'http://twitter.com/' . $twitter_account;
}
$option_social->add_field(array('id' => 'twitter_site', 'title' => __('Twitter URL', 'hocwp'), 'value' => $twitter_account));
$option_social->add_field(array('id' => 'instagram_url', 'title' => __('Instagram URL', 'hocwp'), 'value' => hocwp_get_wpseo_social_value('instagram_url')));
$option_social->add_field(array('id' => 'linkedin_url', 'title' => __('LinkedIn URL', 'hocwp'), 'value' => hocwp_get_wpseo_social_value('linkedin_url')));
$option_social->add_field(array('id' => 'myspace_url', 'title' => __('Myspace URL', 'hocwp'), 'value' => hocwp_get_wpseo_social_value('myspace_url')));
$option_social->add_field(array('id' => 'pinterest_url', 'title' => __('Pinterest URL', 'hocwp'), 'value' => hocwp_get_wpseo_social_value('pinterest_url')));
$option_social->add_field(array('id' => 'youtube_url', 'title' => __('YouTube URL', 'hocwp'), 'value' => hocwp_get_wpseo_social_value('youtube_url')));
$option_social->add_field(array('id' => 'google_plus_url', 'title' => __('Google+ URL', 'hocwp'), 'value' => hocwp_get_wpseo_social_value('google_plus_url')));
$option_social->add_field(array('id' => 'rss_url', 'title' => __('RSS URL', 'hocwp')));
$option_social->add_field(array('id' => 'addthis_id', 'title' => __('AddThis ID', 'hocwp'), 'section' => 'account'));
$option_social->add_field(array('id' => 'fbadminapp', 'title' => __('Facebook App ID', 'hocwp'), 'section' => 'facebook', 'value' => hocwp_get_wpseo_social_value('fbadminapp')));
$option_social->init();
hocwp_option_add_object_to_list($option_social);

function hocwp_option_social_update($input) {
    $key = 'facebook_site';
    if(isset($input[$key])) {
        hocwp_update_wpseo_social($key, $input[$key]);
    }
    $key = 'twitter_site';
    if(isset($input[$key])) {
        hocwp_update_wpseo_social($key, $input[$key]);
    }
    $key = 'instagram_url';
    if(isset($input[$key])) {
        hocwp_update_wpseo_social($key, $input[$key]);
    }
    $key = 'linkedin_url';
    if(isset($input[$key])) {
        hocwp_update_wpseo_social($key, $input[$key]);
    }
    $key = 'myspace_url';
    if(isset($input[$key])) {
        hocwp_update_wpseo_social($key, $input[$key]);
    }
    $key = 'pinterest_url';
    if(isset($input[$key])) {
        hocwp_update_wpseo_social($key, $input[$key]);
    }
    $key = 'youtube_url';
    if(isset($input[$key])) {
        hocwp_update_wpseo_social($key, $input[$key]);
    }
    $key = 'google_plus_url';
    if(isset($input[$key])) {
        hocwp_update_wpseo_social($key, $input[$key]);
    }
    $key = 'fbadminapp';
    if(isset($input[$key])) {
        hocwp_update_wpseo_social($key, $input[$key]);
    }
}
add_action('hocwp_sanitize_' . $option_social->get_option_name_no_prefix() . '_option', 'hocwp_option_social_update');

function hocwp_addthis_script($args = array()) {
    $id = isset($args['id']) ? $args['id'] : '';
    if(empty($id)) {
        $id = hocwp_option_get_value('option_social', 'addthis_id');
    }
    if(empty($id)) {
        $use_default_addthis_id = apply_filters('hocwp_use_default_addthis_id', false);
        if($use_default_addthis_id) {
            $id = 'ra-4e8109ea4780ac8d';
        }
    }
    $id = apply_filters('hocwp_addthis_id', $id);
    if(empty($id)) {
        return;
    }
    ?>
    <!-- Go to www.addthis.com/dashboard to customize your tools -->
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo $id; ?>" async="async"></script>
    <?php
}

function hocwp_addthis_toolbox($args = array()) {
    $post_id = isset($args['post_id']) ? $args['post_id'] : get_the_ID();
    $class = isset($args['class']) ? $args['class'] : 'addthis_native_toolbox';
    $class = apply_filters('hocwp_addthis_toolbox_class', $class);
    $url = isset($args['url']) ? $args['url'] : get_the_permalink();
    $title = isset($args['title']) ? $args['title'] : get_the_title();
    ?>
    <!-- Go to www.addthis.com/dashboard to customize your tools -->
    <div class="<?php echo $class; ?>" data-url="<?php echo $url; ?>" data-title="<?php echo hocwp_wpseo_get_post_title($post_id); ?>"></div>
    <?php
}

hocwp_add_option_page_smtp_email($parent_slug);

function hocwp_sanitize_option_smtp_mail($input) {
    if(isset($input['to_email'])) {
        if(is_email($input['to_email'])) {
            set_transient('hocwp_test_smtp_email', $input['to_email']);
        }
    }
    unset($input['to_email']);
    return $input;
}
add_filter('hocwp_sanitize_option_' . $option_smtp->get_option_name_no_prefix(), 'hocwp_sanitize_option_smtp_mail');

function hocwp_option_smtp_mail_update($input) {
    return $input;
}
add_action('hocwp_sanitize_' . $option_smtp->get_option_name_no_prefix() . '_option', 'hocwp_option_smtp_mail_update');

function hocwp_option_smtp_email_testing() {
    if(false !== ($email = get_transient('hocwp_test_smtp_email'))) {
        if(is_email($email)) {
            unset($_GET['settings-updated']);
            $test_message = hocwp_mail_test_smtp_setting($email);
            set_transient('hocwp_test_smtp_email_message', $test_message);
            delete_transient('hocwp_test_smtp_email');
            add_action('admin_notices', 'hocwp_option_smtp_email_testing_message');
            unset($phpmailer);
        }
    }
}
add_action('admin_init', 'hocwp_option_smtp_email_testing');

function hocwp_option_smtp_email_testing_message() {
    if(false !== ($message = get_transient('hocwp_test_smtp_email_message'))) {
        hocwp_admin_notice(array('text' => $message));
        delete_transient('hocwp_test_smtp_email_message');
    }
}

$writing_option = new HOCWP_Option('', 'writing');
$writing_option->set_page('options-writing.php');
$writing_option->add_field(array('id' => 'default_post_thumbnail', 'title' => __('Default post thumbnail', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload'));
$writing_option->init();
hocwp_option_add_object_to_list($writing_option);

$reading_option = new HOCWP_Option('', 'reading');
$reading_option->set_page('options-reading.php');
$reading_option->add_field(array('id' => 'post_statistics', 'title' => __('Post Statistics', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'label' => __('Track post views on your site.', 'hocwp')));
$reading_option->add_section(array('id' => 'scroll_top_section', 'title' => __('Scroll To Top', 'hocwp'), 'description' => __('This option can help you to display scroll to top button on your site.', 'hocwp')));
$reading_option->add_field(array('id' => 'go_to_top', 'title' => __('Scroll Top Button', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'label' => __('Display scroll top to top button on bottom right of site.', 'hocwp'), 'section' => 'scroll_top_section'));
$reading_option->add_field(array('id' => 'scroll_top_icon', 'title' => __('Button Icon', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload', 'section' => 'scroll_top_section'));
$reading_option->init();
hocwp_option_add_object_to_list($reading_option);

$discussion_option = new HOCWP_Option('', 'discussion');
$discussion_option->set_page('options-discussion.php');
$discussion_option->add_section(array('id' => 'comment_form', 'title' => __('Comment Form', 'hocwp'), 'description' => __('These options can help you to custom comment form on your site.', 'hocwp')));
$field_options = array(
    array(
        'id' => 'comment_system_default',
        'label' => __('Use WordPress default comment system.', 'hocwp'),
        'option_value' => 'default'
    ),
    array(
        'id' => 'comment_system_facebook',
        'label' => __('Use Facebook comment system.', 'hocwp'),
        'option_value' => 'facebook'
    ),
    array(
        'id' => 'comment_system_default_and_facebook',
        'label' => __('Display bold WordPress default comment system and Facebook comment system.', 'hocwp'),
        'option_value' => 'default_and_facebook'
    )
);
$discussion_option->add_field(array('id' => 'comment_system', 'title' => __('Comment System', 'hocwp'), 'field_callback' => 'hocwp_field_input_radio', 'options' => $field_options, 'section' => 'comment_form'));
$discussion_option->init();
hocwp_option_add_object_to_list($discussion_option);