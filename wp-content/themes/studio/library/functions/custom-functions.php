<?php

function wt_get_ID_by_page_name($page_name)
{
	global $wpdb;
	$page_name_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'");
	return $page_name_id;
}
////////////////////////////////////////////////////////////////////////////////
// new thumbnail code for wp 2.9+
////////////////////////////////////////////////////////////////////////////////
if ( function_exists( 'add_theme_support' ) ) { // Added in 2.9
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 150, 150, true ); // Normal post thumbnails
	add_image_size( 'single-post-thumbnail', 400, 9999 ); // Permalink thumbnail size
}
// This theme uses wp_nav_menu() in one location.
register_nav_menus( array(
	'primary' => __( 'Primary Navigation', 'studio' ),
) );

function wpmudev_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'wpmudev_page_menu_args' );

function signup_button(){
	include (get_template_directory() . '/library/options/options.php');
	$signupfeat_buttontext = get_option('dev_studio_signupfeat_buttontext');
	$signupfeat_buttontext_custom = get_option('dev_studio_signupfeat_buttontextcustom');
	
	if ($signupfeat_buttontext == ""){
		$signupfeat_buttontext = "Join now";
	}
	
	if (($bp_existed == 'true') && ($signupfeat_buttontext_custom == "")){
	?>
		<a href="<?php echo get_option('home') ?>/register/" class="button"><?php echo stripslashes($signupfeat_buttontext); ?></a>
		
		<?php		
	}
	else if ($signupfeat_buttontext_custom != ""){
		?>
			<a href="<?php echo $signupfeat_buttontext_custom; ?>" class="button"><?php echo stripslashes($signupfeat_buttontext); ?></a>
		<?php
	}
	else{
		if ($multi_site_on == 'true'){
				?>
				<a href="<?php echo get_option('home') ?>/wp-signup.php" class="button"><?php echo stripslashes($signupfeat_buttontext); ?></a>
				<?php
		}
		else{
			?>
		  <a href="<?php echo get_option('home') ?>/wp-login.php" class="button"><?php echo stripslashes($signupfeat_buttontex); ?></a>
		<?php
		}
	}
	
}
function font_show(){
	$fonttype = get_option('dev_studio_header_font');
	$bodytype = get_option('dev_studio_body_font');
	$font ="";
	if (($fonttype == "")&&($bodytype == "")){
	?>
<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
      h1, h2, h3, h4, h5, h6, #site-logo{
font-family: 'Nobile', arial, serif;
	}
	body{
		font-family: Helvetica, Arial, Sans-serif;
	}
    </style>
	<?php
	}
	else if (($fonttype == "Cantarell, arial, serif") || ($bodytype == "Cantarell, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
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
	      h1, h2, h3, h4, h5, h6, #site-logo{
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
	      h1, h2, h3, h4, h5, h6, #site-logo{
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
	      h1, h2, h3, h4, h5, h6, #site-logo{
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
	      h1, h2, h3, h4, h5, h6, #site-logo{
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
	      h1, h2, h3, h4, h5, h6, #site-logo{
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
	      h1, h2, h3, h4, h5, h6, #site-logo{
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
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
		$font == "set";
	}
	else if (($fonttype == "Molengo, arial, serif") || ($bodytype == "Molengo, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Molengo' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
		$font == "set";
	}
	else if (($fonttype == "Neuton, arial, serif") || ($bodytype == "Neuton, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Neuton' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	$font == "set";
	}
	else if (($fonttype == "Nobile, arial, serif") || ($bodytype == "Nobile, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "OFL Sorts Mill Goudy TT, arial, serif") || ($bodytype == "OFL Sorts Mill Goudy TT, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
		$font == "set";
	}
	else if (($fonttype == "Reenie Beanie, arial, serif") || ($bodytype == "Reenie Beanie, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
		$font == "set";
	}	
	else if (($fonttype == "Tangerine, arial, serif") || ($bodytype == "Tangerine, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
		$font == "set";
	}
	else if (($fonttype == "Old Standard TT, arial, serif") || ($bodytype == "Old Standard TT, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
		$font == "set";
	}
	else if (($fonttype == "Volkorn, arial, serif") || ($bodytype == "Volkorn, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Volkorn' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
		$font == "set";
	}
	else if (($fonttype == "Yanone Kaffessatz, arial, serif") || ($bodytype == "Yanone Kaffessatz, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
		$font == "set";
	}
	if ($font != "set"){
		?>
		<style type="text/css" media="screen">
			      h1, h2, h3, h4, h5, h6, #site-logo{
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