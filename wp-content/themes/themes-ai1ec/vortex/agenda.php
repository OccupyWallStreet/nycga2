<h2 class="ai1ec-calendar-title"><?php echo esc_html( $title ); ?></h2>
<div class="ai1ec-title-buttons btn-toolbar">
	<div class="btn-group">
		<a id="ai1ec-today" class="ai1ec-load-view btn btn-mini"
			href="#action=ai1ec_agenda&amp;ai1ec_post_ids=<?php echo $post_ids; ?>">
			<?php _e( 'Today', AI1EC_PLUGIN_NAME ); ?>
		</a>
	</div>
	<?php if( $dates ): ?>
		<div class="btn-group">
			<a id="ai1ec-expand-all" class="btn btn-mini">
				<i class="icon-plus-sign"></i> <?php _e( 'Expand All', AI1EC_PLUGIN_NAME ) ?>
			</a><a
			id="ai1ec-collapse-all" class="btn btn-mini">
				<i class="icon-minus-sign"></i> <?php _e( 'Collapse All', AI1EC_PLUGIN_NAME ) ?>
			</a>
		</div>
	<?php endif; ?>
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
<ol class="ai1ec-agenda-view">
	<?php if( ! $dates ): ?>
		<p class="ai1ec-no-results">
			<?php _e( 'There are no upcoming events to display at this time.', AI1EC_PLUGIN_NAME ) ?>
		</p>
	<?php else: ?>
		<?php foreach( $dates as $timestamp => $date_info ): ?>
			<li class="ai1ec-date <?php if( isset( $date_info['today'] ) && $date_info['today'] ) echo 'ai1ec-today' ?>
				<?php if ( $show_year_in_agenda_dates ) echo 'ai1ec-agenda-plus-year' ?>">
				<div class="ai1ec-date-title">
					<div class="ai1ec-month"><?php echo date_i18n( 'M', $timestamp, true ) ?></div>
					<div class="ai1ec-day"><?php echo date_i18n( 'j', $timestamp, true ) ?></div>
					<div class="ai1ec-weekday"><?php echo date_i18n( 'D', $timestamp, true ) ?></div>
					<?php if ( $show_year_in_agenda_dates ): ?>
						<div class="ai1ec-year"><?php echo date_i18n( 'Y', $timestamp, true ) ?></div>
					<?php endif; ?>
				</div>
				<ol class="ai1ec-date-events">
					<?php foreach( $date_info['events'] as $category ): ?>
						<?php foreach( $category as $event ): ?>
							<li class="ai1ec-event
								ai1ec-event-id-<?php echo $event->post_id ?>
								ai1ec-event-instance-id-<?php echo $event->instance_id ?>
								<?php if( $event->allday ) echo 'ai1ec-allday' ?>
								<?php if( $event->post_id == $active_event ) echo 'ai1ec-active-event' ?>
								<?php if( $expanded ) echo 'ai1ec-expanded' ?>">

								<div class="ai1ec-event-title">
									<div class="ai1ec-event-click">
										<?php echo esc_html( apply_filters( 'the_title', $event->post->post_title ) ) ?>
										<?php if ( $show_location_in_title && isset( $event->venue ) && $event->venue != '' ): ?>
											<span class="ai1ec-event-location"><?php echo sprintf( __( '@ %s', AI1EC_PLUGIN_NAME ), $event->venue ); ?></span>
										<?php endif; ?>
										<div class="ai1ec-event-time">
											<?php if( $event->allday ): ?>
												<span class="ai1ec-allday-label">
													<?php echo esc_html( $event->short_start_date ) ?>
													<?php if( $event->short_end_date != $event->short_start_date ): ?>
														– <?php echo esc_html( $event->short_end_date ) ?>
														<?php if( $event->allday ): ?>
														<?php endif ?>
													<?php endif ?>
													<?php _e( ' (all-day)', AI1EC_PLUGIN_NAME ) ?>
												</span>
											<?php else: ?>
												<?php echo esc_html( $event->start_time . ' – ' . $event->end_time ) ?></span>
											<?php endif ?>
										</div>
										<div class="ai1ec-event-expand">
											<?php if( $expanded): ?>
												<i class="icon-minus-sign icon-large"></i>
											<?php else: ?>
												<i class="icon-plus-sign icon-large"></i>
											<?php endif ?>
										</div>
									</div>
								</div>

								<?php // Insert post ID for use by JavaScript filtering later ?>
								<input type="hidden" class="ai1ec-post-id" value="<?php echo $event->post_id ?>" />

								<?php // Hidden summary, until clicked ?>
								<div class="ai1ec-event-summary<?php if( $expanded ) echo ' ai1ec-expanded'; ?>">

									<div class="ai1ec-event-description">
										<?php echo apply_filters( 'the_content', $event->post->post_content ) ?>

										<div class="ai1ec-event-overlay">
											<a class="ai1ec-read-more btn"
												href="<?php echo esc_attr( get_permalink( $event->post_id ) . $event->instance_id ) ?>">
												<?php _e( 'Read more', AI1EC_PLUGIN_NAME ) ?> <i class="icon-arrow-right"></i>
											</a>
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
								</div>


							</li>
						<?php endforeach ?>
					<?php endforeach ?>
				</ol>
			</li>
		<?php endforeach ?>
	<?php endif ?>
</ol>
