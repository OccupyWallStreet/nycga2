<ul class="ai1ec_repeat_tabs">
  <li><a href="#ai1ec_daily_content" id="ai1ec_daily_tab" class="ai1ec_tab ai1ec_active"><?php _e( 'Daily', AI1EC_PLUGIN_NAME ) ;?></a></li>
  <li><a href="#ai1ec_weekly_content" id="ai1ec_weekly_tab" class="ai1ec_tab"><?php _e( 'Weekly', AI1EC_PLUGIN_NAME ) ;?></a></li>
  <li><a href="#ai1ec_monthly_content" id="ai1ec_monthly_tab" class="ai1ec_tab"><?php _e( 'Monthly', AI1EC_PLUGIN_NAME ) ;?></a></li>
  <li><a href="#ai1ec_yearly_content" id="ai1ec_yearly_tab" class="ai1ec_tab"><?php _e( 'Yearly', AI1EC_PLUGIN_NAME ) ;?></a></li>
</ul>
<div style="clear:both;"></div>
<div id="ai1ec_daily_content" class="ai1ec_tab_content" title="daily">
  <?php echo $row_daily ?>
  <div id="ai1ec_repeat_tab_append">
    <div id="ai1ec_ending_box" class="ai1ec_repeat_centered_content">
  		<div id="ai1ec_end_holder">
  		  <label for="ai1ec_end">
  				<?php _e( 'End', AI1EC_PLUGIN_NAME ) ?>:
  			</label>
  			 <?php echo $end ?>
  		</div>
  		<div style="clear:both;"></div>
  		<div id="ai1ec_count_holder">
  		  <label for="ai1ec_count">
  				<?php _e( 'Ending after', AI1EC_PLUGIN_NAME ) ?>:
  			</label>
  			<?php echo $count; ?>
  		</div>
  		<div style="clear:both;"></div>
  		<div id="ai1ec_until_holder">
  		  <label for="ai1ec_until-date-input">
  				<?php _e( 'On date', AI1EC_PLUGIN_NAME ) ?>:
  			</label>
  			<input type="text" class="ai1ec-date-input" id="ai1ec_until-date-input" />
  			<input type="hidden" name="ai1ec_until_time" id="ai1ec_until-time" value="<?php echo !is_null( $until ) && $until > 0 ? $until : '' ?>" />
  		</div>
  		<div style="clear:both;"></div>
  	</div>
  	<div id="ai1ec_apply_button_holder">
      <input type="button" name="ai1ec_none_button" value="<?php _e( 'Apply', AI1EC_PLUGIN_NAME ) ?>" class="ai1ec_repeat_apply button button-highlighted" />
      <a href="#ai1ec_cancel" class="ai1ec_repeat_cancel"><?php _e( 'Cancel', AI1EC_PLUGIN_NAME ) ?></a>
    </div>
    <div style="clear:both;"></div>
  </div>
  <div style="clear:both;"></div>
</div>
<div id="ai1ec_weekly_content" class="ai1ec_tab_content" title="weekly">
  <?php echo $row_weekly ?>
</div>
<div id="ai1ec_monthly_content" class="ai1ec_tab_content" title="monthly">
  <?php echo $row_monthly ?>
</div>
<div id="ai1ec_yearly_content" class="ai1ec_tab_content" title="yearly">
  <?php echo $row_yearly ?>
</div>
<input type="hidden" id="ai1ec_is_box_repeat" value="<?php echo $repeat ?>" />
<div style="clear:both;"></div>