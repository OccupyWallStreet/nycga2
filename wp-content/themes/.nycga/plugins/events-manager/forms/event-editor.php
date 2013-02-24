<?php
/* WARNING! This file may change in the near future as we intend to add features to the event editor. If at all possible, try making customizations using CSS, jQuery, or using our hooks and filters. - 2012-02-14 */
/* 
 * To ensure compatability, it is recommended you maintain class, id and form name attributes, unless you now what you're doing. 
 * You also must keep the _wpnonce hidden field in this form too.
 */
global $EM_Event, $EM_Notices, $bp;

//check that user can access this page
if( is_object($EM_Event) && !$EM_Event->can_manage('edit_events','edit_others_events') ){
	?>
	<div class="wrap"><h2><?php _e('Unauthorized Access','dbem'); ?></h2><p><?php echo sprintf(__('YOU do not have the rights to manage this %s.','dbem'),__('Event','dbem')); ?></p></div>
	<?php
	return false;
}elseif( !is_object($EM_Event) ){
	$EM_Event = new EM_Event();
}
$required = '*';

echo $EM_Notices;
//Success notice
if( !empty($_REQUEST['success']) ){
	if(!get_option('dbem_events_form_reshow')) return false;
}
?>	
<form enctype='multipart/form-data' id="event-form" method="post" action="<?php echo add_query_arg(array('success'=>null)); ?>">
	<div class="wrap">
		<?php do_action('em_front_event_form_header'); ?>
		<?php if(get_option('dbem_events_anonymous_submissions') && !is_user_logged_in()): ?>
		<div class="event-contact">
			<h4 class="event-form-submitter"><?php _e ( 'Contact Information', 'dbem' ); ?></h4>
			<div class="inside event-form-submitter">
				<p>
					<label><?php _e('Contact Name', 'dbem'); ?></label>
					<input type="text" name="event_owner_name" id="event-owner-name" value="<?php echo esc_attr($EM_Event->event_owner_name); ?>" />
				</p>
				<p>
					<label><?php _e('Contact Email', 'dbem'); ?></label>
					<input type="text" name="event_owner_email" id="event-owner-email" value="<?php echo esc_attr($EM_Event->event_owner_email); ?>" />
				</p>
				<?php do_action('em_font_event_form_guest'); ?>
			</div>
		</div>
		<?php endif; ?>
		<div class="inside event-form-name">
			<h4 class="event-form-name"><?php _e ( 'Event Name', 'dbem' ); ?></h4>
			<input type="text" name="event_name" id="event-name" value="<?php echo htmlspecialchars($EM_Event->event_name,ENT_QUOTES); ?>" /><?php echo $required; ?>
			<?php// _e ( 'The event name. Example: Birthday party', 'dbem' )?>
			<div class="event-group">
				<?php em_locate_template('forms/event/group.php',true); ?>
			</div>
		</div>
					
		<div class="event-date">
			<h4 class="event-form-when"><?php _e ( 'Date/Time', 'dbem' ); ?></h4>
			<div class="inside">
			<?php 
				if( empty($EM_Event->event_id) && $EM_Event->can_manage('edit_recurring_events','edit_others_recurring_events') && get_option('dbem_recurrence_enabled') ){
					em_locate_template('forms/event/when-with-recurring.php',true);
				}elseif( $EM_Event->is_recurring()  ){
					em_locate_template('forms/event/recurring-when.php',true);
				}else{
					em_locate_template('forms/event/when.php',true);
				}
			?>
			</div>
		</div>

		<?php if( get_option('dbem_locations_enabled') ): ?>
		<div class="event-location">
			<h4 class="event-form-where"><?php _e ( 'Location', 'dbem' ); ?></h4>
			<div class="inside event-form-where">
				<?php em_locate_template('forms/event/location.php',true); ?>
			</div>
			<div style="clear:both;"></div>
			<div class="event-location-details">
				<?php if(get_option('dbem_categories_enabled')) { em_locate_template('forms/event/attributes-public.php',true); }  ?>
			</div>
			<div style="clear:both;"></div>
		</div>
		<?php endif; ?>
			
		<div class="inside event-form-details">
			<h4 class="event-form-details"><?php _e ( 'Description', 'dbem' ); ?></h4>
			<div class="event-editor">
				<?php if( get_option('dbem_events_form_editor') && function_exists('wp_editor') ): ?>
					<?php wp_editor($EM_Event->post_content, 'em-editor-content', array('textarea_name'=>'content') ); ?> 
				<?php else: ?>
					<textarea name="content" rows="10" style="width:100%"><?php echo $EM_Event->post_content ?></textarea>
					<br />
					<?php _e ( 'Details about the event.', 'dbem' )?><?php _e ( 'HTML Allowed.', 'dbem' )?>
				<?php endif; ?>
			</div>
			
			<!-- Featured Image -->
			<?php if( $EM_Event->can_manage('upload_event_images','upload_event_images') ): ?>
			<div class="event-image">
			<h4><?php _e ( 'Event Image', 'dbem' ); ?></h4>
				<div class="inside event-form-image">
					<?php em_locate_template('forms/event/featured-image-public.php',true); ?>
				</div>
			</div>
			<?php endif; ?>
			
			<!-- Category -->
			<div class="event-category">
				<?php if(get_option('dbem_categories_enabled')) { em_locate_template('forms/event/categories-public.php',true); }  ?>
			</div>
			<div style="clear:both;"></div>
		</div>
		
		<?php if( get_option('dbem_rsvp_enabled') && $EM_Event->can_manage('manage_bookings','manage_others_bookings') ) : ?>
		<!-- START Bookings -->
		<h4><?php _e('Bookings/Registration','dbem'); ?></h4>
		<div class="inside event-form-bookings">				
			<?php em_locate_template('forms/event/bookings.php',true); ?>
		</div>
		<!-- END Bookings -->
		<?php endif; ?>
		
		<?php do_action('em_front_event_form_footer'); ?>
	</div>
	<p class="submit">
		<input type="submit" name="events_update" value="<?php _e ( 'Submit Event', 'dbem' ); ?> &raquo;" />
	</p>
	<input type="hidden" name="event_id" value="<?php echo $EM_Event->event_id; ?>" />
	<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('wpnonce_event_save'); ?>" />
	<input type="hidden" name="action" value="event_save" />
	<?php if( !empty($_REQUEST['redirect_to']) ): ?>
	<input type="hidden" name="redirect_to" value="<?php echo $_REQUEST['redirect_to']; ?>" />
	<?php endif; ?>
</form>
<?php em_locate_template('forms/tickets-form.php', true); //put here as it can't be in the add event form ?>