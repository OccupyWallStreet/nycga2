<p>
	<label for="<?php echo $title['id'] ?>"><?php _e( 'Title:', AI1EC_PLUGIN_NAME ) ?></label>
	<input class="widefat" id="<?php echo $title['id'] ?>" name="<?php echo $title['name'] ?>" type="text" value="<?php echo $title['value'] ?>" />
</p>
<p>
	<label for="<?php echo $events_per_page['id'] ?>"><?php _e( 'Number of events to show:', AI1EC_PLUGIN_NAME ) ?></label>
	<input id="<?php echo $events_per_page['id'] ?>" name="<?php echo $events_per_page['name'] ?>" type="text" size="3" value="<?php echo $events_per_page['value'] ?>" />
</p>
<p class="ai1ec-limit-by-container">
	Limit to:
	<br />
	<!-- Limit by Category -->
	<input id="<?php echo $limit_by_cat['id'] ?>" class="ai1ec-limit-by-cat" name="<?php echo $limit_by_cat['name'] ?>" type="checkbox" value="1" <?php if( $limit_by_cat['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $limit_by_cat['id'] ?>"><?php _e( 'Events with these <strong>Categories</strong>', AI1EC_PLUGIN_NAME ) ?></label>
</p>
<div class="ai1ec-limit-by-options-container" <?php if( ! $limit_by_cat['value'] ) { ?> style="display: none;" <?php } ?>>
	<!-- Limit by Category Select box -->
	<select id="<?php echo $event_cat_ids['id'] ?>" class="ai1ec-widget-cat-ids" name="<?php echo $event_cat_ids['name'] ?>[]" size="5" multiple="multiple">
		<?php foreach( $event_cat_ids['options'] as $event_cat ): ?>
			<option value="<?php echo $event_cat->term_id; ?>"<?php if( in_array( $event_cat->term_id, $event_cat_ids['value'] ) ) { ?> selected="selected"<?php } ?>><?php echo $event_cat->name; ?></option>
		<?php endforeach ?>
		<?php if( count( $event_cat_ids['options'] ) == 0 ) : ?>
			<option disabled="disabled"><?php _e( 'No categories found.', AI1EC_PLUGIN_NAME ) ?></option>
		<?php endif ?>
	</select>
</div>
<p class="ai1ec-limit-by-container">
	<!-- Limit by Tag -->
	<input id="<?php echo $limit_by_tag['id'] ?>" class="ai1ec-limit-by-tag" name="<?php echo $limit_by_tag['name'] ?>" type="checkbox" value="1" <?php if( $limit_by_tag['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $limit_by_tag['id'] ?>"><?php _e( '<strong>Or</strong> events with these <strong>Tags</strong>', AI1EC_PLUGIN_NAME ) ?></label>
</p>
<div class="ai1ec-limit-by-options-container" <?php if( ! $limit_by_tag['value'] ) { ?> style="display: none;" <?php } ?>>
	<!-- Limit by Tag Select box -->
	<select id="<?php echo $event_tag_ids['id'] ?>" class="ai1ec-widget-tag-ids" name="<?php echo $event_tag_ids['name'] ?>[]" size="5" multiple="multiple">
		<?php foreach( $event_tag_ids['options'] as $event_tag ): ?>
			<option value="<?php echo $event_tag->term_id; ?>"<?php if( in_array( $event_tag->term_id, $event_tag_ids['value'] ) ) { ?> selected="selected"<?php } ?>><?php echo $event_tag->name; ?></option>
		<?php endforeach ?>
		<?php if( count( $event_tag_ids['options'] ) == 0 ) : ?>
			<option disabled="disabled"><?php _e( 'No tags found.', AI1EC_PLUGIN_NAME ) ?></option>
		<?php endif ?>
	</select>
</div>
<p class="ai1ec-limit-by-container">
	<!-- Limit by Event -->
	<input id="<?php echo $limit_by_post['id'] ?>" class="ai1ec-limit-by-event" name="<?php echo $limit_by_post['name'] ?>" type="checkbox" value="1" <?php if( $limit_by_post['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $limit_by_post['id'] ?>"><?php _e( '<strong>Or</strong> any of these <strong>Events</strong>', AI1EC_PLUGIN_NAME ) ?></label>
</p>
<div class="ai1ec-limit-by-options-container" <?php if( ! $limit_by_post['value'] ) { ?> style="display: none;" <?php } ?>>
	<!-- Limit by Event Select box -->
	<select id="<?php echo $event_post_ids['id'] ?>" class="ai1ec-widget-event-ids" name="<?php echo $event_post_ids['name'] ?>[]" size="5" multiple="multiple">
		<?php foreach( $event_post_ids['options'] as $event_post ): ?>
			<option value="<?php echo $event_post->ID; ?>"<?php if( in_array( $event_post->ID, $event_post_ids['value'] ) ) { ?> selected="selected"<?php } ?>><?php echo $event_post->post_title; ?></option>
		<?php endforeach ?>
		<?php if( count( $event_post_ids['options'] ) == 0 ) : ?>
			<option disabled="disabled"><?php _e( 'No events found.', AI1EC_PLUGIN_NAME ) ?></option>
		<?php endif ?>
	</select>
</div>
<br />
<p>
	<input id="<?php echo $show_calendar_button['id'] ?>" name="<?php echo $show_calendar_button['name'] ?>" type="checkbox" value="1" <?php if( $show_calendar_button['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $show_calendar_button['id'] ?>"><?php _e( 'Show <strong>View Calendar</strong> button', AI1EC_PLUGIN_NAME ) ?></label>
	<br />
	<input id="<?php echo $show_subscribe_buttons['id'] ?>" name="<?php echo $show_subscribe_buttons['name'] ?>" type="checkbox" value="1" <?php if( $show_subscribe_buttons['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $show_subscribe_buttons['id'] ?>"><?php _e( 'Show <strong>Subscribe</strong> buttons', AI1EC_PLUGIN_NAME ) ?></label>
	<br />
	<input id="<?php echo $hide_on_calendar_page['id'] ?>" name="<?php echo $hide_on_calendar_page['name'] ?>" type="checkbox" value="1" <?php if( $hide_on_calendar_page['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $hide_on_calendar_page['id'] ?>"><?php _e( 'Hide this widget on calendar page', AI1EC_PLUGIN_NAME ) ?></label>
</p>
