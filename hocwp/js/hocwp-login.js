/**
 * Last updated: 07/01/2016
 */

jQuery(document).ready(function($) {
    (function() {
        var logo_url = hocwp.login_logo_url;
        if(hocwp.isImageUrl(logo_url)) {
            $('.login #login > h1 a').html(hocwp.createImageHTML({src: logo_url}));
        }
        $('form .submit .button').attr('class', 'btn btn-warning');
    })();

    (function(){
        $('#nav').find('a').each(function(){
            var that = $(this),
                action = hocwp.getParamByName(that.attr('href'), 'action');
            that.addClass(action);
        });
    })();

    (function() {
        hocwp.iconChangeCaptchaExecute();
    })();
});