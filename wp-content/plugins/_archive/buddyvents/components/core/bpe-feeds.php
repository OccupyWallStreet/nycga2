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
 * Reference the hcalendar specs
 *
 * @package	 Core
 * @since 	 1.5
 */
function bpe_add_hcalendar_spec()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) )
		echo '<link rel="profile" href="http://microformats.org/profile/hcalendar" />';
}
add_action( 'wp_head', 'bpe_add_hcalendar_spec' );

/**
 * iCalendar output
 *
 * @package	 Core
 * @since 	 1.5
 */
function bpe_setup_ical()
{
	global $bp, $wp_query;

	if( ! in_array( 'ical', (array)$bp->action_variables ) )
		return false;

	if( bpe_is_event_cancelled( bpe_get_displayed_event() ) )
		return false;

	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && in_array( bp_current_action(), array( bpe_get_option( 'active_slug' ), bpe_get_option( 'archive_slug' ) ) ) && ! empty( $bp->action_variables[0] ) )
	{
		$wp_query->is_404 = false;
		status_header( 200 );
	
		require_once( EVENT_ABSPATH .'components/core/bpe-ical.php' );
	
		$result = bpe_get_events( array( 'slug' => bp_action_variable( 0 ), 'future' => false ) );
	
		header( "Content-type: 	text/calendar" );
		$ics = new Buddyvents_iCal( $result['events'] );
	
		exit;
	}
}
add_action( 'wp', 'bpe_setup_ical', 0 );

/**
 * Auto discover event feeds
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_add_feed_to_head()
{
	?>
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php _e( 'Latest Events | Sitewide', 'events' ) ?>" href="<?php bpe_sitewide_events_feed_link() ?>" />
	<?php if( bpe_is_event_category() ) : ?>
    	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php _e( 'Latest Events', 'events' ) ?> | <?php echo ucwords( bp_action_variable( 0 ) ) ?>" href="<?php bpe_category_events_feed_link() ?>" />
	<?php endif; ?>
	<?php if( bpe_is_event_timezone() ) : ?>
    	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php _e( 'Latest Events', 'events' ) ?> | <?php echo bpe_get_config( 'timezones', bp_action_variable( 0 ) ) ?>" href="<?php bpe_timezone_events_feed_link() ?>" />
    <?php endif; ?>
	<?php if( bpe_is_event_venue() ) : ?>
    	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php _e( 'Latest Events', 'events' ) ?> | <?php echo bpe_get_config( 'venues', bp_action_variable( 0 ) ) ?>" href="<?php bpe_venue_events_feed_link() ?>" />
    <?php endif;
	
	if( bp_displayed_user_id() ) : ?>
        <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php echo bp_core_get_user_displayname( bp_displayed_user_id() ) ?> | <?php _e( 'Latest User Events', 'events' ) ?>" href="<?php bpe_user_events_feed_link() ?>" />
        <?php if( bpe_is_member_category() ) : ?>
        	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php echo bp_core_get_user_displayname( bp_displayed_user_id() ) ?> | <?php _e( 'Latest Category Events', 'events' ) ?>" href="<?php bpe_user_category_events_feed_link() ?>" />
        <?php endif; ?>
        <?php if( bpe_is_member_timezone() ) : ?>
        	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php echo bp_core_get_user_displayname( bp_displayed_user_id() ) ?> | <?php _e( 'Latest Timezone Events', 'events' ) ?>" href="<?php bpe_user_timezone_events_feed_link() ?>" />
        <?php endif; ?>
        <?php if( bpe_is_member_venue() ) : ?>
        	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php echo bp_core_get_user_displayname( bp_displayed_user_id() ) ?> | <?php _e( 'Latest Venue Events', 'events' ) ?>" href="<?php bpe_user_venue_events_feed_link() ?>" />
        <?php endif; ?>
    <?php endif;
}
add_action( 'wp_head', 'bpe_add_feed_to_head' );

/**
 * Setup the global events feed
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_global_events_feed()
{
	global $wp_query;

	$group_id = ( bp_is_active( 'groups' ) ) ? bp_get_current_group_id() : false;
	$user_id = bp_displayed_user_id();

	if( ! bp_is_current_component( bpe_get_base( 'slug' ) ) || ! bp_is_current_action( bpe_get_option( 'feed_slug' ) ) || $user_id || $group_id )
		return false;

	$wp_query->is_404 = false;
	status_header( 200 );

	require( EVENT_ABSPATH .'components/core/bpe-feed-class.php' );
	
	$feed = new Buddyvents_Feeds();
	$feed->title =  __( 'Latest Sitewide Events', 'events' );
	$feed->link = bpe_get_sitewide_events_feed_link();
	$feed->description = __( 'Latest Global Feed', 'events' );
	$feed->pubdate = mysql2date( 'D, d M Y H:i:s O', bpe_get_last_published(), false );
	$feed->context = 'global';
	$feed->query_args = array( 'public' => 1, 'future' => true, 'past' => false, 'max' => 10 );
	$feed->create();

	exit;
}
add_action( 'wp', 'bpe_global_events_feed', 0 );

/**
 * Setup the category events feed
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_category_events_feed()
{
	global $wp_query;

	$group_id = ( bp_is_active( 'groups' ) ) ? bp_get_current_group_id() : false;
	$user_id = bp_displayed_user_id();

	if( ! bp_is_current_component( bpe_get_base( 'slug' ) ) || ! bp_is_current_action( bpe_get_option( 'category_slug' ) ) || ! bp_is_action_variable( bpe_get_option( 'feed_slug' ), 1 ) || $user_id || $group_id )
		return false;

	$wp_query->is_404 = false;
	status_header( 200 );

	require( EVENT_ABSPATH .'components/core/bpe-feed-class.php' );
	
	$cat = bpe_get_catid_from_slug( bp_action_variable( 0 ) );
	
	$feed = new Buddyvents_Feeds();
	$feed->title = sprintf( __( '%s | Latest Events', 'events' ), ucwords( bp_action_variable( 0 ) ) );
	$feed->link = bpe_get_category_events_feed_link();
	$feed->description = sprintf( __( 'Latest Category Feed - %s', 'events' ), ucwords( bp_action_variable( 0 ) ) );
	$feed->pubdate = mysql2date( 'D, d M Y H:i:s O', bpe_category_get_last_published( $cat ), false );
	$feed->context = 'category';
	$feed->query_args = array( 'public' => 1, 'sort' => 'start_date_asc', 'future' => true, 'past' => false, 'max' => 10 );
	$feed->create();

	exit;
}
add_action( 'wp', 'bpe_category_events_feed', 0 );

/**
 * Setup the timezone events feed
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_timezone_events_feed()
{
	global $wp_query;
	
	if( bpe_get_option( 'geonames_username' ) )
		return false;

	$group_id = ( bp_is_active( 'groups' ) ) ? bp_get_current_group_id() : false;
	$user_id = bp_displayed_user_id();

	if( ! bp_is_current_component( bpe_get_base( 'slug' ) ) || ! bp_is_current_action( bpe_get_option( 'timezone_slug' ) ) || ! bp_is_action_variable( bpe_get_option( 'feed_slug' ), 1 ) || $user_id || $group_id )
		return false;

	$wp_query->is_404 = false;
	status_header( 200 );

	require( EVENT_ABSPATH .'components/core/bpe-feed-class.php' );
	
	$feed = new Buddyvents_Feeds();
	$feed->title = sprintf( __( '%s | Latest Events', 'events' ), bpe_get_config( 'timezones', bp_action_variable( 0 ) ) );
	$feed->link = bpe_get_timezone_events_feed_link();
	$feed->description = sprintf( __( 'Latest Timezone Feed - %s', 'events' ), bpe_get_config( 'timezones', bp_action_variable( 0 ) ) );
	$feed->pubdate = mysql2date( 'D, d M Y H:i:s O', bpe_timezone_get_last_published( bpe_get_config( 'timezones', bp_action_variable( 0 ) ) ), false );
	$feed->context = 'timezone';
	$feed->query_args = array( 'public' => 1, 'sort' => 'start_date_asc', 'future' => true, 'past' => false, 'max' => 10 );
	$feed->create();

	exit;
}
add_action( 'wp', 'bpe_timezone_events_feed', 0 );

/**
 * Setup the venue events feed
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_venue_events_feed()
{
	global $wp_query;
	
	$group_id = ( bp_is_active( 'groups' ) ) ? bp_get_current_group_id() : false;
	$user_id = bp_displayed_user_id();

	if( ! bp_is_current_component( bpe_get_base( 'slug' ) ) || ! bp_is_current_action( bpe_get_option( 'venue_slug' ) ) || ! bp_is_action_variable( bpe_get_option( 'feed_slug' ), 1 ) || $user_id || $group_id )
		return false;

	$wp_query->is_404 = false;
	status_header( 200 );

	require( EVENT_ABSPATH .'components/core/bpe-feed-class.php' );
	
	$feed = new Buddyvents_Feeds();
	$feed->title = sprintf( __( '%s | Latest Events', 'events' ), bpe_get_config( 'venues', bp_action_variable( 0 ) ) );
	$feed->link = bpe_get_venue_events_feed_link();
	$feed->description = sprintf( __( 'Latest Venue Feed - %s', 'events' ), bpe_get_config( 'venues', bp_action_variable( 0 ) ) );
	$feed->pubdate = mysql2date( 'D, d M Y H:i:s O', bpe_venue_get_last_published( bpe_get_config( 'venues', bp_action_variable( 0 ) ) ), false );
	$feed->context = 'venue';
	$feed->query_args = array( 'public' => 1, 'sort' => 'start_date_asc', 'future' => true, 'past' => false, 'max' => 10 );
	$feed->create();

	exit;
}
add_action( 'wp', 'bpe_venue_events_feed', 0 );

/**
 * Setup the user events feed
 *
 * @package	 Core
 * @since 	 1.1
 */
function bpe_user_events_feed()
{
	global $wp_query;
	
	if( ! bp_is_current_action( bpe_get_option( 'feed_slug' ) ) )
		return false;

	$user_id = bp_displayed_user_id();

	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && ! empty( $user_id ) )
	{
		$wp_query->is_404 = false;
		status_header( 200 );
	
		require( EVENT_ABSPATH .'components/core/bpe-feed-class.php' );
		
		$feed = new Buddyvents_Feeds();
		$feed->title = sprintf( __( '%s | Latest Events', 'events' ), bp_core_get_user_displayname( $user_id ) );
		$feed->link = bpe_get_user_events_feed_link();
		$feed->description = sprintf( __( 'Latest User Feed - %s', 'events' ), bp_core_get_user_displayname( $user_id )  );
		$feed->pubdate = mysql2date( 'D, d M Y H:i:s O', bpe_user_get_last_published(), false );
		$feed->context = 'user';
		$feed->query_args = array( 'public' => 1, 'future' => true, 'past' => false, 'max' => 10 );
		$feed->create();

		exit;
	}
}
add_action( 'wp', 'bpe_user_events_feed', 0 );

/**
 * Setup the user categtory events feed
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_user_category_events_feed()
{
	global $wp_query;
	
	if( ! bp_is_action_variable( bpe_get_option( 'feed_slug' ), 1 ) )
		return false;

	$user_id = bp_displayed_user_id();

	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'category_slug' ) ) && ! empty( $user_id ) )
	{
		$wp_query->is_404 = false;
		status_header( 200 );

		require( EVENT_ABSPATH .'components/core/bpe-feed-class.php' );

		$cat = bpe_get_catid_from_slug( bp_action_variable( 0 ) );
		
		$feed = new Buddyvents_Feeds();
		$feed->title = sprintf( __( '%s | %s | Latest Events', 'events' ), bp_core_get_user_displayname( $user_id ), ucwords( bp_action_variable( 0 ) ) );
		$feed->link = bpe_get_user_category_events_feed_link();
		$feed->description = sprintf( __( 'Latest User Category Feed - %s', 'events' ), bp_core_get_user_displayname( $user_id ) );
		$feed->pubdate = mysql2date( 'D, d M Y H:i:s O', bpe_user_get_last_published( $cat ), false );
		$feed->context = 'user_category';
		$feed->query_args = array( 'public' => 1, 'category' => $cat, 'future' => true, 'past' => false, 'max' => 10 );
		$feed->create();

		exit;
	}
}
add_action( 'wp', 'bpe_user_category_events_feed', 0 );

/**
 * Setup the user timezone events feed
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_user_timezone_events_feed()
{
	global $wp_query;

	if( ! bpe_get_option( 'geonames_username' ) )
		return false;

	if( ! bp_is_action_variable( bpe_get_option( 'feed_slug' ), 1 ) )
		return false;

	$user_id = bp_displayed_user_id();

	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'timezone_slug' ) ) && ! empty( $user_id ) )
	{
		$wp_query->is_404 = false;
		status_header( 200 );
	
		require( EVENT_ABSPATH .'components/core/bpe-feed-class.php' );
		
		$feed = new Buddyvents_Feeds();
		$feed->title = sprintf( __( '%s | %s | Latest Events', 'events' ), bp_core_get_user_displayname( $user_id ), bpe_get_config( 'timezones', bp_action_variable( 0 ) ) );
		$feed->link = bpe_get_user_category_events_feed_link();
		$feed->description = sprintf( __( 'Latest User Category Feed - %s', 'events' ), bp_core_get_user_displayname( $user_id ) );
		$feed->pubdate = mysql2date( 'D, d M Y H:i:s O', bpe_user_get_last_published( false, bpe_get_config( 'timezones', bp_action_variable( 0 ) ) ), false );
		$feed->context = 'user_timezone';
		$feed->query_args = array( 'public' => 1, 'timezone' => bpe_get_config( 'timezones', bp_action_variable( 0 ) ), 'future' => true, 'past' => false, 'max' => 10 );
		$feed->create();

		exit;
	}
}
add_action( 'wp', 'bpe_user_timezone_events_feed', 0 );

/**
 * Setup the user venue events feed
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_user_venue_events_feed()
{
	global $wp_query;
	
	if( ! bp_is_action_variable( bpe_get_option( 'feed_slug' ), 1 ) )
		return false;

	$user_id = bp_displayed_user_id();

	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'venue_slug' ) ) && ! empty( $user_id ) )
	{
		$wp_query->is_404 = false;
		status_header( 200 );
	
		require( EVENT_ABSPATH .'components/core/bpe-feed-class.php' );
		
		$feed = new Buddyvents_Feeds();
		$feed->title = sprintf( __( '%s | %s | Latest Events', 'events' ), bp_core_get_user_displayname( $user_id ), bpe_get_config( 'venues', bp_action_variable( 0 ) ) );
		$feed->link = bpe_get_user_category_events_feed_link();
		$feed->description = sprintf( __( 'Latest User Category Feed - %s', 'events' ), bp_core_get_user_displayname( $user_id ) );
		$feed->pubdate = mysql2date( 'D, d M Y H:i:s O', bpe_user_get_last_published( false, false, bpe_get_config( 'venues', bp_action_variable( 0 ) ) ), false );
		$feed->context = 'user_venue';
		$feed->query_args = array( 'public' => 1, 'location' => bpe_get_config( 'venues', bp_action_variable( 0 ) ), 'future' => true, 'past' => false, 'max' => 10 );
		$feed->create();
		
		exit;
	}
}
add_action( 'wp', 'bpe_user_venue_events_feed', 0 );

/**
 * Display link according to the page we are on (global)
 *
 * @package	 Core
 * @since 	 1.1
 */
function bpe_event_feed_links()
{
	if( bp_is_current_action( bpe_get_option( 'category_slug' ) ) && bp_action_variable( 0 ) )
		bpe_category_events_feed_link();

	elseif( bp_is_current_action( bpe_get_option( 'timezone_slug' ) ) && bp_action_variable( 0 ) )
		bpe_timezone_events_feed_link();

	elseif( bp_is_current_action( bpe_get_option( 'venue_slug' ) ) && bp_action_variable( 0 ) )
		bpe_venue_events_feed_link();
		
	else
		bpe_sitewide_events_feed_link();
}

/**
 * Sitewide feed link
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_sitewide_events_feed_link()
{
	echo bpe_get_sitewide_events_feed_link();
}
	function bpe_get_sitewide_events_feed_link()
	{
		return apply_filters( 'bpe_get_sitewide_events_feed_link', site_url( bpe_get_base( 'root_slug' ) . '/'. bpe_get_option( 'feed_slug' ) .'/' ) );
	}

/**
 * Category feed link
 *
 * @package	 Core
 * @since 	 1.1
 */
function bpe_category_events_feed_link()
{
	echo bpe_get_category_events_feed_link();
}
	function bpe_get_category_events_feed_link()
	{
		return apply_filters( 'bpe_get_category_events_feed_link', site_url( bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'category_slug' ) .'/'. bp_action_variable( 0 ) .'/'. bpe_get_option( 'feed_slug' ) .'/' ) );
	}

/**
 * Timezone feed link
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_timezone_events_feed_link()
{
	echo bpe_get_timezone_events_feed_link();
}
	function bpe_get_timezone_events_feed_link()
	{
		return apply_filters( 'bpe_get_timezone_events_feed_link', site_url( bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'timezone_slug' ) .'/'. bp_action_variable( 0 ) .'/'. bpe_get_option( 'feed_slug' ) .'/' ) );
	}

/**
 * Venue feed link
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_venue_events_feed_link()
{
	echo bpe_get_venue_events_feed_link();
}
	function bpe_get_venue_events_feed_link()
	{
		return apply_filters( 'bpe_get_venue_events_feed_link', site_url( bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'venue_slug' ) .'/'. bp_action_variable( 0 ) .'/'. bpe_get_option( 'feed_slug' ) .'/' ) );
	}

/**
 * Display link according to the page we are on (user)
 *
 * @package	 Core
 * @since 	 1.6
 */
function bpe_event_user_category_feed_links()
{
	if( bp_is_current_action( bpe_get_option( 'category_slug' ) ) && bp_action_variable( 0 ) )
		bpe_user_category_events_feed_link();

	elseif( bp_is_current_action( bpe_get_option( 'timezone_slug' ) ) && bp_action_variable( 0 ) )
		bpe_user_timezone_events_feed_link();

	elseif( bp_is_current_action( bpe_get_option( 'venue_slug' ) ) && bp_action_variable( 0 ) )
		bpe_user_venue_events_feed_link();
		
	else
		bpe_user_events_feed_link();
}

/**
 * User feed link
 *
 * @package	 Core
 * @since 	 1.1
 */
function bpe_user_events_feed_link()
{
	echo bpe_get_user_events_feed_link();
}
	function bpe_get_user_events_feed_link()
	{
		return apply_filters( 'bpe_get_user_events_feed_link', bp_displayed_user_domain() . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'feed_slug' ) .'/' );
	}

/**
 * User category feed link
 *
 * @package	 Core
 * @since 	 1.6
 */
function bpe_user_category_events_feed_link()
{
	echo bpe_get_user_category_events_feed_link();
}
	function bpe_get_user_category_events_feed_link()
	{
		return apply_filters( 'bpe_get_user_category_events_feed_link', bp_displayed_user_domain() . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'category_slug' ) .'/'. bp_action_variable( 0 ) .'/'. bpe_get_option( 'feed_slug' ) .'/' );
	}

/**
 * User timezone feed link
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_user_timezone_events_feed_link()
{
	echo bpe_get_user_timezone_events_feed_link();
}
	function bpe_get_user_timezone_events_feed_link()
	{
		return apply_filters( 'bpe_get_user_timezone_events_feed_link', bp_displayed_user_domain() . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'timezone_slug' ) .'/'. bp_action_variable( 0 ) .'/'. bpe_get_option( 'feed_slug' ) .'/' );
	}

/**
 * User venue feed link
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_user_venue_events_feed_link()
{
	echo bpe_get_user_venue_events_feed_link();
}
	function bpe_get_user_venue_events_feed_link()
	{
		return apply_filters( 'bpe_get_user_venue_events_feed_link', bp_displayed_user_domain() . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'venue_slug' ) .'/'. bp_action_variable( 0 ) .'/'. bpe_get_option( 'feed_slug' ) .'/' );
	}
?>