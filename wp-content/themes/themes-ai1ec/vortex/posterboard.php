<h2 class="ai1ec-calendar-title"><?php echo esc_html( $title ); ?></h2>
<div class="ai1ec-title-buttons btn-toolbar">
	<div class="btn-group">
		<a id="ai1ec-today" class="ai1ec-load-view btn btn-mini"
			href="#action=ai1ec_posterboard&amp;ai1ec_post_ids=<?php echo $post_ids; ?>">
			<?php _e( 'Today', AI1EC_PLUGIN_NAME ); ?>
		</a>
	</div>
	<div class="ai1ec-pagination btn-group pull-right">
		<?php foreach( $pagination_links as $link ): ?>
			<a id="<?php echo $link['id']; ?>"
				class="ai1ec-load-view btn"
				href="<?php echo esc_attr( $link['href'] ); ?>&amp;ai1ec_post_ids=<?php echo $post_ids; ?>">
				<?php echo esc_html( $link['text'] ); ?>
			</a>
		<?php endforeach; ?>
	</div>
</div>
<ol class="ai1ec-posterboard-view">
	<?php if( ! $dates ): ?>
		<p class="ai1ec-no-results">
			<?php _e( 'There are no upcoming events to display at this time.', AI1EC_PLUGIN_NAME ) ?>
		</p>
	<?php else: ?>
		<?php foreach( $dates as $timestamp => $date_info ): ?>
			<?php foreach( $date_info['events'] as $category ): ?>
				<?php foreach( $category as $event ): ?>
					<li class="ai1ec-event
						ai1ec-event-id-<?php echo $event->post_id ?>
						ai1ec-event-instance-id-<?php echo $event->instance_id ?>
						<?php if( $event->allday ) echo 'ai1ec-allday' ?>
						<?php if( $event->post_id == $active_event ) echo 'ai1ec-active-event' ?>">
						<div class="ai1ec-event-wrap">
							<?php // Insert post ID for use by JavaScript filtering later ?>
							<input type="hidden" class="ai1ec-post-id" value="<?php echo $event->post_id ?>" />

							<?php // Event summary ?>
							<div class="ai1ec-event-summary">
								<div class="ai1ec-date-block-wrap" <?php echo $event->category_bg_color ?>>
									<div class="ai1ec-month"><?php echo date_i18n( 'M', $timestamp, true ) ?></div>
									<div class="ai1ec-day"><?php echo date_i18n( 'j', $timestamp, true ) ?></div>
								</div>
								<div class="ai1ec-event-title">
									<a href="<?php echo esc_attr( get_permalink( $event->post_id ) . $event->instance_id ) ?>" <?php echo $event->category_text_color ?>>
										<?php echo esc_html( apply_filters( 'the_title', $event->post->post_title ) ) ?>

									</a>
									<?php if ( $show_location_in_title && isset( $event->venue ) && $event->venue != '' ): ?>
										<span class="ai1ec-event-location"><?php echo sprintf( __( '@ %s', AI1EC_PLUGIN_NAME ), $event->venue ); ?></span>
									<?php endif; ?>
								</div>
								<div class="ai1ec-event-time">
									<span class="ai1ec-weekday"><?php echo date_i18n( 'l', $timestamp, true ) ?>:</span>
									<?php if( $event->allday ): ?>
										<?php echo esc_html( $event->short_start_date ) ?>
										<?php if( $event->short_end_date != $event->short_start_date ): ?>
											– <?php echo esc_html( $event->short_end_date ) ?>
										<?php endif ?>
										<?php echo ' (all day)' ?>
									<?php else: ?>
										<?php echo esc_html( $event->start_time . ' – ' . $event->end_time ) ?></span>
									<?php endif ?>
								</div>
								<div class="ai1ec-event-description">
									<?php echo apply_filters( 'the_content', $event->post->post_content ) ?>
								</div>
								<?php if( $event->categories_html ): ?>
									<div class="ai1ec-categories">
										<span class="ai1ec-label"><?php _e( 'Categories:', AI1EC_PLUGIN_NAME ) ?></span>
										<?php echo $event->categories_html ?>
									</div>
								<?php endif ?>
								<?php if( $event->tags_html ): ?>
									<div class="ai1ec-tags">
										<span class="ai1ec-label"><?php _e( 'Tags:', AI1EC_PLUGIN_NAME ) ?></span>
										<?php echo $event->tags_html ?>
									</div>
								<?php endif ?>
							</div>
						</div>
					</li>
				<?php endforeach ?>
			<?php endforeach ?>
		<?php endforeach ?>
	<?php endif ?>
</ol>
