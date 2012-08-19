<table class="timely ai1ec-event ai1ec-multi-event ai1ec-event-id-<?php echo $event->post_id ?> <?php if( $event->multiday ) echo 'ai1ec-multiday' ?> <?php if( $event->allday ) echo 'ai1ec-allday' ?>">
	<tbody>
		<tr>
			<th scope="row" class="ai1ec-time"><?php _e( 'When:', AI1EC_PLUGIN_NAME ); ?></th>
			<td class="ai1ec-time">
				<a class="ai1ec-calendar-link btn btn-mini pull-right" href="<?php echo $calendar_url; ?>">
					<?php _e( 'View in Calendar »', AI1EC_PLUGIN_NAME ); ?>
				</a>
				<?php echo $event->timespan_html; ?>
			</td>
		</tr>
		<?php if( $recurrence ): ?>
			<tr>
				<th scope="row" class="ai1ec-recurrence"><?php _e( 'Repeats:', AI1EC_PLUGIN_NAME ); ?></th>
				<td class="ai1ec-recurrence" colspan="2"><?php echo $recurrence ?></td>
			</tr>
		<?php endif; ?>
		<tr>
			<?php if( $location ): ?>
				<th scope="row" class="ai1ec-location"><?php _e( 'Where:', AI1EC_PLUGIN_NAME ); ?></th>
				<td class="ai1ec-location">
					<?php if( $event->show_map ): ?>
						<a class="btn ai1ec-gmap-link" href="<?php the_permalink(); ?>#ai1ec-event">
							<?php _e( 'View Map »', AI1EC_PLUGIN_NAME ); ?>
						</a>
					<?php endif; ?>
					<?php echo $location; ?>
				</td>
			<?php endif; ?>
		</tr>
		<tr>
			<?php if( $event->cost ): ?>
				<th scope="row" class="ai1ec-cost"><?php _e( 'Cost:', AI1EC_PLUGIN_NAME ); ?></th>
				<td class="ai1ec-cost"><?php echo esc_html( $event->cost ); ?></td>
			<?php endif; ?>
		</tr>
		<tr>
			<?php if( $contact ): ?>
				<th scope="row" class="ai1ec-contact"><?php _e( 'Contact:', AI1EC_PLUGIN_NAME ); ?></th>
				<td class="ai1ec-contact"><?php echo $contact; ?></td>
			<?php endif; ?>
		</tr>
		<tr>
			<?php if( $categories ): ?>
				<th scope="row" class="ai1ec-categories"><?php _e( 'Categories:', AI1EC_PLUGIN_NAME ); ?></th>
				<td class="ai1ec-categories"><?php echo $categories; ?></td>
			<?php endif; ?>
		</tr>
		<tr>
			<?php if( $tags ): ?>
				<th scope="row" class="ai1ec-tags"><?php _e( 'Tags:', AI1EC_PLUGIN_NAME ); ?></th>
				<td class="ai1ec-tags"><?php echo $tags; ?></td>
			<?php endif; ?>
		</tr>
	</tbody>
</table>
