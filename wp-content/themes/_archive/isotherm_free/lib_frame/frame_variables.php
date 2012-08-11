<?php

/* DEFINE FRAMEWORK VARIABLES */
/*------------------------------------------------------------------*/
    $frameversion = "6.6.2";
	$shortname = "bizzthemes";
	$bloghomeurl = "".trailingslashit( get_bloginfo('wpurl') )."";
	$frameurl = "http://tinyurl.com/36qq7rh";
	
/* DEFINE DIRECTORY CONSTANTS */
/*------------------------------------------------------------------*/
    define('BIZZ_FRAME_ROOT', get_bloginfo('template_url') . '/lib_frame');
	define('BIZZ_FRAME_CSS', get_bloginfo('template_url') . '/lib_frame/css');
	define('BIZZ_FRAME_IMAGES', get_bloginfo('template_url') . '/lib_frame/images');
	define('BIZZ_FRAME_JS', get_bloginfo('template_url') . '/lib_frame/js');
	
/* FRAMEWORK FILES */
/*------------------------------------------------------------------*/
	require_once (BIZZ_LIB_FRAME . '/frame_seo.php');
	require_once (BIZZ_LIB_FRAME . '/frame_actions.php');
	require_once (BIZZ_LIB_FRAME . '/frame_scripts.php'); 
	require_once (BIZZ_LIB_FRAME . '/frame_functions.php');
    require_once (BIZZ_LIB_FRAME . '/frame_settings.php'); 
	require_once (BIZZ_LIB_FRAME . '/frame_options.php');
	require_once (BIZZ_LIB_FRAME . '/frame_editor.php');
	require_once (BIZZ_LIB_FRAME . '/frame_updates.php');
	require_once (BIZZ_LIB_FRAME . '/frame_metabox.php');
	require_once (BIZZ_LIB_FRAME . '/frame_hooks.php');
	require_once (BIZZ_LIB_FRAME . '/frame_classes.php');
	require_once (BIZZ_LIB_FRAME . '/frame_shortcodes.php');
	
/* THEME OPTIONS (get from database) */
/*----------------------------------------------------------------------------------------------------------------------*/		
	
	// Regular saved options
	$theme_options = get_option('bizzthemes_options');
	
	// Quote fix saved options
	$theme_odata = $wpdb->get_row("SELECT * FROM $wpdb->options WHERE option_name = 'bizzthemes_options'", ARRAY_A);
	$theme_alt_options = $theme_odata['option_value'];
	$theme_alt_options = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $theme_alt_options );
	$theme_alt_options = preg_replace('!s:(\d+):"(.*?)";!se', '"s:".strlen("$2").":\"$2\";"', $theme_alt_options );
	
	// Default saved options
	global $options;
	$default_options = array();
	foreach ($options as $key => $value){
	    if (isset($value['id']) && isset($value['std']))
		    $default_options[$value['id']] = $value['std'];
	}
	
	// options saved
	if (!empty($theme_options)){
	    $theme_options = bizz_reverse_escape( $theme_options );
		$opt = stripslashes_deep($theme_options);
	}
	// alt options saved
	elseif (!empty($theme_alt_options)){
	    $theme_alt_options = bizz_reverse_escape( $theme_alt_options );
		$opt = unserialize($theme_alt_options);
		$opt = stripslashes_deep($opt);
	}
	// default options
	else {
	    $opt = stripslashes_deep($default_options);
	}
		
/* DESIGN OPTIONS (get from database) */
/*----------------------------------------------------------------------------------------------------------------------*/		
	
	// Regular saved options
	$theme_design = get_option('bizzthemes_design');
	
	// Quote fix saved design
	$theme_ddata = $wpdb->get_row("SELECT * FROM $wpdb->options WHERE option_name = 'bizzthemes_design'", ARRAY_A);
	$theme_alt_design = $theme_ddata['option_value'];
	$theme_alt_design = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $theme_alt_design );
	$theme_alt_design = preg_replace('!s:(\d+):"(.*?)";!se', '"s:".strlen("$2").":\"$2\";"', $theme_alt_design );
	
	// design saved
	if (!empty($theme_design)){
	    $theme_design = bizz_reverse_escape( $theme_design );
		$optd = stripslashes_deep($theme_design);
	}
	// alt design saved
	elseif (!empty($theme_alt_design)){
	    $theme_alt_design = bizz_reverse_escape( $theme_alt_design );
		$optd = unserialize($theme_alt_design);
		$optd = stripslashes_deep($opt);
	}
	
/* theme backend name */
/*----------------------------------------------------------------------------------------------------------------------*/	
    if (
	    isset($GLOBALS['opt']['bizzthemes_branding_back']) && 
	    isset($GLOBALS['opt']['bizzthemes_branding_back_name']) && 
	    $GLOBALS['opt']['bizzthemes_branding_back'] == 'true' && 
	    $GLOBALS['opt']['bizzthemes_branding_back_name'] <> '' 
	)
	    $themename = $GLOBALS['opt']['bizzthemes_branding_back_name'];
	else
	    $themename = $GLOBALS['themename'];

	