<?php
/*
 * Default Events List Template
 * This page displays a list of events, called during the em_content() if this is an events list page.
 * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
 * You can display events however you wish, there are a few variables made available to you:
 * 
 * $args - the args passed onto EM_Events::output()
 * 
 */ 

/* $events = EM_Events::get( apply_filters('em_content_events_args', $args) ); */

global $bp;

$url = $bp->events->link . 'my-events/'; //url to this page

$args['scope'] = !empty($_REQUEST['scope'])? $_REQUEST['scope'] : 'future';
$events_count = EM_Events::get($args, true);
$args['limit'] = get_option('dbem_events_default_limit') ? get_option('dbem_events_default_limit') : 20;
$args['page'] = (!empty($_REQUEST['pno']) && is_numeric($_REQUEST['pno']) )? $_REQUEST['pno'] : 1;
$args['offset'] = ($args['page'] - 1) * $args['limit'];
$EM_Events = EM_Events::get($args);

if( get_option('dbem_events_page_search') && !defined('DOING_AJAX') ){
	em_locate_template('templates/events-search.php',true);
}

if( $events_count > 0 ){ ?>
				<?php if ( $events_count >= $args['limit'] ) : ?>
					<div class="tablenav">
					<?php 
					require_once(EM_DIR . '/admin/em-admin.php');
					$events_nav = em_admin_paginate( $events_count, $args['limit'], $args['page']);
					echo $events_nav; ?>

					<a id="events-list-ics" class="ics-download" href="/events/events.ics">iCal</a>
					<a id="events-list-rss" class="events-rss" title="RSS Feed" href="/events/rss/">Events RSS</a>
					<div style="clear: both"></div>
					</div>
				<?php endif; ?>
				
				<!-- Add event -->
				<?php
					if ( is_user_logged_in() ) {
					    echo '<p><a href="/events/event-edit/?action=edit" class="add-event button">Add New Event</a></p>';
					} else {
					    echo '';
					}
				?>
				
			<table class="widefat events-table">
				<thead>
					<tr class="header-row">
						<?php /* 
						<th class='manage-column column-cb check-column' scope='col'>
							<input class='select-all' type="checkbox" value='1' />
						</th>
						*/ ?>
						<th class="event-date"><?php _e ( 'Date', 'dbem' ); ?></th>
						<th class="event-time"><?php _e ( 'Time', 'dbem' ); ?></th>
						<th class="event-name"><?php _e ( 'Description', 'dbem' ); ?></th>
						<th class="event-group"><?php _e ( 'Group & Category', 'dbem' ); ?></th>
						<!-- <th class="event-location"><?php _e ( 'Location', 'dbem' ); ?></th> -->
					</tr>
				</thead>
				<tbody>
					<?php 
					$rowno = 0;
					$event_count = 0;
					foreach ( $EM_Events as $event ) {
						/* @var $event EM_Event */
							$rowno++;
							$class = ($rowno % 2) ? 'alternate' : '';
							// FIXME set to american
							$localised_start_date = date_i18n('D, M d', $event->start);
							$localised_end_date = date_i18n('D, M d', $event->end);
							$style = "";
							$today = date( "Y-m-d", current_time('timestamp') );
							$event_date = date('Y-m-d', $event->start);
							
							if ($event->start_date < $today && $event->end_date < $today){
								$class .= " past";
							}
							//Check pending approval events
							if ( !$event->status ){
								$class .= " pending";
							}
							
							$event_group = groups_get_group( array( 'group_id' => $event->group_id ) );
												
							?>
							<tr class="event <?php echo trim($class); ?>" <?php echo $style; ?> id="event_<?php echo $event->id ?>">
								
								<td class="event-datetime">
									<div class="event-date">
									<!-- Modified to show day of week -->
										<?php echo $event_date == $today ? _e('Today') : '<span>' . date('D', $event->start) . '</span>' . date('M d', $event->start) . '<br /><span>' . date('Y', $event->start).'</span>'; ?>
									</div>
								</td>
								<td>
									<div class="event-time"><?php
										//TODO Should 00:00 - 00:00 be treated as an all day event? 
										echo date('g:ia', strtotime($event->start_time)) ?> - <?php echo ($localised_end_date != $localised_start_date) ? $localised_end_date . ' @' : '' ?>
										<?php echo date('g:ia', strtotime($event->end_time)); 
									?></div>
								</td>
								
								<td class="event-title">
								<!-- Title -->
									<p class="event-name"><a class="row-title" href="<?php echo $event->output('#_EVENTURL') ?>"><span class="event-thumbnail"><?php echo $event->output('#_EVENTIMAGE') ?></span></a> <?php echo $event->output('#_EVENTLINK') ?></p>
								<!--
</td>
								<td>
-->
									<!-- Location -->
									<p class="event-location-name"><?php echo $event->output('#_LOCATIONLINK') ?></p>
									<span class="event-address"><?php echo $event->output('#_LOCATIONFULLLINE') ?></span>
									<span class="event-location-details"><?php echo $event->attributes['Location Details'] ?></span>
								</td>
									
								<td class="event-group">
									<!-- Group -->
									<div class="group-cat">
									<?php if ($event->group_id > 0) : ?>
										<a href="<?php echo bp_get_group_permalink($event_group) ?>"><?php echo bp_core_fetch_avatar('object=group&item_id='.$event_group->id) ?><br/>
										<span class="event-group-name"><?php echo $event_group->name ?></span></a>
										<span class="event-categories"><?php echo $event->output('#_EVENTCATEGORIES') ?></span>
									<?php else : ?>
										<span class="event-group-name"></span>
										<span class="event-categories"><?php echo $event->output('#_EVENTCATEGORIES') ?></span>
									<?php endif; ?>
									</div>

									<?php 
									if( get_option('dbem_rsvp_enabled') == 1 && $event->rsvp == 1 ){
										?>
										<br/>
										<?php if( get_option('dbem_bookings_approval') == 1 ): ?>
											| <?php _e("Pending",'dbem') ?>: <?php echo $event->get_bookings()->get_pending_spaces(); ?>
										<?php endif;
									}
									
									?>
									<?php echo $event->is_recurrence() ? '<p class="recurrence-descr">' . $event->get_recurrence_description() . '</p>' : ''; ?>
									<?php if ((groups_is_user_admin(get_current_user_id(), $event->group_id) || groups_is_user_mod(get_current_user_id(), $event->group_id)) || get_current_user_id() == $event->id || current_user_can('edit_others_events') || current_user_can('delete_others_events')) : ?>
										
										<div class="event-actions">
										<!-- Edit event -->
										<?php if ((groups_is_user_admin(get_current_user_id(), $event->group_id) || groups_is_user_mod(get_current_user_id(), $event->group_id)) || get_current_user_id() == $event->id || current_user_can('edit_others_events')) : ?>
											<a class="button event-edit bp-secondary-action" href="/events/event-edit/?action=edit&event_id=<?php echo $event->id ?>" title="<?php _e ( 'Edit this event', 'dbem' ); ?>"><?php _e ( 'Edit', 'dbem' ); ?></a>
										<?php endif; ?>
										
										<!-- Duplicate event -->
										<?php if ((groups_is_user_admin(get_current_user_id(), $event->group_id) || groups_is_user_mod(get_current_user_id(), $event->group_id)) || get_current_user_id() == $event->id || current_user_can('delete_others_events')) : ?>
											<a class="button event-duplicate bp-secondary-action" href="<?php echo esc_url(add_query_arg(array('action'=>'event_duplicate', 'event_id'=>$event->event_id, '_wpnonce'=> wp_create_nonce('event_duplicate_'.$event->event_id)))); ?>">Duplicate</a>
										<?php endif; ?>
										
										<!-- Delete event -->
										<?php if ((groups_is_user_admin(get_current_user_id(), $event->group_id) || groups_is_user_mod(get_current_user_id(), $event->group_id)) || get_current_user_id() == $event->id || current_user_can('delete_others_events')) : ?>
											
											<span class="trash"><a class="button event-delete em-event-rec-delete" href="<?php echo $url ?>?action=event_delete&amp;event_id=<?php echo $event->id ?>" class="button event-delete em-event-rec-delete"  title="<?php _e ( 'Delete this event', 'dbem' ); ?>" onclick ="if( !confirm('Are you sure? This cannot be undone.') ){ return false; }"><?php _e('X Delete','dbem'); ?></a></span>

										<?php if ( $event->is_recurrence() && $event->can_manage('edit_events','edit_others_events') ) : $recurrence_delete_confirm = __('WARNING! You will delete ALL recurrences of this event.','dbem'); ?>
											<span class="trash">&nbsp;<a class="button bp-secondary-action" href="<?php echo $url ?>?action=event_delete&amp;event_id=<?php echo $event->recurrence_id ?>&scope=future" class="em-event-rec-delete" title="<?php _e ( 'Delete this series', 'dbem' ); ?>" onclick ="if( !confirm('<?php echo $recurrence_delete_confirm; ?>') ){ return false; }"><?php _e('Delete Series','dbem'); ?></a></span>
										<?php endif; ?>
										</div>
										
									<?php endif; ?>
								</td>						
					<?php endif; ?>
							</tr>
							<?php
					}
					?>
				</tbody>
			</table>  
			<div class="tablenav">
				<?php
				if ( $events_count >= $args['limit'] ) {
					echo $events_nav;
				}
				?>
				<br class="clear" />
			</div>
<?php
}else{
	echo get_option ( 'dbem_no_events_message' );
}