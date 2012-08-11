body {
font-family: <?php echo $tn_buddycom_body_font; ?>!important;
background: <?php if($tn_buddycom_bg_color == ""){ ?><?php echo "#f2f2f2"; } else { ?><?php echo $tn_buddycom_bg_color; ?><?php } ?><?php if($tn_buddycom_bg_image == "") { ?><?php } else { ?> url(<?php echo $tn_buddycom_bg_image; ?>)<?php } ?> <?php echo $tn_buddycom_bg_image_repeat; ?> <?php echo $tn_buddycom_bg_image_attachment; ?> <?php echo $tn_buddycom_bg_image_horizontal; ?> <?php echo $tn_buddycom_bg_image_vertical; ?>
!important;
}

h1, h2, h3, h4, h5, h6 {
font-family: <?php echo $tn_buddycom_headline_font; ?>!important;
line-height: 1.2;
}

<?php if(($tn_buddycom_font_size == "normal") || ($tn_buddycom_font_size == "")) { ?>
#wrapper { font-size: 0.785em; line-height: 1.6em !important; }
<?php } elseif ($tn_buddycom_font_size == "small") { ?>
#wrapper { font-size: 0.6875em; line-height: 1.6em !important; }
<?php } elseif ($tn_buddycom_font_size == "medium") { ?>
#wrapper { font-size: 0.85em; line-height: 1.6em !important;   }
<?php } elseif ($tn_buddycom_font_size == "bigger") { ?>
#wrapper { font-size: 0.9em; line-height: 1.6em !important;   }
<?php } elseif ($tn_buddycom_font_size == "largest") { ?>
#wrapper { font-size: 1em; line-height: 1.6em !important;   }
<?php } ?>


<?php if($tn_buddycom_span_meta_color == "") { ?>
<?php } else { ?>
.activity-list .activity-header a:first-child, span.highlight {
	color: <?php echo $tn_buddycom_span_meta_text_color; ?>!important;
	border: 1px solid <?php echo $tn_buddycom_span_meta_border_color; ?>!important;
	background: <?php echo $tn_buddycom_span_meta_color; ?>!important;
}
span.activity {
		color: <?php echo $tn_buddycom_span_meta_text_color; ?>!important;
	border: 1px solid <?php echo $tn_buddycom_span_meta_border_color; ?>!important;
	background: <?php echo $tn_buddycom_span_meta_color; ?>!important;
}
<?php } ?>


<?php if($tn_buddycom_span_meta_hover_color == "") { ?>
<?php } else { ?>
.activity-list .activity-header a:first-child:hover, span.highlight:hover {
	color: <?php echo $tn_buddycom_span_meta_text_hover_color; ?>!important;
	border: 1px solid <?php echo $tn_buddycom_span_meta_border_hover_color; ?>!important;
	background: <?php echo $tn_buddycom_span_meta_hover_color; ?>!important;
}
<?php } ?>

<?php if($tn_buddycom_global_links == "") { ?>
<?php } else { ?>

ul.wpnv li a:hover {
color: <?php echo $tn_buddycom_global_links; ?>!important;
}

#post-navigator .current {
border: 1px solid <?php echo $tn_buddycom_global_links; ?>!important;
}


.wp-pagenavi .pages, #post-navigator a, #post-navigator a:hover {
border: 1px solid <?php echo $tn_buddycom_global_links; ?>!important;
}
<?php } ?>




<?php if($tn_buddycom_sidebar_text_links_color == "") { ?>
<?php } else { ?>

#sidebar a, #sidebar .textwidget a, #sidebar .widget_tag_cloud a  {
color: <?php echo $tn_buddycom_sidebar_text_links_color; ?>!important;
}
<?php } ?>



<?php if($tn_buddycom_sidebar_border_color == "") { ?>
<?php } else { ?>

#sidebar .bpside h2 {
border-bottom: 1px solid <?php echo $tn_buddycom_sidebar_border_color; ?>!important;
}

<?php } ?>




<?php if($tn_buddycom_sidebar_memberbar_color == "") { ?>
<?php } else { ?>
#sidebar ul#bp-nav, #sidebar ul#options-nav  {
background: <?php echo $tn_buddycom_sidebar_memberbar_color; ?>!important;
}
<?php } ?>




<?php if($tn_buddycom_footer_text_color == "") { ?>
<?php } else { ?>
#footer {
color: <?php echo $tn_buddycom_footer_text_color; ?>!important;
}
<?php } ?>


<?php if($tn_buddycom_footer_text_link_color == "") { ?>
<?php } else { ?>
#footer a {
color: <?php echo $tn_buddycom_footer_text_link_color; ?>!important;
}
#footer a:hover {
color: <?php echo $tn_buddycom_footer_text_link_color; ?>!important;
text-decoration: underline!important;
}
<?php } ?>



<?php if($tn_buddycom_sidebar_text_color == "") { ?>
<?php } else { ?>
#sidebar {
color: <?php echo $tn_buddycom_sidebar_text_color; ?>!important;
}
#sidebar, #sidebar h2, #sidebar h2 a, #sidebar .bpside .time-since {
color: <?php echo $tn_buddycom_sidebar_text_color; ?>!important;
}
<?php } ?>



<?php if($tn_buddycom_sidebar_header_color == "") { ?>
<?php } else { ?>
#sidebar h2.widgettitle {
color: <?php echo $tn_buddycom_sidebar_header_color; ?>!important;
}
<?php } ?>



<?php if($tn_buddycom_sidebar_userbar_li_color == "") { ?>
<?php } else { ?>
ul#options-nav li a, ul#bp-nav li a {
color: <?php echo $tn_buddycom_sidebar_userbar_li_color; ?>!important;
}
<?php } ?>



<?php if($tn_buddycom_member_header_text_color == "") { ?>
<?php } else { ?>
#member-content h4 {
color: <?php echo $tn_buddycom_member_header_text_color; ?>!important;
}
<?php } ?>



<?php if($tn_buddycom_sidebar_userbar_link_color == "") { ?>
<?php } else { ?>
#sidebar li.current a {
color: <?php echo $tn_buddycom_sidebar_userbar_link_color; ?>!important;
}
<?php } ?>



<?php if($tn_buddycom_sidebar_userbar_current_color == "") { ?>
<?php } else { ?>

ul#options-nav li.current a  {
background: <?php echo $tn_buddycom_sidebar_userbar_current_color; ?> url(<?php echo get_template_directory_uri(); ?>/_inc/images/members/options.png) no-repeat 8px center;
}

ul#bp-nav .current #my-activity  {
background: <?php echo $tn_buddycom_sidebar_userbar_current_color; ?> url(<?php echo get_template_directory_uri(); ?>/_inc/images/members/activity-bullet.png) no-repeat 8px center;
}

ul#bp-nav .current #my-profile {
	background: <?php echo $tn_buddycom_sidebar_userbar_current_color; ?> url(<?php echo get_template_directory_uri(); ?>/_inc/images/members/profile-bullet.png) no-repeat 8px center;
}

ul#bp-nav .current #my-blogs {
	background: <?php echo $tn_buddycom_sidebar_userbar_current_color; ?> url(<?php echo get_template_directory_uri(); ?>/_inc/images/members/blog-bullet.png) no-repeat 8px center;
}

ul#bp-nav .current #my-wire {
	background: <?php echo $tn_buddycom_sidebar_userbar_current_color; ?> url(<?php echo get_template_directory_uri(); ?>/_inc/images/members/wire-bullet.png) no-repeat 8px center;
}


ul#bp-nav .current #my-messages {
	background: <?php echo $tn_buddycom_sidebar_userbar_current_color; ?> url(<?php echo get_template_directory_uri(); ?>/_inc/images/members/message-bullet.png) no-repeat 8px center;
}

ul#bp-nav .current #my-friends {
	background: <?php echo $tn_buddycom_sidebar_userbar_current_color; ?> url(<?php echo get_template_directory_uri(); ?>/_inc/images/members/friend-bullet.png) no-repeat 8px center;
}



ul#bp-nav .current #my-groups {
	background: <?php echo $tn_buddycom_sidebar_userbar_current_color; ?> url(<?php echo get_template_directory_uri(); ?>/_inc/images/members/group-bullet.png) no-repeat 8px center;
}

ul#bp-nav .current #my-settings {
	background: <?php echo $tn_buddycom_sidebar_userbar_current_color; ?> url(<?php echo get_template_directory_uri(); ?>/_inc/images/members/setting-bullet.png) no-repeat 8px center;
}


ul#bp-nav .current #wp-logout {
	background: <?php echo $tn_buddycom_sidebar_userbar_current_color; ?> url(<?php echo get_template_directory_uri(); ?>/_inc/images/members/logout-bullet.png) no-repeat 8px center;
}

<?php } ?>



<?php if($tn_buddycom_sidebar_memberbar_border_color == '') { ?>
<?php } else { ?>
#sidebar ul#bp-nav li a, #sidebar ul#options-nav li a {
border-bottom: 1px solid <?php echo $tn_buddycom_sidebar_memberbar_border_color; ?>!important;
}
<?php } ?>



<?php if($tn_buddycom_post_meta_color == '') { ?>
<?php } else { ?>
#post-entry .bpside blockquote, #member-content blockquote {
background: <?php echo $tn_buddycom_post_meta_color; ?>!important;
border: none 0px!important;
}
<?php } ?>



<?php if($tn_buddycom_global_links == '') { ?>
<?php } else { ?>
#member-content li a, #member-content #activity-rss p a, #member-content form a, #main-column a, .bpside li a, #post-entry .textwidget a, #post-entry div.widget_tag_cloud a, .message-box a, .item-options a, .post-content a, h1 a, .post-author a, p.tags a, #post-navigator-single a, table a, .group-button a, .generic-button a, #activity-pag a, div.textwidget a, div.item-title a, div.pagination-links a, div.directory-listing a, #custom ul#options-nav li a  {
color: <?php echo $tn_buddycom_global_links; ?>!important;
}

#commentpost a, #cf a, #respond a {
color: <?php echo $tn_buddycom_global_links; ?>;
}

div.create-account a, #rss-com p a, #rss-com p a:hover, ul#letter-list li a, #container ul.content-header-nav li a, div.reply a, .content-header-nav .current a {
background: <?php echo $tn_buddycom_global_links; ?>!important;
color: #FFF !important;
}
<?php } ?>








<?php if($tn_buddycom_searchbox_color == '') { ?>
<?php } else { ?>
#searchbox {
background: <?php echo $tn_buddycom_searchbox_color; ?>!important;
<?php if($tn_buddycom_searchbox_bottom_border_color == '') { ?>
border-bottom: 2px solid <?php echo $tn_buddycom_searchbox_color; ?>!important;
<?php } else { ?>
border-bottom: 2px solid <?php echo $tn_buddycom_searchbox_bottom_border_color; ?>!important;
<?php } ?>
}

#signup-button a {
	background: <?php echo $tn_buddycom_searchbox_color; ?>!important;
	border: 3px solid <?php echo $tn_buddycom_searchbox_bottom_border_color; ?>!important;
}
#signup-button a:hover {
   background: <?php echo $tn_buddycom_header_bottom_border_color; ?>!important;
   border: 3px solid <?php echo $tn_buddycom_header_bottom_border_color; ?>!important;
}

<?php } ?>



<?php if($tn_buddycom_header_color == '') { ?>
<?php } else { ?>
#header, #meprofile, #optionsbar h3, #optionsbar p.avatar {
background: <?php echo $tn_buddycom_header_color; ?>!important;
<?php if($tn_buddycom_header_bottom_border_color == '') { ?>
border-bottom: 10px solid <?php echo $tn_buddycom_header_color; ?>!important;
<?php } else { ?>
border-bottom: 10px solid <?php echo $tn_buddycom_header_bottom_border_color; ?>!important;
<?php } ?>
}
<?php } ?>




<?php if($tn_buddycom_header_text_color == '') { ?>
<?php } else { ?>
#header #intro-text h2, #header #intro-text span, #header a, div#meprofile, div#optionsbar h3  {
color: <?php echo $tn_buddycom_header_text_color; ?>!important;
}
<?php } ?>




<?php if($tn_buddycom_footer_color == '') { ?>
<?php } else { ?>
#footer {
background: <?php echo $tn_buddycom_footer_color; ?>!important;
<?php if($tn_buddycom_footer_bottom_border_color == '') { ?>
<?php } else { ?>
border-bottom: 10px solid <?php echo $tn_buddycom_footer_bottom_border_color; ?>!important;
<?php } ?>
}
<?php } ?>



<?php if($tn_buddycom_global_links == '') { ?>
<?php } else { ?>
#post-entry .bpside h2 {
border-bottom: 3px solid <?php echo $tn_buddycom_global_links; ?>!important;
}
<?php } ?>


<?php if($tn_buddycom_sidebar_color == '') { ?>
<?php } else { ?>
#sidebar {
background: <?php echo $tn_buddycom_sidebar_color; ?>!important;
<?php if($tn_buddycom_sidebar_border_color == '') { ?>
<?php } else { ?>
border-left: 5px solid <?php echo $tn_buddycom_sidebar_border_color; ?>!important;
<?php } ?>
}
ul.item-list li {
border-bottom: 0 none !important;
}
<?php } ?>



<?php if($tn_buddycom_post_meta_color == '') { ?>
<?php } else { ?>

.post-tagged p.com a {
border: 1px solid <?php echo $tn_buddycom_post_meta_color; ?>!important;
}
<?php } ?>




<?php if($tn_buddycom_sidebar_meta_color == '') { ?>
<?php } else { ?>

.post-tagged p.com a:hover {
border: 1px solid <?php echo $tn_buddycom_sidebar_meta_color; ?>!important;
}
<?php } ?>




<?php if($tn_buddycom_member_header_color == '') { ?>
<?php } else { ?>
#member-content h4 {
background: <?php echo $tn_buddycom_member_header_color; ?>!important;
border-bottom: 5px solid <?php echo $tn_buddycom_member_header_bottom_line_color; ?>!important;
}
#signup-button a, div.create-account a {
background: <?php echo $tn_buddycom_member_header_color; ?>!important;
color: #FFF!important;
}
<?php } ?>


<?php if($tn_buddycom_member_header_links_color == '') { ?>
<?php } else { ?>
#member-content h4 a, ul#letter-list li a, #member-content ul.content-header-nav li a {
color: <?php echo $tn_buddycom_member_header_links_color; ?>!important;
}
<?php } ?>