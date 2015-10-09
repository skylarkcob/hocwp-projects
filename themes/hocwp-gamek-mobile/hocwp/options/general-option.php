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
        $id = 'ra-4e8109ea4780ac8d';
    }
    $id = apply_filters('hocwp_addthis_id', $id);
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

$option_smtp = new HOCWP_Option(__('SMTP Email', 'hocwp'), 'hocwp_option_smtp_email');
$option_smtp->set_parent_slug($parent_slug);
$option_smtp->add_section(array('id' => 'smtp_option', 'title' => __('SMTP Options', 'hocwp'), 'description' => __('These options only apply if you have chosen to send mail by SMTP above.', 'hocwp')));
$option_smtp->add_section(array('id' => 'testing', 'title' => __('Configuration Testing', 'hocwp'), 'description' => __('If you do not feel very confident with the above configuration, you can send a test mail to know the results.', 'hocwp')));
$option_smtp->add_field(array('id' => 'mail_from', 'title' => __('From Email', 'hocwp'), 'description' => __('You can specify the email address that emails should be sent from. If you leave this blank, the default email will be used.', 'hocwp')));
$option_smtp->add_field(array('id' => 'mail_from_name', 'title' => __('From Name', 'hocwp'), 'description' => __('You can specify the name that emails should be sent from. If you leave this blank, the emails will be sent from WordPress.', 'hocwp')));
$field_options = array(
    array(
        'id' => 'mailer_smtp',
        'label' => __('Send all WordPress emails via SMTP.', 'hocwp'),
        'option_value' => 'smtp'
    ),
    array(
        'id' => 'mailer_mail',
        'label' => __('Use the PHP mail() function to send emails.', 'hocwp'),
        'option_value' => 'mail'
    )
);
$option_smtp->add_field(array('id' => 'mailer', 'title' => __('Mailer', 'hocwp'), 'field_callback' => 'hocwp_field_input_radio', 'options' => $field_options));
$field_options = array(
    array(
        'id' => 'mail_set_return_path',
        'label' => __('Set the return-path to match the From Email.', 'hocwp')
    )
);
$option_smtp->add_field(array('id' => 'mail_set_return_path', 'title' => __('Return Path', 'hocwp'), 'field_callback' => 'hocwp_field_input_checkbox', 'options' => $field_options));
$option_smtp->add_field(array('id' => 'smtp_host', 'title' => __('SMTP Host', 'hocwp'), 'default' => 'localhost', 'section' => 'smtp_option'));
$option_smtp->add_field(array('id' => 'smtp_port', 'title' => __('SMTP Port', 'hocwp'), 'default' => 25, 'section' => 'smtp_option'));
$field_options = array(
    array(
        'id' => 'smtp_ssl_none',
        'label' => __('No encryption.', 'hocwp'),
        'option_value' => 'none'
    ),
    array(
        'id' => 'smtp_ssl_ssl',
        'label' => __('Use SSL encryption.', 'hocwp'),
        'option_value' => 'ssl'
    ),
    array(
        'id' => 'smtp_ssl_tls',
        'label' => __('Use TLS encryption. This is not the same as STARTTLS. For most servers SSL is the recommended option.', 'hocwp'),
        'option_value' => 'tls'
    )
);
$option_smtp->add_field(array('id' => 'smtp_ssl', 'title' => __('Encryption', 'hocwp'), 'field_callback' => 'hocwp_field_input_radio', 'options' => $field_options, 'section' => 'smtp_option'));
$field_options = array(
    array(
        'id' => 'smtp_auth_true',
        'label' => __('Yes: Use SMTP authentication.', 'hocwp'),
        'option_value' => 'true'
    ),
    array(
        'id' => 'smtp_auth_false',
        'label' => __('No: Do not use SMTP authentication.', 'hocwp'),
        'option_value' => 'false'
    )
);
$option_smtp->add_field(array('id' => 'smtp_auth', 'title' => __('Authentication', 'hocwp'), 'field_callback' => 'hocwp_field_input_radio', 'options' => $field_options, 'section' => 'smtp_option'));
$option_smtp->add_field(array('id' => 'smtp_user', 'title' => __('Username', 'hocwp'), 'section' => 'smtp_option'));
$option_smtp->add_field(array('id' => 'smtp_pass', 'title' => __('Password', 'hocwp'), 'section' => 'smtp_option', 'type' => 'password'));
$option_smtp->add_field(array('id' => 'to_email', 'title' => __('To', 'hocwp'), 'section' => 'testing', 'description' => __('Type an email address here and then click Send Test to generate a test email.', 'hocwp'), 'type' => 'email'));
$option_smtp->init();
hocwp_option_add_object_to_list($option_smtp);

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