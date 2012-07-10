<?php

class PageLinesUpgradePaths {
	

	/**
	*
	* @TODO document
	*
	*/
	function __construct() {
  
		if ( ! VPRO && 'pagelines' == basename( pl_get_uri( false ) ) ) {

			update_option( PAGELINES_SETTINGS, pagelines_settings_defaults() );
			update_option( PAGELINES_TEMPLATE_MAP, the_template_map() );
		}

		/**
		* Insure the correct default logo is being displayed after import.
		*/      
		if ( ! VPRO && 'logo-platform.png' == basename( ploption( 'pagelines_custom_logo' ) ) )
			plupop( 'pagelines_custom_logo',  PL_IMAGES . '/logo.png' );

		/**
		* Fix broken repeated excerpt problem on pagelines.com
		*/      
		if ( ! VPRO && 'pagelines' == basename( pl_get_uri( false ) ) ) {

			if ( ! isset( $a['content_blog'] ) || true != $a['content_blog'] )
				plupop( 'content_blog', true );

			if ( ! isset( $a['content_blog'] ) || true == $a['excerpt_blog'] )
				plupop( 'excerpt_blog', false );

		/**
		* Fix broken templates
		*/    
		$t = ( array ) get_option( PAGELINES_TEMPLATE_MAP, the_template_map() );
		$s = ( array ) get_option( PAGELINES_SPECIAL );

		$t['main']['templates']['posts']['sections'] = array( 'PageLinesQuickSlider', 'PageLinesBoxes' );
		$s['posts']['_pagelines_layout_mode'] = 'fullwidth';
		update_option( PAGELINES_SPECIAL, $s );   
		update_option( PAGELINES_TEMPLATE_MAP, $t );  
		plupop( 'pagelines_version', CORE_VERSION );
		}

		/**
		* Upgrade from Platform(pro) to PageLines and import settings.
		*/  
		$pagelines = get_option( PAGELINES_SETTINGS );
		$platform = get_option( PAGELINES_SETTINGS_LEGACY );

		if ( is_array( $pagelines ) ) {

		if ( ! isset( $settings['pagelines_version'] ) )
			plupop( 'pagelines_version', CORE_VERSION );

		// were done.
		return;
		}

		if ( is_array( $platform ) ) {
			$this->upgrade( $platform );      
		}

	}

	function rebuild_sidebars( $pagelines ) {
		
		if ( ! VPRO )
			return;

		$version = ( isset( $pagelines['pagelines_version'] ) ) ? $pagelines['pagelines_version'] : '';
		
		// possible 'reset options'
		if ( ! $version ) {
			plupop( 'pagelines_version', CORE_VERSION );
			return;
		}
		
		// if on version 2.2 in settings
		if ( version_compare( $version, '2.2-b1' ) >= 0 || version_compare( get_theme_mod( 'pagelines_version' ), '2.2-b1' ) >= 0 ) 
			return;
		
		if ( isset( $pagelines['enable_sidebar_reorder'] ) && $pagelines['enable_sidebar_reorder'] ) {
			
			// no need to do this...
			plupop( 'pagelines_version', CORE_VERSION );
			return;
		}
		$sidebars = get_option( 'sidebars_widgets' );
		
		$new_sidebars = array(
			
			'wp_inactive_widgets'	=> $sidebars['wp_inactive_widgets'],
			'sidebar-1'	=>	$sidebars['sidebar-7'],
			'sidebar-2'	=>	$sidebars['sidebar-8'],
			'sidebar-3'	=>	$sidebars['sidebar-9'],
			'sidebar-4'	=>	$sidebars['sidebar-10'],
			'sidebar-5'	=>	$sidebars['sidebar-6'],
			'sidebar-6'	=>	$sidebars['sidebar-5'],
			'sidebar-7'	=>	$sidebars['sidebar-2'],
			'sidebar-8'	=>	$sidebars['sidebar-3'],
			'sidebar-9'	=>	$sidebars['sidebar-4'],
			'sidebar-10'=>	$sidebars['sidebar-1'],
			'array_version'	=> $sidebars['array_version']
			
		);
		update_option( 'sidebars_widgets', $new_sidebars );
		plupop( 'pagelines_version', CORE_VERSION );
		return;	
	}


	/**
	*
	* @TODO document
	*
	*/
	function upgrade( $settings ) {

		
			// beta versions will all be using the old array...
			if ( isset( $settings['pl_login_image']) )
				$this->beta_upgrade( $settings );
			else 
				$this->full_upgrade( $settings );
	}

	/**
	*
	* @TODO document
	*
	*/
	function full_upgrade( $settings ) {
		
		// here we go, 1st were gonna set the defaults
		add_option( PAGELINES_SETTINGS, pagelines_settings_defaults() );
		add_option( PAGELINES_TEMPLATE_MAP, get_option( PAGELINES_TEMPLATE_MAP_LEGACY ) );
		
		$defaults = get_option( PAGELINES_SETTINGS );

		// copy the template-maps
		update_option( PAGELINES_TEMPLATE_MAP, get_option( PAGELINES_TEMPLATE_MAP_LEGACY ) );

		// now were gonna merge...
	
		foreach( $settings as $key => $data ) {
		
			if ( isset( $defaults[$key]) ) {
				if ( !empty( $data ) )
					plupop( $key, $data );
			}
		}
		plupop( 'pagelines_version', CORE_VERSION );
	}

	/**
	*
	* @TODO document
	*
	*/
	function beta_upgrade( $settings ) {
		
		update_option( PAGELINES_SETTINGS, $settings );
		update_option( PAGELINES_TEMPLATE_MAP, get_option( PAGELINES_TEMPLATE_MAP_LEGACY ) );		
	}		
}

new PageLinesUpgradePaths;
