body#custom {
font-family: <?php echo $tn_buddyfun_body_font; ?>!important;
background: <?php if($tn_buddyfun_bg_color == ""){ ?><?php echo "#E4E4E4"; } else { ?><?php echo $tn_buddyfun_bg_color; ?><?php } ?><?php if($tn_buddyfun_bg_image == "") { ?><?php } else { ?> url(<?php echo $tn_buddyfun_bg_image; ?>)<?php } ?> <?php echo $tn_buddyfun_bg_image_repeat; ?> <?php echo $tn_buddyfun_bg_image_attachment; ?> <?php echo $tn_buddyfun_bg_image_horizontal; ?> <?php echo $tn_buddyfun_bg_image_vertical; ?>;
}

#custom #header .custom-img-header {
height: <?php echo $tn_buddyfun_image_height; ?>px !important;
}

h1, h2, h3, h4, h5, h6 {
font-family: <?php echo $tn_buddyfun_headline_font; ?>!important;
line-height: 1.2;
}


<?php if(($tn_buddyfun_font_size == "normal") || ($tn_buddyfun_font_size == "")) { ?>
#wrapper { font-size: 0.785em; line-height: 1.6em !important; }
<?php } elseif ($tn_buddyfun_font_size == "small") { ?>
#wrapper { font-size: 0.6875em; line-height: 1.7em !important; }
<?php } elseif ($tn_buddyfun_font_size == "medium") { ?>
#wrapper { font-size: 0.85em; line-height: 1.6em !important;   }
<?php } elseif ($tn_buddyfun_font_size == "bigger") { ?>
#wrapper { font-size: 0.9em; line-height: 1.6em !important;   }
<?php } elseif ($tn_buddyfun_font_size == "largest") { ?>
#wrapper { font-size: 1em; line-height: 1.6em !important;   }
<?php } ?>


<?php if($tn_buddyfun_adminbar_bg_color != "") { ?>
#custom #wp-admin-bar {
	background: <?php echo $tn_buddyfun_adminbar_bg_color; ?> !important;
}
<?php } ?>

<?php if($tn_buddyfun_adminbar_hover_bg_color != "") { ?>
#custom #wp-admin-bar ul.main-nav li:hover, #custom #wp-admin-bar ul.main-nav li.sfhover, #custom #wp-admin-bar ul.main-nav li ul li.sfhover {
	background-color: <?php echo $tn_buddyfun_adminbar_hover_bg_color; ?> !important;
}
<?php } ?>

<?php if($tn_buddyfun_span_meta_color == "") { ?>
<?php } else { ?>
#custom .activity-list .activity-header a:first-child, #custom span.highlight {
	color: <?php echo $tn_buddyfun_span_meta_text_color; ?>!important;
	border: 1px solid <?php echo $tn_buddyfun_span_meta_border_color; ?>!important;
	background: <?php echo $tn_buddyfun_span_meta_color; ?>!important;
}
span.activity {
		color: <?php echo $tn_buddyfun_span_meta_text_color; ?>!important;
	border: 1px solid <?php echo $tn_buddyfun_span_meta_border_color; ?>!important;
	background: <?php echo $tn_buddyfun_span_meta_color; ?>!important;
}
<?php } ?>


<?php if($tn_buddyfun_span_meta_hover_color == "") { ?>
<?php } else { ?>
#custom .activity-list .activity-header a:first-child:hover, #custom span.highlight:hover {
	color: <?php echo $tn_buddyfun_span_meta_text_hover_color; ?>!important;
	border: 1px solid <?php echo $tn_buddyfun_span_meta_border_hover_color; ?>!important;
	background: <?php echo $tn_buddyfun_span_meta_hover_color; ?>!important;
}
<?php } ?>