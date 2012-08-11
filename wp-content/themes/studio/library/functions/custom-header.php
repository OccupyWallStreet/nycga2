<?php

/****
 * Custom header image support. You can remove this entirely in a child theme by adding this line
 * to your functions.php: define( 'BP_DTHEME_DISABLE_CUSTOM_HEADER', true );
 */
function bp_dtheme_add_custom_header_support() {
	//$get_current_scheme = get_option('dev_wpplugins_custom_style');
	
	$headerpath = "%s/library/styles/images/default_header.jpg";
		
	/* Set the defaults for the custom header image (http://ryan.boren.me/2007/01/07/custom-image-header-api/) */

		define( 'HEADER_TEXTCOLOR', '111111' );
	
	define( 'HEADER_IMAGE', $headerpath ); // %s is theme dir uri
	define( 'HEADER_IMAGE_WIDTH', 980 );
	define( 'HEADER_IMAGE_HEIGHT', 100 );

	function bp_dtheme_header_style() { ?>
		<style type="text/css">
			#headercustom { height: 100px; background-image: url(<?php header_image() ?>); }
			<?php if ( 'blank' == get_header_textcolor() ) { ?>
			#headercustom h1 a {display: none; }
			#headercustom h1 a:visited {display: none; }
			#headercustom h1 a:hover {display: none; }
			<?php } else { ?>
			#headercustom h1 a {color:#<?php header_textcolor() ?>;}
			#headercustom h1 a:visited {color:#<?php header_textcolor() ?>;}
			#headercustom h1 a:hover {color:#<?php header_textcolor() ?>;}
			<?php } ?>
		</style>
	<?php
	}

	function bp_dtheme_admin_header_style() { ?>
		<style type="text/css">
			#headercustomimg {
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

			#headercustomimg h1{
				position: absolute;
				bottom: 15px;
				left: 15px;
				width: 44%;
				margin: 0;
				font-family: Arial, Tahoma, sans-serif;
			}
			#headercustomimg h1 a{
				color:#<?php header_textcolor() ?>;
				text-decoration: none;
				border-bottom: none;
			}
			#headercustomimg #desc{
				color:#<?php header_textcolor() ?>;
				font-size:1em;
				margin-top:-0.5em;
			}

			#desc {
				display: none;
			}

			<?php if ( 'blank' == get_header_textcolor() ) { ?>
			#headercustomimg h1, #headercustomimg #desc {
				display: none;
			}
			#headercustomimg h1 a, #headercustomimg #desc {
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