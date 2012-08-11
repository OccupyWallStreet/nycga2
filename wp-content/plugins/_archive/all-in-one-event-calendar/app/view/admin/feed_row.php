<div class="ai1ec-feed-container">
	<h4 class="ai1ec_feed_h4">
		<?php _e( 'iCalendar/.ics Feed URL:', AI1EC_PLUGIN_NAME ); ?>
	</h4>
	<div class="ai1ec-feed-url"><input type="text" class="ai1ec-feed-url" readonly="readonly" value="<?php echo esc_attr( $feed_url ) ?>" /></div>
	<input type="hidden" name="feed_id" class="ai1ec_feed_id" value="<?php echo $feed_id;?>" />
	<?php if( $event_category ): ?>
		<div class="ai1ec-feed-category">
			<?php _e( 'Event category:', AI1EC_PLUGIN_NAME ); ?>
			<strong><?php echo $event_category; ?></strong>
		</div>
	<?php endif ?>
	<?php if( $tags ): ?>
		<div class="ai1ec-feed-tags">
			<?php _e( 'Tag with', AI1EC_PLUGIN_NAME ); ?>:
			<strong><?php echo $tags; ?></strong>
		</div>
	<?php endif ?>
	<input type="button" class="button ai1ec_delete_ics" value="<?php _e( '× Delete', AI1EC_PLUGIN_NAME ); ?>" />
	<input type="button" class="button ai1ec_update_ics" value="<?php _e( 'Update', AI1EC_PLUGIN_NAME ); ?>" />
	<?php if( $events ): ?>
		<input type="button" class="button ai1ec_flush_ics" value="<?php printf( _n( 'Flush 1 event', 'Flush %s events', $events, AI1EC_PLUGIN_NAME ), $events ) ?>" />
	<?php endif ?>
	<img src="images/wpspin_light.gif" class="ajax-loading" alt="" />
</div>
