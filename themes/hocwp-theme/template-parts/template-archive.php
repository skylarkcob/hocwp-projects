<div class="<?php hocwp_wrap_class(); ?>">
	<?php
	hocwp_breadcrumb();
	hocwp_theme_site_main_before();
	hocwp_the_archive_title();
	if ( have_posts() ) {
		echo '<div class="loop-posts">';
		while ( have_posts() ) {
			the_post();
			hocwp_theme_get_loop( 'post' );
		}
		echo '</div>';
		hocwp_pagination();
	} else {
		hocwp_theme_get_content_none();
	}
	hocwp_theme_site_main_after();
	get_sidebar( 'secondary' );
	?>
</div>