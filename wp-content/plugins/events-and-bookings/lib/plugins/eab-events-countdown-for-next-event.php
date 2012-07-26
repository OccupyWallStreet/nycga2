<?php
/*
Plugin Name: Next Event Countdown
Description: Generates a flexible countdown shortcode for the next upcoming event that has not started yet. Visitor viewing the page can be redirected to any url when countdown expires. 
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 0.27
Author: Hakan Evin
*/

/*
Detail: Minimal usage: [next_event_countdown]<br />Extended Usage: [next_event_countdown id="1" format="dHMS" goto="http://example.com" class="countdown-class" type="flip" size="70" add="-120" expired="Too late!"]<br />For explanation of the parameters, please see Event Countdown.


Minimal usage:
[next_event_countdown]

Extended Usage:
[next_event_countdown id="1" format="dHMS" goto="http://example.com" class="countdown-class" type="flip" size="70" add="-120" expired="Expired"]
Where:
@id is a unique id. Only necessary and mandatory if more than one instance will be used on the same page. Default is null.

@format is the countdown format of the output as defined in http://keith-wood.name/countdown.html
e.g. "dHMS", which is the default, will countdown using days (unless it is not zero), hours, minutes and seconds. 
Lowercase means, that time part will be showed if not zero.
Uppercase means, that time part will always be displayed. 
As default, days will only be displayed when necessary, the rest will be shown even if they are zero.

@goto is the page that visitor will be redirected to when countdown expires. Default is null (No redirection).
Tip: Just to refresh the current page (e.g. letting other plugins to redirect the visitor or cleaning the countdown) enter: window.location.href

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

class Eab_Events_CountdownforNextEvent {

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
		$me = new Eab_Events_CountdownforNextEvent;
		$me->_add_hooks();
	}

	/**
	 * Hooks 
	 */	
	private function _add_hooks () {
		add_action( 'wp_enqueue_scripts', array( &$this, 'register_scripts') );
		add_shortcode( 'next_event_countdown', array(&$this, 'shortcode') );
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
			if (stripos($post->post_content, 'next_event_countdown') !== false) {
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
		'format'	=> 'dHMS',
		'goto'		=> '',
		'class'		=> '',
		'type'		=> '',
		'size'		=> 70,
		'add'		=> 0,
		'expired'	=> __('Closed', Eab_EventsHub::TEXT_DOMAIN)
		), $atts ) );
		
		$id = str_replace( array(" ","'",'"'), "", $id ); // We cannot let spaces and quotes in id
		$goto = trim( $goto );
		
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
		
		global $wpdb;
		
		$result = $wpdb->get_row(
			"SELECT estart.* 
			FROM $wpdb->posts wposts, $wpdb->postmeta estart, $wpdb->postmeta eend, $wpdb->postmeta estatus
			WHERE 
			wposts.ID=estart.post_id AND wposts.ID=eend.post_id AND wposts.ID=estatus.post_id 
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
		$script .= "$('#eab_next_event_countdown".$id."').countdown({
					format: '".$format."',
					expiryText: '".$expired."',
					until: ".$secs.",";
		if ( $goto )
			$script .= "onExpiry: eab_next_event_refresh".$id.",";
		if ( $type == 'flip' )
			$script .= "onTick: eab_next_event_update_flip".$id.",";
		$script .= "alwaysExpire: true
					});";
		if ( $goto )
			$script .= "function eab_next_event_refresh".$id."() {window.location.href=".$goto.";}";
		if ( $type == 'flip' ) {
			$script .= "function eab_next_event_update_flip".$id."(periods) {
						$(this).find('.countdown_amount').css('height','".$height."').css('width','".($size*2)."').css('display','inline-block');
						$(this).find('.countdown_amount').each(function(index) {
							var value = parseInt($(this).text());
							var tens = parseInt( value/10 );
							var ones = value - tens*10;
							$(this).empty();
							$(this).append('<span class=\'eab_event_flip_tens\'/><span class=\'eab_event_flip_ones\'/><div style=\'clear:both\'/>');
							$(this).find('span').css('background','url(".$sprite_file.") 0 0 no-repeat' ).css('height','".$height."').css('width','".$size."').css('float','left').css('display','inline-block');
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
		
		$this->add_countdown = true;
		
		return "<div id='eab_next_event_countdown".$id."'" . $class ."></div>". $script;
	}
}

Eab_Events_CountdownforNextEvent::serve();