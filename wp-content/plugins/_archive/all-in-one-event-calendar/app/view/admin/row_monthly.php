<div class="ai1ec_repeat_centered_content">
  <label for="ai1ec_monthly_count">
	  <?php _e( 'Every', AI1EC_PLUGIN_NAME ) ?>:
  </label>
  <?php echo $count ?>
  <div class="ai1ec_repeat_monthly_type">
	<input type="radio" name="ai1ec_monthly_type" id="ai1ec_monthly_type_bymonthday" value="bymonthday" checked="1" />
	<label for="ai1ec_monthly_type_bymonthday">
	  <?php _e( 'On day of the month', AI1EC_PLUGIN_NAME ) ?>
	</label>
	<input type="radio" name="ai1ec_monthly_type" id="ai1ec_monthly_type_byday" value="byday" />
	<label for="ai1ec_monthly_type_byday">
	  <?php _e( 'On day of the week', AI1EC_PLUGIN_NAME ) ?>
	</label>
  </div>
  <div style="clear:both;"></div>
  <div id="ai1c_repeat_monthly_bymonthday">
  	<?php echo $month ?>
  </div>
  <div id="ai1c_repeat_monthly_byday">
	<label for="ai1ec_monthly_type_byday">
	  <?php _e( 'Every', AI1EC_PLUGIN_NAME ) ?>
	</label>
  	<?php echo $day_nums ?>
  	<?php echo $week_days ?>
  </div>
</div>