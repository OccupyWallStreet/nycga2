<?php
global $blog_id, $wp_query, $booking, $post, $current_user;
$event = new Eab_EventModel($post);
get_header();
?>
       
        
<?php
	the_post();
	$start_day = date_i18n('m', strtotime(get_post_meta($post->ID, 'incsub_event_start', true)));
?>
<div id="post-entry">

			
<div class="event <?php echo Eab_Template::get_status_class($post); ?>" id="wpmudevevents-wrapper">
	<div id="wpmudevents-single">
		<div class="wpmudevevents-header">
			<h2><?php echo $event->get_title(); ?></h2><br />
		<div class="wpmudevevents-contentmeta" style="clear:both">
				<div id="event-share">
					<a class="button iCal" href="">iCal</a>
					<a class="button tweet" href="">Tweet</a>
					<a class="button like" href="">Like</a>
					<a class="button share" href="">Share</a>
				</div>
			 <?php echo Eab_Template::get_event_details($event); ?>
		</div>
				<p><a href="">Actions</a>, <a href="">Specials Events</a></p>
		</div>
		<?php echo Eab_Template::get_error_notice(); ?>
		<div id="wpmudevevents-left">	
			<div id="wpmudevevents-tickets" class="wpmudevevents-box">
				<?php
                    	if ($event->is_premium() && $event->user_is_coming() && !$event->user_paid()) { 
                    ?>
					<div id="wpmudevevents-payment">
						<a href="" id="wpmudevevents-notpaid-submit">You haven't paid for this event</a>
					</div>
					<?php echo Eab_Template::get_payment_forms($event); ?>
					<?php } ?>
			</div>
			<div id="wpmudevevents-content" class="wpmudevevents-box">
				<div class="wpmudevevents-boxheader">
					<h3>About this event</h3>
				</div>
					<div class="wpmudevevents-boxinner">
					<div class="event-thumbnail"><?php the_post_thumbnail(); ?></div>
					<?php 
						add_filter('agm_google_maps-options', 'eab_autoshow_map_off', 99);
						the_content();
						remove_filter('agm_google_maps-options', 'eab_autoshow_map_off');
					?>
					</div>
					<div><?php echo Eab_Template::get_inline_rsvps($event); ?></div>
			</div>
		</div>
		<div id="wpmudevevents-right">
			<!-- Attending Buttons -->
			<div id="wpmudevevents-attending" class="wpmudevevents-box">
				<?php echo Eab_Template::get_rsvp_form($event); ?>
			</div>
			<!-- Contact Information -->
			<div id="wpmudevevents-host" class="wpmudevevents-box">
				<div class="wpmudevevents-boxheader">
					<h3>Contact</h3>			
				</div>
				<div class="wpmudevevents-boxinner">
					<p><a href="<?php echo bp_core_get_user_domain( get_the_author_meta( 'ID' ) ) ?>" title="<?php echo bp_core_get_user_displayname( get_the_author_meta( 'ID' ) ) ?>"><?php the_author_meta('display_name'); ?></a>, <a href="">TechOps</a></p>
					<p></p>
					<p>
					<?php the_author_meta('description'); ?>

					<? $args=array(
					  'author' => get_the_author('ID'),
					  'post_type' => 'incsub_event',
					  'post_status' => 'publish',
					  'posts_per_page' => 1,
					  'caller_get_posts'=> 3,
					  'offset'=> 1
					);
					$my_query = null;
					$my_query = new WP_Query($args);
					if( $my_query->have_posts() ) { 
					  while ($my_query->have_posts()) : $my_query->the_post(); ?>
					    <p><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></p>
					    <?php
					  endwhile;
					}
					wp_reset_query();   // Restore global post data stomped by the_post().  ?>
					
					<h3>Other Member Events</h3>
					<ul>
						<li>Fri., August 3, 2012 @ 7:00pm - <a href="/events/2012/08/sunset-park-food-for-thought-film-series-broken-on-all-sides/">Sunset Park Food For Thought Film Series: Broken on All Sides</a></li>
					</ul>

				</div>
			</div>
			<!-- Map -->
			<?php if ($event->has_venue_map()) { ?>
			<div id="wpmudevevents-googlemap" class="wpmudevevents-box">
				<div class="wpmudevevents-boxheader">
					<h3>Google Map</h3>
				</div>
					<div class="wpmudevevents-boxinner">
					<?php echo $event->get_venue_location(Eab_EventModel::VENUE_AS_MAP, array('width' => '99%')); ?>
					</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>

<div style="clear:both"><?php comments_template( '', true ); ?></div>

	</div>

        
<?php get_footer('event'); ?>