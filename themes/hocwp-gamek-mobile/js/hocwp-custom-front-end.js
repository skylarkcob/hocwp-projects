window.wp = window.wp || {};
window.hocwp = window.hocwp || {};

(function($) {
    (function() {
        $('.home .paging a').on('click', function(e) {
            e.preventDefault();
            var $element = $(this),
                $container = $element.parent(),
                $list_posts = $container.prev(),
                $last_li_item = $list_posts.find('li:last'),
                paged = parseInt($container.attr('data-paged'));
            $('.ajax-loading.full-page').toggleClass('active');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                data: {
                    action: 'hocwp_load_more_home_post',
                    offset: parseInt($container.attr('data-offset')),
                    paged: paged,
                    posts_per_page: parseInt($container.attr('data-posts-per-page')),
                    query_vars: JSON.stringify($container.attr('data-query-vars'))
                },
                success: function(response){
                    if(response.have_posts) {
                        $list_posts.append(response.html);
                        $(window).trigger('scroll');
                        hocwp.scrollToPosition($last_li_item.offset().top + $last_li_item.height() - 10, 100);
                    }
                    if(!response.more) {
                        $container.hide();
                    } else {
                        $container.attr('data-paged', response.paged);
                        $container.attr('data-offset', response.offset);
                    }
                    $('.ajax-loading.full-page').toggleClass('active');
                    $container.attr('data-query-vars', response.query_vars);
                }
            });
        });

        $('.archive .paging a').on('click', function(e) {
            e.preventDefault();
            var $element = $(this),
                $container = $element.parent(),
                $list_posts = $('.archive .site-main .loop-default'),
                $last_li_item = $list_posts.find('li:last');
            $('.ajax-loading.full-page').toggleClass('active');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                data: {
                    action: 'hocwp_load_more_archive_post',
                    query_vars: $container.attr('data-query-vars')
                },
                success: function(response){
                    if(response.have_posts) {
                        $list_posts.append(response.html);
                        $(window).trigger('scroll');
                        hocwp.scrollToPosition($last_li_item.offset().top + $last_li_item.height() - 10, 100);
                    }
                    if(!response.more) {
                        $container.hide();
                    }
                    $('.ajax-loading.full-page').toggleClass('active');
                    $container.attr('data-query-vars', JSON.stringify(response.query_vars));
                }
            });
        });
    })();

    (function() {
        $('.video-box .view-more').on('click', function(e) {
            e.preventDefault();
            var $element = $(this),
                $video_box = $element.closest('.video-box'),
                $list = $video_box.find('.list'),
                query_vars = $element.attr('data-query-vars');
            $('.ajax-loading.full-page').toggleClass('active');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                data: {
                    action: 'hocwp_load_more_video_box',
                    query_vars: query_vars
                },
                success: function(response){
                    if(response.have_posts) {
                        $list.html(response.html);
                    }
                    if(!response.more) {
                        $element.hide();
                    }
                    $('.ajax-loading.full-page').toggleClass('active');
                    $element.attr('data-query-vars', JSON.stringify(response.query_vars));
                }
            });
        });
    })();

    (function() {
        var $video_player = $('.video-box .videotructiep iframe');
        if($video_player.length) {
            $video_player.attr('src', $video_player.attr('src') + '&autoplay=1');
        }
        $('.page-template-video .hocwp-post').live('click', function(e) {
            e.preventDefault();
            var $element = $(this),
                post_id = $element.attr('data-id');
            $('.ajax-loading.full-page').toggleClass('active');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                data: {
                    action: 'hocwp_play_video_change',
                    post_id: post_id
                },
                success: function() {
                    $('.ajax-loading.full-page').toggleClass('active');
                    window.location.reload();
                }
            });
        });
    })();

    (function() {
        $('.mobile-menus-bar .menu-control').on('click', function(e) {
            e.preventDefault();
            var $element = $(this),
                $text = $element.find('.text'),
                $site_header = $('.site-header'),
                $page = $('#page');
            $site_header.toggleClass('menu-open');
            $page.toggleClass('menu-open');
            $element.toggleClass('active');
            if($element.hasClass('active')) {
                $text.html('Quay lại');
            } else {
                $text.html('Mở rộng');
            }
        });
    })();
})(jQuery);