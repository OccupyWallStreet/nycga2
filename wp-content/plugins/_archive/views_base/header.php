<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package views_base
 */
?><!DOCTYPE html>
<!--[if lt IE 7 ]>	<html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>	 <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>	 <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>	 <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php wp_title( '|', true, 'right' ); ?> <?php bloginfo( 'name' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php
if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
	wp_enqueue_script( 'comment-reply' );
	wp_enqueue_style( 'views_base', get_stylesheet_uri() );
?>
<?php 
do_action( 'views_base_before_header' );
wp_head();
do_action( 'views_base_after_header' );
?>
</head>

<body <?php body_class(); ?>>
<div id="site-container" class="hfeed clearfix">
	<div id="header-container" class="clearfix">
	<header id="masthead" class="site-header" role="banner">	
		<hgroup>
			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php 
			if(get_theme_mod('custom_logo', '') != '')
			{
				?>
				<img src="<?php echo get_theme_mod('custom_logo', '');?>" />
				<?php
			}
			else{
				bloginfo( 'name' );
			} ?></a></h1>
			<?php if(get_theme_mod('site_description_position', 0)==0){?>
			<h2 class="site-description">|<span class="site-descriptionpadding"><?php bloginfo( 'description' ); ?></span></h2>
			<?php } 
			else{
			?><h3 class="site-description"><?php bloginfo( 'description' ); ?></h3>
			<?php }?>
		</hgroup>
		<?php if(get_theme_mod('social_icons') != 'none'){?>
		<div id="social-icons">
		<?php if(get_theme_mod('social_icons') == 'default'){?>
			<ul>
				<?php if(get_theme_mod('enable_linkedin', 0)){?>
				<li><a href="<?php echo get_theme_mod('social_icons_linkedin');?>" class="social_icons_linkedin" title="<?php _e('Connect on LinkedIn', 'views_base'); ?>" target="_blank"></a></li>
				<?php }?>
				<?php if(get_theme_mod('enable_facebook', 0)){?>
				<li><a href="<?php echo get_theme_mod('social_icons_facebook');?>" class="social_icons_facebook" title="<?php _e('Follow on Facebook', 'views_base'); ?>" target="_blank"></a></li>
				<?php }?>
				<?php if(get_theme_mod('enable_google_plus', 0)){?>
				<li><a href="<?php echo get_theme_mod('social_icons_google_plus');?>" class="social_icons_google_plus" title="<?php _e('Follow on Google+', 'views_base'); ?>" target="_blank"></a></li>
				<?php }?>
				<?php if(get_theme_mod('enable_twitter', 0)){?>
				<li><a href="<?php echo get_theme_mod('social_icons_twitter');?>" class="social_icons_twitter" title="<?php _e('Follow on Twitter', 'views_base'); ?>" target="_blank"></a></li>
				<?php }?>
				<?php if(get_theme_mod('enable_flickr', 0)){?>
				<li><a href="<?php echo get_theme_mod('social_icons_flickr');?>" class="social_icons_flickr" title="<?php _e('Follow on Flickr', 'views_base'); ?>" target="_blank"></a></li>
				<?php }?>
			</ul>
		<?php }
			else if(get_theme_mod('social_icons') == 'baidu_share')
			{ 
				echo htmlspecialchars_decode(get_theme_mod('baidu_share_code'));
			}
		?>
		</div>
		<?php }?>
		<?php $header_image = get_header_image();
		if ( ! empty( $header_image ) ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" alt="" />
		<?php endif; ?>
	   	 <div id="navigation-container" class="clearfix">
	   			<nav class="site-navigation main-navigation" role="navigation">
	   				<h3 class="assistive-text"><?php _e( 'Menu', 'views_base' ); ?></h3>
	   				<div class="skip-link assistive-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'views_base' ); ?>"><?php _e( 'Skip to content', 'views_base' ); ?></a></div>
	   				<?php wp_nav_menu( array(
	   					'theme_location' => 'primary', 
	   					'menu_class' => 'mainmenu',
	   					'container' => ''
	   						)); ?>
	   			</nav>
	        </div>
		<?php do_action( 'views_base_before_close_header' );?>
		</header><!-- #masthead -->
     </div><!-- #header-container -->
	 <?php do_action( 'views_base_before_main_container' );?>
	 <div id="main-container" class="clearfix"> 
		 <div id="main">