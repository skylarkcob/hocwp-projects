<?php
function hocwp_dat_ve_tau_get_license_defined_data() {
    global $hocwp_dat_ve_tau_license_data;
    $hocwp_dat_ve_tau_license_data = hocwp_sanitize_array($hocwp_dat_ve_tau_license_data);
    $data = $hocwp_dat_ve_tau_license_data;
    $data = apply_filters('hocwp_dat_ve_tau_license_defined_data', $data);
    return $data;
}

function hocwp_dat_ve_tau_license_valid() {
    global $hocwp_dat_ve_tau_license, $hocwp_dat_ve_tau_license_valid;

    if(!hocwp_object_valid($hocwp_dat_ve_tau_license)) {
        $hocwp_dat_ve_tau_license = new HOCWP_License();
        $hocwp_dat_ve_tau_license->set_type('plugin');
        $hocwp_dat_ve_tau_license->set_use_for(HOCWP_DAT_VE_TAU_BASENAME);
        $hocwp_dat_ve_tau_license->set_option_name('hocwp_plugin_licenses');
    }

    $hocwp_dat_ve_tau_license_valid = $hocwp_dat_ve_tau_license->check_valid(hocwp_dat_ve_tau_get_license_defined_data());
    return $hocwp_dat_ve_tau_license_valid;
}

function hocwp_dat_ve_tau_shortcode_func($atts = array(), $content = null) {
    $atts = shortcode_atts(array(
        'attr_1' => 'attribute 1 default',
        'attr_2' => 'attribute 2 default'
    ), $atts);
    ob_start();
    hocwp_plugin_get_module(HOCWP_DAT_VE_TAU_INC_PATH, 'form-dat-ve-tau');
    return ob_get_clean();
}
if(hocwp_dat_ve_tau_license_valid()) add_shortcode('hocwp_ve_tau', 'hocwp_dat_ve_tau_shortcode_func');

$meta = new HOCWP_Meta('post');
$meta->add_post_type('ve_tau');
$meta->set_title(__('Thông tin vé tàu', 'hocwp'));
$meta->set_id('hocwp_thong_tin_ve_tau');
$meta->add_field(array('field_args' => array('id' => 'ga_di', 'label' => 'Ga đi:')));
$meta->add_field(array('field_args' => array('id' => 'ga_den', 'label' => 'Ga đến:')));
$meta->add_field(array('field_args' => array('id' => 'so_luong', 'label' => 'Số lượng:')));
$meta->add_field(array('field_args' => array('id' => 'hang_ghe', 'label' => 'Hạng ghế:')));
$meta->add_field(array('field_args' => array('id' => 'ngay_di', 'label' => 'Ngày đi:')));
$meta->add_field(array('field_args' => array('id' => 'ngay_ve', 'label' => 'Ngày về:')));
$meta->init();

$meta = new HOCWP_Meta('post');
$meta->add_post_type('ve_tau');
$meta->set_title(__('Thông tin khách hàng', 'hocwp'));
$meta->set_id('hocwp_thong_tin_khach_hang');
$meta->add_field(array('field_args' => array('id' => 'customer_name', 'label' => 'Họ và tên:')));
$meta->add_field(array('field_args' => array('id' => 'customer_email', 'label' => 'Email:')));
$meta->add_field(array('field_args' => array('id' => 'customer_phone', 'label' => 'Số điện thoại:')));
$meta->add_field(array('field_args' => array('id' => 'customer_cmnd', 'label' => 'CMND:')));
$meta->add_field(array('field_args' => array('id' => 'customer_address', 'label' => 'Địa chỉ:')));
$meta->init();