<div class="ai1ec-event-footer">
	<?php if( $event->ical_feed_url ): ?>
		<p class="ai1ec-source-link">
			<?php echo sprintf( __( 'This post was replicated from another site\'s <a class="ai1ec-ics-icon" href="%s" title="iCalendar feed">calendar feed</a>.' ),
				esc_attr( str_replace( 'http://', 'webcal://', $event->ical_feed_url ) ) ) ?>
			<?php if( $event->ical_source_url ): ?>
				<a href="<?php echo esc_attr( $event->ical_source_url ) ?>" target="_blank">
					<?php _e( 'View original post', AI1EC_PLUGIN_NAME ) ?>
          <i class="icon-external-link"></i>
				</a>
			<?php endif ?>
		</p>
	<?php endif ?>
</div>
