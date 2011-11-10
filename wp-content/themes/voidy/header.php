<?php
global $options, $option_values;

foreach ($options as $value) {
	if($value['id'] != "voidy_temp"){
	    if (empty($option_values[ $value['id']])) {
			$$value['id'] = $value['std'];
		} else {
			$$value['id'] = $option_values[ $value['id'] ]; 
		}
	}
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php wp_title( '|', true, 'right' ); ?><?php bloginfo('name'); ?></title>
	
	<link type="text/css" rel="stylesheet" media="all" href="<?php bloginfo('stylesheet_url'); ?>" />
	
	<?php if ($voidy_favicon) { ?>
		<link rel="shortcut icon" href="<?php echo $voidy_favicon; ?>" type="image/vnd.microsoft.icon" />
		<link rel="icon" href="<?php echo $voidy_favicon; ?>" type="image/gif" />
	<?php } add_theme_support( 'automatic-feed-links' ); ?>
	<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div id="header">
	<div id="logo">
		<div id="h1"><a href="<?php echo get_option('home'); ?>/">
			<?php if ($voidy_logo) { 
				echo "<img src='".$voidy_logo."' style='".$voidy_logo_style."' alt='".get_bloginfo('name')."' />";
			} else { bloginfo('name'); } ?>
		</a></div>
		<div id="h2" class="description"><?php bloginfo('description'); ?></div>
	</div>
	<div id="header-icons">
	
		<?php if ($voidy_hide_twitter == "true") { ?>
			<div class="spacer"></div>
		<?php }else{ ?>
			<div class="twitter"><a href="http://twitter.com/<?php if ($voidy_twitter) {echo $voidy_twitter;} ?>">&nbsp;</a></div>
		<?php } ?>
		
		<?php if ($voidy_hide_rss == "true") { ?>
			<div class="spacer"></div>
		<?php }else{ ?>
			<div class="rss"><a href="<?php if ($voidy_rss) {echo $voidy_rss;} else { echo get_bloginfo('rss_url'); } ?>">&nbsp;</a></div>
		<?php } ?>
	</div>
		
	<div id="menu">
		<div class="menu-bottom">
			<?php wp_nav_menu( array( 'container_class' => 'menu-header', 'menu' => 'Primary Menu', 'fallback_cb' => 'default_nav_menu' , 'depth' => ($voidy_disable_submenus=="true" ? 1 : 3)) ); ?>
			<div class="spacer" style="clear: both;"></div>
		</div>
	</div>
</div>