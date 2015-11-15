window.wp = window.wp || {};
window.hocwp = window.hocwp || {};

if(typeof jQuery === 'undefined') {
    throw new Error(hocwp.i18n.jquery_undefined_error)
}

jQuery(document).ready(function($) {
    'use strict';

    var version = $.fn.jquery.split(' ')[0].split('.');
    if((version[0] < 2 && version[1] < 9) || (version[0] == 1 && version[1] == 9 && version[2] < 1)) {
        throw new Error(hocwp.i18n.jquery_version_error)
    }
});

hocwp.media_frame = null;
hocwp.media_items = {};

jQuery(document).ready(function($) {
    'use strict';

    hocwp.getParamByName = function(url, name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(url);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    };

    hocwp.receiveSelectedMediaItems = function(file_frame) {
        return file_frame.state().get('selection');
    };

    hocwp.receiveSelectedMediaItem = function(file_frame) {
        var items = hocwp.receiveSelectedMediaItems(file_frame);
        return items.first().toJSON();
    };

    hocwp.isImageUrl = function(url) {
        if(!$.trim(url)) {
            return false;
        }
        var result = true,
            extension = url.slice(-4);
        if(extension != '.png' && extension != '.jpg' && extension != '.gif' && extension != '.bmp' && extension != 'jpeg') {
            if(extension != '.ico') {
                result = false;
            }
        }
        return result;
    };

    hocwp.isUrl = function(text) {
        var url_regex = new RegExp('^(http:\/\/www.|https:\/\/www.|ftp:\/\/www.|www.){1}([0-9A-Za-z]+\.)');
        return url_regex.test(text);
    };

    hocwp.isArray = function(variable){
        return (Object.prototype.toString.call(variable) === '[object Array]');
    };

    hocwp.getFirstMediaItemJSON = function(media_items) {
        return media_items.first().toJSON();
    };

    hocwp.createImageHTML = function(args) {
        args = args || {};
        var alt = args.alt || '',
            src = args.src || '';
        if($.trim(src)) {
            return '<img src="' + src + '" alt="' + alt + '">';
        }
    };

    hocwp.autoReloadPageNoActive = function(reload_time, delay_time) {
        reload_time = reload_time || 60000;
        delay_time = delay_time || 10000;
        var time = new Date().getTime();
        $(document.body).bind('mousemove keypress', function() {
            time = new Date().getTime();
        });
        function refresh() {
            if(new Date().getTime() - time >= reload_time) {
                window.location.reload(true);
            } else {
                setTimeout(refresh, delay_time);
            }
        }
        setTimeout(refresh, delay_time);
    };

    hocwp.setCookie = function(cname, cvalue, exmin) {
        var d = new Date();
        d.setTime(d.getTime() + (exmin * 60 * 1000));
        var expires = "expires=" + d.toGMTString(),
            my_cookies;
        my_cookies = cname + "=" + cvalue + "; " + expires + "; path=/";
        document.cookie = my_cookies;
    };

    hocwp.formatNumber = function(number, separator, currency) {
        currency = currency || ' ₫';
        separator = separator || ',';
        var number_string = number.toString(),
            decimal = '.',
            numbers = number_string.split('.'),
            result = '';
        if(!hocwp.isArray(numbers)) {
            numbers = number_string.split(',');
            decimal = ',';
        }
        if(hocwp.isArray(numbers)) {
            number_string = numbers[0];
        }
        var number_len = parseInt(number_string.length);
        var last = number_string.slice(-3);
        if(number_len > 3) {
            result += separator + last;
        } else {
            result += last;
        }

        while(number_len > 3) {
            number_len -= 3;
            number_string = number_string.slice(0, number_len);
            last = number_string.slice(-3);

            if(number_len <= 3) {
                result = last + result;
            } else {
                result = separator + last + result;
            }
        }
        if(hocwp.isArray(numbers) && $.isNumeric(numbers[1])) {
            result += decimal + numbers[1];
        }
        result += currency;
        result = $.trim(result);
        return result;
    };

    hocwp.scrollToPosition = function(pos, time) {
        time = time || 1000;
        $('html, body').stop().animate({scrollTop: pos}, time);
    };

    hocwp.goToTop = function() {
        hocwp.scrollToPosition(0);
        return false;
    };

    hocwp.scrollToTop = function() {
        hocwp.goToTop();
    };

    hocwp.isEmail = function(email) {
        return this.test(email, '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$');
    };

    hocwp.isEmpty = function(text) {
        return text.trim();
    };

    hocwp.switcherAjax = function() {
        $('.hocwp-switcher-ajax .icon-circle').on('click', function(e) {
            e.preventDefault();
            var $element = $(this),
                opacity = '0.5';
            if($element.hasClass('icon-circle-success')) {
                opacity = '0.25';
            }
            $element.css({'opacity' : opacity});
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                data: {
                    action: 'hocwp_switcher_ajax',
                    post_id: $element.attr('data-id'),
                    value: $element.attr('data-value'),
                    key: $element.attr('data-key')
                },
                success: function(response){
                    if(response.success) {
                        $element.toggleClass('icon-circle-success');
                    }
                    $element.css({'opacity' : '1'});
                }
            });
        });
    };

    hocwp.chosenSelectUpdated = function(el) {
        var $element = el,
            values = $element.chosen().val();
        var $parent = $element.parent(),
            $result = $parent.find('.chosen-result');
        if(null == values) {
            $result.val('');
            return;
        }
        var new_value = [],
            taxonomy = null,
            $option = null,
            i = 0,
            count_value = values.length,
            is_term = false;
        for(i; i <= count_value; i++) {
            var current_value = values[i],
                new_item = {value: current_value};
            $option = $parent.find('option[value="' + current_value + '"]');
            taxonomy = $option.attr('data-taxonomy');
            if($.trim(taxonomy)) {
                new_item.taxonomy = taxonomy;
                is_term = true;
            }
            new_value.push(new_item);
        }
        $result.val(JSON.stringify(new_value));
    };

    hocwp.mediaRemove = function(upload, remove, preview, url, id) {
        preview.html('');
        url.val('');
        id.val('');
        remove.addClass('hidden');
        upload.removeClass('hidden');
    };

    hocwp.mediaChange = function(upload, remove, preview, url, id) {
        if(hocwp.isImageUrl(url.val())) {
            preview.html(hocwp.createImageHTML({src: url.val()}));
        } else {
            preview.html('');
        }
        id.val('');
    };

    hocwp.mediaUpload = function(button, options) {
        var defaults = {
            title: hocwp.i18n.insert_media_title,
            button_text: null,
            multiple: false,
            remove: false,
            change: false
        };
        options = options || {};
        options = $.extend({}, defaults, options);
        var $container = button.parent(),
            $url = $container.find('input.media-url'),
            $id = $container.find('input.media-id'),
            $remove = $container.find('.btn-remove'),
            $preview = $container.find('.media-preview'),
            media_frame = null;
        if(!options.remove && !options.change) {
            if(button.hasClass('selecting')) {
                return;
            }
            if(!options.button_text) {
                if(options.multiple) {
                    options.button_text = hocwp.i18n.insert_media_button_texts;
                } else {
                    options.button_text = hocwp.i18n.insert_media_button_text;
                }
            }
            button.addClass('selecting');
            if(media_frame) {
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
            media_frame.on('select', function() {
                var media_items = hocwp.receiveSelectedMediaItems(media_frame);
                if(!options.multiple) {
                    var media_item = hocwp.getFirstMediaItemJSON(media_items);
                    if(media_item.id) {
                        $id.val(media_item.id);
                    }
                    if(media_item.url) {
                        $url.val(media_item.url);
                        $preview.html(hocwp.createImageHTML({src: media_item.url}));
                        button.addClass('hidden');
                        $remove.removeClass('hidden');
                    }
                }
                button.removeClass('selecting');
            });
            media_frame.on('escape', function() {
                button.removeClass('selecting');
            });
            media_frame.open();
        } else {
            if(options.remove) {
                hocwp.mediaRemove(button, $remove, $preview, $url, $id);
            }
        }

        if(options.change) {
            hocwp.mediaChange(button, $remove, $preview, $url, $id);
        }

        $url.on('change input', function(e) {
            e.preventDefault();
            hocwp.mediaChange(button, $remove, $preview, $url, $id);
        });

        $remove.on('click', function(e) {
            e.preventDefault();
            hocwp.mediaRemove(button, $remove, $preview, $url, $id);
        });
    };

    hocwp.sortableTermStop = function(container) {
        var $input_result = container.find('.input-result'),
            $sortable_result = container.find('.connected-result'),
            value = [];
        $sortable_result.find('li').each(function(index, el) {
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

    hocwp.sortablePostTypeStop = function(container) {
        var $input_result = container.find('.input-result'),
            $sortable_result = container.find('.connected-result'),
            value = [];
        $sortable_result.find('li').each(function(index, el) {
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
});

jQuery(document).ready(function($) {
    function MediaUpload(element, options) {
        this.self = this;
        this.element = element;
        this.options = $.extend({}, MediaUpload.DEFAULTS, options);
        this.items = null;
        this.$element = $(element);
        this.$container = this.$element.parent();
        this.$id = this.$container.find('input.media-id');
        this.$url = this.$container.find('input.media-url');
        this.$preview = this.$container.find('.media-preview');
        this.$remove = this.$container.find('.btn-remove');
        this.$th = this.$container.prev();
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
        multiple: false
    };

    MediaUpload.prototype.init = function() {
        if(!this.options.button_text) {
            if(this.options.multiple) {
                this.options.button_text = hocwp.i18n.insert_media_button_texts;
            } else {
                this.options.button_text = hocwp.i18n.insert_media_button_text;
            }
        }
    };

    MediaUpload.prototype.selected = function() {
        this.items = hocwp.receiveSelectedMediaItems(this.frame);
        if(!this.options.multiple) {
            var media_item = hocwp.getFirstMediaItemJSON(this.items);
            if(media_item.id) {
                this.$id.val(media_item.id);
            }
            if(media_item.url) {
                this.$url.val(media_item.url);
                this.$preview.html(hocwp.createImageHTML({src: media_item.url}));
                this.$element.addClass('hidden');
                this.$remove.removeClass('hidden');
            }
        }
        this.$element.removeClass('selecting');
    };

    MediaUpload.prototype.remove = function(e) {
        e.preventDefault();
        this.$preview.html('');
        this.$url.val('');
        this.$id.val('');
        this.$remove.addClass('hidden');
        this.$element.removeClass('hidden');
    };

    MediaUpload.prototype.add = function(e) {
        e.preventDefault();
        var $element = this.$element;
        if(this.$element.hasClass('selecting')) {
            return;
        }
        this.$element.addClass('selecting');
        if(this.frame) {
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
        this.frame.on('escape', function() {
            $element.removeClass('selecting');
        });
        this.frame.open();
    };

    MediaUpload.prototype.change = function(e) {
        e.preventDefault();
        if(hocwp.isImageUrl(this.$url.val())) {
            this.$preview.html(hocwp.createImageHTML({src: this.$url.val()}));
        } else {
            this.$preview.html('');
        }
        this.$id.val('');
    };

    $.fn.hocwpMediaUpload = function(options) {
        return this.each(function() {
            if(!$.data(this, MediaUpload.NAME)) {
                $.data(this, MediaUpload.NAME, new MediaUpload(this, options));
            }
        });
    };
});

jQuery(document).ready(function($) {
    function ScrollTop(element, options) {
        var $window = $(window),
            current_pos = $window.scrollTop();
        this.self = this;
        this.element = element;
        this.options = $.extend({}, ScrollTop.DEFAULTS, options);
        this.$element = $(element);
        this._defaults = ScrollTop.DEFAULTS;
        this._name = ScrollTop.NAME;

        this.init();

        var pos_to_show = this.options.posToShow,
            $element = this.$element;

        if(current_pos >= pos_to_show) {
            $element.fadeIn();
        }

        $window.scroll(function() {
            if($(this).scrollTop() >= pos_to_show) {
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

    ScrollTop.prototype.init = function() {

    };

    ScrollTop.prototype.click = function(e) {
        e.preventDefault();
        hocwp.scrollToTop();
    };

    $.fn.hocwpScrollTop = function(options) {
        return this.each(function() {
            if(!$.data(this, ScrollTop.NAME)) {
                $.data(this, ScrollTop.NAME, new ScrollTop(this, options));
            }
        });
    };
});

jQuery(document).ready(function($) {
    function SortableList(element, options) {
        this.self = this;
        this.element = element;
        this.options = $.extend({}, SortableList.DEFAULTS, options);
        this.$element = $(element);
        this._defaults = SortableList.DEFAULTS;
        this._name = SortableList.NAME;
        if(this.$element.hasClass('manage-column')) {
            return;
        }
        this.init();
        var $element = this.$element,
            $container = $element.parent(),
            $sortable_result = $element.next(),
            sortable_options = {
                placeholder: 'ui-state-highlight',
                sort: function(event, ui) {
                    var that = $(this),
                        ui_state_highlight = that.find('.ui-state-highlight');
                    ui_state_highlight.css({'height': ui.item.height()});
                    if(that.hasClass('display-inline')) {
                        ui_state_highlight.css({'width': ui.item.width()});
                    }
                },
                stop: function() {
                    var $sortable_result = $container.find('.connected-result');
                    if($sortable_result.hasClass('term-sortable')) {
                        hocwp.sortableTermStop($container);
                    } else if($sortable_result.hasClass('post-type-sortable')) {
                        hocwp.sortablePostTypeStop($container);
                    }
                }
            };
        if($sortable_result.length && $sortable_result.hasClass('sortable')) {
            $sortable_result.css({'height': $element.height()});
        }
        if($element.hasClass('connected-list')) {
            sortable_options.connectWith = '.connected-list';
        }
        $element.sortable(sortable_options).disableSelection();
    }

    SortableList.NAME = 'hocwp.sortableList';

    SortableList.DEFAULTS = {};

    SortableList.prototype.init = function() {

    };

    $.fn.hocwpSortable = function(options) {
        return this.each(function() {
            if(!$.data(this, SortableList.NAME)) {
                $.data(this, SortableList.NAME, new SortableList(this, options));
            }
        });
    };
});

jQuery(document).ready(function($) {
    function MobileMenu(element, options) {
        var $window = $(window),
            $body = $('body'),
            current_width = $window.width();
        this.self = this;
        this.element = element;
        this.options = $.extend({}, MobileMenu.DEFAULTS, options);
        this.$element = $(element);
        this._defaults = MobileMenu.DEFAULTS;
        this._name = MobileMenu.NAME;
        this.init();
        var $element = this.$element,
            $menu_parent = $element.parent(),
            display_width = parseFloat(this.options.displayWidth),
            body_height = $body.height();
        this.element_class = $element.attr('class');
        this.html = $element.html();
        var html = this.html,
            menu_class = this.element_class,
            position = this.options.position;

        function hocwp_update_mobile_menu() {
            $element.removeClass('sf-menu sf-js-enabled');
            $element.find('li.menu-item-has-children').not('.appended').addClass('appended').append('<i class="fa fa-plus"></i>');
            $element.css({height: body_height});
            $element.show();
            $element.addClass(position);
            $element.addClass('hocwp-mobile-menu');
            if(!$menu_parent.find('.mobile-menu-button').length) {
                $menu_parent.append(hocwp.mobile_menu_icon);
                $menu_parent.find('.mobile-menu-button').attr('aria-controls', $element.attr('id'))
            }
            $menu_parent.find('.mobile-menu-button').on('click', function() {
                $element.toggleClass('active');
            });
            $element.on('click', function(e) {
                if(e.target == this) {
                    $element.toggleClass('active');
                }
            });
            $element.find('li.menu-item-has-children .fa').on('click', function(e) {
                e.preventDefault();
                var $this = $(this),
                    $current_li = $this.parent(),
                    $sub_menu = $current_li.children('.sub-menu');
                if($this.hasClass('active')) {
                    $sub_menu.slideUp();
                    $this.removeClass('fa-minus');
                    $this.addClass('fa-plus');
                    $current_li.find('.fa-minus').each(function() {
                        $(this).removeClass('fa-minus active').addClass('fa-plus');
                    });
                    $current_li.find('.sub-menu').hide();
                } else {
                    $this.removeClass('fa-plus');
                    $this.addClass('fa-minus');
                    $sub_menu.slideDown();
                }
                $this.toggleClass('active');
            });
        }

        if(current_width <= display_width) {
            hocwp_update_mobile_menu();
        }

        $window.on('resize', function() {
            current_width = $window.width();
            if(current_width > display_width) {
                $element.attr('class', menu_class);
                $element.attr('style', '');
                $element.html(html)
            } else {
                hocwp_update_mobile_menu();
            }
        })
    }

    MobileMenu.NAME = 'hocwp.mobileMenu';

    MobileMenu.DEFAULTS = {
        displayWidth: 980,
        position: 'left'
    };

    MobileMenu.prototype.init = function() {
        if(!this.$element.is('ul')) {
            this.$element = this.$element.find('ul');
        }
    };

    MobileMenu.prototype.click = function(e) {
        e.preventDefault();
        hocwp.scrollToTop();
    };

    $.fn.hocwpMobileMenu = function(options) {
        return this.each(function() {
            if(!$.data(this, MobileMenu.NAME)) {
                $.data(this, MobileMenu.NAME, new MobileMenu(this, options));
            }
        });
    };
});

jQuery(document).ready(function($) {
    function ChosenSelect(element, options) {
        var $window = $(window);
        this.self = this;
        this.element = element;
        this.options = $.extend({}, ChosenSelect.DEFAULTS, options);
        this.$element = $(element);
        this._defaults = ChosenSelect.DEFAULTS;
        this._name = ChosenSelect.NAME;
        this.multiple = this.$element.attr('multiple');
        this.init();
        var $element = this.$element,
            loaded = parseInt(this.$element.attr('data-loaded')),
            chosen_params = {
                width: this.options.width || '100%'
        };
        if(1 == loaded) {
            this.$element.parent().find('.chosen-container').remove();
        }
        if('multiple' == this.multiple) {
            this.$element.chosen(chosen_params).on('change', function() {
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

    ChosenSelect.prototype.init = function() {
        var $element_parent = this.$element.parent(),
            $next_element = $element_parent.next();
        if($next_element.hasClass('chosen-container')) {
            $next_element.remove();
        }
        this.$element.addClass('hocwp-chosen-select');
        this.$element.attr('data-loaded', 1);
    };

    $.fn.hocwpChosenSelect = function(options) {
        return this.each(function() {
            if(!$.data(this, ChosenSelect.NAME)) {
                $.data(this, ChosenSelect.NAME, new ChosenSelect(this, options));
            }
        });
    };
});

jQuery(document).ready(function($) {
    $.fn.hocwpShow = function(show, fade) {
        var that = $(this);
        fade = fade || false;
        if(show) {
            if(fade) {
                that.addClass('active').fadeIn();
            } else {
                that.addClass('active').show();
            }
        } else {
            if(fade) {
                that.removeClass('active').fadeOut();
            } else {
                that.removeClass('active').hide();
            }
        }
    };

    $.fn.hocwpExternalLinkFilter = function() {
        var that = $(this);
        that.filter(function() {
            return this.hostname && this.hostname !== location.hostname;
        }).addClass('external');
    };
});

jQuery(document).ready(function($) {
    (function() {
        $('.btn-insert-media').hocwpMediaUpload();
    })();
});