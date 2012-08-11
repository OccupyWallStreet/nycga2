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
 * Search form action
 * 
 * @package	Core
 * @since 	1.5
 */
function bpe_directory_events_search_action( $echo = true )
{
	$action = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'search_slug' ) .'/'. bpe_get_option( 'results_slug' ) .'/';
	
	if( $echo )
		echo $action;
	else
		return $action;
}

/**
 * Display the search form
 * 
 * @package	Core
 * @since 	1.0
 */
function bpe_directory_events_search_form()
{
	$search_value = ( ! empty( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : __( 'Search Events...', 'events' );

	$search  = '<form action="'. bpe_directory_events_search_action( false ) .'" method="get" id="search-events-form">';
	$search .= '<label><input type="text" name="s" id="events_search" value="'. esc_attr( $search_value ) .'" onfocus="if (this.value == \''. __( 'Search Events...', 'events' ) .'\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \''. __( 'Search Events...', 'events' ) .'\';}" /></label>';
	$search .= '<input type="submit" id="events_search_submit" value="'. __( 'Search', 'events' ) .'" />';
	$search .= '</form>';

	echo apply_filters( 'bpe_directory_events_search_form', $search, $search_value );
}

/**
 * Add events to main search
 * 
 * @package	Core
 * @since 	2.0
 */
function bpe_add_to_main_search( $options )
{
	$options['events'] = __( 'Events', 'events' );
	
	return $options;
}
add_filter( 'bp_search_form_type_select_options', 'bpe_add_to_main_search' );

/**
 * Add events to main search
 * 
 * @package	Core
 * @since 	2.0
 */
function bpe_redirect_event_search( $redirect, $search_terms )
{
	global $bp;
	
	if( $_POST['search-which'] == 'events' )
		$redirect = home_url( bpe_get_base( 'root_slug' ) .'?s='. urlencode( $search_terms ) );
		
	return $redirect;
}
add_filter( 'bp_core_search_site', 'bpe_redirect_event_search', 10, 2 );

/**
 * Return the search query
 * 
 * @package	Core
 * @since 	1.1
 */
function bpe_get_search_query()
{
	$search_query = get_search_query();
	
	return ( empty( $search_query ) ? __( 'location', 'events' ) : $search_query );
}

/**
 * Process event search
 * 
 * @package	Core
 * @since 	1.6
 */
function bpe_validate_event_search()
{
	if( isset($_POST['search-events'] ) )
	{
		if( empty( $_POST['l'] ) && ! bpe_loggedin_user_has_location() || empty( $_POST['l'] ) && ! is_user_logged_in() || empty( $_POST['r'] ) )
		{
			$cookie =  array(
				'loc' 	 => ( isset( $_POST['l'] ) ? $_POST['l'] : '' ),
				'radius' => ( isset( $_POST['r'] ) ? $_POST['r'] : '' ),
				'term' 	 => ( isset( $_POST['s'] ) ? $_POST['s'] : '' )
			);
			
			@setcookie( 'buddyvents_search', serialize( $cookie ), time() + 86400, COOKIEPATH );

			bp_core_add_message( __( 'Please fill in all fields marked with *', 'events' ), 'error' );
			bp_core_redirect( bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'search_slug' ) .'/' );
		}
		
		@setcookie( 'buddyvents_search', false, time() - 1000, COOKIEPATH );
	}
}
add_action( 'wp', 'bpe_validate_event_search', 2 );
?>