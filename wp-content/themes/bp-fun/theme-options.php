
body#custom {
font-family: <?php echo $tn_buddyfun_body_font; ?>!important;
color: <?php echo $tn_buddyfun_body_text_color; ?>!important;
background: <?php if($tn_buddyfun_bg_color == ""){ ?><?php echo "#F5F6F5"; } else { ?><?php echo $tn_buddyfun_bg_color; ?><?php } ?><?php if($tn_buddyfun_bg_image == "") { ?><?php } else { ?> url(<?php echo $tn_buddyfun_bg_image; ?>)<?php } ?> <?php echo $tn_buddyfun_bg_image_repeat; ?> <?php echo $tn_buddyfun_bg_image_attachment; ?> <?php echo $tn_buddyfun_bg_image_horizontal; ?> <?php echo $tn_buddyfun_bg_image_vertical; ?> !important;
}


h1, h2, h3, h4, h5, h6 {
font-family: <?php echo $tn_buddyfun_headline_font; ?>!important;
line-height: 1.2; 
}


<?php if(($tn_buddyfun_font_size == "normal") || ($tn_buddyfun_font_size == "")) { ?>
#wrapper { font-size: 0.6875em; line-height: 1.6em !important; }
<?php } elseif ($tn_buddyfun_font_size == "small") { ?>
#wrapper { font-size: 0.65em; line-height: 1.6em !important; }
<?php } elseif ($tn_buddyfun_font_size == "medium") { ?>
#wrapper { font-size: 0.785em; line-height: 1.6em !important;   }
<?php } elseif ($tn_buddyfun_font_size == "bigger") { ?>
#wrapper { font-size: 0.85em; line-height: 1.6em !important;   }
<?php } elseif ($tn_buddyfun_font_size == "largest") { ?>
#wrapper { font-size: 1em; line-height: 1.6em !important;   }
<?php } ?>



<?php if($tn_buddyfun_global_links == "") { ?>
<?php } else { ?>
.item-list .meta a, #custom .site-title h1 a {
color: <?php echo $tn_buddyfun_global_links; ?>!important;
}
#container .content a {
color: <?php echo $tn_buddyfun_global_links; ?>!important;
}
#container .content a:hover {
color: <?php echo $tn_buddyfun_global_hover_links; ?>!important;
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


<?php if($tn_buddyfun_content_line_bg_color == "") { ?>
<?php } else { ?>
table tr td.label {
border-right:1px solid <?php echo $tn_buddyfun_content_line_bg_color; ?>!important;
}

ul.item-list li, div.pagination, #container table tbody tr.sticky td {
border-bottom:1px solid <?php echo $tn_buddyfun_content_line_bg_color; ?>!important;
}
#container table tbody tr.sticky td {
border-top: 0 none !important;
}
div.activity-comments > ul, div.item-list-tabs, #container table thead tr, #container table tbody tr.sticky td, ul#topic-post-list li.alt, div.messages-options-nav  {
background: <?php echo $tn_buddyfun_content_line_bg_color; ?>!important;
}

#custom #activity-stream li { border-bottom: 1px solid <?php echo $tn_buddyfun_content_line_bg_color; ?>!important; }

.post-content blockquote {
	border-left: 4px solid <?php echo $tn_buddyfun_content_line_bg_color; ?>!important;
}

#post-entry h2.widgettitle {
border-bottom: 1px solid <?php echo $tn_buddyfun_content_line_bg_color; ?>!important;
}

#post-entry .item-list li, .message-box, .info-group, .bp-widget, .alt-post ul li {
border-bottom: 1px solid <?php echo $tn_buddyfun_content_line_bg_color; ?>!important;
}
.group-forum {
	border-bottom: 1px solid <?php echo $tn_buddyfun_content_line_bg_color; ?>!important;   
}
#bottom-entry {
	border-top: 1px solid <?php echo $tn_buddyfun_content_line_bg_color; ?>!important;
}
.wp-caption, ol.commentlist li, #cf {
	background: <?php echo $tn_buddyfun_content_line_bg_color; ?>!important;
}
.post, .page .page, h2#post-header, #commentpost h4 {
	border-bottom: 1px solid <?php echo $tn_buddyfun_content_line_bg_color; ?>!important;
}

div.crop-img, div.crop-preview img, .post-tagged p.com a {
background-color: <?php echo $tn_buddyfun_content_line_bg_color; ?> !important;
border: 1px solid <?php echo $tn_buddyfun_content_line_bg_color; ?> !important;
}


#content #post-navigator a, #setupform {
    background: <?php echo $tn_buddyfun_content_line_bg_color; ?> !important;
}

ol.pinglist li {
    border-bottom: 1px solid <?php echo $tn_buddyfun_content_line_bg_color; ?> !important;
}
<?php } ?>





<?php if($tn_buddyfun_sidebar_header_text_color == "") { ?>
<?php } else { ?>
#content #post-navigator a {
	color: <?php echo $tn_buddyfun_sidebar_header_text_color; ?> !important;
}
<?php } ?>


<?php if($tn_buddyfun_post_title_links == "") { ?>
<?php } else { ?>
#container h1.post-title a {
	color: <?php echo $tn_buddyfun_post_title_links; ?>!important;
}
<?php } ?>



<?php if($tn_buddyfun_post_title_hover_links == "") { ?>
<?php } else { ?>
#container h1.post-title a:hover {
	color: <?php echo $tn_buddyfun_post_title_hover_links; ?> !important;
	text-decoration: none;
}
<?php } ?>





<?php if($tn_buddyfun_sidebar_line_bg_color == "") { ?>
<?php } else { ?>
#sidebar-column .item-list li {
border-bottom: 1px solid <?php echo $tn_buddyfun_sidebar_line_bg_color; ?>!important;
}
<?php } ?>


<?php if($tn_buddyfun_sidebar_box_text_link_hover_color == "") { ?>
<?php } else { ?>
#container .content #sidebar-column a:hover {
color: <?php echo $tn_buddyfun_sidebar_box_text_link_hover_color; ?>!important;
}
<?php } ?>


<?php if($tn_buddyfun_sidebar_header_text_link_color == "") { ?>
<?php } else { ?>
#container .content #sidebar-column .widgettitle a {
color: <?php echo $tn_buddyfun_sidebar_header_text_link_color; ?>!important;
}
<?php } ?>


<?php if($tn_buddyfun_global_blockquote != "") { ?>
#container table.forum .alt {
  background: <?php echo $tn_buddyfun_global_blockquote; ?> !important;
}

#post-entry li blockquote, .wire-post-content, .profile-fields .alt, .button-block .pending a, #commentpost blockquote, li ul li .activity-inner, .activity-inner .activity-inner, #groups-list .item-desc p, .activity-inner blockquote {
background: <?php if($tn_buddyfun_global_blockquote == ""){ ?><?php echo "#F5F6F5"; } else { ?><?php echo $tn_buddyfun_global_blockquote; ?><?php } ?>!important;
border: 1px solid <?php echo $tn_buddyfun_content_line_bg_color; ?>!important;
}
<?php } ?>

<?php if($tn_buddyfun_sidebar_blockquote == "") { ?>
<?php } else { ?>
#sidebar-column li blockquote {
background: <?php echo $tn_buddyfun_sidebar_blockquote; ?>!important;
}
<?php } ?>


<?php if($tn_buddyfun_header_text_color == "") { ?>
<?php } else { ?>
#custom .site-title {
color: <?php echo $tn_buddyfun_header_text_color; ?>!important;
}
<?php } ?>




<?php if($tn_buddyfun_sidebar_header_color == ""){ ?>
<?php } else { ?>
#container #call-action .call-button a {
    background: <?php echo $tn_buddyfun_sidebar_header_color; ?>!important;
	color: <?php echo $tn_buddyfun_sidebar_header_text_color; ?>!important;
}

#container ol.commentlist li div.reply a {
	background: <?php echo $tn_buddyfun_sidebar_header_color; ?>!important;
	color: <?php echo $tn_buddyfun_sidebar_header_text_color; ?>!important;
}


<?php } ?>


.time-since, .post-author, #container .alt-post small a, span.signup-description {
color: <?php if($tn_buddyfun_body_text_color == ""){ ?><?php echo "#666"; } else { ?><?php echo $tn_buddyfun_body_text_color; ?><?php } ?>!important;
}

<?php if($tn_buddyfun_topnav_block_link_color == ""){ ?>
<?php } else { ?>
ul.pagenav li a, ul.pagenav li.selected a {
    color: <?php echo $tn_buddyfun_topnav_block_link_color; ?>!important;
	background: <?php echo $tn_buddyfun_topnav_block_color; ?>!important;
}
#content ul#activity-filter-links li a,#content ul#activity-filter-links li a:hover, #content ul#activity-filter-links li.selected a  {
      color: <?php echo $tn_buddyfun_topnav_block_link_color; ?>!important;
	background: <?php echo $tn_buddyfun_topnav_block_color; ?>!important;
  }
<?php } ?>



#top-header .navigation {
	background: <?php if($tn_buddyfun_nav_bg_color == ""){ ?><?php echo "#204C6E"; } else { ?><?php echo $tn_buddyfun_nav_bg_color; ?><?php } ?> <?php if($tn_buddyfun_header_gloss_on == 'enable') { ?>url(<?php echo get_template_directory_uri(); ?>/_inc/images/gloss.png) repeat-x left -4px<?php } else { ?><?php } ?>!important;
}

<?php if($tn_buddyfun_nav_bg_color != ""){ ?>
#nav li a:hover, #container #nav ul li a,#nav li:hover a { background: <?php echo colourCreator($tn_buddyfun_nav_bg_color,-20); ?> !important; }
#container #nav ul li a:hover { background: <?php echo colourCreator($tn_buddyfun_nav_bg_color,-30); ?> !important; }
<?php } ?>

<?php if($tn_buddyfun_nav_text_link_color == ""){ ?>
<?php } else { ?>
#top-header .navigation a {
	color: <?php echo $tn_buddyfun_nav_text_link_color; ?>!important;
}
#top-header .navigation a:hover {
	color: <?php echo $tn_buddyfun_nav_text_link_hover_color; ?>!important;
}
<?php } ?>



<?php if($tn_buddyfun_footer_text_color == ""){ ?>
<?php } else { ?>
#footer {
  color: <?php echo $tn_buddyfun_footer_text_color; ?>!important;
  background: <?php echo $tn_buddyfun_footer_color; ?>!important;
}
#footer a {
  color: <?php echo $tn_buddyfun_footer_text_link_color; ?>!important;
}
<?php } ?>








<?php if($tn_buddyfun_sidebar_box_color == ""){ ?>
<?php } else { ?>
#sidebar-column {
color: <?php echo $tn_buddyfun_sidebar_box_text_color; ?>!important;
background: <?php echo $tn_buddyfun_sidebar_box_color; ?>!important;
}
<?php } ?>





#sidebar-column .widgettitle {
background: <?php if($tn_buddyfun_sidebar_header_color == ""){ ?><?php echo "#204C6E"; } else { ?><?php echo $tn_buddyfun_sidebar_header_color; ?><?php } ?> <?php if($tn_buddyfun_header_gloss_on == 'enable') { ?>url(<?php echo get_template_directory_uri(); ?>/_inc/images/gloss.png) repeat-x left -8px<?php } else { ?><?php } ?>!important;
<?php if($tn_buddyfun_sidebar_header_text_color == "" ){ ?><?php } else { ?>
color: <?php echo $tn_buddyfun_sidebar_header_text_color; ?>!important;
<?php } ?>
}



<?php if( $tn_buddyfun_sidebar_box_text_link_color == ""){ ?>
<?php } else { ?>

#container .content #sidebar-column a {
  color: <?php echo $tn_buddyfun_sidebar_box_text_link_color; ?>!important;
}
<?php } ?>



<?php if( $tn_buddyfun_userbar_text_color == ""){ ?>
<?php } else { ?>
#userbar {
    color: <?php echo $tn_buddyfun_userbar_text_color; ?>!important;
	background: <?php echo $tn_buddyfun_userbar_bg_color; ?>!important;
}
#userbar a {
    color: <?php echo $tn_buddyfun_userbar_text_link_color; ?>!important;
}
<?php } ?>




<?php if($tn_buddyfun_content_bg_color == ""){ ?>
<?php } else { ?>


.post-content table {
    background: none repeat scroll 0 0 transparent;
    border: 2px solid <?php echo colourCreator($tn_buddyfun_content_bg_color,10); ?>;
}
table.bbp-forums th, table.bbp-topics th, table.bbp-topic th, table.bbp-replies th,.post-content th {
    background: none repeat scroll 0 0 <?php echo colourCreator($tn_buddyfun_content_bg_color,10); ?>;
}
.post-content th, .post-content td,ol.commentlist li, .commentlist .reply a, #container fieldset.bbp-form {
    border: 1px solid <?php echo colourCreator($tn_buddyfun_content_bg_color,10); ?>;
}

.post-content td.label {
    border-right: 1px solid <?php echo colourCreator($tn_buddyfun_content_bg_color,10); ?> !important;
}

.activity-permalink #activity-stream, .activity-permalink #site-wide-stream { background: <?php echo $tn_buddyfun_content_bg_color; ?>!important; }

div#user-pag.pagination, .friends div.pagination, .mygroups div.pagination, .myblogs div.pagination, noscript div.pagination {
background: <?php echo $tn_buddyfun_content_bg_color; ?>!important;
}


#post-entry, #container .content #content, div#subnav.item-list-tabs, #container table tbody tr {
background: <?php echo $tn_buddyfun_content_bg_color; ?>!important;
}

ul#options-nav li {
	background: <?php echo $tn_buddyfun_content_bg_color; ?> url(_inc/images/members/options.png) no-repeat 5px center!important;
}

#top-header .top-h, #call-action {
color: <?php echo $tn_buddyfun_body_text_color; ?>!important;
background: <?php echo $tn_buddyfun_content_bg_color; ?>!important;
}

<?php } ?>



<?php if($tn_buddyfun_body_text_color == ""){ ?>
<?php } else { ?>
#container #content #right-box #optionsbar #options-nav li.current a {
color: <?php echo $tn_buddyfun_body_text_color; ?>!important;
}
<?php } ?>



<?php if($tn_buddyfun_optionbar_text_color == ""){ ?>
<?php } else { ?>
#left-box, #right-box {
color: <?php echo $tn_buddyfun_optionbar_text_color; ?>!important;
}
<?php } ?>


<?php if($tn_buddyfun_optionbar_text_link_color == ""){ ?>
<?php } else { ?>
.info-bar a, #container #content .info-group a, #container #content .info-group a  {
color: <?php echo $tn_buddyfun_optionbar_text_link_color; ?> !important;
}
<?php } ?>



#header .custom-img-header {
height: <?php echo $tn_buddyfun_image_height; ?>px !important;
}


<?php if($tn_buddyfun_header_color != "") { ?>
#top-header .top-h {
background: <?php echo $tn_buddyfun_header_color; ?>!important;
}
<?php } ?>

