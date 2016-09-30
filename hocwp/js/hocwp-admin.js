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
                            $widget = $(this),
                            using_chosen = false;
                        if ('hocwp_widget_post' == id_base || 'hocwp_widget_top_commenter' == id_base || 'hocwp_widget_term' == id_base) {
                            using_chosen = true;
                        } else if ('hocwp_widget_link' == id_base) {
                            using_chosen = true;
                        }
                        if (using_chosen) {
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

    // Manage slider item
    (function () {
        var $add_slider_button = $('.hocwp-meta-box #add_slider');
        if ($add_slider_button.length) {
            $add_slider_button.hocwpMediaUpload({
                hideAddButton: false
            });
            $('.hocwp-meta-box .list-slider-items img.item-image').hocwpMediaUpload();
            $body.on('hocwp_media:selected', function (e, media_data, $button) {
                e.preventDefault();
                $button = $($button) || $(this);
                if ($button.length) {
                    var media_url = media_data.url,
                        media_id = parseInt(media_data.id);
                    if ($button.hasClass('item-image')) {
                        var $slider_item = $button.parent(),
                            $item_image_url = $slider_item.find('.item-image-url'),
                            $item_image_id = $slider_item.find('.item-image-id');
                        if (media_id > 0) {
                            $button.attr('src', media_url);
                            $item_image_url.val(media_url);
                            $item_image_id.val(media_id);
                        }
                    } else {
                        var $slider_items_container = $button.closest('.hocwp-meta-box'),
                            $list_slider_items = $slider_items_container.find('.list-slider-items'),
                            count_item = parseInt($list_slider_items.attr('data-items')),
                            max_item_id = parseInt($list_slider_items.attr('data-max-id')),
                            $item_order = $slider_items_container.find('.item-order'),
                            item_order_value = $item_order.val();
                        if (media_id > 0) {
                            count_item++;
                            max_item_id++;
                            $button.addClass('disabled');
                            $.ajax({
                                type: 'POST',
                                dataType: 'json',
                                url: hocwp.ajax_url,
                                data: {
                                    action: 'hocwp_generate_slider_sortable_item',
                                    max_item_id: max_item_id,
                                    media_url: media_url,
                                    media_id: media_id
                                },
                                success: function (response) {
                                    if ($.trim(response.html_data)) {
                                        $list_slider_items.append(response.html_data);
                                        $list_slider_items.attr('data-items', count_item);
                                        $list_slider_items.attr('data-max-id', max_item_id);
                                        if ($.trim(item_order_value)) {
                                            item_order_value += ',';
                                        }
                                        item_order_value += max_item_id;
                                        $item_order.val(item_order_value);
                                    }
                                    $button.removeClass('disabled');
                                    $list_slider_items.find('.item-image').hocwpMediaUpload();
                                    $list_slider_items.find('.hocwp-color-picker').wpColorPicker();
                                }
                            });
                        }
                    }
                }
            });

            $body.on('hocwp_sortable:stop', function (e, ui, $list) {
                var $element = $($list);
                if ($element.length && $element.hasClass('sortable')) {
                    var $list_slider_items = $element,
                        $slider_item_container = $list_slider_items.parent(),
                        $item_order = $slider_item_container.find('.item-order'),
                        item_order_value = '';
                    $list_slider_items.find('li').each(function (index, el) {
                        var $li_item = $(el);
                        item_order_value += $li_item.attr('data-item');
                        item_order_value += ',';
                    });
                    item_order_value = item_order_value.slice(0, -1);
                    $item_order.val(item_order_value);
                }
            });

            // Remove slider item
            $('.hocwp-meta-box .list-slider-items').on('click', '.icon-delete', function (e) {
                e.preventDefault();
                var $element = $(this),
                    $slider_item = $element.parent(),
                    $list_slider_items = $slider_item.parent(),
                    $slider_item_container = $list_slider_items.parent(),
                    $item_order = $slider_item_container.find('.item-order'),
                    item_order_value = '';
                if (confirm(hocwp.i18n.delete_confirm_message)) {
                    $element.addClass('disabled');
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: hocwp.ajax_url,
                        data: {
                            action: 'hocwp_remove_slider_item',
                            item_id: parseInt($slider_item.attr('data-item')),
                            post_id: parseInt($list_slider_items.attr('data-post'))
                        },
                        success: function (response) {
                            $slider_item.remove();
                            $list_slider_items.find('li').each(function (index, el) {
                                var $li_item = $(el);
                                item_order_value += $li_item.attr('data-item');
                                item_order_value += ',';
                            });
                            item_order_value = item_order_value.slice(0, -1);
                            $item_order.val(item_order_value);
                        }
                    });
                }
            });

            $('.hocwp-meta-box .list-slider-items').on('click', '.advance .dashicons', function (e) {
                e.preventDefault();
                var $element = $(this),
                    $advance = $element.parent(),
                    $box_content = $element.next();
                $advance.toggleClass('active');
                if ($advance.hasClass('active')) {
                    $box_content.slideDown();
                } else {
                    $box_content.slideUp();
                }
                $element.toggleClass('dashicons-editor-contract dashicons-editor-expand');
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
                $compress_core = $('#hocwp_developers_compress_core'),
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
                            force_compress: force_compress,
                            compress_core: $compress_core.is(':checked')
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