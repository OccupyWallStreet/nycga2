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

class Buddyvents_Group extends BP_Group_Extension
{	
	/**
	 * Initialize the groups component
	 *
	 * @package	 Groups
	 * @since 	 1.0
	 */
	function __construct()
	{
		global $bp;
		
		$group_id = bp_get_current_group_id();
		$active_count = bpe_get_event_count( 'active', $group_id, 'group' );
		$archive_count = bpe_get_event_count( 'archive', $group_id, 'group' );
		
		$this->name = sprintf( __( 'Events <span>%d</span>', 'events' ), $active_count + $archive_count );
		$this->slug = bpe_get_base( 'slug' );
		
		$this->nav_item_position = 50;
		$this->enable_create_step  = false;
		$this->enable_edit_item = false;
	}

	/**
	 * Display group events
	 *
	 * @package	 Groups
	 * @since 	 1.0
	 */
	function display()
	{
		bpe_load_template( 'events/group/navigation' );
		
		echo '<div id="events-dir-list">';
		
		$deactivated_tabs = bpe_get_option( 'deactivated_tabs' );

		if( ! bp_action_variable( 0 ) )
			$this->{bpe_get_option( 'default_tab' )}();

		elseif( bp_is_action_variable( bpe_get_option( 'active_slug' ), 0 ) && ! isset( $deactivated_tabs['active'] ) )
			$this->active();

		elseif( bp_is_action_variable( bpe_get_option( 'archive_slug' ), 0 ) && ! isset( $deactivated_tabs['archive'] ) )
			$this->archive();

		elseif( bp_is_action_variable( bpe_get_option( 'calendar_slug' ), 0 ) && ! isset( $deactivated_tabs['calendar'] ) )
			$this->calendar();

		elseif( bp_is_action_variable( bpe_get_option( 'map_slug' ), 0 ) && ! isset( $deactivated_tabs['map'] ) )
			$this->map();

		elseif( bp_is_action_variable( bpe_get_option( 'category_slug' ), 0 ) && bp_action_variable( 1 ) )
			$this->category();

		elseif( bp_is_action_variable( bpe_get_option( 'timezone_slug' ), 0 ) && bp_action_variable( 1 ) )
			$this->timezone();

		elseif( bp_is_action_variable( bpe_get_option( 'venue_slug' ), 0 ) && bp_action_variable( 1 ) )
			$this->venue();

		elseif( bp_is_action_variable( bpe_get_option( 'day_slug' ), 0 ) && bp_action_variable( 1 ) )
			$this->day();

		elseif( bp_is_action_variable( bpe_get_option( 'month_slug' ), 0 ) && bp_action_variable( 1 ) && bp_action_variable( 2 ) )
			$this->month();
		
		echo '</div>';
	}

	/**
	 * Archive template
	 *
	 * @package	 Groups
	 * @since 	 1.0
	 */
	function archive()
	{
		bpe_load_template( 'events/group/archive' );
	}
	
	/**
	 * Map template
	 *
	 * @package	 Groups
	 * @since 	 1.0
	 */
	function map()
	{
		bpe_load_template( 'events/group/map' );
	}
	
	/**
	 * Active template
	 *
	 * @package	 Groups
	 * @since 	 1.0
	 */
	function active()
	{
		bpe_load_template( 'events/group/home' );
	}

	/**
	 * Category template
	 *
	 * @package	 Groups
	 * @since 	 1.0
	 */
	function category()
	{
		bpe_load_template( 'events/group/category' );
	}

	/**
	 * Timezone template
	 *
	 * @package	 Groups
	 * @since 	 1.0
	 */
	function timezone()
	{
		bpe_load_template( 'events/group/timezone' );
	}

	/**
	 * Venue template
	 *
	 * @package	 Groups
	 * @since 	 1.0
	 */
	function venue()
	{
		bpe_load_template( 'events/group/venue' );
	}

	/**
	 * Day template
	 *
	 * @package	 Groups
	 * @since 	 1.0
	 */
	function day()
	{
		bpe_load_template( 'events/group/day' );
	}

	/**
	 * Month template
	 *
	 * @package	 Groups
	 * @since 	 1.0
	 */
	function month()
	{
		bpe_load_template( 'events/group/month' );
	}

	/**
	 * Calendar template
	 *
	 * @package	 Groups
	 * @since 	 1.0
	 */
	function calendar()
	{
		if( bpe_get_option( 'use_fullcalendar' ) === true ) :
			echo bpe_fullcalendar();

		else :
			$month = apply_filters( 'bpe_group_calendar_month_variable', (int)( ( bp_action_variable( 1 ) ) ? bp_action_variable( 1 ) : gmdate( 'm' ) ) );
			$month = ( $month < 10 ) ? '0'. $month : $month;
	
			$year = apply_filters( 'bpe_group_calendar_year_variable', (int)( ( bp_action_variable( 2 ) ) ? bp_action_variable( 2 ) : gmdate( 'Y' ) ) );
			
			$result = bpe_get_events( array( 'month' => $month, 'year' => $year, 'sort' => 'calendar', 'per_page' => false, 'group_id' => bp_get_current_group_id(), 'future' => false ) );
			$events = $result['events'];
		
			echo '<h4 class="cal-title"><a href="'. bp_get_group_permalink( groups_get_current_group() ) . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'month_slug' ) .'/'. $month .'/'. $year .'/">'. bpe_localize_month_name( $month, $year ) .' '. $year .'</a></h4>';
			echo bpe_calendar_controls( $month, $year );
			echo bpe_draw_calendar( $month, $year, $events, 'normal', 'group' );
			
			if( ! bpe_use_fullcalendar() ) : ?>
			<script type="text/javascript">
			jQuery(document).ready( function() {
				jQuery('form#cal-controls').submit(function() {
					var year = jQuery('#cal-controls #year').val();
					var month = jQuery('#cal-controls #month').val();
					window.location.href = '<?php echo bp_get_group_permalink( groups_get_current_group() ) . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'calendar_slug' ) ?>/'+ month +'/'+ year +'/';
					return false;
				});
			});
			</script>
			<?php
			endif;
		endif;
 	}
}
bp_register_group_extension( 'Buddyvents_Group' );
?>