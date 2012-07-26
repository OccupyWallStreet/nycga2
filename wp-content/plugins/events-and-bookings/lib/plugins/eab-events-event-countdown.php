<?php
/*
Plugin Name: Event Countdown
Description: Generates a flexible countdown shortcode for the current or selected event. Visitor viewing the page can be redirected to any url when countdown expires. 
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 0.27
Author: Hakan Evin
*/

/*
Detail: Minimal usage: [event_countdown] <br /> Extended usage: [event_countdown id="1" event_id="3" format="dHMS" goto="http://example.com" class="countdown-class" type="flip" size="70" add="-120" expired="Too late!"]<br /><b>id</b> is a unique number that you should give when you are using more than one instance of the shortcode on a single page.<br /><b>event_id</b> is the ID of the Event that will be displayed. If omitted, countdown will be based on the current event, if it exists.<br /><b>format</b> is the countdown format of the output as defined in <a href="http://keith-wood.name/countdownRef.html" target="_blank">jQuery Countdown Reference</a>.<br /><b>goto</b> is the URL that the visitor <b>viewing</b> the page will be redirected to when countdown expires.<br /><b>class</b> is the name of the css wrapper class.<br /><b>type:</b> if entered "flip", creates a flip counter, like in airport terminals.<br /><b>size:</b> Width of a digit in px only for flip counter. Supported sizes are 70, 82, 127, 254.<br /><b>add:</b> How many minutes to add to the countdown. It can take negative values.<br /><b>expired:</b> You can enter a text that is displayed when countdown expires. Default is "Closed".

Where:
@id is a unique id. Only necessary and mandatory if more than one instance will be used on the same page. Default is null.

@event_id is optional and if it is given, countdown is calculated for the event having the event_id.
If it is not given, current event is taken into account (If shortcode is placed in its page) 

@format is the countdown format of the output as defined in http://keith-wood.name/countdown.html
e.g. "dHMS", which is the default, will countdown using days (unless it is not zero), hours, minutes and seconds. 
Lowercase means, that time part will be showed if not zero.
Uppercase means, that time part will always be displayed. 
As default, days will only be displayed when necessary, the rest will be shown even if they are zero.

@goto is the page that the visitor who is *viewing the page* will be redirected to when countdown expires. Default is null (No redirection).
Please note that this is not a permanent redirect; it is only applied to the visitor viewing the page at the time countdown expires.
Tip: Just to refresh the current page (e.g. for letting other plugins to redirect the visitor or cleaning the countdown) enter: window.location.href

@class is the name of the wrapper class. Default is null.

More Styling: Modify the css file events-and-bookings/js/jquery.countdown.css

@type: if entered "flip", creates a flip counter, like in airport terminals.

@size: Width of a digit in px only for flip counter. Supported sizes are 70, 82, 127, 254. Default is 70.
Note that if the content width is not wide enough, digits may overlap.

@add: How many minutes to add to the countdown. Default is naturally zero. It can take negative values.
For example, if you have a "Doors open time" of 2 hours before the event, enter -120 (=>2 hours) here.

Localization: Download the language pack from http://keith-wood.name/countdown.html and upload it in events-and-bookings/js/ folder.
Countdown will automatically switch to your local settings as defined in locale setting or WPLANG of wp-config.php. 
If this language javascript file does not exist, English will be used.
Note from wordpress.org: If you have a site network (Wordpress multisite), 
the language is set on a per-blog basis through the "Site language" option in the Settings->General subpanel.

*/

class Eab_Events_EventCountdown {

	/**
	 * Constructor
	 */	
	private function __construct () {
		$this->add_countdown = false;
	}

	/**
	 * Run the Addon
	 */	
	public static function serve () {
		$me = new Eab_Events_EventCountdown;
		$me->_add_hooks();
	}

	/**
	 * Hooks 
	 */	
	private function _add_hooks () {
		add_action( 'wp_enqueue_scripts', array( &$this, 'register_scripts') );
		add_shortcode( 'event_countdown', array(&$this, 'shortcode') );
		add_action( 'wp_footer', array(&$this, 'load_scripts_footer') );
		add_filter( 'the_posts', array(&$this, 'load_styles') );
	}

	/**
	 * Register jQuery countdown
	 */		
	function register_scripts() {
		wp_register_script('jquery-countdown',plugins_url('events-and-bookings/js/').'jquery.countdown.min.js',array('jquery','jquery-ui-widget'));
	}

	/**
	 * Load style only when they are necessary
	 * http://beerpla.net/2010/01/13/wordpress-plugin-development-how-to-include-css-and-javascript-conditionally-and-only-when-needed-by-the-posts/
	 */		
	function load_styles( $posts ) {
		if ( empty($posts) OR is_admin() ) 
			return $posts;
	
		$shortcode_found = false; // use this flag to see if styles and scripts need to be enqueued
		foreach ($posts as $post) {
			if (stripos($post->post_content, 'event_countdown') !== false) {
				$shortcode_found = true;
				break;
			}
		}
 
		if ($shortcode_found) 
			wp_enqueue_style('jquery-countdown',plugins_url('events-and-bookings/css/').'jquery.countdown.css');
 
		return $posts;
	}

	/**
	 * Load scripts to the footer only when they are necessary
	 */		
	function load_scripts_footer() {
		if ( $this->add_countdown ) {
			wp_enqueue_script('jquery-countdown');
				if ( $locale = $this->locale() )
			wp_enqueue_script('jquery-countdown-'.$locale,plugins_url('events-and-bookings/js/').'jquery.countdown-'.$locale.'.js',array('jquery-countdown'));
		}
	}
	
	/**
	 * Check if a localized countdown js file exists and locale settings match
	 */		
	function locale() {
		if ( !$locale = str_replace( "_", "-", get_locale() ) )
			return false;
		
		// First check with full match, e.g. zh-CN	
		if ( file_exists( WP_PLUGIN_DIR . "/events-and-bookings/js/jquery.countdown-".$locale.".js" ) )
			return $locale;
			
		// Then check the first abbr. e.g. zh
		list( $locale1, $locale2 ) = explode( "-", $locale );
		if ( file_exists( WP_PLUGIN_DIR . "/events-and-bookings/js/jquery.countdown-".$locale1.".js" ) )
			return $locale1;
			
		// No localized js file exists, use English
		return false;
	}

	/**
	 * Generate shortcode
	 */	
	function shortcode( $atts ) {

		extract( shortcode_atts( array(
		'id'		=> '',
		'event_id'	=> '',
		'format'	=> 'dHMS',
		'goto'		=> '',
		'class'		=> '',
		'type'		=> '',
		'size'		=> 70,
		'add'		=> 0,
		'expired'	=> __('Closed', Eab_EventsHub::TEXT_DOMAIN)
		), $atts ) );
		
		$this->add_countdown = true;
		
		global $wpdb, $post;

		$event_id = trim( $event_id );
		$id = str_replace( array(" ","'",'"'), "", $id ); // We cannot let spaces and quotes in id
		$goto = trim( $goto );
		
		if ( $event_id )
			$post_id = $event_id;
		else {	
			if ( !is_object( $post ) OR !$post->ID )
				return false; // This page does not support $post 
			
			$post_id = $post->ID;
		}
		
		if ( $class )
			$class = " class='".$class."'";
		
		// Do not add quotes for page refresh
		if ( $goto && $goto != "window.location.href" )
			$goto = "'". str_replace( array("'",'"'), "", $goto ). "'"; // Do not allow quotes which may break js
		
		switch ($size) {
			case 70:	$height = 72; break;
			case 82:	$height = 84; break;
			case 127:	$height = 130; break;
			case 254:	$height = 260; break;
			default:	$size = 70; $height = 72; break;
		}
		
		$sprite_file = plugins_url('/events-and-bookings/img/sprite_'.$size.'x'.$height.'.png');
		
		$result = $wpdb->get_row(
			"SELECT estart.* 
			FROM $wpdb->posts wposts, $wpdb->postmeta estart, $wpdb->postmeta eend, $wpdb->postmeta estatus
			WHERE 
			".$post_id."=wposts.ID AND wposts.ID=estart.post_id AND wposts.ID=eend.post_id AND wposts.ID=estatus.post_id 
			AND estart.meta_key='incsub_event_start' AND estart.meta_value > DATE_ADD(UTC_TIMESTAMP(),INTERVAL ". ( current_time('timestamp') - time() - 60 * abs($add) ). " SECOND)
			AND eend.meta_key='incsub_event_end' AND eend.meta_value > estart.meta_value
			AND estatus.meta_key='incsub_event_status' AND estatus.meta_value <> 'closed'
			AND wposts.post_type='incsub_event' AND wposts.post_status='publish'
			ORDER BY estart.meta_value ASC
			LIMIT 1
			");

			// Find how many seconds left to the event
		if ( $result == null ) 
			$secs = -1;
		else
			$secs = strtotime( $result->meta_value ) - current_time('timestamp') + 60 * (int)$add;
		
		$script  = '';
		$script .= "<script type='text/javascript'>";
		$script .= "jQuery(document).ready(function($) {";
		$script .= "$('#eab_event_countdown".$id."').countdown({
					format: '".$format."',
					expiryText: '".$expired."',
					until: ".$secs.",";
		if ( $goto )
			$script .= "onExpiry: eab_event_refresh".$id.",";
		if ( $type == 'flip' )
			$script .= "onTick: eab_event_update_flip".$id.",";
		$script .= "alwaysExpire: true
					});";
		if ( $goto )
			$script .= "function eab_event_refresh".$id."() {window.location.href=".$goto.";}";
		if ( $type == 'flip' ) {
			$script .= "function eab_event_update_flip".$id."(periods) {
						$(this).find('.countdown_amount').css('height','".$height."').css('width','".($size*2)."').css('display','inline-block');
						$(this).find('.countdown_amount').each(function(index) {
							var value = parseInt($(this).text());
							var tens = parseInt( value/10 );
							var ones = value - tens*10;
							$(this).empty();
							$(this).append('<span class=\'eab_event_flip_tens\'/><span class=\'eab_event_flip_ones\'/><div style=\'clear:both\'/>');
							$(this).find('span').css('background','url(".$sprite_file.") no-repeat' ).css('height','".$height."').css('width','".$size."').css('float','left').css('display','inline-block');
							$(this).find('.eab_event_flip_ones').css('background-position', '-'+(ones+1)*".$size."+'px 0');
							if ( tens < 1 ) {
								$(this).find('.eab_event_flip_tens').css('background-position', '0 0');
							}
							else{
								$(this).find('.eab_event_flip_tens').css('background-position', '-'+(tens+1)*".$size."+'px 0');
							}
						});
			}";
		}
		$script .= "});</script>";
		
		// remove line breaks to prevent wpautop break the script
		$script = str_replace( array("\r","\n","\t","<br>","<br />"), "", $script );
		
		
		
		return "<div id='eab_event_countdown".$id."'" . $class ."></div>". $script;
	}
}

Eab_Events_EventCountdown::serve();