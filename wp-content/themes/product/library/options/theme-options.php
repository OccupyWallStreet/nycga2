a, a:link{
	color: <?php if($dev_product_link_colour == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_link_colour; ?><?php } ?>;
}

a:hover{
	color: <?php if($dev_product_link_hover_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_product_link_hover_colour; ?><?php } ?>;
}

a:visited{
	color: <?php if($dev_product_link_visited_colour == ""){ ?><?php echo "#333333"; } else { ?><?php echo $dev_product_link_visited_colour; ?><?php } ?>;
}

a.comment-reply-link, a.button, input[type=submit], input[type=button],
ul.button-nav li a, div.generic-button a{
	<?php if(($dev_product_button_bg_image == "")&&($dev_product_button_bg != "")) { ?>
	background: <?php echo $dev_product_button_bg; ?>;
	<?php } ?>
	<?php if(($dev_product_button_bg_image != "")&&($dev_product_button_bg != "")) { ?>
	background: <?php echo $dev_product_button_bg; ?> url(<?php echo $dev_product_button_bg_image; ?>) repeat-x; 
	<?php } ?>
	<?php if(($dev_product_button_bg_image != "")&&($dev_product_button_bg == "")) { ?>
	background: #e9e9e9 url(<?php echo $dev_product_button_bg_image; ?>) repeat-x;
	<?php } ?>
	<?php if(($dev_product_button_bg_image == "")&&($dev_product_button_bg == "")) { ?>
	background: #e9e9e9 url('<?php bloginfo('template_directory'); ?>/library/styles/product-images/button_off.jpg') repeat-x; 
	<?php } ?>
	border: 1px solid <?php if($dev_product_button_border == ""){ ?><?php echo "#cccccc"; } else { ?><?php echo $dev_product_button_border; ?><?php }?>;
	color: <?php if($dev_product_button_text == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_product_button_text; ?><?php }?>;
}

a.comment-reply-link:hover, a.button:hover, a.button:focus, input[type=submit]:hover, input[type=button]:hover,
ul.button-nav li a:hover, ul.button-nav li.current a,
div.generic-button a:hover {
	<?php if(($dev_product_button_hover_bg_image == "")&&($dev_product_button_hover_bg != "")) { ?>
	background: <?php echo $dev_product_button_hover_bg; ?>;
	<?php } ?>
	<?php if(($dev_product_button_hover_bg_image != "")&&($dev_product_button_hover_bg != "")) { ?>
	background: <?php echo $dev_product_button_hover_bg; ?> url(<?php echo $dev_product_button_hover_bg_image; ?>) repeat-x; 
	<?php } ?>
	<?php if(($dev_product_button_hover_bg_image != "")&&($dev_product_button_hover_bg == "")) { ?>
	background: #dddddd url(<?php echo $dev_product_button_hover_bg_image; ?>) repeat-x;
	<?php } ?>
	<?php if(($dev_product_button_hover_bg_image == "")&&($dev_product_button_hover_bg == "")) { ?>
	background: #e9e9e9 url('<?php bloginfo('template_directory'); ?>/library/styles/product-images/button_hover.jpg') repeat-x; 
	<?php } ?>
	border: 1px solid <?php if($dev_product_button_hover_border == ""){ ?><?php echo "#cccccc"; } else { ?><?php echo $dev_product_button_hover_border; ?><?php }?>;
	color: <?php if($dev_product_button_hover_text == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_product_button_hover_text; ?><?php }?>;
}

.activity-list li .activity-inreplyto {
background: url( '<?php bloginfo('template_directory'); ?>/library/styles/product-images/replyto_arrow.gif' ) 7px 0 no-repeat;
}

body.activity-permalink .activity-list li .activity-header > p {
background: url( '<?php bloginfo('template_directory'); ?>/library/styles/product-images/activity_arrow.gif' ) top left no-repeat;
}

.activity-list .activity-header a:first-child, span.highlight {
	background: <?php if($dev_product_highlight_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_highlight_bg; ?><?php } ?>;
	color: <?php if($dev_product_highlight_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_highlight_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_highlight_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_highlight_border; ?><?php } ?>;
}

.activity-list .activity-header a:first-child:hover {
	background: <?php if($dev_product_highlight_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_highlight_bg; ?><?php } ?>;
	color: <?php if($dev_product_highlight_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_highlight_text; ?><?php } ?>;
}

.activity-list .activity-content img.thumbnail {
	border: 2px solid <?php if($dev_product_image_bg== ""){ ?><?php echo "#eeeeee"; } else { ?><?php echo $dev_product_image_bg; ?><?php } ?>;
}

.activity-list li.load-more {
	background: <?php if($dev_product_highlight_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_highlight_bg; ?><?php } ?>;
	color: <?php if($dev_product_highlight_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_highlight_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_highlight_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_highlight_border; ?><?php } ?>;
}

.activity-list div.activity-meta a {
	background: <?php if($dev_product_highlight_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_highlight_bg; ?><?php } ?>;
	color: <?php if($dev_product_highlight_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_highlight_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_highlight_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_highlight_border; ?><?php } ?>;
}

.activity-list div.activity-meta a.acomment-reply {
	background: <?php if($dev_product_highlight_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_highlight_bg; ?><?php } ?>;
	color: <?php if($dev_product_highlight_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_highlight_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_highlight_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_highlight_border; ?><?php } ?>;
}

div.activity-meta a:hover {
	background: <?php if($dev_product_element_bg == ""){ ?><?php echo "#eeeeee"; } else { ?><?php echo $dev_product_element_bg; ?><?php } ?>;
	color: <?php if($dev_product_element_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_element_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_element_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_element_border; ?><?php } ?>;
}

div.activity-meta a.acomment-reply:hover {
	background: <?php if($dev_product_element_bg == ""){ ?><?php echo "#eeeeee"; } else { ?><?php echo $dev_product_element_bg; ?><?php } ?>;
	color: <?php if($dev_product_element_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_element_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_element_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_element_border; ?><?php } ?>;
}

div.activity-comments > ul {
	background: <?php if($dev_product_highlight_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_highlight_bg; ?><?php } ?>;
	color: <?php if($dev_product_highlight_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_highlight_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_highlight_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_highlight_border; ?><?php } ?>;
}

div.activity-comments ul li {
	border-top: 1px solid <?php if($dev_product_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_border; ?><?php } ?>;
}

div.activity-comments form.ac-form {
	background: <?php if($dev_product_element_bg == ""){ ?><?php echo "#eeeeee"; } else { ?><?php echo $dev_product_element_bg; ?><?php } ?>;
	color: <?php if($dev_product_element_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_element_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_element_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_element_border; ?><?php } ?>;
}

div.activity-comments form.loading {
background-image: url( '<?php bloginfo('template_directory'); ?>/library/styles/product-images/ajax-loader.gif' );
background-position: 2% 95%;
background-repeat: no-repeat;
}

div.ac-reply-avatar img {
	border: 2px solid <?php if($dev_product_image_bg== ""){ ?><?php echo "#eeeeee"; } else { ?><?php echo $dev_product_image_bg; ?><?php } ?>;
}

body {
	<?php if(($dev_product_body_bg_image == "")&&($dev_product_body_bg != "")) { ?>
	background: <?php echo $dev_product_body_bg; ?>;
	<?php } ?>
	<?php if(($dev_product_body_bg_image != "")&&($dev_product_body_bg != "")) { ?>
	background: <?php echo $dev_product_body_bg; ?> url(<?php echo $dev_product_body_bg_image; ?>) <?php echo $dev_product_body_bg_repeat; ?>; 
	<?php } ?>
	<?php if(($dev_product_body_bg_image != "")&&($dev_product_body_bg == "")) { ?>
	background: #e1e1e1 url(<?php echo $dev_product_body_bg_image; ?>) <?php echo $dev_product_body_bg_repeat; ?>;
	<?php } ?>
	<?php if(($dev_product_body_bg_image == "")&&($dev_product_body_bg == "")) { ?>
	background: #e1e1e1; 
	<?php } ?>
	color: <?php if($dev_product_font_colour == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_font_colour; ?><?php } ?>;
	font-family: <?php if($dev_product_body_font == ""){ ?><?php echo "Helvetica, Arial, sans-serif"; } else { ?><?php echo $dev_product_body_font; ?><?php } ?>;
}

#buddypress-navigation{
	<?php if(($dev_product_bp_nav_bg_image == "")&&($dev_product_bp_nav_bg != "")) { ?>
	background: <?php echo $dev_product_bp_nav_bg_colour; ?>;
	<?php } ?>
	<?php if(($dev_product_bp_nav_bg_image != "")&&($dev_product_bp_nav_bg != "")) { ?>
	background: <?php echo $dev_product_bp_nav_bg; ?> url(<?php echo $dev_product_bp_nav_bg_image; ?>) <?php echo $dev_product_bp_nav_bg_repeat; ?>; 
	<?php } ?>
	<?php if(($dev_product_bp_nav_bg_image != "")&&($dev_product_bp_nav_bg == "")) { ?>
	background: #ffffff url(<?php echo $dev_product_bp_nav_bg_image; ?>) <?php echo $dev_product_bp_nav_bg_repeat; ?>;
	<?php } ?>
	<?php if(($dev_product_bp_nav_bg_image == "")&&($dev_product_bp_nav_bg == "")) { ?>
	background: #ffffff url('<?php bloginfo('template_directory'); ?>/library/styles/product-images/navigation_gradient.png') repeat-x; 
	<?php } ?>
}

#buddypress-navigation .sf-menu a, #buddypress-navigation .sf-menu a:visited  { /* visited pseudo selector so IE6 applies text colour*/
	color: <?php if($dev_product_bp_nav_text == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_product_bp_nav_text; ?><?php } ?>;
}

#buddypress-navigation .sf-menu li li {
	background: <?php if($dev_product_bp_nav_drop_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_bp_nav_drop_bg; ?><?php } ?>;
}

#buddypress-navigation .sf-menu li li li {
background: <?php if($dev_product_bp_nav_drop_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_bp_nav_drop_bg; ?><?php } ?>;
}

#buddypress-navigation .sf-menu li:hover, #buddypress-navigation .sf-menu li.sfHover,
#buddypress-navigation .sf-menu a:focus, #buddypress-navigation .sf-menu a:hover, #buddypress-navigation .sf-menu a:active, #buddypress-navigation .sf-menu .current_page_item, #buddypress-navigation .sf-menu .selected{
	background: <?php if($dev_product_bp_nav_hover_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_bp_nav_hover_bg; ?><?php } ?>;
	color: <?php if($dev_product_bp_nav_hover_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_bp_nav_hover_text; ?><?php } ?>;
	outline:		0;
}

#content, .container{
	background: <?php if($dev_product_content_bg == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_product_content_bg; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_content_border == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_product_content_border; ?><?php } ?>;
}

#page-navigation .sf-menu a, #page-navigation .sf-menu a:visited  { /* visited pseudo selector so IE6 applies text colour*/
	color: <?php if($dev_product_page_nav_text == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_product_page_nav_text; ?><?php } ?>;
}
#page-navigation .sf-menu li li {
	background: <?php if($dev_product_page_nav_drop_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_page_nav_drop_bg; ?><?php } ?>;
}
#page-navigation .sf-menu li li li {
	background: <?php if($dev_product_page_nav_drop_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_page_nav_drop_bg; ?><?php } ?>;
}
#page-navigation .sf-menu li:hover, #page-navigation .sf-menu li.sfHover,
#page-navigation .sf-menu a:focus, #page-navigation .sf-menu a:hover, #page-navigation .sf-menu a:active, #page-navigation .sf-menu .current_page_item, #page-navigation .sf-menu .selected{
	background: <?php if($dev_product_page_nav_hover_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_page_nav_hover_bg; ?><?php } ?>;
	color: <?php if($dev_product_page_nav_hover_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_page_nav_hover_text; ?><?php } ?>;
	outline:		0;
}

#footer{
	background: <?php if($dev_product_footer_bg == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_product_footer_bg; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_footer_border == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_product_footer_border; ?><?php } ?>;
	color: <?php if($dev_product_footer_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_footer_text; ?><?php } ?>;
}

#footer-navigation a {
	color: <?php if($dev_product_footer_nav_text == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_product_footer_nav_text; ?><?php } ?>;
}

#footer-navigation{
	<?php if(($dev_product_footer_nav_bg_image == "")&&($dev_product_footer_nav_bg != "")) { ?>
	background: <?php echo $dev_product_footer_nav_bg; ?>;
	<?php } ?>
	<?php if(($dev_product_footer_nav_bg_image != "")&&($dev_product_footer_nav_bg != "")) { ?>
	background: <?php echo $dev_product_footer_nav_bg; ?> url(<?php echo $dev_product_footer_nav_bg_image; ?>) <?php echo $dev_product_footer_nav_bg_repeat; ?>; 
	<?php } ?>
	<?php if(($dev_product_footer_nav_bg_image != "")&&($dev_product_footer_nav_bg == "")) { ?>
	background: #f3f3f3 url(<?php echo $dev_product_footer_nav_bg_image; ?>) <?php echo $dev_product_footer_nav_bg_repeat; ?>;
	<?php } ?>
	<?php if(($dev_product_footer_nav_bg_image == "")&&($dev_product_footer_nav_bg == "")) { ?>
	background: #f3f3f3; 
	<?php } ?>
	color: <?php if($dev_product_footer_nav_text == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_product_footer_nav_text; ?><?php } ?>;
}

h1{
	color: <?php if($dev_product_h1_colour == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_h1_colour; ?><?php } ?>;
	font-family: <?php if($dev_product_header_font == ""){ ?><?php echo "Helvetica, Arial, sans-serif"; } else { ?><?php echo $dev_product_header_font; ?><?php } ?>;
}

#header h1{
	color: <?php if($dev_product_header_h1_colour == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_header_h1_colour; ?><?php } ?>;
	font-family: <?php if($dev_product_header_font == ""){ ?><?php echo "Helvetica, Arial, sans-serif"; } else { ?><?php echo $dev_product_header_font; ?><?php } ?>;
}

hr {
	background: <?php if($dev_product_element_bg == ""){ ?><?php echo "#eeeeee"; } else { ?><?php echo $dev_product_element_bg; ?><?php } ?>;
}

h2{
	color: <?php if($dev_product_h2_colour == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_h2_colour; ?><?php } ?>;
	font-family: <?php if($dev_product_header_font == ""){ ?><?php echo "Helvetica, Arial, sans-serif"; } else { ?><?php echo $dev_product_header_font; ?><?php } ?>;
}

#search-bar h2{
	color: <?php if($dev_product_header_h2_colour == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_header_h2_colour; ?><?php } ?>;
	font-family: <?php if($dev_product_header_font == ""){ ?><?php echo "Helvetica, Arial, sans-serif"; } else { ?><?php echo $dev_product_header_font; ?><?php } ?>;
}

h3{
	color: <?php if($dev_product_h3_colour == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_h3_colour; ?><?php } ?>;
	font-family: <?php if($dev_product_header_font == ""){ ?><?php echo "Helvetica, Arial, sans-serif"; } else { ?><?php echo $dev_product_header_font; ?><?php } ?>;
}

h4{
	color: <?php if($dev_product_h4_colour == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_h4_colour; ?><?php } ?>;
	font-family: <?php if($dev_product_header_font == ""){ ?><?php echo "Helvetica, Arial, sans-serif"; } else { ?><?php echo $dev_product_header_font; ?><?php } ?>;
}

h5{
	color: <?php if($dev_product_h5_colour == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_h5_colour; ?><?php } ?>;
	font-family: <?php if($dev_product_header_font == ""){ ?><?php echo "Helvetica, Arial, sans-serif"; } else { ?><?php echo $dev_product_header_font; ?><?php } ?>;
}

h6{
	color: <?php if($dev_product_h6_colour == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_h6_colour; ?><?php } ?>;
	font-family: <?php if($dev_product_header_font == ""){ ?><?php echo "Helvetica, Arial, sans-serif"; } else { ?><?php echo $dev_product_header_font; ?><?php } ?>;
}

img.avatar {
	border: 2px solid <?php if($dev_product_image_bg== ""){ ?><?php echo "#eeeeee"; } else { ?><?php echo $dev_product_image_bg; ?><?php } ?>;
}

div#invite-list {
	background: <?php if($dev_product_element_bg == ""){ ?><?php echo "#eeeeee"; } else { ?><?php echo $dev_product_element_bg; ?><?php } ?>;
	color: <?php if($dev_product_element_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_element_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_element_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_element_border; ?><?php } ?>;
}

div#item-header h2 span.highlight span {
	background: <?php if($dev_product_element_bg == ""){ ?><?php echo "#eeeeee"; } else { ?><?php echo $dev_product_element_bg; ?><?php } ?>;
	color: <?php if($dev_product_element_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_element_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_element_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_element_border; ?><?php } ?>;
}

ul.item-list li {
	border-bottom: 1px solid <?php if($dev_product_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_border; ?><?php } ?>;
}

div.item-list-tabs {
	background: <?php if($dev_product_itemlist_bg == ""){ ?><?php echo "#eeeeee"; } else { ?><?php echo $dev_product_itemlist_bg; ?><?php } ?>;
	color: <?php if($dev_product_itemlist_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_itemlist_text; ?><?php } ?>;
}

div.item-list-tabs#subnav {
	border-bottom: 1px solid <?php if($dev_product_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_border; ?><?php } ?>;
}

div.item-list-tabs ul li a span.unread-count {
	color: <?php if($dev_product_unread_text == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_product_unread_text; ?><?php } ?>;
}

.ajax-loader {background: url( '<?php bloginfo('template_directory'); ?>/library/styles/product-images/ajax-loader.gif' ) center left no-repeat !important;}

a.loading {
background-image: url( '<?php bloginfo('template_directory'); ?>/library/styles/product-images/ajax-loader.gif' ) !important;
background-position: 95% 50% !important;
background-repeat: no-repeat !important;
}

ul li.loading a {
background-image: url( '<?php bloginfo('template_directory'); ?>/library/styles/product-images/ajax-loader.gif' );
background-position: 92% 50%;
background-repeat: no-repeat;
}

span.activity, div#message p {
	background: <?php if($dev_product_highlight_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_highlight_bg; ?><?php } ?>;
	color: <?php if($dev_product_highlight_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_highlight_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_highlight_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_highlight_border; ?><?php } ?>;
}

div#message.updated p {
	background: <?php if($dev_product_element_bg == ""){ ?><?php echo "#eeeeee"; } else { ?><?php echo $dev_product_element_bg; ?><?php } ?>;
	color: <?php if($dev_product_element_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_element_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_element_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_element_border; ?><?php } ?>;
}

div.messages-options-nav {
	background: <?php if($dev_product_element_bg == ""){ ?><?php echo "#eeeeee"; } else { ?><?php echo $dev_product_element_bg; ?><?php } ?>;
	color: <?php if($dev_product_element_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_element_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_element_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_element_border; ?><?php } ?>;
}

div#message.error p {
	background: <?php if($dev_product_error_bg == ""){ ?><?php echo "#e41717"; } else { ?><?php echo $dev_product_error_bg; ?><?php } ?>;
	color: <?php if($dev_product_error_text == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_product_error_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_error_border == ""){ ?><?php echo "#a71a1a"; } else { ?><?php echo $dev_product_error_border; ?><?php } ?>;
}

div#message-thread div.alt{
	background: <?php if($dev_product_alt_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_alt_bg; ?><?php } ?>;
	color: <?php if($dev_product_alt_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_alt_text; ?><?php } ?>;
}

#page-navigation{
	<?php if(($dev_product_page_nav_bg_image == "")&&($dev_product_page_nav_bg != "")) { ?>
	background: <?php echo $dev_product_page_nav_bg; ?>;
	<?php } ?>
	<?php if(($dev_product_page_nav_bg_image != "")&&($dev_product_page_nav_bg != "")) { ?>
	background: <?php echo $dev_product_page_nav_bg; ?> url(<?php echo $dev_product_page_nav_bg_image; ?>) <?php echo $dev_product_page_nav_bg_repeat; ?>; 
	<?php } ?>
	<?php if(($dev_product_page_nav_bg_image != "")&&($dev_product_page_nav_bg == "")) { ?>
	background: #ffffff url(<?php echo $dev_product_page_nav_bg_image; ?>) <?php echo $dev_product_page_nav_bg_repeat; ?>;
	<?php } ?>
	<?php if(($dev_product_page_nav_bg_image == "")&&($dev_product_page_nav_bg == "")) { ?>
	background: #ffffff url('<?php bloginfo('template_directory'); ?>/library/styles/product-images/navigation_gradient.png') repeat-x; 
	<?php } ?>
}

div.pagination {
	border-bottom: 1px solid <?php if($dev_product_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_border; ?><?php } ?>;
}

div.pending a, a.disabled {
	border: 1px solid <?php if($dev_product_pending_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_pending_border; ?><?php } ?>;
	color: <?php if($dev_product_pending_text == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_product_pending_text; ?><?php } ?>;
}

div.pending a:hover, a.disabled:hover {
	border: 1px solid <?php if($dev_product_pending_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_pending_border; ?><?php } ?>;
	color: <?php if($dev_product_pending_text == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_product_pending_text; ?><?php } ?>;
}

div.post pre, div.post code p {
	background: <?php if($dev_product_highlight_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_highlight_bg; ?><?php } ?>;
	color: <?php if($dev_product_highlight_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_highlight_text; ?><?php } ?>;
}

div.post table {
	border: 1px solid <?php if($dev_product_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_border; ?><?php } ?>;
}

div.post table th {
	border-top: 1px solid <?php if($dev_product_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_border; ?><?php } ?>;
}

div.post table td {
	border-top: 1px solid <?php if($dev_product_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_border; ?><?php } ?>;
}

div.post div.author-box, div.comment-avatar-box {
	background: <?php if($dev_product_highlight_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_highlight_bg; ?><?php } ?>;
}

div.post div.author-box img, div.comment-avatar-box img {
	border: 4px solid <?php if($dev_product_image_bg== ""){ ?><?php echo "#eeeeee"; } else { ?><?php echo $dev_product_image_bg; ?><?php } ?>;
}

div.post{
	border-bottom: 1px solid <?php if($dev_product_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_border; ?><?php } ?>;
}

div.post .wp-caption {
	background: <?php if($dev_product_highlight_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_highlight_bg; ?><?php } ?>;
	color: <?php if($dev_product_highlight_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_highlight_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_highlight_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_highlight_border; ?><?php } ?>;}

div#sidebar div.item-options {
	border-bottom: 1px solid <?php if($dev_product_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_border; ?><?php } ?>;
}

#sidebar{
	background: <?php if($dev_product_sidebar_bg == ""){ ?><?php echo "#fbfbfb"; } else { ?><?php echo $dev_product_sidebar_bg; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_sidebar_border == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_sidebar_border; ?><?php } ?>;
}

form.standard-form#signup_form div div.error {
	background: <?php if($dev_product_error_bg == ""){ ?><?php echo "#e41717"; } else { ?><?php echo $dev_product_error_bg; ?><?php } ?>;
	color: <?php if($dev_product_error_text == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_product_error_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_error_border == ""){ ?><?php echo "#a71a1a"; } else { ?><?php echo $dev_product_error_border; ?><?php } ?>;
}

form.standard-form textarea, form.standard-form input[type=text],
form.standard-form select, form.standard-form input[type=password],
.dir-search input[type=text], input[type=text], input[type=password], select {
	border: 1px solid <?php if($dev_product_input_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_input_border; ?><?php } ?>;
	background: <?php if($dev_product_input_bg == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_product_input_bg; ?><?php } ?>;
}

textarea{
	border: 1px solid <?php if($dev_product_textarea_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_textarea_border; ?><?php } ?>;
	background: <?php if($dev_product_textarea_bg == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_product_textarea_bg; ?><?php } ?>;
}

table tr td.label {
	border-right: 1px solid <?php if($dev_product_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_border; ?><?php } ?>;
}

table tr.alt {
	background: <?php if($dev_product_alt_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_alt_bg; ?><?php } ?>;
	color: <?php if($dev_product_alt_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_alt_text; ?><?php } ?>;
}

table.forum tr:first-child {
	background: <?php if($dev_product_element_bg == ""){ ?><?php echo "#eeeeee"; } else { ?><?php echo $dev_product_element_bg; ?><?php } ?>;
	color: <?php if($dev_product_element_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_element_text; ?><?php } ?>;
}

table.forum tr.sticky td {
	background: <?php if($dev_product_sticky_bg == ""){ ?><?php echo "#FFF9DB"; } else { ?><?php echo $dev_product_sticky_bg; ?><?php } ?>;
	color: <?php if($dev_product_sticky_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_sticky_text; ?><?php } ?>;
}

table.forum tr.closed td.td-title {
background-image: url( '<?php bloginfo('template_directory'); ?>/library/styles/product-images/closed.png' );
background-position: 15px 50%;
background-repeat: no-repeat;
}

table#message-threads tr.unread td {
	background: <?php if($dev_product_unread_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_unread_bg; ?><?php } ?>;
	color: <?php if($dev_product_unread_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_unread_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_unread_text == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_unread_text; ?><?php } ?>;
}

ul#topic-post-list li.alt {
	background: <?php if($dev_product_alt_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_alt_bg; ?><?php } ?>;
	color: <?php if($dev_product_alt_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_alt_text; ?><?php } ?>;
}

li span.unread-count, tr.unread span.unread-count {
	background: <?php if($dev_product_unread_bg == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_product_unread_bg; ?><?php } ?>;
	color: <?php if($dev_product_unread_text == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_unread_text; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_unread_text == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_unread_text; ?><?php } ?>;
}

form#whats-new-form {
	border-bottom: 1px solid <?php if($dev_product_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_border; ?><?php } ?>;
}

form#whats-new-form #whats-new-textarea {
	border: 1px solid <?php if($dev_product_textarea_border == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_textarea_border; ?><?php } ?>;
	background: <?php if($dev_product_textarea_bg == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_product_textarea_bg; ?><?php } ?>;
}

.shadow-spacer{
	background: url('<?php bloginfo('template_directory'); ?>/library/styles/product-images/background-shadow.png') no-repeat;
}

ul.pagination li { 
	background: <?php if($dev_product_slideshow_tabs_bg == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_slideshow_tabs_bg; ?><?php } ?>;
	font-size: 16px;}
ul.pagination{ 
	background: <?php if($dev_product_slideshow_tabs_bg == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_slideshow_tabs_bg; ?><?php } ?>;
	border: 1px solid <?php if($dev_product_slideshow_tabs_bg == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_slideshow_tabs_bg; ?><?php } ?>;}
ul.pagination a{ 
	background: <?php if($dev_product_slideshow_tabs_bg == ""){ ?><?php echo "#f1f1f1"; } else { ?><?php echo $dev_product_slideshow_tabs_bg; ?><?php } ?>;
	color: <?php if($dev_product_slideshow_tabs_link == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_product_slideshow_tabs_link; ?><?php } ?>;
}
ul.pagination li.active a {
	background: <?php if($dev_product_slideshow_tabs_bg_active == ""){ ?><?php echo "#e2e2e2"; } else { ?><?php echo $dev_product_slideshow_tabs_bg_active; ?><?php } ?>;
	color: <?php if($dev_product_slideshow_tabs_link_active == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_product_slideshow_tabs_link_active; ?><?php } ?>;
}