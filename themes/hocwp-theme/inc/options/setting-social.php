<?php
if(!function_exists('add_filter')) exit;

global $hocwp_tos_tabs;
$parent_slug = 'hocwp_theme_option';

$option = new HOCWP_Option(__('Socials', 'hocwp-theme'), 'hocwp_option_social');
$option->set_parent_slug($parent_slug);
$option->set_use_style_and_script(true);
$option->add_section(array('id' => 'account', 'title' => __('Account', 'hocwp-theme'), 'description' => __('Your social accounts to config API on website.', 'hocwp-theme')));
$option->add_section(array('id' => 'facebook', 'title' => __('Facebook', 'hocwp-theme'), 'description' => __('All information about Facebook account and Facebook Insights Admins.', 'hocwp-theme')));
$option->add_section(array('id' => 'google', 'title' => __('Google', 'hocwp-theme'), 'description' => __('All information about Google account and Google console.', 'hocwp-theme')));
$option->add_field(array('id' => 'facebook_site', 'title' => __('Facebook page URL', 'hocwp-theme'), 'value' => hocwp_get_wpseo_social_value('facebook_site')));
$twitter_account = hocwp_get_wpseo_social_value('twitter_site');
if(!empty($twitter_account) && !hocwp_url_valid($twitter_account)) {
	$twitter_account = 'http://twitter.com/' . $twitter_account;
}
$option->add_field(array('id' => 'twitter_site', 'title' => __('Twitter URL', 'hocwp-theme'), 'value' => $twitter_account));
$option->add_field(array('id' => 'instagram_url', 'title' => __('Instagram URL', 'hocwp-theme'), 'value' => hocwp_get_wpseo_social_value('instagram_url')));
$option->add_field(array('id' => 'linkedin_url', 'title' => __('LinkedIn URL', 'hocwp-theme'), 'value' => hocwp_get_wpseo_social_value('linkedin_url')));
$option->add_field(array('id' => 'myspace_url', 'title' => __('Myspace URL', 'hocwp-theme'), 'value' => hocwp_get_wpseo_social_value('myspace_url')));
$option->add_field(array('id' => 'pinterest_url', 'title' => __('Pinterest URL', 'hocwp-theme'), 'value' => hocwp_get_wpseo_social_value('pinterest_url')));
$option->add_field(array('id' => 'youtube_url', 'title' => __('YouTube URL', 'hocwp-theme'), 'value' => hocwp_get_wpseo_social_value('youtube_url')));
$option->add_field(array('id' => 'google_plus_url', 'title' => __('Google+ URL', 'hocwp-theme'), 'value' => hocwp_get_wpseo_social_value('google_plus_url')));
$option->add_field(array('id' => 'rss_url', 'title' => __('RSS URL', 'hocwp-theme')));
$option->add_field(array('id' => 'addthis_id', 'title' => __('AddThis ID', 'hocwp-theme'), 'section' => 'account'));
$option->add_field(array('id' => 'fbadminapp', 'title' => __('Facebook App ID', 'hocwp-theme'), 'section' => 'facebook', 'value' => hocwp_get_wpseo_social_value('fbadminapp')));
$option->add_field(array('id' => 'google_api_key', 'title' => __('Google API Key', 'hocwp-theme'), 'section' => 'google'));
$option->add_field(array('id' => 'google_client_id', 'title' => __('Google Client ID', 'hocwp-theme'), 'section' => 'google'));

$option->add_option_tab($hocwp_tos_tabs);
$option->set_page_header_callback('hocwp_theme_option_form_before');
$option->set_page_footer_callback('hocwp_theme_option_form_after');
$option->set_page_sidebar_callback('hocwp_theme_option_sidebar_tab');
$option->init();
hocwp_option_add_object_to_list($option);

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
add_action('hocwp_sanitize_' . $option->get_option_name_no_prefix() . '_option', 'hocwp_option_social_update');

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
	hocwp_add_string_with_space_before($class, 'addthis-tools');
	$url = isset($args['url']) ? $args['url'] : get_the_permalink();
	$title = isset($args['title']) ? $args['title'] : get_the_title();
	?>
	<!-- Go to www.addthis.com/dashboard to customize your tools -->
	<div class="<?php echo $class; ?>" data-url="<?php echo $url; ?>" data-title="<?php echo hocwp_wpseo_get_post_title($post_id); ?>"></div>
	<?php
}