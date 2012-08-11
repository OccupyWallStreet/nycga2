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
 * Auto discover event feeds
 *
 * @package	 Groups
 * @since 	 2.1.1
 */
function bpe_add_group_feeds_to_head()
{
	if( bp_is_group() ) : ?>
        <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php bp_current_group_name() ?> | <?php _e( 'Latest Group Events', 'events' ) ?>" href="<?php bpe_group_events_feed_link() ?>" />
		<?php if( bp_is_action_variable( bpe_get_option( 'category_slug' ), 0 ) && bp_action_variable( 1 ) ) : ?>
        	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php bp_current_group_name() ?> | <?php _e( 'Latest Category Events', 'events' ) ?>" href="<?php bpe_group_category_events_feed_link() ?>" />
        <?php endif; ?>
        <?php if( bp_is_action_variable( bpe_get_option( 'timezone_slug' ), 0 ) && bp_action_variable( 1 ) ) : ?>
        	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php bp_current_group_name() ?> | <?php _e( 'Latest Timezone Events', 'events' ) ?>" href="<?php bpe_group_timezone_events_feed_link() ?>" />
        <?php endif; ?>
        <?php if( bp_is_action_variable( bpe_get_option( 'venue_slug' ), 0 ) && bp_action_variable( 1 ) ) : ?>
        	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php bp_current_group_name() ?> | <?php _e( 'Latest Venue Events', 'events' ) ?>" href="<?php bpe_group_venue_events_feed_link() ?>" />
        <?php endif; ?>
    <?php endif;
}
add_action( 'wp_head', 'bpe_add_group_feeds_to_head' );

/**
 * Setup the group events feed
 *
 * @package	 Groups
 * @since 	 1.0
 */
function bpe_group_events_feed()
{
	global $wp_query;
	
	if( ! bp_is_action_variable( bpe_get_option( 'feed_slug' ), 0 ) )
		return false;

	if( bpe_is_groups() && bp_is_current_action( bpe_get_base( 'slug' ) ) )
	{
		$wp_query->is_404 = false;
		status_header( 200 );

		$group_name = bp_get_current_group_name();
	
		require( EVENT_ABSPATH .'components/core/bpe-feed-class.php' );
		
		$feed = new Buddyvents_Feeds();
		$feed->title = sprintf( __( '%s | Latest Events', 'events' ), $group_name );
		$feed->link = bpe_get_group_events_feed_link();
		$feed->description = sprintf( __( 'Latest Group Feed - %s', 'events' ), $group_name  );
		$feed->pubdate = mysql2date( 'D, d M Y H:i:s O', bpe_group_get_last_published(), false );
		$feed->context = 'group';
		$feed->query_args = array( 'public' => 1, 'sort' => 'start_date_asc', 'future' => true, 'past' => false, 'max' => 10 );
		$feed->create();

		exit;
	}
}
add_action( 'wp', 'bpe_group_events_feed', 0 );

/**
 * Setup the group categtory events feed
 *
 * @package	 Groups
 * @since 	 1.0
 */
function bpe_group_category_events_feed()
{
	global $wp_query;
	
	if( ! bp_is_action_variable( bpe_get_option( 'feed_slug' ), 2 ) )
		return false;

	if( bpe_is_groups() && bp_is_current_action( bpe_get_base( 'slug' ) ) && bp_is_action_variable( bpe_get_option( 'category_slug' ), 0 ) )
	{
		$wp_query->is_404 = false;
		status_header( 200 );

		$group_name = bp_get_current_group_name();
	
		require( EVENT_ABSPATH .'components/core/bpe-feed-class.php' );

		$cat = bpe_get_catid_from_slug( bp_action_variable( 1 ) );
	
		$feed = new Buddyvents_Feeds();
		$feed->title = sprintf( __( '%s | %s | Latest Events', 'events' ), $group_name, ucwords( bp_action_variable( 1 ) ) );
		$feed->link = bpe_get_group_category_events_feed_link();
		$feed->description = sprintf( __( 'Latest Group Category Feed - %s', 'events' ), $group_name );
		$feed->pubdate = mysql2date( 'D, d M Y H:i:s O', bpe_group_get_last_published( $cat ), false );
		$feed->context = 'group_category';
		$feed->query_args = array( 'public' => 1, 'category' => $cat, 'future' => true, 'past' => false, 'max' => 10 );
		$feed->create();

		exit;
	}
}
add_action( 'wp', 'bpe_group_category_events_feed', 0 );

/**
 * Setup the group timezone events feed
 *
 * @package	 Groups
 * @since 	 1.7
 */
function bpe_group_timezone_events_feed()
{
	global $wp_query;

	if( ! bpe_get_option( 'geonames_username' ) )
		return false;
	
	if( ! bp_is_action_variable( bpe_get_option( 'feed_slug' ), 2 ) )
		return false;

	if( bpe_is_groups() && bp_is_current_action( bpe_get_base( 'slug' ) ) && bp_is_action_variable( bpe_get_option( 'timezone_slug' ), 0 ) )
	{
		$wp_query->is_404 = false;
		status_header( 200 );

		$group_name = bp_get_current_group_name();
	
		require( EVENT_ABSPATH .'components/core/bpe-feed-class.php' );
		
		$feed = new Buddyvents_Feeds();
		$feed->title = sprintf( __( '%s | %s | Latest Events', 'events' ), $group_name, bpe_get_config( 'timezones', bp_action_variable( 1 ) ) );
		$feed->link = bpe_get_group_timezone_events_feed_link();
		$feed->description = sprintf( __( 'Latest Group Timezone Feed - %s', 'events' ), $group_name );
		$feed->pubdate = mysql2date( 'D, d M Y H:i:s O', bpe_group_get_last_published( false, bpe_get_config( 'timezones', bp_action_variable( 1 ) ) ), false );
		$feed->context = 'group_timezone';
		$feed->query_args = array( 'public' => 1, 'timezone' => bpe_get_config( 'timezones', bp_action_variable( 1 ) ), 'future' => true, 'past' => false, 'max' => 10 );
		$feed->create();

		exit;
	}
}
add_action( 'wp', 'bpe_group_timezone_events_feed', 0 );

/**
 * Setup the group venue events feed
 *
 * @package	 Groups
 * @since 	 1.7
 */
function bpe_group_venue_events_feed()
{
	global $wp_query;
	
	if( ! bp_is_action_variable( bpe_get_option( 'feed_slug'), 2 ) )
		return false;

	if( bpe_is_groups() && bp_is_current_action( bpe_get_base( 'slug' ) ) && bp_is_action_variable( bpe_get_option( 'venue_slug' ), 0 ) )
	{
		$wp_query->is_404 = false;
		status_header( 200 );

		$group_name = bp_get_current_group_name();
	
		require( EVENT_ABSPATH .'components/core/bpe-feed-class.php' );
		
		$feed = new Buddyvents_Feeds();
		$feed->title = sprintf( __( '%s | %s | Latest Events', 'events' ), $group_name, bpe_get_config( 'venues', bp_action_variable( 1 ) ) );
		$feed->link = bpe_get_group_timezone_events_feed_link();
		$feed->description = sprintf( __( 'Latest Group Venue Feed - %s', 'events' ), $group_name );
		$feed->pubdate = mysql2date( 'D, d M Y H:i:s O', bpe_group_get_last_published( false, bpe_get_config( 'venues', bp_action_variable( 1 ) ) ), false );
		$feed->context = 'group_venue';
		$feed->query_args = array( 'public' => 1, 'location' => bpe_get_config( 'venues', bp_action_variable( 1 ) ), 'future' => true, 'past' => false, 'max' => 10 );
		$feed->create();

		exit;
	}
}
add_action( 'wp', 'bpe_group_venue_events_feed', 0 );

/**
 * Display link according to the page we are on (group)
 *
 * @package	 Groups
 * @since 	 1.1
 */
function bpe_event_group_category_feed_links()
{
	if( bp_is_action_variable( bpe_get_option( 'category_slug' ), 0 ) && bp_action_variable( 1 ) )
		bpe_group_category_events_feed_link();

	elseif( bp_is_action_variable( bpe_get_option( 'timezone_slug' ), 0 ) && bp_action_variable( 1 ) )
		bpe_group_timezone_events_feed_link();

	elseif( bp_is_action_variable( bpe_get_option( 'venue_slug' ), 0 ) && bp_action_variable( 1 ) )
		bpe_group_venue_events_feed_link();
		
	else
		bpe_group_events_feed_link();
}

/**
 * Group feed link
 *
 * @package	 Groups
 * @since 	 1.0
 */
function bpe_group_events_feed_link()
{
	echo bpe_get_group_events_feed_link();
}
	function bpe_get_group_events_feed_link()
	{
		return apply_filters( 'bpe_get_group_events_feed_link', bp_get_group_permalink( groups_get_current_group() ) . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'feed_slug' ) .'/' );
	}

/**
 * Group category feed link
 *
 * @package	 Groups
 * @since 	 1.1
 */
function bpe_group_category_events_feed_link()
{
	echo bpe_get_group_category_events_feed_link();
}

	function bpe_get_group_category_events_feed_link()
	{
		return apply_filters( 'bpe_get_group_category_events_feed_link', bp_get_group_permalink( groups_get_current_group() ) . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'category_slug' ) .'/'. bp_action_variable( 1 ) .'/'. bpe_get_option( 'feed_slug' ) .'/' );
	}

/**
 * Group timezone feed link
 *
 * @package	 Groups
 * @since 	 1.7
 */
function bpe_group_timezone_events_feed_link()
{
	echo bpe_get_group_timezone_events_feed_link();
}
	function bpe_get_group_timezone_events_feed_link()
	{
		return apply_filters( 'bpe_get_group_timezone_events_feed_link', bp_get_group_permalink( groups_get_current_group() ) . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'timezone_slug' ) .'/'. bp_action_variable( 1 ) .'/'. bpe_get_option( 'feed_slug' ) .'/' );
	}

/**
 * Group venue feed link
 *
 * @package	 Groups
 * @since 	 1.7
 */
function bpe_group_venue_events_feed_link()
{
	echo bpe_get_group_venue_events_feed_link();
}
	function bpe_get_group_venue_events_feed_link()
	{
		return apply_filters( 'bpe_get_group_venue_events_feed_link', bp_get_group_permalink( groups_get_current_group() ) . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'venue_slug' ) .'/'. bp_action_variable( 1 ) .'/'. bpe_get_option( 'feed_slug' ) .'/' );
	}
?>