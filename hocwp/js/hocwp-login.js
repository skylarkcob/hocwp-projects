/**
 * Last updated: 20/04/2016
 */

jQuery(document).ready(function ($) {
    (function () {
        var logo_url = hocwp.login_logo_url,
            submit_class = 'btn btn-' + hocwp.logins.button_style;
        if (hocwp.isImageUrl(logo_url)) {
            $('.login #login > h1 a').html(hocwp.createImageHTML({src: logo_url}));
        }
        $('form .submit .button').attr('class', submit_class);
    })();

    (function () {
        $('#nav').find('a').each(function () {
            var that = $(this),
                action = hocwp.getParamByName(that.attr('href'), 'action');
            that.addClass(action);
        });
    })();

    (function () {
        hocwp.iconChangeCaptchaExecute();
    })();
});