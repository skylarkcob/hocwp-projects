<?php
$anchor_bottom = apply_filters('hocwp_fixed_widget_anchor_bottom', '');
$bottom_spacing = apply_filters('hocwp_fixed_widget_bottom_spacing', 0);
?>
<script>
    (function($) {
        var $wpadminbar = $('#wpadminbar'),
            $site_footer = $('.site-footer'),
            $anchor_bottom = $('<?php echo $anchor_bottom; ?>'),
            custom_bottom_spacing = parseInt(<?php echo $bottom_spacing; ?>),
            top_spacing = 0,
            bottom_spacing = 0;
        if($wpadminbar.length) {
            top_spacing = $wpadminbar.height();
        }
        if($site_footer.length) {
            bottom_spacing = $site_footer.height();
            bottom_spacing += parseInt($site_footer.css('margin-top').replace('px', ''));
        }
        if($anchor_bottom.length) {
            bottom_spacing += $anchor_bottom.height();
        }
        if($.isNumeric(custom_bottom_spacing) && custom_bottom_spacing > 0) {
            bottom_spacing += custom_bottom_spacing;
        }
        $('.hocwp .sidebar .widget:last').sticky({
            topSpacing: top_spacing,
            bottomSpacing: bottom_spacing
        });
    })(jQuery);
</script>