<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php 
	$ci_defaults['archive_no']  	= '5';
	$ci_defaults['archive_week'] 	= 'enabled';
	$ci_defaults['archive_month'] 	= 'enabled';
	$ci_defaults['archive_year'] 	= 'enabled';
?>
<?php else: ?>
	<fieldset class="set">
		<p class="guide"><?php _e('The number of the latest posts displayed in the archive page.', CI_DOMAIN); ?></p>
		<label><?php _e('Number of latest posts', CI_DOMAIN); ?></label>
		<input type="text" size="60" name="<?php echo THEME_OPTIONS; ?>[archive_no]" value="<?php echo $ci['archive_no']; ?>" />
	</fieldset>
	<p class="guide"><?php _e('Use the following options to display various types of archives.', CI_DOMAIN); ?></p>
	<fieldset>
		<input type="checkbox" class="check" id="archive-week" name="<?php echo THEME_OPTIONS; ?>[archive_week]" value="enabled" <?php checked($ci['archive_week'], 'enabled'); ?> />
		<label><?php _e('Display weekly archive', CI_DOMAIN); ?></label>	
	</fieldset>
	<fieldset>
		<input type="checkbox" class="check" id="archive-month" name="<?php echo THEME_OPTIONS; ?>[archive_month]" value="enabled" <?php checked($ci['archive_month'], 'enabled'); ?> />
		<label><?php _e('Display monthly archive', CI_DOMAIN); ?></label>	
	</fieldset>
	<fieldset>
		<input type="checkbox" class="check" id="archive-year" name="<?php echo THEME_OPTIONS; ?>[archive_year]" value="enabled" <?php checked($ci['archive_year'], 'enabled'); ?> />
		<label><?php _e('Display yearly archive', CI_DOMAIN); ?></label>	
	</fieldset>
<?php endif; ?>