window.wp = window.wp || {};
window.hocwp = window.hocwp || {};

(function($) {
    hocwp.addBulkAction = function(actions) {
        actions = actions || [];
        for(var i = 0; i < actions.length; i++) {
            $('<option>').val(actions[i][0]).text(actions[i][1]).appendTo("select[name='action']");
            $('<option>').val(actions[i][0]).text(actions[i][1]).appendTo("select[name='action2']");
        }
    };

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

    (function() {
        $('#hocwp_plugin_license_use_for').on('change', function(e) {
            e.preventDefault();
            var $element = $(this),
                $customer_email = $('#hocwp_plugin_license_customer_email'),
                $license_code = $('#hocwp_plugin_license_license_code');
            $customer_email.val('');
            $license_code.val('');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                data: {
                    action: 'hocwp_fetch_plugin_license',
                    use_for: $element.val()
                },
                success: function(response){
                    $customer_email.val(response.customer_email);
                    $license_code.val(response.license_code);
                }
            });
        });
    })();
})(jQuery);