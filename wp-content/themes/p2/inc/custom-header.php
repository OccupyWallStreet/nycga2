<?php
/**
 * Setup and callbacks for WordPress custom header feature.
 *
 * @package P2
 * @since P2 1.4
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for previous versions.
 * Use feature detection of get_custom_header() which was introduced
 * in WordPress 3.4.
 *
 * @uses p2_header_style()
 * @uses p2_admin_header_style()
 *
 * @package P2
 * @since P2 1.4
 */
function p2_setup_custom_header() {
	$args = array(
		'width'               => 980,
		'height'              => 120,
		'default-image'       => '',
		'default-text-color'  => '3478e3',
		'wp-head-callback'    => 'p2_header_style',
		'admin-head-callback' => 'p2_admin_header_style',
	);

	$args = apply_filters( 'p2_custom_header_args', $args );

	if ( function_exists( 'get_custom_header' ) ) {
		add_theme_support( 'custom-header', $args );
	} else {
		// Compat: Versions of WordPress prior to 3.4.
		define( 'HEADER_TEXTCOLOR',    $args['default-text-color'] );
		define( 'HEADER_IMAGE',        $args['default-image'] );
		define( 'HEADER_IMAGE_WIDTH',  $args['width'] );
		define( 'HEADER_IMAGE_HEIGHT', $args['height'] );
		add_custom_image_header( $args['wp-head-callback'], $args['admin-head-callback'] );
	}
}

/**
 * Styles for the Custom Header admin UI.
 *
 * @package P2
 * @since P2 1.1
 */
function p2_admin_header_style() {
?>
	<style type="text/css">
	#headimg {
		background: url(<?php header_image(); ?>) repeat;
		padding: 0 0 0 10px;
		width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
		height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
	}
	#headimg a {
		width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
		height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
	}
	#headimg h1 {
		font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-weight: 200;
		margin: 0;
		padding-top: 20px;
	}
	#headimg h1 a {
		color: #<?php header_textcolor(); ?>;
		border-bottom: none;
		font-size: 40px;
		margin: -0.4em 0 0 0;
		text-decoration: none;
	}
	#headimg #desc {
		color: #<?php header_textcolor(); ?>;
		font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-size: 13px;
		font-weight: 400;
		margin-top: 1em;
	}

	<?php if ( 'blank' == get_header_textcolor() ) : ?>
	#headimg h1,
	#headimg #desc {
		display: none;
	}
	#headimg h1 a,
	#headimg #desc {
		color: #<?php echo HEADER_TEXTCOLOR ?>;
	}
	<?php endif; ?>

	</style>
<?php
}

/**
 * Styles to display custom header in template files.
 *
 * @package P2
 * @since P2 1.1
 */
function p2_header_style() {
?>
	<style type="text/css">

	<?php if ( '' != get_header_image() ) : ?>
		#header {
			background: url(<?php header_image(); ?>) repeat;
			height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
		}
		#header a.secondary {
			display: block;
			position: absolute;
			top: 0;
			width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
			height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
		}
		#header a.secondary:hover {
			border: 0;
		}
		#header .sleeve {
			background-color: transparent;
			margin-top: 0;
			margin-right: 0;
			position: relative;
			height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
			-webkit-box-shadow: none !important;
			-moz-box-shadow: none !important;
			box-shadow: none !important;
		}
		#header {
			-webkit-box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
			-moz-box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
			box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
		}
	<?php endif; ?>

	<?php if ( 'blank' == get_header_textcolor() ) : ?>
		#header h1,
		#header small {
			padding: 0;
			text-indent: -1000em;
		}
	<?php else: ?>
		#header h1 a,
		#header small {
			color: #<?php header_textcolor(); ?>;
		}
	<?php endif; ?>

	</style>
<?php
}