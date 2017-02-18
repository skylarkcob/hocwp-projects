/**
 * Last updated: 20/04/2016
 */

jQuery(document).ready(function ($) {
    var $body = $('body');

    (function () {
        $('.sf-menu, .hocwp-superfish-menu > ul').each(function () {
            var $element = $(this),
                options = {
                    hoverClass: 'sf-hover',
                    delay: 100,
                    cssArrows: false,
                    dropShadows: false
                };
            if ($element.hasClass('hocwp-mobile-menu')) {
                return;
            }
            if (!$element.hasClass('sf-menu')) {
                $element.addClass('sf-menu');
            }
            if ($element.hasClass('slide')) {
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
            if ($element.hasClass('arrow')) {
                options.cssArrows = true;
            }
            $element.superfish(options);
        });
    })();

    (function () {
        $('.hocwp-go-top').hocwpScrollTop();
    })();

    (function () {
        $('input[type="file"].hocwp-field-upload').each(function () {
            hocwp.limitUploadFile($(this));
        });
    })();

    (function () {
        $('.hocwp .comment-tools .comment-likes').on('click', function (e) {
            e.preventDefault();
            var $element = $(this),
                $container = $element.closest('.comment'),
                $count = $element.find('.count'),
                comment_id = parseInt($container.attr('data-comment-id')),
                likes = parseInt($element.attr('data-likes'));
            $element.addClass('disabled');
            $element.css({'text-decoration': 'none'});
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                cache: true,
                data: {
                    action: 'hocwp_comment_likes',
                    comment_id: comment_id,
                    likes: likes
                },
                success: function (response) {
                    likes++;
                    $element.attr('data-likes', likes);
                    $count.html(response.likes);
                }
            });
            return false;
        });

        $('.hocwp .comment-tools .comment-report').on('click', function (e) {
            e.preventDefault();
            var $element = $(this),
                $container = $element.closest('.comment'),
                comment_id = parseInt($container.attr('data-comment-id'));
            $element.addClass('disabled');
            $element.css({'text-decoration': 'none'});
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                cache: true,
                data: {
                    action: 'hocwp_comment_report',
                    comment_id: comment_id
                },
                success: function (response) {

                }
            });
            return false;
        });

        $('.hocwp .comment-tools .comment-share').on('click', function (e) {
            e.preventDefault();
            var $element = $(this);
            $element.css({'text-decoration': 'none'});
            $element.toggleClass('active');
            return false;
        });

        $('.hocwp .comment-tools .comment-share .list-share .fa').on('click', function (e) {
            e.preventDefault();
            var $element = $(this);
            $element.css({'text-decoration': 'none'});
            window.open($element.attr('data-url'), 'ShareWindow', 'height=450, width=550, toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
        });
    })();

    (function () {
        hocwp.iconChangeCaptchaExecute();
    })();

    (function () {
        $('.hocwp.hocwp-google-maps .hocwp-field-maps').hocwpGoogleMaps();
    })();

    (function () {
        $('.vote .vote-post').on('click', function (e) {
            e.preventDefault();
            var $element = $(this),
                $parent = $element.parent(),
                vote_type = $element.attr('data-vote-type'),
                post_id = $parent.attr('data-post-id');
            $element.addClass('disabled');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                cache: true,
                data: {
                    action: 'hocwp_vote_post',
                    post_id: post_id,
                    vote_type: vote_type,
                    value: $element.attr('data-vote')
                },
                success: function (response) {
                    if (response.success) {
                        $element.attr('data-vote', response.value);
                        $parent.addClass('disabled');
                    }
                }
            });
        });
    })();

    (function () {
        var $cart_preview = $('#hocwpCart');
        if ($cart_preview.length) {
            $cart_preview.on('click', '.hocwp-post .fa-remove', function (e) {
                e.preventDefault();
                var $element = $(this),
                    post_id = $element.attr('data-id'),
                    $item = $element.closest('.hocwp-post'),
                    $cart_contents = $element.closest('.hocwp-cart-contents');
                $element.addClass('disabled');
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: hocwp.ajax_url,
                    cache: true,
                    data: {
                        action: 'hocwp_wc_remove_cart_item',
                        post_id: post_id
                    },
                    success: function (response) {
                        $item.fadeOut();
                        $item.remove();
                        if (response.updated) {
                            $cart_contents.html(response.cart_contents);
                        }
                    }
                });
            });
        }
    })();

    // Tab widget
    (function () {
        var $tabber_widgets = $('.hocwp-tabber-widget');
        if ($tabber_widgets.length) {
            $tabber_widgets.each(function () {
                var $element = $(this),
                    $list_tabs = $element.find('ul.nav-tabs');
                $element.find('.tab-item').each(function () {
                    var widget = $(this).attr('id');
                    $(this).find('a.tab-title').attr('href', '#' + widget).wrap('<li></li>').parent().detach().appendTo($list_tabs);
                });
                $list_tabs.find('li:first').addClass('active');
                $list_tabs.fadeIn();
                $element.find('.tab-pane:first').addClass('active');
            });
            $tabber_widgets.on('click', '.nav-tabs li a', function (e) {
                e.preventDefault();
                var $element = $(this),
                    id = $element.attr('href').replace('#', ''),
                    $widget = $element.closest('.hocwp-tabber-widget'),
                    $pane = $widget.find('div[id^="' + id + '"]');
                $widget.find('.tab-pane').removeClass('active');
                $pane.addClass('active');
            });
        }
    })();

    // Product fast buy
    (function () {
        var $modal = $('.single-product.woocommerce .modal.product-fast-buy');
        if ($modal.length) {
            $modal.on('click', '.customer-info form button', function (e) {
                e.preventDefault();
                var $element = $(this),
                    $modal_body = $element.closest('.modal-body'),
                    $attributes_form = $modal_body.find('.attributes-form'),
                    attributes = [],
                    $form = $element.closest('form'),
                    $full_name = $form.find('.full-name'),
                    $phone = $form.find('.phone'),
                    $email = $form.find('.email'),
                    $address = $form.find('.address'),
                    $message = $form.find('.message');
                if ($full_name.prop('required') && !$.trim($full_name.val())) {
                    $full_name.focus();
                } else if ($phone.prop('required') && !$.trim($phone.val())) {
                    $phone.focus();
                } else if ($email.prop('required') && !$.trim($email.val())) {
                    $email.focus();
                } else if ($address.prop('required') && !$.trim($address.val())) {
                    $address.focus();
                } else if ($message.prop('required') && !$.trim($message.val())) {
                    $message.focus();
                } else {
                    $element.addClass('disabled');
                    if ($attributes_form.length) {
                        $attributes_form.find('select').each(function () {
                            var $select = $(this),
                                attribute = {name: $select.attr('data-attribute_name'), value: $select.val()};
                            attributes.push(attribute);
                        });
                    }
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: hocwp.ajax_url,
                        cache: true,
                        data: {
                            action: 'hocwp_wc_order_item',
                            post_id: $element.attr('data-id'),
                            name: $full_name.val(),
                            phone: $phone.val(),
                            email: $email.val(),
                            message: $message.val(),
                            address: $address.val(),
                            attributes: attributes
                        },
                        success: function (response) {
                            if ($.trim(response.html_data)) {
                                $modal_body.html(response.html_data);
                            }
                        }
                    });
                }
            });
        }
    })();

    (function () {
        if ($body.hasClass('woocommerce') && $body.hasClass('single-product')) {
            var $main_image = $('.single-product.woocommerce .images .woocommerce-main-image');
            if ($main_image.length) {
                var current_image = $main_image.html(),
                    $current_image = $main_image.children('img'),
                    $thumbnails = $main_image.next('.thumbnails');
                if ($thumbnails.length && $body.hasClass('thumbnail-preview')) {
                    $thumbnails.on('mouseover', 'img', function (e) {
                        e.preventDefault();
                        var $element = $(this),
                            url = $element.attr('url'),
                            srcset = $element.attr('srcset');
                        $thumbnails.children('a').removeClass('active');
                        $element.parent().addClass('active');
                        $current_image.attr('src', url).attr('srcset', srcset);
                        $main_image.trigger('hocwp:image_src_changed', [url]);
                    });
                }
                if ($body.hasClass('thumbnail-zooming')) {
                    if ($thumbnails.length) {
                        $thumbnails.children('a').first().addClass('active');
                    }
                    if (hocwp.is_function(jQuery().zoom)) {
                        $main_image.zoom({
                            callback: function () {
                                var $image_zoom = $main_image.find('.zoomImg');
                                if ($image_zoom.width() < $main_image.width()) {
                                    $main_image.trigger('zoom.destroy');
                                }
                            }
                        });
                        $main_image.on('hocwp:image_src_changed', function (e, url) {
                            $main_image.trigger('zoom.destroy');
                            $main_image.zoom({
                                url: url,
                                callback: function () {
                                    var $image_zoom = $main_image.find('.zoomImg');
                                    if ($image_zoom.width() < $main_image.width()) {
                                        $main_image.trigger('zoom.destroy');
                                    }
                                }
                            });
                        });
                    }
                }
            }
        }
    })();

    // User subscribe widget
    (function () {
        var $hocwp_widget_subscribe = $('.hocwp-subscribe-widget');
        if ($hocwp_widget_subscribe.length) {
            $hocwp_widget_subscribe.find('.hocwp-subscribe-form').on('submit', function (e) {
                e.preventDefault();
                var $element = $(this),
                    $messages = $element.find('.messages'),
                    use_captcha = $element.attr('data-captcha'),
                    register = $element.attr('data-register'),
                    $submit = $element.find('input[type="submit"]'),
                    $email = $element.find('.input-email'),
                    $name = $element.find('.input-name'),
                    $phone = $element.find('.input-phone'),
                    $captcha = $element.find('.hocwp-captcha-code'),
                    captcha = '';
                if ($name.length && $name.prop('required') && !$.trim($name.val())) {
                    $name.focus();
                } else if ($phone.length && $phone.prop('required') && !$.trim($phone.val())) {
                    $phone.focus();
                } else if ($email.length && $email.prop('required') && !$.trim($email.val())) {
                    $email.focus();
                } else if ($captcha.length && $captcha.prop('required') && !$.trim($captcha.val())) {
                    $captcha.focus();
                } else {
                    if ($captcha.length) {
                        captcha = $captcha.val();
                    }
                    $submit.addClass('disabled');
                    $element.find('.img-loading').show();
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: hocwp.ajax_url,
                        cache: true,
                        data: {
                            action: 'hocwp_widget_subscribe',
                            name: $name.val(),
                            phone: $phone.val(),
                            email: $email.val(),
                            use_captcha: use_captcha,
                            captcha: captcha,
                            register: register
                        },
                        success: function (response) {
                            $element.find('.img-loading').hide();
                            $captcha.next().next().trigger('click');
                            $messages.html(response.message);
                            if (response.success) {

                            } else {
                                $submit.removeClass('disabled');
                            }
                        }
                    });
                }
            });
        }
    })();

    (function () {
        var $hocwp_widget_facebook_messenger = $('.hocwp .hocwp-widget-facebook-messenger');
        if ($hocwp_widget_facebook_messenger.length) {
            if ($hocwp_widget_facebook_messenger.hasClass('fixed')) {
                var $messenger_box = $hocwp_widget_facebook_messenger.find('.messenger-box'),
                    $module_header = $messenger_box.find('.module-header'),
                    $icon_angle = $module_header.find('i'),
                    $icon_delete = $module_header.find('.facebook-messenger-box-control'),
                    box_height = parseInt($module_header.outerHeight()),
                    box_width = parseInt($messenger_box.outerWidth());
                if ($hocwp_widget_facebook_messenger.hasClass('bottom-left') || $hocwp_widget_facebook_messenger.hasClass('bottom-right')) {
                    $messenger_box.css({height: box_height + 'px'});
                    $module_header.on('click', function (e) {
                        e.preventDefault();
                        $messenger_box.toggleClass('active');
                        if ($messenger_box.hasClass('active')) {
                            $messenger_box.css({height: 'auto'});
                            $icon_angle.toggleClass('fa-angle-up fa-angle-down');
                        } else {
                            $messenger_box.css({height: box_height + 'px'});
                            $icon_angle.toggleClass('fa-angle-up fa-angle-down');
                        }
                    });
                } else {
                    $module_header.on('click', function (e) {
                        e.preventDefault();
                        $messenger_box.toggleClass('active');
                        if ($messenger_box.hasClass('active')) {
                            $icon_delete.hide();
                        } else {
                            $icon_delete.fadeIn();
                        }
                    });
                }
            }
        }
    })();

    (function () {
        if ($body.hasClass('hocwp-shop-site')) {
            $body.on('click', '.number-up, .number-down', function (e) {
                e.preventDefault();
                var $element = $(this),
                    $container = $element.parent(),
                    $input_number = $container.children('.input-number'),
                    value = $input_number.val(),
                    max = parseInt($input_number.attr('max')),
                    min = parseInt($input_number.attr('min'));
                if ($element.hasClass('number-up')) {
                    value++;
                } else {
                    value--;
                }
                if ($.isNumeric(min) && value < min) {
                    value = min;
                } else if ($.isNumeric(max) && value > max) {
                    value = max;
                }
                $input_number.val(value);
            });
        }
    })();

    (function () {
        $('.hocwp').on('click', '.save-post, .favorite-post, .interest-post, .love-post, .btn-user-save-post, .btn-save-post', function (e) {
            e.preventDefault();
            if ($body.hasClass('hocwp-user')) {
                var $element = $(this),
                    post_id = $element.attr('data-post-id'),
                    action = $element.attr('data-action'),
                    text = $element.text(),
                    data_text = $element.attr('data-text'),
                    type = $element.attr('data-type'),
                    prevent_default = parseInt($element.attr('data-prevent-default'));
                if (1 == prevent_default) {
                    return false;
                }
                $element.addClass('disabled');
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: hocwp.ajax_url,
                    cache: true,
                    data: {
                        action: 'hocwp_favorite_post',
                        post_id: post_id,
                        type: type,
                        data_action: action
                    },
                    success: function (response) {
                        if (response.success) {
                            if ($.trim(response.html_data)) {
                                $element.html(response.html_data);
                            }
                            if ('do' == action) {
                                $element.addClass('active');
                                action = 'undo';
                            } else {
                                $element.removeClass('active');
                                action = 'do';
                            }
                            if ('undo' != action) {
                                $element.removeClass('disabled');
                            }
                            if (response.remove) {
                                $body.trigger('hocwp_remove_favorite_post', $element);
                            }
                            $element.attr('data-action', action);
                            if ($.trim(data_text)) {
                                $element.text(data_text);
                                $element.attr('data-text', text);
                            }
                        }
                    }
                });
            } else {
                window.location.href = hocwp.login_url;
            }
        });
    })();

    (function () {
        var body_lazy_original = $body.attr('data-original');
        if (typeof body_lazy_original != 'undefined' && $.trim(body_lazy_original)) {
            $body.lazyload({
                effect: 'fadeIn'
            });
        }
    })();

    (function () {
        $('.temperature-up, .temperature-down').on('click', function (e) {
            e.preventDefault();
            var $element = $(this),
                post_id = parseInt($element.attr('data-id')),
                type = $element.attr('data-action');
            $element.addClass('disabled');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                cache: true,
                data: {
                    action: 'hocwp_update_post_temperature',
                    post_id: post_id,
                    type: type
                },
                success: function (response) {

                }
            });
        });
    })();

    (function () {
        if ($body.hasClass('hocwp')) {
            if ($body.hasClass('page-template-login') || $body.hasClass('page-template-account') || $body.hasClass('page-template-register')) {
                var $login_form = $('#loginform'),
                    $lost_password_form = $('#lostpasswordform'),
                    $register_form = $('#registerform'),
                    submit_class = 'btn btn-' + hocwp.logins.button_style;
                if ($login_form.length) {
                    $login_form.find('input[type="submit"]').addClass(submit_class);
                }
                if ($lost_password_form.length) {
                    $lost_password_form.find('input[type="submit"]').addClass(submit_class);
                }
                if ($register_form.length) {
                    $register_form.find('input[type="submit"]').addClass(submit_class);
                }
            }
            var $comment_form = $('#commentform');
            if ($comment_form.length) {
                $comment_form.find('input[type="submit"]').addClass('btn btn-' + hocwp.discussions.button_style);
            }
        }
    })();

    (function () {
        $('.hocwp').on('click', 'input.select-all, textarea.select-all', function () {
            $(this).select();
        });
    })();

    (function () {
        var $ajax_form = $('.hocwp .hocwp-form-ajax');
        if ($ajax_form.length) {
            $ajax_form.on('submit', function (e) {
                e.preventDefault();
                var $element = $(this),
                    $submit = $element.find('input[type="submit"]');
                if (!$submit.length) {
                    $submit = $element.find('input[name="submit"]');
                }
                $element.addClass('processing');
                $element.find('.messages .alert').fadeOut();
                if ($submit.length) {
                    $submit.addClass('disabled');
                    $submit.attr('data-text', $submit.val());
                    $submit.val(hocwp.i18n.processing_text);
                }
            });
            $ajax_form.on('hocwp:ajax_complete', function (e, response) {
                var $element = $(this),
                    $submit = $element.find('input[type="submit"]'),
                    $alert_message = null,
                    data_text = '';
                if (!$submit.length) {
                    $submit = $element.find('input[name="submit"]');
                }
                $element.removeClass('processing');
                if ($submit.length) {
                    $submit.val($submit.attr('data-text'));
                    $submit.removeClass('disabled');
                }
                if (response.success) {
                    $alert_message = $element.find('.messages .alert-success');
                    if ($alert_message.length && $.trim(response.message)) {
                        data_text = $alert_message.attr('data-text');
                        if (!$.trim(data_text)) {
                            data_text = $alert_message.html();
                            $alert_message.attr('data-text', data_text);
                        }
                        $alert_message.html(response.message);
                    } else {
                        $alert_message.html($alert_message.attr('data-text'));
                    }
                } else {
                    $alert_message = $element.find('.messages .alert-danger');
                    if ($alert_message.length && $.trim(response.message)) {
                        data_text = $alert_message.attr('data-text');
                        if (!$.trim(data_text)) {
                            data_text = $alert_message.html();
                            $alert_message.attr('data-text', data_text);
                        }
                        $alert_message.html(response.message);
                    } else {
                        $alert_message.html($alert_message.attr('data-text'));
                    }
                }
                if ($alert_message.length) {
                    $alert_message.fadeIn();
                }
            });
        }
    })();
});