<?php
/**
 * Initialize Singletons Style Globals
 *
 * @since 2.0.b20
 **/

/**
 * Sets default global settings -- NOTE only fires first time
 */
if(!get_option(PAGELINES_SETTINGS))
	add_option( PAGELINES_SETTINGS, pagelines_settings_defaults() ); 

/**
 * Set options globals, cache?
 */
$GLOBALS['global_pagelines_settings'] = get_option( PAGELINES_SETTINGS );	
$GLOBALS['pagelines_special_meta'] = get_option( PAGELINES_SPECIAL );	

/**
 * Singleton >> Integrations Handling
 */
$GLOBALS['pl_active_integrations'] = get_option(PAGELINES_INTEGRATIONS);

/**
 * Singletons >> Metapanel Options
 */
$GLOBALS['metapanel_options'] =  new PageLinesMetaPanel( array('global' => true) );
$GLOBALS['profile_panel_options'] =  new ProfileEngine( );
$GLOBALS['global_meta_options'] = get_global_meta_options();

/**
 * PageLines Section Factory Object (Singleton)
 * Note: Must load before the config template file
 * @global object $pl_section_factory
 * @since 1.0.0
 */
$GLOBALS['pl_section_factory'] = new PageLinesSectionFactory();


/**
 * Dynamic CSS Factory
 * @global object $css_factory
 * @since 2.0.b6
 */
$GLOBALS['css_factory'] = array( );

/**
 * Template Buffer
 * @global arrayt $tmpl
 * @since 2.1.4
 */
$GLOBALS['plbuffer'] = array( );

$GLOBALS['lesscode'] = '';
