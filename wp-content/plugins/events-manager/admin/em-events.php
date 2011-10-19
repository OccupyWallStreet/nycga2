<?php

/**
 * Determines whether to show event page or events page, and saves any updates to the event or events
 * @return null
 */
function em_admin_events_page() {
	//TODO Simplify panel for events, use form flags to detect certain actions (e.g. submitted, etc)
	global $wpdb, $EM_Notices, $EM_Event;
	
	$action = ( !empty($_REQUEST ['action']) ) ? $_REQUEST ['action']:'';
	$order = ( !empty($_REQUEST ['order']) ) ? $_REQUEST ['order']:'ASC';
	$limit = ( !empty($_REQUEST['limit']) ) ? $_REQUEST['limit'] : 20;//Default limit
	$page = ( !empty($_REQUEST['pno']) ) ? $_REQUEST['pno']:1;
	$offset = ( $page > 1 ) ? ($page-1)*$limit : 0;
	$search = ( !empty($_REQUEST['em_search']) ) ? $_REQUEST['em_search']:'';
	$scope_names = em_get_scopes();
	$scope = ( !empty($_REQUEST ['scope']) && array_key_exists($_REQUEST ['scope'], $scope_names) ) ? $_REQUEST ['scope']:'future';
	$selectedEvents = ( !empty($_REQUEST ['events']) ) ? $_REQUEST ['events']:'';
	
	$args = array('scope'=>$scope, 'limit'=>0, 'order'=>$order, 'search'=>$search );
	
	if(	!current_user_can('edit_others_events') ){
		$args['owner'] = get_current_user_id();
	}
	//Figure out what status to search for
	$args['status'] = ( isset($_REQUEST['status']) && is_numeric($_REQUEST['status']) ) ? $_REQUEST['status'] : false;
	
	$events = EM_Events::get( $args );
	$events_count = count ( $events );
	$pending_count = EM_Events::count( array('status'=>0, 'scope'=>$scope) );
	$approved_count = EM_Events::count( array('status'=> 1, 'scope'=>$scope) );
	$total_count = EM_Events::count( array('status'=> false, 'scope'=>$scope) );
	
	$use_events_end = get_option('dbem_use_event_end');
	echo $EM_Notices; 
	?>
	<div class="wrap">
		<div id="icon-events" class="icon32"><br />
		</div>
		<h2>	
			<?php echo $scope_names[$scope]; ?>
 	 		<a href="admin.php?page=events-manager-event" class="button add-new-h2"><?php _e('Add New','dbem'); ?></a>
 	 	</h2>
		<?php	
			$link = array ();
			$link ['past'] = "<a href='" . get_bloginfo ( 'wpurl' ) . "/wp-admin/admin.php?page=events-manager&amp;scope=past&amp;order=desc'>" . __ ( 'Past events', 'dbem' ) . "</a>";
			$link ['all'] = " <a href='" . get_bloginfo ( 'wpurl' ) . "/wp-admin/admin.php?page=events-manager&amp;scope=all&amp;order=desc'>" . __ ( 'All events', 'dbem' ) . "</a>";
			$link ['future'] = "  <a href='" . get_bloginfo ( 'wpurl' ) . "/wp-admin/admin.php?page=events-manager&amp;scope=future'>" . __ ( 'Future events', 'dbem' ) . "</a>";
		?> 
		<?php if ( !empty($_REQUEST['error']) ) : ?>
		<div id='message' class='error'>
			<p><?php echo $_REQUEST['error']; ?></p>
		</div>
		<?php endif; ?>
		<?php if ( !empty($_REQUEST['message']) ) : ?>
		<div id='message' class='updated fade'>
			<p><?php echo $_REQUEST['message']; ?></p>
		</div>
		<?php endif; ?>
		<form id="posts-filter" action="" method="get"><input type='hidden' name='page' value='events-manager' />
			<ul class="subsubsub">
				<li><a href='<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager&amp;scope=<?php echo $scope; ?>' <?php echo ( !isset($_REQUEST['status']) ) ? 'class="current"':''; ?>><?php _e ( 'Total', 'dbem' ); ?> <span class="count">(<?php echo $total_count; ?>)</span></a></li>
				<?php if( current_user_can('publish_events') ): ?>
				<li>| <a href='<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager&amp;scope=<?php echo $scope; ?>&amp;status=1' <?php echo ( isset($_REQUEST['status']) && $_REQUEST['status']=='1' ) ? 'class="current"':''; ?>><?php _e ( 'Approved', 'dbem' ); ?> <span class="count">(<?php echo $approved_count; ?>)</span></a></li>
				<li>| <a href='<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager&amp;scope=<?php echo $scope; ?>&amp;status=0' <?php echo ( isset($_REQUEST['status']) && $_REQUEST['status']=='0' ) ? 'class="current"':''; ?>><?php _e ( 'Pending', 'dbem' ); ?> <span class="count">(<?php echo $pending_count; ?>)</span></a></li>
				<?php endif; ?>
			</ul>
			<p class="search-box">
				<label class="screen-reader-text" for="post-search-input"><?php _e('Search Events','dbem'); ?>:</label>
				<input type="text" id="post-search-input" name="em_search" value="<?php echo (!empty($_REQUEST['em_search'])) ? $_REQUEST['em_search']:''; ?>" />
				<input type="submit" value="<?php _e('Search Events','dbem'); ?>" class="button" />
			</p>			
			<div class="tablenav">
			
				<div class="alignleft actions">
					<select name="action">
						<option value="-1" selected="selected"><?php _e ( 'Bulk Actions' ); ?></option>
						<option value="event_delete"><?php _e ( 'Delete selected','dbem' ); ?></option>
					</select> 
					<input type="submit" value="<?php _e ( 'Apply' ); ?>" name="doaction2" id="doaction2" class="button-secondary action" /> 
					<select name="scope">
						<?php
						foreach ( $scope_names as $key => $value ) {
							$selected = "";
							if ($key == $scope)
								$selected = "selected='selected'";
							echo "<option value='$key' $selected>$value</option>  ";
						}
						?>
					</select> 
					<input id="post-query-submit" class="button-secondary" type="submit" value="<?php _e ( 'Filter' )?>" />
				</div>
				<!--
				<div class="view-switch">
					<a href="/wp-admin/edit.php?mode=list"><img class="current" id="view-switch-list" src="http://wordpress.lan/wp-includes/images/blank.gif" width="20" height="20" title="List View" alt="List View" name="view-switch-list" /></a> <a href="/wp-admin/edit.php?mode=excerpt"><img id="view-switch-excerpt" src="http://wordpress.lan/wp-includes/images/blank.gif" width="20" height="20" title="Excerpt View" alt="Excerpt View" name="view-switch-excerpt" /></a>
				</div>
				-->
				<?php
				if ( $events_count >= $limit ) {
					$events_nav = em_admin_paginate( $events_count, $limit, $page);
					echo $events_nav;
				}
				?>
				<br class="clear" />
			</div>
				
			<?php
			if (empty ( $events )) {
				// TODO localize
				_e ( 'no events','dbem' );
			} else {
			?>
					
			<table class="widefat events-table">
				<thead>
					<tr>
						<th class='manage-column column-cb check-column' scope='col'>
							<input class='select-all' type="checkbox" value='1' />
						</th>
						<th><?php _e ( 'Name', 'dbem' ); ?></th>
						<th>&nbsp;</th>
						<th><?php _e ( 'Location', 'dbem' ); ?></th>
						<th colspan="2"><?php _e ( 'Date and time', 'dbem' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$rowno = 0;
					$event_count = 0;
					foreach ( $events as $event ) {
						/* @var $event EM_Event */
						if( ($rowno < $limit || empty($limit)) && ($event_count >= $offset || $offset === 0) ) {
							$rowno++;
							$class = ($rowno % 2) ? 'alternate' : '';
							// FIXME set to american
							$localised_start_date = date_i18n('D d M Y', $event->start);
							$localised_end_date = date_i18n('D d M Y', $event->end);
							$style = "";
							$today = date ( "Y-m-d" );
							$location_summary = "<b>" . $event->location->name . "</b><br/>" . $event->location->address . " - " . $event->location->town;
							
							if ($event->start_date < $today && $event->end_date < $today){
								$class .= " past";
							}
							//Check pending approval events
							if ( !$event->status ){
								$class .= " pending";
							}					
							?>
							<tr class="event <?php echo trim($class); ?>" <?php echo $style; ?> id="event_<?php echo $event->id ?>">
								<td>
									<input type='checkbox' class='row-selector' value='<?php echo $event->id; ?>' name='events[]' />
								</td>
								<td>
									<strong>
										<a class="row-title" href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager-event&amp;event_id=<?php echo $event->id ?>&amp;scope=<?php echo $scope ?>&amp;pno=<?php echo $page ?>"><?php echo ($event->name); ?></a>
									</strong>
									<?php 
									if( get_option('dbem_rsvp_enabled') == 1 && $event->rsvp == 1 ){
										?>
										<br/>
										<a href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager-bookings&amp;event_id=<?php echo $event->id ?>"><?php echo __("Bookings",'dbem'); ?></a> &ndash;
										<?php _e("Booked",'dbem'); ?>: <?php echo $event->get_bookings()->get_booked_spaces()."/".$event->get_spaces(); ?>
										<?php if( get_option('dbem_bookings_approval') == 1 ): ?>
											| <?php _e("Pending",'dbem') ?>: <?php echo $event->get_bookings()->get_pending_spaces(); ?>
										<?php endif;
									}
									?>
									<div class="row-actions">
										<span class="trash"><a href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager&amp;action=event_delete&amp;event_id=<?php echo $event->id ?>&amp;scope=<?php echo $scope ?>&amp;pno=<?php echo $page ?>" class="em-event-delete"><?php _e('Delete','dbem'); ?></a></span>
										<?php if( !$event->status && current_user_can('publish_events') ): ?>
										| <a href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager&amp;action=event_approve&amp;event_id=<?php echo $event->id ?>&amp;scope=<?php echo $scope ?>&amp;pno=<?php echo $page ?>" class="em-event-approve" style="color:green"><?php _e('Approve','dbem'); ?></a>
										<?php endif; ?>
									</div>
								</td>
								<td>
									<a href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager-event&amp;action=event_duplicate&amp;event_id=<?php echo $event->id; ?>&amp;scope=<?php echo $scope ?>&amp;pno=<?php echo $page ?>" title="<?php _e ( 'Duplicate this event', 'dbem' ); ?>">
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
										<a href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager-event&amp;event_id=<?php echo $event->recurrence_id ?>&amp;scope=<?php echo $scope ?>&amp;pno=<?php echo $page ?>"><?php _e ( 'Reschedule', 'dbem' ); ?></a> |
										<span class="trash"><a href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager&amp;action=event_delete&amp;event_id=<?php echo $event->recurrence_id ?>&amp;scope=<?php echo $scope ?>&amp;pno=<?php echo $page ?>" class="em-event-rec-delete" onclick ="if( !confirm('<?php echo $recurrence_delete_confirm; ?>') ){ return false; }"><?php _e('Delete','dbem'); ?></a></span>
										</strong>
										<?php
									}
									?>
								</td>
							</tr>
							<?php
						}
						$event_count++;
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
	<?php
}

?>