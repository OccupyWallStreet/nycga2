<?php
/*
Plugin Name: Next Event Shortcode
Description: Generates a formattable shortcode displaying time of the next upcoming event that has not started yet 
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 0.27
Author: Hakan Evin
*/

/*
Detail: Minimal usage: [next_event] <br />Extended Usage: [next_event format="H:i T l" class="next-event-class" add="-120" expired="Too late!"] <br /><b>format</b> is time format. For details see <a href="http://php.net/manual/en/function.date.php" target="_blank">PHP Date Function</a>.<br /><b>class</b> is the name of the css class that will be applied to the wrapper.<br /><b>add</b> is number of minutes that will be added to the time. It can be negative. Addition does not affect the expiry time of the event.<br /><b>expired:</b> You can enter a text that is displayed when countdown expires. Default is "Closed".

Explanation:
@format is the time format of the output as defined in http://php.net/manual/en/function.date.php
e.g. "H:i T l", which is the default, will give something like 16:30 EST Wednesday
Note: You may need to use date_default_timezone_set function if your timezone are not displayed correctly.
Tip: If you want to make a special format using your own characters, place \\ in front of them.
For example: "H:i \\B\\l\\a l", will give something like 16:30 Bla Wednesday

@class is the name of css class that will be applied to the output. Default is null.

@add: How many minutes to add to the displayed time. Default is naturally zero. It can take negative values.
For example, if you have a "Doors open time" of 2 hours before the event, enter -120 (=>2 hours) here.

*/

class Eab_Events_NextEventShortcode {

	private $_data;

	/**
	 * Constructor
	 */	
	private function __construct () {
	}

	/**
	 * Run the Addon
	 *
	 */	
	public static function serve () {
		$me = new Eab_Events_NextEventShortcode;
		$me->_add_hooks();
	}

	/**
	 * Hooks 
	 *
	 */	
	private function _add_hooks () {
		add_shortcode( 'next_event', array(&$this, 'shortcode') );
	}

	/**
	 * Generate shortcode
	 *
	 */	
	function shortcode( $atts ) {
	
		extract( shortcode_atts( array(
		'format'=> 'H:i T l',
		'class'	=> '',
		'add'	=> 0,
		'expired'	=> __('Closed', Eab_EventsHub::TEXT_DOMAIN)
		), $atts ) );
		
		if ( $class )
			$class = " class='".$class."'";
		
		global $wpdb;
		
		$result = $wpdb->get_row(
			"SELECT estart.* 
			FROM $wpdb->posts wposts, $wpdb->postmeta estart, $wpdb->postmeta eend, $wpdb->postmeta estatus
			WHERE 
			wposts.ID=estart.post_id AND wposts.ID=eend.post_id AND wposts.ID=estatus.post_id 
			AND estart.meta_key='incsub_event_start' AND estart.meta_value > DATE_ADD(UTC_TIMESTAMP(),INTERVAL ". ( current_time('timestamp') - time() ). " SECOND)
			AND eend.meta_key='incsub_event_end' AND eend.meta_value > estart.meta_value
			AND estatus.meta_key='incsub_event_status' AND estatus.meta_value <> 'closed'
			AND wposts.post_type='incsub_event' AND wposts.post_status='publish'
			ORDER BY estart.meta_value ASC
			LIMIT 1
			");
		
		$secs = 0;
		if ( $add )
			$secs = 60 * (int)$add;
		
		if ( $result != null )
			$out = '<div'.$class.'>'. date( $format, strtotime( $result->meta_value, current_time('timestamp') ) + $secs ) . "</div>";
		else
			$out = '<div'.$class.'>'. $expired . "</div>";
			
		return $out;
	}
}

Eab_Events_NextEventShortcode::serve();