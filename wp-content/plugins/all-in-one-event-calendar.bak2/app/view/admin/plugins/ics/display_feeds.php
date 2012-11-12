<p>
<?php _e(
    'Configure which other calendars your own calendar subscribes to.
    You can add any calendar that provides an iCalendar (.ics) feed.
    Enter the feed URL(s) below and the events from those feeds will be
    imported periodically.',
    AI1EC_PLUGIN_NAME ); ?>
</p>
<div id="ics-alerts"></div>
<label class="textinput" for="cron_freq">
  <?php _e( 'Check for new events', AI1EC_PLUGIN_NAME ) ?>:
</label>
<?php echo $cron_freq ?>
<br class="clear" />

<div id="ai1ec-feeds-after" class="ai1ec-feed-container">
	<h4 class="ai1ec_feed_h4"><?php _e( 'iCalendar/.ics Feed URL:', AI1EC_PLUGIN_NAME ) ?></h4>
	<div class="ai1ec-feed-url"><input type="text" name="ai1ec_feed_url" id="ai1ec_feed_url" /></div>
	<div class="ai1ec-feed-category">
		<label for="ai1ec_feed_category">
			<?php _e( 'Event category', AI1EC_PLUGIN_NAME ); ?>:
		</label>
		<?php echo $event_categories; ?>
	</div>
	<div class="ai1ec-feed-tags">
		<label for="ai1ec_feed_tags">
			<?php _e( 'Tag with', AI1EC_PLUGIN_NAME ); ?>:
		</label>
		<input type="text" name="ai1ec_feed_tags" id="ai1ec_feed_tags" />
	</div>
	<input type="button" id="ai1ec_add_new_ics" class="button" value="<?php _e( '+ Add new subscription', AI1EC_PLUGIN_NAME ) ?>" />
</div>

<?php echo $feed_rows; ?>
<div class="modal hide" id="ai1ec-ics-modal">
	<div class="modal-header">
		<button class="close" data-dismiss="modal">×</button>
		<h3><?php echo esc_html__( "Removing ICS Feed", AI1EC_PLUGIN_NAME )?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo esc_html__( "Do you want to keep the events imported from the calendar or remove them?", AI1EC_PLUGIN_NAME );?></p>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn remove btn-danger"><?php echo esc_html__( "Remove Events", AI1EC_PLUGIN_NAME );?></a>
		<a href="#" class="btn keep btn-primary"><?php echo esc_html__( "Keep Events", AI1EC_PLUGIN_NAME );?></a>
	</div>
</div>
<br class="clear" />
<?php submit_button( esc_attr__( 'Update Settings', AI1EC_PLUGIN_NAME ), 'primary', 'ai1ec_save_settings' ); ?>
