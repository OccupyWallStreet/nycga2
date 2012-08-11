a, a:link{
	color: <?php if($dev_buddydaily_link_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_link_colour; ?><?php } ?>;
}

a:hover{
	color: <?php if($dev_buddydaily_link_hover_colour == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_buddydaily_link_hover_colour; ?><?php } ?>;
}

a:visited{
	color: <?php if($dev_buddydaily_link_visited_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_link_visited_colour; ?><?php } ?>;
}

a:focus { outline: 1px dotted #ccc; }

a.comment-reply-link,a.button, input[type=submit], input[type=button],
ul.button-nav li a, div.generic-button a{
	<?php if(($dev_buddydaily_button_background_image == "")&&($dev_buddydaily_button_background_colour != "")) { ?>
	background: <?php echo $dev_buddydaily_button_background_colour; ?>;
	<?php } ?>
	<?php if(($dev_buddydaily_button_background_image != "")&&($dev_buddydaily_button_background_colour != "")) { ?>
	background: <?php echo $dev_buddydaily_button_background_colour; ?> url(<?php echo $dev_buddydaily_button_background_image; ?>) <?php echo $dev_buddydaily_button_image_repeat; ?>; 
	<?php } ?>
	<?php if(($dev_buddydaily_button_background_image != "")&&($dev_buddydaily_button_background_colour == "")) { ?>
	background: #eeeeee url(<?php echo $dev_buddydaily_button_background_image; ?>) <?php echo $dev_buddydaily_button_image_repeat; ?>;
	<?php } ?>
	<?php if(($dev_buddydaily_button_background_image == "")&&($dev_buddydaily_button_background_colour == "")) { ?>
	background: #eeeeee url('<?php bloginfo('template_directory'); ?>/library/styles/daily-images/button_off.jpg') repeat-x; 
	<?php } ?>
	border: 1px solid <?php if($dev_buddydaily_button_border == ""){ ?><?php echo "#cccccc"; } else { ?><?php echo $dev_buddydaily_button_border; ?><?php }?>;
	color: <?php if($dev_buddydaily_button_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_button_colour; ?><?php }?>;
}


a.comment-reply-link:hover, a.button:hover, a.button:focus, input[type=submit]:hover, input[type=button]:hover,
ul.button-nav li a:hover, ul.button-nav li.current a,
div.generic-button a:hover {
	<?php if(($dev_buddydaily_button_hover_background_image == "")&&($dev_buddydaily_button_hover_background_colour != "")) { ?>
	background: <?php echo $dev_buddydaily_button_hover_background_colour; ?>;
	<?php } ?>
	<?php if(($dev_buddydaily_button_hover_background_image != "")&&($dev_buddydaily_button_hover_background_colour != "")) { ?>
	background: <?php echo $dev_buddydaily_button_hover_background_colour; ?> url(<?php echo $dev_buddydaily_button_hover_background_image; ?>) <?php echo $dev_buddydaily_button_hover_image_repeat; ?>; 
	<?php } ?>
	<?php if(($dev_buddydaily_button_hover_background_image != "")&&($dev_buddydaily_button_hover_background_colour == "")) { ?>
	background: #eeeeee url(<?php echo $dev_buddydaily_button_hover_background_image; ?>) <?php echo $dev_buddydaily_button_hover_image_repeat; ?>;
	<?php } ?>
	<?php if(($dev_buddydaily_button_hover_background_image == "")&&($dev_buddydaily_button_hover_background_colour == "")) { ?>
	background: #eeeeee url('<?php bloginfo('template_directory'); ?>/library/styles/daily-images/button_hover.jpg') repeat-x; 
	<?php } ?>
	border: 1px solid <?php if($dev_buddydaily_button_hover_border_colour == ""){ ?><?php echo "#cccccc"; } else { ?><?php echo $dev_buddydaily_button_button_hover_border_colour; ?><?php }?>;
	color: <?php if($dev_buddydaily_button_hover_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_button_hover_colour; ?><?php }?>;
}

.activity-list li .activity-inreplyto {
background: url( <?php bloginfo('template_directory'); ?>/library/styles/daily-images/replyto_arrow.gif ) 7px 0 no-repeat;
}

body.activity-permalink .activity-list li .activity-header > p {
background: url( <?php bloginfo('template_directory'); ?>/library/styles/daily-images/activity_arrow.gif ) top left no-repeat;
}

.activity-list .activity-header a:first-child, span.highlight {
	background: <?php if($dev_buddydaily_child_background_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_child_background_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_child_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_child_border_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_child_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_child_colour; ?><?php } ?>;
}

.activity-list .activity-header a:first-child:hover {
	background: <?php if($dev_buddydaily_child_hover_background_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_child_hover_background_colour; ?><?php } ?> !important;
}

.activity-list .activity-content img.thumbnail {
	border: 2px solid <?php if($dev_buddydaily_image_border_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_image_border_colour; ?><?php } ?>;
}

.activity-list li.load-more {
	background: <?php if($dev_buddydaily_load_background_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_load_background_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_load_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_load_border_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_load_text_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_load_text_colour; ?><?php } ?>;
}

.activity-list div.activity-meta a {
	background: <?php if($dev_buddydaily_activity_background_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_activity_background_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_activity_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_activity_border_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_activity_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_activity_colour; ?><?php } ?>;
}

.activity-list div.activity-meta a.acomment-reply {
background: <?php if($dev_buddydaily_activity_background_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_activity_background_colour; ?><?php } ?>;
border: 1px solid <?php if($dev_buddydaily_activity_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_activity_border_colour; ?><?php } ?>;
color: <?php if($dev_buddydaily_activity_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_activity_colour; ?><?php } ?>;
}

div.activity-meta a:hover {
	background: <?php if($dev_buddydaily_activity_hover_background_colour == ""){ ?><?php echo "#DDDDDD"; } else { ?><?php echo $dev_buddydaily_activity_hover_background_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_activity_hover_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_activity_hover_border_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_activity_hover_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_activity_hover_colour; ?><?php } ?>;
}

div.activity-meta a.acomment-reply:hover {
background: <?php if($dev_buddydaily_activity_hover_background_colour == ""){ ?><?php echo "#DDDDDD"; } else { ?><?php echo $dev_buddydaily_activity_hover_background_colour; ?><?php } ?>;
border: 1px solid <?php if($dev_buddydaily_activity_hover_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_activity_hover_border_colour; ?><?php } ?>;
color: <?php if($dev_buddydaily_activity_hover_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_activity_hover_colour; ?><?php } ?>;
}

div.activity-comments > ul {
	background: <?php if($dev_buddydaily_activity_comments_background_colour == ""){ ?><?php echo "#F5F5F5"; } else { ?><?php echo $dev_buddydaily_activity_comments_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_activity_comments_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_activity_comments_colour; ?><?php } ?>;
}

div.activity-comments ul li {
	border-top: 2px solid <?php if($dev_buddydaily_activity_comments_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_activity_comments_border_colour; ?><?php } ?>;
}

div.activity-comments form.ac-form {
	background: <?php if($dev_buddydaily_activity_comments_background_colour == ""){ ?><?php echo "#F5F5F5"; } else { ?><?php echo $dev_buddydaily_activity_comments_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_activity_comments_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_activity_comments_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_activity_comments_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_activity_comments_border_colour; ?><?php } ?>;
}

div.activity-comments form.loading {
background-image: url( <?php bloginfo('template_directory'); ?>/library/styles/daily-images/ajax-loader.gif );
background-position: 2% 95%;
background-repeat: no-repeat;
}

div.ac-reply-avatar img {
	border: 2px solid <?php if($dev_buddydaily_author_box_border_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_author_box_border_colour; ?><?php } ?>;
}

body {
	background: #f9f9f9;
	<?php if(($dev_buddydaily_body_background_image == "")&&($dev_buddydaily_body_background_colour != "")) { ?>
	background: <?php echo $dev_buddydaily_body_background_colour; ?>;
	<?php } ?>
	<?php if(($dev_buddydaily_body_background_image != "")&&($dev_buddydaily_body_background_colour != "")) { ?>
	background: <?php echo $dev_buddydaily_body_background_colour; ?> url(<?php echo $dev_buddydaily_body_background_image; ?>) <?php echo $dev_buddydaily_body_image_repeat; ?>; 
	<?php } ?>
	<?php if(($dev_buddydaily_body_background_image != "")&&($dev_buddydaily_body_background_colour == "")) { ?>
	background: #f9f9f9 url(<?php echo $dev_buddydaily_body_background_image; ?>) <?php echo $dev_buddydaily_body_image_repeat; ?>;
	<?php } ?>
	<?php if(($dev_buddydaily_body_background_image == "")&&($dev_buddydaily_body_background_colour == "")) { ?>
	background: #f9f9f9; 
	<?php } ?>
	color: <?php if($dev_buddydaily_body_colour == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_buddydaily_body_colour; ?><?php } ?>;
	font-family: <?php if($dev_buddydaily_body_font == ""){ ?><?php echo "Arial, sans-serif"; } else { ?><?php echo $dev_buddydaily_body_font; ?><?php } ?>;
}

#breadcrumb-navigation{
	background: <?php if($dev_buddydaily_breadcrumb_background_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $dev_buddydaily_breadcrumb_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_breadcrumb_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_breadcrumb_colour; ?><?php } ?>;
	border-top: 1px solid <?php if($dev_buddydaily_breadcrumb_border_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_buddydaily_breadcrumb_border_colour; ?><?php } ?>;
}

.byline{
	color: <?php if($dev_buddydaily_byline_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_byline_colour; ?><?php } ?>;
}

#category-navigation{
	background: <?php if($dev_buddydaily_category_background_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_category_background_colour; ?><?php } ?>;
	border-top: 1px solid <?php if($dev_buddydaily_category_border_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_buddydaily_category_border_colour; ?><?php } ?>;
}

#category-navigation .sf-menu a {
border-right: 1px solid <?php if($dev_buddydaily_category_menu_border_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_buddydaily_category_menu_border_colour; ?><?php } ?>;
}

#category-navigation .sf-menu a, #category-navigation .sf-menu a:visited  { /* visited pseudo selector so IE6 applies text colour*/
	color: <?php if($dev_buddydaily_category_menu_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_buddydaily_category_menu_colour; ?><?php } ?>;
}
#category-navigation .sf-menu li {
background: <?php if($dev_buddydaily_category_menu_background_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_category_menu_background_colour; ?><?php } ?>;
}
#category-navigation .sf-menu li li {
	background: <?php if($dev_buddydaily_category_menu_background_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_category_menu_background_colour; ?><?php } ?>;
}
#category-navigation .sf-menu li li li {background: <?php if($dev_buddydaily_category_menu_background_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_category_menu_background_colour; ?><?php } ?>;
}
#category-navigation .sf-menu li:hover, #category-navigation .sf-menu li.sfHover,
#category-navigation .sf-menu a:focus, #category-navigation .sf-menu a:hover, #category-navigation .sf-menu a:active, #category-navigation .sf-menu .current_page_item, #category-navigation .sf-menu .selected{
background: <?php if($dev_buddydaily_category_menu_selected_background_colour == ""){ ?><?php echo "#d0d0d0"; } else { ?><?php echo $dev_buddydaily_category_menu_selected_background_colour; ?><?php } ?>;
color: <?php if($dev_buddydaily_category_menu_selected_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_category_menu_selected_colour; ?><?php } ?>;
	outline:		0;
}

.content-block{
	border: 1px solid <?php if($dev_buddydaily_column_border_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $dev_buddydaily_column_border_colour; ?><?php } ?>;
}

.content-block-end{
	border: 1px solid <?php if($dev_buddydaily_column_border_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $dev_buddydaily_column_border_colour; ?><?php } ?>;
}

.page-navigation .sf-menu a {
	border-right: 1px solid <?php if($dev_buddydaily_pagination_menu_border_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_buddydaily_pagination_menu_border_colour; ?><?php } ?>;
}

.page-navigation .sf-menu a, .page-navigation .sf-menu a:visited  { /* visited pseudo selector so IE6 applies text colour*/
color: <?php if($dev_buddydaily_pagination_menu_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_pagination_menu_colour; ?><?php } ?>;
}
.page-navigation .sf-menu li {
background: <?php if($dev_buddydaily_pagination_menu_background_colour == ""){ ?><?php echo "#d0d0d0"; } else { ?><?php echo $dev_buddydaily_pagination_menu_background_colour; ?><?php } ?>;
}

.page-navigation .sf-menu li li {
	background: <?php if($dev_buddydaily_pagination_menu_background_colour == ""){ ?><?php echo "#d0d0d0"; } else { ?><?php echo $dev_buddydaily_pagination_menu_background_colour; ?><?php } ?>;
}

.page-navigation .sf-menu li li li {
	background: <?php if($dev_buddydaily_pagination_menu_background_colour == ""){ ?><?php echo "#d0d0d0"; } else { ?><?php echo $dev_buddydaily_pagination_menu_background_colour; ?><?php } ?>;
}

.page-navigation .sf-menu li:hover, .page-navigation .sf-menu li.sfHover,
.page-navigation .sf-menu a:focus, .page-navigation .sf-menu a:hover, .page-navigation .sf-menu a:active, .page-navigation .sf-menu .current_page_item, .page-navigation .sf-menu .selected{
	background: <?php if($dev_buddydaily_pagination_menu_selected_background_colour == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_buddydaily_pagination_menu_selected_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_pagination_menu_selected_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_pagination_menu_selected_colour; ?><?php } ?>;
	outline:		0;
}

div#container {
	background: <?php if($dev_buddydaily_container_background_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_container_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_container_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_container_colour; ?><?php } ?>;
}

.dark-container{
	background: <?php if($dev_buddydaily_dark_background_colour == ""){ ?><?php echo "#F9F9F9"; } else { ?><?php echo $dev_buddydaily_dark_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_dark_text_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_dark_text_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_dark_border_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $dev_buddydaily_dark_border_colour; ?><?php } ?>;
}

.light-container, .post{
	background: <?php if($dev_buddydaily_light_background_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_light_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_light_text_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_light_text_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_light_border_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $dev_buddydaily_light_border_colour; ?><?php } ?>;
}

.light-container input[type=text]{
	border: 1px solid <?php if($dev_buddydaily_input_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_input_border_colour; ?><?php } ?>;
	background: <?php if($dev_buddydaily_input_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_buddydaily_input_background_colour; ?><?php } ?>;
}

#information-bar{
	color: <?php if($dev_buddydaily_information_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_information_colour; ?><?php } ?>;
}

#footer-navigation {
	color: <?php if($dev_buddydaily_footer_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_footer_colour; ?><?php } ?>;
}

#footer-navigation a {
	color: <?php if($dev_buddydaily_footer_link_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_buddydaily_footer_link_colour; ?><?php } ?>;
}

#footer-navigation{
	background: <?php if($dev_buddydaily_footer_background_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_footer_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_footer_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_footer_colour; ?><?php } ?>;
}

#search-bar input[type=text] {
	border: 1px solid <?php if($dev_buddydaily_input_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_input_border_colour; ?><?php } ?>;
	background: <?php if($dev_buddydaily_input_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_buddydaily_input_background_colour; ?><?php } ?>;
}

.padder h3, h2.pagetitle{
	color: <?php if($dev_buddydaily_header_feature_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_header_feature_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_header_feature_border_colour == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_buddydaily_header_feature_border_colour; ?><?php } ?>;	
	background: <?php if($dev_buddydaily_header_background_colour == ""){ ?><?php echo "#f9f9f9"; } else { ?><?php echo $dev_buddydaily_header_background_colour; ?><?php } ?>;
}

hr {
	background: <?php if($dev_buddydaily_hr_colour == ""){ ?><?php echo "#E7E7E7"; } else { ?><?php echo $dev_buddydaily_hr_colour; ?><?php } ?>;
}

h1{
	color: <?php if($dev_buddydaily_h1_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_h1_colour; ?><?php } ?>;
	font-family: <?php if($dev_buddydaily_header_font == ""){ ?><?php echo "Georgia, sans-serif"; } else { ?><?php echo $dev_buddydaily_header_font; ?><?php } ?>;
}

h2{
	color: <?php if($dev_buddydaily_h2_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_h2_colour; ?><?php } ?>;
	font-family: <?php if($dev_buddydaily_header_font == ""){ ?><?php echo "Georgia, sans-serif"; } else { ?><?php echo $dev_buddydaily_header_font; ?><?php } ?>;
}

h3{
	color: <?php if($dev_buddydaily_h3_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_h3_colour; ?><?php } ?>;
	font-family: <?php if($dev_buddydaily_header_font == ""){ ?><?php echo "Georgia, sans-serif"; } else { ?><?php echo $dev_buddydaily_header_font; ?><?php } ?>;
}

h4{
	color: <?php if($dev_buddydaily_h4_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_h4_colour; ?><?php } ?>;
	font-family: <?php if($dev_buddydaily_header_font == ""){ ?><?php echo "Georgia, sans-serif"; } else { ?><?php echo $dev_buddydaily_header_font; ?><?php } ?>;
}

h5{
	color: <?php if($dev_buddydaily_h5_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_h5_colour; ?><?php } ?>;
	font-family: <?php if($dev_buddydaily_header_font == ""){ ?><?php echo "Georgia, sans-serif"; } else { ?><?php echo $dev_buddydaily_header_font; ?><?php } ?>;
}

h6{
	color: <?php if($dev_buddydaily_h6_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_h6_colour; ?><?php } ?>;
	font-family: <?php if($dev_buddydaily_header_font == ""){ ?><?php echo "Georgia, sans-serif"; } else { ?><?php echo $dev_buddydaily_header_font; ?><?php } ?>;
}

img.avatar {
		border: 2px solid <?php if($dev_buddydaily_author_box_border_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_author_box_border_colour; ?><?php } ?>;
}

div#invite-list {
	background: <?php if($dev_buddydaily_invite_background_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_invite_background_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_invite_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_invite_border_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_invite_text_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_invite_text_colour; ?><?php } ?>;
}

div#item-header h2 span.highlight span {
	background: <?php if($dev_buddydaily_highlight_background_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_highlight_background_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_highlight_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_highlight_border_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_highlight_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_highlight_colour; ?><?php } ?>;
}

ul.item-list li {
	border-bottom: 1px solid <?php if($dev_buddydaily_item_border_colour == ""){ ?><?php echo "#EAEAEA"; } else { ?><?php echo $dev_buddydaily_item_border_colour; ?><?php } ?>;
}

div.item-list-tabs {
	background: <?php if($dev_buddydaily_tabs_background_colour == ""){ ?><?php echo "#DDDDDD"; } else { ?><?php echo $dev_buddydaily_tabs_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_tabs_text_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_tabs_text_colour; ?><?php } ?>;
}

div.item-list-tabs ul li.selected a,
div.item-list-tabs ul li.current a {
	background: <?php if($dev_buddydaily_tabs_selected_background_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_tabs_selected_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_tabs_selected_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_tabs_selected_colour; ?><?php } ?>;
}

div.item-list-tabs#subnav {
	background: <?php if($dev_buddydaily_tabs_sub_background_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_tabs_sub_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_tabs_sub_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_tabs_sub_colour; ?><?php } ?>;
	border-bottom: 1px solid <?php if($dev_buddydaily_tabs_sub_border_colour == ""){ ?><?php echo "#DDDDDD"; } else { ?><?php echo $dev_buddydaily_tabs_sub_border_colour; ?><?php } ?>;
}

div.item-list-tabs ul li a span.unread-count {
	color: <?php if($dev_buddydaily_tabs_unread_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_tabs_unread_colour; ?><?php } ?>;
}

.ajax-loader {background: url( <?php bloginfo('template_directory'); ?>/library/styles/daily-images/ajax-loader.gif ) center left no-repeat !important;}

a.loading {
background-image: url( <?php bloginfo('template_directory'); ?>/library/styles/daily-images/ajax-loader.gif ) !important;
background-position: 95% 50% !important;
background-repeat: no-repeat !important;
}

ul li.loading a {
background-image: url( <?php bloginfo('template_directory'); ?>/library/styles/daily-images/ajax-loader.gif );
background-position: 92% 50%;
background-repeat: no-repeat;
}

span.activity, div#message p {
	color: <?php if($dev_buddydaily_messages_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_messages_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_messages_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_messages_border_colour; ?><?php } ?>;
	background: <?php if($dev_buddydaily_messages_background_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_messages_background_colour; ?><?php } ?>;
}

div#message.updated p {
border: 1px solid <?php if($dev_buddydaily_updated_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_updated_border_colour; ?><?php } ?>;
background: <?php if($dev_buddydaily_updated_background_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_updated_background_colour; ?><?php } ?>;
}

div.messages-options-nav {	
	background: <?php if($dev_buddydaily_message_options_background_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_message_options_background_colour; ?><?php } ?>;
}

div#message.error p {
	color: <?php if($dev_buddydaily_error_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_buddydaily_error_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_error_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_error_border_colour; ?><?php } ?>;
	background: <?php if($dev_buddydaily_error_background_colour == ""){ ?><?php echo "#e41717"; } else { ?><?php echo $dev_buddydaily_updated_error_colour; ?><?php } ?>;
}

div#message-thread div.alt {
	color: <?php if($dev_buddydaily_messages_alt_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_messages_alt_colour; ?><?php } ?>;
	background: <?php if($dev_buddydaily_messages_alt_background_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $dev_buddydaily_messages_alt_background_colour; ?><?php } ?>;
}

.page-navigation{
background: <?php if($dev_buddydaily_pagination_background_colour == ""){ ?><?php echo "#d0d0d0"; } else { ?><?php echo $dev_buddydaily_pagination_background_colour; ?><?php } ?>;
border-top: 1px solid <?php if($dev_buddydaily_pagination_border_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_buddydaily_pagination_border_colour; ?><?php } ?>;
}

div.pagination {
	border-bottom: 1px solid <?php if($dev_buddydaily_pagination_border_colour == ""){ ?><?php echo "#EAEAEA"; } else { ?><?php echo $dev_buddydaily_pagination_border_colour; ?><?php } ?>;
}

div.pending a, a.disabled {
	color: <?php if($dev_buddydaily_pending_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_pending_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_pending_border_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_pending_border_colour; ?><?php } ?>;
}

div.pending a:hover, a.disabled:hover {
	color: <?php if($dev_buddydaily_pending_hover_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_buddydaily_pending_hover_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_pending_hover_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_pending_hover_border_colour; ?><?php } ?>;
}

div.post pre, div.post code p {
	color: <?php if($dev_buddydaily_pre_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_pre_colour; ?><?php } ?>;
	background: <?php if($dev_buddydaily_pre_background_colour == ""){ ?><?php echo "#f4f4f4"; } else { ?><?php echo $dev_buddydaily_pre_background_colour; ?><?php } ?>;
}

div.post table {
	border: 1px solid <?php if($dev_buddydaily_table_border_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_table_border_colour; ?><?php } ?>;
}

div.post table th {
	border-top: 1px solid <?php if($dev_buddydaily_table_border_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_table_border_colour; ?><?php } ?>;
}

div.post table td {
	border-top: 1px solid <?php if($dev_buddydaily_table_border_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_table_border_colour; ?><?php } ?>;
}

div.post div.author-box, div.comment-avatar-box {
	background: <?php if($dev_buddydaily_author_box_background_colour == ""){ ?><?php echo "#f0f0f0"; } else { ?><?php echo $dev_buddydaily_author_box_colour; ?><?php } ?>;
}

div.post div.author-box img, div.comment-avatar-box img {
	border: 4px solid <?php if($dev_buddydaily_author_box_border_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_buddydaily_author_box_border_colour; ?><?php } ?>;
}

div.post p.postmetadata, div.comment-meta, div.comment-options, .widget-error {
	background: <?php if($dev_buddydaily_widget_background_colour == ""){ ?><?php echo "#FEFEFE"; } else { ?><?php echo $dev_buddydaily_widget_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_widget_colour == ""){ ?><?php echo "#999999"; } else { ?><?php echo $dev_buddydaily_widget_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_widget_border_colour == ""){ ?><?php echo "#FAFAFA"; } else { ?><?php echo $dev_buddydaily_widget_border_colour; ?><?php } ?>;
}

div.post .wp-caption {
	background: <?php if($dev_buddydaily_caption_background_colour == ""){ ?><?php echo "#DDDDDD"; } else { ?><?php echo $dev_buddydaily_caption_background_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_caption_border_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $dev_buddydaily_caption_border_colour; ?><?php } ?>;
}

.spotlight-post{
	background: <?php if($dev_buddydaily_spotlight_background_colour == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $dev_buddydaily_spotlight_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_spotlight_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_spotlight_colour; ?><?php } ?>;
	border-top: 1px solid <?php if($dev_buddydaily_spotlight_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_spotlight_border_colour; ?><?php } ?>;
}

div#sidebar h3.widgettitle, #footer h3.widgettitle {
	background: <?php if($dev_buddydaily_widget_title_background_colour == ""){ ?><?php echo "#EAEAEA"; } else { ?><?php echo $dev_buddydaily_widget_title_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_widget_title_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_widget_title_colour; ?><?php } ?>;
}

div#sidebar div.item-options {
	background: <?php if($dev_buddydaily_sidebar_item_background_colour == ""){ ?><?php echo "#F8F8F8"; } else { ?><?php echo $dev_buddydaily_sidebar_item_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_sidebar_item_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_sidebar_item_colour; ?><?php } ?>;
}

#site-wrapper{
	background: <?php if($dev_buddydaily_wrapper_background_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_wrapper_background_colour; ?><?php } ?>;
	border-right: 1px solid <?php if($dev_buddydaily_wrapper_border_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $dev_buddydaily_wrapper_border_colour; ?><?php } ?>;
	border-left: 1px solid <?php if($dev_buddydaily_wrapper_border_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $dev_buddydaily_wrapper_border_colour; ?><?php } ?>;
	border-bottom: 1px solid <?php if($dev_buddydaily_wrapper_border_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $dev_buddydaily_wrapper_border_colour; ?><?php } ?>;
}

#slider {	
	background: <?php if($dev_buddydaily_slideshow_background_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_slideshow_background_colour; ?><?php } ?>;
}

#sliderContent {
	background: <?php if($dev_buddydaily_slideshow_content_background_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_slideshow_content_background_colour; ?><?php } ?>;
}

.sliderImage span {
	background: <?php if($dev_buddydaily_slideshow_image_background_colour == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_buddydaily_slideshow_image_background_colour; ?><?php } ?>;
}

.sliderImage span {
	color: <?php if($dev_buddydaily_slideshow_colour == ""){ ?><?php echo "#AAAAAA"; } else { ?><?php echo $dev_buddydaily_slideshow_colour; ?><?php } ?>;
}

  #first {
	background: <?php if($dev_buddydaily_tabs_first_background_colour == ""){ ?><?php echo "#F9F9F9"; } else { ?><?php echo $dev_buddydaily_tabs_first_background_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_tabs_first_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $dev_buddydaily_tabs_first_colour; ?><?php } ?>;
    }

    #second {
		background: <?php if($dev_buddydaily_tabs_second_background_colour == ""){ ?><?php echo "#F9F9F9"; } else { ?><?php echo $dev_buddydaily_tabs_second_background_colour; ?><?php } ?>;
		border: 1px solid <?php if($dev_buddydaily_tabs_second_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $dev_buddydaily_tabs_second_colour; ?><?php } ?>;
    }

    #third {
		background: <?php if($dev_buddydaily_tabs_third_background_colour == ""){ ?><?php echo "#F9F9F9"; } else { ?><?php echo $dev_buddydaily_tabs_third_background_colour; ?><?php } ?>;
		border: 1px solid <?php if($dev_buddydaily_tabs_third_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $dev_buddydaily_tabs_third_colour; ?><?php } ?>;
    }

form.standard-form#signup_form div div.error {
	color: <?php if($dev_buddydaily_error_colour == ""){ ?><?php echo "#a71a1a"; } else { ?><?php echo $dev_buddydaily_error_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_error_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_error_border_colour; ?><?php } ?>;
	background: <?php if($dev_buddydaily_error_background_colour == ""){ ?><?php echo "#e41717"; } else { ?><?php echo $dev_buddydaily_updated_error_colour; ?><?php } ?>;
}

form.standard-form input[type=text],
form.standard-form select, form.standard-form input[type=password],
.dir-search input[type=text] {
	border: 1px solid <?php if($dev_buddydaily_input_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_input_border_colour; ?><?php } ?>;
	background: <?php if($dev_buddydaily_input_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_buddydaily_input_background_colour; ?><?php } ?>;
}

form.standard-form div.checkbox label,
form.standard-form div.radio label {
	color: <?php if($dev_buddydaily_label_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_label_colour; ?><?php } ?>;
}

form.standard-form input:focus, form.standard-form textarea:focus, form.standard-form select:focus {
	border: 1px solid <?php if($dev_buddydaily_input_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_input_border_colour; ?><?php } ?>;
	background: <?php if($dev_buddydaily_input_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_buddydaily_input_background_colour; ?><?php } ?>;
}

form.standard-form textarea, textarea{
	border: 1px solid <?php if($dev_buddydaily_textarea_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_textarea_border_colour; ?><?php } ?>;
	background: <?php if($dev_buddydaily_textarea_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_buddydaily_textarea_background_colour; ?><?php } ?>;
}

table tr td.label {
	border-right: 1px solid <?php if($dev_buddydaily_table_border_colour == ""){ ?><?php echo "#EEEEEE"; } else { ?><?php echo $dev_buddydaily_table_border_colour; ?><?php } ?>;
}

table tr.alt {
	background: <?php if($dev_buddydaily_topic_alt_background_colour == ""){ ?><?php echo "#F4F4F4"; } else { ?><?php echo $dev_buddydaily_topic_alt_background_colour; ?><?php } ?>;
}

table.forum tr:first-child {
	background: <?php if($dev_buddydaily_alt_background_colour == ""){ ?><?php echo "#F4F4F4"; } else { ?><?php echo $dev_buddydaily_alt_background_colour; ?><?php } ?>;
}

table.forum tr.sticky td {
	background: <?php if($dev_buddydaily_sticky_background_colour == ""){ ?><?php echo "#F9F9F9"; } else { ?><?php echo $dev_buddydaily_sticky_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_sticky_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_sticky_colour; ?><?php } ?>;
}

table.forum tr.closed td.td-title {
background-image: url( <?php bloginfo('template_directory'); ?>/library/styles/daily-images/closed.png );
background-position: 15px 50%;
background-repeat: no-repeat;
}

table#message-threads tr.unread td {
	background: <?php if($dev_buddydaily_unread_background_colour == ""){ ?><?php echo "#F9F9F9"; } else { ?><?php echo $dev_buddydaily_unread_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_unread_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_unread_colour; ?><?php } ?>;
	border: 1px solid <?php if($dev_buddydaily_unread_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_unread_border_colour; ?><?php } ?>;
}

ul.tabNavigation {
		background: <?php if($dev_buddydaily_tabs_nav_background_colour == ""){ ?><?php echo "#F9F9F9"; } else { ?><?php echo $dev_buddydaily_tabs_nav_background_colour; ?><?php } ?>;
		border: 1px solid <?php if($dev_buddydaily_tabs_nav_border_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $dev_buddydaily_tabs_nav_border_colour; ?><?php } ?>;
}

ul.tabNavigation li a {
	background: <?php if($dev_buddydaily_tabs_nav_link_background_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $dev_buddydaily_tabs_nav_link_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_tabs_nav_link_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_tabs_nav_link_colour; ?><?php } ?>;
}

ul.tabNavigation li a.selected,
ul.tabNavigation li a:hover {
	background: <?php if($dev_buddydaily_tabs_selected_background_colour == ""){ ?><?php echo "#333333"; } else { ?><?php echo $dev_buddydaily_tabs_selected_background_colour; ?><?php } ?>;
	color: <?php if($dev_buddydaily_tabs_selected_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_tabs_selected_colour; ?><?php } ?>;
}

ul#topic-post-list li.alt {
	color: <?php if($dev_buddydaily_topic_alt_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $dev_buddydaily_topic_alt_colour; ?><?php } ?>;
	background: <?php if($dev_buddydaily_topic_alt_background_colour == ""){ ?><?php echo "#f4f4f4"; } else { ?><?php echo $dev_buddydaily_topic_alt_error_colour; ?><?php } ?>;
}

li span.unread-count, tr.unread span.unread-count {
	color: <?php if($dev_buddydaily_unread_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $dev_buddydaily_unread_colour; ?><?php } ?>;
	background: <?php if($dev_buddydaily_unread_background_colour == ""){ ?><?php echo "#dd0000"; } else { ?><?php echo $dev_buddydaily_unread_background_colour; ?><?php } ?>;
}

form#whats-new-form {
	border-bottom: 1px solid <?php if($dev_buddydaily_whats_border_colour == ""){ ?><?php echo "#f0f0f0"; } else { ?><?php echo $dev_buddydaily_whats_border_colour; ?><?php } ?>;
}

form#whats-new-form #whats-new-textarea {
	border: 1px solid <?php if($dev_buddydaily_textarea_border_colour == ""){ ?><?php echo "#CCCCCC"; } else { ?><?php echo $dev_buddydaily_textarea_border_colour; ?><?php } ?>;
	background: <?php if($dev_buddydaily_textarea_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_buddydaily_textarea_background_colour; ?><?php } ?>;
}
