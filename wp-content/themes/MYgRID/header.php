<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/style-ie.css" />
<![endif]-->
<?php 
wp_enqueue_script('jquery');
wp_enqueue_script( 'superfish', get_template_directory_uri().'/js/superfish.js');
wp_enqueue_script( 'jquery.hoverIntent.minified', get_template_directory_uri().'/js/jquery.hoverIntent.minified.js');
wp_enqueue_script('ownScript', get_stylesheet_directory_uri() .'/js/ownScript.js','','', true);
?>

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>


<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div id="container">

<?php 
	$pov_logo = get_option('pov_logo');
	$pov_disinfo = get_option('pov_disinfo');
?>



	<?php if( get_header_image() != '' ) : ?>
		<div id="custom_header">
			<img src="<?php echo get_header_image(); ?>" />
		</div>
	<?php endif; ?>
<div id="header">
	<div class="box">
	<div id="logo">
    	<h1><a href="<?php echo home_url(); ?>/"><?php bloginfo('name'); ?></a></h1>
  	</div>





	<div id="navigation">
		    <?php
   			if ( function_exists('has_nav_menu') && has_nav_menu('primary-menu') ) {
   				wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_class' => 'nav fl', 'menu_id' => 'main-nav' , 'theme_location' => 'primary-menu' ) );
    		} else {
    		?>
        <ul id="main-nav" class="nav">
			<?php 
        	if ( get_option('fiftytwo_custom_nav_menu') == 'true' ) {
        		if ( function_exists('fiftytwo_custom_navigation_output') )
					fiftytwo_custom_navigation_output();

			} else { ?>
            	
	            <?php if ( is_page() ) $highlight = "page_item"; else $highlight = "page_item current_page_item"; ?>
	            <li class="<?php echo $highlight; ?>"><a id="homes" href="<?php echo home_url(); ?>"><?php _e('Home', 'fiftytwothemes') ?></a></li>
	            <?php wp_list_pages('sort_column=menu_order&depth=6&title_li=&exclude='.get_option('fiftytwo_nav_exclude')); 
			}
			?>
        </ul><!-- /#nav -->
        <?php } ?>


	</div>
	</div>


</div>
<div class="hrlineB" style="width:100%;"></div>
