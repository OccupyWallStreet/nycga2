<?php
class EM_Event_Posts_Admin{
	function init(){
		global $pagenow;
		if( $pagenow == 'edit.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] == EM_POST_TYPE_EVENT ){ //only needed for events list
			if( !empty($_REQUEST['category_id']) && is_numeric($_REQUEST['category_id']) ){
				$term = get_term_by('id', $_REQUEST['category_id'], EM_TAXONOMY_CATEGORY);
				if( !empty($term->slug) ){
					$_REQUEST['category_id'] = $term->slug;
				}
			}
			//hide some cols by default:
			$screen = 'edit-'.EM_POST_TYPE_EVENT;
			$hidden = get_user_option( 'manage' . $screen . 'columnshidden' );
			if( !$hidden ){
				$hidden = array('event-id');
				update_user_option(get_current_user_id(), "manage{$screen}columnshidden", $hidden, true);
			}
			//deal with actions
			$row_action_type = is_post_type_hierarchical( EM_POST_TYPE_EVENT ) ? 'page_row_actions' : 'post_row_actions';
			add_filter($row_action_type, array('EM_Event_Posts_Admin','row_actions'),10,2);
			add_action('admin_head', array('EM_Event_Posts_Admin','admin_head'));
			//collumns
			add_filter('manage_edit-'.EM_POST_TYPE_EVENT.'_columns' , array('EM_Event_Posts_Admin','columns_add'));
			add_filter('manage_'.EM_POST_TYPE_EVENT.'_posts_custom_column' , array('EM_Event_Posts_Admin','columns_output'),10,2 );
			//TODO alter views of locations, events and recurrences, specifically find a good way to alter the wp_count_posts method to force user owned posts only
			//add_filter('views_edit-'.EM_POST_TYPE_EVENT, array('EM_Event_Posts_Admin','views'),10,1);
		}
		add_action('restrict_manage_posts', array('EM_Event_Posts_Admin','restrict_manage_posts'));
	}
	
	function admin_head(){
		//quick hacks to make event admin table make more sense for events
		?>
		<script type="text/javascript">
			jQuery(document).ready( function($){
				$('.inline-edit-date').prev().css('display','none').next().css('display','none').next().css('display','none');
				$('.em-detach-link').click(function( event ){
					if( !confirm(EM.event_detach_warning) ){
						event.preventDefault();
						return false;
					}
				});
				$('.em-delete-recurrence-link').click(function( event ){
					if( !confirm(EM.delete_recurrence_warning) ){
						event.preventDefault();
						return false;
					}
				});
			});
		</script>
		<style>
			table.fixed{ table-layout:auto !important; }
			.tablenav select[name="m"] { display:none; }
		</style>
		<?php
	}
	
	function restrict_manage_posts(){
		global $wp_query;
		if( $wp_query->query_vars['post_type'] == EM_POST_TYPE_EVENT || $wp_query->query_vars['post_type'] == 'event-recurring' ){
			?>
			<select name="scope">
				<?php
				$scope = (!empty($wp_query->query_vars['scope'])) ? $wp_query->query_vars['scope']:'future';
				foreach ( em_get_scopes() as $key => $value ) {
					$selected = "";
					if ($key == $scope)
						$selected = "selected='selected'";
					echo "<option value='$key' $selected>$value</option>  ";
				}
				?>
			</select>
			<?php
			if( get_option('dbem_categories_enabled') ){
				//Categories
	            $selected = !empty($_GET['event-categories']) ? $_GET['event-categories'] : 0;
				wp_dropdown_categories(array( 'hide_empty' => 1, 'name' => 'event-categories',
                              'hierarchical' => true, 'id' => EM_TAXONOMY_CATEGORY,
                              'taxonomy' => EM_TAXONOMY_CATEGORY, 'selected' => $selected,
                              'show_option_all' => __('View all categories')));
			}
            if( !empty($_REQUEST['author']) ){
            	?>
            	<input type="hidden" name="author" value="<?php echo $_REQUEST['author'] ?>" />
            	<?php            	
            }
		}
	}
	
	function views($views){
		if( !current_user_can('edit_others_events') ){
			//alter the views to reflect correct numbering
			 
		}
		return $views;
	}
	
	function columns_add($columns) {
		if( array_key_exists('cb', $columns) ){
			$cb = $columns['cb'];
	    	unset($columns['cb']);
	    	$id_array = array('cb'=>$cb, 'event-id' => sprintf(__('%s ID','dbem'),__('Event','dbem')));
		}else{
	    	$id_array = array('event-id' => sprintf(__('%s ID','dbem'),__('Event','dbem')));
		}
	    unset($columns['comments']);
	    unset($columns['date']);
	    unset($columns['author']);
	    $columns = array_merge($id_array, $columns, array(
	    	'location' => __('Location','dbem'),
	    	'date-time' => __('Date and Time','dbem'),
	    	'author' => __('Owner','dbem'),
	    	'extra' => ''
	    ));
	    if( !get_option('dbem_locations_enabled') ){
	    	unset($columns['location']);
	    }
	    return $columns;
	}
	
	function columns_output( $column ) {
		global $post, $EM_Event;
		$EM_Event = em_get_event($post, 'post_id');
		/* @var $post EM_Event */
		switch ( $column ) {
			case 'event-id':
				echo $EM_Event->event_id;
				break;
			case 'location':
				//get meta value to see if post has location, otherwise
				$EM_Location = $EM_Event->get_location();
				if( !empty($EM_Location->location_id) ){
					echo "<strong>" . $EM_Location->location_name . "</strong><br/>" . $EM_Location->location_address . " - " . $EM_Location->location_town;
				}else{
					echo __('None','dbem');
				}
				break;
			case 'date-time':
				//get meta value to see if post has location, otherwise
				$localised_start_date = date_i18n(get_option('date_format'), $EM_Event->start);
				$localised_end_date = date_i18n(get_option('date_format'), $EM_Event->end);
				echo $localised_start_date;
				echo ($localised_end_date != $localised_start_date) ? " - $localised_end_date":'';
				echo "<br />";
				if(!$EM_Event->event_all_day){
					echo date_i18n(get_option('time_format'), $EM_Event->start) . " - " . date_i18n(get_option('time_format'), $EM_Event->end);
				}else{
					echo get_option('dbem_event_all_day_message');
				}
				break;
			case 'extra':
				if( get_option('dbem_rsvp_enabled') == 1 && !empty($EM_Event->event_rsvp) && $EM_Event->can_manage('manage_bookings','manage_others_bookings')){
					?>
					<a href="<?php echo $EM_Event->get_bookings_url(); ?>"><?php echo __("Bookings",'dbem'); ?></a> &ndash;
					<?php _e("Booked",'dbem'); ?>: <?php echo $EM_Event->get_bookings()->get_booked_spaces()."/".$EM_Event->get_spaces(); ?>
					<?php if( get_option('dbem_bookings_approval') == 1 ): ?>
						| <?php _e("Pending",'dbem') ?>: <?php echo $EM_Event->get_bookings()->get_pending_spaces(); ?>
					<?php endif;
					echo ($EM_Event->is_recurrence()) ? '<br />':'';
				}
				if ( $EM_Event->is_recurrence() && $EM_Event->can_manage('edit_recurring_events','edit_others_recurring_events') ) {
					$recurrence_delete_confirm = __('WARNING! You will delete ALL recurrences of this event, including booking history associated with any event in this recurrence. To keep booking information, go to the relevant single event and save it to detach it from this recurrence series.','dbem');
					?>
					<strong>
					<?php echo $EM_Event->get_recurrence_description(); ?> <br />
					</strong>
					<div class="row-actions">
						<a href="<?php echo admin_url(); ?>post.php?action=edit&amp;post=<?php echo $EM_Event->get_event_recurrence()->post_id ?>"><?php _e ( 'Edit Recurring Events', 'dbem' ); ?></a> | <span class="trash"><a class="em-delete-recurrence-link" href="<?php echo get_delete_post_link($EM_Event->get_event_recurrence()->post_id); ?>"><?php _e('Delete','dbem'); ?></a></span> | <a class="em-detach-link" href="<?php echo $EM_Event->get_detach_url(); ?>"><?php _e('Detach', 'dbem'); ?></a>
					</div>
					<?php
				}
				
				break;
		}
	}
	
	function row_actions($actions, $post){
		if($post->post_type == EM_POST_TYPE_EVENT){
			global $post, $EM_Event;
			$EM_Event = em_get_event($post, 'post_id');
			$actions['duplicate'] = '<a href="'.admin_url().'edit.php?action=event_duplicate&amp;event_id='.$EM_Event->event_id.'&amp;_wpnonce='.wp_create_nonce('event_duplicate_'.$EM_Event->event_id).'" title="'.sprintf(__('Duplicate %s','dbem'), __('Event','dbem')).'">'.__('Duplicate','dbem').'</a>';
		}
		return $actions;
	}
}
add_action('admin_init', array('EM_Event_Posts_Admin','init'));

/*
 * Recurring Events
 */
class EM_Event_Recurring_Posts_Admin{
	function init(){
		global $pagenow;
		if( $pagenow == 'edit.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'event-recurring' ){
			//hide some cols by default:
			$screen = 'edit-'.EM_POST_TYPE_EVENT;
			$hidden = get_user_option( 'manage' . $screen . 'columnshidden' );
			if( !$hidden ){
				$hidden = array('event-id');
				update_user_option(get_current_user_id(), "manage{$screen}columnshidden", $hidden, true);
			}
			//notices			
			add_action('admin_notices',array('EM_Event_Recurring_Posts_Admin','admin_notices'));
			add_action('admin_head', array('EM_Event_Recurring_Posts_Admin','admin_head'));
			//collumns
			add_filter('manage_edit-event-recurring_columns' , array('EM_Event_Recurring_Posts_Admin','columns_add'));
			add_filter('manage_posts_custom_column' , array('EM_Event_Recurring_Posts_Admin','columns_output'),10,1 );
			add_action('restrict_manage_posts', array('EM_Event_Posts_Admin','restrict_manage_posts'));
		}
	}
	
	function admin_notices(){
		$warning = sprintf(__( 'Modifications to these events will cause all recurrences of each event to be deleted and recreated and previous bookings will be deleted! You can edit individual recurrences and detach them from recurring events by visiting the <a href="%s">events page</a>.', 'dbem' ), admin_url().'edit.php?post_type='.EM_POST_TYPE_EVENT);
		?><div class="updated"><p><?php echo $warning; ?></p></div><?php
	}
	
	function admin_head(){
		//quick hacks to make event admin table make more sense for events
		?>
		<script type="text/javascript">
			jQuery(document).ready( function($){
				$('.inline-edit-date').prev().css('display','none').next().css('display','none').next().css('display','none');
				if(!EM.recurrences_menu){
					$('#menu-posts-'+EM.event_post_type+', #menu-posts-'+EM.event_post_type+' > a').addClass('wp-has-current-submenu');
				}
			});
		</script>
		<style>
			table.fixed{ table-layout:auto !important; }
			.tablenav select[name="m"] { display:none; }
		</style>
		<?php
	}
	
	function columns_add($columns) {
		if( array_key_exists('cb', $columns) ){
			$cb = $columns['cb'];
	    	unset($columns['cb']);
	    	$id_array = array('cb'=>$cb, 'event-id' => sprintf(__('%s ID','dbem'),__('Event','dbem')));
		}else{
	    	$id_array = array('event-id' => sprintf(__('%s ID','dbem'),__('Event','dbem')));
		}
	    unset($columns['comments']);
	    unset($columns['date']);
	    unset($columns['author']);
	    return array_merge($id_array, $columns, array(
	    	'location' => __('Location'),
	    	'date-time' => __('Date and Time'),
	    	'author' => __('Owner','dbem'),
	    ));
	}

	
	function columns_output( $column ) {
		global $post, $EM_Event;
		if( $post->post_type == 'event-recurring' ){
			$post = $EM_Event = em_get_event($post);
			/* @var $post EM_Event */
			switch ( $column ) {
				case 'event-id':
					echo $EM_Event->event_id;
					break;
				case 'location':
					//get meta value to see if post has location, otherwise
					$EM_Location = $EM_Event->get_location();
					if( !empty($EM_Location->location_id) ){
						echo "<strong>" . $EM_Location->location_name . "</strong><br/>" . $EM_Location->location_address . " - " . $EM_Location->location_town;
					}else{
						echo __('None','dbem');
					}
					break;
				case 'date-time':
					echo $EM_Event->get_recurrence_description();
					break;
			}
		}
	}	
}
add_action('admin_init', array('EM_Event_Recurring_Posts_Admin','init'));