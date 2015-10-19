<?php
$title = hocwp_option_get_value('booking_form', 'title');
$form_footer = hocwp_option_get_value('booking_form', 'form_footer');
$fallens = get_terms('ga', array('hide_empty' => 0));
$hang_ghe = get_terms('hang_ghe', array('hide_empty' => 0));
$dia_chi = get_terms('dia_chi', array('hide_empty' => 0));
$calendar_image = hocwp_plugin_get_image_url(HOCWP_DAT_VE_TAU_URL, 'calendar.gif');
?>
<div class="hocwp-ve-tau">
    <div class="ve-tau-header">
        <h3><?php echo $title; ?></h3>
        <p>Những trường có dấu (<?php echo HOCWP_REQUIRED_HTML; ?>) là bắt buộc.</p>
    </div>
    <form method="post" action="" class="ve-tau-form">
        <div style="float: left; width: 50%; padding-right: 10px; box-sizing: border-box;">
            <div class="title_form">Ga đi&nbsp;(<?php echo HOCWP_REQUIRED_HTML; ?>):</div>
            <div class="form_text">
                <select class="station ga-di" id="gadi" name="gadi">
                    <?php
                    foreach($fallens as $fallen) {
                        echo '<option value="' . $fallen->name . '" ' . selected('ha-noi', $fallen->slug, false) . '>' . $fallen->name . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div style="clear:both"></div>
            <div class="title_form">Ga đến&nbsp;(<?php echo HOCWP_REQUIRED_HTML; ?>):</div>
            <div class="form_text">
                <select class="station ga-den" id="gaden" name="gaden">
                    <?php
                    foreach($fallens as $fallen) {
                        echo '<option value="' . $fallen->name . '" ' . selected('sai-gon', $fallen->slug, false) . '>' . $fallen->name . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div style="clear:both"></div>
            <div class="title_form">Ưu tiên&nbsp;:</div>
            <div style="float:left" class="form_text">
                <select class="soluong so-luong" id="soluong" name="soluong">
                    <option value="01 ">01 </option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option>
                </select>
            </div>
            <div class="form_text">
                <select class="loaicho hang-ghe" id="hangghe" name="hangghe">
                    <?php
                    foreach($hang_ghe as $term) {
                        echo '<option value="' . $term->name . '">' . $term->name . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div style="clear:both"></div>
            <div class="title_form">Ngày đi&nbsp;(<span style="color:#FF0000;">*</span>):</div>
            <div class="form_text">
                <input type="text" style="width:150px" onblur="if(this.value=='') this.value='Ng / Thg / Năm'" onfocus="if(this.value=='Ng/Thg/Nam') this.value=''" name="starting_date" id="starting_date" class="form_date ngay-di">
            </div>
            <div style="clear:both"></div>
            <div class="title_form">Ngày về&nbsp;<br>(<span style="font-weight:normal;">nếu có</span>):</div>
            <div class="form_text">
                <input type="text" value="" style="width:150px" onblur="if(this.value=='') this.value='Ng / Thg / Năm'" onfocus="if(this.value=='Ng/Thg/Nam') this.value=''" name="ending_date" id="ending_date" class="form_date ngay-ve">
            </div>
            <div style="clear:both"></div>
        </div>
        <div style="float: left; width: 50%;" class="noidung_lienhe">
            <div class="form_kh">Họ và tên&nbsp;(<?php echo HOCWP_REQUIRED_HTML; ?>):</div>
            <div class="form_kh_r">
                <input type="text" value="" class="input ho-va-ten name" id="name_contact" name="name">
            </div>
            <div style="clear:both"></div>
            <div class="form_kh">E-mail&nbsp;(<?php echo HOCWP_REQUIRED_HTML; ?>):</div>
            <div class="form_kh_r">
                <input type="text" value="" class="input email" id="email_contact" name="email">
            </div>
            <div style="clear:both"></div>
            <div class="form_kh">Số điện thoại&nbsp;(<?php echo HOCWP_REQUIRED_HTML; ?>):</div>
            <div class="form_kh_r">
                <input type="text" value="" class="input so-dien-thoai phone" id="phone_contact" name="phone">
            </div>
            <div style="clear:both"></div>
            <div class="form_kh">Số CMT:</div>
            <div class="form_kh_r">
                <input type="text" value="" class="input so-cmnd cmnd" id="cmt" name="cmt">
            </div>
            <div style="clear:both"></div>
            <div style="color:#999999; margin-top: 5px;">(Dành cho KH nhận vé qua chuyển phát. Nhân viên chuyển phát sẽ kiểm tra CMT tại thời điểm giao vé)</div>
            <div style="clear:both"></div>
            <div class="form_kh">Hiện bạn đang ở:</div>
            <div class="form_kh_r">
                <select id="address_contact" name="address" class="dia-chi address">
                    <?php
                    foreach($dia_chi as $term) {
                        echo '<option value="' . $term->name . '">' . $term->name . '</option>';
                    }
                    ?>
                </select>
            </div>
            <!--
            <div style="clear:both"></div>
            <div class="form_kh">Mã an toàn&nbsp;(<?php echo HOCWP_REQUIRED_HTML; ?>):</div>
            <div class="form_kh_r">
                <?php hocwp_field_captcha(array('placeholder' => '', 'input_width' => 47)); ?>
            </div>
            <div></div>
            -->
        </div>
        <div style="clear:both"></div>
        <div class="form-submit">
            <button class="submit" name="submit"><?php _e('Đặt vé', 'hocwp'); ?> <img src="<?php echo hocwp_plugin_get_image_url(HOCWP_DAT_VE_TAU_URL, 'icon-loading-circle.gif'); ?>" style="display: none"></button>
        </div>
    </form>
    <div style="clear:both"></div>
    <div class="ve-tau-footer" style="margin-top: 10px">
        <?php echo wpautop($form_footer); ?>
    </div>
</div>
