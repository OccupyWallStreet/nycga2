a, a:link, .ubox .list a, .ubox .list li ul li a{
	color: <?php if($dev_businessservices_link_colour == ""){ ?><?php echo "#D88C0E"; } else { ?><?php echo $dev_businessservices_link_colour; ?><?php } ?>;
}

a:hover{
	color: <?php if($dev_businessservices_link_hover_colour == ""){ ?><?php echo "#15B1D5"; } else { ?><?php echo $dev_businessservices_link_hover_colour; ?><?php } ?>;
}

a:visited{
	color: <?php if($dev_businessservices_link_visited_colour == ""){ ?><?php echo "#D88C0E"; } else { ?><?php echo $dev_businessservices_link_visited_colour; ?><?php } ?>;
}

.activity-list .activity-header a:first-child, span.highlight {
	color: <?php if($dev_businessservices_button_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessservices_button_text_colour; ?><?php } ?>;
	background: <?php if($dev_businessservices_button_background_colour == ""){ ?><?php echo "#1B9BCF"; } else { ?><?php echo $dev_businessservices_button_background_colour; ?><?php } ?>;
}

.activity-list .activity-header a:first-child:hover {
color: <?php if($dev_businessservices_button_hover_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessservices_button_hover_text_colour; ?><?php } ?> !important;
background: <?php if($dev_businessservices_button_background_hover_colour == ""){ ?><?php echo "#D88C0E"; } else { ?><?php echo $dev_businessservices_button_background_hover_colour; ?><?php } ?> !important;
}

.activity-list div.activity-meta a {
	color: <?php if($dev_businessservices_button_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessservices_button_text_colour; ?><?php } ?>;
	background: <?php if($dev_businessservices_button_background_colour == ""){ ?><?php echo "#1B9BCF"; } else { ?><?php echo $dev_businessservices_button_background_colour; ?><?php } ?>;
}

.activity-list div.activity-meta a.acomment-reply {
	color: <?php if($dev_businessservices_button_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessservices_button_text_colour; ?><?php } ?>;
	background: <?php if($dev_businessservices_button_background_colour == ""){ ?><?php echo "#1B9BCF"; } else { ?><?php echo $dev_businessservices_button_background_colour; ?><?php } ?>;
}

div.activity-meta a:hover {
	color: <?php if($dev_businessservices_button_hover_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessservices_button_hover_text_colour; ?><?php } ?>;
	background: <?php if($dev_businessservices_button_background_hover_colour == ""){ ?><?php echo "#D88C0E"; } else { ?><?php echo $dev_businessservices_button_background_hover_colour; ?><?php } ?>;
}

div.activity-meta a.acomment-reply:hover {
color: <?php if($dev_businessservices_button_hover_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessservices_button_hover_text_colour; ?><?php } ?>;
background: <?php if($dev_businessservices_button_background_hover_colour == ""){ ?><?php echo "#D88C0E"; } else { ?><?php echo $dev_businessservices_button_background_hover_colour; ?><?php } ?>;
}

h1{
	font-family: <?php if($dev_businessservices_header_font == ""){ ?><?php echo "Arial, Tahoma, Lucida Sans"; } else { ?><?php echo $dev_businessservices_header_font; ?><?php } ?>;	
		color: <?php if($dev_businessservices_font_colour == ""){ ?><?php echo "#151515"; } else { ?><?php echo $dev_businessservices_font_colour; ?><?php } ?>;	
}

h2, h2.pagetitle{
	font-family: <?php if($dev_businessservices_header_font == ""){ ?><?php echo "Arial, Tahoma, Lucida Sans"; } else { ?><?php echo $dev_businessservices_header_font; ?><?php } ?>;
		color: <?php if($dev_businessservices_font_colour == ""){ ?><?php echo "#151515"; } else { ?><?php echo $dev_businessservices_font_colour; ?><?php } ?>;	
}

h3{
	font-family: <?php if($dev_businessservices_header_font == ""){ ?><?php echo "Arial, Tahoma, Lucida Sans"; } else { ?><?php echo $dev_businessservices_header_font; ?><?php } ?>;	
		color: <?php if($dev_businessservices_font_colour == ""){ ?><?php echo "#151515"; } else { ?><?php echo $dev_businessservices_font_colour; ?><?php } ?>;	
}

h4{
	font-family: <?php if($dev_businessservices_header_font == ""){ ?><?php echo "Arial, Tahoma, Lucida Sans"; } else { ?><?php echo $dev_businessservices_header_font; ?><?php } ?>;	
		color: <?php if($dev_businessservices_font_colour == ""){ ?><?php echo "#151515"; } else { ?><?php echo $dev_businessservices_font_colour; ?><?php } ?>;
}

h5{
	font-family: <?php if($dev_businessservices_header_font == ""){ ?><?php echo "Arial, Tahoma, Lucida Sans"; } else { ?><?php echo $dev_businessservices_header_font; ?><?php } ?>;
		color: <?php if($dev_businessservices_font_colour == ""){ ?><?php echo "#151515"; } else { ?><?php echo $dev_businessservices_font_colour; ?><?php } ?>;	
}

h6{
	font-family: <?php if($dev_businessservices_header_font == ""){ ?><?php echo "Arial, Tahoma, Lucida Sans"; } else { ?><?php echo $dev_businessservices_header_font; ?><?php } ?>;	
		color: <?php if($dev_businessservices_font_colour == ""){ ?><?php echo "#151515"; } else { ?><?php echo $dev_businessservices_font_colour; ?><?php } ?>;
}

body{
	color: <?php if($dev_businessservices_font_colour == ""){ ?><?php echo "#151515"; } else { ?><?php echo $dev_businessservices_font_colour; ?><?php } ?>;
	font-family: <?php if($dev_businessservices_body_font == ""){ ?><?php echo "Arial, Verdana, sans-serif"; } else { ?><?php echo $dev_businessservices_body_font; ?><?php } ?>;	
}