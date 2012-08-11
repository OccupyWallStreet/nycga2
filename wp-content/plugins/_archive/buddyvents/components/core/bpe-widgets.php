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

class Buddyvents_Widget extends WP_Widget
{
	function __construct()
	{
		global $pagenow;

		$widget_ops = array( 'classname' => 'widget_events', 'description' => __( 'Display Buddyvents Events (try saying the last 2 words 10 times in a row really fast :)', 'events' ) );
		$this->WP_Widget( 'events', __( 'Events Widget', 'events' ), $widget_ops );
		
		if( is_admin() && $pagenow == 'widgets.php' )
			add_action( 'admin_enqueue_scripts', array( &$this, 'load_js_css' ) );
	}
	
	function load_js_css()
	{
		if( ! wp_script_is( 'jquery-ui-datepicker', 'registered' ) )
			wp_register_script( 'jquery-ui-datepicker', EVENT_URLPATH .'js/deprecated/datepicker.js', array( 'jquery' ), '1.0', true );

		wp_enqueue_style( 'bpe-datepicker-css', EVENT_URLPATH .'css/datepicker.css' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
	}

	function widget( $args, $instance )
	{
		global $wpdb;

		extract( $args );
			
		$title	= ( empty( $instance['title'] 	) ) ? __( 'Events', 'events' ) 	: $instance['title'];
		$group 	= ( empty( $instance['group'] 	) ) ? false 					: $instance['group'];
		$user 	= ( empty( $instance['user'] 	) ) ? false 					: $instance['user'];
		$max 	= ( empty( $instance['max'] 	) ) ? 3 						: $instance['max'];
		$ids 	= ( empty( $instance['ids'] 	) ) ? false 					: $wpdb->escape( $instance['ids'] );
		$cat 	= ( empty( $instance['cat'] 	) ) ? false 					: $instance['cat'];
		$begin 	= ( empty( $instance['begin'] 	) ) ? false 					: $instance['begin'];
		$end 	= ( empty( $instance['end'] 	) ) ? false 					: $instance['end'];
		$sort 	= ( empty( $instance['sort'] 	) ) ? false 					: $instance['sort'];
		$future = ( empty( $instance['future'] 	) ) ? true 						: $instance['future'];
		$past 	= ( empty( $instance['past'] 	) ) ? false 					: $instance['past'];
		
		if( empty( $begin ) || empty( $end ) )
			$begin = $end = false;

		if( $group == -1 )
		{
			$group = bp_get_current_group_id();
			if( empty( $group ) )
				return false;
		}

		if( $user == -1 )
		{
			$user = bp_displayed_user_id();
			
			if( empty( $user ) )
				return false;

			$name = bp_core_get_user_displayname( bp_displayed_user_id() );
			if( ! empty( $name ) )
			{
				if( in_array( substr( $name, -1 ), array( 's', 'z' ) ) )
					$name = $name ."'";
				else
					$name = $name ."'s";
			}
			
			$pre = ( bp_displayed_user_id() == bp_loggedin_user_id() ) ? __( 'My', 'events' ) : $name;
				
			$title = ( empty( $instance['title'] ) ) ? __( '{USER} Events', 'events' ) : str_replace( '{USER}', $pre, $instance['title'] );
		}
		
		$radius = false;
		if( isset( $instance['radius'] ) && $instance['radius'] > 0 )
		{
			$user = false;
			$radius = $instance['radius'];
		}
		
		if( bpe_has_events( apply_filters( 'bpe_widget_has_event_args', array( 'meta' => 'active', 'meta_key' => 'status', 'group_id' => $group, 'user_id' => $user, 'max' => $max, 'radius' => $radius, 'slug' => false, 'search_terms' => false, 'begin' => $begin, 'end' => $end, 'category' => $cat, 'sort' => $sort, 'ids' => $ids, 'future' => $future, 'past' => $past, 'spam' => 0 ), $instance ) ) ) :

			$create = '';
			if( is_user_logged_in() )
				$create = '<span class="widget-create-link"><a href="'. bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'create_slug' ) .'/">'. __( 'Create &rarr;', 'events' ) .'</a></span>';
		
			echo $before_widget;
			
			if ( $title)
				echo $before_title . $title . $create .  $after_title;
			?>
			<ul id="widget-events-list">
			
			<?php while ( bpe_events() ) : bpe_the_event(); ?>
            
            	<?php bpe_load_template( 'events/includes/widget' ); ?>
		
			<?php endwhile; ?>
			
			</ul>
		
			<?php
            echo $after_widget;
		endif;
	}

	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;
		
		$instance['title'] 	= wp_filter_kses( $new_instance['title'] );
		$instance['group'] 	= (int)$new_instance['group'];
		$instance['user'] 	= (int)$new_instance['user'];
		$instance['max'] 	= (int)$new_instance['max'];
		$instance['radius'] = (int)$new_instance['radius'];
		$instance['ids'] 	= wp_filter_kses( $new_instance['ids'] );
		$instance['cat'] 	= (int)$new_instance['cat'];
		$instance['begin'] 	= wp_filter_kses( $new_instance['begin'] );
		$instance['end'] 	= wp_filter_kses( $new_instance['end'] );
		$instance['sort'] 	= wp_filter_kses( $new_instance['sort'] );
		$instance['future'] = ( isset( $new_instance['future'] ) ) ? 1 : 0;
		$instance['past'] 	= ( isset( $new_instance['past'] ) ) ? 1 : 0;

		return $instance;
	}

	function form( $instance )
	{
		global $wpdb, $bpe, $bp;
		
		$categories = bpe_get_event_categories();
		$groups 	= $wpdb->get_results( $wpdb->prepare( "SELECT id, name FROM {$bp->groups->table_name} WHERE status = 'public'" ) );
		$users 		= $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT e.user_id, u.user_nicename FROM {$bpe->tables->events} e RIGHT JOIN {$wpdb->users} u ON u.ID = e.user_id AND u.user_status = 0" ) );

		$instance 	= wp_parse_args( (array) $instance, array( 'title' => 'Events', 'group' => false, 'user' => false, 'max' => 3, 'radius' => false, 'ids' => false, 'cat' => false, 'begin' => false, 'end' => false, 'sort' => 'end_date_asc', 'future' => true, 'past' => false ) );
		$title  	= esc_attr( $instance['title'] );
		$group  	= esc_attr( $instance['group'] );
		$user  		= esc_attr( $instance['user'] );
		$max  		= esc_attr( $instance['max'] );
		$radius  	= esc_attr( $instance['radius'] );
		$ids  		= esc_attr( $instance['ids'] );
		$cat  		= esc_attr( $instance['cat'] );
		$begin  	= esc_attr( $instance['begin'] );
		$end  		= esc_attr( $instance['end'] );
		$sort  		= esc_attr( $instance['sort'] );
		$future  	= (bool)$instance['future'];
		$past  		= (bool)$instance['past'];
		?>
		<p>
        	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'events' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
		<p>
        	<label for="<?php echo $this->get_field_id( 'group' ); ?>"><?php _e( 'Group:', 'events' ); ?></label><br />
            <select id="<?php echo $this->get_field_id( 'group' ); ?>" name="<?php echo $this->get_field_name( 'group' ); ?>">
                <option value="0">----</option>
                <option value="-1"><?php _e( 'Displayed Group', 'events' ); ?></option>
                <?php foreach( $groups as $key => $val ) { ?>
                    <option<?php if( $group == $val->id ) echo ' selected="selected"'; ?> value="<?php echo $val->id ?>"><?php echo $val->name ?></option>
                <?php } ?>
            </select>
        </p>
		<p>
        	<label for="<?php echo $this->get_field_id( 'user' ); ?>"><?php _e( 'User:', 'events' ); ?></label><br />
            <select id="<?php echo $this->get_field_id( 'user' ); ?>" name="<?php echo $this->get_field_name( 'user' ); ?>">
                <option value="0">----</option>
                <option value="-1"><?php _e( 'Displayed User', 'events' ); ?></option>
                <?php foreach( $users as $key => $val ) { ?>
                    <option<?php if( $user == $val->ID ) echo ' selected="selected"'; ?> value="<?php echo $val->ID ?>"><?php echo $val->user_nicename ?></option>
                <?php } ?>
            </select>
        </p>
		<p>
        	<label for="<?php echo $this->get_field_id( 'radius' ); ?>"><?php _e( 'Radius:', 'events' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'radius' ); ?>" name="<?php echo $this->get_field_name( 'radius' ); ?>" type="text" value="<?php echo $radius; ?>" />
            <br /><small><?php _e( 'If a numeric value is set, then the \'User\' field will be ignored.', 'events' ); ?></small>
        </p>
		<p>
        	<label for="<?php echo $this->get_field_id( 'max' ); ?>"><?php _e( 'Max:', 'events' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'max' ); ?>" name="<?php echo $this->get_field_name( 'max' ); ?>" type="text" value="<?php echo $max; ?>" />
        </p>
		<p>
        	<label for="<?php echo $this->get_field_id( 'ids' ); ?>"><?php _e( 'Comma separated event ids:', 'events' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'ids' ); ?>" name="<?php echo $this->get_field_name( 'ids' ); ?>" type="text" value="<?php echo $ids; ?>" />
        </p>
		<p>
        	<label for="<?php echo $this->get_field_id( 'cat' ); ?>"><?php _e( 'Category:', 'events' ); ?></label><br />
            <select id="<?php echo $this->get_field_id( 'cat' ); ?>" name="<?php echo $this->get_field_name( 'cat' ); ?>">
                <option value="">----</option>
                <?php foreach( $categories as $key => $val ) { ?>
                    <option<?php if( $cat == $val->id ) echo ' selected="selected"'; ?> value="<?php echo $val->id ?>"><?php echo $val->name ?></option>
                <?php } ?>
            </select>
        </p>
		<p>
        	<label for="<?php echo $this->get_field_id( 'begin' ); ?>"><?php _e( 'Begin:', 'events' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'begin' ); ?>" name="<?php echo $this->get_field_name( 'begin' ); ?>" type="text" value="<?php echo $begin; ?>" />

        	<label for="<?php echo $this->get_field_id( 'end' ); ?>"><?php _e( 'End:', 'events' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'end' ); ?>" name="<?php echo $this->get_field_name( 'end' ); ?>" type="text" value="<?php echo $end; ?>" />
        </p>
		<p>
        	<label for="<?php echo $this->get_field_id( 'future' ); ?>">
            	<input id="<?php echo $this->get_field_id( 'future' ); ?>" name="<?php echo $this->get_field_name( 'future' ); ?>" type="checkbox"<?php if( $future == true ) echo ' checked="checked"'; ?> value="1" />
                <?php _e( 'Show only future events', 'events' ); ?>
            </label>
        </p>
		<p>
        	<label for="<?php echo $this->get_field_id( 'past' ); ?>">
            	<input id="<?php echo $this->get_field_id( 'past' ); ?>" name="<?php echo $this->get_field_name( 'past' ); ?>" type="checkbox"<?php if( $past == true ) echo ' checked="checked"'; ?> value="1" />
				<?php _e( 'Show only past events', 'events' ); ?>
            </label>
        </p>
		<p>
        	<label for="<?php echo $this->get_field_id( 'sort' ); ?>"><?php _e( 'Sort:', 'events' ); ?></label><br />
            <select id="<?php echo $this->get_field_id( 'sort' ); ?>" name="<?php echo $this->get_field_name( 'sort' ); ?>">
            	<option value="end_date_asc"><?php _e( 'By end_date ASC', 'events' ); ?></option>
            	<option value="end_date_desc"><?php _e( 'By end_date DESC', 'events' ); ?></option>
            	<option value="start_date_asc"><?php _e( 'By start_date ASC', 'events' ); ?></option>
            	<option value="start_date_desc"><?php _e( 'By start_date DESC', 'events' ); ?></option>
            	<option value="date_created_asc"><?php _e( 'By date_created ASC', 'events' ); ?></option>
            	<option value="date_created_desc"><?php _e( 'By date_created DESC', 'events' ); ?></option>
            	<option value="random"><?php _e( 'Random', 'events' ); ?></option>
            </select>
        </p>
        
        <script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery(function() {
				var dates = jQuery( "#<?php echo $this->get_field_id( 'begin' ); ?>,#<?php echo $this->get_field_id( 'end' ); ?>" ).datepicker({
					firstDay: <?php echo ( bpe_get_option( 'week_start' ) == 1 ) ? 1 : 0; ?>,
					minDate: '+1d',
					changeMonth: false,
					changeYear: false,
					dateFormat: "yy-mm-dd",
					onSelect: function( selectedDate ) {
						var option = this.id == "<?php echo $this->get_field_id( 'begin' ); ?>" ? "minDate" : "maxDate", instance = jQuery(this).data("datepicker");
						date = jQuery.datepicker.parseDate( instance.settings.dateFormat || jQuery.datepicker._defaults.dateFormat, selectedDate, instance.settings );
						dates.not(this).datepicker( "option", option, date );
					}
				});
			});
		});
		</script>
		<?php	
	}
}
add_action( 'widgets_init', create_function( '', 'return register_widget("Buddyvents_Widget");' ) );

/**
 * Events calendar widget
 * @since 1.4
 */
class Buddyvents_Calendar_Widget extends WP_Widget
{
	static $add_script;
 
	function __construct()
	{
		$widget_ops = array( 'classname' => 'bpe_widget_calendar', 'description' => __( 'A calendar of your events', 'events' ) );
		$this->WP_Widget( 'bpe_calendar', __( 'Events Calendar', 'events' ), $widget_ops );

 		add_action( 'wp_footer', array( &$this, 'print_scripts' ) );
	}
	
	function print_scripts()
	{
		if( ! self::$add_script )
			return;
 
		wp_print_scripts( 'bpe-general' );
	}

	function widget( $args, $instance )
	{
		self::$add_script = true;
		
		extract( $args );
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Events Calendar', 'events' ) : $instance['title'], $instance, $this->id_base );
		
		if( bpe_is_event_day_archive() )
		{
			$date = explode( '-', bp_action_variable( 0 ) );
			$month = $date[1];
			$year = $date[0];
		}
		elseif( bpe_is_event_month_archive() )
		{
			$month = bp_action_variable( 0 );
			$year = bp_action_variable( 1 );
		}
		else
		{
			$month = gmdate( 'm' );
			$year = gmdate( 'Y' );
		}
		
		$result = bpe_get_events( array( 'month' => $month, 'year' => $year, 'sort' => 'calendar', 'per_page' => false, 'future' => false, 'past' => false ) );
		$events = $result['events'];

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

			echo '<form id="cal_'. $widget_id .'">';
				echo '<input type="hidden" name="current_month" id="current_month_'. $widget_id .'" value="'. $month .'" />';
				echo '<input type="hidden" name="current_year" id="current_year_'. $widget_id .'" value="'. $year .'" />';
				echo '<div class="cal-widget-head">';
					echo '<a class="cal-widget-prev" title="'. __( 'Previous month', 'events' ) .'" href="#">&lt;&lt;</a>';
					echo '<span class="cal-widget-title"><a href="'. bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'month_slug' ) .'/'. $month .'/'. $year .'/">'. bpe_localize_month_name( $month, $year ) .' '. $year .'</a></span>';
					echo '<a class="cal-widget-next" title="'. __( 'Next month', 'events' ) .'" href="#">&gt;&gt;</a>';
				echo '</div>';
				
				echo bpe_draw_calendar( $month, $year, $events, 'widget' );
			echo '</form>';

		echo $after_widget;
	}

	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	function form( $instance )
	{
		$instance = wp_parse_args( (array)$instance, array( 'title' => __( 'Events Calendar', 'events' ) ) );
		$title = strip_tags( $instance['title'] );
		?>
		<p>
        	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'events' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    	</p>
		<?php
	}
}
add_action( 'widgets_init', create_function( '', 'return register_widget("Buddyvents_Calendar_Widget");' ) );
?>