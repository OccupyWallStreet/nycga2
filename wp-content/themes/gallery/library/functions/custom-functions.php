<?php

////////////////////////////////////////////////////////////////////////////////
// new thumbnail code for wp 2.9+
////////////////////////////////////////////////////////////////////////////////
if ( function_exists( 'add_theme_support' ) ) { // Added in 2.9
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 210,160, true ); // Normal post thumbnails
	add_image_size( 'single-post-thumbnail', 400, 9999 ); // Permalink thumbnail size
}

function wt_get_ID_by_page_name($page_name)
{
	global $wpdb;
	$page_name_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'");
	return $page_name_id;
}

function wpmudev_autoexpand_rel_wlightbox($content) {

		global $post;	
		$pattern = "/(<a(?![^>]*?rel=['\"]lightbox.*)[^>]*?href=['\"][^'\"]+?\.(?:bmp|gif|jpg|jpeg|png)['\"][^\>]*)>/i";
		$replacement = '$1 id="gallery_popup" rel="exhibition_gallery">';
		$content = preg_replace($pattern, $replacement, $content);
		
	return $content;
}
add_filter('the_content', 'wpmudev_autoexpand_rel_wlightbox', 99);
add_filter('the_excerpt', 'wpmudev_autoexpand_rel_wlightbox', 99);

// This theme uses wp_nav_menu() in one location.
register_nav_menus( array(
	'main' => __( 'Main Navigation', 'gallery' ),
) );

register_nav_menus( array(
	'exhibitions' => __( 'Exhibitions', 'gallery' ),
) );

// This theme allows users to set a custom background
add_custom_background();

function wpmudev_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'wpmudev_page_menu_args' );

function font_show(){
	$get_current_scheme = get_option('dev_gallery_custom_style');
	$fonttype = get_option('dev_gallery_header_font');
	$bodytype = get_option('dev_gallery_body_font');
	if (($fonttype == "Cantarell, arial, serif") || ($bodytype == "Cantarell, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Cardo, arial, serif") || ($bodytype == "Cardo, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Cardo' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Crimson Text, arial, serif") || ($bodytype == "Crimson Text, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Crimson+Text' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Droid Sans, arial, serif") || ($bodytype == "Droid Sans, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Droid Serif, arial, serif") || ($bodytype == "Droid Serif, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Droid+Serif' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "IM Fell DW Pica, arial, serif") || ($bodytype == "IM Fell DW Pica, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=IM+Fell+DW+Pica' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Josefin Sans Std Light, arial, serif") || ($bodytype == "Josefin Sans Std Light, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Josefin+Sans+Std+Light' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Lobster, arial, serif") || ($bodytype == "Lobster, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
		else if (($get_current_scheme == "strip-field.css") || ($get_current_scheme == "columns.css") || ($get_current_scheme == "columns-label.css") || ($get_current_scheme == "strip-snowflake.css") || ($get_current_scheme == "strip-christmastree.css")){
		?>
		<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
		      h1, #branding{
		font-family: Lobster, arial, serif;
			}
		    </style>
		<?php
		}
	else if (($fonttype == "Molengo, arial, serif") || ($bodytype == "Molengo, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Molengo' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Neuton, arial, serif") || ($bodytype == "Neuton, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Neuton' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Nobile, arial, serif") || ($bodytype == "Nobile, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
		else if ($get_current_scheme == "divide-sofa.css"){
		?>
		<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
		      h1, #branding{
		font-family: Nobile, arial, serif;
			}
		    </style>
		<?php
		}
	else if (($fonttype == "OFL Sorts Mill Goudy TT, arial, serif") || ($bodytype == "OFL Sorts Mill Goudy TT, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Reenie Beanie, arial, serif") || ($bodytype == "Reenie Beanie, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}	
	else if (($fonttype == "Tangerine, arial, serif") || ($bodytype == "Tangerine, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Old Standard TT, arial, serif") || ($bodytype == "Old Standard TT, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Volkorn, arial, serif") || ($bodytype == "Volkorn, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Volkorn' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Yanone Kaffessatz, arial, serif") || ($bodytype == "Yanone Kaffessatz, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #branding{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
}
?>