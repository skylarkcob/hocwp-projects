window.wp = window.wp || {};
window.hocwp = window.hocwp || {};

(function($) {
    (function() {
        $(document).on('widget-updated', function(e, widget) {
            $(this).find('.btn-insert-media').live('click', function(e) {
                e.preventDefault();
                hocwp.mediaUpload($(this));
            });
            $(this).find('.btn-remove').live('click', function(e) {
                e.preventDefault();
                var $container = $(this).parent();
                hocwp.mediaUpload($container.find('.btn-insert-media'), {remove: true});
            });
            $(this).find('input.media-url').live('change input', function(e) {
                e.preventDefault();
                var $container = $(this).parent();
                hocwp.mediaUpload($container.find('.btn-insert-media'), {change: true});
            });
        });
        $('div.widgets-sortables').bind('sortreceive', function(event, ui) {
            $(this).find('.btn-insert-media').live('click', function(e) {
                e.preventDefault();
                hocwp.mediaUpload($(this));
            });
        });
        $(document).delegate('.btn-insert-media', 'click', function(e) {
            e.preventDefault();
            hocwp.mediaUpload($(this));
        });
    })();

    (function() {
        $('.sortable').hocwpSortable();
    })();
})(jQuery);