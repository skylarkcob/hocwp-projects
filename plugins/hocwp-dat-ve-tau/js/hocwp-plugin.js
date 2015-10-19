(function($) {
    $('.form_date').datepicker({
        showOn: 'button',
        buttonImage: hocwp.datepicker_icon,
        buttonImageOnly: true,
        buttonText: 'Chọn ngày',
        dateFormat: 'dd/mm/yy',
        minDate: 0
    });

    $('.hocwp-ve-tau form').on('submit', function(e) {
        e.preventDefault();
        var $element = $(this),
            $ga_di = $element.find('.ga-di'),
            $ga_den = $element.find('.ga-den'),
            $so_luong = $element.find('.so-luong'),
            $hang_ghe = $element.find('.hang-ghe'),
            $ngay_di = $element.find('.ngay-di'),
            $ngay_ve = $element.find('.ngay-ve'),
            $name = $element.find('.name'),
            $email = $element.find('.email'),
            $phone = $element.find('.phone'),
            $cmnd = $element.find('.cmnd'),
            $address = $element.find('.address'),
            $captcha = $element.find('.hocwp-captcha-code');
        if(!$.trim($ngay_di.val())) {
            alert('Xin vui lòng nhập ngày đi.');
            return false;
        } else if(!$.trim($name.val())) {
            alert('Xin vui lòng nhập họ và tên.');
            return false;
        } else if(!$.trim($email.val())) {
            alert('Địa chỉ email không đúng, xin vui lòng kiểm tra lại.');
            return false;
        } else if(!$.trim($phone.val())) {
            alert('Xin vui lòng nhập số điện thoại.');
            return false;
        } else if($captcha.length && !$.trim($captcha.val())) {
            alert('Xin vui lòng nhập mã bảo mật.');
            return false;
        } else {
            $element.find('button img').show();
            $element.find('button').css({'pointer-events' : 'none'});
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: hocwp.ajax_url,
                data: {
                    action: 'hocwp_ve_tau_submit',
                    ga_di: $ga_di.val(),
                    ga_den: $ga_den.val(),
                    so_luong: $so_luong.val(),
                    hang_ghe: $hang_ghe.val(),
                    ngay_di: $ngay_di.val(),
                    ngay_ve: $ngay_ve.val(),
                    name: $name.val(),
                    email: $email.val(),
                    phone: $phone.val(),
                    cmnd: $cmnd.val(),
                    address: $address.val(),
                    captcha: $captcha.val()
                },
                success: function(response){
                    $element.find('button img').hide();
                    $element.find('button').css({'pointer-events' : 'inherit'});
                    $element.find('img.hocwp-captcha-reload').trigger('click');
                    $captcha.val('');
                    if(response.success) {
                        if($.trim(response.message)) {
                            alert(response.message);
                        }
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
    });
})(jQuery);