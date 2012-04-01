<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes( 'xhtml' ); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php tj_custom_titles(); ?></title>
<?php tj_custom_description(); ?>
<?php tj_custom_keywords(); ?>
<?php tj_custom_canonical(); ?>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link rel="stylesheet" type="text/css" href="<?php bloginfo( 'template_url' ); ?>/colors/<?php echo get_option('videoplus_theme_stylesheet');?>" />
<link rel="stylesheet" type="text/css" href="<?php bloginfo( 'template_url' ); ?>/includes/fancybox/jquery.fancybox.css" />
<link rel="stylesheet" type="text/css" href="<?php bloginfo( 'template_url' ); ?>/custom.css" />
<?php wp_head(); ?>
</head>
<?php if (is_home() || is_archive() || is_search() ) add_filter('img_caption_shortcode', create_function('$a, $b, $c','return $c;'), 10, 3); ?>
<body <?php body_class(); ?>>
	<span id="home-url" name="<?php bloginfo( 'template_url' ); ?>" style="display: none;"></span>
	<header>
        <div class="wrap">
		    <?php if (get_option('videoplus_text_logo_enable') == 'on') { ?>
			<div id="text-logo">
				<h1 id="site-title"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>
				<p id="site-desc"><?php bloginfo('description'); ?></p>
			</div><!-- #text-logo -->
		    <?php } else { ?>
			    <a href="<?php bloginfo('url'); ?>"><?php $logo = (get_option('videoplus_logo') <> '') ? get_option('videoplus_logo') : get_bloginfo('template_directory').'/images/logo.png'; ?><img src="<?php echo $logo; ?>" alt="<?php bloginfo('name'); ?>" id="logo"/></a>
		    <?php }?>
		    <div class="right">
			    <div id="primary-nav">
				    <?php $menuClass = 'nav';
					$menuID = 'primary-navigation';
					$primaryNav = '';
					if (function_exists('wp_nav_menu')) {
						$primaryNav = wp_nav_menu( array( 'theme_location' => 'primary-nav', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'menu_id' => $menuID, 'echo' => false ) );
					};
					if ($primaryNav == '') { ?>
						<ul id="<?php echo $menuID; ?>" class="<?php echo $menuClass; ?>">
							<?php show_page_menu($menuClass,false,false); ?>
							<li class="top-twitter"><a href="<?php echo get_option('videoplus_twitter_url'); ?>"><?php _e('Follow us', 'themejunkie') ?></a></li>								
							<li class="top-rss"><a href="<?php echo get_option('videoplus_rss_url'); ?>"><?php _e('RSS', 'themejunkie') ?></a></li>				
						</ul>
					<?php }	else echo($primaryNav); ?>
				</div><!-- #primary-nav -->
				<div class="clear"></div>
				<div id="header-search" role="search">
					<form action="<?php bloginfo('url'); ?>" method="get" id="search-form">
						<label><input type="text" name="s" id="site_search" value="Search this site..."  onfocus="if (this.value == 'Search this site...') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search this site...';}" /></label>
						<input type="submit" id="search-submit" value="Search" />
					</form>
				</div><!-- #header-search -->
			</div><!-- .right -->
		</div><!-- .wrap-->
    </header><!-- header-->
    
    <div class="clear"></div>
    
	<nav <?php if(get_option('videoplus_secondary_nav_style') == "Full Width") { echo "style=\"width:100%;\""; } ?>>
		<div class="wrap">
			<div id="secondary-nav">
			<?php $menuClass = 'nav';
			$menuID = 'secondary-navigation';
			$secondaryNav = '';
			if (function_exists('wp_nav_menu')) {
				$secondaryNav = wp_nav_menu( array( 'theme_location' => 'secondary-nav', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'menu_id' => $menuID, 'echo' => false ) );
			};
			if ($secondaryNav == '') { ?>
				<ul id="<?php echo $menuID; ?>" class="<?php echo $menuClass; ?>">
					<?php if (get_option('videoplus_home_link') == 'on') { ?>
						<li class="<?php if(!is_archive()) echo('first');?>"><a href="<?php bloginfo('url'); ?>"><?php _e('Home', 'themejunkie') ?></a></li>
					<?php } ?>
					<?php show_categories_menu($menuClass,false,false); ?>
				</ul>
			<?php }	else echo($secondaryNav); ?>
			</div><!-- #secondary-nav -->
		</div><!-- .wrap -->
	</nav><!-- nav -->
    
    <?php if(!is_home()){?>
        <div class="wrap">
    <?php }?>
