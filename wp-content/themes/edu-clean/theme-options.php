body {
font-family: <?php echo $tn_edus_body_font; ?>!important;
}

h1, h2, h3, h4, h5, h6 {
font-family: <?php echo $tn_edus_headline_font; ?>!important;
line-height: 1.26em;
}


<?php if(($tn_edus_font_size == "normal") || ($tn_edus_font_size == "")) { ?>
#custom { font-size: 0.75em; }
<?php } elseif ($tn_edus_font_size == "small") { ?>
#custom { font-size: 0.6875em; }
<?php } elseif ($tn_edus_font_size == "bigger") { ?>
#custom { font-size: 0.85em; }
<?php } elseif ($tn_edus_font_size == "largest") { ?>
#custom { font-size: 1em; }
<?php } ?>


<?php if($tn_edus_span_meta_color == "") { ?>
<?php } else { ?>
#custom .activity-list .activity-header a:first-child, #custom span.highlight {
	color: <?php echo $tn_edus_span_meta_text_color; ?>!important;
	border: 1px solid <?php echo $tn_edus_span_meta_border_color; ?>!important;
	background: <?php echo $tn_edus_span_meta_color; ?>!important;
}
span.activity {
		color: <?php echo $tn_edus_span_meta_text_color; ?>!important;
	border: 1px solid <?php echo $tn_edus_span_meta_border_color; ?>!important;
	background: <?php echo $tn_edus_span_meta_color; ?>!important;
}
<?php } ?>


<?php if($tn_edus_span_meta_hover_color == "") { ?>
<?php } else { ?>
#custom .activity-list .activity-header a:first-child:hover, #custom span.highlight:hover {
	color: <?php echo $tn_edus_span_meta_text_hover_color; ?>!important;
	border: 1px solid <?php echo $tn_edus_span_meta_border_hover_color; ?>!important;
	background: <?php echo $tn_edus_span_meta_hover_color; ?>!important;
}
<?php } ?>


<?php if($tn_edus_top_header_bg_colour == "") { ?>
<?php } else { ?>
.top-header-wrap {
background: <?php echo $tn_edus_top_header_bg_colour; ?><?php if($tn_edus_top_header_bg_image != "") { ?> url(<?php echo $tn_edus_top_header_bg_image; ?>) repeat-x left top<?php } ?> !important;
}
<?php } ?>


<?php if($tn_edus_top_header_text_colour == "") { ?>
<?php } else { ?>
.top-header-wrap {
color: <?php echo $tn_edus_top_header_text_colour; ?> !important;
}
<?php } ?>

<?php if($tn_edus_top_header_text_link_colour == "") { ?>
<?php } else { ?>
.top-header-wrap h1 a {
color: <?php echo $tn_edus_top_header_text_link_colour; ?> !important;
}
<?php } ?>


<?php if($tn_edus_top_header_text_link_hover_colour == "") { ?>
<?php } else { ?>
.top-header-wrap h1 a:hover {
color: <?php echo $tn_edus_top_header_text_link_hover_colour; ?> !important;
}
<?php } ?>


<?php if($tn_edus_link_colour == "") { ?>
<?php } else { ?>
#container a, #edublog-free p a {
color: <?php echo $tn_edus_link_colour; ?>!important;
}
<?php } ?>


<?php if($tn_edus_pri_bg_colour == "") { ?>
<?php } else { ?>
#main-header-content, #top-right-panel, #footer, ul.sidebar_list li h3, #post-navigator a {
background: <?php echo $tn_edus_pri_bg_colour; ?>!important;
color: <?php echo $tn_edus_pri_text_colour; ?>!important;
}
<?php } ?>



<?php if($tn_edus_nav_bg_color == "") { ?>
<?php } else { ?>
#nav li a, #home a {
background: <?php echo $tn_edus_nav_bg_color; ?>!important;
color: <?php echo $tn_edus_nav_text_color; ?>!important;
}
<?php } ?>

<?php if($tn_edus_nav_hover_bg_color == "") { ?>
<?php } else { ?>
#nav ul li a, #nav li:hover a, #nav li a:hover, #nav li.selected a, #nav li.current_page_item a, #nav li.current_page_item a:hover {
background: <?php echo $tn_edus_nav_hover_bg_color; ?>!important;
color: <?php echo $tn_edus_nav_hover_text_color; ?>!important;
}
<?php } ?>


<?php if($tn_edus_nav_hover_border_color == "") { ?>
<?php } else { ?>
#nav ul li a, #nav ul li a:hover {
background: <?php echo $tn_edus_nav_hover_border_color; ?>!important;
border-bottom: 1px solid <?php echo $tn_edus_nav_hover_border_color; ?>!important;
}
<?php } ?>


<?php if($tn_edus_pri_text_colour == "") { ?>
<?php } else { ?>
.footer a {
color: <?php echo $tn_edus_pri_text_colour; ?>;
}
<?php } ?>

<?php if($tn_edus_pri_bg_border_colour == "") { ?>
<?php } else { ?>

#main-header-content {
border-bottom: 5px solid <?php echo $tn_edus_pri_bg_border_colour; ?>!important;
}


#top-right-panel, ul.sidebar_list li h3 {
border: 1px solid <?php echo $tn_edus_pri_bg_border_colour; ?>!important;
}

#footer {
border-top: 1px solid <?php echo $tn_edus_pri_bg_border_colour; ?>!important;
}

input.inbox { border: 1px solid <?php echo $tn_edus_pri_bg_border_colour; ?>!important; }
<?php } ?>



<?php if($tn_edus_pri_text_colour != "") { ?>
#top-right-panel, #top-right-panel label, #top-right-panel p {
color: <?php echo $tn_edus_pri_text_colour; ?>!important;
}

#container #top-right-panel a, #container ul.sidebar_list h3 a {
color: <?php echo $tn_edus_pri_text_colour; ?>!important;
font-weight: bold!important;
}
<?php } ?>



<?php if($tn_edus_tab_bg_colour == "") { ?>
<?php } else { ?>

#container .rss-feeds {
    color: <?php echo $tn_edus_tab_text_colour; ?>!important;
	background: <?php echo $tn_edus_tab_bg_colour; ?>!important;
	border-right: 1px solid <?php echo $tn_edus_tab_border_colour; ?>!important;
	border-bottom: 1px solid <?php echo $tn_edus_tab_border_colour; ?>!important;
	border-left: 1px solid <?php echo $tn_edus_tab_border_colour; ?>!important;
}

#container .feed-pull {
border-bottom: 1px solid <?php echo $tn_edus_tab_border_colour; ?>!important;
}


#container .rss-feeds a {
color: <?php echo $tn_edus_tab_link_colour; ?>!important;
}

#container ul.tabbernav li.tabberactive a {
    color: <?php echo $tn_edus_tab_link_colour; ?>!important;
   	background: <?php echo $tn_edus_tab_bg_colour; ?>!important;
	border-top: 1px solid <?php echo $tn_edus_tab_border_colour; ?>!important;
	border-right: 1px solid <?php echo $tn_edus_tab_border_colour; ?>!important;
	border-left: 1px solid <?php echo $tn_edus_tab_border_colour; ?>!important;
}

#container ul.tabbernav li a {
    color: #292929!important;
	background: #fff;
	border-top: 1px solid <?php echo $tn_edus_tab_border_colour; ?>!important;
	border-right: 1px solid <?php echo $tn_edus_tab_border_colour; ?>!important;
	border-left: 1px solid <?php echo $tn_edus_tab_border_colour; ?>!important;
}
<?php } ?>


