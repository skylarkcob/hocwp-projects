<div class="<?php hocwp_wrap_class(); ?>">
	<?php
	hocwp_breadcrumb();
	hocwp_theme_site_main_before();
	hocwp_article_before();
	hocwp_post_title_single();
	hocwp_entry_content();
	hocwp_article_after();
	hocwp_theme_site_main_after();
	get_sidebar( 'page' );
	?>
</div>