<?php do_action( 'ai1ec_advanced_settings_before' ); ?>

<label class="textinput" for="calendar_css_selector"><?php _e( 'Contain calendar in this DOM element:', AI1EC_PLUGIN_NAME ) ?></label>
<input name="calendar_css_selector" id="calendar_css_selector" type="text" size="20" value="<?php echo esc_attr( $calendar_css_selector ) ?>" />
<div class="description"><?php _e( 'Optional. Provide a <a href="http://api.jquery.com/category/selectors/" target="_blank">jQuery selector</a> that evaluates to a single DOM element. Replaces any existing markup found within target. If left empty, calendar is shown in normal page content container.', AI1EC_PLUGIN_NAME ) ?></div>

<div class="clear"></div>

<?php do_action( 'ai1ec_advanced_settings_after' ); ?>
