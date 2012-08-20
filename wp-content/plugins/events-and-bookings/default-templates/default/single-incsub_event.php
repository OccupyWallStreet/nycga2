<?php
global $blog_id, $wp_query, $booking, $post, $current_user;
$event = new Eab_EventModel($post);
get_header();
?>
          
<?php
	the_post();
	$start_day = date_i18n('m', strtotime(get_post_meta($post->ID, 'incsub_event_start', true)));
?>

<div class="event <?php echo Eab_Template::get_status_class($post); ?>" id="wpmudevevents-wrapper">
	<div id="wpmudevents-single">
		<div class="wpmudevevents-header">
			<h2><?php echo $event->get_title(); ?></h2>
			<div class="wpmudevevents-contentmeta" style="clear:both">
			<div id="event-bread-crumbs" ><?php Eab_Template::get_breadcrumbs($event); ?></div>
			<div id="event-management">
			<!-- If user can edit this event, show button -->
			<?php
				if ( current_user_can('edit_post', $post->ID) ) {
				 echo '<a href="http://www.nycga.net/edit-event?event_id=' . $post->ID . '" class="edit-event button">Edit Event</a>';
				}
			?>
			<!-- If user can create an event, show button -->
			<?php
				if ( current_user_can('create_events') ) {
				 echo '<a href="http://www.nycga.net/edit-event" class="add-event button">Create Event</a>';
				}
			?>
			</div>
			<!-- Buttons should be styled -->
			<div id="event-share">
				<a class="button iCal" href="<?php echo get_permalink($post); ?>?eab_format=ical&attachment">iCal</a>
				<a class="button tweet" href="">Tweet</a>
				<a class="button like" href="">Like</a>
				<a class="button share" href="">Share</a>					
			</div>
			<?php echo Eab_Template::get_event_details($event); ?>
      <p style="clear:both;">
      <!-- Show the categories -->
		  <?php
		  $categories = get_terms('eab_events_category');
		  $output_categories = array();
		  foreach ($categories as $category):
			  echo '<a href="/events/' . $category->slug . '">' . $category->name . '</a> ';
		  endforeach; ?>
		  </p>
			</div>
		</div>
		<?php echo Eab_Template::get_error_notice(); ?>
		<div id="wpmudevevents-left">	
			<div id="wpmudevevents-tickets" class="wpmudevevents-box">
				<?php if ($event->is_premium() && $event->user_is_coming() && !$event->user_paid()) { ?>
					<div id="wpmudevevents-payment">
						<a href="" id="wpmudevevents-notpaid-submit">You haven't paid for this event</a>
					</div>
					<?php echo Eab_Template::get_payment_forms($event); ?>
					<?php } ?>
			</div>
				<div class="wpmudevevents-boxinner">
					<!-- <div class="event-thumbnail"><?php the_post_thumbnail(); ?></div> -->
					<?php 
						add_filter('agm_google_maps-options', 'eab_autoshow_map_off', 99);
						the_post_thumbnail(array('class' => 'event-thumbnail'));
						the_content();
						remove_filter('agm_google_maps-options', 'eab_autoshow_map_off');
					?>
				</div>
				<?php wdpv_vote(); ?>
			<!-- </div> -->
			<!-- Attending Buttons -->
			<div id="wpmudevevents-attending" class="wpmudevevents-box">
				<div class="wpmudevevents-boxheader">
					<h3>RSVP For This Event</h3>
				</div>
				<div class="rvsp-buttons"><?php echo Eab_Template::get_rsvp_form($event); ?></div>
				<div class="rvsp-list"><?php echo Eab_Template::get_inline_rsvps($event); ?></div>
			</div>
		</div>
		<div id="wpmudevevents-right">
			<!-- Contact Information -->
			<div id="wpmudevevents-host" class="wpmudevevents-box">
				<div class="wpmudevevents-boxheader">
					<h3>Contact</h3>			
				</div>
				<div class="wpmudevevents-boxinner">
					<p><a href="<?php echo bp_core_get_user_domain( get_the_author_meta( 'ID' ) ) ?>" title="<?php echo bp_core_get_user_displayname( get_the_author_meta( 'ID' ) ) ?>"><?php the_author_meta('display_name'); ?></a>
					<!-- If it's a group event, show the group -->
					<?php
					global $post;
					$group_id = get_post_meta($post->ID, 'eab_event-bp-group_event', true);
					if ($group_id) {
						$group = groups_get_group(array('group_id' => $group_id));
						if ($group) {
							echo '<br /> ';
							echo '<a href="/' . $bp->groups->slug . '/' . $group->slug . '/group-events/">' . $group->name . '</a>';
							// Would like to list over event from same group
							echo ' <div class="alert">If this event is a group event, other events from the group should show up here.</div>';
							}
					}
					?>
					</p>
				</div>
			</div>

			<!-- Map -->
			<?php if ($event->has_venue_map()) { ?>
			<div id="wpmudevevents-googlemap" class="wpmudevevents-box">
				<!-- Would like to put this in a overlay or hide until clicked -->
				<div class="wpmudevevents-boxheader">
					<h3>Map</h3>
				</div>
					<div class="wpmudevevents-boxinner">
					<?php echo $event->get_venue_location(Eab_EventModel::VENUE_AS_MAP, array('width' => '99%')); ?>
					</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>

<div><?php comments_template( '', true ); ?></div>

        
<?php get_footer('event'); ?>