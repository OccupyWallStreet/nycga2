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

class Buddyvents_Feeds
{
	/**
	 * Blog charset
	 */
	private $charset;

	/**
	 * RSS language
	 */
	private $language;

	/**
	 * Sitename
	 */
	private $sitename;

	/**
	 * Buddyvents generator
	 */
	private $generator;
	
	/**
	 * Feed title
	 */
	public $title;

	/**
	 * Feed link
	 */
	public $link;

	/**
	 * Feed description
	 */
	public $description;

	/**
	 * Publication date
	 */
	public $pubdate;

	/**
	 * Context
	 * 
	 * global, group, user, category, timezone, venue,
	 * group_category, group_timezone, group_venue,
	 * user_category, user_timezone or user_venue
	 */
	public $context;

	/**
	 * Events query args
	 */
	public $query_args;
	
	/**
	 * PHP5 Constructor
	 *
	 * @package	 Core
	 * @since 	 1.6
	 */
	public function __construct()
	{
		global $bpe;
		
		$this->charset   = get_blog_option( Buddyvents::$root_blog, 'blog_charset' );
		$this->language  = get_blog_option( Buddyvents::$root_blog, 'rss_language' );
		$this->sitename  = get_blog_option( Buddyvents::$root_blog, 'blogname' );
		$this->generator = 'http://shabushabu.eu/?buddyvents_version='. EVENT_VERSION;
	}
	
	/**
	 * Create a feed entry
	 *
	 * @package	 Core
	 * @since 	 1.6
	 */
	private function entry()
	{
        echo "\t".'<item>'."\n";
        echo "\t".'<guid>'. bpe_get_event_link() .'</guid>'."\n";
        echo "\t".'<title><![CDATA['. bpe_get_event_name() .']]></title>."\n"';
        echo "\t".'<link>'. bpe_get_event_link() .'</link>."\n"';
        echo "\t".'<pubDate>'. mysql2date('D, d M Y H:i:s O', bpe_event_date_created(), false) .'</pubDate>'."\n";
        echo "\t".'<description><![CDATA[';
		printf( __( '<strong>Venue</strong>: %s<br />', 'events' ), bpe_get_event_location_link() );

        if( bpe_is_all_day_event() ) :
			printf( __( '<strong>Start</strong>: %s (all day event)<br />', 'events' ), bpe_get_event_start_date() );
            if( bpe_get_event_start_date() != bpe_get_event_end_date() ) :
				printf( __( '<strong>End</strong>: %s (all day event)<br />', 'events' ), bpe_get_event_end_date() );
            endif;
        else :
			printf( __( '<strong>Start</strong>: %s at %s<br />', 'events' ), bpe_get_event_start_date(), bpe_get_event_start_time() );
			printf( __( '<strong>End</strong>: %s at %s<br />', 'events' ), bpe_get_event_end_date(), bpe_get_event_end_time() );
        endif;

        if( bpe_has_event_timezone() ) :
			printf( __( '<strong>Timezone</strong>: %s<br />', 'events' ), bpe_get_event_timezone() );
		endif;
        
		printf( __( '<strong>Category</strong>: %s<br />', 'events' ), bpe_get_event_category() );

        if( bpe_has_url() ) :
			printf( __( '<strong>Website</strong>: %s<br />', 'events' ), bpe_get_event_url() );
		endif;

		if( bpe_attached_to_group() ) :
            printf( __( '<strong>Group</strong>: <a href="%s">%s</a><br />', 'events' ), bpe_event_get_group_permalink(), bpe_event_get_group_name() );
        endif;
		
        printf( __( '<p><strong>Description</strong>:<br />%s</p>', 'events' ), bpe_get_event_description_raw() );
        echo ']]></description>'."\n";
		
		if( bpe_has_event_location() ) :
			echo "\t".'<georss:where>'."\n";
			echo "\t\t".'<gml:Point>'."\n";
			echo "\t\t\t".'<gml:pos>'. bpe_get_event_latitude() .' '. bpe_get_event_longitude() .'</gml:pos>'."\n";
			echo "\t\t".'</gml:Point>'."\n";
			echo "\t".'</georss:where>'."\n";
		endif;

        do_action( 'bpe_'. $this->context .'_feed_item' );
        echo "\t".'</item>'."\n";
	}

	/**
	 * Get the current URL we're on
	 *
	 * @package	 Core
	 * @since 	 1.6
	 */
	private function self_link()
	{
		$host = @parse_url( home_url() );
		$host = $host['host'];
		
		return esc_url( 'http'. ( ( isset( $_SERVER['https'] ) && $_SERVER['https'] == 'on' ) ? 's' : '' ) .'://'. $host . stripslashes( $_SERVER['REQUEST_URI'] ) );
	}

	/**
	 * Create the actual feed
	 *
	 * @package	 Core
	 * @since 	 1.6
	 */
	public function create()
	{
		header( 'Content-Type: text/xml; charset='. $this->charset, true );
		header( 'Status: 200 OK' );
		
		echo '<?xml version="1.0" encoding="'. $this->charset .'"?'.'>'."\n";
		echo '<rss version="2.0"
				xmlns:content="http://purl.org/rss/1.0/modules/content/"
				xmlns:wfw="http://wellformedweb.org/CommentAPI/"
				xmlns:dc="http://purl.org/dc/elements/1.1/"
				xmlns:atom="http://www.w3.org/2005/Atom"
				xmlns:georss="http://www.georss.org/georss"
				xmlns:gml="http://www.opengis.net/gml"';
        		do_action( 'bpe_'. $this->context .'_feed_head' );
        echo '>'."\n";
		echo '<channel>'."\n";

		echo "\t".'<title>'. sprintf( '%s | %s', $this->sitename, $this->title ) .'</title>'."\n";
		echo "\t".'<atom:link href="'. $this->self_link() .'" rel="self" type="application/rss+xml" />'."\n";
		echo "\t".'<link>'. $this->link .'</link>'."\n";
		echo "\t".'<description>'. sprintf( '%s | %s', $this->sitename, $this->description ) .'</description>'."\n";
		echo "\t".'<pubDate>'. $this->pubdate .'</pubDate>'."\n";
		echo "\t".'<generator>'. $this->generator .'</generator>'."\n";
		echo "\t".'<language>'. $this->language .'</language>'."\n";
        do_action( 'bpe_'. $this->context .'_feed_channel' );
		
		if( bpe_has_events( apply_filters( 'bpe_'. $this->context .'_feed_query_args', $this->query_args ) ) ) :
			while ( bpe_events() ) : bpe_the_event();
				$this->entry();
			endwhile;
		endif;
		
		echo '</channel>'."\n";
		echo '</rss>';
	}
}
?>