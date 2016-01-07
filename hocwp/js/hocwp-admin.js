/**
 * Last update: 07/01/2016
 */

jQuery(document).ready(function($) {
    hocwp.addBulkAction = function(actions) {
        actions = actions || [];
        for(var i = 0; i < actions.length; i++) {
            $('<option>').val(actions[i][0]).text(actions[i][1]).appendTo("select[name='action']");
            $('<option>').val(actions[i][0]).text(actions[i][1]).appendTo("select[name='action2']");
        }
    };

    hocwp.widgetPostTypeChange = function(element) {
        var $element = $(element),
            selected = $element.val(),
            $hocwp_widget = $element.closest('.hocwp-widget'),
            $select_category = $hocwp_widget.find('.select-category'),
            $select_category_container = $select_category.closest('.hocwp-widget-field-group');
        if('category' == selected) {
            $select_category_container.fadeIn(500);
        } else {
            $select_category_container.fadeOut();
        }
    };

    (function() {
        $(document).on('widget-updated', function(event, widget) {
            var widget_id = widget[0].id;
            if(widget_id && widget_id.match('hocwp')) {
                var $widget = $(this);
                if(widget_id.match('hocwp_widget_banner')) {
                    $widget.find('.btn-insert-media').live('click', function(e) {
                        e.preventDefault();
                        hocwp.mediaUpload($(this));
                    });
                    $widget.find('.btn-remove').live('click', function(e) {
                        e.preventDefault();
                        var $container = $(this).parent();
                        hocwp.mediaUpload($container.find('.btn-insert-media'), {remove: true});
                    });
                    $widget.find('input.media-url').live('change input', function(e) {
                        e.preventDefault();
                        var $container = $(this).parent();
                        hocwp.mediaUpload($container.find('.btn-insert-media'), {change: true});
                    });
                } else if(widget_id.match('hocwp_widget_post')) {
                    $widget.find('.hocwp-widget-post .get-by').on('change', function(e) {
                        e.preventDefault();
                        hocwp.widgetPostTypeChange(this);
                    });
                }
            }
        });

        $('div.widgets-sortables').bind('sortreceive', function(event, ui) {
            var widget_id = $(ui.item).attr('id');
            if(widget_id && widget_id.match('hocwp')) {
                var $widget = $(ui.item);
                if(widget_id.match('hocwp_widget_banner')) {
                    $widget.find('.btn-insert-media').live('click', function(e) {
                        e.preventDefault();
                        hocwp.mediaUpload($(this));
                    });
                } else if(widget_id.match('hocwp_widget_post')) {
                    $widget.find('.hocwp-widget-post .get-by').on('change', function(e) {
                        e.preventDefault();
                        hocwp.widgetPostTypeChange(this);
                    });
                }
            }
        }).bind('sortstop', function(event, ui) {
            var widget_id = $(ui.item).attr('id');
            if(widget_id && widget_id.match('hocwp')) {
                var $widget = $(ui.item);
                if(widget_id.match('hocwp_widget_post')) {
                    $widget.find('.hocwp-widget-post .get-by').on('change', function(e) {
                        e.preventDefault();
                        hocwp.widgetPostTypeChange(this);
                    });
                }
            }
        });

        $(document).ajaxSuccess(function(e, xhr, settings) {
            if(settings.data.search('action=save-widget') != -1) {
                if(settings.data.search('hocwp') != -1) {
                    var id_base = hocwp.getParamByName(settings.data, 'id_base'),
                        $widget = $(this);
                    if('hocwp_widget_post' == id_base || 'hocwp_widget_top_commenter' == id_base) {
                        $widget.find('.hocwp-widget .chosen-container').hide();
                        $widget.find('.hocwp-widget .chooseable').hocwpChosenSelect();
                        $widget.find('.hocwp-widget .chosen-container').show();
                    }
                }
            }
        });

        $(document).delegate('.btn-insert-media', 'click', function(e) {
            e.preventDefault();
            hocwp.mediaUpload($(this));
        });
    })();

    (function() {
        $('.hocwp-widget-post .get-by').live('change', function(e) {
            e.preventDefault();
            hocwp.widgetPostTypeChange(this);
        });
    })();

    (function() {
        var $sortable = $('.sortable');
        if($sortable.length) {
            $sortable.hocwpSortable();
        }
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

    (function() {
        $('.hocwp input[type="checkbox"]').on('click', function(e) {
            var $element = $(this),
                value = $element.val();
            if($element.is(':checked')) {
                if(1 != value) {
                    $element.val(1);
                }
            } else {
                if(1 == value || '' == value) {
                    $element.val(0);
                }
            }
        });
    })();

    (function() {
        hocwp.switcherAjax();
    })();

    (function() {
        var $choseables = $('.hocwp-widget .chooseable');
        if($choseables.length) {
            $choseables.hocwpChosenSelect();
        }
    })();

    (function() {
        var $color_picker = $('.hocwp-color-picker');
        if($color_picker.length) {
            $color_picker.wpColorPicker();
        }
    })();

    (function() {
        $('.hocwp.server-information .postbox > .handlediv').on('click', function(e) {
            e.preventDefault();
            var $element = $(this),
                $postbox = $element.parent();
            $postbox.toggleClass('closed');
            if($postbox.hasClass('closed')) {
                $element.attr('aria-expanded', 'false');
            } else {
                $element.attr('aria-expanded', 'true');
            }
        });
    })();

    (function() {
        var $hocwp_option_page = $('.hocwp.option-page');
        if($hocwp_option_page.length) {
            var $sidebar = $hocwp_option_page.find('.sidebar'),
                $main = $hocwp_option_page.find('.main-content');
            if($sidebar.height() >= $main.height()) {
                $main.css({'min-height' : $sidebar.height() + 50 + 'px'});
            }
        }
    })();
});