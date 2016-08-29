/**
 * Last updated: 07/05/2016
 */
window.hocwp = window.hocwp || {};
window.wp = window.wp || {};

jQuery(document).ready(function ($) {
    var $body = $('body');

    hocwp.addBulkAction = function (actions) {
        actions = actions || [];
        for (var i = 0; i < actions.length; i++) {
            $('<option>').val(actions[i][0]).text(actions[i][1]).appendTo("select[name='action']");
            $('<option>').val(actions[i][0]).text(actions[i][1]).appendTo("select[name='action2']");
        }
    };

    hocwp.widgetPostTypeChange = function (element) {
        var $element = $(element),
            selected = $element.val(),
            $hocwp_widget = $element.closest('.hocwp-widget'),
            $select_category = $hocwp_widget.find('.select-category'),
            $select_category_container = $select_category.closest('.hocwp-widget-field-group');
        if ('category' == selected) {
            $select_category_container.fadeIn(500);
        } else {
            $select_category_container.fadeOut();
        }
    };

    (function () {
        if ($body.hasClass('widgets-php')) {
            $(document).on('widget-updated', function (event, widget) {
                var widget_id = widget[0].id;
                if (widget_id && widget_id.match('hocwp')) {
                    var $widget = $(this);
                    if (widget_id.match('hocwp_widget_banner') || widget_id.match('hocwp_widget_icon')) {
                        $widget.find('.btn-insert-media').live('click', function (e) {
                            e.preventDefault();
                            hocwp.mediaUpload($(this));
                        });
                        $widget.find('.btn-remove').live('click', function (e) {
                            e.preventDefault();
                            var $container = $(this).parent();
                            hocwp.mediaUpload($container.find('.btn-insert-media'), {remove: true});
                        });
                        $widget.find('input.media-url').live('change input', function (e) {
                            e.preventDefault();
                            var $container = $(this).parent();
                            hocwp.mediaUpload($container.find('.btn-insert-media'), {change: true});
                        });
                    } else if (widget_id.match('hocwp_widget_post')) {
                        $widget.find('.hocwp-widget-post .get-by').on('change', function (e) {
                            e.preventDefault();
                            hocwp.widgetPostTypeChange(this);
                        });
                    }
                }
            });

            $('div.widgets-sortables').bind('sortreceive', function (event, ui) {
                var widget_id = $(ui.item).attr('id');
                if (widget_id && widget_id.match('hocwp')) {
                    var $widget = $(ui.item);
                    if (widget_id.match('hocwp_widget_banner') || widget_id.match('hocwp_widget_icon')) {
                        $widget.find('.btn-insert-media').live('click', function (e) {
                            e.preventDefault();
                            hocwp.mediaUpload($(this));
                        });
                    } else if (widget_id.match('hocwp_widget_post')) {
                        $widget.find('.hocwp-widget-post .get-by').on('change', function (e) {
                            e.preventDefault();
                            hocwp.widgetPostTypeChange(this);
                        });
                    }
                }
            }).bind('sortstop', function (event, ui) {
                var widget_id = $(ui.item).attr('id');
                if (widget_id && widget_id.match('hocwp')) {
                    var $widget = $(ui.item);
                    if (widget_id.match('hocwp_widget_post')) {
                        $widget.find('.hocwp-widget-post .get-by').on('change', function (e) {
                            e.preventDefault();
                            hocwp.widgetPostTypeChange(this);
                        });
                    }
                }
            });

            $(document).ajaxSuccess(function (e, xhr, settings) {
                if (settings.data.search('action=save-widget') != -1) {
                    if (settings.data.search('hocwp') != -1) {
                        var id_base = hocwp.getParamByName(settings.data, 'id_base'),
                            $widget = $(this);
                        if ('hocwp_widget_post' == id_base || 'hocwp_widget_top_commenter' == id_base || 'hocwp_widget_term' == id_base) {
                            $widget.find('.hocwp-widget .chosen-container').hide();
                            $widget.find('.hocwp-widget .chooseable').hocwpChosenSelect();
                            $widget.find('.hocwp-widget .chosen-container').show();
                        }
                    }
                }
            });

            $(document).delegate('.btn-insert-media', 'click', function (e) {
                e.preventDefault();
                hocwp.mediaUpload($(this));
            });
        }
    })();

    (function () {
        $('.hocwp-widget-post .get-by').live('change', function (e) {
            e.preventDefault();
            hocwp.widgetPostTypeChange(this);
        });
    })();

    (function () {
        var $sortable = $('.sortable');
        if ($sortable.length) {
            $sortable.hocwpSortable();
        }
    })();

    (function () {
        $('#hocwp_plugin_license_use_for').on('change', function (e) {
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
                success: function (response) {
                    $customer_email.val(response.customer_email);
                    $license_code.val(response.license_code);
                }
            });
        });
    })();

    (function () {
        $('.hocwp input[type="checkbox"]').on('click', function (e) {
            var $element = $(this),
                value = $element.val();
            if ($element.is(':checked')) {
                if (1 != value) {
                    $element.val(1);
                }
            } else {
                if (1 == value || '' == value) {
                    $element.val(0);
                }
            }
        });
    })();

    (function () {
        hocwp.switcherAjax();
    })();

    (function () {
        if ($body.hasClass('post-php') || $body.hasClass('post-new-php')) {
            hocwp.addDefaultQuicktagButton();
        }
    })();

    (function () {
        var $choseables = $('.hocwp-widget .chooseable');
        if ($choseables.length) {
            $choseables.hocwpChosenSelect();
        }
        $choseables = $('.hocwp-meta-box .chooseable');
        if ($choseables.length) {
            $choseables.hocwpChosenSelect();
        }
    })();

    (function () {
        var $color_picker = $('.hocwp-color-picker'),
            $datetime_picker = $('.hocwp-datetime-picker');
        if ($color_picker.length) {
            $color_picker.wpColorPicker();
        }
        if ($datetime_picker.length) {
            var options = {},
                min_date = $datetime_picker.attr('data-min-date'),
                date_format = $datetime_picker.attr('data-date-format');
            if ($.isNumeric(min_date)) {
                options.minDate = 0;
            }
            if ($.trim(date_format)) {
                options.dateFormat = date_format;
            }
            $datetime_picker.datepicker(options);
        }
    })();

    (function () {
        $('.hocwp.server-information .postbox > .handlediv').on('click', function (e) {
            e.preventDefault();
            var $element = $(this),
                $postbox = $element.parent();
            $postbox.toggleClass('closed');
            if ($postbox.hasClass('closed')) {
                $element.attr('aria-expanded', 'false');
            } else {
                $element.attr('aria-expanded', 'true');
            }
        });
    })();

    (function () {
        var $hocwp_option_page = $('.hocwp.option-page');
        if ($hocwp_option_page.length) {
            var $sidebar = $hocwp_option_page.find('.sidebar'),
                $main = $hocwp_option_page.find('.main-content');
            if ($sidebar.height() >= $main.height()) {
                $main.css({'min-height': $sidebar.height() + 50 + 'px'});
            }
        }
    })();

    (function () {
        $('.hocwp-field-maps').hocwpGoogleMaps();
        var $category_list = $('.classifieds.hocwp-google-maps #categorychecklist, .classifieds #classifieds_typechecklist, .classifieds #classifieds_objectchecklist, .classifieds #pricechecklist, .classifieds #acreagechecklist');
        $category_list.find('input[type="checkbox"]').on('change', function () {
            var $element = $(this),
                checked = $element.is(':checked'),
                $list_item = $element.closest('ul');
            $list_item.find('input[type="checkbox"]').attr('checked', false);
            if (checked) {
                $element.attr('checked', true);
            }
        });
    })();
});

jQuery(document).ready(function ($) {
    (function () {
        var $body = $('body');
        if ($body.hasClass('tools_page_hocwp_developers')) {
            var $compress_button = $('#hocwp_developers_compress_css_js'),
                $compress_css = $('#hocwp_developers_compress_css'),
                $compress_js = $('#hocwp_developers_compress_js'),
                $recompress = $('#hocwp_developers_re_compress'),
                $force_compress = $('#hocwp_developers_force_compress'),
                force_compress = false;
            if ($compress_button.length) {
                $compress_button.on('click', function () {
                    var type = [];
                    if ($compress_css.is(':checked')) {
                        type.push('css');
                    }
                    if ($compress_js.is(':checked')) {
                        type.push('js');
                    }
                    if ($recompress.is(':checked')) {
                        type.push('recompress');
                    }
                    if ($force_compress.is(':checked')) {
                        force_compress = true;
                    }
                    $compress_button.addClass('disabled');
                    $body.css({cursor: 'wait'});
                    alert('All your files are compressing, please wait...');
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: hocwp.ajax_url,
                        data: {
                            action: 'hocwp_compress_style_and_script',
                            type: JSON.stringify(type),
                            force_compress: force_compress
                        },
                        success: function (response) {
                            $body.css({cursor: 'auto'});
                            alert('These files compressed successfully!');
                        },
                        complete: function () {
                            $compress_button.removeClass('disabled');
                        }
                    });
                });
            }
        }
    })();

    (function () {
        $('.form-table .hocwp-disconnect-social').on('click', function (e) {
            e.preventDefault();
            var $element = $(this),
                user_id = $element.attr('data-user-id'),
                social = $element.attr('data-social');
            if (confirm(hocwp.i18n.disconnect_confirm_message)) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: hocwp.ajax_url,
                    data: {
                        action: 'hocwp_disconnect_social_account',
                        social: social,
                        user_id: user_id
                    },
                    success: function (response) {
                        window.location.href = window.location.href;
                    }
                });
            }
        });
    })();
});