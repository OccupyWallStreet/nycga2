<?php
/**
 * Layout functions.
 *
 * Useful functions for developers.
 *
 * @package PageLines Framework
 * @author PageLines
 */

/**
 * Get current page mode
 *
 * @return string
 */
function pl_layout_mode() {
	
	$layout = pagelines_layout_library_data();	
	return $layout->layout_mode;
}

/**
 * Get current page width
 *
 * @return int
 */
function pl_page_width() {
	
	$layout = pagelines_layout_library_data();	
	return $layout->layout_map['content_width'];
}

/**
 * Get current page responsive width
 *
 * @return int
 */
function pl_responsive_width() {
	
	$layout = pagelines_layout_library_data();	
	return $layout->layout_map['responsive_width'];
}

/**
 * Get current page content width
 *
 * @return int
 */
function pl_content_width() {
	
	$mode = pl_layout_mode();
	
	$layout = pagelines_layout_library_data();	
	return $layout->layout_map[ $mode ][ 'maincolumn_width' ];
}

/**
 * Get current page primary sidebar width
 *
 * @return int
 */
function pl_sidebar_width() {
	
	$mode = pl_layout_mode();
	
	$layout = pagelines_layout_library_data();	
	return $layout->layout_map[ $mode ][ 'primarysidebar_width' ];
}

/**
 * Get current page secondary sidebar width
 *
 * @return int
 */
function pl_secondary_sidebar_width() {

	$width = pl_page_width() - pl_sidebar_width() - pl_content_width();
		
	return $width;
}

/**
 * Get current page full layout data
 *
 * @return array
 */
function pagelines_layout_library_data() {
		
		global $pagelines_layout;

		if ( !is_object( $pagelines_layout ) )
			build_pagelines_layout();
		
		return $pagelines_layout;	
}

/**
 * Add pages to main settings area.
 *
 * @since 2.2
 *
 * @param $args Array as input.
 * @param string $name Name of page.
 * @param string $title Title of page.
 * @param string $path Function use to get page contents.
 * @param array $array Array containing page page of settings.
 * @param string $type Type of page.
 * @param string $raw Send raw HTML straight to the page.
 * @param string $layout Layout type.
 * @param string $icon URI for page icon.
 * @param int $postion Position to insert into main menu.
 * @return array $optionarray 
 */
function pl_add_options_page( $args ) {
	

	$defaults = array(
	
		'name'		=>	null,
		'title'	 	=>	'custom page',
		'path'		=>	null,
		'array'		=>	null,
		'type'		=>	'text_content_null',
		'raw'		=>	'',
		'layout'	=>	'full',
		'icon'		=>	PL_ADMIN_ICONS.'/settings.png',
		'position'	=>	null
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	global $pagelines_add_options_page;

	if ( isset( $args['name'] )  && ! isset( $pagelines_add_options_page[ $args['name'] ] ) )
		$pagelines_add_options_page[$args['name']] = $args;
	
}

add_filter( 'pagelines_options_array', 'pl_add_options_page_filter' );

/**
 * Filter to add custom pages to main settings area.
 *
 * @since 2.2
 *
 * @param array $optionarray 
 * @return array $optionarray 
 */
function pl_add_options_page_filter( $optionarray ){

		global $pagelines_add_options_page;
		
		if ( ! isset( $pagelines_add_options_page ) || !is_array( $pagelines_add_options_page ) )
			return $optionarray;
			
		foreach( $pagelines_add_options_page as $page => $data ) {
			
			$content = ( $data['path'] ) ? $data['path']() : $data['raw'];

			if( is_array( $data['array'])) {
				
				// Merge in icon to option array
				$data_array = array_merge(array('icon'=>$data['icon']), $data['array']);
				
				$out[$page] = $data_array;
	
			} else {

				$out[$page] = array(

					'icon'		=>	$data['icon'],

					$page	=>	array(
					
						'type'		=>	$data['type'],
						'shortexp'	=>	$content,
						'title'		=>	$data['title'],
						'layout'	=>	$data['layout']					
					)
				);
			}
			if ( isset( $data['position']) && is_numeric( $data['position'] ) )
				$optionarray = pl_insert_into_array( $optionarray, $out, $data['position']);
			else
				$optionarray[$page] = $out[$page];
		}

		return $optionarray;
}


/**
 * PL Global Option
 *
 * Add global options.
 *
 * @since       2.2
 *
 * @link        http://www.pagelines.com/wiki/Pl_global_option
 *
 * @param       $args
 * @internal    param string $menu Menu slug.
 * @internal    param array $options The options to insert.
 * @internal    param string $location before|after|top|bottom where to insert.
 * @internal    param string $option string If before or after, where?
 */
function pl_global_option( $args ) {
	
	$defaults = array(
	
		'menu'		=>	'custom_options',
		'options'	=>	null,
		'location'	=>	'bottom',
		'option'	=>	false
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	
	global $pagelines_add_global_option;

	if ( isset( $args['menu'] )  && isset( $args['options'] ) && is_array( $args['options'] ) )
		$pagelines_add_global_option[] = array(
			
			'menu'		=>	$args['menu'],
			'options'	=>	$args['options'],
			'location'	=>	$args['location'],
			'option'	=>	$args['option']
		);
}
add_filter( 'pagelines_options_array', 'pl_add_global_options_filter' );

function pl_add_global_options_filter( $optionarray ){

		global $pagelines_add_global_option;
		
		if ( ! isset( $pagelines_add_global_option ) || !is_array( $pagelines_add_global_option ) )
			return $optionarray;
	
		foreach( $pagelines_add_global_option as $key => $data ) {
			
			if ( ! $data['menu'] )
				return $optionarray;
			
			if ( $data['menu'] == 'custom_options' && !isset( $optionarray[$data['menu']] ) )
				$optionarray[$data['menu']] = array();
			
			if ( $data['location'] == 'before' || $data['location'] == 'after' && $data['option'] ) {
				
				$optionarray[$data['menu']] = pl_array_insert( $optionarray[$data['menu']], $data['option'], $data['options'], ( $data['location'] == 'before' ) ? true : false );
			}
			
			if ( $data['location'] == 'top' ) {
				$optionarray[$data['menu']] = pl_insert_into_array( $optionarray[$data['menu']], $data['options'], 0);
			}
			
			if ( $data['location'] == 'bottom' ) {				
				$optionarray[$data['menu']] = pl_insert_into_array( $optionarray[$data['menu']], $data['options'], 9999);
			}
		}

return $optionarray;
}
