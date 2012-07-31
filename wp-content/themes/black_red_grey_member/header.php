<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">

	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<title><?php bp_page_title() ?></title>
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

	<?php bp_styles(); ?>
	<?php?>
	
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	
	<?php if ( function_exists( 'bp_sitewide_activity_feed_link' ) ) : ?>
		<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> <?php _e('Site Wide Activity RSS Feed', 'buddypress' ) ?>" href="<?php bp_sitewide_activity_feed_link() ?>" />
	<?php endif; ?>
	
	<?php wp_head(); ?>
	
	<!--[if IE 6]>
	<link rel="stylesheet" href="<?php echo bloginfo('template_url') . '/css/ie/ie6.css' ?>" type="text/css" media="screen" />	
	<![endif]-->
	
	<!--[if IE 7]>
	<link rel="stylesheet" href="<?php echo bloginfo('template_url') . '/css/ie/ie7.css' ?>" type="text/css" media="screen" />	
	<![endif]-->
	
	<!-- start popup script for login and register -->
  <script language="JavaScript" type="text/javascript">
  function login(showhide){
    if(showhide == "show"){
        document.getElementById('popupbox').style.visibility="visible"; /* If the function is called with the variable 'show', show the login box */
    }else if(showhide == "hide"){
        document.getElementById('popupbox').style.visibility="hidden"; /* If the function is called with the variable 'hide', hide the login box */
    }
  }
  </script>
<!-- end popup script for login and register -->
</head>

<body>
<div id="wrapper">
	<!-- <div id="search-login-bar">
		<?php bp_search_form() ?>		
		<?php bp_login_bar() ?>
		
		<div class="clear"></div>
	</div> -->

	<div id="header">		
		<h1 id="logo"><a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'buddypress' ) ?>"><?php bp_site_name() ?></a></h1>
		
		<div id="bp_search_form">
			<?php bp_search_form() ?>
		</div>
		
		<div id="clear"></div>
	</div>

	<div id="nav">
			<ul id="nav">
			<li<?php if ( bp_is_page( 'home' ) ) {?> class="selected"<?php } ?>><a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'buddypress' ) ?>"><?php _e( 'Home', 'buddypress' ) ?></a></li>
			<li<?php if ( bp_is_page( BP_HOME_BLOG_SLUG ) ) {?> class="selected"<?php } ?>><a href="<?php echo get_option('home') ?>/<?php echo BP_HOME_BLOG_SLUG ?>" title="<?php _e( 'Blog', 'buddypress' ) ?>"><?php _e( 'Blog', 'buddypress' ) ?></a></li>
			<li<?php if ( bp_is_page( BP_MEMBERS_SLUG ) ) {?> class="selected"<?php } ?>><a href="<?php echo get_option('home') ?>/<?php echo BP_MEMBERS_SLUG ?>" title="<?php _e( 'Members', 'buddypress' ) ?>"><?php _e( 'Members', 'buddypress' ) ?></a></li>

			<?php if ( function_exists( 'groups_install' ) ) { ?>
				<li<?php if ( bp_is_page( BP_GROUPS_SLUG ) ) {?> class="selected"<?php } ?>><a href="<?php echo get_option('home') ?>/<?php echo BP_GROUPS_SLUG ?>" title="<?php _e( 'Groups', 'buddypress' ) ?>"><?php _e( 'Groups', 'buddypress' ) ?></a></li>
			<?php } ?>

			<?php if ( function_exists( 'bp_blogs_install' ) ) { ?>
				<li<?php if ( bp_is_page( BP_BLOGS_SLUG ) ) {?> class="selected"<?php } ?>><a href="<?php echo get_option('home') ?>/<?php echo BP_BLOGS_SLUG ?>" title="<?php _e( 'Blogs', 'buddypress' ) ?>"><?php _e( 'Blogs', 'buddypress' ) ?></a></li>
			<?php } ?>

			<?php do_action( 'bp_nav_items' ); ?>
		</ul>
	</div>
	




