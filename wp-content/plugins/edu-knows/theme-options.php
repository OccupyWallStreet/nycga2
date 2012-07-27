body {
font-family: <?php echo $tn_edufaq_body_font; ?> !important;

<?php if( ($tn_edufaq_body_bg == "Default Blue") || ($tn_edufaq_body_bg == "") ) { ?>

background: url(<?php echo get_template_directory_uri(); ?>/images/bg.gif) repeat-x top !important;

<?php } else if($tn_edufaq_body_bg == "Red") { ?>

background: #FFFFFF url(<?php echo get_template_directory_uri(); ?>/images/bg-red.gif) repeat-x top !important;

<?php } else if($tn_edufaq_body_bg == "Yellow") { ?>

background: #FFFFFF url(<?php echo get_template_directory_uri(); ?>/images/bg-yellow.gif) repeat-x top !important;

<?php } else if($tn_edufaq_body_bg == "Grey") { ?>

background: #FFFFFF url(<?php echo get_template_directory_uri(); ?>/images/bg-grey.gif) repeat-x top !important;

<?php } else if($tn_edufaq_body_bg == "White Shade") { ?>

background: #FFFFFF url(<?php echo get_template_directory_uri(); ?>/images/bg-white-shade.gif) repeat-x top !important;

<?php } else if($tn_edufaq_body_bg == "Light Brown") { ?>

background: #FFFFFF url(<?php echo get_template_directory_uri(); ?>/images/bg-brown.gif) repeat-x top !important;

<?php } else if($tn_edufaq_body_bg == "Light Green") { ?>

background: #FFFFFF url(<?php echo get_template_directory_uri(); ?>/images/bg-light-green.gif) repeat-x top !important;

<?php } else if($tn_edufaq_body_bg == "Dark Green") { ?>

background: #FFFFFF url(<?php echo get_template_directory_uri(); ?>/images/bg-dark-green.gif) repeat-x top !important;

<?php } else if($tn_edufaq_body_bg == "White") { ?>

background: #FFFFFF url(<?php echo get_template_directory_uri(); ?>/images/spacer.gif) repeat-x top !important;

<?php } ?>

}

<?php if($tn_edufaq_headline_font != "") { ?>
h1, h2, h3, h4, h5, h6 {
font-family: <?php echo $tn_edufaq_headline_font; ?> !important;
}
<?php } ?>


<?php if(($tn_edufaq_font_size == "normal") || ($tn_edufaq_font_size == "")) { ?>
#wrap { font-size: 0.6875em; }
<?php } elseif ($tn_edufaq_font_size == "small") { ?>
#wrap { font-size: 0.5em; }
<?php } elseif ($tn_edufaq_font_size == "bigger") { ?>
#wrap { font-size: 0.85em; }
<?php } elseif ($tn_edufaq_font_size == "largest") { ?>
#wrap { font-size: 0.9125em; }
<?php } ?>


<?php if($tn_edufaq_content_link_colour != "") { ?>
#content a {
color: <?php echo $tn_edufaq_content_link_colour; ?>!important;
}
#wrap #fulltag a, #wrap #fulltag a:hover {
  background: <?php echo $tn_edufaq_content_link_colour; ?>!important;
}
#nav li a:hover, #nav li.home a, #nav li.current_page_item a, #nav li li a, #nav li li a:hover, #nav li:hover a, #post-navigator a {
    color: #fff !important;
	background: <?php echo $tn_edufaq_content_link_colour; ?>!important;
}

#entry .nolist li a:hover {
	color: #FFFFFF!important;
    -moz-border-radius: 5px;
	-khtml-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
	background: <?php echo $tn_edufaq_content_link_colour; ?>!important;
}



#container ol.commentlist li div.reply a, #container ol.commentlist li div.reply a:hover {
color: #fff !important;
background: <?php echo $tn_edufaq_content_link_colour; ?> !important;
}

<?php } ?>


<?php if($tn_edufaq_searchbox_colour != "") { ?>
#searchbar #mysearch {
    -moz-border-radius: 6px;
	-khtml-border-radius: 6px;
	-webkit-border-radius: 6px;
	border-radius: 6px;
    background: <?php echo $tn_edufaq_searchbox_colour; ?>!important;
	border: 2px solid <?php echo $tn_edufaq_searchbox_border_colour; ?>!important;

}
<?php } ?>