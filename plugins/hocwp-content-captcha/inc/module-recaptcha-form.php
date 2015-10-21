<?php
$data = hocwp_option_get_data('content_captcha');
$site_key = hocwp_get_value_by_key($data, 'site_key');
$secret_key = hocwp_get_value_by_key($data, 'secret_key');
?>
<form action="" method="POST">
	<?php hocwp_field_recaptcha(array('site_key' => $site_key, 'id' => 'hocwp_recaptcha_' . get_the_ID())); ?>
	<br/>
	<input type="hidden" name="recaptcha_post_id" value="<?php the_ID(); ?>">
	<input type="submit" value="Submit">
</form>