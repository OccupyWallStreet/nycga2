<?php

/****
 * Custom header image support. You can remove this entirely in a child theme by adding this line
 * to your functions.php: define( 'BP_DTHEME_DISABLE_CUSTOM_HEADER', true );
 */
function bp_dtheme_add_custom_header_support() {
	$get_current_scheme = get_option('dev_buddydaily_custom_style');
		if(($get_current_scheme == '') || ($get_current_scheme == 'default.css')) { 
			$headerpath = "%s/library/styles/images/default_header.jpg";
		}
		else{
			$headerpath = "%s/library/styles/";
			if ($get_current_scheme == "gossip.css"){
				$styledir = "gossip";
			}
			if ($get_current_scheme == "daily.css"){
				$styledir = "daily";
			}
			if ($get_current_scheme == "tabloid.css"){
				$styledir = "tabloid";
			}
			if ($get_current_scheme == "motoring.css"){
				$styledir = "motoring";
			}
			if ($get_current_scheme == "press.css"){
				$styledir = "press";
			}
			if ($get_current_scheme == "technology.css"){
				$styledir = "technology";
			}
			if ($get_current_scheme == "politics.css"){
				$styledir = "politics";
			}
			if ($get_current_scheme == "culture.css"){
				$styledir = "culture";
			}
			$headerpath .= $styledir;
			$headerpath .= "-images/default_header.jpg";
		}
	/* Set the defaults for the custom header image (http://ryan.boren.me/2007/01/07/custom-image-header-api/) */
	if (($get_current_scheme == "tabloid.css")||($get_current_scheme == "gossip.css")||($get_current_scheme == "culture.css")||($get_current_scheme == "politics.css")){
		define( 'HEADER_TEXTCOLOR', 'FFFFFF' );
	}
	else if ($get_current_scheme == "motoring.css"){
		define( 'HEADER_TEXTCOLOR', 'D5D5D5' );
	}
	else if ($get_current_scheme == "technology.css"){
		define( 'HEADER_TEXTCOLOR', '51ABC5' );
	}
	else {
		define( 'HEADER_TEXTCOLOR', '111111' );
	}
	
	define( 'HEADER_IMAGE', $headerpath ); // %s is theme dir uri
	define( 'HEADER_IMAGE_WIDTH', 970 );
	define( 'HEADER_IMAGE_HEIGHT', 80 );

	function bp_dtheme_header_style() { ?>
		<style type="text/css">
			#header { background-image: url(<?php header_image() ?>); }
			<?php if ( 'blank' == get_header_textcolor() ) { ?>
			#header h1 a {display: none; }
			#header h1 a:visited {display: none; }
			#header h1 a:hover {display: none; }
			<?php } else { ?>
			#header h1 a {color:#<?php header_textcolor() ?>;}
			#header h1 a:visited {color:#<?php header_textcolor() ?>;}
			#header h1 a:hover {color:#<?php header_textcolor() ?>;}
			<?php } ?>
		</style>
	<?php
	}

	function bp_dtheme_admin_header_style() { ?>
		<style type="text/css">
			#headimg {
				position: relative;
				color: #fff;
				background: url(<?php header_image() ?>);
				-moz-border-radius-bottomleft: 6px;
				-webkit-border-bottom-left-radius: 6px;
				-moz-border-radius-bottomright: 6px;
				-webkit-border-bottom-right-radius: 6px;
				margin-bottom: 20px;
				height: 100px;
				padding-top: 25px;
			}

			#headimg h1{
				position: absolute;
				bottom: 15px;
				left: 15px;
				width: 44%;
				margin: 0;
				font-family: Arial, Tahoma, sans-serif;
			}
			#headimg h1 a{
				color:#<?php header_textcolor() ?>;
				text-decoration: none;
				border-bottom: none;
			}
			#headimg #desc{
				color:#<?php header_textcolor() ?>;
				font-size:1em;
				margin-top:-0.5em;
			}

			#desc {
				display: none;
			}

			<?php if ( 'blank' == get_header_textcolor() ) { ?>
			#headimg h1, #headimg #desc {
				display: none;
			}
			#headimg h1 a, #headimg #desc {
				color:#<?php echo HEADER_TEXTCOLOR ?>;
			}
			<?php } ?>
		</style>
	<?php
	}
	add_custom_image_header( 'bp_dtheme_header_style', 'bp_dtheme_admin_header_style' );
}
if ( !defined( 'BP_DTHEME_DISABLE_CUSTOM_HEADER' ) )
	add_action( 'init', 'bp_dtheme_add_custom_header_support' );


?>