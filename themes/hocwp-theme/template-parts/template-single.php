<div class="<?php hocwp_wrap_class(); ?>">
	<?php
	hocwp_breadcrumb();
	hocwp_theme_site_main_before();
	hocwp_article_before();
	hocwp_post_title_single();
	hocwp_entry_meta();
	hocwp_entry_content();
	hocwp_addthis_toolbox();
	hocwp_comments_template();
	hocwp_entry_tags();
	$args  = array(
		'posts_per_page' => 5,
		'post_type'      => 'post'
	);
	$query = hocwp_query_related_post( $args );
	if ( $query->have_posts() ) {
		?>
		<div class="module related">
			<div class="module-header cross">
				<h3><?php _e( 'Related Posts', 'hocwp-theme' ); ?></h3>
			</div>
			<div class="module-body">
				<?php
				echo '<div class="loop-posts">';
				while ( $query->have_posts() ) {
					$query->the_post();
					hocwp_theme_get_loop( 'post' );
				}
				wp_reset_postdata();
				echo '</div>';
				?>
			</div>
		</div>
		<?php
	}
	hocwp_article_after();
	hocwp_theme_site_main_after();
	get_sidebar( 'secondary' );
	?>
</div>