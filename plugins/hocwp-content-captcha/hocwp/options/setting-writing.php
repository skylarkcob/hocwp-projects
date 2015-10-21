<?php
$writing_option = new HOCWP_Option('', 'writing');
$writing_option->set_page('options-writing.php');
$writing_option->add_field(array('id' => 'default_post_thumbnail', 'title' => __('Default post thumbnail', 'hocwp'), 'field_callback' => 'hocwp_field_media_upload'));
$writing_option->init();
hocwp_option_add_object_to_list($writing_option);