<?php if($tn_blogsmu_body_font != "") { ?>
body, #custom .widget blockquote p, #custom .item-list blockquote p {
font-family: <?php echo $tn_blogsmu_body_font; ?> !important;
}
<?php } ?>

<?php if($tn_blogsmu_headline_font != "") { ?>
h1, h2, h3, h4, h5, h6, #custom #header-name-alt, .one-community {
font-family: <?php echo $tn_blogsmu_headline_font; ?>!important;
}
<?php } ?>

<?php if($tn_blogsmu_activity_block_color != "") { ?>
.activity-list .activity-content .activity-inner, .activity-list .activity-content blockquote {
background: <?php echo $tn_blogsmu_activity_block_color; ?>!important;
color: <?php echo $tn_blogsmu_activity_block_text_color; ?>!important;
}
<?php } ?>


<?php if($tn_blogsmu_span_meta_color == "") { ?>
<?php } else { ?>
#custom .activity-list .activity-header a:first-child, #custom span.highlight {
	color: <?php echo $tn_blogsmu_span_meta_text_color; ?>!important;
	border: 1px solid <?php echo $tn_blogsmu_span_meta_border_color; ?>!important;
	background: <?php echo $tn_blogsmu_span_meta_color; ?>!important;
}
span.activity {
		color: <?php echo $tn_blogsmu_span_meta_text_color; ?>!important;
	border: 1px solid <?php echo $tn_blogsmu_span_meta_border_color; ?>!important;
	background: <?php echo $tn_blogsmu_span_meta_color; ?>!important;
}
<?php } ?>


<?php if($tn_blogsmu_span_meta_hover_color == "") { ?>
<?php } else { ?>
#custom .activity-list .activity-header a:first-child:hover, #custom span.highlight:hover {
	color: <?php echo $tn_blogsmu_span_meta_text_hover_color; ?>!important;
	border: 1px solid <?php echo $tn_blogsmu_span_meta_border_hover_color; ?>!important;
	background: <?php echo $tn_blogsmu_span_meta_hover_color; ?>!important;
}
<?php } ?>




<?php if($tn_blogsmu_section_widget_status == "hide") { ?>
#content-intro-content {
padding: 15px 0 0px !important;
}
<?php } ?>






<?php if($tn_blogsmu_font_size != "") {
$tn_blogsmu_font_headline_size = $tn_blogsmu_font_size + 3;
$tn_blogsmu_font_headline_line_height = $tn_blogsmu_font_line_height + 5;

$tn_blogsmu_font_post_size = $tn_blogsmu_font_size + 1;
$tn_blogsmu_font_post_line_height = $tn_blogsmu_font_line_height + 2;
?>

#custom .post-content {
font-size: <?php echo $tn_blogsmu_font_post_size; ?>px !important;
line-height: <?php echo $tn_blogsmu_font_post_line_height; ?>px !important;
}

#custom .activity-header p  {
font-size: <?php echo $tn_blogsmu_font_size - 1; ?>px !important;
line-height: <?php echo $tn_blogsmu_font_line_height - 2; ?>px !important;
}


#custom .widget, #custom div.widget blockquote, #custom .widget blockquote p, #custom .bp-widget, #custom .sidebar_list, #custom .item-list li, #services-content p {
font-size: <?php echo $tn_blogsmu_font_size; ?>px !important;
line-height: <?php echo $tn_blogsmu_font_line_height; ?>px !important;
}

#custom div.widget h4.item-title {
font-size: <?php echo $tn_blogsmu_font_headline_size; ?>px !important;
line-height: <?php echo $tn_blogsmu_font_headline_line_height; ?>px !important;
}

#custom .widget h2, #custom .bp-widget h4 {
font-size: <?php echo $tn_blogsmu_font_headline_size; ?>px !important;
}


<?php } ?>

<?php if($tn_blogsmu_blog_global_links_color != "") { ?>
#container a, .one-community a, .services-box a, #custom ul.hlist a {
color: <?php echo $tn_blogsmu_blog_global_links_color; ?>;
}

#post-navigator a {
color: #FFF !important;
background: <?php echo $tn_blogsmu_blog_global_links_color; ?> !important;
border: 1px solid <?php echo $tn_blogsmu_blog_global_links_color; ?> !important;
}
<?php } ?>


<?php if($tn_blogsmu_blog_global_links_hover_color != "") { ?>

#post-navigator a:hover {
color: #FFF !important;
background: <?php echo $tn_blogsmu_blog_global_links_hover_color; ?> !important;
border: 1px solid <?php echo $tn_blogsmu_blog_global_links_hover_color; ?> !important;
}

#post-navigator .current {
color: #FFF !important;
background: <?php echo $tn_blogsmu_blog_global_links_hover_color; ?> !important;
border: 1px solid <?php echo $tn_blogsmu_blog_global_links_hover_color; ?> !important;
}

<?php } ?>


<?php if($tn_blogsmu_featured_intro_button_color  != "") { ?>
#right-panel div.submit-button a {
background: <?php echo $tn_blogsmu_featured_intro_button_color; ?>!important;
color: <?php echo $tn_blogsmu_featured_intro_button_text_link_color; ?>!important;
}
<?php } ?>


<?php if($tn_blogsmu_nav_font != "") { ?>
#custom #navigation li { font-family: <?php echo $tn_blogsmu_nav_font; ?> !important; }
<?php } ?>




<?php if($tn_blogsmu_nav_bg_main_color != "") { ?>
#custom #navigation {
background: <?php echo $tn_blogsmu_nav_bg_main_color; ?>;
<?php if($tn_blogsmu_nav_bg_secondary_color != "") { ?>
background: -moz-linear-gradient(top, <?php echo $tn_blogsmu_nav_bg_main_color; ?> 0%, <?php echo $tn_blogsmu_nav_bg_secondary_color; ?> 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $tn_blogsmu_nav_bg_main_color; ?>), color-stop(100%,<?php echo $tn_blogsmu_nav_bg_secondary_color; ?>));
background: -webkit-linear-gradient(top, <?php echo $tn_blogsmu_nav_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_nav_bg_secondary_color; ?> 100%);
background: -o-linear-gradient(top, <?php echo $tn_blogsmu_nav_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_nav_bg_secondary_color; ?> 100%);
background: -ms-linear-gradient(top, <?php echo $tn_blogsmu_nav_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_nav_bg_secondary_color; ?> 100%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $tn_blogsmu_nav_bg_main_color; ?>', endColorstr='<?php echo $tn_blogsmu_nav_bg_secondary_color; ?>',GradientType=0 );
background: linear-gradient(top, <?php echo $tn_blogsmu_nav_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_nav_bg_secondary_color; ?> 100%);
<?php } ?>
}
<?php } ?>

<?php if($tn_blogsmu_nav_border_color != "") { ?>
#custom #navigation {
border-top: 1px solid <?php echo $tn_blogsmu_nav_border_color; ?>;
border-bottom: 1px solid <?php echo $tn_blogsmu_nav_border_color; ?>;
}
<?php } ?>


<?php if($tn_blogsmu_nav_text_link_color != "") { ?>
#custom #nav li a {
color: <?php echo $tn_blogsmu_nav_text_link_color; ?>;
}
<?php } ?>

<?php if($tn_blogsmu_nav_dropdown_bg_color != "") { ?>
#nav li ul, #nav li.current_page_item a, #nav li.current_menu_item a, #nav li.current-menu-item a, #nav li.home a, #nav li.selected a { background: <?php echo $tn_blogsmu_nav_dropdown_bg_color; ?> none !important; }
<?php } ?>

<?php if($tn_blogsmu_nav_dropdown_link_hover_color != "") { ?>
#nav ul li a:hover, #nav li.current_page_item a:hover, #nav li.current_menu_item a:hover, #nav li.current-menu-item a:hover, #nav li.home a, #nav li.selected a:hover { background: <?php echo $tn_blogsmu_nav_dropdown_link_hover_color; ?> none !important; }
<?php } ?>



<?php if($tn_blogsmu_top_header_bg_main_color != "") { ?>
#top-bg {
background: <?php echo $tn_blogsmu_top_header_bg_main_color; ?>;
<?php if($tn_blogsmu_top_header_bg_secondary_color != "") { ?>
background: -moz-linear-gradient(top, <?php echo $tn_blogsmu_top_header_bg_main_color; ?> 0%, <?php echo $tn_blogsmu_top_header_bg_secondary_color; ?> 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $tn_blogsmu_top_header_bg_main_color; ?>), color-stop(100%,<?php echo $tn_blogsmu_top_header_bg_secondary_color; ?>));
background: -webkit-linear-gradient(top, <?php echo $tn_blogsmu_top_header_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_top_header_bg_secondary_color; ?> 100%);
background: -o-linear-gradient(top, <?php echo $tn_blogsmu_top_header_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_top_header_bg_secondary_color; ?> 100%);
background: -ms-linear-gradient(top, <?php echo $tn_blogsmu_top_header_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_top_header_bg_secondary_color; ?> 100%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $tn_blogsmu_top_header_bg_main_color; ?>', endColorstr='<?php echo $tn_blogsmu_top_header_bg_secondary_color; ?>',GradientType=0 );
background: linear-gradient(top, <?php echo $tn_blogsmu_top_header_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_top_header_bg_secondary_color; ?> 100%);
<?php } ?>
}
<?php } ?>


<?php if($tn_blogsmu_main_header_bg_main_color != "") { ?>
#header-gfx-inner { background: transparent none; }
#header {
background: <?php echo $tn_blogsmu_main_header_bg_main_color; ?>;
<?php if($tn_blogsmu_main_header_bg_secondary_color != "") { ?>
background: -moz-linear-gradient(top, <?php echo $tn_blogsmu_main_header_bg_main_color; ?> 0%, <?php echo $tn_blogsmu_main_header_bg_secondary_color; ?> 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $tn_blogsmu_main_header_bg_main_color; ?>), color-stop(100%,<?php echo $tn_blogsmu_main_header_bg_secondary_color; ?>));
background: -webkit-linear-gradient(top, <?php echo $tn_blogsmu_main_header_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_main_header_bg_secondary_color; ?> 100%);
background: -o-linear-gradient(top, <?php echo $tn_blogsmu_main_header_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_main_header_bg_secondary_color; ?> 100%);
background: -ms-linear-gradient(top, <?php echo $tn_blogsmu_main_header_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_main_header_bg_secondary_color; ?> 100%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $tn_blogsmu_main_header_bg_main_color; ?>', endColorstr='<?php echo $tn_blogsmu_main_header_bg_secondary_color; ?>',GradientType=0 );
background: linear-gradient(top, <?php echo $tn_blogsmu_main_header_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_main_header_bg_secondary_color; ?> 100%);
<?php } ?>
}
<?php } ?>


<?php if($tn_blogsmu_top_header_text_color != "") { ?>
#top-bg, #top-bg .alignright, #top-bg li.user-tab { color: <?php echo $tn_blogsmu_top_header_text_color; ?> !important; }
<?php } ?>

<?php if($tn_blogsmu_top_header_text_link_color != "") { ?>
#top-bg a { color: <?php echo $tn_blogsmu_top_header_text_link_color; ?>; }
<?php } ?>

<?php if($tn_blogsmu_main_header_text_color != "") { ?>
#right-panel h4, #right-panel p.headtext { color: <?php echo $tn_blogsmu_main_header_text_color; ?>; }
<?php } ?>

<?php if($tn_blogsmu_main_header_text_link_color != "") { ?>
#right-panel p.headtext a { color: <?php echo $tn_blogsmu_main_header_text_link_color; ?>; }
<?php } ?>



<?php if($tn_blogsmu_home_footer_block == 'disable') { ?>
#custom #bottom-content {
background: <?php echo $tn_blogsmu_footer_bg_main_color; ?>;
<?php if($tn_blogsmu_footer_bg_secondary_color != "") { ?>
background: -moz-linear-gradient(top, <?php echo $tn_blogsmu_footer_bg_main_color; ?> 0%, <?php echo $tn_blogsmu_footer_bg_secondary_color; ?> 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $tn_blogsmu_footer_bg_main_color; ?>), color-stop(100%,<?php echo $tn_blogsmu_footer_bg_secondary_color; ?>));
background: -webkit-linear-gradient(top, <?php echo $tn_blogsmu_footer_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_footer_bg_secondary_color; ?> 100%);
background: -o-linear-gradient(top, <?php echo $tn_blogsmu_footer_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_footer_bg_secondary_color; ?> 100%);
background: -ms-linear-gradient(top, <?php echo $tn_blogsmu_footer_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_footer_bg_secondary_color; ?> 100%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $tn_blogsmu_footer_bg_main_color; ?>', endColorstr='<?php echo $tn_blogsmu_footer_bg_secondary_color; ?>',GradientType=0 );
background: linear-gradient(top, <?php echo $tn_blogsmu_footer_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_footer_bg_secondary_color; ?> 100%);
<?php } ?>
}

.bottom-content-inner {
color: <?php echo $tn_blogsmu_footer_text_color; ?> !important;
}

#custom div#bottom-content a {
color: <?php echo $tn_blogsmu_footer_text_link_color; ?> !important;
text-decoration: none;
border: 0 none !important;
}
<?php } ?>


<?php if($tn_blogsmu_footer_bg_main_color != "") { ?>
#custom #footer {
background: <?php echo $tn_blogsmu_footer_bg_main_color; ?> none;
<?php if($tn_blogsmu_footer_bg_secondary_color != "") { ?>
background: -moz-linear-gradient(top, <?php echo $tn_blogsmu_footer_bg_main_color; ?> 0%, <?php echo $tn_blogsmu_footer_bg_secondary_color; ?> 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $tn_blogsmu_footer_bg_main_color; ?>), color-stop(100%,<?php echo $tn_blogsmu_footer_bg_secondary_color; ?>));
background: -webkit-linear-gradient(top, <?php echo $tn_blogsmu_footer_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_footer_bg_secondary_color; ?> 100%);
background: -o-linear-gradient(top, <?php echo $tn_blogsmu_footer_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_footer_bg_secondary_color; ?> 100%);
background: -ms-linear-gradient(top, <?php echo $tn_blogsmu_footer_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_footer_bg_secondary_color; ?> 100%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $tn_blogsmu_footer_bg_main_color; ?>', endColorstr='<?php echo $tn_blogsmu_footer_bg_secondary_color; ?>',GradientType=0 );
background: linear-gradient(top, <?php echo $tn_blogsmu_footer_bg_main_color; ?> 0%,<?php echo $tn_blogsmu_footer_bg_secondary_color; ?> 100%);
<?php } ?>
}
<?php } ?>


<?php if($tn_blogsmu_footer_text_color != "") { ?>
#footer, #footer li { color: <?php echo $tn_blogsmu_footer_text_color; ?> !important; }
<?php } ?>

<?php if($tn_blogsmu_footer_header_text_color != "") { ?>
#footer h3, #footer h3 a { color: <?php echo $tn_blogsmu_footer_header_text_color; ?> !important; }
<?php } ?>

<?php if($tn_blogsmu_footer_text_link_color != "") { ?>
#footer a, #footer h3 a, #footer li a { color: <?php echo $tn_blogsmu_footer_text_link_color; ?> !important; }
<?php } ?>

<?php if($tn_blogsmu_footer_text_link_hover_color != "") { ?>
#footer a:hover, #footer h3 a:hover, #footer li a:hover { color: <?php echo $tn_blogsmu_footer_text_link_hover_color; ?> !important; }
<?php } ?>

<?php if($tn_blogsmu_footer_bg_main_color != "") { ?>
#custom #footer li li { border-bottom: 1px solid <?php echo colourCreator($tn_blogsmu_footer_bg_main_color, 5); ?> !important; }
#footer li { border-bottom: 0px none !important; }
<?php } ?>

<?php if($tn_blogsmu_featured_intro_text_shadow != "") { ?>
#right-panel h4, #right-panel p.headtext {
    text-shadow: 1px 1px 1px <?php echo $tn_blogsmu_featured_intro_text_shadow; ?>;
	-moz-text-shadow: 1px 1px 1px <?php echo $tn_blogsmu_featured_intro_text_shadow; ?>;
	-webkit-text-shadow: 1px 1px 1px <?php echo $tn_blogsmu_featured_intro_text_shadow; ?>;
}
<?php } ?>