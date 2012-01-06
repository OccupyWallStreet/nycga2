<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php 
	$ci_defaults['feedburner_feed'] = '';
	$ci_defaults['feedburner_email']= '';
?>
<?php else: ?>
	<fieldset class="set">
		<p class="guide"><?php _e('By adding your FeedBurner URL here, your main feed will be served by FeedBurner instead of your Wordpress site.', CI_DOMAIN); ?></p>
		<label><?php _e('FeedBurner Feed URL', CI_DOMAIN); ?></label>
		<input type="text" size="60" name="<?php echo THEME_OPTIONS; ?>[feedburner_feed]" value="<?php echo $ci['feedburner_feed']; ?>" />
		<label><?php _e('FeedBurner Email URL', CI_DOMAIN); ?></label>
		<input type="text" size="60" name="<?php echo THEME_OPTIONS; ?>[feedburner_email]" value="<?php echo $ci['feedburner_email']; ?>" />
	</fieldset>
<?php endif; ?>