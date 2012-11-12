<h2 class="ai1ec-calendar-title"><?php echo esc_html( $title ); ?></h2>
<div class="ai1ec-title-buttons btn-toolbar">
  <div class="btn-group">
  	<a id="ai1ec-today" class="ai1ec-load-view btn btn-mini"
      href="#action=ai1ec_month&amp;ai1ec_post_ids=<?php echo $post_ids; ?>">
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
<table class="ai1ec-month-view">
	<thead>
		<tr>
			<?php foreach( $weekdays as $weekday ): ?>
				<th class="ai1ec-weekday"><?php echo $weekday; ?></th>
			<?php endforeach; // weekday ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach( $cell_array as $week ): ?>
			<tr class="ai1ec-week">
				<?php foreach( $week as $day ): ?>
					<?php if( $day['date'] ): ?>
						<td <?php if( $day['today'] ) echo 'class="ai1ec-today"' ?>>

              <?php
                // TODO: This div should not be needed, but with multi-day
                // event bars it is required until a better method of arranging
                // events is contrived:
              if( ! isset( $week['added_stretcher'] ) ): ?>
                <div class="ai1ec-day-stretcher"></div>
                <?php $week['added_stretcher'] = TRUE; ?>
              <?php endif; ?>

							<div class="ai1ec-day">
								<div class="ai1ec-date"><?php echo $day['date'] ?></div>
								<?php foreach ( $day['events'] as $event ): ?>
									<a href="<?php echo esc_attr( get_permalink( $event->post_id ) ) . $event->instance_id ?>"
										<?php if( $event->multiday ) : ?>
											data-end-day="<?php echo $event->multiday_end_day; ?>"
											data-start-truncated="<?php echo $event->start_truncated ? 'true' : 'false'; ?>"
											data-end-truncated="<?php echo $event->end_truncated ? 'true' : 'false'; ?>"
										<?php endif; ?>
										class="ai1ec-event-container
											ai1ec-popup-summary-parent
											ai1ec-event-id-<?php echo $event->post_id ?>
											ai1ec-event-instance-id-<?php echo $event->instance_id ?>
											<?php if ( $event->allday ) echo 'ai1ec-allday' ?>
											<?php if ( $event->multiday ) echo 'ai1ec-multiday' ?>">

										<?php // Insert post ID for use by JavaScript filtering later ?>
										<input type="hidden" class="ai1ec-post-id" value="<?php echo $event->post_id ?>" />

										<div class="ai1ec-event <?php if( $event->post_id == $active_event ) echo 'ai1ec-active-event' ?>" style="<?php echo $event->color_style ?>">
											<span class="ai1ec-event-title"><?php echo esc_html( apply_filters( 'the_title', $event->post->post_title ) ) ?></span>
											<?php if( ! $event->allday ): ?>
												<span class="ai1ec-event-time"><?php echo esc_html( $event->short_start_time ) ?></span>
											<?php endif ?>
										</div>

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
								<?php endforeach // events ?>
							</div>
						</td>
					<?php else: ?>
						<td class="ai1ec-empty"></td>
					<?php endif // date ?>
				<?php endforeach // day ?>
			</tr>
		<?php endforeach // week ?>
	</tbody>
</table>
