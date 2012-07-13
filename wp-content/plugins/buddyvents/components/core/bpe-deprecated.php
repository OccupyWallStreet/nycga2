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
 * Return the default tab
 *
 * @package	 	Core
 * @since 	 	1.7
 * @deprecated 	since version 2.0
 */
function bpe_get_default_tab()
{
	_deprecated_function( __FUNCTION__, 'Buddyvents v2.0', 'bpe_get_option' );
	
	return bpe_get_option( 'default_tab' );
}


/**
 * Save a timezone
 *
 * @package	 	Core
 * @since 	 	1.7
 * @deprecated 	since version 1.7.10
 */
function bpe_add_to_timezone_array( $timezone )
{
	_deprecated_function( __FUNCTION__, 'Buddyvents v1.7.10', null );
	
	global $bpe;
	
	if( empty( $timezone ) )
		return false;
	
	if( ! in_array( $timezone, (array) bpe_get_config( 'timezones' ) ) )
	{
		$slug = sanitize_title_with_dashes( str_replace( '/', '-', $timezone ) );
		$bpe->config->timezones[$slug] = $timezone;
		
		$bpe->config->timezones = array_unique( bpe_get_config( 'timezones' ) );
		update_blog_option( Buddyvents::$root_blog, 'bpe_timezones', bpe_get_config( 'timezones' ) );
	}
}

/**
 * Save a venue
 *
 * @package	 	Core
 * @since 	 	1.7
 * @deprecated 	since version 1.7.10
 */
function bpe_add_to_venue_array( $venue )
{
	_deprecated_function( __FUNCTION__, 'Buddyvents v1.7.10', null );
	
	global $bpe;
	
	if( empty( $venue ) )
		return false;
	
	if( ! in_array( $venue, (array) bpe_get_config( 'venues' ) ) )
	{
		$slug = sanitize_title_with_dashes( $venue );
		$bpe->config->venues[$slug] = $venue;
		
		$bpe->config->venues = array_unique( bpe_get_config( 'venues' ) );
		update_blog_option( Buddyvents::$root_blog, 'bpe_venues', bpe_get_config( 'venues' ) );
	}
}

/**
 * Display the navigation on single view
 *
 * @package	 	Core
 * @since 	 	1.2
 * @deprecated 	since version 1.6
 */
function bpe_single_navigation()
{
	_deprecated_function( __FUNCTION__, 'Buddyvents v1.6', null );

	$prev_event = bpe_get_previous_event_link();
	$next_event = bpe_get_next_event_link();

	if( ! empty( $prev_event ) || ! empty( $next_event ) ) : ?>
	
		<div class="single-nav">
			<div class="previous-event">
				<?php bpe_previous_event_link() ?>
			</div>
	
			<div class="next-event">
				<?php bpe_next_event_link() ?>
			</div>
		</div>
	
	<?php endif;	
}

/**
 * Send a note to facebook
 *
 * @package	 	Core
 * @since 	 	1.6
 * @deprecated 	since version 1.7.10
 */
function bpe_facebook_process_send_action( $event )
{
	_deprecated_function( __FUNCTION__, 'Buddyvents v1.7.10', 'bpe_facebook_send_update' );

	if( function_exists( 'bpe_facebook_send_update' ) )
		bpe_facebook_send_update( $event );
}

/**
 * Send a note to twitter
 *
 * @package	 	Core
 * @since 	 	1.6
 * @deprecated 	since version 1.7.10
 */
function bpe_twitter_process_send_action( $event )
{
	_deprecated_function( __FUNCTION__, 'Buddyvents v1.7.10', 'bpe_twitter_process_send' );

	if( function_exists( 'bpe_twitter_process_send' ) )
		bpe_twitter_process_send( $event );
}

/**
 * Send the event to eventbrite
 *
 * @package	 	Core
 * @since 	 	1.6
 * @deprecated 	since version 2.0
 */
function bpe_eventbrite_process_send_action( $event )
{
	_deprecated_function( __FUNCTION__, 'Buddyvents v2.0', 'bpe_eventbrite_process_send' );
	
	if( function_exists( 'bpe_eventbrite_process_send' ) )
		bpe_eventbrite_process_send( $event );
}


/**
 * Get group contact details
 *
 * @package	 	Core
 * @since 	 	1.0
 * @deprecated 	since version 2.0
 */
function bpe_groupmeta( $key, $echo = true )
{
	_deprecated_function( __FUNCTION__, 'Buddyvents v2.0', 'bpe_get_displayed_group' );
	
	if( $echo )
		echo bpe_get_displayed_group( $key );
	else
		return bpe_get_displayed_group( $key );
}

/**
 * Get the category slug
 *
 * @package	 	Core
 * @since 		1.2
 * @deprecated 	since version 2.0
 */
function bpe_get_cat_slug()
{
	_deprecated_function( __FUNCTION__, 'Buddyvents v2.0', 'bp_action_variable' );

	return bp_action_variable( 0 );
}

/**
 * Add to the main nav menu
 * 
 * @package 	API
 * @since 		1.5
 * @deprecated 	since version 2.0
 */
function bpe_api_add_menu()
{
	_deprecated_function( __FUNCTION__, 'Buddyvents v2.0', null );
		
	if( is_user_logged_in() )
	{
		?>
        <li class="last<?php if( bpe_is_events_api() ) echo ' selected'; ?>">
			<a href="<?php echo site_url() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'api_key_slug' ) .'/' ?>" title="<?php _e( 'Get your API key here', 'events' ) ?>"><?php _e( 'API', 'events' ) ?></a>
		</li>
        <?php
	}
}

/**
 * Is current page the api key page
 *
 * @package	 	Core
 * @since 	 	1.5
 * @deprecated 	since version 2.0
 */
function bpe_is_events_api()
{
	_deprecated_function( __FUNCTION__, 'Buddyvents v2.0', null );
	
	if( ! is_user_logged_in() )
		return false;
		
	if( ! bpe_get_option( 'enable_api' ) )
		return false;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'api_key_slug' ) ) )
		return true;
	
	return false;
}

/**
 * Get a short bit.ly url
 *
 * @package 	Core
 * @since 		1.6
 * @deprecated 	since version 2.0
 */
function bpe_bitly_get_result( $url )
{
	_deprecated_function( __FUNCTION__, 'Buddyvents v2.0', null );
		
	$data = wp_remote_get( $url );
		
	return wp_remote_retrieve_body( $data );
}
?>