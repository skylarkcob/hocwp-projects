(function($) {
    (function() {
        $('.column-content_captcha .icon-circle').on('click', function(e) {
            e.preventDefault();
            var $element = $(this);
            $element.css({'opacity' : '0.25'});
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                data: {
                    action: 'hocwp_content_captcha',
                    post_id: $element.attr('data-id'),
                    content_captcha: $element.attr('data-content-captcha')
                },
                success: function(response){
                    if(response.success) {
                        $element.toggleClass('icon-circle-success');
                    }
                    $element.css({'opacity' : '1'});
                }
            });
        });
    })();
})(jQuery);