<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */
 
// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Look for the templates in the proper places
 * 
 * @package	Core
 * @since 	1.0
 */
function bpe_load_template_filter( $found_template, $templates )
{
	global $bp;

	if( bp_is_current_component( bpe_get_base( 'slug' ) ) )
	{
		foreach( (array)$templates as $template )
		{
			if( file_exists( STYLESHEETPATH . '/' . $template ) )
				$filtered_templates[] = STYLESHEETPATH . '/' . $template;
				
			else
				$filtered_templates[] = EVENT_ABSPATH . 'templates/' . $template;
		}
	
		return apply_filters( 'bpe_load_template_filter', $filtered_templates[0] );
	}
	else
		return $found_template;
}
add_filter( 'bp_located_template', 'bpe_load_template_filter', 10, 2 );

/**
 * Load a template in the correct order
 * 
 * @package	Core
 * @since 	1.0
 */
function bpe_load_template( $template_name )
{
	global $bpe, $bp; // makes these vars available within the included templates
	
	if( file_exists( STYLESHEETPATH .'/'. $template_name .'.php' ) )
		$located = STYLESHEETPATH .'/'. $template_name .'.php';
		
	elseif( file_exists( TEMPLATEPATH .'/'. $template_name .'.php' ) )
		$located = TEMPLATEPATH .'/'. $template_name .'.php';
	
	else
		$located = EVENT_ABSPATH .'templates/'. $template_name .'.php';

	include( $located );
}
?>