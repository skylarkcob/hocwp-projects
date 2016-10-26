<div class="logo-area top-header">
	<div class="<?php hocwp_wrap_class(); ?>">
		<div class="row">
			<div class="col-sm-4 col-xs-12 logo">
				<?php hocwp_theme_the_logo(); ?>
			</div>
			<div class="col-sm-8 col-xs-12 right-logo">
				<?php hocwp_show_ads( 'leaderboard' ); ?>
			</div>
		</div>
	</div>
</div>
<div class="primary-menus">
	<div class="<?php hocwp_wrap_class(); ?>">
		<div class="row">
			<div class="col-xs-12">
				<?php hocwp_theme_the_menu( array( 'theme_location' => 'primary' ) ); ?>
			</div>
		</div>
	</div>
</div>