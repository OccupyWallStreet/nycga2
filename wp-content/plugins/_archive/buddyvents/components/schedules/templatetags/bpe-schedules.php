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

class Buddyvents_Schedule_Template
{
	var $current_schedule = -1;
	var $schedule_count;
	var $schedules;
	var $schedule;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_schedule_count;

	function __construct( $event_id, $day, $start, $end, $page, $per_page, $max, $search_terms )
	{
		$this->pag_page = isset( $_REQUEST['spage'] ) ? intval( $_REQUEST['spage'] 	) : $page;
		$this->pag_num  = isset( $_REQUEST['num'] 	) ? intval( $_REQUEST['num'] 	) : $per_page;

		$this->schedules = bpe_get_schedules( array( 'event_id' => $event_id, 'day' => $day, 'start' => $start, 'end' => $end, 'per_page' => $this->pag_num, 'page' => $this->pag_page, 'search_terms' => $search_terms ) );

		if( ! $max || $max >= (int)$this->schedules['total'] )
			$this->total_schedule_count = (int)$this->schedules['total'];
		else
			$this->total_schedule_count = (int)$max;

		$this->schedules = $this->schedules['schedules'];

		if( $max )
		{
			if( $max >= count( $this->schedules ) )
				$this->schedule_count = count( $this->schedules );
			else
				$this->schedule_count = (int)$max;
		}
		else
			$this->schedule_count = count( $this->schedules );
		
		$this->pag_links = paginate_links( array(
			'base' 		=> add_query_arg( array( 'spage' => '%#%' ) ),
			'format' 	=> '',
			'total' 	=> ceil( $this->total_schedule_count / $this->pag_num ),
			'current' 	=> $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' 	=> 3
		));
	}

	function has_schedules()
	{
		if( $this->schedule_count )
			return true;

		return false;
	}

	function next_schedule()
	{
		$this->current_schedule++;
		$this->schedule = $this->schedules[$this->current_schedule];

		return $this->schedule;
	}

	function rewind_schedules()
	{
		$this->current_schedule = -1;
		
		if ( $this->schedule_count > 0 )
		{
			$this->schedule = $this->schedules[0];
		}
	}

	function schedules()
	{
		if ( $this->current_schedule + 1 < $this->schedule_count )
		{
			return true;
		}
		elseif( $this->current_schedule + 1 == $this->schedule_count )
		{
			do_action( 'bpe_schedule_loop_end' );
			$this->rewind_schedules();
		}

		$this->in_the_loop = false;
		return false;
	}

	function the_schedule()
	{
		$this->in_the_loop = true;
		$this->schedule = $this->next_schedule();

		if( $this->current_schedule == 0 )
			do_action( 'bpe_schedule_loop_start' );
	}

}

function bpe_has_schedules( $args = '' )
{
	global $schedule_template;

	$search_terms = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
	
	$defaults = array(
		'event_id' 		=> bpe_get_displayed_event( 'id' ),
		'day' 			=> false,
		'start' 		=> false,
		'end' 			=> false,
		'page' 			=> 1,
		'per_page' 		=> 50,
		'max' 			=> false,
		'search_terms' 	=> $search_terms
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	$schedule_template = new Buddyvents_Schedule_Template( (int)$event_id, $day, $start, $end, (int)$page, (int)$per_page, (int)$max, $search_terms );
	return apply_filters( 'bpe_has_schedules', $schedule_template->has_schedules(), &$schedule_template );
}

function bpe_schedules()
{
	global $schedule_template;

	return $schedule_template->schedules();
}

function bpe_the_schedule()
{
	global $schedule_template;

	return $schedule_template->the_schedule();
}

function bpe_get_schedules_count()
{
	global $schedule_template;

	return $schedule_template->schedule_count;
}

function bpe_get_total_schedules_count()
{
	global $schedule_template;

	return $schedule_template->total_schedule_count;
}

/**
 * Pagination links
 * @since 1.5
 */
function bpe_schedules_pagination_links()
{
	echo bpe_get_schedules_pagination_links();
}
	function bpe_get_schedules_pagination_links()
	{
		global $schedule_template;
	
		if( ! empty( $schedule_template->pag_links ) )
			return sprintf( __( 'Page: %s', 'events' ), $schedule_template->pag_links );
	}

/**
 * Pagination count
 * @since 1.5
 */
function bpe_schedules_pagination_count()
{
	echo bpe_get_schedules_pagination_count();
}
	function bpe_get_schedules_pagination_count()
	{
		global $bp, $schedule_template;
	
		$from_num = bp_core_number_format( intval( ( $schedule_template->pag_page - 1 ) * $schedule_template->pag_num ) + 1 );
		$to_num = bp_core_number_format( ( $from_num + ( $schedule_template->pag_num - 1 ) > $schedule_template->total_schedule_count ) ? $schedule_template->total_schedule_count : $from_num + ( $schedule_template->pag_num - 1 ) );
		$total = bp_core_number_format( $schedule_template->total_schedule_count );
	
		return apply_filters( 'bpe_get_schedules_pagination_count', sprintf( __( 'Viewing schedule %1$s to %2$s (of %3$s schedules)', 'schedules' ), $from_num, $to_num, $total ) );
	}

/**
 * Schedule id
 * @since 1.5
 */
function bpe_schedule_id( $s = false )
{
	echo bpe_get_schedule_id( $s );
}
	function bpe_get_schedule_id( $s = false )
	{
		global $schedule_template;

		$schedule = ( isset( $schedule_template->schedule ) && empty( $s ) ) ? $schedule_template->schedule : $s;

		if( ! isset( $schedule->id ) )
			return false;

		return apply_filters( 'bpe_get_schedule_id', $schedule->id, $schedule );
	}

/**
 * Schedule event_id
 * @since 1.5
 */
function bpe_schedule_event_id( $s = false )
{
	echo bpe_get_schedule_event_id( $s );
}
	function bpe_get_schedule_event_id( $s = false )
	{
		global $schedule_template;

		$schedule = ( isset( $schedule_template->schedule ) && empty( $s ) ) ? $schedule_template->schedule : $s;

		if( ! isset( $schedule->event_id ) )
			return false;

		return apply_filters( 'bpe_get_schedule_event_id', $schedule->event_id, $schedule );
	}

/**
 * Schedule day
 * @since 1.5
 */
function bpe_schedule_day( $s = false )
{
	echo bpe_get_schedule_day( $s );
}
	function bpe_get_schedule_day( $s = false )
	{
		return apply_filters( 'bpe_get_schedule_day', mysql2date( bpe_get_option( 'date_format' ), bpe_get_schedule_day_raw( $s ) ), $schedule );
	}

/**
 * Schedule day raw
 * @since 1.5
 */
function bpe_schedule_day_raw( $s = false )
{
	echo bpe_get_schedule_day_raw( $s );
}
	function bpe_get_schedule_day_raw( $s = false )
	{
		global $schedule_template;

		$schedule = ( isset( $schedule_template->schedule ) && empty( $s ) ) ? $schedule_template->schedule : $s;

		if( ! isset( $schedule->day ) )
			return false;

		return apply_filters( 'bpe_get_raw_schedule_day', $schedule->day, $schedule );
	}

/**
 * Schedule start
 * @since 1.5
 */
function bpe_schedule_start( $s = false )
{
	echo bpe_get_schedule_start( $s );
}
	function bpe_get_schedule_start( $s = false )
	{
		global $schedule_template, $bpe;

		$schedule = ( isset( $schedule_template->schedule ) && empty( $s ) ) ? $schedule_template->schedule : $s;

		if( ! isset( $schedule->start ) )
			return false;

		$time = ( bpe_get_option( 'clock_type' ) == 24 ) ? gmdate( 'H:i', strtotime( $schedule->start ) ) : gmdate( 'g:i a', strtotime( $schedule->start ) );

		return apply_filters( 'bpe_get_schedule_start', $time, $schedule );
	}

/**
 * Schedule end
 * @since 1.5
 */
function bpe_schedule_end( $s = false )
{
	echo bpe_get_schedule_end( $s );
}
	function bpe_get_schedule_end( $s = false )
	{
		global $schedule_template, $bpe;

		$schedule = ( isset( $schedule_template->schedule ) && empty( $s ) ) ? $schedule_template->schedule : $s;

		$time = ( bpe_get_option( 'clock_type' ) == 24 ) ? gmdate( 'H:i', strtotime( $schedule->end ) ) : gmdate( 'g:i a', strtotime( $schedule->end ) );
		
		if( ! empty( $schedule->end ) )
			return apply_filters( 'bpe_get_schedule_end', ' - '. $time, $schedule );
	}

/**
 * Schedule start raw
 * @since 1.5
 */
function bpe_schedule_start_raw( $s = false )
{
	echo bpe_get_schedule_start_raw( $s );
}
	function bpe_get_schedule_start_raw( $s = false )
	{
		global $schedule_template;

		$schedule = ( isset( $schedule_template->schedule ) && empty( $s ) ) ? $schedule_template->schedule : $s;

		if( ! isset( $schedule->start ) )
			return false;

		return apply_filters( 'bpe_get_raw_schedule_start', gmdate( 'H:i', strtotime( $schedule->start ) ), $schedule );
	}

/**
 * Schedule end raw
 * @since 1.5
 */
function bpe_schedule_end_raw( $s = false )
{
	echo bpe_get_schedule_end_raw( $s );
}
	function bpe_get_schedule_end_raw( $s = false )
	{
		global $schedule_template;

		$schedule = ( isset( $schedule_template->schedule ) && empty( $s ) ) ? $schedule_template->schedule : $s;

		if( ! isset( $schedule->end ) )
			return false;

		return apply_filters( 'bpe_get_raw_schedule_end', gmdate( 'H:i', strtotime( $schedule->end ) ), $schedule );
	}

/**
 * Schedule description
 * @since 1.5
 */
function bpe_schedule_description( $s = false )
{
	echo bpe_get_schedule_description( $s );
}
	function bpe_get_schedule_description( $s = false )
	{
		return apply_filters( 'bpe_events_get_schedule_description', bpe_get_schedule_description_raw( $s ), $schedule );
	}

/**
 * Schedule description raw
 * @since 1.5
 */
function bpe_schedule_description_raw( $s = false )
{
	echo bpe_get_schedule_description_raw( $s );
}
	function bpe_get_schedule_description_raw( $s = false )
	{
		global $schedule_template;

		$schedule = ( isset( $schedule_template->schedule ) && empty( $s ) ) ? $schedule_template->schedule : $s;

		if( ! isset( $schedule->description ) )
			return false;

		return apply_filters( 'bpe_get_raw_schedule_description', $schedule->description, $schedule );
	}
?>