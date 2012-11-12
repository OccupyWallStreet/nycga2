<?php do_action( 'ai1ec_general_settings_before' ); ?>

<h2><?php _e( 'Viewing Events', AI1EC_PLUGIN_NAME ) ?></h2>

<label class="textinput" for="calendar_page_id"><?php _e( 'Calendar page:', AI1EC_PLUGIN_NAME ) ?></label>
<div class="alignleft"><?php echo $calendar_page ?></div>
<br class="clear" />

<?php if( $show_timezone ) : ?>
  <label class="textinput" for="timezone"><?php _e( 'Timezone:', AI1EC_PLUGIN_NAME ) ?></label>
  <?php echo $timezone_control ?>
<?php endif; ?>
<br class="clear" />

<label class="textinput" for="week_start_day"><?php _e( 'Week starts on', AI1EC_PLUGIN_NAME ) ?></label>
<?php echo $week_start_day ?>
<br class="clear" />

<div class="ai1ec-admin-view-settings">
	<label><?php _e( 'Available Views:', AI1EC_PLUGIN_NAME ) ?></label>
	<?php echo $default_calendar_view ?>
</div>

<label class="textinput" for="posterboard_events_per_page"><?php _e( 'Posterboard pages show at most', AI1EC_PLUGIN_NAME ) ?></label>
<input name="posterboard_events_per_page" id="posterboard_events_per_page" type="text" size="1" value="<?php echo esc_attr( $posterboard_events_per_page ) ?>" />&nbsp;<?php _e( 'events', AI1EC_PLUGIN_NAME ) ?>
<br class="clear" />

<label class="textinput" for="agenda_events_per_page"><?php _e( 'Agenda pages show at most', AI1EC_PLUGIN_NAME ) ?></label>
<input name="agenda_events_per_page" id="agenda_events_per_page" type="text" size="1" value="<?php echo esc_attr( $agenda_events_per_page ) ?>" />&nbsp;<?php _e( 'events', AI1EC_PLUGIN_NAME ) ?>
<br class="clear" />

<label for="agenda_events_expanded">
<input class="checkbox" name="agenda_events_expanded" id="agenda_events_expanded" type="checkbox" value="1" <?php echo $agenda_events_expanded ?> />
<?php _e( 'Keep all events <strong>expanded</strong> in the agenda view', AI1EC_PLUGIN_NAME ) ?>
</label>
<br class="clear" />

<label for="show_year_in_agenda_dates">
<input class="checkbox" name="show_year_in_agenda_dates" id="show_year_in_agenda_dates" type="checkbox" value="1" <?php echo $show_year_in_agenda_dates ?> />
<?php _e( '<strong>Show year</strong> in agenda date labels', AI1EC_PLUGIN_NAME ) ?>
</label>
<br class="clear" />

<label for="show_location_in_title">
<input class="checkbox" name="show_location_in_title" id="show_location_in_title" type="checkbox" value="1" <?php echo $show_location_in_title ?> />
<?php _e( '<strong>Show location in event titles</strong> in calendar views', AI1EC_PLUGIN_NAME ) ?>
</label>
<br class="clear" />

<label for="exclude_from_search">
<input class="checkbox" name="exclude_from_search" id="exclude_from_search" type="checkbox" value="1" <?php echo $exclude_from_search ?> />
<?php _e( '<strong>Exclude</strong> events from search results', AI1EC_PLUGIN_NAME ) ?>
</label>
<br class="clear" />

<label for="show_create_event_button">
<input class="checkbox" name="show_create_event_button" id="show_create_event_button" type="checkbox" value="1" <?php echo $show_create_event_button ?> />
<?php _e( 'Show <strong>Post Your Event</strong> button above the calendar to privileged users', AI1EC_PLUGIN_NAME ) ?>
</label>
<br class="clear" />

<label for="turn_off_subscription_buttons">
<input class="checkbox" name="turn_off_subscription_buttons" id="turn_off_subscription_buttons" type="checkbox" value="1" <?php echo $turn_off_subscription_buttons ?> />
<?php _e( 'Hide <strong>Subscribe</strong>/<strong>Add to Calendar</strong> buttons in calendar and single event views', AI1EC_PLUGIN_NAME ) ?>
</label>
<br class="clear" />

<label for="hide_maps_until_clicked">
<input class="checkbox" name="hide_maps_until_clicked" id="hide_maps_until_clicked" type="checkbox" value="1" <?php echo $hide_maps_until_clicked ?> />
<?php _e( 'Hide <strong>Google Maps</strong> until clicked', AI1EC_PLUGIN_NAME ) ?>
</label>
<br class="clear" />

<label for="inject_categories">
<input class="checkbox" name="inject_categories" id="inject_categories" type="checkbox" value="1" <?php echo $inject_categories ?> />
<?php _e( 'Include <strong>event categories</strong> in post category lists', AI1EC_PLUGIN_NAME ) ?>
</label>
<br class="clear" />

<h2><?php _e( 'Adding/Editing Events', AI1EC_PLUGIN_NAME ) ?></h2>

<label class="textinput" for="input_date_format"><?php _e( 'Input dates in this format:', AI1EC_PLUGIN_NAME ) ?></label>
<?php echo $input_date_format ?>
<br class="clear" />

<label for="input_24h_time">
<input class="checkbox" name="input_24h_time" id="input_24h_time" type="checkbox" value="1" <?php echo $input_24h_time ?> />
<?php _e( 'Use <strong>24h time</strong> in time pickers', AI1EC_PLUGIN_NAME ) ?>
</label>
<br class="clear" />

<label for="disable_autocompletion">
<input class="checkbox" name="disable_autocompletion" id="disable_autocompletion" type="checkbox" value="1" <?php echo $disable_autocompletion ?> />
<?php _e( '<strong>Disable address autocomplete</strong> function', AI1EC_PLUGIN_NAME ) ?>
</label>
<br class="clear" />

<label for="geo_region_biasing">
<input class="checkbox" name="geo_region_biasing" id="geo_region_biasing" type="checkbox" value="1" <?php echo $geo_region_biasing ?> />
<?php _e( 'Use the configured <strong>region</strong> (WordPress locale) to bias the address autocomplete function', AI1EC_PLUGIN_NAME ) ?>
</label>
<br class="clear" />

<label for="show_publish_button">
<input class="checkbox" name="show_publish_button" id="show_publish_button" type="checkbox" value="1" <?php echo $show_publish_button ?> />
<?php _e( 'Display <strong>Publish</strong> at bottom of Edit Event form', AI1EC_PLUGIN_NAME ) ?>
</label>
<br class="clear" />

<?php do_action( 'ai1ec_general_settings_after' ); ?>
