body {
color: <?php if($dev_studio_font_colour == ""){ ?><?php echo "#101010"; } else { ?><?php echo $dev_studio_font_colour; ?><?php } ?>;
background: #ececec;
}

#panel a , #panel a.button:hover {
text-decoration: none;
color: <?php if($dev_studio_link_colour == ""){ ?><?php echo "#0097b9"; } else { ?><?php echo $dev_studio_link_colour; ?><?php } ?>;
}

#panel a {
text-decoration: none;
color: <?php if($dev_studio_link_colour == ""){ ?><?php echo "#0097b9"; } else { ?><?php echo $dev_studio_link_colour; ?><?php } ?>;
}

.tab ul.login li a {
color: <?php if($dev_studio_link_colour == ""){ ?><?php echo "#0097b9"; } else { ?><?php echo $dev_studio_link_colour; ?><?php } ?>;
}

a:link {
color: <?php if($dev_studio_link_colour == ""){ ?><?php echo "#0097b9"; } else { ?><?php echo $dev_studio_link_colour; ?><?php } ?>;
}

a:visited {
color: <?php if($dev_studio_link_visited_colour == ""){ ?><?php echo "#222222"; } else { ?><?php echo $dev_studio_link_visited_colour; ?><?php } ?>;
}

a:active,
a:hover {
color: <?php if($dev_studio_link_hover_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_studio_link_hover_colour; ?><?php } ?>;
}

.widget h3, div#sidebar div.item-options, div#sidebar h3.widgettitle {
color: <?php if($dev_studio_header_colour == ""){ ?><?php echo "#7b7b7b"; } else { ?><?php echo $dev_studio_header_colour; ?><?php } ?>;
text-shadow: <?php if($dev_studio_header_shadow_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_studio_header_shadow_colour; ?><?php } ?> 1px 1px 0px;
}

#feature-wrapper {
background: <?php if($dev_studio_feature_colour == ""){ ?><?php echo "#0097b9"; } else { ?><?php echo $dev_studio_feature_colour; ?><?php } ?>;
}

#feature h3, #feature h4 {
text-shadow: <?php if($dev_studio_feature_text_shadow_colour == ""){ ?><?php echo "#707070"; } else { ?><?php echo $dev_studio_feature_text_shadow_colour; ?><?php } ?> 1px 1px 0px;
color: <?php if($dev_studio_feature_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_studio_feature_text_colour; ?><?php } ?>;
}

#feature {
color: <?php if($dev_studio_feature_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_studio_feature_text_colour; ?><?php } ?>;
}

#strapline h1, #strapline h2, #site-wrapper-home h1, #site-wrapper-home h2, #site-wrapper-home h3, #site-wrapper-home h4, #site-wrapper-home h5, #site-wrapper-home h6 {
color: <?php if($dev_studio_header_colour == ""){ ?><?php echo "#7b7b7b"; } else { ?><?php echo $dev_studio_header_colour; ?><?php } ?>;
text-shadow: <?php if($dev_studio_header_shadow_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_studio_header_shadow_colour; ?><?php } ?> 1px 1px 0px;
}

#site-wrapper h1, #site-wrapper h2, #site-wrapper h3, #site-wrapper h4, #site-wrapper h5, #site-wrapper h6 {
color: <?php if($dev_studio_header_colour == ""){ ?><?php echo "#7b7b7b"; } else { ?><?php echo $dev_studio_header_colour; ?><?php } ?>;
text-shadow: <?php if($dev_studio_header_shadow_colour == ""){ ?><?php echo "#eeeeee"; } else { ?><?php echo $dev_studio_header_shadow_colour; ?><?php } ?> 1px 1px 0px;
}

#panel h4, #panel h3 {
text-shadow: <?php if($dev_studio_feature_text_shadow_colour == ""){ ?><?php echo "#000000"; } else { ?><?php echo $dev_studio_feature_text_shadow_colour; ?><?php } ?> 1px 1px 0px;
}

#header-wrapper {
background: <?php if($dev_studio_header_background_colour == ""){ ?><?php echo "#272727"; } else { ?><?php echo $dev_studio_header_background_colour; ?><?php } ?>;
}

#header h1 a, #site-logo a, #site-logo a:visited, #site-logo a:hover{
color: <?php if($dev_studio_site_header_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_studio_site_header_colour; ?><?php } ?>;
}

.nav a, .nav a:visited, .nav a:link {
color: <?php if($dev_studio_nav_text_colour == ""){ ?><?php echo "#cbedf5"; } else { ?><?php echo $dev_studio_nav_text_colour; ?><?php } ?>;
background: <?php if($dev_studio_nav_background_colour == ""){ ?><?php echo "#444444"; } else { ?><?php echo $dev_studio_nav_background_colour; ?><?php } ?>;
text-shadow: <?php if($dev_studio_nav_shadow_colour == ""){ ?><?php echo "#000000"; } else { ?><?php echo $dev_studio_nav_shadow_colour; ?><?php } ?> 1px 1px solid;
border: solid 1px <?php if($dev_studio_nav_border_colour == ""){ ?><?php echo "#555555"; } else { ?><?php echo $dev_studio_nav_border_colour; ?><?php } ?>;
}
/* main level link hover */
.nav .current a, .nav li:hover > a, .nav li.current_page_item a {
color: <?php if($dev_studio_nav_hover_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_studio_nav_hover_text_colour; ?><?php } ?>;
background: <?php if($dev_studio_nav_hover_background_colour == ""){ ?><?php echo "#000000"; } else { ?><?php echo $dev_studio_nav_hover_background_colour; ?><?php } ?>;
text-shadow: <?php if($dev_studio_nav_shadow_colour == ""){ ?><?php echo "#000000"; } else { ?><?php echo $dev_studio_nav_shadow_colour; ?><?php } ?> 1px 1px solid;
border: solid 1px <?php if($dev_studio_nav_border_colour == ""){ ?><?php echo "#555555"; } else { ?><?php echo $dev_studio_nav_border_colour; ?><?php } ?>;
}

/* sub levels link hover */
.nav ul li:hover a, .nav li:hover li a {
color: <?php if($dev_studio_nav_hover_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_studio_nav_hover_text_colour; ?><?php } ?>;
}

.nav ul a:hover {
	color: <?php if($dev_studio_nav_hover_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_studio_nav_hover_text_colour; ?><?php } ?>;
	background: <?php if($dev_studio_nav_hover_background_colour == ""){ ?><?php echo "#000000"; } else { ?><?php echo $dev_studio_nav_hover_background_colour; ?><?php } ?>;
	text-shadow: <?php if($dev_studio_nav_shadow_colour == ""){ ?><?php echo "#000000"; } else { ?><?php echo $dev_studio_nav_shadow_colour; ?><?php } ?> 1px 1px solid;
	border: solid 1px <?php if($dev_studio_nav_border_colour == ""){ ?><?php echo "#555555"; } else { ?><?php echo $dev_studio_nav_border_colour; ?><?php } ?>;
}

ul.nav li.selected a, ul.nav li.current_page_item a {
	color: <?php if($dev_studio_nav_hover_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_studio_nav_hover_text_colour; ?><?php } ?>;
	background: <?php if($dev_studio_nav_hover_background_colour == ""){ ?><?php echo "#000000"; } else { ?><?php echo $dev_studio_nav_hover_background_colour; ?><?php } ?>;
}