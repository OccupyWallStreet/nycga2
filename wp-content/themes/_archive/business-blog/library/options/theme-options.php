a, a:link{
	color: <?php if($dev_businessblog_link_colour == ""){ ?><?php echo "#4498ba"; } else { ?><?php echo $dev_businessblog_link_colour; ?><?php } ?>;
}

a:hover{
	color: <?php if($dev_businessblog_link_hover_colour == ""){ ?><?php echo "#15B1D5"; } else { ?><?php echo $dev_businessblog_link_hover_colour; ?><?php } ?>;
}

a:visited{
	color: <?php if($dev_businessblog_link_visited_colour == ""){ ?><?php echo "#4498ba"; } else { ?><?php echo $dev_businessblog_link_visited_colour; ?><?php } ?>;
}

.activity-list .activity-header a:first-child, span.highlight {
	color: <?php if($dev_businessblog_button_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessblog_button_text_colour; ?><?php } ?>;
	background: <?php if($dev_businessblog_button_background_colour == ""){ ?><?php echo "#11A3C7"; } else { ?><?php echo $dev_businessblog_button_background_colour; ?><?php } ?>;
}

.activity-list .activity-header a:first-child:hover {
color: <?php if($dev_businessblog_button_hover_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessblog_button_hover_text_colour; ?><?php } ?> !important;
background: <?php if($dev_businessblog_button_background_hover_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_businessblog_button_background_hover_colour; ?><?php } ?> !important;
}

.activity-list div.activity-meta a {
	color: <?php if($dev_businessblog_button_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessblog_button_text_colour; ?><?php } ?>;
	background: <?php if($dev_businessblog_button_background_colour == ""){ ?><?php echo "#11A3C7"; } else { ?><?php echo $dev_businessblog_button_background_colour; ?><?php } ?>;
}

.activity-list div.activity-meta a.acomment-reply {
	color: <?php if($dev_businessblog_button_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessblog_button_text_colour; ?><?php } ?>;
	background: <?php if($dev_businessblog_button_background_colour == ""){ ?><?php echo "#11A3C7"; } else { ?><?php echo $dev_businessblog_button_background_colour; ?><?php } ?>;
}

div.activity-meta a:hover {
	color: <?php if($dev_businessblog_button_hover_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessblog_button_hover_text_colour; ?><?php } ?>;
	background: <?php if($dev_businessblog_button_background_hover_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_businessblog_button_background_hover_colour; ?><?php } ?>;
}

div.activity-meta a.acomment-reply:hover {
color: <?php if($dev_businessblog_button_hover_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessblog_button_hover_text_colour; ?><?php } ?>;
background: <?php if($dev_businessblog_button_background_hover_colour == ""){ ?><?php echo "#666666"; } else { ?><?php echo $dev_businessblog_button_background_hover_colour; ?><?php } ?>;
}

h1, #site-logo h1{
	font-family: <?php if($dev_businessblog_header_font == ""){ ?><?php echo "Georgia, Times New Roman, Helvetica, sans-serif"; } else { ?><?php echo $dev_businessblog_header_font; ?><?php } ?>;
		color: <?php if($dev_businessblog_font_colour == ""){ ?><?php echo "#3D3D3D"; } else { ?><?php echo $dev_businessblog_font_colour; ?><?php } ?>;	
}

h2{
	font-family: <?php if($dev_businessblog_header_font == ""){ ?><?php echo "Georgia, Times New Roman, Helvetica, sans-serif"; } else { ?><?php echo $dev_businessblog_header_font; ?><?php } ?>;	
		color: <?php if($dev_businessblog_font_colour == ""){ ?><?php echo "#3D3D3D"; } else { ?><?php echo $dev_businessblog_font_colour; ?><?php } ?>;
}

h3{
	font-family: <?php if($dev_businessblog_header_font == ""){ ?><?php echo "Georgia, Times New Roman, Helvetica, sans-serif"; } else { ?><?php echo $dev_businessblog_header_font; ?><?php } ?>;
		color: <?php if($dev_businessblog_font_colour == ""){ ?><?php echo "#3D3D3D"; } else { ?><?php echo $dev_businessblog_font_colour; ?><?php } ?>;	
}

h4{
	font-family: <?php if($dev_businessblog_header_font == ""){ ?><?php echo "Georgia, Times New Roman, Helvetica, sans-serif"; } else { ?><?php echo $dev_businessblog_header_font; ?><?php } ?>;
		color: <?php if($dev_businessblog_font_colour == ""){ ?><?php echo "#3D3D3D"; } else { ?><?php echo $dev_businessblog_font_colour; ?><?php } ?>;	
}

h5{
	font-family: <?php if($dev_businessblog_header_font == ""){ ?><?php echo "Georgia, Times New Roman, Helvetica, sans-serif"; } else { ?><?php echo $dev_businessblog_header_font; ?><?php } ?>;
		color: <?php if($dev_businessblog_font_colour == ""){ ?><?php echo "#3D3D3D"; } else { ?><?php echo $dev_businessblog_font_colour; ?><?php } ?>;	
}

h6{
	font-family: <?php if($dev_businessblog_header_font == ""){ ?><?php echo "Georgia, Times New Roman, Helvetica, sans-serif"; } else { ?><?php echo $dev_businessblog_header_font; ?><?php } ?>;	
		color: <?php if($dev_businessblog_font_colour == ""){ ?><?php echo "#3D3D3D"; } else { ?><?php echo $dev_businessblog_font_colour; ?><?php } ?>;
}

body{
	color: <?php if($dev_businessblog_font_colour == ""){ ?><?php echo "#3D3D3D"; } else { ?><?php echo $dev_businessblog_font_colour; ?><?php } ?>;
	font-family: <?php if($dev_businessblog_body_font == ""){ ?><?php echo "Georgia, Times New Roman, Helvetica, sans-serif"; } else { ?><?php echo $dev_businessblog_body_font; ?><?php } ?>;	
}
