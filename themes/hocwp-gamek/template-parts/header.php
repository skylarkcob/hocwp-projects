<?php
$logo = hocwp_theme_get_option('mobile_logo');
$logo = hocwp_sanitize_media_value($logo);
$logo_text = get_bloginfo('name');
if(empty($logo['url'])) {
    $logo = hocwp_theme_get_option('logo');
    $logo = hocwp_sanitize_media_value($logo);
}
if(!empty($logo['url'])) {
    $logo_text = '<img src="' . $logo['url'] . '">';
}
$search_page = hocwp_theme_get_option('search_page');
if($search_page > 0) {
    $search_page = get_permalink($search_page);
}
?>
<div class="mobile-menus-bar">
    <ul class="list-inline list-unstyled clearfix">
        <li class="text-center logo">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php echo $logo_text; ?></a>
        </li>
        <li class="text-center menu-control">
            <i class="fa fa-bars"></i>
            <span class="text">Mở rộng</span>
        </li>
        <li class="text-center">
            <a href="<?php echo $search_page; ?>"><i class="fa fa-search"></i><span class="text">Tìm kiếm</span></a>
        </li>
    </ul>
</div>
<div class="mobile-menus">
    <?php hocwp_theme_the_menu(array('theme_location' => 'mobile')); ?>
</div>
<div class="container">
    <div class="top-socials clearfix">
        <?php
        $facebook = hocwp_get_wpseo_social_value('facebook_site');
        $gplus = hocwp_get_wpseo_social_value('google_plus_url');
        $youtube = hocwp_get_wpseo_social_value('youtube_url');
        $rss = hocwp_theme_get_option('rss_url', 'option_social');
        ?>
        <ul class="list-socials list-inline list-unstyled pull-right">
            <?php
            $item = $facebook;
            if(!empty($item)) : ?>
                <li><a href="<?php echo $item; ?>"><i class="fa fa-facebook"></i></a></li>
            <?php endif; ?>
            <?php
            $item = $gplus;
            if(!empty($item)) : ?>
                <li><a href="<?php echo $item; ?>"><i class="fa fa-google-plus"></i></a></li>
            <?php endif; ?>
            <?php
            $item = $youtube;
            if(!empty($item)) : ?>
                <li><a href="<?php echo $item; ?>"><i class="fa fa-youtube"></i></a></li>
            <?php endif; ?>
            <?php
            $item = $rss;
            if(!empty($item)) : ?>
                <li><a href="<?php echo $item; ?>"><i class="fa fa-rss"></i></a></li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="primary-menus clearfix">
        <?php hocwp_theme_the_menu(array('theme_location' => 'primary')); ?>
    </div>
    <div class="secondary-menus clearfix">
        <?php hocwp_theme_the_menu(array('theme_location' => 'secondary')); ?>
    </div>
</div>
<div class="top-banner one-widget">
    <div class="container">
        <?php dynamic_sidebar('home_top_banner'); ?>
    </div>
</div>