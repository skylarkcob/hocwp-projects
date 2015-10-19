<?php
function hocwp_ve_tau_submit_ajax_callback() {
    $result = array(
        'success' => false,
        'message' => __('Đã có lỗi xảy ra, xin vui lòng thử lại.', 'hocwp')
    );
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    if(is_email($email)) {
        $captcha = isset($_POST['captcha']) ? $_POST['captcha'] : '';
        $hocwp_captcha = new HOCWP_Captcha();
        if($hocwp_captcha->check($captcha) || true) {
            $ga_di = isset($_POST['ga_di']) ? $_POST['ga_di'] : '';
            $ga_den = isset($_POST['ga_den']) ? $_POST['ga_den'] : '';
            $so_luong = isset($_POST['so_luong']) ? $_POST['so_luong'] : 1;
            $hang_ghe = isset($_POST['hang_ghe']) ? $_POST['hang_ghe'] : '';
            $ngay_di = isset($_POST['ngay_di']) ? $_POST['ngay_di'] : '';
            $ngay_ve = isset($_POST['ngay_ve']) ? $_POST['ngay_ve'] : '';
            $name = isset($_POST['name']) ? $_POST['name'] : '';
            $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
            $cmnd = isset($_POST['cmnd']) ? $_POST['cmnd'] : '';
            $address = isset($_POST['address']) ? $_POST['address'] : '';
            $post_title = $name . ' đi từ ' . $ga_di . ' đến ' . $ga_den . ' vào ngày ' . $ngay_di;
            $post_data = array(
                'post_title' => $post_title,
                'post_type' => 've_tau'
            );
            $post_id = hocwp_insert_post($post_data);
            if($post_id > 0) {
                update_post_meta($post_id, 'ga_di', $ga_di);
                update_post_meta($post_id, 'ga_den', $ga_den);
                update_post_meta($post_id, 'so_luong', $so_luong);
                update_post_meta($post_id, 'hang_ghe', $hang_ghe);
                update_post_meta($post_id, 'ngay_di', $ngay_di);
                update_post_meta($post_id, 'ngay_ve', $ngay_ve);

                update_post_meta($post_id, 'customer_name', $name);
                update_post_meta($post_id, 'customer_email', $email);
                update_post_meta($post_id, 'customer_phone', $phone);
                update_post_meta($post_id, 'customer_cmnd', $cmnd);
                update_post_meta($post_id, 'customer_address', $address);

                $result['success'] = true;
                $result['message'] = __('Thông tin của bạn đã được lưu, chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất.');

                $subject = 'Đặt vé tàu: ' . $post_title;
                $message = '<p><strong>Thông tin đặt vé tàu:</strong></p>';
                $message .= '<p>Ga đi: ' . $ga_di . '</p>';
                $message .= '<p>Ga đến: ' . $ga_den . '</p>';
                $message .= '<p>Số lượng: ' . $so_luong . '</p>';
                $message .= '<p>Hạng ghế: ' . $hang_ghe . '</p>';
                $message .= '<p>Ngày đi: ' . $ngay_di . '</p>';
                $message .= '<p>Ngày về: ' . $ngay_ve . '</p>';

                $message .= '<p></p>';

                $message .= '<p><strong>Thông tin khách hàng:</strong></p>';
                $message .= '<p>Họ và tên: ' . $name . '</p>';
                $message .= '<p>Email: ' . $email . '</p>';
                $message .= '<p>Số điện thoại: ' . $phone . '</p>';
                $message .= '<p>CMND: ' . $cmnd . '</p>';
                $message .= '<p>Địa chỉ: ' . $address . '</p>';
                hocwp_send_html_mail(hocwp_get_admin_email(), $subject, $message);

                $subject = 'Bạn đã đặt vé tàu đi từ ' . $ga_di . ' đến ' . $ga_den . ' vào ngày ' . $ngay_di;
                $message = '<p>Cảm ơn bạn đã đặt vé tàu tại ' . get_bloginfo('name') . ', chúng tôi sẽ xem xét và liên hệ lại với bạn trong thời gian sớm nhất có thể. Bên dưới là những thông tin về vé tàu bạn đã đặt.</p>';
                $message .= '<p><strong>Thông tin đặt vé tàu:</strong></p>';
                $message .= '<p>Ga đi: ' . $ga_di . '</p>';
                $message .= '<p>Ga đến: ' . $ga_den . '</p>';
                $message .= '<p>Số lượng: ' . $so_luong . '</p>';
                $message .= '<p>Hạng ghế: ' . $hang_ghe . '</p>';
                $message .= '<p>Ngày đi: ' . $ngay_di . '</p>';
                $message .= '<p>Ngày về: ' . $ngay_ve . '</p>';

                $message .= '<p></p>';

                $message .= '<p><strong>Thông tin của bạn:</strong></p>';
                $message .= '<p>Họ và tên: ' . $name . '</p>';
                $message .= '<p>Email: ' . $email . '</p>';
                $message .= '<p>Số điện thoại: ' . $phone . '</p>';
                $message .= '<p>CMND: ' . $cmnd . '</p>';
                $message .= '<p>Địa chỉ: ' . $address . '</p>';
                hocwp_send_html_mail($email, $subject, $message);
            }
        } else {
            $result['message'] = __('Mã bảo mật bạn nhập không đúng, xin vui lòng kiểm tra lại.', 'hocwp');
        }
    } else {
        $result['message'] = __('Địa chỉ email bạn nhập không đúng, xin vui lòng kiểm tra lại.', 'hocwp');
    }
    echo json_encode($result);
    die();
}
add_action('wp_ajax_hocwp_ve_tau_submit', 'hocwp_ve_tau_submit_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_ve_tau_submit', 'hocwp_ve_tau_submit_ajax_callback');