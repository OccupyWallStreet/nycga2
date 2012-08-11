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

class Buddyvents_iCal
{
	/**
	 * All available events for the iCal
	 */
	private $events;
	
	/**
	 * Holds the generated output
	 */
	private $output;

	/**
	 * Boolean value. Either echo or return
	 */
	private $echo;
	
	/**
	 * PHP5 Constructorr
	 *
	 * @package	 Core
	 * @since 	 1.6
	 */
	public function __construct( $events = array(), $echo = true )
	{
		if( empty( $events ) )
			return false;
		
		$this->events = $events;
		$this->echo = $echo;
		
		$this->process();
	}

	/**
	 * Format an event description
	 *
	 * @package	 Core
	 * @since 	 1.6
	 */
	private function process()
	{
		$this->output  = 'BEGIN:VCALENDAR'."\n";
		$this->output .= 'X-WR-CALNAME;VALUE=TEXT:'. get_bloginfo( 'name' ) ."\n";
		$this->output .= 'VERSION:2.0'."\n";
		$this->output .= 'PRODID:-//'. $this->domain() .'/'. bpe_get_base( 'root_slug' ) .'//NONSGML Buddyvents '. Buddyvents::VERSION .'//'."\n";
		$this->output .= 'METHOD:'. apply_filters( 'bpe_ical_method', 'PUBLISH' ) ."\n";
		$this->output .= 'CALSCALE:'. apply_filters( 'bpe_ical_scale', 'GREGORIAN' ) ."\n";

		foreach( $this->events as $event )
			$this->output .= $this->event( $event );

		$this->output .= 'END:VCALENDAR';
	}
   
	/**
	 * Setup an iCal event
	 *
	 * @package	 Core
	 * @since 	 1.6
	 */
	private function event( $event )
	{
		$vevent  = 'BEGIN:VEVENT'."\n";
		$vevent .= 'DTSTART';
		if( bpe_get_event_timezone_raw( $event ) )
			$vevent .= ';TZID='. $this->extract_tz_name( bpe_get_event_timezone_raw( $event ) );
		$vevent .= ':'. mysql2date( 'Ymd\THis\Z', bpe_get_event_start_date_raw( $event ) .' '. bpe_get_event_start_time_raw( $event ), false ) ."\n";
		$vevent .= 'DTSTAMP:'. mysql2date( 'Ymd\THis\Z', bpe_get_event_date_created( $event ), false ) ."\n";
		$vevent .= 'DESCRIPTION:'. $this->description( bpe_get_event_description_raw( $event ) ) ."\n";
		$vevent .= 'CLASS:'. ( ( bpe_get_event_public( $event ) == 1 ) ? 'PUBLIC' : 'PRIVATE' ) ."\n";
		$vevent .= 'LOCATION:'. str_replace( ',', '\,', bpe_get_event_location( $event ) ) ."\n";
		$vevent .= 'ORGANIZER;CN='. bp_core_get_user_displayname( bpe_get_event_user_id( $event ) ) .':MAILTO:'. bp_core_get_user_email( bpe_get_event_user_id( $event ) ) ."\n";
		$vevent .= 'UID:'. $this->unique_id( $event ) ."\n";
		$vevent .= 'DTEND';
		if( bpe_get_event_timezone_raw( $event ) )
			$vevent .= ';TZID='. $this->extract_tz_name( bpe_get_event_timezone_raw( $event ) );
		$vevent .= ':'. mysql2date( 'Ymd\THis\Z', bpe_get_event_end_date_raw( $event ) .' '. bpe_get_event_end_time_raw( $event ), false )."\n";
		$vevent .= 'SUMMARY:'. bpe_get_event_name( $event ) ."\n";
		$vevent .= 'BEGIN:VALARM'."\n";
		$vevent .= 'TRIGGER;VALUE=DURATION:'. apply_filters( 'bpe_ical_reminder_trigger', '-P1D' ) ."\n";
		$vevent .= 'ACTION:'. apply_filters( 'bpe_ical_reminder_action', 'DISPLAY' )."\n";
		$vevent .= 'DESCRIPTION:'. __( 'Event Reminder', 'events' ) ."\n";
		$vevent .= 'END:VALARM'."\n";
		$vevent .= 'END:VEVENT'."\n";
		
		return apply_filters( 'bpe_ical_single_event', $vevent );
	}
   
 	/**
	 * Get the timezone name
	 *
	 * @package	 Core
	 * @since 	 1.6
	 */
	private function extract_tz_name( $timezone )
	{
		$tz = explode( ' ', $timezone );
		return $tz[2];
	}
 
	/**
	 * Get a unique id for an event
	 *
	 * @package	 Core
	 * @since 	 1.6
	 */
	private function unique_id( $event )
	{
		return bpe_get_event_id( $event ) .'-'. md5( bpe_get_event_start_date_raw( $event ) . bpe_get_event_start_time_raw( $event ) . bpe_get_event_end_date_raw( $event ) . bpe_get_event_end_time_raw( $event ) ) .'@'. $this->domain();
	}
   
	/**
	 * Format an event description
	 *
	 * @package	 Core
	 * @since 	 1.6
	 */
	private function description( $desc )
	{
		$desc = stripslashes( $desc );
		$desc = normalize_whitespace( $desc );
		$desc = str_replace( "\n\n", '', $desc );
		$desc = str_replace( "\n", '', $desc );
	   
		return $desc;
	}
  
	/**
	 * Gets the raw domain
	 *
	 * @package	 Core
	 * @since 	 1.6
	 */
	private function domain()
	{
		$domain = str_replace( 'www.', '', bp_get_root_domain() );
		$domain = str_replace( 'https://', '', $domain );  
		$domain = str_replace( 'http://', '', $domain );
   
		return $domain; 
	}
   
	/**
	 * Runs when the class has run its course
	 *
	 * @package	 Core
	 * @since 	 1.6
	 */
	public function __destruct()
	{
		$this->output = apply_filters( 'bpe_ical_output', $this->output, $this->events );
		
		if( $this->echo === true )
			echo $this->output;
		else
			return $this->output;  
	}
}
?>