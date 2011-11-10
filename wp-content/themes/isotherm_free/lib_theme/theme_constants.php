<?php

/*

  FILE STRUCTURE:

- DEFINE DIRECTORY CONSTANTS
- DEFINE THEME FILES

*/
		
/* DEFINE DIRECTORY CONSTANTS */
/*------------------------------------------------------------------*/
	define('BIZZ_THEME_CSS', get_bloginfo('template_url') . '/lib_theme/css');
	define('BIZZ_THEME_IMAGES', get_bloginfo('template_url') . '/lib_theme/images');
	define('BIZZ_THEME_JS', get_bloginfo('template_url') . '/lib_theme/js');
	define('BIZZ_THEME_SKINS', get_bloginfo('template_url') . '/lib_theme/skins');
	define('BIZZ_THEME_HOOKS', BIZZ_LIB_THEME . '/hooks');
	
/* DEFINE THEME FILES */
/*------------------------------------------------------------------*/
	require_once (BIZZ_LIB_THEME . '/theme_hooks.php');
	require_once (BIZZ_LIB_THEME . '/theme_scripts.php');
	require_once (BIZZ_LIB_THEME . '/theme_options.php');
	require_once (BIZZ_LIB_THEME . '/theme_design.php');
	require_once (BIZZ_LIB_THEME . '/theme_widgets.php');
	require_once (BIZZ_LIB_THEME . '/theme_functions.php');

?>