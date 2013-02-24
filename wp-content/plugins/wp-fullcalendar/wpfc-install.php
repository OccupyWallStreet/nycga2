<?php
//if called, assume we're installing/updated
add_option('wpfc_theme', get_option('dbem_emfc_theme',1));
add_option('wpfc_theme_css', get_option('dbem_emfc_theme_css',plugins_url('includes/css/ui-themes/ui-lightness.css',__FILE__)));
add_option('wpfc_limit', get_option('dbem_emfc_events_limit',3));
add_option('wpfc_limit_txt', get_option('dbem_emfc_events_limit_txt','more ...'));
add_option('wpfc_qtips', get_option('dbem_emfc_qtips',true));
add_option('wpfc_qtips_style', get_option('dbem_emfc_qtips_style','light'));
add_option('wpfc_qtips_my', get_option('dbem_emfc_qtips_my','top center'));
add_option('wpfc_qtips_at', get_option('dbem_emfc_qtips_at','bottom center'));
add_option('wpfc_qtips_rounded', get_option('dbem_emfc_qtips_rounded', false));
add_option('wpfc_qtips_image',1);
add_option('wpfc_qtips_image_w',75);
add_option('wpfc_qtips_image_h',75);
add_option('wpfc_timeFormat', 'h(:mm)t');
add_option('wpfc_defaultView', 'month');
add_option('wpfc_available_views', array('month','basicWeek','basicDay'));

//update version
update_option('wpfc_version', WPFC_VERSION);