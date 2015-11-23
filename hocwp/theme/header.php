<?php
if(!function_exists('add_filter')) exit;
do_action('hocwp_before_doctype');
$maintenance_mode = hocwp_in_maintenance_mode();
?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <?php
    do_action('hocwp_before_wp_head');
    wp_head();
    do_action('hocwp_after_wp_head');
    do_action('hocwp_maintenance_head');
    ?>
</head>
<body <?php body_class(); ?>>
<?php
do_action('hocwp_open_body');
do_action('hocwp_before_site');
?>
<div id="page" class="hfeed site">
    <div class="site-inner">
        <?php if(!$maintenance_mode) : ?>
            <?php do_action('hocwp_before_site_header'); ?>
            <header id="masthead" class="site-header" role="banner">
                <?php hocwp_theme_get_template('header'); ?>
            </header><!-- .site-header -->
            <?php do_action('hocwp_after_site_header'); ?>
        <?php endif; ?>
        <?php do_action('hocwp_before_site_content'); ?>
        <div id="content" class="site-content">