<?php do_action( 'ai1ec_advanced_settings_before' ); ?>

<label class="textinput" for="calendar_css_selector"><?php _e( 'Contain calendar in this DOM element:', AI1EC_PLUGIN_NAME ) ?></label>
<input name="calendar_css_selector" id="calendar_css_selector" type="text" size="20" value="<?php echo esc_attr( $calendar_css_selector ) ?>" />
<div class="description"><?php _e( 'Optional. Provide a <a href="http://api.jquery.com/category/selectors/" target="_blank">jQuery selector</a> that evaluates to a single DOM element. Replaces any existing markup found within target. If left empty, calendar is shown in normal page content container.', AI1EC_PLUGIN_NAME ) ?></div>

<?php if( $display_event_platform ): ?>
  <label for="event_platform">
    <input class="checkbox" name="event_platform" id="event_platform" type="checkbox" value="1"
      <?php echo $event_platform; ?>
      <?php echo $event_platform_disabled; ?> />
    <?php _e( 'Turn this blog into an <strong>events-only platform</strong>', AI1EC_PLUGIN_NAME ); ?>
    <?php if( $event_platform_disabled ): ?>
      <div class="description"><?php _e( 'To deactivate event platform mode, set <code>AI1EC_EVENT_PLATFORM</code> in <code>all-in-one-event-calendar.php</code> to <code>FALSE</code>.', AI1EC_PLUGIN_NAME ) ?></div>
    <?php endif; ?>
  </label>

  <label for="event_platform_strict">
    <input class="checkbox" name="event_platform_strict" id="event_platform_strict" type="checkbox" value="1"
      <?php echo $event_platform_strict; ?> />
    <?php _e( '<strong>Strict</strong> event platform mode', AI1EC_PLUGIN_NAME ); ?>
    <div class="description"><?php _e( 'Prevents plugins from adding menu items unrelated to calendar/media/user management', AI1EC_PLUGIN_NAME ); ?></div>
  </label>
<?php endif; ?>

<div class="clear"></div>

<?php do_action( 'ai1ec_advanced_settings_after' ); ?>
