		<tr class="recurrence-row">
			<td><?php _e('Recurrence:','tribe-events-calendar-pro'); ?></td>
			<td>
				<input type="hidden" name="is_recurring" value="<?php echo $recType && $recType != "None" ? "true" : "false" ?>" />
				<input type="hidden" name="recurrence_action" value="" />
				<select name="recurrence[type]">
					<option data-plural="" value="None" <?php selected($recType, "None") ?>><?php _e('None','tribe-events-calendar-pro'); ?></option>
					<option data-single="day" data-plural="days" value="Every Day" <?php selected($recType, "Every Day") ?>><?php _e('Every Day','tribe-events-calendar-pro'); ?></option>
					<option data-single="week" data-plural="weeks" value="Every Week" <?php selected($recType, "Every Week") ?>><?php _e('Every Week','tribe-events-calendar-pro'); ?></option>
					<option data-single="month" data-plural="months" value="Every Month" <?php selected($recType, "Every Month") ?>><?php _e('Every Month','tribe-events-calendar-pro'); ?></option>
					<option data-single="year" data-plural="years" value="Every Year" <?php selected($recType, "Every Year") ?>><?php _e('Every Year','tribe-events-calendar-pro'); ?></option>
					<option data-single="event" data-plural="events" value="Custom" <?php selected($recType, "Custom") ?>><?php _e('Custom','tribe-events-calendar-pro'); ?></option>
				</select>
				<span id="recurrence-end" style="display: <?php echo !$recType || $recType == "None" ? "none" : "inline" ?>">
					<?php _e('End','tribe-events-calendar-pro'); ?>
					<select name="recurrence[end-type]">
						<option value="On" <?php selected($recEndType, "None") ?>><?php _e('On','tribe-events-calendar-pro'); ?></option>
						<option value="After" <?php selected($recEndType, "After") ?>><?php _e('After','tribe-events-calendar-pro'); ?></option>
					</select>
					<input autocomplete="off" placeholder="<?php echo TribeDateUtils::dateOnly( date(TribeDateUtils::DBDATEFORMAT) ) ?>" type="text" class="datepicker" name="recurrence[end]" id="recurrence_end"  value="<?php echo $recEnd  ?>" style="display:<?php echo !$recEndType || $recEndType == "On" ? "inline" : "none"; ?>"/>
					<span id="rec-count" style="display:<?php echo $recEndType == "After" ? "inline" : "none"; ?>"><input autocomplete="off" type="text" name="recurrence[end-count]" id="recurrence_end_count"  value="<?php echo $recEndCount ? $recEndCount : 1 ?>" style='width: 40px;'/> <span id='occurence-count-text'><?php echo isset($recOccurrenceCountText) ? $recOccurrenceCountText : ""; ?></span></span>
					<span id="rec-end-error" class="rec-error"><?php _e('You must select a recurrence end date','tribe-events-calendar-pro'); ?></span>
				</span>
			</td>
		</tr>
		<tr class="recurrence-row" id="custom-recurrence-frequency" style="display: <?php echo $recType == "Custom" ? "table-row" : "none" ?>;">
			<td></td>
			<td>
				<?php _e('Frequency','tribe-events-calendar-pro'); ?>
				<select name="recurrence[custom-type]">
					<option value="Daily" data-plural="<?php _e('Day(s)','tribe-events-calendar-pro'); ?>" data-tablerow="" <?php selected($recCustomType, "None") ?>><?php _e('Daily','tribe-events-calendar-pro'); ?></option>
					<option value="Weekly" data-plural="<?php _e('Week(s) on:','tribe-events-calendar-pro'); ?>" data-tablerow="#custom-recurrence-weeks" <?php selected($recCustomType, "Weekly") ?>><?php _e('Weekly','tribe-events-calendar-pro'); ?></option>
					<option value="Monthly" data-plural="<?php _e('Month(s) on the:','tribe-events-calendar-pro'); ?>" data-tablerow="#custom-recurrence-months" <?php selected($recCustomType, "Monthly") ?>><?php _e('Monthly','tribe-events-calendar-pro'); ?></option>
					<option value="Yearly" data-plural="<?php _e('Year(s) on:','tribe-events-calendar-pro'); ?>" data-tablerow="#custom-recurrence-years" <?php selected($recCustomType, "Yearly") ?>><?php _e('Yearly','tribe-events-calendar-pro'); ?></option>
				</select>
				<?php _e('Every','tribe-events-calendar-pro'); ?>
				<input type="text" name="recurrence[custom-interval]" value="<?php echo $recCustomInterval ?>"/> <span id="recurrence-interval-type"><?php echo $recCustomTypeText ?></span>
				<input type="hidden" name="recurrence[custom-type-text]" value="<?php echo esc_attr( $recCustomTypeText ) ?>"/>
				<input type="hidden" name="recurrence[occurrence-count-text]" value="<?php echo esc_attr( $recOccurrenceCountText ) ?>"/>
				<span id="rec-days-error" class="rec-error"><?php _e('Frequency of recurring event must be a number','tribe-events-calendar-pro'); ?></span>

			</td>
		</tr>
		<?php if(!isset($recCustomWeekDay)) $recCustomWeekDay = array(); ?>
		<tr class="custom-recurrence-row" id="custom-recurrence-weeks" style="display: <?php echo $recType == "Custom"  && $recCustomType == "Weekly" ? "table-row" : "none" ?>;">
			<td></td>
			<td>
				<label><input type="checkbox" name="recurrence[custom-week-day][]" value="1" <?php checked(in_array("1", $recCustomWeekDay)) ?>/> <?php _e('M','tribe-events-calendar-pro'); ?></label>
				<label><input type="checkbox" name="recurrence[custom-week-day][]" value="2" <?php checked(in_array("2", $recCustomWeekDay)) ?>/> <?php _e('Tu','tribe-events-calendar-pro'); ?></label>
				<label><input type="checkbox" name="recurrence[custom-week-day][]" value="3" <?php checked(in_array("3", $recCustomWeekDay)) ?>/> <?php _e('W','tribe-events-calendar-pro'); ?></label>
				<label><input type="checkbox" name="recurrence[custom-week-day][]" value="4" <?php checked(in_array("4", $recCustomWeekDay)) ?>/> <?php _e('Th','tribe-events-calendar-pro'); ?></label>
				<label><input type="checkbox" name="recurrence[custom-week-day][]" value="5" <?php checked(in_array("5", $recCustomWeekDay)) ?>/> <?php _e('F','tribe-events-calendar-pro'); ?></label>
				<label><input type="checkbox" name="recurrence[custom-week-day][]" value="6" <?php checked(in_array("6", $recCustomWeekDay)) ?>/> <?php _e('Sa','tribe-events-calendar-pro'); ?></label>
				<label><input type="checkbox" name="recurrence[custom-week-day][]" value="7" <?php checked(in_array("7", $recCustomWeekDay)) ?>/> <?php _e('Su','tribe-events-calendar-pro'); ?></label>
			</td>
		</tr>
		<tr class="custom-recurrence-row" id="custom-recurrence-months" style="display: <?php echo $recType == "Custom"  && $recCustomType == "Monthly" ? "table-row" : "none" ?>;">
			<td></td>
			<td>
				<div id="recurrence-month-on-the">
					<select name="recurrence[custom-month-number]">
						<option value="First" <?php selected($recCustomMonthNumber, $recCustomMonthNumber ? "First" : "") ?>><?php _e('First','tribe-events-calendar-pro'); ?></option>
						<option value="Second" <?php selected($recCustomMonthNumber, "Second") ?>><?php _e('Second','tribe-events-calendar-pro'); ?></option>
						<option value="Third" <?php selected($recCustomMonthNumber, "Third") ?>><?php _e('Third','tribe-events-calendar-pro'); ?></option>
						<option value="Fourth" <?php selected($recCustomMonthNumber, "Fourth") ?>><?php _e('Fourth','tribe-events-calendar-pro'); ?></option>
						<option value="Last" <?php selected($recCustomMonthNumber, "Last") ?>><?php _e('Last','tribe-events-calendar-pro'); ?></option>
						<option value="" >--</option>
						<?php for($i = 1; $i<=31; $i++): ?>
							<option value="<?php echo $i ?>" <?php selected($recCustomMonthNumber, $i) ?>><?php echo $i; ?></option>						
						<?php endfor; ?>
					</select>
					<select name="recurrence[custom-month-day]" style="display: <?php echo is_numeric($recCustomMonthNumber) ? "none" : "inline" ?>">
						<option value="1"  <?php selected($recCustomMonthDay, "1") ?>><?php _e('Monday','tribe-events-calendar-pro'); ?></option>
						<option value="2" <?php selected($recCustomMonthDay, "2") ?>><?php _e('Tuesday','tribe-events-calendar-pro'); ?></option>
						<option value="3" <?php selected($recCustomMonthDay, "3") ?>><?php _e('Wednesday','tribe-events-calendar-pro'); ?></option>
						<option value="4" <?php selected($recCustomMonthDay, "4") ?>><?php _e('Thursday','tribe-events-calendar-pro'); ?></option>
						<option value="5" <?php selected($recCustomMonthDay, "5") ?>><?php _e('Friday','tribe-events-calendar-pro'); ?></option>
						<option value="6" <?php selected($recCustomMonthDay, "6") ?>><?php _e('Saturday','tribe-events-calendar-pro'); ?></option>
						<option value="7" <?php selected($recCustomMonthDay, "7") ?>><?php _e('Sunday','tribe-events-calendar-pro'); ?></option>
						<option value="-" <?php selected($recCustomMonthDay, "-") ?>>--</option>
						<option value="-1" <?php selected($recCustomMonthDay, "-1") ?>><?php _e('Day','tribe-events-calendar-pro'); ?></option>
					</select>
				</div>
			</td>
		</tr>
		<tr class="custom-recurrence-row" id="custom-recurrence-years"  style="display: <?php echo $recCustomType == "Yearly" ? "table-row" : "none" ?>;">
			<td></td>
			<td>
				<div>
					<label><input type="checkbox" name="recurrence[custom-year-month][]" value="1" <?php checked(in_array("1", $recCustomYearMonth)) ?>/> <?php _e('Jan','tribe-events-calendar-pro'); ?></label>
					<label><input type="checkbox" name="recurrence[custom-year-month][]" value="2" <?php checked(in_array("2", $recCustomYearMonth)) ?>/> <?php _e('Feb','tribe-events-calendar-pro'); ?></label>
					<label><input type="checkbox" name="recurrence[custom-year-month][]" value="3" <?php checked(in_array("3", $recCustomYearMonth)) ?>/> <?php _e('Mar','tribe-events-calendar-pro'); ?></label>
					<label><input type="checkbox" name="recurrence[custom-year-month][]" value="4" <?php checked(in_array("4", $recCustomYearMonth)) ?>/> <?php _e('Apr','tribe-events-calendar-pro'); ?></label>
					<label><input type="checkbox" name="recurrence[custom-year-month][]" value="5" <?php checked(in_array("5", $recCustomYearMonth)) ?>/> <?php _e('May','tribe-events-calendar-pro'); ?></label>
					<label><input type="checkbox" name="recurrence[custom-year-month][]" value="6" <?php checked(in_array("6", $recCustomYearMonth)) ?>/> <?php _e('Jun','tribe-events-calendar-pro'); ?></label>
				</div>
				<div style="clear:both"></div>
				<div>
					<label><input type="checkbox" name="recurrence[custom-year-month][]" value="7" <?php checked(in_array("7", $recCustomYearMonth)) ?>/> <?php _e('Jul','tribe-events-calendar-pro'); ?></label>
					<label><input type="checkbox" name="recurrence[custom-year-month][]" value="8" <?php checked(in_array("8", $recCustomYearMonth)) ?>/> <?php _e('Aug','tribe-events-calendar-pro'); ?></label>
					<label><input type="checkbox" name="recurrence[custom-year-month][]" value="9" <?php checked(in_array("9", $recCustomYearMonth)) ?>/> <?php _e('Sep','tribe-events-calendar-pro'); ?></label>
					<label><input type="checkbox" name="recurrence[custom-year-month][]" value="10" <?php checked(in_array("10", $recCustomYearMonth)) ?>/> <?php _e('Oct','tribe-events-calendar-pro'); ?></label>
					<label><input type="checkbox" name="recurrence[custom-year-month][]" value="11" <?php checked(in_array("11", $recCustomYearMonth)) ?>/> <?php _e('Nov','tribe-events-calendar-pro'); ?></label>
					<label><input type="checkbox" name="recurrence[custom-year-month][]" value="12" <?php checked(in_array("12", $recCustomYearMonth)) ?>/> <?php _e('Dec','tribe-events-calendar-pro'); ?></label>
				</div>
				<div style="clear:both"></div>				
				<div>
					<input type="checkbox" name="recurrence[custom-year-filter]" value="1" <?php checked($recCustomYearFilter, "1") ?>/>
					<?php _e('On the:','tribe-events-calendar-pro'); ?>
					<select name="recurrence[custom-year-month-number]">
						<option value="1" <?php selected($recCustomYearMonthNumber, "1") ?>><?php _e('First','tribe-events-calendar-pro'); ?></option>
						<option value="2" <?php selected($recCustomYearMonthNumber, "2") ?>><?php _e('Second','tribe-events-calendar-pro'); ?></option>
						<option value="3" <?php selected($recCustomYearMonthNumber, "3") ?>><?php _e('Third','tribe-events-calendar-pro'); ?></option>
						<option value="4" <?php selected($recCustomYearMonthNumber, "4") ?>><?php _e('Fourth','tribe-events-calendar-pro'); ?></option>
						<option value="-1" <?php selected($recCustomYearMonthNumber, "-1") ?>><?php _e('Last','tribe-events-calendar-pro'); ?></option>
					</select>
					<select name="recurrence[custom-year-month-day]">
						<option value="1"  <?php selected($recCustomYearMonthDay, "1") ?>><?php _e('Monday','tribe-events-calendar-pro'); ?></option>
						<option value="2" <?php selected($recCustomYearMonthDay, "2") ?>><?php _e('Tuesday','tribe-events-calendar-pro'); ?></option>
						<option value="3" <?php selected($recCustomYearMonthDay, "3") ?>><?php _e('Wednesday','tribe-events-calendar-pro'); ?></option>
						<option value="4" <?php selected($recCustomYearMonthDay, "4") ?>><?php _e('Thursday','tribe-events-calendar-pro'); ?></option>
						<option value="5" <?php selected($recCustomYearMonthDay, "5") ?>><?php _e('Friday','tribe-events-calendar-pro'); ?></option>
						<option value="6" <?php selected($recCustomYearMonthDay, "6") ?>><?php _e('Saturday','tribe-events-calendar-pro'); ?></option>
						<option value="7" <?php selected($recCustomYearMonthDay, "7") ?>><?php _e('Sunday','tribe-events-calendar-pro'); ?></option>
						<option value="-" <?php selected($recCustomYearMonthDay, "-") ?>>--</option>
						<option value="-1" <?php selected($recCustomYearMonthDay, "-1") ?>><?php _e('Day','tribe-events-calendar-pro'); ?></option>
					</select>
				</div>
			</td>
		</tr>
