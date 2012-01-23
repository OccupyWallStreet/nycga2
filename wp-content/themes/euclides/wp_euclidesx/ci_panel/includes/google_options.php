<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php 
	$ci_defaults['google_analytics_code'] = '';
?>
<?php else: ?>
	<p class="guide"><?php _e('Paste here your Google Analytics Code, as given by the Analytics website, and it will be automatically included on every page.', CI_DOMAIN); ?></p>
	<fieldset class="set">
		<label for="ga_code"><?php _e('Google Analytics Code', CI_DOMAIN); ?>:</label>
		<textarea id="ga_code" name="<?php echo THEME_OPTIONS; ?>[google_analytics_code]" rows="5"><?php echo $ci['google_analytics_code']; ?></textarea>
	</fieldset>
<?php endif; ?>