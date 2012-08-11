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
 * [events] shortcode
 * 
 * @package	Core
 * @since 	2.0
 */
class Buddyvents_Shortcode
{
	static $add_script;
 
	function init()
	{
		add_shortcode( 'events', array( __CLASS__, 'handle_shortcode' ) );
 		add_action( 'wp_footer', array( __CLASS__, 'print_scripts' ) );
	}
 
	function handle_shortcode( $atts )
	{
		global  $eventloop_args;
		
		self::$add_script = true;
 
        $eventloop_args = shortcode_atts( apply_filters( 'bpe_eventloop_shortcode_args', array(
			'ids' 			=> false,	'user_id' 	=> false,
			'group_id' 		=> false,	'name' 		=> false,
			'start_date'	=> false,	'max' 		=> false,
			'start_time'	=> false,	'end_date' 	=> false,
			'end_time' 		=> false,	'timezone' 	=> false,
			'day' 			=> false,	'month' 	=> false,
			'year' 			=> false,	'future' 	=> false,
			'past' 			=> false,	'location' 	=> false,
			'radius' 		=> false,	'sort' 		=> 'start_date_asc',
			'search_terms'	=> false,	'venue' 	=> false,
			'category'		=> false,	'meta' 		=> 'active',
			'meta_key'		=> 'status','operator' 	=> '=',
			'begin'			=> false,	'end' 		=> false,
			'per_page'		=> bpe_get_view_per_page(),
        ) ), $atts );
    
		ob_start();
		echo '<div class="event-wrapper">';
		bpe_load_template( 'events/includes/loop' );
		echo '</div>';
		$out = ob_get_contents();
		ob_end_clean();
            
        return $out;
	}
 
	function print_scripts()
	{
		if( ! self::$add_script )
			return;
 
		wp_print_scripts( array( 'bpe-general', 'colorbox', 'bpe-maps-js' ) );
	}
} 
Buddyvents_Shortcode::init();

/**
 * [eventcal] shortcode
 * 
 * @package	Core
 * @since 	2.0
 */
class Buddyvents_Cal_Shortcode
{
	static $add_script;
	static $year;
	static $month;
 
	function init()
	{
		add_shortcode( 'eventcal', array( __CLASS__, 'handle_shortcode' ) );
		add_action( 'wp_footer',   array( __CLASS__, 'print_scripts'    ) );
	}
 
	function handle_shortcode( $atts )
	{
		self::$add_script = true;

        extract( shortcode_atts( array(
			'month' => ( date( 'n' ) - 1 ),
			'year' => date( 'Y' ),
        ), $atts ) );
 
		self::$year = $year;
		self::$month = $month;

		ob_start();
		bpe_calendar( zeroise( $month, 2 ), $year );
		$out = ob_get_contents();
		ob_end_clean();
		
		return $out;
	}
 
	function print_scripts()
	{
		if( ! self::$add_script )
			return;
			
		$scripts = array( 'colorbox', 'bpe-maps-js' );
 
 		if( bpe_get_option( 'use_fullcalendar' ) === true )
			$scripts[] = 'bpe-fullcalendar-js';
		
		wp_print_scripts( $scripts );
		
		if( bpe_get_option( 'use_fullcalendar' ) === true ) :
			echo '<script type="text/javascript">'."\n";
			echo 'jQuery(document).ready(function() {'."\n";
				echo 'calendar.fullCalendar("gotoDate",'. self::$year .','. ( self::$month - 1 ) .');'."\n";
			echo '});'."\n";
			echo '</script>'."\n";
		endif;
	}
} 
Buddyvents_Cal_Shortcode::init();
?>