<div class="<?php hocwp_wrap_class(); ?>">
	<?php
	hocwp_theme_site_main_before();
	hocwp_article_before();
	?>
	<nav id="image-navigation" class="navigation image-navigation">
		<div class="nav-links">
			<div class="nav-previous">
				<?php previous_image_link( false, __( 'Previous Image', 'hocwp-theme' ) ); ?>
			</div>
			<div class="nav-next">
				<?php next_image_link( false, __( 'Next Image', 'hocwp-theme' ) ); ?>
			</div>
		</div>
	</nav>
	<header class="entry-header">
		<?php hocwp_post_title_single(); ?>
	</header>
	<div class="entry-content">
		<div class="entry-attachment">
			<?php
			$image_size = apply_filters( 'hocwp_attachment_size', 'full' );
			echo wp_get_attachment_image( get_the_ID(), $image_size );
			hocwp_entry_summary( '', 'entry-caption' );
			?>
		</div>
		<?php
		the_content();
		wp_link_pages( array(
			'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'hocwp-theme' ) . '</span>',
			'after'       => '</div>',
			'link_before' => '<span>',
			'link_after'  => '</span>',
			'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'hocwp-theme' ) . ' </span>%',
			'separator'   => '<span class="screen-reader-text">, </span>',
		) );
		?>
	</div>
	<footer class="entry-footer">
		<div class="entry-meta">
			<?php hocwp_entry_date(); ?>
		</div>
		<?php
		$metadata = wp_get_attachment_metadata();
		if ( $metadata ) {
			printf( '<span class="full-size-link"><span class="screen-reader-text">%1$s </span><a href="%2$s">%3$s &times; %4$s</a></span>',
				esc_html_x( 'Full size', 'Used before full size attachment link.', 'hocwp-theme' ),
				esc_url( wp_get_attachment_url() ),
				absint( $metadata['width'] ),
				absint( $metadata['height'] )
			);
		}
		?>
		<?php
		edit_post_link(
			sprintf(
				__( 'Edit <span class="screen-reader-text"> "%s"</span>', 'hocwp-theme' ),
				get_the_title()
			),
			'<span class="edit-link">',
			'</span>'
		);
		?>
	</footer>
	<?php
	hocwp_article_after();
	hocwp_comments_template();
	the_post_navigation( array(
		'prev_text' => _x( '<span class="meta-nav">Published in</span> <span class="post-title">%title</span>', 'Parent post link', 'hocwp-theme' ),
	) );
	hocwp_theme_site_main_after();
	get_sidebar( 'secondary' );
	?>
</div>