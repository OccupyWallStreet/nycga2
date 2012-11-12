<?php
global $blog_id, $wp_query, $booking, $post, $current_user;
$event = new Eab_EventModel($post);
get_header();
?>
       
        
<?php
	the_post();
	$start_day = date_i18n('m', strtotime(get_post_meta($post->ID, 'incsub_event_start', true)));
?>
	<div id="primary">
		<div id="content" role="main" class="full-width">
			
<div class="event <?php echo Eab_Template::get_status_class($post); ?>" id="wpmudevevents-wrapper">
	<div id="wpmudevents-single">
		<div class="wpmudevevents-header">
			<h2><?php echo $event->get_title(); ?></h2>
			<div id="event-bread-crumbs" ><?php Eab_Template::get_breadcrumbs($event); ?></div>
			<div class="wpmudevevents-contentmeta" style="clear:both">
				<?php echo Eab_Template::get_event_details($event); ?>
			</div>
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
					<h3>About this event :</h3>
				</div>
					<div class="wpmudevevents-boxinner">
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
			<div id="wpmudevevents-attending" class="wpmudevevents-box">
				<?php echo Eab_Template::get_rsvp_form($event); ?>
			</div>
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
			<div id="wpmudevevents-host" class="wpmudevevents-box">
				<div class="wpmudevevents-boxheader">
				<h3>Your host : <?php the_author_meta('display_name'); ?></h3>
				</div>
					<div class="wpmudevevents-boxinner">
					<p>
						<?php the_author_meta('description'); ?>
					</p>
					</div>
			</div>
		</div>
	</div>
</div>

<div style="clear:both"><?php comments_template( '', true ); ?></div>

		</div>
	</div>
        
        
<?php get_footer('event'); ?>