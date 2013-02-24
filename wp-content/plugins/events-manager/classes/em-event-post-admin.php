<?php
/*
 * Events Edit Page
 */
class EM_Event_Post_Admin{
	function init(){
		global $pagenow;
		if($pagenow == 'post.php' || $pagenow == 'post-new.php' ){ //only needed if editing post
			add_action('admin_head', array('EM_Event_Post_Admin','admin_head'));
			//Meta Boxes
			add_action('add_meta_boxes', array('EM_Event_Post_Admin','meta_boxes'));
			//Notices
			add_action('admin_notices',array('EM_Event_Post_Admin','admin_notices'));
		}
		//Save/Edit actions
		add_action('save_post',array('EM_Event_Post_Admin','save_post'),10,1);
		add_action('before_delete_post',array('EM_Event_Post_Admin','before_delete_post'),10,1);
		add_action('trashed_post',array('EM_Event_Post_Admin','trashed_post'),10,1);
		add_action('untrash_post',array('EM_Event_Post_Admin','untrash_post'),10,1);
		add_action('untrashed_post',array('EM_Event_Post_Admin','untrashed_post'),10,1);
		//Notices
		add_action('post_updated_messages',array('EM_Event_Post_Admin','admin_notices_filter'),1,1);
	}

	function admin_head(){
		global $post, $EM_Event;
		if( !empty($post) && $post->post_type == EM_POST_TYPE_EVENT ){
			$EM_Event = em_get_event($post->ID, 'post_id');
		}
	}
	
	function admin_notices(){
		//When editing
		global $post, $EM_Event, $pagenow;
		if( $pagenow == 'post.php' && ($post->post_type == EM_POST_TYPE_EVENT || $post->post_type == 'event-recurring') ){
			if ( $EM_Event->is_recurring() ) {
				$warning = "<p><strong>".__( 'WARNING: This is a recurring event.', 'dbem' )."</strong></p>";
				$warning .= "<p>". __( 'Modifications to this event will cause all recurrences of this event to be deleted and recreated and previous bookings will be deleted! You can edit individual recurrences and disassociate them with this recurring event.', 'dbem' );
				?><div class="updated"><?php echo $warning; ?></div><?php
			} elseif ( $EM_Event->is_recurrence() ) {
				$warning = "<p><strong>".__('WARNING: This is a recurrence in a set of recurring events.', 'dbem')."</strong></p>";
				$warning .= "<p>". sprintf(__('If you update this event data and save, it could get overwritten if you edit the recurring event template. To make it an independent, <a href="%s">detach it</a>.', 'dbem' ), $EM_Event->get_detach_url())."</p>";
				$warning .= "<p>".sprintf(__('To manage the whole set, <a href="%s">edit the recurring event template</a>.', 'dbem'),admin_url('post.php?action=edit&amp;post='.$EM_Event->get_event_recurrence()->post_id))."</p>";
				?><div class="updated"><?php echo $warning; ?></div><?php
			}
			if( !empty($EM_Event->group_id) && function_exists('groups_get_group') ){
				$group = groups_get_group(array('group_id'=>$EM_Event->group_id));
				$warning = sprintf(__('WARNING: This is a event belonging to the group "%s". Other group admins can also modify this event.', 'dbem'), $group->name);
				?><div class="updated"><p><?php echo $warning; ?></p></div><?php
			}
		}
	}
	
	function admin_notices_filter($messages){
		//When editing
		global $post, $EM_Notices;
		if( $post->post_type == EM_POST_TYPE_EVENT || $post->post_type == 'event-recurring' ){
			if ( $EM_Notices->count_errors() > 0 ) {
				unset($_GET['message']);
			}
		}
		return $messages;
	}
	
	function save_post($post_id){
		global $wpdb, $EM_Event, $EM_Location, $EM_Notices;
		$post_type = get_post_type($post_id);
		$is_post_type = $post_type == EM_POST_TYPE_EVENT || $post_type == 'event-recurring';
		$saving_status = !in_array(get_post_status($post_id), array('trash','auto-draft')) && !defined('DOING_AUTOSAVE');
		if(!defined('UNTRASHING_'.$post_id) && $is_post_type && $saving_status ){
			if( !empty($_REQUEST['_emnonce']) && wp_verify_nonce($_REQUEST['_emnonce'], 'edit_event') ){ 
				//this is only run if we know form data was submitted, hence the nonce
				$EM_Event = em_get_event($post_id, 'post_id');
				do_action('em_event_save_pre', $EM_Event); //technically, the event is saved... but the meta isn't. wp doesn't give an pre-intervention action for this (or does it?)
				//Handle Errors by making post draft
				$get_meta = $EM_Event->get_post_meta();
				$validate_meta = $EM_Event->validate_meta();
				$save_meta = $EM_Event->save_meta();
				$EM_Event->get_categories()->save(); //save categories in case of default category
				if( !$get_meta || !$validate_meta || !$save_meta ){
					//failed somewhere, set to draft, don't publish
					$EM_Event->set_status(null, true);
					if( $EM_Event->is_recurring() ){
						$EM_Notices->add_error( '<strong>'.__('Your event details are incorrect and recurrences cannot be created, please correct these errors first:','dbem').'</strong>', true); //Always seems to redirect, so we make it static
					}else{
						$EM_Notices->add_error( '<strong>'.sprintf(__('Your %s details are incorrect and cannot be published, please correct these errors first:','dbem'),__('event','dbem')).'</strong>', true); //Always seems to redirect, so we make it static
					}
					$EM_Notices->add_error($EM_Event->get_errors(), true); //Always seems to redirect, so we make it static
					apply_filters('em_event_save', false, $EM_Event);
				}else{
					//if this is just published, we need to email the user about the publication, or send to pending mode again for review
					if( (!$EM_Event->is_recurring() && !current_user_can('publish_events')) || ($EM_Event->is_recurring() && !current_user_can('publish_recurring_events')) ){
						if( $EM_Event->is_published() ){ $EM_Event->set_status(0, true); } //no publishing and editing... security threat
					}
					apply_filters('em_event_save', true, $EM_Event);
				}
			}else{
				//we're updating only the quick-edit style information, which is only post info saved into the index
				$EM_Event = em_get_event($post_id, 'post_id'); //grab event, via post info
				if( $EM_Event->validate() ){
					do_action('em_event_save_pre', $EM_Event); //technically, the event is saved... but the meta isn't. wp doesn't give an pre-intervention action for this (or does it?)
					//first things first, we must make sure we have an index, if not, reset it to a new one:
					$event_truly_exists = $wpdb->get_var('SELECT event_id FROM '.EM_EVENTS_TABLE." WHERE event_id={$EM_Event->event_id}") == $EM_Event->event_id;
					if(empty($EM_Event->event_id) || !$event_truly_exists){ $EM_Event->save_meta(); }
					//we can save the status now
					$event_status = $EM_Event->get_status(true);
					//if this is just published, we need to email the user about the publication, or send to pending mode again for review
					if( (!$EM_Event->is_recurring() && !current_user_can('publish_events')) || ($EM_Event->is_recurring() && !current_user_can('publish_recurring_events')) ){
						if( $EM_Event->is_published() ){ $EM_Event->set_status(0, true); } //no publishing and editing... security threat
					}
					//now update the db
					$wpdb->query("UPDATE ".EM_EVENTS_TABLE." SET event_name='{$EM_Event->event_name}', event_slug='{$EM_Event->event_slug}', event_status={$event_status}, event_private={$EM_Event->event_private} WHERE event_id='{$EM_Event->event_id}'");
					if( $EM_Event->is_recurring() &&  $EM_Event->is_published()){
						//recurrences are (re)saved only if event is published
						$EM_Event->save_events();
					}
					apply_filters('em_event_save', true, $EM_Event);
				}else{
					do_action('em_event_save_pre', $EM_Event); //technically, the event is saved... but the meta isn't. wp doesn't give an pre-intervention action for this (or does it?)
					//Event doesn't validate, so set status to null
					$EM_Event->set_status(null, true);
					apply_filters('em_event_save', false, $EM_Event);
				}
			}
			self::maybe_publish_location($EM_Event);
		}
	}
	
	/**
	 * Publish the location if the event has just been approved and the location is pending. We assume an editor published the event and approves the location too.
	 * @param EM_Event $EM_Event
	 */
	function maybe_publish_location($EM_Event){
		//do a dirty update for location too if it's not published
		if( $EM_Event->is_published() && !empty($EM_Event->location_id) ){
			$EM_Location = $EM_Event->get_location();
			if( $EM_Location->location_status !== 1 ){
				//let's also publish the location
				$EM_Location->set_status(1, true);
			}
		}
	}

	function before_delete_post($post_id){
		if(get_post_type($post_id) == EM_POST_TYPE_EVENT){
			$EM_Event = em_get_event($post_id,'post_id');
			$EM_Event->delete_meta();
		}
	}
	
	function trashed_post($post_id){
		if(get_post_type($post_id) == EM_POST_TYPE_EVENT){
			global $EM_Notices;
			$EM_Event = em_get_event($post_id,'post_id');
			$EM_Event->set_status(null);
			$EM_Notices->remove_all(); //no validation/notices needed
		}
	}
	
	function untrash_post($post_id){
		if(get_post_type($post_id) == EM_POST_TYPE_EVENT){
			//set a constant so we know this event doesn't need 'saving'
			if(!defined('UNTRASHING_'.$post_id)) define('UNTRASHING_'.$post_id, true);
		}
	}
	
	function untrashed_post($post_id){
		if(get_post_type($post_id) == EM_POST_TYPE_EVENT){
			global $EM_Notices;
			$EM_Event = em_get_event($post_id,'post_id');			
			$EM_Event->set_status(1);
			$EM_Notices->remove_all(); //no validation/notices needed
		}
	}
	
	function meta_boxes(){
		global $EM_Event;
		if( !empty($EM_Event->event_owner_anonymous) ){
			add_meta_box('em-event-anonymous', __('Anonymous Submitter Info','dbem'), array('EM_Event_Post_Admin','meta_box_anonymous'),EM_POST_TYPE_EVENT, 'side','high');
		}
		add_meta_box('em-event-when', __('When','dbem'), array('EM_Event_Post_Admin','meta_box_date'),EM_POST_TYPE_EVENT, 'side','high');
		if(get_option('dbem_locations_enabled', true)){
			add_meta_box('em-event-where', __('Where','dbem'), array('EM_Event_Post_Admin','meta_box_location'),EM_POST_TYPE_EVENT, 'normal','high');
		}
		if( defined('WP_DEBUG') && WP_DEBUG ){
			add_meta_box('em-event-meta', 'Event Meta (debugging only)', array('EM_Event_Post_Admin','meta_box_metadump'),EM_POST_TYPE_EVENT, 'normal','high');
		}
		if(get_option('dbem_rsvp_enabled', true)){
			add_meta_box('em-event-bookings', __('Bookings/Registration','dbem'), array('EM_Event_Post_Admin','meta_box_bookings'),EM_POST_TYPE_EVENT, 'normal','high');
			if( !empty($EM_Event->event_id) && $EM_Event->event_rsvp ){
				add_meta_box('em-event-bookings-stats', __('Bookings Stats','dbem'), array('EM_Event_Post_Admin','meta_box_bookings_stats'),EM_POST_TYPE_EVENT, 'side','core');
			}
		}
		if( get_option('dbem_attributes_enabled', true) ){
			add_meta_box('em-event-attributes', __('Attributes','dbem'), array('EM_Event_Post_Admin','meta_box_attributes'),EM_POST_TYPE_EVENT, 'normal','default');
		}
		if( EM_MS_GLOBAL && !is_main_site() && get_option('dbem_categories_enabled') ){
			add_meta_box('em-event-categories', __('Site Categories','dbem'), array('EM_Event_Post_Admin','meta_box_ms_categories'),EM_POST_TYPE_EVENT, 'side','low');
		}
	}
	
	function meta_box_metadump(){
		global $post,$EM_Event;
		echo "<pre>"; print_r($EM_Event); echo "</pre>";
	}
	
	function meta_box_anonymous(){
		global $EM_Event;
		?>
		<div class='updated'><p><?php _e('This event was submitted by a guest. You will find their details in the <em>Anonymous Submitter Info</em> box','dbem')?></p></div>
		<p><strong><?php _e('Name','dbem'); ?> :</strong> <?php echo $EM_Event->event_owner_name; ?></p> 
		<p><strong><?php _e('Name','dbem'); ?> :</strong> <?php echo $EM_Event->event_owner_email; ?></p> 
		<?php
	}
	
	function meta_box_date(){
		//create meta box check of date nonce
		?><input type="hidden" name="_emnonce" value="<?php echo wp_create_nonce('edit_event'); ?>" /><?php
		em_locate_template('forms/event/when.php', true);
	}
	
	function meta_box_bookings_stats(){
		em_locate_template('forms/event/booking-stats.php',true);
	}

	function meta_box_bookings(){
		em_locate_template('forms/event/bookings.php', true);
		add_action('admin_footer',array('EM_Event_Post_Admin','meta_box_bookings_overlay'));
	}
	
	function meta_box_bookings_overlay(){
		em_locate_template('forms/tickets-form.php', true); //put here as it can't be in the add event form
	}
	
	function meta_box_attributes(){
		em_locate_template('forms/event/attributes.php',true);
	}
	
	function meta_box_location(){
		em_locate_template('forms/event/location.php',true);
	}
	
	function meta_box_ms_categories(){
		global $EM_Event;
		$categories = EM_Categories::get(array('orderby'=>'category_name','hide_empty'=>false));
		?>
		<?php if( count($categories) > 0 ): ?>
			<p>
				<?php foreach( $categories as $EM_Category ):?>
				<label><input type="checkbox" name="event_categories[]" value="<?php echo $EM_Category->id; ?>" <?php if($EM_Event->get_categories()->has($EM_Category->id)) echo 'checked="checked"'; ?> /> <?php echo $EM_Category->name ?></label><br />			
				<?php endforeach; ?>
			</p>
		<?php else: ?>
			<p><?php sprintf(__('No categories available, <a href="%s">create one here first</a>','dbem'), get_bloginfo('wpurl').'/wp-admin/admin.php?page=events-manager-categories'); ?></p>
		<?php endif; ?>
		<!-- END Categories -->
		<?php
	}
}
add_action('admin_init',array('EM_Event_Post_Admin','init'));

/*
 * Recurring Events
 */
class EM_Event_Recurring_Post_Admin{
	function init(){
		global $pagenow;
		if($pagenow == 'post.php' || $pagenow == 'post-new.php' ){ //only needed if editing post
			add_action('admin_head', array('EM_Event_Recurring_Post_Admin','admin_head'));
			//Meta Boxes
			add_action('add_meta_boxes', array('EM_Event_Recurring_Post_Admin','meta_boxes'));
			//Notices
			add_action('admin_notices',array('EM_Event_Post_Admin','admin_notices')); //shared with posts
		}
		//Save/Edit actions
		add_action('before_delete_post',array('EM_Event_Recurring_Post_Admin','before_delete_post'),10,1);
		add_action('trashed_post',array('EM_Event_Recurring_Post_Admin','trashed_post'),10,1);
		add_action('untrash_post',array('EM_Event_Recurring_Post_Admin','untrash_post'),10,1);
		add_action('untrashed_post',array('EM_Event_Recurring_Post_Admin','untrashed_post'),10,1);
		//Notices
		add_action('post_updated_messages',array('EM_Event_Post_Admin','admin_notices_filter'),1,1); //shared with posts
	}
	
	function admin_head(){
		global $post, $EM_Event;
		if( !empty($post) && $post->post_type == 'event-recurring' ){
			$EM_Event = em_get_event($post->ID, 'post_id');
			//quick hacks to make event admin table make more sense for events
			?>
			<script type="text/javascript">
				jQuery(document).ready( function($){
					if(!EM.recurrences_menu){
						$('#menu-posts-'+EM.event_post_type+', #menu-posts-'+EM.event_post_type+' > a').addClass('wp-has-current-submenu');
					}
				});
			</script>
			<?php
		}
	}

	function before_delete_post($post_id){
		if(get_post_type($post_id) == 'event-recurring'){
			$EM_Event = em_get_event($post_id,'post_id');
			//now delete recurrences
			$events_array = EM_Events::get( array('recurrence'=>$EM_Event->event_id, 'scope'=>'all', 'status'=>'all' ) );
			foreach($events_array as $event){
				/* @var $event EM_Event */
				if($EM_Event->event_id == $event->recurrence_id && !empty($event->recurrence_id) ){ //double check the event is a recurrence of this event
					wp_delete_post($event->post_id, true);
				}
			}
			$EM_Event->post_type = EM_POST_TYPE_EVENT; //trick it into thinking it's one event.
			$EM_Event->delete_meta();
		}
	}
	
	function trashed_post($post_id){
		if(get_post_type($post_id) == 'event-recurring'){
			global $EM_Notices, $wpdb;
			$EM_Event = em_get_event($post_id,'post_id');
			$EM_Event->set_status(null);
			//now trash recurrences
			$events_array = EM_Events::get( array('recurrence_id'=>$EM_Event->event_id, 'scope'=>'all', 'status'=>'all' ) );
			foreach($events_array as $event){
				/* @var $event EM_Event */
				if($EM_Event->event_id == $event->recurrence_id ){ //double check the event is a recurrence of this event
					wp_trash_post($event->post_id);
				}
			}
			$EM_Notices->remove_all(); //no validation/notices needed
		}
	}
	
	function untrash_post($post_id){
		if(get_post_type($post_id) == 'event-recurring'){
			global $wpdb;
			//set a constant so we know this event doesn't need 'saving'
			if(!defined('UNTRASHING_'.$post_id)) define('UNTRASHING_'.$post_id, true);
			$EM_Event = em_get_event($post_id,'post_id');
			$events_array = EM_Events::get( array('recurrence_id'=>$EM_Event->event_id, 'scope'=>'all', 'status'=>'all' ) );
			foreach($events_array as $event){
				/* @var $event EM_Event */
				if($EM_Event->event_id == $event->recurrence_id){
					wp_untrash_post($event->post_id);
				}
			}
		}
	}
	
	function untrashed_post($post_id){
		if(get_post_type($post_id) == 'event-recurring'){
			global $EM_Notices,$EM_Event;
			$EM_Event->set_status(1);
			$EM_Notices->remove_all(); //no validation/notices needed
		}
	}
	
	function meta_boxes(){
		add_meta_box('em-event-recurring', __('Recurrences','dbem'), array('EM_Event_Recurring_Post_Admin','meta_box_recurrence'),'event-recurring', 'normal','high');
		//add_meta_box('em-event-meta', 'Event Meta (debugging only)', array('EM_Event_Post_Admin','meta_box_metadump'),'event-recurring', 'normal','high');
		add_meta_box('em-event-where', __('Where','dbem'), array('EM_Event_Post_Admin','meta_box_location'),'event-recurring', 'normal','high');
		if(get_option('dbem_rsvp_enabled')){
			add_meta_box('em-event-bookings', __('Bookings/Registration','dbem'), array('EM_Event_Post_Admin','meta_box_bookings'),'event-recurring', 'normal','high');
		}
		if( get_option('dbem_attributes_enabled') ){
			add_meta_box('em-event-attributes', __('Attributes','dbem'), array('EM_Event_Post_Admin','meta_box_attributes'),'event-recurring', 'normal','default');
		}
		if( EM_MS_GLOBAL && !is_main_site() && get_option('dbem_categories_enabled') ){
			add_meta_box('em-event-categories', __('Site Categories','dbem'), array('EM_Event_Post_Admin','meta_box_ms_categories'),'event-recurring', 'side','low');
		}
	}
	
	function meta_box_recurrence(){
		em_locate_template('forms/event/recurring-when.php', true);
	}
}
add_action('admin_init',array('EM_Event_Recurring_Post_Admin','init'));