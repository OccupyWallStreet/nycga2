<?php
	//TODO Simplify panel for events, use form flags to detect certain actions (e.g. submitted, etc)
	global $wpdb, $bp, $EM_Event, $EM_Notices;
	/* @var $args array */
	/* @var $EM_Events array */
	/* @var events_count int */
	/* @var future_count int */
	/* @var pending_count int */
	/* @var url string */
	//add new button will only appear if called from em_event_admin template tag, or if the $show_add_new var is set
	if(!empty($show_add_new) && current_user_can('edit_events')) echo '<a class="em-button button add-new-h2" href="'.em_add_get_params($_SERVER['REQUEST_URI'],array('action'=>'edit','scope'=>null,'status'=>null,'event_id'=>null)).'">'.__('Add New','dbem').'</a>';
	?>
	<div class="wrap">
		<?php echo $EM_Notices; ?>
		<form id="posts-filter" action="" method="get">
			<div class="subsubsub">
				<a href='<?php echo em_add_get_params($_SERVER['REQUEST_URI'], array('scope'=>null,'status'=>null)); ?>' <?php echo ( !isset($_GET['status']) ) ? 'class="current"':''; ?>><?php _e ( 'Upcoming', 'dbem' ); ?> <span class="count">(<?php echo $future_count; ?>)</span></a> &nbsp;|&nbsp; 
				<?php if( !current_user_can('publish_events') ): ?>
				<a href='<?php echo em_add_get_params($_SERVER['REQUEST_URI'], array('scope'=>null,'status'=>0)); ?>' <?php echo ( isset($_GET['status']) && $_GET['status']=='0' ) ? 'class="current"':''; ?>><?php _e ( 'Pending', 'dbem' ); ?> <span class="count">(<?php echo $pending_count; ?>)</span></a> &nbsp;|&nbsp; 
				<?php endif; ?>
				<a href='<?php echo em_add_get_params($_SERVER['REQUEST_URI'], array('scope'=>'past','status'=>null)); ?>' <?php echo ( !empty($_REQUEST['scope']) && $_REQUEST['scope'] == 'past' ) ? 'class="current"':''; ?>><?php _e ( 'Past Events', 'dbem' ); ?></a>
			</div>
			<p class="search-box">
				<label class="screen-reader-text" for="post-search-input"><?php _e('Search Events','dbem'); ?>:</label>
				<input type="text" id="post-search-input" name="em_search" value="<?php echo (!empty($_REQUEST['em_search'])) ? $_REQUEST['em_search']:''; ?>" />
				<input type="submit" value="<?php _e('Search Events','dbem'); ?>" class="button" />
			</p>
			<div class="tablenav">
				<?php
				if ( $events_count >= $limit ) {
					$events_nav = em_admin_paginate( $events_count, $limit, $page);
					echo $events_nav;
				}
				?>
				<br class="clear" />
			</div>
				
			<?php
			if ( empty($EM_Events) ) {
				echo get_option ( 'dbem_no_events_message' );
			} else {
			?>
					
			<table class="widefat events-table">
				<thead>
					<tr>
						<?php /* 
						<th class='manage-column column-cb check-column' scope='col'>
							<input class='select-all' type="checkbox" value='1' />
						</th>
						*/ ?>
						<th><?php _e ( 'Name', 'dbem' ); ?></th>
						<th>&nbsp;</th>
						<th><?php _e ( 'Location', 'dbem' ); ?></th>
						<th colspan="2"><?php _e ( 'Date and time', 'dbem' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php 
					foreach ( $EM_Events as $event ) {
						/* @var $event EM_Event */
						$rowno++;
						$class = ($rowno % 2) ? 'alternate' : '';
						// FIXME set to american
						$localised_start_date = date_i18n('D d M Y', $event->start);
						$localised_end_date = date_i18n('D d M Y', $event->end);
						$style = "";
						$today = date ( "Y-m-d" );
						$location_summary = "<b>" . $event->get_location()->name . "</b><br/>" . $event->get_location()->address . " - " . $event->get_location()->town;
						
						if ($event->start_date < $today && $event->end_date < $today){
							$class .= " past";
						}
						//Check pending approval events
						if ( !$event->status ){
							$class .= " pending";
						}					
						?>
						<tr class="event <?php echo trim($class); ?>" <?php echo $style; ?> id="event_<?php echo $event->event_id ?>">
							<?php /*
							<td>
								<input type='checkbox' class='row-selector' value='<?php echo $event->event_id; ?>' name='events[]' />
							</td>
							*/ ?>
							<td>
								<strong>
									<a class="row-title" href="<?php echo esc_url($event->get_edit_url()); ?>"><?php echo esc_html($event->name); ?></a>
								</strong>
								<?php 
								if( get_option('dbem_rsvp_enabled') == 1 && $event->rsvp == 1 ){
									?>
									<br/>
									<a href="<?php echo esc_url($event->get_bookings_url()); ?>"><?php echo __("Bookings",'dbem'); ?></a> &ndash;
									<?php _e("Booked",'dbem'); ?>: <?php echo $event->get_bookings()->get_booked_spaces()."/".$event->get_spaces(); ?>
									<?php if( get_option('dbem_bookings_approval') == 1 ): ?>
										| <?php _e("Pending",'dbem') ?>: <?php echo $event->get_bookings()->get_pending_spaces(); ?>
									<?php endif;
								}
								?>
								<div class="row-actions">
									<?php if( current_user_can('delete_events')) : ?>
									<span class="trash"><a href="<?php echo esc_url(add_query_arg(array('action'=>'event_delete', 'event_id'=>$event->event_id, '_wpnonce'=> wp_create_nonce('event_delete_'.$event->event_id)))); ?>" class="em-event-delete"><?php _e('Delete','dbem'); ?></a></span>
									<?php endif; ?>
								</div>
							</td>
							<td>
								<a href="<?php echo esc_url(add_query_arg(array('action'=>'event_duplicate', 'event_id'=>$event->event_id, '_wpnonce'=> wp_create_nonce('event_duplicate_'.$event->event_id)))); ?>" title="<?php _e ( 'Duplicate this event', 'dbem' ); ?>">
									<strong>+</strong>
								</a>
							</td>
							<td>
								<?php echo $location_summary; ?>
							</td>
					
							<td>
								<?php echo $localised_start_date; ?>
								<?php echo ($localised_end_date != $localised_start_date) ? " - $localised_end_date":'' ?>
								<br />
								<?php
									//TODO Should 00:00 - 00:00 be treated as an all day event? 
									echo substr ( $event->start_time, 0, 5 ) . " - " . substr ( $event->end_time, 0, 5 ); 
								?>
							</td>
							<td>
								<?php 
								if ( $event->is_recurrence() ) {
									$recurrence_delete_confirm = __('WARNING! You will delete ALL recurrences of this event, including booking history associated with any event in this recurrence. To keep booking information, go to the relevant single event and save it to detach it from this recurrence series.','dbem');
									?>
									<strong>
									<?php echo $event->get_recurrence_description(); ?> <br />
									<a href="<?php echo esc_url($event->get_edit_reschedule_url()); ?>"><?php _e ( 'Edit Recurring Events', 'dbem' ); ?></a>
									<?php if( current_user_can('delete_events')) : ?>
									<span class="trash"><a href="<?php echo esc_url(add_query_arg(array('action'=>'event_delete', 'event_id'=>$event->recurrence_id, '_wpnonce'=> wp_create_nonce('event_delete_'.$event->recurrence_id)))); ?>" class="em-event-rec-delete" onclick ="if( !confirm('<?php echo $recurrence_delete_confirm; ?>') ){ return false; }"><?php _e('Delete','dbem'); ?></a></span>
									<?php endif; ?>										
									</strong>
									<?php
								}
								?>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>  
			<?php
			} // end of table
			?>
			<div class='tablenav'>
				<div class="alignleft actions">
				<br class='clear' />
				</div>
				<?php if ( $events_count >= $limit ) : ?>
				<div class="tablenav-pages">
					<?php
					echo $events_nav;
					?>
				</div>
				<?php endif; ?>
				<br class='clear' />
			</div>
		</form>
	</div>