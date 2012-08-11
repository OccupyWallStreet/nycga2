<?php
/*
 * This page displays a single event, called during the em_content() if this is an event page.
 * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
 * You can display events however you wish, there are a few variables made available to you:
 * 
 * $args - the args passed onto EM_Events::output() 
 */

global $bp;
$url = $bp->events->link . 'my-events/'; //url to this page 

global $EM_Event;
/* @var $EM_Event EM_Event */
if( $EM_Event->status == 1 ){
	echo $EM_Event->output_single();
}else{
	echo get_option('dbem_no_events_message');
}

global $wpdb;

$EM_Event_owner = $wpdb->get_results("SELECT event_owner FROM wp_em_events WHERE event_id={$EM_Event->id}");

global $current_user;
get_currentuserinfo();

if ((groups_is_user_admin(get_current_user_id(), $EM_Event->group_id) || groups_is_user_mod(get_current_user_id(), $EM_Event->group_id)) || get_current_user_id() == $EM_Event->id || current_user_can('edit_others_events') || current_user_can('delete_others_events')) : ?>
	<div class="event-actions">
	<?php if ((groups_is_user_admin(get_current_user_id(), $EM_Event->group_id) || groups_is_user_mod(get_current_user_id(), $EM_Event->group_id)) || get_current_user_id() == $EM_Event->id || current_user_can('edit_others_events')) : ?><a class="button bp-secondary-action" href="<?php echo $url ?>edit/?action=edit&amp;event_id=<?php echo $EM_Event->id ?>" title="<?php _e ( 'Edit this event', 'dbem' ); ?>"><?php _e ( 'Edit', 'dbem' ); ?></a><?php endif; ?><?php if ((groups_is_user_admin(get_current_user_id(), $EM_Event->group_id) || groups_is_user_mod(get_current_user_id(), $EM_Event->group_id)) || get_current_user_id() == $EM_Event->id || current_user_can('delete_others_events')) : ?><a class="button bp-secondary-action" href="<?php echo $url ?>edit/?action=event_duplicate&amp;event_id=<?php echo $EM_Event->id ?>" title="<?php _e ( 'Duplicate this event', 'dbem' ); ?>">Duplicate</a><?php endif; ?><?php if ((groups_is_user_admin(get_current_user_id(), $EM_Event->group_id) || groups_is_user_mod(get_current_user_id(), $EM_Event->group_id)) || get_current_user_id() == $EM_Event->id || current_user_can('delete_others_events')) : ?><a class="button bp-secondary-action" href="<?php echo $url ?>?action=event_delete&amp;event_id=<?php echo $EM_Event->id ?>" class="em-event-delete"  title="<?php _e ( 'Delete this event', 'dbem' ); ?>" onclick ="if( !confirm('Are you sure? This cannot be undone.') ){ return false; }"><?php _e('Delete','dbem'); ?></a><?php endif; ?><?php if ( $EM_Event->is_recurrence() && $EM_Event->can_manage('edit_events','edit_others_events') ) : $recurrence_delete_confirm = __('WARNING! You will delete ALL recurrences of this event.','dbem'); ?><a class="button bp-secondary-action" href="<?php echo $url ?>?action=event_delete&amp;event_id=<?php echo $EM_Event->recurrence_id ?>&scope=future" class="em-event-rec-delete" title="<?php _e ( 'Delete this series', 'dbem' ); ?>" onclick ="if( !confirm('<?php echo $recurrence_delete_confirm; ?>') ){ return false; }"><?php _e('Delete Series','dbem'); ?></a>
	<?php endif; ?>
	</div>
<?php endif; ?>