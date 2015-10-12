<?php
$parent_slug = 'edit.php?post_type=ve_tau';
$option_dat_ve_tau = new HOCWP_Option(__('Booking Form', 'hocwp-dat-ve-tau'), 'hocwp_booking_form');
$option_dat_ve_tau->set_parent_slug($parent_slug);
$option_dat_ve_tau->add_field(array('id' => 'title', 'title' => __('Form Title', 'hocwp-dat-ve-tau')));
$option_dat_ve_tau->add_field(array('id' => 'form_footer', 'title' => __('Form Footer', 'hocwp-dat-ve-tau'), 'field_callback' => 'hocwp_field_editor'));
$option_dat_ve_tau->init();
hocwp_option_add_object_to_list($option_dat_ve_tau);

hocwp_add_option_page_smtp_email('options-general.php');