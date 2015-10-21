var CaptchaCallback = function() {
    var all_gcaptcha = document.getElementsByClassName('g-recaptcha');
    for(var i = 0, max = all_gcaptcha.length; i < max; i++) {
        grecaptcha.render(all_gcaptcha[i].id, {'sitekey' : hocwp.recaptcha_site_key});
    }
};
(function($) {

})(jQuery);