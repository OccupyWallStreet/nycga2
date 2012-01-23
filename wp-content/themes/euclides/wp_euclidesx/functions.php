<?php 
	if(!defined('CI_DOMAIN'))
		define('CI_DOMAIN', 'ci_euclides');

	load_theme_textdomain( 'ci_euclides', TEMPLATEPATH . '/lang' );

	// This is the main options array. Can be accessed as a global in order to reduce
	if(!defined('THEME_OPTIONS'))
		define('THEME_OPTIONS', 'ci_euclides_theme_options');
	$ci = get_option(THEME_OPTIONS);
	$ci_defaults = array();

	require_once('functions/ci_development.php');
	require_once('functions/ci_generic.php');
	require_once('functions/ci_widgets.php');
	require_once('functions/nav_menus.php');
	require_once('functions/post_types.php');
	require_once('functions/comments.php');
	require_once('functions/sidebars.php');
	require_once('ci_panel/ci_panel.php');



	//
	// Define our various image sizes.
	//
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 180, 180, true );
	add_image_size( 'ci_euclides_cptr', 181, 90, true);
	add_image_size( 'ci_euclides_side', 284, 120, true);


	
	// Handle Feeds.
	ci_register_custom_feed();



	//If just activated, go to our options page.
	if ( is_admin() and isset($_GET['activated'] ) and $pagenow == "themes.php" )
	{
		ci_default_options(true);
		wp_redirect( 'themes.php?page=ci_panel.php' );
	}

	add_filter('admin_footer_text', 'ci_change_admin_footer');
	function ci_change_admin_footer($str) {
		echo '<a href="http://www.cssigniter.com/">CSSIgniter</a> - <a href="http://www.cssigniter.com/forum/">Theme Support</a>';
	} 


?>