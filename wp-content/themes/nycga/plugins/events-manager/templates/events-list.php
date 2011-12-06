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

if( get_option('dbem_events_page_search') ){
	em_locate_template('templates/events-search.php',true);
}	
if( $events_count > 0 ){ ?>
				<?php if ( $events_count >= $args['limit'] ) : ?>
					<div class="tablenav">
					<?php 
					require_once(EM_DIR . '/admin/em-admin.php');
					$events_nav = em_admin_paginate( $events_count, $args['limit'], $args['page']);
					echo $events_nav; ?>
					<script type="text/javascript">
						jQuery(document).ready(function($){
							$('#events-list-ics').click(function(){
								$this = $(this);
								$icsLink = $('#ics-link');
								$icsLink.position({
									    my:        "left top",
									    at:        "left bottom",
									    of:        $this, // or $("#otherdiv)
									    offset:    '0 10'
									})
								if ($icsLink.css('visibility') == 'hidden')
								{
									$icsLink.css('visibility', 'visible');
									$('input', $icsLink).select()
								}
								else
								{
									$icsLink.css('visibility', 'hidden');
								}
								return false;
							})
							$('#close-ics').click(function()
							{
								$('#ics-link').css('visibility', 'hidden');
								return false;
							})
						})
					</script>
					<a id="events-list-ics" class="ics-download" href="#">Subscribe</a>
					<div id="ics-link">
						<a id="close-ics" href="#">[x]</a>
						<h4>Subscribe to NYCGA Events</h4>
						<p>Copy this link to your favorite calendar application:</p>
						<input type="text" value="<?php bloginfo('siteurl') ?>/events.ics" />
					</div>
					<a id="events-list-rss" class="events-rss" title="RSS Feed" href="/events/rss/">Events RSS</a>
					<div style="clear: both"></div>
					</div>
				<?php endif; ?>
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
						<th class="event-location"><?php _e ( 'Location', 'dbem' ); ?></th>
<!-- 						<th class="event-category"><?php _e ('Category', 'dbem') ?></th> -->
						<th class="event-group"><?php _e ( 'Group', 'dbem' ); ?></th>
						<th class="event-name"><?php _e ( 'Description', 'dbem' ); ?></th>
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
								$now .= "now";
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
								<td>
									<p style="margin: 10px 0"><?php echo $event->location->name?></p>
<!-- 									<?php echo $event->location->address ?> - <?php echo $event->location->town; ?><br/> -->
									<?php echo $event->attributes['Location Details'] ?>
								</td>
<!--
								<td>
								</td>
-->
								<td class="event-group">
									<?php if ($event->group_id > 0) : ?>
										<a href="<?php echo bp_get_group_permalink($event_group) ?>"><?php echo bp_core_fetch_avatar('object=group&item_id='.$event_group->id) ?><br/>
										<small><?php echo $event_group->name ?></small></a>
									<?php else : ?>
										<small></small>
									<?php endif; ?>
								</td>
								<td class="event-title">
									<p><a class="row-title" href="/events?event_id=<?php echo $event->id ?>"><?php echo ($event->name); ?></a></p>
									<?php echo $event->output('#_CATEGORIES') ?>
									<?php 
									if( get_option('dbem_rsvp_enabled') == 1 && $event->rsvp == 1 ){
										?>
										<br/>
										<?php if( get_option('dbem_bookings_approval') == 1 ): ?>
											| <?php _e("Pending",'dbem') ?>: <?php echo $event->get_bookings()->get_pending_spaces(); ?>
										<?php endif;
									}
									
									?>
<!-- 									<?php echo $event->output('<p>#_EXCERPT</p>');  ?> -->
									<?php echo $event->is_recurrence() ? '<p class="recurrence-descr">' . $event->get_recurrence_description() . '</p>' : ''; ?>
									<?php if ((groups_is_user_admin(get_current_user_id(), $event->group_id) || groups_is_user_mod(get_current_user_id(), $event->group_id)) || get_current_user_id() == $event->id || current_user_can('edit_others_events') || current_user_can('delete_others_events')) : ?>
										<div class="event-actions">
										<?php if ((groups_is_user_admin(get_current_user_id(), $event->group_id) || groups_is_user_mod(get_current_user_id(), $event->group_id)) || get_current_user_id() == $event->id || current_user_can('edit_others_events')) : ?>
											&nbsp;<a class="button bp-secondary-action" href="<?php echo $url ?>edit/?event_id=<?php echo $event->id ?>" title="<?php _e ( 'Edit this event', 'dbem' ); ?>"><?php _e ( 'Edit', 'dbem' ); ?></a>
										<?php endif; ?>
										<?php if ((groups_is_user_admin(get_current_user_id(), $event->group_id) || groups_is_user_mod(get_current_user_id(), $event->group_id)) || get_current_user_id() == $event->id || current_user_can('delete_others_events')) : ?>
											<a class="button bp-secondary-action" href="<?php echo $url ?>edit/?action=event_duplicate&amp;event_id=<?php echo $event->id ?>" title="<?php _e ( 'Duplicate this event', 'dbem' ); ?>">Duplicate</a>
										<?php endif; ?>
										<?php if ((groups_is_user_admin(get_current_user_id(), $event->group_id) || groups_is_user_mod(get_current_user_id(), $event->group_id)) || get_current_user_id() == $event->id || current_user_can('delete_others_events')) : ?>
											<span class="trash">&nbsp;<a class="button bp-secondary-action" href="<?php echo $url ?>?action=event_delete&amp;event_id=<?php echo $event->id ?>" class="em-event-delete"  title="<?php _e ( 'Delete this event', 'dbem' ); ?>" onclick ="if( !confirm('Are you sure? This cannot be undone.') ){ return false; }"><?php _e('Delete','dbem'); ?></a></span>
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