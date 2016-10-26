/**
 * Last updated: 07/05/2016
 */

window.wp = window.wp || {};
window.hocwp = window.hocwp || {};

if (typeof jQuery === 'undefined') {
    throw new Error(hocwp.i18n.jquery_undefined_error)
}

jQuery(document).ready(function ($) {
    'use strict';

    var version = $.fn.jquery.split(' ')[0].split('.');
    if ((version[0] < 2 && version[1] < 9) || (version[0] == 1 && version[1] == 9 && version[2] < 1)) {
        throw new Error(hocwp.i18n.jquery_version_error)
    }
});

hocwp.media_frame = null;
hocwp.media_items = {};

jQuery(document).ready(function ($) {
    'use strict';
    var $body = $('body');

    hocwp.getParamByName = function (url, name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(url);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    };

    hocwp.receiveSelectedMediaItems = function (file_frame) {
        return file_frame.state().get('selection');
    };

    hocwp.receiveSelectedMediaItem = function (file_frame) {
        var items = hocwp.receiveSelectedMediaItems(file_frame);
        return items.first().toJSON();
    };

    hocwp.wcPriceSliderAuto = function () {
        $body.bind('price_slider_change', function () {
            var $price_slider = $('.widget .price_slider'),
                $form = $price_slider.closest('form');
            $form.submit();
        });
    };

    hocwp.addParamToUrl = function (param, value, url) {
        var result = new RegExp(param + "=([^&]*)", "i").exec(window.location.search),
            loc = window.location;
        result = result && result[1] || "";
        url = url || loc.protocol + '//' + loc.host + loc.pathname + loc.search;
        if (result == '') {
            if (loc.search == '') {
                url += "?" + param + '=' + value;
            } else {
                url += "&" + param + '=' + value;
            }
        } else {
            url = hocwp.updateUrlParam(param, value, url);
        }
        return url + loc.hash;
    };

    hocwp.updateUrlParam = function (param, value, url) {
        var pattern = new RegExp('\\b(' + param + '=).*?(&|$)');
        if (url.search(pattern) >= 0) {
            return url.replace(pattern, '$1' + value + '$2');
        }
        return url + (url.indexOf('?') > 0 ? '&' : '?') + param + '=' + value;
    };

    hocwp.isImageUrl = function (url) {
        if (!$.trim(url)) {
            return false;
        }
        var result = true,
            extension = url.slice(-4);
        if (extension != '.png' && extension != '.jpg' && extension != '.gif' && extension != '.bmp' && extension != 'jpeg') {
            if (extension != '.ico') {
                result = false;
            }
        }
        return result;
    };

    hocwp.getTagName = function ($tag) {
        if ($tag.length) {
            return $tag.get(0).tagName;
        }
        return '';
    };

    hocwp.isUrl = function (text) {
        var url_regex = new RegExp('^(http:\/\/www.|https:\/\/www.|ftp:\/\/www.|www.){1}([0-9A-Za-z]+\.)');
        return url_regex.test(text);
    };

    hocwp.isArray = function (variable) {
        return (Object.prototype.toString.call(variable) === '[object Array]');
    };

    hocwp.getFirstMediaItemJSON = function (media_items) {
        return media_items.first().toJSON();
    };

    hocwp.createImageHTML = function (args) {
        args = args || {};
        var alt = args.alt || '',
            id = args.id || 0,
            src = args.src || '',
            $element = args.element || null;
        if ($.isNumeric(id) && id > 0) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                cache: true,
                data: {
                    action: 'hocwp_sanitize_media_value',
                    url: src,
                    id: id
                },
                success: function (response) {
                    if (!response.is_image) {
                        src = response.type_icon;
                    }
                    if ($element.length) {
                        $element.html('<img src="' + src + '" alt="' + alt + '">');
                    }
                }
            });
        } else {
            if ($.trim(src)) {
                return '<img src="' + src + '" alt="' + alt + '">';
            }
        }
    };

    hocwp.is_function = function (object) {
        return (typeof object !== 'undefined' && typeof object == 'function') || false;
    };

    hocwp.autoReloadPageNoActive = function (reload_time, delay_time) {
        reload_time = reload_time || 60000;
        delay_time = delay_time || 10000;
        var time = new Date().getTime();
        $(document.body).bind('mousemove keypress', function () {
            time = new Date().getTime();
        });
        function refresh() {
            if (new Date().getTime() - time >= reload_time) {
                window.location.reload(true);
            } else {
                setTimeout(refresh, delay_time);
            }
        }

        setTimeout(refresh, delay_time);
    };

    hocwp.autoReloadPage = function (delay_time) {
        delay_time = delay_time || 2000;
        var time = new Date().getTime();

        function refresh() {
            if (new Date().getTime() - time >= delay_time) {
                window.location.reload(true);
            } else {
                setTimeout(refresh, 1000);
            }
        }

        setTimeout(refresh, 1000);
    };

    hocwp.debugLog = function (object) {
        var data = JSON.stringify(object);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: hocwp.ajax_url,
            cache: true,
            data: {
                action: 'hocwp_debug_log',
                object: data
            },
            success: function (response) {

            }
        });
    };

    hocwp.limitUploadFile = function ($element) {
        $element.on('change', function () {
            var count_file = $element.get(0).files.length,
                max_file = parseInt($element.attr('data-max')),
                object = this,
                $image_preview = $element.next();
            if (!$.isNumeric(max_file)) {
                max_file = -1;
            }
            if (max_file > 0 && count_file > max_file) {
                alert(hocwp.i18n.max_file_item_select_error);
                $element.val('');
                return false;
            }
            if ($image_preview.length) {
                $image_preview.empty();
                if (typeof (FileReader) != "undefined") {
                    for (var i = 0; i < count_file; i++) {
                        var reader = new FileReader(),
                            file_name = object.files.item(i).name;
                        reader.onload = function (e) {
                            var $image = $('<img>', {
                                src: e.target.result,
                                class: 'thumb-image',
                                alt: ''
                            }).attr('data-file-name', file_name);
                            $image.appendTo($image_preview);
                        };
                        $image_preview.show();
                        reader.readAsDataURL($element.get(0).files[i]);
                    }

                }
            }
        });
    };

    hocwp.setCookie = function (cname, cvalue, exmin) {
        var d = new Date();
        d.setTime(d.getTime() + (exmin * 60 * 1000));
        var expires = "expires=" + d.toGMTString(),
            my_cookies;
        my_cookies = cname + "=" + cvalue + "; " + expires + "; path=/";
        document.cookie = my_cookies;
    };

    hocwp.iconChangeCaptchaExecute = function () {
        var $icon_refresh_captcha = $('img.hocwp-captcha-reload'),
            $captcha_image = $('img.hocwp-captcha-image');
        if (!$captcha_image.length) {
            return false;
        }
        $captcha_image.css({'cursor': 'text'});
        $icon_refresh_captcha.css({'opacity': '0.75'});
        $icon_refresh_captcha.on('mouseover', function (e) {
            e.preventDefault();
            $(this).css({'opacity': '1'});
        });
        $icon_refresh_captcha.on('mouseout mouseleave', function (e) {
            e.preventDefault();
            $(this).css({'opacity': '0.75'});
        });
        $icon_refresh_captcha.on('click', function (e) {
            e.preventDefault();
            var $element = $(this),
                $container = $element.parent(),
                $image = $container.find('img.hocwp-captcha-image');
            $element.css({'opacity': '0.25', 'pointer-events': 'none'});
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                cache: true,
                data: {
                    action: 'hocwp_change_captcha_image'
                },
                success: function (response) {
                    if (response.success) {
                        $image.attr('src', response.captcha_image_url);
                    } else {
                        alert(response.message);
                    }
                    $element.css({'opacity': '0.75', 'pointer-events': 'inherit'});
                }
            });
        });
    };

    hocwp.addDefaultQuicktagButton = function () {
        var $quicktags_toolbar = $body.find('.quicktags-toolbar');
        if (!$body.hasClass('front-end') && $quicktags_toolbar.length && $quicktags_toolbar.attr('id') == 'ed_toolbar') {
            QTags.addButton('hr', 'hr', '\n<hr>\n', '', 'h', 'Horizontal rule line', 30);
            QTags.addButton('dl', 'dl', '<dl>\n', '</dl>\n\n', 'd', 'HTML Description List Element', 100);
            QTags.addButton('dt', 'dt', '\t<dt>', '</dt>\n', '', 'HTML Definition Term Element', 101);
            QTags.addButton('dd', 'dd', '\t<dd>', '</dd>\n', '', 'HTML Description Element', 102);
        }
    };

    hocwp.formatNumber = function (number, separator, currency) {
        currency = currency || ' ₫';
        separator = separator || ',';
        var number_string = number.toString(),
            decimal = '.',
            numbers = number_string.split('.'),
            result = '';
        if (!hocwp.isArray(numbers)) {
            numbers = number_string.split(',');
            decimal = ',';
        }
        if (hocwp.isArray(numbers)) {
            number_string = numbers[0];
        }
        var number_len = parseInt(number_string.length);
        var last = number_string.slice(-3);
        if (number_len > 3) {
            result += separator + last;
        } else {
            result += last;
        }

        while (number_len > 3) {
            number_len -= 3;
            number_string = number_string.slice(0, number_len);
            last = number_string.slice(-3);

            if (number_len <= 3) {
                result = last + result;
            } else {
                result = separator + last + result;
            }
        }
        if (hocwp.isArray(numbers) && $.isNumeric(numbers[1])) {
            result += decimal + numbers[1];
        }
        result += currency;
        result = $.trim(result);
        return result;
    };

    hocwp.scrollToPosition = function (pos, time) {
        time = time || 1000;
        $('html, body').stop().animate({scrollTop: pos}, time);
    };

    hocwp.goToTop = function () {
        hocwp.scrollToPosition(0);
        return false;
    };

    hocwp.scrollToTop = function () {
        hocwp.goToTop();
    };

    hocwp.isEmail = function (email) {
        return this.test(email, '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$');
    };

    hocwp.isEmpty = function (text) {
        return text.trim();
    };

    hocwp.switcherAjax = function () {
        $('.hocwp-switcher-ajax .icon-circle').on('click', function (e) {
            e.preventDefault();
            var $element = $(this),
                opacity = '0.5';
            if ($element.hasClass('icon-circle-success')) {
                opacity = '0.25';
            }
            $element.css({'opacity': opacity});
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                cache: true,
                data: {
                    action: 'hocwp_switcher_ajax',
                    post_id: $element.attr('data-id'),
                    value: $element.attr('data-value'),
                    key: $element.attr('data-key')
                },
                success: function (response) {
                    if (response.success) {
                        $element.toggleClass('icon-circle-success');
                    }
                    $element.css({'opacity': '1'});
                }
            });
        });
    };

    hocwp.chosenSelectUpdated = function (el) {
        var $element = el,
            values = $element.chosen().val();
        var $parent = $element.parent(),
            $result = $parent.find('.chosen-result');
        if (null == values) {
            $result.val('');
            return;
        }
        var new_value = [],
            taxonomy = null,
            $option = null,
            i = 0,
            count_value = values.length,
            is_term = false;
        for (i; i <= count_value; i++) {
            var current_value = values[i],
                new_item = {value: current_value};
            $option = $parent.find('option[value="' + current_value + '"]');
            taxonomy = $option.attr('data-taxonomy');
            if ($.trim(taxonomy)) {
                new_item.taxonomy = taxonomy;
                is_term = true;
            }
            new_value.push(new_item);
        }
        $result.val(JSON.stringify(new_value));
    };

    hocwp.mediaRemove = function (upload, remove, preview, url, id) {
        preview.html('');
        url.val('');
        id.val('');
        remove.addClass('hidden');
        upload.removeClass('hidden');
    };

    hocwp.mediaChange = function (upload, remove, preview, url, id) {
        if (hocwp.isImageUrl(url.val())) {
            preview.html(hocwp.createImageHTML({src: url.val(), id: id.val(), element: preview}));
        } else {
            preview.html('');
        }
        id.val('');
    };

    hocwp.mediaUpload = function (button, options) {
        var defaults = {
            title: hocwp.i18n.insert_media_title,
            button_text: null,
            multiple: false,
            remove: false,
            change: false
        };
        options = options || {};
        options = $.extend({}, defaults, options);
        var $container = button.parent();
        var $url = $container.find('input.media-url'),
            $id = $container.find('input.media-id'),
            $remove = $container.find('.btn-remove'),
            $preview = $container.find('.media-preview'),
            media_frame = null;
        if (!options.remove && !options.change) {
            if (button.hasClass('selecting')) {
                return;
            }
            if (!options.button_text) {
                if (options.multiple) {
                    options.button_text = hocwp.i18n.insert_media_button_texts;
                } else {
                    options.button_text = hocwp.i18n.insert_media_button_text;
                }
            }
            button.addClass('selecting');
            if (media_frame) {
                media_frame.open();
                return;
            }
            media_frame = wp.media({
                title: options.title,
                button: {
                    text: options.button_text
                },
                multiple: options.multiple
            });
            media_frame.on('select', function () {
                var media_items = hocwp.receiveSelectedMediaItems(media_frame);
                if (!options.multiple) {
                    var media_item = hocwp.getFirstMediaItemJSON(media_items);
                    if (media_item.id) {
                        $id.val(media_item.id);
                    }
                    if (media_item.url) {
                        $url.val(media_item.url);
                        $preview.html(hocwp.createImageHTML({
                            src: media_item.url,
                            id: media_item.id,
                            element: $preview
                        }));
                        button.addClass('hidden');
                        $remove.removeClass('hidden');
                    }
                    $body.trigger('hocwp_media:selected', [media_item, button, options]);
                    button.trigger('hocwp_media:selected', [media_item, options]);
                }
                button.removeClass('selecting');
            });
            media_frame.on('escape', function () {
                button.removeClass('selecting');
            });
            media_frame.open();
        } else {
            if (options.remove) {
                hocwp.mediaRemove(button, $remove, $preview, $url, $id);
            }
        }

        if (options.change) {
            hocwp.mediaChange(button, $remove, $preview, $url, $id);
        }

        $url.on('change input', function (e) {
            e.preventDefault();
            hocwp.mediaChange(button, $remove, $preview, $url, $id);
        });

        $remove.on('click', function (e) {
            e.preventDefault();
            hocwp.mediaRemove(button, $remove, $preview, $url, $id);
        });
    };

    hocwp.sortableTermStop = function (container) {
        var $input_result = container.find('.input-result'),
            $sortable_result = container.find('.connected-result'),
            value = [];
        $sortable_result.find('li').each(function (index, el) {
            var $element = $(el),
                item = {
                    id: $element.attr('data-id'),
                    taxonomy: $element.attr('data-taxonomy')
                };
            value.push(item);
        });
        value = JSON.stringify(value);
        $input_result.val(value);
        return value;
    };

    hocwp.sortablePostTypeStop = function (container) {
        var $input_result = container.find('.input-result'),
            $sortable_result = container.find('.connected-result'),
            value = [];
        $sortable_result.find('li').each(function (index, el) {
            var $element = $(el),
                item = {
                    id: $element.attr('data-id')
                };
            value.push(item);
        });
        value = JSON.stringify(value);
        $input_result.val(value);
        return value;
    };

    hocwp.sortableTaxonomyStop = function (container) {
        var $input_result = container.find('.input-result'),
            $sortable_result = container.find('.connected-result'),
            value = [];
        $sortable_result.find('li').each(function (index, el) {
            var $element = $(el),
                item = {
                    id: $element.attr('data-id')
                };
            value.push(item);
        });
        value = JSON.stringify(value);
        $input_result.val(value);
        return value;
    };

    hocwp.sortableStop = function ($element, $container) {
        var $input_result = $container.find('.input-result'),
            value = [];
        $element.find('li').each(function (index, el) {
            var $element = $(el),
                taxonomy = $element.attr('data-taxonomy'),
                item = {
                    id: $element.attr('data-id')
                };
            if (typeof taxonomy !== typeof undefined && taxonomy !== false) {
                item.taxonomy = taxonomy;
            }
            value.push(item);
        });
        value = JSON.stringify(value);
        $input_result.val(value);
        return value;
    };

    hocwp.administrativeBoundaries = function ($element, child_name, $container) {
        $container = $container || $element.closest('form');
        var $form = $container,
            $child = $form.find('select[name=' + child_name + ']'),
            $default = $child.find('option[value=0]'),
            element_name = $element.attr('name');
        if ($child.length) {
            if (!$default.length) {
                $default = $child.find('option[value=""]')
            }
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                cache: true,
                data: {
                    action: 'hocwp_fetch_administrative_boundaries',
                    parent: $element.val(),
                    taxonomy: 'category',
                    type: element_name,
                    default: $default.prop('outerHTML')
                },
                success: function (response) {
                    $child.html(response.html_data);
                }
            });
        }
    };
});

jQuery(document).ready(function ($) {
    function MediaUpload(element, options) {
        this.self = this;
        this.$element = $(element);
        if (!this.$element.length || !hocwp.is_function(wp.media)) {
            return this;
        }
        this.element = element;
        this.options = $.extend({}, MediaUpload.DEFAULTS, options);
        this.items = null;
        this.$container = this.$element.parent();
        this.$id = this.$container.find('input.media-id');
        this.$url = this.$container.find('input.media-url');
        this.$preview = this.$container.find('.media-preview');
        this.$remove = this.$container.find('.btn-remove');
        //this.$th = this.$container.prev();
        this._defaults = MediaUpload.DEFAULTS;
        this._name = MediaUpload.NAME;
        this.frame = null;

        this.init();

        this.$element.on('click', $.proxy(this.add, this));
        this.$url.on('change input', $.proxy(this.change, this));
        this.$remove.on('click', $.proxy(this.remove, this));
    }

    MediaUpload.NAME = 'hocwp.mediaUpload';

    MediaUpload.DEFAULTS = {
        title: hocwp.i18n.insert_media_title,
        button_text: null,
        multiple: false,
        hideAddButton: true
    };

    MediaUpload.prototype.init = function () {
        if (!this.options.button_text) {
            if (this.options.multiple) {
                this.options.button_text = hocwp.i18n.insert_media_button_texts;
            } else {
                this.options.button_text = hocwp.i18n.insert_media_button_text;
            }
        }
    };

    MediaUpload.prototype.selected = function () {
        this.items = hocwp.receiveSelectedMediaItems(this.frame);
        if (!this.options.multiple) {
            var media_item = hocwp.getFirstMediaItemJSON(this.items);
            if (media_item.id) {
                this.$id.val(media_item.id);
            }
            if (media_item.url) {
                this.$url.val(media_item.url);
                this.$preview.html(hocwp.createImageHTML({
                    src: media_item.url,
                    id: media_item.id,
                    element: this.$preview
                }));
                if (this.$remove.length && this.options.hideAddButton) {
                    this.$element.addClass('hidden');
                }
                this.$remove.removeClass('hidden');
            }
            $('body').trigger('hocwp_media:selected', [media_item, this.$element, this.options]);
            this.$element.trigger('hocwp_media:selected', [media_item, this.options]);
        }
        this.$element.removeClass('selecting');
    };

    MediaUpload.prototype.remove = function (e) {
        e.preventDefault();
        this.$preview.html('');
        this.$url.val('');
        this.$id.val('');
        this.$remove.addClass('hidden');
        this.$element.removeClass('hidden');
    };

    MediaUpload.prototype.add = function (e) {
        e.preventDefault();
        var $element = this.$element;
        if (this.$element.hasClass('selecting')) {
            return;
        }
        this.$element.addClass('selecting');
        if (this.frame) {
            this.frame.open();
            return;
        }
        this.frame = wp.media({
            title: this.options.title,
            button: {
                text: this.options.button_text
            },
            multiple: this.options.multiple
        });
        this.frame.on('select', $.proxy(this.selected, this));
        this.frame.on('escape', function () {
            $element.removeClass('selecting');
        });
        this.frame.open();
    };

    MediaUpload.prototype.change = function (e) {
        e.preventDefault();
        if (hocwp.isImageUrl(this.$url.val())) {
            this.$preview.html(hocwp.createImageHTML({
                src: this.$url.val(),
                id: this.$id.val(),
                element: this.$preview
            }));
        } else {
            this.$preview.html('');
        }
        this.$id.val('');
    };

    $.fn.hocwpMediaUpload = function (options) {
        return this.each(function () {
            if (!$.data(this, MediaUpload.NAME)) {
                $.data(this, MediaUpload.NAME, new MediaUpload(this, options));
            }
        });
    };
});

jQuery(document).ready(function ($) {
    function ScrollTop(element, options) {
        var $window = $(window),
            current_pos = $window.scrollTop();
        this.self = this;
        this.$element = $(element);
        if (!this.$element.length) {
            return this;
        }
        this.element = element;
        this.options = $.extend({}, ScrollTop.DEFAULTS, options);
        this._defaults = ScrollTop.DEFAULTS;
        this._name = ScrollTop.NAME;

        this.init();

        var pos_to_show = this.options.posToShow,
            $element = this.$element;

        if (current_pos >= pos_to_show) {
            $element.fadeIn();
        }

        $window.scroll(function () {
            if ($(this).scrollTop() >= pos_to_show) {
                $element.fadeIn();
            } else {
                $element.fadeOut();
            }
        });

        $element.on('click', $.proxy(this.click, this));
    }

    ScrollTop.NAME = 'hocwp.scrollTop';

    ScrollTop.DEFAULTS = {
        posToShow: 100
    };

    ScrollTop.prototype.init = function () {

    };

    ScrollTop.prototype.click = function (e) {
        e.preventDefault();
        hocwp.scrollToTop();
    };

    $.fn.hocwpScrollTop = function (options) {
        return this.each(function () {
            if (!$.data(this, ScrollTop.NAME)) {
                $.data(this, ScrollTop.NAME, new ScrollTop(this, options));
            }
        });
    };
});

jQuery(document).ready(function ($) {
    var $body = $('body');

    function SortableList(element, options) {
        this.self = this;
        this.$element = $(element);
        if (!this.$element.length || !hocwp.is_function(jQuery().sortable)) {
            return this;
        }
        this.element = element;
        this.options = $.extend({}, SortableList.DEFAULTS, options);
        this._defaults = SortableList.DEFAULTS;
        this._name = SortableList.NAME;
        if (this.$element.hasClass('manage-column')) {
            return;
        }
        this.init();
        var $element = this.$element,
            data_disable_selection = $element.attr('data-disable-selection'),
            $container = $element.parent(),
            $sortable_result = $element.next(),
            sortable_options = {
                placeholder: 'ui-state-highlight',
                sort: function (event, ui) {
                    var that = $(this),
                        $sortable_result = $container.find('.connected-result'),
                        ui_state_highlight = that.find('.ui-state-highlight');
                    if ($sortable_result.length) {
                        $sortable_result.css({'height': 'auto'});
                    }
                    ui_state_highlight.css({'height': ui.item.height()});
                    if (that.hasClass('display-inline')) {
                        ui_state_highlight.css({'width': ui.item.width()});
                    }
                    $('body').trigger('hocwp_sortable:sort', [ui, $(this)]);
                },
                stop: function (event, ui) {
                    var $sortable_result = $container.find('.connected-result'),
                        element_height = $element.height(),
                        sortable_result_height = $sortable_result.height();
                    if ($sortable_result.length) {
                        if ($sortable_result.hasClass('term-sortable')) {
                            hocwp.sortableTermStop($container);
                        } else if ($sortable_result.hasClass('post-type-sortable')) {
                            hocwp.sortablePostTypeStop($container);
                        } else if ($sortable_result.hasClass('taxonomy-sortable')) {
                            hocwp.sortableTaxonomyStop($container);
                        }
                    } else {
                        hocwp.sortableStop($element, $container);
                    }
                    if (element_height >= sortable_result_height) {
                        $sortable_result.css({'height': element_height});
                    } else {
                        $sortable_result.css({'height': 'auto'});
                    }
                    $body.trigger('hocwp_sortable:stop', [ui, $(this)]);
                    $body.trigger('hocwp:sortable_stop', [ui, $(this)]);
                }
            };
        if ($sortable_result.length && $sortable_result.hasClass('sortable')) {
            var element_height = $element.height(),
                sortable_result_height = $sortable_result.height();
            if (element_height >= sortable_result_height) {
                $sortable_result.css({'height': element_height});
            } else {
                $sortable_result.css({'height': 'auto'});
            }
        }
        if ($element.hasClass('connected-list')) {
            sortable_options.connectWith = '.connected-list';
        }
        sortable_options = $.extend({}, sortable_options, this.options);
        if ($.isNumeric(data_disable_selection)) {
            sortable_options.disableSelection = (0 != data_disable_selection);
        }
        if (sortable_options.disableSelection) {
            $element.sortable(sortable_options).disableSelection();
        } else {
            $element.sortable(sortable_options);
        }
    }

    SortableList.NAME = 'hocwp.sortableList';

    SortableList.DEFAULTS = {
        cancel: ':input, .ui-state-disabled, .icon-delete',
        disableSelection: true
    };

    SortableList.prototype.init = function () {

    };

    $.fn.hocwpSortable = function (options) {
        return this.each(function () {
            if (!$.data(this, SortableList.NAME)) {
                $.data(this, SortableList.NAME, new SortableList(this, options));
            }
        });
    };
});

jQuery(document).ready(function ($) {
    var $body = $('body');

    function MobileMenu(element, options) {
        var $window = $(window),
            $body = $('body'),
            current_width = $window.width();
        this.self = this;
        this.$element = $(element);
        if (!this.$element.length) {
            return this;
        }
        this.element = element;
        this.options = $.extend({}, MobileMenu.DEFAULTS, options);
        this._defaults = MobileMenu.DEFAULTS;
        this._name = MobileMenu.NAME;
        if (this.options.displayWidth < $window.width()) {
            return this;
        }
        this.init();
        var $element = this.$element,
            menu_options = this.options,
            $menu_parent = $element.parent(),
            $mobile_menu_button = this.options.mobileButton,
            $search_form = $menu_parent.find('.search-form'),
            display_width = parseFloat(this.options.displayWidth),
            height = parseInt(this.options.height),
            body_height = $body.height(),
            force_search_form = this.options.forceSearchForm,
            fit_window_height = this.options.fitWindowHeight,
            search_form_added = false;
        if (null == $mobile_menu_button || !$mobile_menu_button.length) {
            $mobile_menu_button = $menu_parent.find('.mobile-menu-button');
        }
        this.element_class = $element.attr('class');
        this.html = $element.html();
        var html = this.html,
            menu_class = this.element_class,
            position = this.options.position,
            window_resized = false;

        function hocwp_update_mobile_menu() {
            if (fit_window_height) {
                body_height = $(window).height();
            }
            $element.removeClass('sf-menu sf-js-enabled');
            $element.find('li.menu-item-has-children').not('.appended').addClass('appended').append('<i class="fa fa-plus"></i>');
            $element.css({height: body_height});
            $element.show();
            $element.addClass(position);
            $element.addClass('hocwp-mobile-menu');
            if (null == $mobile_menu_button || !$mobile_menu_button.length) {
                $menu_parent.append(hocwp.mobile_menu_icon);
                $mobile_menu_button = $menu_parent.find('.mobile-menu-button');
                $mobile_menu_button.attr('aria-controls', $element.attr('id'))
            }
            if (!search_form_added && (!$search_form.length || force_search_form)) {
                if (!search_form_added && (!$element.find('li.search-item').length || force_search_form)) {
                    $element.prepend('<li class="search-item menu-item" style="overflow: hidden">' + hocwp.search_form + '</li>');
                    search_form_added = true;
                }
            }
            $mobile_menu_button.css({'line-height': height + 'px'});
            $mobile_menu_button.show();

            var mobile_menu_button_left = parseInt($mobile_menu_button.css('left'));
            if (!$.isNumeric(mobile_menu_button_left)) {
                mobile_menu_button_left = 10;
                $mobile_menu_button.css({'left': '10px', 'position': 'absolute'});
            }

            if (null != menu_options.mobileButton && menu_options.mobileButton.length) {
                $mobile_menu_button.on('click', function (e) {
                    e.stopPropagation();
                    $element.toggleClass('active');
                    if ($element.hasClass('active')) {
                        $mobile_menu_button.css({'left': 250 + 'px'});
                    } else {
                        $mobile_menu_button.css({'left': mobile_menu_button_left + 'px'});
                    }
                });
                $body.on('click', function () {
                    $element.removeClass('active');
                    $mobile_menu_button.css({'left': mobile_menu_button_left + 'px'});
                });

                $menu_parent.on('click', function (e) {
                    e.stopPropagation();
                    if (e.target == this) {
                        $element.toggleClass('active');
                        if (!$element.hasClass('active')) {
                            $mobile_menu_button.css({'left': mobile_menu_button_left + 'px'});
                        }
                    }
                });
            } else {
                $menu_parent.off('click', '.mobile-menu-button').on('click', '.mobile-menu-button', function (e) {
                    e.stopPropagation();
                    $element.toggleClass('active');
                });

                $body.on('click', function () {
                    $element.removeClass('active');
                });

                $menu_parent.off('click', '.hocwp-mobile-menu').on('click', '.hocwp-mobile-menu', function (e) {
                    e.stopPropagation();
                    if (e.target == this) {
                        $element.toggleClass('active');
                    }
                });
            }

            $element.find('.search-field').on('click', function (e) {
                e.preventDefault();
            });

            if ($body.hasClass('jquery-mobile')) {
                $menu_parent.on('swipeleft', '.hocwp-mobile-menu', function (e) {
                    e.preventDefault();
                    $element.removeClass('active');
                });
            }

            $element.find('li.menu-item-has-children .fa').off('click').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var $this = $(this),
                    $current_li = $this.parent(),
                    $sub_menu = $current_li.children('.sub-menu');
                if ($this.hasClass('active')) {
                    $sub_menu.stop(true, false, true).slideUp();
                    $this.removeClass('fa-minus');
                    $this.addClass('fa-plus');
                    $current_li.find('.fa-minus').each(function () {
                        $(this).removeClass('fa-minus active').addClass('fa-plus');
                    });
                    $current_li.find('.sub-menu').not($sub_menu).hide();
                } else {
                    $this.removeClass('fa-plus');
                    $this.addClass('fa-minus');
                    $sub_menu.stop(true, false, true).slideDown();
                }
                $this.toggleClass('active');
            });

            $window.scroll(function () {
                var pos = $(this).scrollTop(),
                    $admin_bar = $('#wpadminbar'),
                    admin_bar_height = 0;
                if ($admin_bar.length) {
                    admin_bar_height = $admin_bar.height();
                }
                if (pos < 100) {
                    pos = admin_bar_height;
                }
                if (pos == admin_bar_height) {
                    $element.css({'top': pos + 'px'});
                } else {
                    $element.css({'top': '-' + pos + 'px'});
                }
            });
        }

        if (current_width > display_width) {
            if (!window_resized) {
                $window.on('resize', function () {
                    window_resized = true;
                    current_width = $window.width();
                    if (current_width > display_width) {
                        if ($element.hasClass('hocwp-mobile-menu')) {
                            $element.attr('class', menu_class);
                            $element.attr('style', '');
                            $element.html(html);
                            location.reload();
                        }
                    } else {
                        hocwp_update_mobile_menu();
                    }
                });
            }
            return this;
        }

        if (current_width <= display_width) {
            hocwp_update_mobile_menu();
        }

        if (!window_resized) {
            $window.on('resize', function () {
                window_resized = true;
                current_width = $window.width();
                if (current_width > display_width) {
                    if ($element.hasClass('hocwp-mobile-menu')) {
                        $element.attr('class', menu_class);
                        $element.attr('style', '');
                        $element.html(html);
                        location.reload();
                    }
                } else {
                    hocwp_update_mobile_menu();
                }
            });
        }
        $element.closest('.navigation').show();
    }

    MobileMenu.NAME = 'hocwp.mobileMenu';

    MobileMenu.DEFAULTS = {
        displayWidth: 980,
        position: 'left',
        height: 30,
        forceSearchForm: false,
        fitWindowHeight: false,
        mobileButton: null
    };

    MobileMenu.prototype.init = function () {
        if (!this.$element.is('ul')) {
            this.$element = this.$element.find('ul');
        }
        this.$element.css({position: 'fixed'});
        $body.addClass('responsive');
    };

    MobileMenu.prototype.click = function (e) {
        e.preventDefault();
        hocwp.scrollToTop();
    };

    $.fn.hocwpMobileMenu = function (options) {
        return this.each(function () {
            if (!$.data(this, MobileMenu.NAME)) {
                $.data(this, MobileMenu.NAME, new MobileMenu(this, options));
            }
        });
    };
});

jQuery(document).ready(function ($) {
    function ChosenSelect(element, options) {
        this.self = this;
        this.$element = $(element);
        if (!this.$element.length || !hocwp.is_function(jQuery().chosen)) {
            return this;
        }
        this.element = element;
        this.options = $.extend({}, ChosenSelect.DEFAULTS, options);
        this._defaults = ChosenSelect.DEFAULTS;
        this._name = ChosenSelect.NAME;
        this.multiple = this.$element.attr('multiple');
        this.init();
        var $element = this.$element,
            loaded = parseInt(this.$element.attr('data-loaded')),
            chosen_params = {
                width: this.options.width || '100%'
            };
        if (1 == loaded) {
            this.$element.parent().find('.chosen-container').remove();
        }
        if ('multiple' == this.multiple) {
            this.$element.chosen(chosen_params).on('change', function () {
                hocwp.chosenSelectUpdated($element);
            });
        } else {
            this.$element.chosen(chosen_params);
        }
        this.$element.parent().find('.chosen-container').show();
    }

    ChosenSelect.NAME = 'hocwp.chosenSelect';

    ChosenSelect.DEFAULTS = {
        displayWidth: 980,
        position: 'left'
    };

    ChosenSelect.prototype.init = function () {
        var $element_parent = this.$element.parent(),
            $next_element = $element_parent.next();
        if ($next_element.hasClass('chosen-container')) {
            $next_element.remove();
        }
        this.$element.addClass('hocwp-chosen-select');
        this.$element.attr('data-loaded', 1);
    };

    $.fn.hocwpChosenSelect = function (options) {
        return this.each(function () {
            if (!$.data(this, ChosenSelect.NAME)) {
                $.data(this, ChosenSelect.NAME, new ChosenSelect(this, options));
            }
        });
    };
});

jQuery(document).ready(function ($) {
    function PostRating(element, options) {
        this.self = this;
        this.$element = $(element);
        if (!this.$element.length || !hocwp.is_function(jQuery().raty)) {
            return this;
        }
        this.element = element;
        this.options = $.extend({}, PostRating.DEFAULTS, options);
        this._defaults = PostRating.DEFAULTS;
        this._name = PostRating.NAME;
        this.multiple = this.$element.attr('multiple');
        this.init();
        var $element = this.$element;
        $element.raty(this.options);
    }

    PostRating.NAME = 'hocwp.postRating';

    PostRating.DEFAULTS = {
        score: function () {
            return $(this).attr('data-score');
        },
        path: function () {
            return this.getAttribute('data-path');
        },
        number: parseInt($(this).attr('data-number')),
        numberMax: parseInt($(this).attr('data-number-max')),
        readOnly: function () {
            var readonly = parseInt($(this).attr('data-readonly'));
            return readonly == 1;
        },
        click: function (score, e) {
            var $element = $(this),
                post_id = parseInt(this.getAttribute('data-id'));
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                cache: true,
                data: {
                    action: 'hocwp_rate_post',
                    post_id: post_id,
                    score: score,
                    number: $element.attr('data-number'),
                    number_max: $element.attr('data-number-max')
                },
                success: function (response) {
                    if (response.success) {
                        var refresh = parseInt($element.attr('data-refresh'));
                        if (1 == refresh) {
                            $element.attr('data-score', response.score);
                        } else {
                            $element.attr('data-score', score);
                        }
                        $element.attr('data-readonly', 1);
                        $element.raty(options);
                    }
                }
            });
        }
    };

    PostRating.prototype.init = function () {

    };

    $.fn.hocwpPostRating = function (options) {
        return this.each(function () {
            if (!$.data(this, PostRating.NAME)) {
                $.data(this, PostRating.NAME, new PostRating(this, options));
            }
        });
    };
});

jQuery(document).ready(function ($) {
    var $body = $('body');

    function GoogleMaps(element, options) {
        this.self = this;
        this.$element = $(element);
        if (!this.$element.length || !$body.hasClass('hocwp-google-maps')) {
            return this;
        }
        this.element = element;
        this.options = $.extend({}, GoogleMaps.DEFAULTS, options);
        this._defaults = GoogleMaps.DEFAULTS;
        this._name = GoogleMaps.NAME;
        this.init();
        var $element = this.$element,
            $google_maps = $body.find('#google_maps'),
            $geo_address = $body.find('.hocwp-geo-address'),
            $province = $body.find('select[name=province]'),
            $category_list = $('.classifieds.hocwp-google-maps #categorychecklist'),
            lat_long = new google.maps.LatLng($element.attr('data-lat'), $element.attr('data-long')),
            map_options = {
                zoom: parseInt($element.attr('data-zoom')),
                center: lat_long,
                scrollwheel: $element.attr('data-scrollwheel')
            },
            map = new google.maps.Map(document.getElementById($element.attr('id')), map_options),
            marker = new google.maps.Marker({
                position: lat_long,
                map: map,
                draggable: true,
                title: $element.attr('data-marker-title')
            }),
            point = marker.getPosition();
        google.maps.event.addListener(marker, 'dragend', function (event) {
            point = marker.getPosition();
            map.panTo(point);
            if ($google_maps.length) {
                $google_maps.val(JSON.stringify(point));
            }
            $element.attr('data-lat', point.lat);
            $element.attr('data-long', point.lng);
        });
        var geocoder = new google.maps.Geocoder();
        if ($geo_address.length) {
            $geo_address.on('change', function (e) {
                e.preventDefault();
                if ($.trim($geo_address.val())) {
                    if (geocoder == null) {
                        geocoder = new google.maps.Geocoder();
                    }
                    geocoder.geocode({address: $geo_address.val()}, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            var bounds = results[0].geometry.bounds;
                            if (bounds) {
                                map.fitBounds(bounds);
                                map.setZoom(14);
                                lat_long = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
                                marker.setPosition(lat_long);
                                point = marker.getPosition();
                                if ($google_maps.length) {
                                    $google_maps.val(JSON.stringify(point));
                                }
                                map.setCenter(point);
                                google.maps.event.addListener(marker, 'dragend', function (event) {
                                    point = marker.getPosition();
                                    map.panTo(point);
                                    if ($google_maps.length) {
                                        $google_maps.val(JSON.stringify(point));
                                    }
                                    $element.attr('data-lat', point.lat);
                                    $element.attr('data-long', point.lng);
                                });
                            }
                        }
                    });
                }
            });
        }
        if ($category_list.length) {
            $category_list.find('input[type="checkbox"]').on('change', function (e) {
                e.preventDefault();
                var $input_category = $(this);
                if ($input_category.is(':checked')) {
                    if (geocoder == null) {
                        geocoder = new google.maps.Geocoder();
                    }
                    if (!$.trim($geo_address.val())) {
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: hocwp.ajax_url,
                            cache: true,
                            data: {
                                action: 'hocwp_get_term_administrative_boundaries_address',
                                term_id: $input_category.val(),
                                taxonomy: 'category'
                            },
                            success: function (response) {
                                if ($.trim(response.address)) {
                                    geocoder.geocode({address: response.address}, function (results, status) {
                                        if (status == google.maps.GeocoderStatus.OK) {
                                            var bounds = results[0].geometry.bounds;
                                            if (bounds) {
                                                map.fitBounds(bounds);
                                                map.setZoom(14);
                                                lat_long = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
                                                marker.setPosition(lat_long);
                                                point = marker.getPosition();
                                                if ($google_maps.length) {
                                                    $google_maps.val(JSON.stringify(point));
                                                }
                                                map.setCenter(point);
                                                google.maps.event.addListener(marker, 'dragend', function (event) {
                                                    point = marker.getPosition();
                                                    map.panTo(point);
                                                    if ($google_maps.length) {
                                                        $google_maps.val(JSON.stringify(point));
                                                    }
                                                    $element.attr('data-lat', point.lat);
                                                    $element.attr('data-long', point.lng);
                                                });
                                            }
                                        }
                                    });
                                }
                            }
                        });
                    }
                }
            });
        }
        if ($province.length) {
            var $district = $body.find('select[name=district]'),
                $ward = $body.find('select[name=ward]'),
                $street = $body.find('select[name=street]');
            $province.add($district).add($ward).add($street).on('change', function (e) {
                e.preventDefault();
                var term_id = $(this).val();
                if ($.isNumeric(term_id) && term_id > 0) {
                    if (geocoder == null) {
                        geocoder = new google.maps.Geocoder();
                    }
                    if (!$.trim($geo_address.val())) {
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: hocwp.ajax_url,
                            cache: true,
                            data: {
                                action: 'hocwp_get_term_administrative_boundaries_address',
                                term_id: term_id,
                                taxonomy: 'category'
                            },
                            success: function (response) {
                                if ($.trim(response.address)) {
                                    geocoder.geocode({address: response.address}, function (results, status) {
                                        if (status == google.maps.GeocoderStatus.OK) {
                                            var bounds = results[0].geometry.bounds;
                                            if (bounds) {
                                                map.fitBounds(bounds);
                                                map.setZoom(14);
                                                lat_long = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
                                                marker.setPosition(lat_long);
                                                point = marker.getPosition();
                                                if ($google_maps.length) {
                                                    $google_maps.val(JSON.stringify(point));
                                                }
                                                map.setCenter(point);
                                                google.maps.event.addListener(marker, 'dragend', function (event) {
                                                    point = marker.getPosition();
                                                    map.panTo(point);
                                                    if ($google_maps.length) {
                                                        $google_maps.val(JSON.stringify(point));
                                                    }
                                                    $element.attr('data-lat', point.lat);
                                                    $element.attr('data-long', point.lng);
                                                });
                                            }
                                        }
                                    });
                                }
                            }
                        });
                    }
                }
            });
        }
    }

    GoogleMaps.NAME = 'hocwp.googleMaps';

    GoogleMaps.DEFAULTS = {};

    GoogleMaps.prototype.init = function () {

    };

    $.fn.hocwpGoogleMaps = function (options) {
        return this.each(function () {
            if (!$.data(this, GoogleMaps.NAME)) {
                $.data(this, GoogleMaps.NAME, new GoogleMaps(this, options));
            }
        });
    };
});

jQuery(document).ready(function ($) {
    $.fn.hocwpShow = function (show, fade) {
        var that = $(this);
        fade = fade || false;
        if (show) {
            if (fade) {
                that.addClass('active').fadeIn();
            } else {
                that.addClass('active').show();
            }
        } else {
            if (fade) {
                that.removeClass('active').fadeOut();
            } else {
                that.removeClass('active').hide();
            }
        }
    };

    $.fn.hocwpExternalLinkFilter = function () {
        var that = $(this);
        that.filter(function () {
            return this.hostname && this.hostname !== location.hostname;
        }).addClass('external');
    };
});

jQuery(document).ready(function ($) {
    var $body = $('body');

    function HocWPSlider(element, options) {
        this.self = this;
        this.$element = $(element);
        if (!this.$element.length || !hocwp.is_function(jQuery().slick)) {
            return this;
        }
        this.element = element;
        this.options = $.extend({}, HocWPSlider.DEFAULTS, options);
        this._defaults = HocWPSlider.DEFAULTS;
        this._name = HocWPSlider.NAME;
        this.init();
        var $element = this.$element,
            fit_width = parseInt($element.attr('data-fit-width')),
            height = parseInt($element.attr('data-height'));
        if (!$.isNumeric(height)) {
            height = this.options.height;
        }
        if (this.options.fitWidth || ($.isNumeric(fit_width) && 1 == fit_width)) {
            $element.find('img.slider-image').css({width: '100%'});
        }
        $element.find('img.slider-image').css({height: height + 'px'});
        $element.addClass('hocwp-slider');
        if (this.options.customArrow) {
            $element.addClass('custom-arrow');
        }
        if (this.options.fontAwesome) {
            $element.addClass('font-awesome');
        }
        if (this.options.thumbnailPager || $element.hasClass('thumbnail-pager')) {
            this.options.dots = true;
            $element.addClass('thumbs-paging thumbnail-pager');
            this.options.customPaging = function (slider, i) {
                var $slick_thumbs = $element.find('.slick-thumbs');
                $slick_thumbs.hide();
                return '<button class="tab">' + $('.slick-thumbs li:nth-child(' + (i + 1) + ')').html() + '</button>';
            };
        }
        if (!this.options.autoplay) {
            var data_autoplay = parseInt($element.attr('data-autoplay'));
            if (1 === data_autoplay || $element.hasClass('autoplay')) {
                this.options.autoplay = true;
            }
        }
        $element.slick(this.options);
        $element.show();
        if (!this.options.useLazyLoad) {
            var data_use_lazyload = parseInt($element.attr('data-use-lazyload'));
            if (1 == data_use_lazyload || $element.hasClass('use-lazyload')) {
                this.options.useLazyLoad = true;
            }
        }
        if (this.options.useLazyLoad && hocwp.is_function(jQuery().lazyload)) {
            $element.on('lazyload afterChange', function (event, slick, currentSlide, nextSlide) {
                $element.find('.lazy').lazyload();
            });
        }
    }

    HocWPSlider.NAME = 'hocwp.slider';

    HocWPSlider.DEFAULTS = {
        thumbnailPager: 0,
        useLazyLoad: 0,
        customArrow: 0,
        fontAwesome: 0,
        height: 350
    };

    HocWPSlider.prototype.init = function () {
        this.$element.hide();
    };

    $.fn.hocwpSlider = function (options) {
        return this.each(function () {
            if (!$.data(this, HocWPSlider.NAME)) {
                $.data(this, HocWPSlider.NAME, new HocWPSlider(this, options));
            }
        });
    };
});

jQuery(document).ready(function ($) {
    var $body = $('body'),
        $window = $(window);

    function HocWPFixed(element, options) {
        this.self = this;
        this.$element = $(element);
        if (!this.$element.length) {
            return this;
        }
        this.element = element;
        this.options = $.extend({}, HocWPFixed.DEFAULTS, options);
        this._defaults = HocWPFixed.DEFAULTS;
        this._name = HocWPFixed.NAME;
        this.init();
        var $element = this.$element,
            settings = this.options,
            element_width = $element.width(),
            current_pos = $window.scrollTop(),
            element_pos = $element.offset().top,
            backup_style = $element.attr('style'),
            backup_class = $element.attr('class'),
            $admin_bar = $('#wpadminbar'),
            window_width = $window.width(),
            top = 0;
        if (!backup_style) {
            backup_style = '';
        }
        if (!backup_class) {
            backup_class = '';
        }
        if ($admin_bar.length) {
            top += $admin_bar.height();
        }
        if (settings.anchorTop && $.isNumeric(settings.anchorTop)) {
            element_pos = settings.anchorTop;
        }

        function hocwp_fix_item_helper(current_pos, element_pos) {
            if (current_pos > element_pos) {
                var css = {
                    position: 'fixed',
                    display: 'block',
                    zIndex: 599,
                    top: top,
                    left: 0,
                    right: 0
                };
                $element.addClass('fixed');
                if (settings.autoStretch) {
                    var $element_inner = $element.children(':first');
                    css.left = 0;
                    css.right = 0;
                    if (window_width > settings.changeInnerWidthMin) {
                        $element_inner.css({
                            width: element_width + 'px',
                            marginLeft: 'auto',
                            marginRight: 'auto'
                        });
                    }
                }
                $element.css(css);
            } else {
                $element.attr('style', backup_style);
                $element.attr('class', backup_class);
            }
        }

        hocwp_fix_item_helper(current_pos, element_pos);

        $window.scroll(function () {
            current_pos = $window.scrollTop();
            hocwp_fix_item_helper(current_pos, element_pos);
        });
    }

    HocWPFixed.NAME = 'hocwp.fixed';

    HocWPFixed.DEFAULTS = {
        autoStretch: false,
        changeInnerWidthMin: 480,
        anchorTop: null
    };

    HocWPFixed.prototype.init = function () {
        this.$element.addClass('hocwp-fixed-plugin');
    };

    $.fn.hocwpFixed = function (options) {
        return this.each(function () {
            if (!$.data(this, HocWPFixed.NAME)) {
                $.data(this, HocWPFixed.NAME, new HocWPFixed(this, options));
            }
        });
    };
});

jQuery(document).ready(function ($) {
    var $body = $('body'),
        $window = $(window);

    function DropdownChosen(element, options) {
        this.self = this;
        this.$element = $(element);
        if (!this.$element.length) {
            return this;
        }
        this.element = element;
        this.options = $.extend({}, DropdownChosen.DEFAULTS, options);
        this._defaults = DropdownChosen.DEFAULTS;
        this._name = DropdownChosen.NAME;
        this.init();
        var $element = this.$element,
            settings = this.options,
            $input = $element.children('input[type="hidden"]'),
            $text = $element.children('.text'),
            $menu = $element.children('.menu'),
            $items = $menu.children('.item');
        $text.css({
            cursor: 'pointer',
            display: 'inline-block'
        });
        $element.css({
            width: '1%',
            whiteSpace: 'nowrap'
        });
        $text.on('click', function (e) {
            e.preventDefault();
            $element.toggleClass('active');
        });
        $items.on('click', function (e) {
            e.preventDefault();
            var new_item = $(this).attr('data-id');
            $element.toggleClass('active');
            $menu.find('.item').removeClass('active');
            $(this).addClass('active');
            $input.val(new_item);
            $text.attr('data-id', new_item);
            $text.html($(this).html());
            //$element.css({width: 'auto'});
        });
    }

    DropdownChosen.NAME = 'hocwp.dropdownChosen';

    DropdownChosen.DEFAULTS = {};

    DropdownChosen.prototype.init = function () {
        this.$element.addClass('hocwp-dropdown-chosen');
    };

    $.fn.hocwpDropdownChosen = function (options) {
        return this.each(function () {
            if (!$.data(this, DropdownChosen.NAME)) {
                $.data(this, DropdownChosen.NAME, new DropdownChosen(this, options));
            }
        });
    };
});

jQuery(document).ready(function ($) {
    (function () {
        $('.btn-insert-media').hocwpMediaUpload();
    })();

    (function () {
        $('.hocwp-geo-address').on('input', function () {
            $(this).addClass('user-type-address');
            $(this).attr('data-user-type', 1);
        });
    })();
});