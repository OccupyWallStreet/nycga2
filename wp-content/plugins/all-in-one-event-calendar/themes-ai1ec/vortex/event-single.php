<a name="ai1ec-event"></a>
<table class="timely ai1ec-full-event ai1ec-single-event ai1ec-event-id-<?php echo $event->post_id ?> <?php if( $event->multiday ) echo 'ai1ec-multiday' ?> <?php if( $event->allday ) echo 'ai1ec-allday' ?>">
	<tbody>
		<tr>
			<th scope="row" class="ai1ec-time"><?php _e( 'When:', AI1EC_PLUGIN_NAME ) ?></th>
			<td colspan="2" class="ai1ec-time">
				<a class="ai1ec-calendar-link btn pull-right" href="<?php echo $calendar_url ?>">
					<i class="icon-arrow-left"></i> <?php _e( 'Back to Calendar', AI1EC_PLUGIN_NAME ) ?>
				</a>
				<?php echo $event->timespan_html ?>
			</td>
		</tr>
		<?php if( $recurrence ): ?>
			<tr>
				<th scope="row" class="ai1ec-recurrence"><?php _e( 'Repeats:', AI1EC_PLUGIN_NAME ) ?></th>
				<td class="ai1ec-recurrence" colspan="2"><?php echo $recurrence ?></td>
			</tr>
		<?php endif ?>
		<?php if( $exclude ): ?>
			<tr>
				<th scope="row" class="ai1ec-exclude"><?php _e( 'Excluding:', AI1EC_PLUGIN_NAME ) ?></th>
				<td class="ai1ec-exclude" colspan="2"><?php echo $exclude ?></td>
			</tr>
		<?php endif ?>
			<th scope="row" class="ai1ec-location <?php if( ! $location ) echo 'ai1ec-empty' ?>"><?php if( $location ) _e( 'Where:', AI1EC_PLUGIN_NAME ) ?></th>
			<td class="ai1ec-location <?php if( ! $location ) echo 'ai1ec-empty' ?>"><?php echo $location ?></td>
			<td rowspan="5" class="ai1ec-map <?php if( $map ) echo 'ai1ec-has-map' ?>">
				<?php echo $map ?>
				<?php if( $show_subscribe_buttons ) : ?>
  				<a class="btn btn-small ai1ec-subscribe"
  					href="<?php echo esc_attr( $subscribe_url ) ?>"
  					title="<?php _e( 'Add this event to your favourite calendar program (iCal, Outlook, etc.)', AI1EC_PLUGIN_NAME ) ?>" />
  					<?php _e( '✔ Add to Calendar', AI1EC_PLUGIN_NAME ) ?></a>
  				<a class="btn btn-small ai1ec-subscribe-google" target="_blank"
  					href="<?php echo esc_attr( $google_url ) ?>"
  					title="<?php _e( 'Add this event to your Google Calendar', AI1EC_PLUGIN_NAME ) ?>" />
  					<img src="<?php echo $this->get_theme_img_url( 'google-calendar.png' ) ?>" />
  					<?php _e( 'Add to Google Calendar', AI1EC_PLUGIN_NAME ) ?>
  				</a>
				<?php endif ?>
			</td>
		</tr>
		<tr>
			<?php if( $event->cost ): ?>
				<th scope="row" class="ai1ec-cost"><?php _e( 'Cost:', AI1EC_PLUGIN_NAME ) ?></th>
				<td class="ai1ec-cost"><?php echo esc_html( $event->cost ) ?></td>
			<?php endif ?>
		</tr>
		<tr>
			<?php if( $contact ): ?>
				<th scope="row" class="ai1ec-contact"><?php _e( 'Contact:', AI1EC_PLUGIN_NAME ) ?></th>
				<td class="ai1ec-contact"><?php echo $contact ?></td>
			<?php endif ?>
		</tr>
		<tr>
			<?php if( $categories ): ?>
				<th scope="row" class="ai1ec-categories"><?php _e( 'Categories:', AI1EC_PLUGIN_NAME ) ?></th>
				<td class="ai1ec-categories"><?php echo $categories ?></td>
			<?php endif ?>
		</tr>
		<tr>
			<th scope="row" class="ai1ec-tags">
				<?php if( $tags ): ?>
					<i class="icon-tags"></i> <?php _e( 'Tags:', AI1EC_PLUGIN_NAME ) ?>
				<?php endif ?>
			</th>
			<td class="ai1ec-tags"><?php echo $tags ?></td>
		</tr>
	</tbody>
</table>
