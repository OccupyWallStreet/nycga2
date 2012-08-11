a, a:link{
	color: <?php if($ne_buddyscholar_link_colour == ""){ ?><?php echo "#668877"; } else { ?><?php echo $ne_buddyscholar_link_colour; ?><?php } ?>;
}

a:hover{
	color: <?php if($ne_buddyscholar_link_hover_colour == ""){ ?><?php echo "#557766"; } else { ?><?php echo $ne_buddyscholar_link_hover_colour; ?><?php } ?>;
}

a:visited{
	color: <?php if($ne_buddyscholar_link_visited_colour == ""){ ?><?php echo "#557766"; } else { ?><?php echo $ne_buddyscholar_link_visited_colour; ?><?php } ?>;
}

.activity-comments ul{
	border-top: 1px solid <?php if($ne_buddyscholar_box_border_colour == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $ne_buddyscholar_box_border_colour; ?><?php } ?>;
}

.activity-comments li{
	border: 1px solid <?php if($ne_buddyscholar_box_border_colour == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $ne_buddyscholar_box_border_colour; ?><?php } ?>;
}

div#item-header h3 span.highlight span, .activity-list div.activity-meta a, div.generic-button a, div.comment-options a, .activity-list div.activity-meta a.acomment-reply{
	border: 1px solid <?php if($ne_buddyscholar_button_border == ""){ ?><?php echo "DDDDDD"; } else { ?><?php echo $ne_buddyscholar_button_border; ?><?php } ?>;
	background-color: <?php if($ne_buddyscholar_button_colour == ""){ ?><?php echo "#F8F8F8"; } else { ?><?php echo $ne_buddyscholar_button_colour; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_button_text == ""){ ?><?php echo "#999999"; } else { ?><?php echo $ne_buddyscholar_button_text; ?><?php } ?>;
}

div.activity-meta a:hover,  div.comment-options a:hover, div.generic-button a:hover, div.activity-meta a.acomment-reply:hover {
	background-color: <?php if($ne_buddyscholar_button_hover_background == ""){ ?><?php echo "#557766"; } else { ?><?php echo $ne_buddyscholar_button_hover_background; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_button_hover_text == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_button_hover_text; ?><?php } ?>;
}

.alt{
	background: <?php if($ne_buddyscholar_alt_background_colour == ""){ ?><?php echo "#F8F8F8"; } else { ?><?php echo $ne_buddyscholar_alt_background_colour; ?><?php } ?>;
	border: 1px solid <?php if($ne_buddyscholar_alt_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_alt_border_colour; ?><?php } ?>;
}

.entry blockquote{
	color: <?php if($ne_buddyscholar_blockquote_colour == ""){ ?><?php echo "#668877"; } else { ?><?php echo $ne_buddyscholar_blockquote_colour; ?><?php } ?>;
	border-left: 2px solid <?php if($ne_buddyscholar_blockquote_border_colour == ""){ ?><?php echo "#668877"; } else { ?><?php echo $ne_buddyscholar_blockquote_border_colour; ?><?php } ?>;
}

body{
	color: <?php if($ne_buddyscholar_text_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $ne_buddyscholar_text_colour; ?><?php } ?>;
	background-color: <?php if($ne_buddyscholar_footer_background_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $ne_buddyscholar_footer_background_colour; ?><?php } ?>;
	font-family: <?php if($ne_buddyscholar_body_font == ""){ ?><?php echo "Tahoma, Helvetica, Sans-serif"; } else { ?><?php echo $ne_buddyscholar_body_font; ?><?php } ?>;
}

.content-box-inner{
	background-color: <?php if($ne_buddyscholar_box_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_box_background_colour; ?><?php } ?>;
	border: 1px solid <?php if($ne_buddyscholar_box_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_box_border_colour; ?><?php } ?>;
}

.content-box-outer, #item-header{
	background-color: <?php if($ne_buddyscholar_box_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_box_background_colour; ?><?php } ?>;
}

.content-box-outer-activity{
	background-color: <?php if($ne_buddyscholar_box_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_box_background_colour; ?><?php } ?>;
}

#container-wrapper{
	background: <?php if($ne_buddyscholar_content_colour == ""){ ?><?php echo "#f5f5f5"; } else { ?><?php echo $ne_buddyscholar_content_colour; ?><?php } ?>;
	border-top: 2px solid <?php if($ne_buddyscholar_content_border_colour == ""){ ?><?php echo "#e8e8e8"; } else { ?><?php echo $ne_buddyscholar_content_border_colour; ?><?php } ?>;
	border-bottom: 2px solid <?php if($ne_buddyscholar_content_border_colour == ""){ ?><?php echo "#e8e8e8"; } else { ?><?php echo $ne_buddyscholar_content_border_colour; ?><?php } ?>;
}

.description{
	color: 	<?php if($ne_buddyscholar_login_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_login_text_colour; ?><?php } ?>;
}

p.description{
	color: <?php if($ne_buddyscholar_text_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $ne_buddyscholar_text_colour; ?><?php } ?>;
	text-shadow: none;
	font-size: 12px;
}

.entry-image{
	border: 1px solid <?php if($ne_buddyscholar_box_border_colour == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $ne_buddyscholar_box_border_colour; ?><?php }?>;
}

.entry ul{
	color: <?php if($ne_buddyscholar_list_colour == ""){ ?><?php echo "#668877"; } else { ?><?php echo $ne_buddyscholar_list_colour; ?><?php } ?>;
}

.entry ol{
	color: <?php if($ne_buddyscholar_list_colour == ""){ ?><?php echo "#668877"; } else { ?><?php echo $ne_buddyscholar_list_colour; ?><?php } ?>;
}

.entry h1{
	color: <?php if($ne_buddyscholar_h1_colour == ""){ ?><?php echo "#668877"; } else { ?><?php echo $ne_buddyscholar_h1_colour; ?><?php } ?>;
	font-family: <?php if($ne_buddyscholar_headline_font == ""){ ?><?php echo "Tahoma, Helvetica, Sans-serif"; } else { ?><?php echo $ne_buddyscholar_headline_font; ?><?php } ?>;
}

.entry h2{
	color: <?php if($ne_buddyscholar_h2_colour == ""){ ?><?php echo "#668877"; } else { ?><?php echo $ne_buddyscholar_h2_colour; ?><?php } ?>;
	font-family: <?php if($ne_buddyscholar_headline_font == ""){ ?><?php echo "Tahoma, Helvetica, Sans-serif"; } else { ?><?php echo $ne_buddyscholar_headline_font; ?><?php } ?>;
}

.entry h3{
	color: <?php if($ne_buddyscholar_h3_colour == ""){ ?><?php echo "#668877"; } else { ?><?php echo $ne_buddyscholar_h3_colour; ?><?php } ?>;
	font-family: <?php if($ne_buddyscholar_headline_font == ""){ ?><?php echo "Tahoma, Helvetica, Sans-serif"; } else { ?><?php echo $ne_buddyscholar_headline_font; ?><?php } ?>;
}

.entry h4{
	color: <?php if($ne_buddyscholar_h4_colour == ""){ ?><?php echo "#668877"; } else { ?><?php echo $ne_buddyscholar_h4_colour; ?><?php } ?>;
	font-family: <?php if($ne_buddyscholar_headline_font == ""){ ?><?php echo "Tahoma, Helvetica, Sans-serif"; } else { ?><?php echo $ne_buddyscholar_headline_font; ?><?php } ?>;
}

.entry h5{
	color: <?php if($ne_buddyscholar_h4_colour == ""){ ?><?php echo "#668877"; } else { ?><?php echo $ne_buddyscholar_h4_colour; ?><?php } ?>;
	font-family: <?php if($ne_buddyscholar_headline_font == ""){ ?><?php echo "Tahoma, Helvetica, Sans-serif"; } else { ?><?php echo $ne_buddyscholar_headline_font; ?><?php } ?>;
}

.entry h6{
	color: <?php if($ne_buddyscholar_h4_colour == ""){ ?><?php echo "#668877"; } else { ?><?php echo $ne_buddyscholar_h4_colour; ?><?php } ?>;
	font-family: <?php if($ne_buddyscholar_headline_font == ""){ ?><?php echo "Tahoma, Helvetica, Sans-serif"; } else { ?><?php echo $ne_buddyscholar_headline_font; ?><?php } ?>;
}

#footer-links{
	border-top: 2px solid <?php if($ne_buddyscholar_footer_border_colour == ""){ ?><?php echo "#f5f5f5"; } else { ?><?php echo $ne_buddyscholar_footer_border_colour; ?><?php } ?>;
}

#footer-wrapper{
	background: <?php if($ne_buddyscholar_footer_background_colour == ""){ ?><?php echo "#DEDEDE"; } else { ?><?php echo $ne_buddyscholar_footer_background_colour; ?><?php } ?>;
	border-top: 2px solid <?php if($ne_buddyscholar_footer_border_colour == ""){ ?><?php echo "#f5f5f5"; } else { ?><?php echo $ne_buddyscholar_footer_border_colour; ?><?php } ?>;
}

.forum{
	border: 1px solid <?php if($ne_buddyscholar_box_border_colour == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $ne_buddyscholar_box_border_colour; ?><?php } ?>;
}

#front-sidebar{
	background-color: <?php if($ne_buddyscholar_box_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_box_background_colour; ?><?php } ?>;
}

h1, h1 a, h1 a:link, h1 a:visited, h1:hover{
	color: <?php if($ne_buddyscholar_h1_colour == ""){ ?><?php echo "#557766"; } else { ?><?php echo $ne_buddyscholar_h1_colour; ?><?php } ?>;
	font-family: <?php if($ne_buddyscholar_headline_font == ""){ ?><?php echo "Tahoma, Helvetica, Sans-serif"; } else { ?><?php echo $ne_buddyscholar_headline_font; ?><?php } ?>;
}

#header h1, #header h1 a, #header h1 a:link, #header h1 a:visited, #header h1:hover{
	color: <?php if($ne_buddyscholar_h1_header_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_h1_header_colour; ?><?php } ?>;
	font-family: <?php if($ne_buddyscholar_headline_font == ""){ ?><?php echo "Tahoma, Helvetica, Sans-serif"; } else { ?><?php echo $ne_buddyscholar_headline_font; ?><?php } ?>;

}

h2{
	color: <?php if($ne_buddyscholar_h2_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_h2_colour; ?><?php } ?>;
	font-family: <?php if($ne_buddyscholar_headline_font == ""){ ?><?php echo "Tahoma, Helvetica, Sans-serif"; } else { ?><?php echo $ne_buddyscholar_headline_font; ?><?php } ?>;

}

.h3-background h3{
	color: <?php if($ne_buddyscholar_h3_title_colour == ""){ ?><?php echo "#557766"; } else { ?><?php echo $ne_buddyscholar_h3_title_colour; ?><?php } ?>;	
	font-family: <?php if($ne_buddyscholar_headline_font == ""){ ?><?php echo "Tahoma, Helvetica, Sans-serif"; } else { ?><?php echo $ne_buddyscholar_headline_font; ?><?php } ?>;
}

h3{
	color: <?php if($ne_buddyscholar_h3_colour == ""){ ?><?php echo "#556677"; } else { ?><?php echo $ne_buddyscholar_h3_colour; ?><?php } ?>;
	font-family: <?php if($ne_buddyscholar_headline_font == ""){ ?><?php echo "Tahoma, Helvetica, Sans-serif"; } else { ?><?php echo $ne_buddyscholar_headline_font; ?><?php } ?>;
}

h4{
	color: <?php if($ne_buddyscholar_h4_colour == ""){ ?><?php echo "#556677"; } else { ?><?php echo $ne_buddyscholar_h4_colour; ?><?php } ?>;
	font-family: <?php if($ne_buddyscholar_headline_font == ""){ ?><?php echo "Tahoma, Helvetica, Sans-serif"; } else { ?><?php echo $ne_buddyscholar_headline_font; ?><?php } ?>;
}

.h4-background h4{
	color: <?php if($ne_buddyscholar_h4_title_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_h4_title_colour; ?><?php } ?>;
	font-family: <?php if($ne_buddyscholar_headline_font == ""){ ?><?php echo "Tahoma, Helvetica, Sans-serif"; } else { ?><?php echo $ne_buddyscholar_headline_font; ?><?php } ?>;
}

.h3-background{
	background: <?php if($ne_buddyscholar_h3_title_background_colour == ""){ ?><?php echo "#e8e8e8"; } else { ?><?php echo $ne_buddyscholar_h3_title_background_colour; ?><?php } ?>;
	border-bottom: 1px solid <?php if($ne_buddyscholar_h3_title_border_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_h3_title_border_colour; ?><?php } ?>;
}

.h4-background{
	<?php if(($ne_buddyscholar_h4_title_background_image == "")&&($ne_buddyscholar_h4_title_background_colour != "")) { ?>
	background: <?php echo $ne_buddyscholar_h4_title_background_colour; ?>;
	<?php } ?>

	<?php if(($ne_buddyscholar_h4_title_background_image != "")&&($ne_buddyscholar_h4_title_background_colour != "")) { ?>
	background: <?php echo $ne_buddyscholar_h4_title_background_colour; ?> url(<?php echo $ne_buddyscholar_h4_title_background_image; ?>) <?php echo $ne_buddyscholar_h4_title_image_repeat; ?>; 
	<?php } ?>
	
	<?php if(($ne_buddyscholar_h4_title_background_image != "")&&($ne_buddyscholar_h4_title_background_colour == "")) { ?>
	background: #a2a2ac url(<?php echo $ne_buddyscholar_h4_title_background_image; ?>) <?php echo $ne_buddyscholar_h4_title_image_repeat; ?>;
	<?php } ?>
	
	<?php if(($ne_buddyscholar_h4_title_background_image == "")&&($ne_buddyscholar_h4_title_background_colour == "")) { ?>
	background: #a2a2ac url('<?php bloginfo('template_directory'); ?>/_inc/images/sidebar_heading_background.png') repeat-x; 
	<?php } ?>
}

#header-wrapper{
	<?php if(($ne_buddyscholar_header_background_image == "")&&($ne_buddyscholar_header_background_colour != "")) { ?>
	background: <?php echo $ne_buddyscholar_header_background_colour; ?>;
	<?php } ?>

	<?php if(($ne_buddyscholar_header_background_image != "")&&($ne_buddyscholar_header_background_colour != "")) { ?>
	background: <?php echo $ne_buddyscholar_header_background_colour; ?> url(<?php echo $ne_buddyscholar_header_background_image; ?>) <?php echo $ne_buddyscholar_header_image_repeat; ?>; 
	<?php } ?>
	
	<?php if(($ne_buddyscholar_header_background_image != "")&&($ne_buddyscholar_header_background_colour == "")) { ?>
	background: #68837C url(<?php echo $ne_buddyscholar_header_background_image; ?>) <?php echo $ne_buddyscholar_header_image_repeat; ?>;
	<?php } ?>
	
	<?php if(($ne_buddyscholar_header_background_image == "")&&($ne_buddyscholar_header_background_colour == "")) { ?>
	background: #68837C url('<?php bloginfo('template_directory'); ?>/_inc/styles/sage-images/green_header_background.png') repeat-x; 
	<?php } ?>
	
	border-bottom: 2px solid <?php if($ne_buddyscholar_header_border_colour == ""){ ?><?php echo "#557766"; } else { ?><?php echo $ne_buddyscholar_header_border_colour; ?><?php } ?>;
	border-top: 2px solid <?php if($ne_buddyscholar_header_border_colour == ""){ ?><?php echo "#557766"; } else { ?><?php echo $ne_buddyscholar_header_border_colour; ?><?php } ?>;
}

hr {
	background: <?php if($ne_buddyscholar_box_border_colour == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $ne_buddyscholar_box_border_colour; ?><?php }?>;
}

img{
	border: 1px solid <?php if($ne_buddyscholar_box_border_colour == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $ne_buddyscholar_box_border_colour; ?><?php }?>;
}

.info, .error{
	background-color: <?php if($ne_buddyscholar_information_background_colour == ""){ ?><?php echo "F8F8F8"; } else { ?><?php echo $ne_buddyscholar_information_background_colour; ?><?php } ?>;
	border: 1px solid <?php if($ne_buddyscholar_information_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_information_border_colour; ?><?php } ?>;
}

#info-wrapper{
	background: <?php if($ne_buddyscholar_login_background_colour == ""){ ?><?php echo "#557766"; } else { ?><?php echo $ne_buddyscholar_login_background_colour; ?><?php } ?>;
	border-bottom: 2px solid <?php if($ne_buddyscholar_login_border_colour == ""){ ?><?php echo "#668877"; } else { ?><?php echo $ne_buddyscholar_login_border_colour; ?><?php } ?>;
	color:  <?php if($ne_buddyscholar_login_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_login_text_colour; ?><?php } ?>;
}

a.comment-reply-link, input[type="button"], .button{
	border: 1px solid <?php if($ne_buddyscholar_button_colour == ""){ ?><?php echo "DDDDDD"; } else { ?><?php echo $ne_buddyscholar_button_colour; ?><?php } ?>;
	background-color: <?php if($ne_buddyscholar_button_colour == ""){ ?><?php echo "#F8F8F8"; } else { ?><?php echo $ne_buddyscholar_button_colour; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_button_text == ""){ ?><?php echo "#999999"; } else { ?><?php echo $ne_buddyscholar_button_text; ?><?php } ?>;
}

textarea{
	border: 1px solid <?php if($ne_buddyscholar_form_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_form_border_colour; ?><?php } ?>;
	background-color: <?php if($ne_buddyscholar_form_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_form_background_colour; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_form_text == ""){ ?><?php echo "#999999"; } else { ?><?php echo $ne_buddyscholar_form_text; ?><?php } ?>;
}

input[type="submit"]{
	border: 1px solid <?php if($ne_buddyscholar_submit_border_colour == ""){ ?><?php echo "#779988"; } else { ?><?php echo $ne_buddyscholar_submit_border_colour; ?><?php } ?>;
	background-color: <?php if($ne_buddyscholar_submit_background_colour == ""){ ?><?php echo "#779988"; } else { ?><?php echo $ne_buddyscholar_submit_background_colour; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_submit_text_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_submit_text_colour; ?><?php } ?>;
}

input[type="text"]{
	border: 1px solid <?php if($ne_buddyscholar_form_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_form_border_colour; ?><?php } ?>;
	background-color: <?php if($ne_buddyscholar_form_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_form_background_colour; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_form_text == ""){ ?><?php echo "#999999"; } else { ?><?php echo $ne_buddyscholar_form_text; ?><?php } ?>;
}

input[type="search"]{
	border: 1px solid <?php if($ne_buddyscholar_form_border_colour == ""){ ?><?php echo "#779988"; } else { ?><?php echo $ne_buddyscholar_form_border_colour; ?><?php } ?>;
	background-color: <?php if($ne_buddyscholar_form_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_form_background_colour; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_form_text == ""){ ?><?php echo "#999999"; } else { ?><?php echo $ne_buddyscholar_form_text; ?><?php } ?>;
}

input[type="password"]{
	border: 1px solid <?php if($ne_buddyscholar_form_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_form_border_colour; ?><?php } ?>;
	background-color: <?php if($ne_buddyscholar_form_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_form_background_colour; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_form_text == ""){ ?><?php echo "#999999"; } else { ?><?php echo $ne_buddyscholar_form_text; ?><?php } ?>;
}

ul.item-list li {
	border-bottom: 1px solid <?php if($ne_buddyscholar_box_border_colour == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $ne_buddyscholar_box_border_colour; ?><?php } ?>;
}

div.item-list-tabs {
	background-color: <?php if($ne_buddyscholar_box_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_box_background_colour; ?><?php } ?>;
}

.item-list-tabs a, .item-list-tabs a:link, .item-list-tabs a:visited{
	border: 1px solid <?php if($ne_buddyscholar_button_border == ""){ ?><?php echo "DDDDDD"; } else { ?><?php echo $ne_buddyscholar_button_border; ?><?php } ?>;
	background-color: <?php if($ne_buddyscholar_button_colour == ""){ ?><?php echo "#F8F8F8"; } else { ?><?php echo $ne_buddyscholar_button_colour; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_button_text == ""){ ?><?php echo "#999999"; } else { ?><?php echo $ne_buddyscholar_button_text; ?><?php } ?>;
}

.item-list-tabs a:hover{
	background-color: <?php if($ne_buddyscholar_button_hover_background == ""){ ?><?php echo "#557766"; } else { ?><?php echo $ne_buddyscholar_button_hover_background; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_button_hover_text == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_button_hover_text; ?><?php } ?>;
}

.item-list-tabs li.selected a{
	background-color: <?php if($ne_buddyscholar_button_hover_background == ""){ ?><?php echo "#557766"; } else { ?><?php echo $ne_buddyscholar_button_hover_background; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_button_hover_text == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_button_hover_text; ?><?php } ?>;
}

label{
	color: <?php if($ne_buddyscholar_label_text_colour == ""){ ?><?php echo "#779988"; } else { ?><?php echo $ne_buddyscholar_label_text_colour; ?><?php } ?>;
}

#login-wrapper{
	background: <?php if($ne_buddyscholar_login_background_colour == ""){ ?><?php echo "#557766"; } else { ?><?php echo $ne_buddyscholar_login_background_colour; ?><?php } ?>;
	border-bottom: 2px solid <?php if($ne_buddyscholar_login_border_colour == ""){ ?><?php echo "#668877"; } else { ?><?php echo $ne_buddyscholar_login_border_colour; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_login_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_login_text_colour; ?><?php } ?>;
}

#login-wrapper label{
	color: <?php if($ne_buddyscholar_login_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_login_text_colour; ?><?php } ?>;
}

#login-wrapper a, #login-wrapper a:link, #login-wrapper a:hover{
	color: <?php if($ne_buddyscholar_login_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_login_text_colour; ?><?php } ?>;
}

#login-wrapper a:visited{
	color: <?php if($ne_buddyscholar_login_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_login_text_colour; ?><?php } ?>;
}

#login-wrapper .button{
	border: 1px solid <?php if($ne_buddyscholar_submit_border_colour == ""){ ?><?php echo "#779988"; } else { ?><?php echo $ne_buddyscholar_submit_border_colour; ?><?php } ?>;
	background-color: <?php if($ne_buddyscholar_submit_background_colour == ""){ ?><?php echo "#779988"; } else { ?><?php echo $ne_buddyscholar_submit_background_colour; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_submit_text_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_submit_text_colour; ?><?php } ?>;
}

.meta-author{
	background: url('<?php bloginfo('template_directory'); ?>/_inc/images/male.png') no-repeat;
	background-position: 0px 2px;
}

.meta-category{
	background: url('<?php bloginfo('template_directory'); ?>/_inc/images/blogs.png') no-repeat;
	background-position: 0px 2px;
}

.meta-comments{
	background: url('<?php bloginfo('template_directory'); ?>/_inc/images/listen.png') no-repeat;
	background-position: 0px 2px;
}

.meta-date{
	background: url('<?php bloginfo('template_directory'); ?>/_inc/images/options.png') no-repeat;
	background-position: 0px 2px;
}

.meta-tag{
	background: url('<?php bloginfo('template_directory'); ?>/_inc/images/tag.png') no-repeat;
	background-position: 0px 2px;
}

#navigation-wrapper{
	<?php if(($ne_buddyscholar_navigation_background_image == "")&&($ne_buddyscholar_navigation_background_colour != "")) { ?>
	background: <?php echo $ne_buddyscholar_navigation_background_colour; ?>;
	<?php } ?>

	<?php if(($ne_buddyscholar_navigation_background_image != "")&&($ne_buddyscholar_navigation_background_colour != "")) { ?>
	background: <?php echo $ne_buddyscholar_navigation_background_colour; ?> url(<?php echo $ne_buddyscholar_navigation_background_image; ?>) <?php echo $ne_buddyscholar_navigation_image_repeat; ?>; 
	<?php } ?>
	
	<?php if(($ne_buddyscholar_navigation_background_image != "")&&($ne_buddyscholar_navigation_background_colour == "")) { ?>
	background: #68837C url(<?php echo $ne_buddyscholar_navigation_background_image; ?>) <?php echo $ne_buddyscholar_navigation_image_repeat; ?>;
	<?php } ?>
	
	<?php if(($ne_buddyscholar_navigation_background_image == "")&&($ne_buddyscholar_navigation_background_colour == "")) { ?>
	background: #68837C url('<?php bloginfo('template_directory'); ?>/_inc/styles/sage-images/navigation_background.png') repeat-x; 
	<?php } ?>
	border-top: 1px solid <?php if($ne_buddyscholar_navigation_border_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_navigation_border_colour; ?><?php } ?>;
	border-bottom: 1px solid <?php if($ne_buddyscholar_navigation_border_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_navigation_border_colour; ?><?php } ?>;
}

ol.commentlist li.comment div.vcard img.avatar { border:1px solid <?php if($ne_buddyscholar_comment_list_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_comment_list_border_colour; ?><?php } ?>; }


ol.commentlist ul.children li.depth-2 { border-left:5px solid <?php if($ne_buddyscholar_comment_list_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_comment_list_border_colour; ?><?php } ?>; }
ol.commentlist ul.children li.depth-3 { border-left:5px solid <?php if($ne_buddyscholar_comment_list_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_comment_list_border_colour; ?><?php } ?>; }
ol.commentlist ul.children li.depth-4 { border-left:5px solid <?php if($ne_buddyscholar_comment_list_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_comment_list_border_colour; ?><?php } ?>; }

ol.commentlist li.even { 
	border: 1px solid <?php if($ne_buddyscholar_comment_even_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_comment_even_border_colour; ?><?php } ?>;
	background-color: <?php if($ne_buddyscholar_comment_even_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $ne_buddyscholar_comment_even_colour; ?><?php } ?>;
}

ol.commentlist li.odd {
	border: 1px solid <?php if($ne_buddyscholar_comment_odd_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_comment_odd_border_colour; ?><?php } ?>;
	background-color: <?php if($ne_buddyscholar_comment_odd_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $ne_buddyscholar_comment_odd_colour; ?><?php } ?>;
}

ol.commentlist li.parent { border-left:5px solid <?php if($ne_buddyscholar_comment_list_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_comment_list_border_colour; ?><?php } ?>; }

.post-meta-data{
	border-bottom: 1px solid <?php if($ne_buddyscholar_box_border_colour == ""){ ?><?php echo "#f3f3f3"; } else { ?><?php echo $ne_buddyscholar_box_border_colour; ?><?php } ?>;3f3f3;	
}

select{
	border: 1px solid <?php if($ne_buddyscholar_form_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_form_border_colour; ?><?php } ?>;
	background-color: <?php if($ne_buddyscholar_form_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_form_background_colour; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_form_text == ""){ ?><?php echo "#999999"; } else { ?><?php echo $ne_buddyscholar_form_text; ?><?php } ?>;
}

.sf-menu a, .sf-menu a:visited  { 
	color: <?php if($ne_buddyscholar_navigation_link_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_navigation_link_colour; ?><?php } ?>;
	cursor: pointer;
}

.sf-menu li li { 
	background-color: <?php if($ne_buddyscholar_navigation_background_drop_colour == ""){ ?><?php echo "#779988"; } else { ?><?php echo $ne_buddyscholar_navigation_background_drop_colour; ?><?php } ?>;
	cursor: pointer;
}

.sf-menu li li li {	
	background-color: <?php if($ne_buddyscholar_navigation_background_drop_colour == ""){ ?><?php echo "#779988"; } else { ?><?php echo $ne_buddyscholar_navigation_background_drop_colour; ?><?php } ?>;
	cursor: pointer;
}

.sf-menu li:hover, .sf-menu li.current, .sf-menu li.current a:visited, .sf-menu li.current_page_item, .sf-menu li.current_page_item a:visited, .sf-menu li.sfHover,
.sf-menu a:focus, .sf-menu a:hover, .sf-menu a:active {
	color: <?php if($ne_buddyscholar_navigation_hover_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_navigation_hover_colour; ?><?php } ?>;
	background-color: <?php if($ne_buddyscholar_navigation_background_hover_colour == ""){ ?><?php echo "#557766"; } else { ?><?php echo $ne_buddyscholar_navigation_background_hover_colour; ?><?php } ?>;
	cursor: pointer;
}

.sf-menu .selected a{
	color: <?php if($ne_buddyscholar_navigation_hover_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_navigation_hover_colour; ?><?php } ?>;
	background-color: <?php if($ne_buddyscholar_navigation_background_hover_colour == ""){ ?><?php echo "#557766"; } else { ?><?php echo $ne_buddyscholar_navigation_background_hover_colour; ?><?php } ?>;
	cursor: pointer;
}

#sidebar, #sidebar-right{
	background-color: <?php if($ne_buddyscholar_box_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_box_background_colour; ?><?php } ?>;
}

#sidebar .widget-wrapper li{
	background: none;
}

#slideshow-image{
	background: <?php if($ne_buddyscholar_slideshow_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_slideshow_background_colour; ?><?php } ?>;
}

#slideshow-text{
	background: <?php if($ne_buddyscholar_slideshow_text_background_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $ne_buddyscholar_slideshow_text_background_colour; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_slideshow_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_slideshow_text_colour; ?><?php } ?>;
}

.sub-navigation-box{
	background-color: <?php if($ne_buddyscholar_box_background_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $ne_buddyscholar_box_background_colour; ?><?php } ?>;
	border: 1px solid <?php if($ne_buddyscholar_box_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_box_border_colour; ?><?php } ?>;
}

#th-title{
	background: <?php if($ne_buddyscholar_table_colour == ""){ ?><?php echo "#e8e8e8"; } else { ?><?php echo $ne_buddyscholar_table_colour; ?><?php } ?>;
	border-bottom: 1px solid <?php if($ne_buddyscholar_table_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_table_border_colour; ?><?php } ?>;
	border-right: 1px solid <?php if($ne_buddyscholar_table_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_table_border_colour; ?><?php } ?>;
}

#th-poster{
	background: <?php if($ne_buddyscholar_table_colour == ""){ ?><?php echo "#e8e8e8"; } else { ?><?php echo $ne_buddyscholar_table_colour; ?><?php } ?>;
	border-bottom: 1px solid <?php if($ne_buddyscholar_table_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_table_border_colour; ?><?php } ?>;
	border-right: 1px solid <?php if($ne_buddyscholar_table_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_table_border_colour; ?><?php } ?>;
}

#th-group{
	background: <?php if($ne_buddyscholar_table_colour == ""){ ?><?php echo "#e8e8e8"; } else { ?><?php echo $ne_buddyscholar_table_colour; ?><?php } ?>;
	border-bottom: 1px solid <?php if($ne_buddyscholar_table_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_table_border_colour; ?><?php } ?>;
	border-right: 1px solid <?php if($ne_buddyscholar_table_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_table_border_colour; ?><?php } ?>;
}

#th-postcount{
	background: <?php if($ne_buddyscholar_table_colour == ""){ ?><?php echo "#e8e8e8"; } else { ?><?php echo $ne_buddyscholar_table_colour; ?><?php } ?>;
	border-bottom: 1px solid <?php if($ne_buddyscholar_table_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_table_border_colour; ?><?php } ?>;
	border-right: 1px solid <?php if($ne_buddyscholar_table_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_table_border_colour; ?><?php } ?>;
}

#th-freshness{
	background: <?php if($ne_buddyscholar_table_colour == ""){ ?><?php echo "#e8e8e8"; } else { ?><?php echo $ne_buddyscholar_table_colour; ?><?php } ?>;
	border-bottom: 1px solid <?php if($ne_buddyscholar_table_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_table_border_colour; ?><?php } ?>;
}

table.forum tr:first-child {
	background: <?php if($ne_buddyscholar_table_colour == ""){ ?><?php echo "#e8e8e8"; } else { ?><?php echo $ne_buddyscholar_table_colour; ?><?php } ?>;
}

table.forum tr.sticky td {
	background: <?php if($ne_buddyscholar_table_sticky_colour == ""){ ?><?php echo "#E9F8F1"; } else { ?><?php echo $ne_buddyscholar_table_sticky_colour; ?><?php } ?>;
	border-top: 1px solid <?php if($ne_buddyscholar_table_sticky_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_table_sticky_border_colour; ?><?php } ?>;
	border-bottom: 1px solid <?php if($ne_buddyscholar_table_sticky_border_colour == ""){ ?><?php echo "#FFFFFF"; } else { ?><?php echo $ne_buddyscholar_table_sticky_border_colour; ?><?php } ?>;
}

table.forum tr.closed td.td-title {
	background-image: url( <?php bloginfo('template_directory'); ?>/_inc/images/closed_topic.png );
	background-position: 15px 50%;
	background-repeat: no-repeat;
}

table#message-threads tr.unread td {
	background: <?php if($ne_buddyscholar_table_unread_colour == ""){ ?><?php echo "#F8F8F8"; } else { ?><?php echo $ne_buddyscholar_table_unread_colour; ?><?php } ?>;
	border-top: 1px solid <?php if($ne_buddyscholar_table_unread_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_table_unread_border_colour; ?><?php } ?>;
	border-bottom: 1px solid <?php if($ne_buddyscholar_table_unread_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_table_unread_border_colour; ?><?php } ?>;
	font-weight: bold;
}

li span.unread-count, tr.unread span.unread-count {
	background: <?php if($ne_buddyscholar_unread_colour == ""){ ?><?php echo "#E8E8E8"; } else { ?><?php echo $ne_buddyscholar_unread_colour; ?><?php } ?>;
	color: <?php if($ne_buddyscholar_unread_text_colour == ""){ ?><?php echo "#111111"; } else { ?><?php echo $ne_buddyscholar_table_unread_text_colour; ?><?php } ?>;
	font-weight: bold;
}

#message-threads tr{
	border: 1px solid <?php if($ne_buddyscholar_messages_border_colour == ""){ ?><?php echo "#F3F3F3"; } else { ?><?php echo $ne_buddyscholar_messages_border_colour; ?><?php } ?>;
}

#url-facebook{
	background: url(<?php bloginfo('template_directory'); ?>/_inc/images/facebook.png) no-repeat 0px 9px;
}

#url-flickr {
	background: url(<?php bloginfo('template_directory'); ?>/_inc/images/flickr.png) no-repeat 0px 9px;
}

#url-twitter {
	background: url(<?php bloginfo('template_directory'); ?>/_inc/images/twitter.png) no-repeat 0px 9px;
}

#url-youtube{
	background: url(<?php bloginfo('template_directory'); ?>/_inc/images/youtube.png) no-repeat 0px 9px;
}

.widget-error{
	background: <?php if($ne_buddyscholar_widget_background_colour == ""){ ?><?php echo "#f8f8f8"; } else { ?><?php echo $ne_buddyscholar_widget_background_colour; ?><?php } ?>;
	border: 1px solid <?php if($ne_buddyscholar_widget_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_widget_border_colour; ?><?php } ?>;
}

.widget-wrapper{
	background: <?php if($ne_buddyscholar_widget_background_colour == ""){ ?><?php echo "#f8f8f8"; } else { ?><?php echo $ne_buddyscholar_widget_background_colour; ?><?php } ?>;
	border: 1px solid <?php if($ne_buddyscholar_widget_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_widget_border_colour; ?><?php } ?>;
}

.widget-wrapper li{
	color: <?php if($ne_buddyscholar_list_colour == ""){ ?><?php echo "#668877"; } else { ?><?php echo $ne_buddyscholar_list_colour; ?><?php } ?>;
}

.footer-block li, .footer-block-end li{
	color: <?php if($ne_buddyscholar_list_colour == ""){ ?><?php echo "#668877"; } else { ?><?php echo $ne_buddyscholar_list_colour; ?><?php } ?>;
	background: url('<?php bloginfo('template_directory'); ?>/_inc/images/arrowright.png') no-repeat;
	background-position: 0px 3px;
}

#whats-new-form, .ac-form{
	background-color: <?php if($ne_buddyscholar_information_background_colour == ""){ ?><?php echo "F8F8F8"; } else { ?><?php echo $ne_buddyscholar_information_background_colour; ?><?php } ?>;
	border: 1px solid <?php if($ne_buddyscholar_information_border_colour == ""){ ?><?php echo "#EBEBEB"; } else { ?><?php echo $ne_buddyscholar_information_border_colour; ?><?php } ?>;
}
