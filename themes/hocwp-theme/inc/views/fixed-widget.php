<script>
    (function($) {
        var $wpadminbar = $('#wpadminbar'),
            $site_footer = $('.site-footer'),
            top_spacing = 0,
            bottom_spacing = 0;
        if($wpadminbar.length) {
            top_spacing = $wpadminbar.height();
        }
        if($site_footer.length) {
            bottom_spacing = $site_footer.height();
            bottom_spacing += parseInt($site_footer.css('margin-top').replace('px', ''));
        }
        $('.hocwp .sidebar .widget:last').sticky({
            topSpacing: top_spacing,
            bottomSpacing: bottom_spacing
        });
    })(jQuery);
</script>