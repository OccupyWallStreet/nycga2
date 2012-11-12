<?php echo $args['before_widget'] ?>

<?php if( $title ): ?>
	<?php echo $before_title . $title . $after_title; ?>
<?php endif; ?>

<div class="timely ai1ec-agenda-widget-view">

	<?php if( ! $dates ): ?>
		<p class="ai1ec-no-results">
			<?php _e( 'There are no upcoming events.', AI1EC_PLUGIN_NAME ); ?>
		</p>
	<?php else: ?>
		<ol>
			<?php foreach( $dates as $timestamp => $date_info ): ?>
				<li class="ai1ec-date <?php if( isset( $date_info['today'] ) && $date_info['today'] ) echo 'ai1ec-today'; ?>">
					<div class="ai1ec-date-title">
						<div class="ai1ec-month"><?php echo date_i18n( 'M', $timestamp, true ); ?></div>
						<div class="ai1ec-day"><?php echo date_i18n( 'j', $timestamp, true ); ?></div>
						<div class="ai1ec-weekday"><?php echo date_i18n( 'D', $timestamp, true ); ?></div>
						<?php if ( $show_year_in_agenda_dates ): ?>
							<div class="ai1ec-year"><?php echo date_i18n( 'Y', $timestamp, true ) ?></div>
						<?php endif; ?>
					</div>
					<ol class="ai1ec-date-events">
						<?php foreach( $date_info['events'] as $category ): ?>
							<?php foreach( $category as $event ): ?>
								<li class="ai1ec-event
									ai1ec-event-id-<?php echo $event->post_id; ?>
									ai1ec-event-instance-id-<?php echo $event->instance_id; ?>
									<?php if( $event->allday ) echo 'ai1ec-allday'; ?>">

									<?php // Insert post ID for use by JavaScript filtering later ?>
									<input type="hidden" class="ai1ec-post-id" value="<?php echo $event->post_id; ?>" />

									<a href="<?php echo esc_attr( get_permalink( $event->post_id ) ) . $event->instance_id; ?>"
										class="ai1ec-popup-summary-parent">
										<?php if( ! $event->allday ): ?>
											<span class="ai1ec-event-time">
												<?php echo esc_html( $event->start_time ); ?></span>
											</span>
										<?php endif; ?>
										<span class="ai1ec-event-title">
											<?php echo esc_html( apply_filters( 'the_title', $event->post->post_title ) ); ?>
											<?php if ( $show_location_in_title && isset( $event->venue ) && $event->venue != '' ): ?>
												<span class="ai1ec-event-location"><?php echo sprintf( __( '@ %s', AI1EC_PLUGIN_NAME ), $event->venue ); ?></span>
											<?php endif; ?>
										</span>
										<?php if( $event->allday ): ?>
											<span class="ai1ec-allday-label">
												<?php _e( 'All Day', AI1EC_PLUGIN_NAME ) ?>
											</span>
										<?php endif; ?>
										<?php if( $event->category_colors ): ?>
											<span class="ai1ec-category-colors"><?php echo $event->category_colors; ?></span>
										<?php endif; ?>

										<div class="ai1ec-popup-summary-wrap">
											<div class="ai1ec-popup-summary">

												<?php if( $event->category_colors ): ?>
												  <div class="ai1ec-category-colors"><?php echo $event->category_colors ?></div>
												<?php endif ?>

												<span class="ai1ec-popup-title">
												  <?php if( function_exists( 'mb_strimwidth' ) ) : ?>
												    <?php echo esc_html( mb_strimwidth( apply_filters( 'the_title', $event->post->post_title ), 0, 35, '...' ) ) ?></span>
												  <?php else : ?>
												    <?php $read_more = strlen( apply_filters( 'the_title', $event->post->post_title ) ) > 35 ? '...' : '' ?>
													<?php echo esc_html( substr( apply_filters( 'the_title', $event->post->post_title ), 0, 35 ) . $read_more );  ?>
												  <?php endif; ?>
													<?php if ( $show_location_in_title && isset( $event->venue ) && $event->venue != '' ): ?>
														<span class="ai1ec-event-location"><?php echo esc_html( sprintf( __( '@ %s', AI1EC_PLUGIN_NAME ), $event->venue ) ); ?></span>
													<?php endif; ?>
												</span>
												<?php if( ! $event->allday ): ?>
													<div class="ai1ec-event-time"><?php echo esc_html( $event->short_start_time ) ?></div>
												<?php endif ?>
												<?php if( $event->allday ): ?>
													<div><small><?php esc_html_e( '(all-day)', AI1EC_PLUGIN_NAME ) ?></small></div>
												<?php endif ?>

												<?php if( $event->post_excerpt ): ?>
													<p class="ai1ec-popup-excerpt"><?php echo esc_html( $event->post_excerpt ) ?></p>
												<?php endif ?>

											</div><!-- .ai1ec-popup-summary -->
										</div><!-- .ai1ec-popup-summary-wrap -->

									</a>

								</li>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</ol>
				</li>
			<?php endforeach; ?>
		</ol>
	<?php endif; ?>

  <?php if( $show_calendar_button || $show_subscribe_buttons ): ?>
    <p>
    	<?php if( $show_calendar_button ): ?>
    		<a class="btn btn-mini pull-right ai1ec-calendar-link" href="<?php echo $calendar_url; ?>">
    			<?php _e( 'View Calendar', AI1EC_PLUGIN_NAME ); ?>
          <i class="icon-arrow-right"></i>
    		</a>
    	<?php endif; ?>

    	<?php if( $show_subscribe_buttons ): ?>
    		<span class="ai1ec-subscribe-buttons pull-left">
    			<a class="btn btn-mini ai1ec-subscribe"
    				href="<?php echo $subscribe_url; ?>"
    				title="<?php _e( 'Subscribe to this calendar using your favourite calendar program (iCal, Outlook, etc.)', AI1EC_PLUGIN_NAME ); ?>" />
    				<?php _e( '✔ Subscribe', AI1EC_PLUGIN_NAME ); ?>
    			</a>
    			<a class="btn btn-mini ai1ec-subscribe-google" target="_blank"
    				href="http://www.google.com/calendar/render?cid=<?php echo urlencode( str_replace( 'webcal://', 'http://', $subscribe_url ) ); ?>"
    				title="<?php _e( 'Subscribe to this calendar in your Google Calendar', AI1EC_PLUGIN_NAME ); ?>" />
    				<img src="<?php echo $this->get_theme_img_url( 'google-calendar.png' ); ?>" />
    				<?php _e( 'Add to Google', AI1EC_PLUGIN_NAME ); ?>
    			</a>
    		</span>
    	<?php endif; ?>
    </p>
  <?php endif; ?>

</div>

<?php echo $args['after_widget']; ?>
