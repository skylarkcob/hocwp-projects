<div class="footer-menus">
    <div class="container">
        <?php hocwp_theme_the_menu(array('theme_location' => 'footer')); ?>
    </div>
</div>
<div class="footer-info">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 left-info">
                <?php echo wpautop(hocwp_theme_get_option('footer_left_text')); ?>
            </div>
            <div class="col-sm-6 right-info">
                <?php echo wpautop(hocwp_theme_get_option('footer_right_text')); ?>
            </div>
        </div>
    </div>
</div>