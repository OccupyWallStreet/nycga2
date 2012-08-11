<?php if ($dev_gallery_textshadow_color != ""){?>
	#branding, #widgets h4, a.button, input[type=submit], input[type=button],
	ul.button-nav li a, div.generic-button a, 
	#homepage-content .nav a, #homepage-content .nav a:visited, #homepage-content .nav a:link,
	.nav a, .nav a:visited, .nav a:link, 
	.nav ul a, h1, h2, h3, h4, h5, h6, div.item-list-tabs, div.item-list-tabs#subnav, .activity-list li.load-more{
	text-shadow: 0px 1px 0 <?php echo $dev_gallery_textshadow_color; ?>;
	}
<?php } ?>

<?php if ($dev_gallery_boxshadow_color != ""){?>
	.grid-block, .grid-block-end, #slider-wrapper, #slider-wrapper-small, #whats-new-form, #respond,
	#homepage-content .nav a, #homepage-content .nav a:visited, #homepage-content .nav a:link, 
	.nav a, .nav a:visited, .nav a:link, .nav ul, .attachment-post-thumbnail, .entry img, img.avatar,
	div.item-list-tabs, div.item-list-tabs#subnav, .activity-list li.load-more{
	-moz-box-shadow: 0 4px 2px <?php echo $dev_gallery_boxshadow_color; ?>;
	-webkit-box-shadow: 0 4px 2px <?php echo $dev_gallery_boxshadow_color; ?>;
	-moz-box-shadow: 0 4px 2px <?php echo $dev_gallery_boxshadow_color; ?>;
	}
<?php } ?>

<?php if (($dev_gallery_avatar_background_color != "") || ($dev_gallery_avatar_border_color != "")){?>
.attachment-post-thumbnail, .entry img, img.avatar{
	border: 1px solid <?php echo $dev_gallery_avatar_border_color; ?>;
	background: none repeat scroll 0 0 <?php echo $dev_gallery_avatar_background_color; ?>;
}
<?php } ?>

<?php if (($dev_gallery_grid_background_color != "") || ($dev_gallery_grid_border_color != "")){?>
	.grid-block, .grid-block-end {
	background: none repeat scroll 0 0 <?php echo $dev_gallery_grid_background_color; ?>;
	border: 1px solid <?php echo $dev_gallery_grid_border_color; ?>;
	}
<?php } ?>

<?php if (($dev_gallery_slider_background_color != "") || ($dev_gallery_slider_border_color != "")){?>
#slider-wrapper, #slider-wrapper-small, #whats-new-form, #respond{
	background: none repeat scroll 0 0 <?php echo $dev_gallery_slider_background_color; ?>;
	border: 1px solid <?php echo $dev_gallery_slider_border_color; ?>;
	}
<?php } ?>

<?php if ($dev_gallery_comment_border_color != ""){?>
ol.commentlist{
	border-top: 1px solid <?php echo $dev_gallery_comment_border_color; ?>;
	border-bottom: 1px solid <?php echo $dev_gallery_comment_border_color; ?>;
}
<?php } ?>

<?php if ($dev_gallery_post_border != ""){?>
.post{
	border-bottom: 1px solid <?php echo $dev_gallery_post_border; ?>;
}
<?php } ?>

<?php if (($dev_gallery_grid_border_color != "") || ($dev_gallery_grid_background_color != "")){?>
.grid-block, .grid-block-end {
background: none repeat scroll 0 0 <?php echo $dev_gallery_grid_background_color; ?>;
border: 1px solid <?php echo $dev_gallery_grid_border_color; ?>;
}
<?php } ?>

<?php if ($dev_gallery_branding_color != ""){?>
#branding, #branding a, #branding a:link, #branding a:visited, #branding a:hover{
	color: <?php echo $dev_gallery_branding_color; ?>;
}
<?php } ?>

<?php if ($dev_gallery_font_color != ""){?>
body{
	color: <?php echo $dev_gallery_font_color; ?>;
}
<?php } ?>

<?php if ($dev_gallery_h1_color != ""){?>
h1{
	color: <?php echo $dev_gallery_h1_color; ?>;
}
<?php } ?>

<?php if ($dev_gallery_h2_color != ""){?>
h2{
	color: <?php echo $dev_gallery_h2_color; ?>;
}
<?php } ?>

<?php if ($dev_gallery_h3_color != ""){?>
h3{
	color: <?php echo $dev_gallery_h3_color; ?>;
}
<?php } ?>

<?php if ($dev_gallery_h4_color != ""){?>
h4{
	color: <?php echo $dev_gallery_h4_color; ?>;
}
<?php } ?>

<?php if ($dev_gallery_h5_color != ""){?>
h5{
	color: <?php echo $dev_gallery_h5_color; ?>;
}
<?php } ?>

<?php if ($dev_gallery_h6_color != ""){?>
h6{
	color: <?php echo $dev_gallery_h6_color; ?>;
}
<?php } ?>

<?php if (($dev_gallery_button_background_color != "") || ($dev_gallery_button_text_color != "") || ($dev_gallery_button_border_color != "")){?>
a.comment-reply-link, a.button, input[type=submit], input[type=button],
ul.button-nav li a, div.generic-button a{
	background: <?php echo $dev_gallery_button_background_color; ?>;
	border: 1px solid <?php echo $dev_gallery_button_border_color; ?>;
	color: <?php echo $dev_gallery_button_text_color; ?>;
}
<?php } ?>

<?php if (($dev_gallery_button_hover_background_color != "") || ($dev_gallery_button_hover_text_color != "") || ($dev_gallery_button_hover_border_color != "")){?>
	a.comment-reply-link:hover, a.button:hover, a.button:focus, input[type=submit]:hover, input[type=button]:hover,
	ul.button-nav li a:hover, ul.button-nav li.current a,
	div.generic-button a:hover{
	background: <?php echo $dev_gallery_button_hover_background_color; ?>;
	border: 1px solid <?php echo $dev_gallery_button_hover_border_color; ?>;
	color: <?php echo $dev_gallery_button_hover_text_color; ?>;
}
<?php } ?>

<?php if (($dev_gallery_form_background_color != "") || ($dev_gallery_form_color != "") || ($dev_gallery_form_border_color != "")){?>
input[type="text"], textarea, input[type="textarea"],textarea{
	background: <?php echo $dev_gallery_form_background_color; ?>;
	border: 1px solid <?php echo $dev_gallery_form_border_color; ?>;
	color: <?php echo $dev_gallery_form_color; ?>;
}
<?php } ?>

<?php if (($dev_gallery_nav_background_color != "") || ($dev_gallery_nav_text_color != "") || ($dev_gallery_nav_border_color != "")){?>
.nav a, .nav a:visited, .nav a:link{
	background: <?php echo $dev_gallery_nav_background_color; ?>;
	border: 1px solid <?php echo $dev_gallery_nav_border_color; ?>;
	color: <?php echo $dev_gallery_nav_text_color; ?>;
}
<?php } ?>

<?php if (($dev_gallery_nav_hover_background_color != "") || ($dev_gallery_nav_hover_text_color != "") || ($dev_gallery_nav_hover_border_color != "")){?>
.nav .current a, .nav li:hover > a, .nav li.current_page_item a, .nav ul, .nav ul a:hover, .nav ul li:hover a, .nav li:hover li a{
	background: <?php echo $dev_gallery_nav_hover_background_color; ?>;
	border: 1px solid <?php echo $dev_gallery_nav_hover_border_color; ?>;
	color: <?php echo $dev_gallery_nav_hover_text_color; ?>;
}
<?php } ?>

<?php if (($dev_gallery_nav_side_background_color != "") || ($dev_gallery_nav_side_text_color != "") || ($dev_gallery_nav_side_border_color != "")){?>
#homepage-content .nav a, #homepage-content .nav a:visited, #homepage-content .nav a:link{
	background: <?php echo $dev_gallery_nav_side_background_color; ?>;
	border: 1px solid <?php echo $dev_gallery_nav_side_border_color; ?>;
	color: <?php echo $dev_gallery_nav_side_text_color; ?>;
}
<?php } ?>

<?php if (($dev_gallery_nav_side_hover_background_color != "") || ($dev_gallery_nav_side_hover_text_color != "") || ($dev_gallery_nav_side_hover_border_color != "")){?>
#homepage-content .nav .current a, #homepage-content .nav li:hover > a, #homepage-content .nav li.current_page_item a, #homepage-content .nav ul, #homepage-content .nav ul a:hover, #homepage-content .nav ul li:hover a, #homepage-content .nav li:hover li a{
	background: <?php echo $dev_gallery_nav_side_hover_background_color; ?>;
	border: 1px solid <?php echo $dev_gallery_nav_side_hover_border_color; ?>;
	color: <?php echo $dev_gallery_nav_side_hover_text_color; ?>;
}
<?php } ?>

<?php if ($dev_gallery_link_color != ""){?>
a, a:link, #footer a, #footer a:link{
	color: <?php echo $dev_gallery_link_color; ?>;
}
<?php } ?>

<?php if ($dev_gallery_link_hover_color != ""){?>
a:active, a:hover{
	color: <?php echo $dev_gallery_link_hover_color; ?>;
}
<?php } ?>

<?php if ($dev_gallery_link_visited_color != ""){?>
a:visited{
	color: <?php echo $dev_gallery_link_visited_color; ?>;
}
<?php } ?>

<?php if ($dev_gallery_alt != ""){?>
div#message-thread div.alt, ul#topic-post-list li.alt, div.messages-options-nav, li span.unread-count, tr.unread span.unread-count, table#message-threads tr.unread td span.activity, table#message-threads tr.unread td, div.activity-comments form.ac-form, div.activity-comments > ul, table tr.alt, table.forum tr:first-child, div#invite-list{
	background: <?php echo $dev_gallery_alt; ?>;
}
<?php } ?>

<?php if ($dev_gallery_alt != ""){?>
ul.item-list li{
	border-top: 1px solid <?php echo $dev_gallery_alt; ?>;
	border-bottom: 1px solid <?php echo $dev_gallery_alt; ?>;
}
<?php } ?>

<?php if (($dev_gallery_loading_background != "") || ($dev_gallery_loading_color != "")){?>
.activity-list li.load-more{
	color: <?php echo $dev_gallery_loading_color; ?>;
	background: <?php echo $dev_gallery_loading_background; ?>;
}
<?php } ?>

<?php if ($dev_gallery_nav != ""){?>
div.pagination#user-pag, .friends div.pagination, .mygroups div.pagination, .myblogs div.pagination, noscript div.pagination, div.item-list-tabs#subnav, div.item-list-tabs{
	background: <?php echo $dev_gallery_nav; ?>;
}
<?php } ?>

<?php if ($dev_gallery_nav_select != ""){?>
div.item-list-tabs ul li.selected a, div.item-list-tabs ul li.current a{
	background: <?php echo $dev_gallery_nav_select; ?>;
}
<?php } ?>

<?php if (($dev_gallery_body_size != "") || ($dev_gallery_body_lineheight != "")){?>
body{
	font-size: <?php echo $dev_gallery_body_size; ?>px;
	line-height: <?php echo $dev_gallery_body_lineheight; ?>px;
}
<?php } ?>

<?php if ($dev_gallery_h1_size != ""){?>
h1{
	font-size: <?php echo $dev_gallery_h1_size; ?>;px
}
<?php } ?>

<?php if ($dev_gallery_h2_size != ""){?>
h2{
	font-size: <?php echo $dev_gallery_h2_size; ?>px;
}
<?php } ?>

<?php if ($dev_gallery_h3_size != ""){?>
h3{
	font-size: <?php echo $dev_gallery_h3_size; ?>px;
}
<?php } ?>

<?php if ($dev_gallery_h4_size != ""){?>
h4{
	font-size: <?php echo $dev_gallery_h4_size; ?>px;
}
<?php } ?>

<?php if (($dev_gallery_nav_size != "") || ($dev_gallery_nav_padding)){?>
.nav a, .nav a:visited, .nav a:link, .nav .current a, .nav li:hover > a, .nav li.current_page_item a{
	font-size: <?php echo $dev_gallery_nav_size; ?>px;
	padding: <?php echo $dev_gallery_nav_padding; ?>px;
}
<?php } ?>

<?php if (($dev_gallery_nav_side_size != "") || ($dev_gallery_nav_side_padding)){?>
#homepage-content .nav a, #homepage-content .nav a:visited, #homepage-content .nav a:link, #homepage-content .nav a, #homepage-content .nav a:visited, #homepage-content .nav a:link{
	font-size: <?php echo $dev_gallery_nav_side_size; ?>px;
	padding: <?php echo $dev_gallery_nav_side_padding; ?>px;
}
<?php } ?>