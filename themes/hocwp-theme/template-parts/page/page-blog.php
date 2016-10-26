<div class="<?php hocwp_wrap_class(); ?>">
	<?php
	hocwp_breadcrumb();
	hocwp_theme_site_main_before();
	if ( is_page() ) {
		echo hocwp_wrap_tag( get_the_title(), 'h1', 'page-title entry-title' );
	}
	$args  = array(
		'post_type' => 'post',
		'paged'     => hocwp_get_paged()
	);
	$query = hocwp_query( $args );
	if ( $query->have_posts() ) {
		echo '<div class="loop-posts">';
		while ( $query->have_posts() ) {
			$query->the_post();
			hocwp_theme_get_loop( 'post' );
		}
		wp_reset_postdata();
		echo '</div>';
		hocwp_pagination( array( 'query' => $query ) );
	} else {
		hocwp_theme_get_content_none();
	}
	hocwp_theme_site_main_after();
	get_sidebar( 'secondary' );
	?>
</div>