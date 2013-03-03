<?php 

/* Custom Header Code */
define( 'HEADER_TEXTCOLOR', 'EF4832' );
define( 'HEADER_IMAGE', '' ); // %s is theme dir uri
define( 'HEADER_IMAGE_WIDTH', 980);
define( 'HEADER_IMAGE_HEIGHT', 120);

function pulse_press_admin_header_style() {

	
?>
	<style type="text/css">
	
	#headimg {
		background: url(<?php header_image(); ?>) repeat;
		background-color: #FFF;
		height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
		width:<?php echo HEADER_IMAGE_WIDTH; ?>px;
		padding:0 0 0 18px;
	}
	#headimg a {
		height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
		width:<?php echo HEADER_IMAGE_WIDTH; ?>px;
	}

	#headimg h1{
		padding-top:40px;
		margin: 0;
		font-family: Calibri, "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-weight: bold;
	}
	#headimg h1 a {
		color:#<?php header_textcolor(); ?>;
		text-decoration: none;
		border-bottom: none;
		font-size: 1.4em;
		margin: -0.4em 0 0 0;
	}
	#headimg #desc{
		color:#888;
		font-size:1.1em;
		margin-top:1em;
		font-family: Calibri,"HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-weight: 200;
	}
	
	<?php if ( 'blank' == get_header_textcolor() ) : ?>
	#headimg h1, #headimg #desc {
		display: none;
		
		
	}
	<?php else: ?>
	#headimg h1 a, #headimg #desc {
		color:#<?php echo HEADER_TEXTCOLOR ?>;
	}
	
	<?php endif; ?>

	</style>
	<?php
}

function pulse_press_header_style() {
	
	if ( get_header_image() || 'blank' == get_header_textcolor()) : ?>
	
	<style type="text/css">
		<?php if( get_header_image() ): ?>
		#header {
			background: url(<?php header_image(); ?>) repeat;
			height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
		}
		#header a.secondary {
			height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
			width:<?php echo HEADER_IMAGE_WIDTH; ?>px;
			display: block;
			position: absolute;
			top: 0;
		}
		#header a.secondary:hover {
			border: 0;
		}
		#header .sleeve {
			position: relative;
			margin-top: 0;
			margin-right: 0;
			background-color: transparent;
			height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
		}
		#header {
			box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2) !important;
			-webkit-box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2) !important;
			-moz-box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2) !important;
		}
		<?php endif; ?>
		<?php if ( 'blank' == get_header_textcolor() ) : ?>
		#header h1, #header small {
			padding: 0;
			text-indent: -1000em;
		}
		<?php else : ?>
		#header h1 a, #header small {
			color: #<?php header_textcolor(); ?>;
		<?php endif; ?>
		
		#main .vote-up, #main .vote-down{
			background-color:#<?php echo HEADER_TEXTCOLOR ?>; 
		}
	</style>
<?php
	endif;
}
add_custom_image_header( 'pulse_press_header_style', 'pulse_press_admin_header_style' );