a, a:link, #fromtheblog h1 a{
	color: <?php if($dev_businessportfolio_link_colour == ""){ ?><?php echo "#7395AC"; } else { ?><?php echo $dev_businessportfolio_link_colour; ?><?php } ?>;
}

a:hover{
	color: <?php if($dev_businessportfolio_link_hover_colour == ""){ ?><?php echo "#15B1D5"; } else { ?><?php echo $dev_businessportfolio_link_hover_colour; ?><?php } ?>;
}

a:visited{
	color: <?php if($dev_businessportfolio_link_visited_colour == ""){ ?><?php echo "#7395AC"; } else { ?><?php echo $dev_businessportfolio_link_visited_colour; ?><?php } ?>;
}

.activity-list .activity-header a:first-child, span.highlight {
	color: <?php if($dev_businessportfolio_button_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessportfolio_button_text_colour; ?><?php } ?>;
	background: <?php if($dev_businessportfolio_button_background_colour == ""){ ?><?php echo "#8da67e"; } else { ?><?php echo $dev_businessportfolio_button_background_colour; ?><?php } ?>;
}

.activity-list .activity-header a:first-child:hover, a.button:hover {
color: <?php if($dev_businessportfolio_button_hover_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessportfolio_button_hover_text_colour; ?><?php } ?> !important;
background: <?php if($dev_businessportfolio_button_background_hover_colour == ""){ ?><?php echo "#ff6f16"; } else { ?><?php echo $dev_businessportfolio_button_background_hover_colour; ?><?php } ?> !important;
	border: none;
}

.activity-list div.activity-meta a, a.button:visited, a.button{
	color: <?php if($dev_businessportfolio_button_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessportfolio_button_text_colour; ?><?php } ?>;
	background: <?php if($dev_businessportfolio_button_background_colour == ""){ ?><?php echo "#8da67e"; } else { ?><?php echo $dev_businessportfolio_button_background_colour; ?><?php } ?>;
		border: none;
}

.activity-list div.activity-meta a.acomment-reply {
	color: <?php if($dev_businessportfolio_button_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessportfolio_button_text_colour; ?><?php } ?>;
	background: <?php if($dev_businessportfolio_button_background_colour == ""){ ?><?php echo "#8da67e"; } else { ?><?php echo $dev_businessportfolio_button_background_colour; ?><?php } ?>;
}

div.activity-meta a:hover {
	color: <?php if($dev_businessportfolio_button_hover_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessportfolio_button_hover_text_colour; ?><?php } ?>;
	background: <?php if($dev_businessportfolio_button_background_hover_colour == ""){ ?><?php echo "#ff6f16"; } else { ?><?php echo $dev_businessportfolio_button_background_hover_colour; ?><?php } ?>;
}

div.activity-meta a.acomment-reply:hover {
color: <?php if($dev_businessportfolio_button_hover_text_colour == ""){ ?><?php echo "#ffffff"; } else { ?><?php echo $dev_businessportfolio_button_hover_text_colour; ?><?php } ?>;
background: <?php if($dev_businessportfolio_button_background_hover_colour == ""){ ?><?php echo "#ff6f16"; } else { ?><?php echo $dev_businessportfolio_button_background_hover_colour; ?><?php } ?>;
}

h1{
	font-family: <?php if($dev_businessportfolio_header_font == ""){ ?><?php echo "Georgia, Times New Roman, Helvetica, sans-serif"; } else { ?><?php echo $dev_businessportfolio_header_font; ?><?php } ?>;	
		color: <?php if($dev_businessportfolio_font_colour == ""){ ?><?php echo "#292718"; } else { ?><?php echo $dev_businessportfolio_font_colour; ?><?php } ?>;
}

h2{
	font-family: <?php if($dev_businessportfolio_header_font == ""){ ?><?php echo "Georgia, Times New Roman, Helvetica, sans-serif"; } else { ?><?php echo $dev_businessportfolio_header_font; ?><?php } ?>;	
		color: <?php if($dev_businessportfolio_font_colour == ""){ ?><?php echo "#292718"; } else { ?><?php echo $dev_businessportfolio_font_colour; ?><?php } ?>;
}

h3{
	font-family: <?php if($dev_businessportfolio_header_font == ""){ ?><?php echo "Georgia, Times New Roman, Helvetica, sans-serif"; } else { ?><?php echo $dev_businessportfolio_header_font; ?><?php } ?>;	
		color: <?php if($dev_businessportfolio_font_colour == ""){ ?><?php echo "#292718"; } else { ?><?php echo $dev_businessportfolio_font_colour; ?><?php } ?>;
}

h4{
	font-family: <?php if($dev_businessportfolio_header_font == ""){ ?><?php echo "Georgia, Times New Roman, Helvetica, sans-serif"; } else { ?><?php echo $dev_businessportfolio_header_font; ?><?php } ?>;	
		color: <?php if($dev_businessportfolio_font_colour == ""){ ?><?php echo "#292718"; } else { ?><?php echo $dev_businessportfolio_font_colour; ?><?php } ?>;
}

h5{
	font-family: <?php if($dev_businessportfolio_header_font == ""){ ?><?php echo "Georgia, Times New Roman, Helvetica, sans-serif"; } else { ?><?php echo $dev_businessportfolio_header_font; ?><?php } ?>;	
		color: <?php if($dev_businessportfolio_font_colour == ""){ ?><?php echo "#292718"; } else { ?><?php echo $dev_businessportfolio_font_colour; ?><?php } ?>;
}

h6{
	font-family: <?php if($dev_businessportfolio_header_font == ""){ ?><?php echo "Georgia, Times New Roman, Helvetica, sans-serif"; } else { ?><?php echo $dev_businessportfolio_header_font; ?><?php } ?>;	
		color: <?php if($dev_businessportfolio_font_colour == ""){ ?><?php echo "#292718"; } else { ?><?php echo $dev_businessportfolio_font_colour; ?><?php } ?>;
}

body{
	color: <?php if($dev_businessportfolio_font_colour == ""){ ?><?php echo "#292718"; } else { ?><?php echo $dev_businessportfolio_font_colour; ?><?php } ?>;
	font-family: <?php if($dev_businessportfolio_body_font == ""){ ?><?php echo "Helvetica, Century Gothic, Arial"; } else { ?><?php echo $dev_businessportfolio_body_font; ?><?php } ?>;	
}

