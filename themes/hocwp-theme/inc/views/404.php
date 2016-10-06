<div class="<?php hocwp_wrap_class(); ?>">
	<div class="row">
		<?php
		do_action( 'hocwp_404_site_content_inner_before' );
		hocwp_theme_site_main_before();
		?>
		<section class="error-404 not-found">
			<header class="page-header">
				<h1 class="page-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'hocwp-theme' ); ?></h1>
			</header>
			<!-- .page-header -->
			<div class="page-content">
				<p><?php _e( 'It looks like nothing was found at this location. Maybe try a search?', 'hocwp-theme' ); ?></p>
				<?php get_search_form(); ?>
			</div>
			<!-- .page-content -->
		</section>
		<!-- .error-404 -->
		<?php
		hocwp_theme_site_main_after();
		get_sidebar( '404' );
		do_action( 'hocwp_404_site_content_inner_after' );
		?>
	</div>
</div>