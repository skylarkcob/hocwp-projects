window.wp = window.wp || {};
window.hocwp = window.hocwp || {};

(function($) {
    (function() {
        $('.sf-menu, .hocwp-superfish-menu > ul').each(function() {
            var $element = $(this),
                options = {
                    hoverClass: 'sf-hover',
                    delay: 100,
                    cssArrows: false,
                    dropShadows: false
                };
            if(!$element.hasClass('sf-menu')) {
                $element.addClass('sf-menu');
            }
            if($element.hasClass('slide')) {
                options.animation = {
                    height: 'show',
                    marginTop: 'show',
                    marginBottom: 'show',
                    paddingTop: 'show',
                    paddingBottom: 'show'
                };
                options.animationOut = {
                    height: 'hide',
                    marginTop: 'hide',
                    marginBottom: 'hide',
                    paddingTop: 'hide',
                    paddingBottom: 'hide'
                };
            }
            if($element.hasClass('arrow')) {
                options.cssArrows = true;
            }
            $element.superfish(options);
        });
    })();

    (function() {
        $('.hocwp-go-top').hocwpScrollTop();
    })();

    (function() {
        $('.hocwp .comment-tools .comment-likes').on('click', function(e) {
            e.preventDefault();
            var $element = $(this),
                $container = $element.closest('.comment'),
                $count = $element.find('.count'),
                comment_id = parseInt($container.attr('data-comment-id')),
                likes = parseInt($element.attr('data-likes'));
            $element.addClass('disabled');
            $element.css({'text-decoration' : 'none'});
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                data: {
                    action: 'hocwp_comment_likes',
                    comment_id: comment_id,
                    likes: likes
                },
                success: function(response){
                    likes++;
                    $element.attr('data-likes', likes);
                    $count.html(response.likes);
                }
            });
        });

        $('.hocwp .comment-tools .comment-report').on('click', function(e) {
            e.preventDefault();
            var $element = $(this),
                $container = $element.closest('.comment'),
                comment_id = parseInt($container.attr('data-comment-id'));
            $element.addClass('disabled');
            $element.css({'text-decoration' : 'none'});
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                data: {
                    action: 'hocwp_comment_report',
                    comment_id: comment_id
                },
                success: function(response){

                }
            });
        });

        $('.hocwp .comment-tools .comment-share').on('click', function(e) {
            e.preventDefault();
            var $element = $(this),
                $container = $element.closest('.comment'),
                $list_share = $element.find('.list-share'),
                comment_id = parseInt($container.attr('data-comment-id'));
            $element.css({'text-decoration' : 'none'});
            $element.toggleClass('active');
        });

        $('.hocwp .comment-tools .comment-share .list-share .fa').on('click', function(e) {
            e.preventDefault();
            var $element = $(this),
                $container = $element.closest('.comment'),
                $list_share = $element.find('.list-share'),
                comment_id = parseInt($container.attr('data-comment-id'));
            $element.css({'text-decoration' : 'none'});
            window.open($element.attr('data-url'), 'ShareWindow', 'height=450, width=550, toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
        });
    })();

    (function() {
        var $icon_refresh_captcha = $('img.hocwp-captcha-reload'),
            $captcha_image = $('img.hocwp-captcha-image');
        if(!$captcha_image.length) {
            return false;
        }
        $captcha_image.css({'cursor' : 'text'});
        $icon_refresh_captcha.css({'opacity' : '0.75'});
        $icon_refresh_captcha.on('mouseover', function(e) {
            e.preventDefault();
            $(this).css({'opacity' : '1'});
        });
        $icon_refresh_captcha.on('mouseleave', function(e) {
            e.preventDefault();
            $(this).$(this).css({'opacity' : '0.75'});
        });
        $icon_refresh_captcha.on('click', function(e) {
            e.preventDefault();
            var $element = $(this),
                $container = $element.parent(),
                $input = $container.find('input.hocwp-captcha-code'),
                $image = $container.find('img.hocwp-captcha-image');
            $element.css({'opacity' : '0.25', 'pointer-events' : 'none'});
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                data: {
                    action: 'hocwp_change_captcha_image'
                },
                success: function(response){
                    if(response.success) {
                        $image.attr('src', response.captcha_image_url);
                    } else {
                        alert(response.message);
                    }
                    $element.css({'opacity' : '0.75', 'pointer-events' : 'inherit'});
                }
            });
        });
    })();
})(jQuery);