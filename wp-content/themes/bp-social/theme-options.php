
body {
font-family: <?php echo $tn_buddysocial_body_font; ?> !important;
}

h1,h2,h3,h4,h5,h6 {
font-family: <?php echo $tn_buddysocial_headline_font; ?> !important;
}

#custom-img-header {
height: <?php echo $tn_buddysocial_image_height; ?>px !important;
}




<?php if($tn_buddysocial_span_meta_color == "") { ?>
<?php } else { ?>
#custom .activity-list .activity-header a:first-child, #custom span.highlight {
	color: <?php echo $tn_buddysocial_span_meta_text_color; ?>!important;
	border: 1px solid <?php echo $tn_buddysocial_span_meta_border_color; ?>!important;
	background: <?php echo $tn_buddysocial_span_meta_color; ?>!important;
}
span.activity {
		color: <?php echo $tn_buddysocial_span_meta_text_color; ?>!important;
	border: 1px solid <?php echo $tn_buddysocial_span_meta_border_color; ?>!important;
	background: <?php echo $tn_buddysocial_span_meta_color; ?>!important;
}
<?php } ?>


<?php if($tn_buddysocial_span_meta_hover_color == "") { ?>
<?php } else { ?>
#custom .activity-list .activity-header a:first-child:hover, #custom span.highlight:hover {
	color: <?php echo $tn_buddysocial_span_meta_text_hover_color; ?>!important;
	border: 1px solid <?php echo $tn_buddysocial_span_meta_border_hover_color; ?>!important;
	background: <?php echo $tn_buddysocial_span_meta_hover_color; ?>!important;
}
<?php } ?>




<?php if($tn_buddysocial_font_size != "") {
$tn_buddysocial_font_headline_size = $tn_buddysocial_font_size + 2;
$tn_buddysocial_font_headline_line_height = $tn_buddysocial_font_line_height + 5;
?>

#custom .activity-header p, .content p, .content .padder div, .content .padder li  {
font-size: <?php echo $tn_buddysocial_font_size; ?>px !important;
line-height: <?php echo $tn_buddysocial_font_line_height; ?>px !important;
}

#custom div.widget, #custom div.widget blockquote, #custom div.bp-widget, #custom .post-content, #custom .item-list li {
font-size: <?php echo $tn_buddysocial_font_size; ?>px !important;
line-height: <?php echo $tn_buddysocial_font_line_height; ?>px !important;
}

#custom div.widget h4.item-title {
font-size: <?php echo $tn_buddysocial_font_headline_size; ?>px !important;
line-height: <?php echo $tn_buddysocial_font_headline_line_height; ?>px !important;
}

#custom div.widget h2, #custom div.bp-widget h4 {
font-size: <?php echo $tn_buddysocial_font_headline_size; ?>px !important;
}
<?php } ?>


<?php if($tn_buddysocial_blog_header_bg_color != "") { ?>
#top-bar {
border-bottom: 1px solid <?php echo $tn_buddysocial_blog_header_bg_sec_color; ?>;
}
ul#options-nav li {
    border-right: 1px solid <?php echo $tn_buddysocial_blog_header_bg_sec_color; ?>;
    }
#top-bar, #user-status p, .avatar-box, #wire-post-new-input {
background: <?php echo $tn_buddysocial_blog_header_bg_color; ?>; /* old browsers */
<?php if($tn_buddysocial_blog_header_bg_sec_color != '') { ?>
background: -moz-linear-gradient(top, <?php echo $tn_buddysocial_blog_header_bg_color; ?> 0%, <?php echo $tn_buddysocial_blog_header_bg_sec_color; ?> 99%); /* firefox */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $tn_buddysocial_blog_header_bg_color; ?>), color-stop(99%,<?php echo $tn_buddysocial_blog_header_bg_sec_color; ?>)); /* webkit */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $tn_buddysocial_blog_header_bg_color; ?>', endColorstr='<?php echo $tn_buddysocial_blog_header_bg_sec_color; ?>',GradientType=0 ); /* ie */
<?php } ?>
}
<?php } ?>

<?php if($tn_buddysocial_blog_header_text_color != "") { ?>
#top-bar p, #top-bar li, #custom #user-status p a, .avatar-box {
color: <?php echo $tn_buddysocial_blog_header_text_color; ?>!important;
}
<?php } ?>

<?php if($tn_buddysocial_blog_header_text_link_color != "") { ?>
#top-bar a, #user-status p,  #custom .avatar-box h3, #custom .avatar-box a {
color: <?php echo $tn_buddysocial_blog_header_text_link_color; ?>!important;
}
<?php } ?>

<?php if($tn_buddysocial_blog_header_text_link_hover_color != "") { ?>
#top-bar a:hover, #user-status p a:hover, #custom .avatar-box a:hover {
color: <?php echo $tn_buddysocial_blog_header_text_link_hover_color; ?>!important;
}
<?php } ?>




<?php if($tn_buddysocial_blog_intro_header_color != "") { ?>
#top-header {
background: <?php echo $tn_buddysocial_blog_intro_header_color; ?>; /* old browsers */
<?php if($tn_buddysocial_blog_intro_header_sec_color != "") { ?>
background: -moz-linear-gradient(top, <?php echo $tn_buddysocial_blog_intro_header_color; ?> 0%, <?php echo $tn_buddysocial_blog_intro_header_sec_color; ?> 99%); /* firefox */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $tn_buddysocial_blog_intro_header_color; ?>), color-stop(99%,<?php echo $tn_buddysocial_blog_intro_header_sec_color; ?>)); /* webkit */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $tn_buddysocial_blog_intro_header_color; ?>', endColorstr='<?php echo $tn_buddysocial_blog_intro_header_sec_color; ?>',GradientType=0 ); /* ie */
<?php } ?>
}
<?php } ?>

<?php if($tn_buddysocial_blog_intro_text_color != "") { ?>
#custom div#top-header .home-intro p, #custom div#top-header .home-intro strong, #custom div#top-header .home-intro h1, #custom div#top-header .home-intro h3 {
color: <?php echo $tn_buddysocial_blog_intro_text_color; ?> !important;
}
<?php } ?>

<?php if($tn_buddysocial_blog_intro_header_link_color != "") { ?>
#custom #top-header a {
color: <?php echo $tn_buddysocial_blog_intro_header_link_color; ?> !important;
}
<?php } ?>

<?php if($tn_buddysocial_blog_intro_header_link_hover_color != "") { ?>
#custom #top-header a:hover {
color: <?php echo $tn_buddysocial_blog_intro_header_link_hover_color; ?> !important;
}
<?php } ?>


<?php if($tn_buddysocial_blog_subnav_color != "") { ?>

#custom ul#nav li a, #custom #nav li a:hover {
	background: <?php echo $tn_buddysocial_blog_subnav_color; ?>;

    color: <?php echo $tn_buddysocial_blog_subnav_link_color; ?> !important;
	}




#custom #nav li a, #custom #nav li.current a, #custom #nav li.selected a, #custom ul#nav li.current_page_item ul li a,#custom ul#nav li.current-menu-item ul li a {
	background: <?php echo $tn_buddysocial_blog_subnav_color; ?> !important;
	border-bottom: 0px none !important;
    color: <?php echo $tn_buddysocial_blog_subnav_link_color; ?> !important;
	}

#custom #nav li a:hover, #custom #nav li.current a:hover, #custom #nav li.selected a:hover, #custom ul#nav li.current_page_item ul li a:hover,#custom ul#nav li.current-menu-item ul li a:hover {
	background: <?php echo $tn_buddysocial_blog_subnav_hover_color; ?> !important;
    border-bottom: 0px none !important;
    color: <?php echo $tn_buddysocial_blog_subnav_link_hover_color; ?> !important;
    text-decoration: none;
	}

#custom ul#nav li.current a, #custom ul#nav li.selected a, #custom ul#nav li.current_page_item a,#custom ul#nav li.current-menu-item a {
color: <?php echo $tn_buddysocial_blog_subnav_link_hover_color; ?> !important;  
background: <?php echo $tn_buddysocial_blog_subnav_hover_color; ?> !important;
}





<?php } ?>


<?php if($tn_buddysocial_blog_global_links_color != "") { ?>
.footer a, .content a, #custom .post-content a, .post-tag a, #custom p a, #custom .widget a, #custom small a, #custom h4 a, .content-inner a, #custom li.load-more a, #custom h1.post-title a, #custom .post-tag a {
color: <?php echo $tn_buddysocial_blog_global_links_color; ?>;
}
<?php } ?>

<?php if($tn_buddysocial_blog_global_links_hover_color != "") { ?>
.footer a:hover, .content a:hover, #custom .post-content a:hover, .post-tag a:hover, #custom p a:hover, #custom .widget a:hover, #custom small a:hover, #custom h4 a:hover, .content-inner a:hover, #custom li.load-more a:hover, #custom h1.post-title a:hover, #custom .post-tag a:hover  {
color: <?php echo $tn_buddysocial_blog_global_links_hover_color; ?>;
}
<?php } ?>



<?php if($tn_buddysocial_featured_bg_color != "") { ?>
#myGallery, #myGallerySet, #flickrGallery, .jdGallery .slideElement, .jdGallery .loadingElement {
background-color: <?php echo $tn_buddysocial_featured_bg_color; ?> !important;
}
<?php } ?>

<?php if($tn_buddysocial_featured_slider_bg_color != "") { ?>
.jdGallery .slideInfoZone {
background: <?php echo $tn_buddysocial_featured_slider_bg_color; ?> !important;
color: #fff;
}
<?php } ?>



<?php if($tn_buddysocial_featured_text_color != "") { ?>

#myGallery, #myGallery p, #myGallerySet, #flickrGallery, .jdGallery .slideElement, .jdGallery .loadingElement {
color: <?php echo $tn_buddysocial_featured_text_color; ?> !important;
}

#custom #myGallery a, #custom #myGallerySet a, #custom #flickrGallery a, #custom .jdGallery .slideElement a, #custom .jdGallery .loadingElement a {
color: <?php echo $tn_buddysocial_featured_text_color; ?> !important;
}

#myGallery a:hover, #myGallerySet a:hover, #flickrGallery a:hover, .jdGallery .slideElement a:hover, .jdGallery .loadingElement a:hover {
color: <?php echo $tn_buddysocial_featured_text_color; ?> !important;
}

<?php } ?>


<?php if($tn_buddysocial_button_bg_color != "") { ?>
a.button{
background: <?php echo $tn_buddysocial_button_bg_color; ?> !important; /* firefox */
}
button, a.button, input[type=submit], input[type=button], input[type=reset], ul.button-nav li a, div.generic-button a, .comment-reply-link, input.button, input.submit, #custom ul#nav li#current_user a, button:hover, a.button:hover, a.button:focus, input[type=submit]:hover, input[type=button]:hover, input[type=reset]:hover, ul.button-nav li a:hover, ul.button-nav li.current a, div.generic-button a:hover, .comment-reply-link:hover {
background: -moz-linear-gradient(top, #ffffff 0%, <?php echo $tn_buddysocial_button_bg_color; ?> 99%) !important; /* firefox */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(99%,<?php echo $tn_buddysocial_button_bg_color; ?>)) !important; /* webkit */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='<?php echo $tn_buddysocial_button_bg_color; ?>',GradientType=0 ) !important;


<?php if($tn_buddysocial_button_text_color != "") { ?>
color: <?php echo $tn_buddysocial_button_text_color; ?> !important;
<?php } ?>
<?php if($tn_buddysocial_button_border_color != "") { ?>
border: 1px solid <?php echo $tn_buddysocial_button_border_color; ?> !important;
<?php } ?>
}


#custom ul#nav li#current_user a {
background: <?php echo $tn_buddysocial_button_bg_color; ?> !important;
<?php if($tn_buddysocial_button_text_color != "") { ?>
color: <?php echo $tn_buddysocial_button_text_color; ?> !important;
<?php } ?>
<?php if($tn_buddysocial_button_border_color != "") { ?>
border-top: 1px solid <?php echo $tn_buddysocial_button_border_color; ?> !important;
border-left: 1px solid <?php echo $tn_buddysocial_button_border_color; ?> !important;
border-right: 1px solid <?php echo $tn_buddysocial_button_border_color; ?> !important;
<?php } ?>
}

<?php } ?>

