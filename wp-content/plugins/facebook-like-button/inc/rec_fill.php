<?php

/*
rec_layout.php auto-fill
*/

//Values part
$app_id_value = (get_option("fpp_rec_appid") != "") ? get_option("fpp_rec_appid") : '';
$mehtod_value = (get_option("fpp_rec_method") != "") ? get_option("fpp_rec_appid") : '';
$domain_value = (get_option("fpp_rec_domain") != "") ? get_option("fpp_rec_domain") : '';
$height_value = (get_option("fpp_rec_height") != "") ? get_option("fpp_rec_height") : '';
$width_value  = (get_option("fpp_rec_width") != "") ? get_option("fpp_rec_width") : '';
$border_value = (get_option("fpp_rec_border") != "") ? get_option("fpp_rec_border") : '';
$widget_value = (get_option('fpp_rec_wid_title') != "") ? get_option("fpp_rec_wid_title") : '';

//Checks part
$check_xfbml  = (get_option("fpp_rec_method") == "xfbml")  ? 'CHECKED' : '';
$check_iframe = (get_option("fpp_rec_method") == "iframe") ? 'CHECKED' : '';
$check_header = (get_option("fpp_rec_header") == "true") ?  'CHECKED' : '';

//Select part
$sel_light     = (get_option("fpp_rec_layout") == "light") ?  'SELECTED' : '';
$sel_dark      = (get_option("fpp_rec_layout") == "dark") ?  'SELECTED' : '';
$sel_arial     = (get_option("fpp_rec_font") == 'arial') ? 'SELECTED' : '';
$sel_lucida    = (get_option("fpp_rec_font") == 'lucida grande') ? 'SELECTED' : '';
$sel_segoe     = (get_option("fpp_rec_font") == 'segoe ui') ? 'SELECTED' : '';
$sel_tahoma    = (get_option("fpp_rec_font") == 'tahoma') ? 'SELECTED' : '';
$sel_trebuchet = (get_option("fpp_rec_font") == 'trebuchet ms') ? 'SELECTED' : '';
$sel_verdana   = (get_option("fpp_rec_font") == 'verdana') ? 'SELECTED' : '';



?>