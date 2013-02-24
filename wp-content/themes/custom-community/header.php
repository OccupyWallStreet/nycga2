<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<?php do_action('favicon') ?>
		
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
		
		<?php do_action( 'bp_head' ) ?>
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

		<?php wp_head(); ?>
	</head>

	<body <?php body_class() ?> id="cc">
 <div id="outerrim">
 
 	<?php do_action( 'bp_before_header' ) ?>
	
	<div id="header">	
	
    	<?php wp_nav_menu( array( 'container_class' => 'menu menu-top', 'theme_location' => 'menu_top','container' => 'div', 'fallback_cb' => false ) ); ?>
        <div class="row-fluid header-widgets">
            <div class="span12">
                <?php if( ! dynamic_sidebar( 'headerfullwidth' )) :?>
                <?php endif; ?>
            </div>
            <?php if (is_active_sidebar('headerleft') ){ ?>
                <div class="widgetarea cc-widget span4">
                    <?php dynamic_sidebar( 'headerleft' )?>
                </div>
            <?php } ?>

            <?php if (is_active_sidebar('headercenter') ){ ?>
                <div <?php if(!is_active_sidebar('headerleft')) { echo 'style="margin-left:350px !important"'; } ?> class="widgetarea cc-widget span4">
                    <?php dynamic_sidebar( 'headercenter' ) ?>
                </div>
            <?php } ?>

            <?php if (is_active_sidebar('headerright') ){ ?>
                <div class="widgetarea cc-widget cc-widget-right span4">
                    <?php dynamic_sidebar( 'headerright' ) ?>
                </div>
            <?php } ?>
  		</div>
		<?php do_action( 'bp_before_access')?>
				
        <div id="access" class="span12">
    		<div class="menu">
	
				<?php do_action('bp_menu') ?>

				<?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.  The menu assiged to the primary position is the one used.  If none is assigned, the menu with the lowest ID is used.  */ ?>
				<?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary','container' => '') ); ?>
			</div>
		</div>
		
		<?php do_action( 'bp_after_header_nav' ) ?>
		
		<div class="clear"></div>
	
	</div><!-- #header -->

	<?php do_action( 'bp_after_header' ) ?>		
	<?php do_action( 'bp_before_container' ) ?>

    <div id="container" class="container-fluid">
        <div class="row-fluid <?php echo cc_get_class_by_sidebar_position(); ?>">
            <?php do_action('sidebar_left');?>