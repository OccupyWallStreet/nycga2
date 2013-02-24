<?php if ($dev_network_navigation_bar_background != ""){?>
#top-navigation-bar{
	background: <?php echo $dev_network_navigation_bar_background; ?>;
}
<?php } ?>

<?php if ($dev_network_font_colour != ""){?>
body {
color: <?php echo $dev_network_font_colour; ?>;
}
<?php } ?>

<?php if ($dev_network_link_colour != ""){?>
#panel a, #panel a:link, #panel a:visited, #panel a.button:hover, .tab ul.login li a, a:link {
color: <?php echo $dev_network_link_colour; ?>;
}
<?php } ?>

<?php if ($dev_network_link_visited_colour != ""){?>
#panel a:visited, a:visited {
color: <?php echo $dev_network_link_visited_colour; ?>;
}
<?php } ?>

<?php if ($dev_network_link_hover_colour != ""){?>
#panel a.button:hover, a:hover, a:active {
color: <?php echo $dev_network_link_hover_colour; ?>;
}
<?php } ?>

<?php if ($dev_network_header_background_colour != ""){?>
#top-navigation-bar {
background-color: <?php echo $dev_network_header_background_colour; ?>;
}
<?php } ?>

<?php if ($dev_network_feature_text_colour != ""){?>
#articleBox h2, #articleBox h2 a {
color: <?php echo $dev_network_feature_text_colour; ?>;
}
<?php } ?>

<?php if ($dev_network_feature_blog_title_colour != ""){?>
#articleBox h3, #articleBox h3 a {
color: <?php echo $dev_network_feature_blog_title_colour; ?>;
}
<?php } ?>

<?php if ($dev_network_feature_box_colour != ""){?>
#articleBox .articles li {
background-color: <?php echo $dev_network_feature_box_colour; ?>;
}
<?php } ?>

<?php if ($dev_network_feature_box_hover_colour != ""){?>
#articleBox .articles li:hover {
background-color: <?php echo $dev_network_feature_box_hover_colour; ?>;
}
<?php } ?>

<?php if ($dev_network_header_colour != ""){?>
#strapline h1, #strapline h2, #site-wrapper-home h1, #site-wrapper-home h2, #site-wrapper-home h3, #site-wrapper-home h4, #site-wrapper-home h5, #site-wrapper-home h6, #site-wrapper h1, #site-wrapper h2, #site-wrapper h3, #site-wrapper h4, #site-wrapper h5, #site-wrapper h6 {
color: <?php echo $dev_network_header_colour; ?>;
}
<?php } ?>

<?php if ($dev_network_site_header_colour != ""){?>
#header h1 a {
color: <?php echo $dev_network_site_header_colour; ?>;
}
<?php } ?>

<?php if (($dev_network_nav_text_colour != "") || ($dev_network_nav_shadow_colour != "")){?>
#topbar ul li a {
color: <?php echo $dev_network_nav_text_colour; ?>;
text-shadow: -1px 2px 0 <?php echo $dev_network_nav_shadow_colour; ?>;
}
<?php } ?>

<?php if ($dev_network_nav_hover_text_colour != ""){?>
#topbar ul li a:hover {
color: <?php echo $dev_network_nav_hover_text_colour; ?>;
}
<?php } ?>

<?php if ($dev_network_nav_background_colour != ""){?>
.sf-menu li, .sf-menu li li, .sf-menu li li li{
background-color: <?php echo $dev_network_nav_background_colour; ?>;
	}
<?php } ?>

<?php if ($dev_network_nav_hover_background_colour != ""){?>
.sf-menu li:hover, #topbar ul li a:hover, .sf-menu li li:hover{
background-color: <?php echo $dev_network_nav_hover_background_colour; ?>;
}
<?php } ?>

<?php if ($dev_network_content_background_colour != ""){?>
.generic-box{
    background: <?php echo $dev_network_content_background_colour; ?>;
    border: none;
}
<?php } ?>