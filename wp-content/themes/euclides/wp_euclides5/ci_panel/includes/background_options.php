<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php 
	$ci_defaults['bg_custom_disabled']	= 'enabled';
	$ci_defaults['bg_color']			= '';
	$ci_defaults['bg_image_disable']	= '';
	$ci_defaults['bg_image']			= '';
	$ci_defaults['bg_image_repeat']		= 'repeat';
	$ci_defaults['bg_image_horizontal']	= 'left';
	$ci_defaults['bg_image_vertical']	= 'top';
	$ci_defaults['bg_image_attachment']	= '';

	// 100 is the priority. It's important to be a big number, i.e. low priority.
	// Low priority means it will execute AFTER the other hooks, hence this will override other styles previously set.
	add_action('wp_head', 'ci_bg_color', 100);
	function ci_bg_color()
	{ ?>
		<?php if (ci_setting('bg_custom_disabled')!='enabled'): ?>
			<style type="text/css">
				body{
					<?php if (ci_setting('bg_color')) echo 'background-color: #'.ci_setting('bg_color').';'; ?>
					<?php 
						if (ci_setting('bg_image')) 
						{
							echo 'background-image: url('.ci_setting('bg_image').');';
							echo 'background-position: '.ci_setting('bg_image_horizontal').' '.ci_setting('bg_image_vertical').';';
							if(ci_setting('bg_image_attachment')=='fixed') echo 'background-attachment: fixed;';
						}
					?>
					<?php if (ci_setting('bg_image_repeat')) echo 'background-repeat: '.ci_setting('bg_image_repeat').';'; ?>
					<?php if (ci_setting('bg_image_disable')=='enabled') echo 'background-image: none;'; ?>
				}
			</style>
		<?php endif; ?>
	<?php }
?>
<?php else: ?>

	<fieldset class="set">
		<p class="guide"><?php _e('Control whether you want to override the theme\'s background, by enabling the custom background option and tweaking the rest as you please.', CI_DOMAIN); ?></p>
		<fieldset>
			<input type="checkbox" class="check toggle-button" id="bg_custom_disabled" name="<?php echo THEME_OPTIONS; ?>[bg_custom_disabled]" value="enabled" <?php checked($ci['bg_custom_disabled'], 'enabled'); ?> />
			<label for="bg_custom_disabled"><?php _e('Disable custom background', CI_DOMAIN); ?></label>	
		</fieldset>
	</fieldset>
	
	<div class="toggle-pane">
	
	<fieldset class="set">
		<p class="guide"><?php _e('You can set the background color of the page here. This option overrides the background color set by the Color Scheme Option above, so leave it empty if you want the default. You may select a color using the color picker (pops up when you click on the input box), or enter its hex number in the input box (without a #).', CI_DOMAIN); ?></p>
		<fieldset>
			<label><?php _e('Background Color', CI_DOMAIN); ?></label>
			<input id="bg_color" type="text" size="10" name="<?php echo THEME_OPTIONS; ?>[bg_color]" value="<?php echo $ci['bg_color']; ?>" />
		</fieldset>
	</fieldset>

	<fieldset class="set">
		<p class="guide"><?php _e('When this option is checked, the body background image is disabled, whether it\'s set by the default stylesheets or by you, from the option below.', CI_DOMAIN); ?></p>
		<fieldset>
			<input type="checkbox" class="check" id="bg_image_disable" name="<?php echo THEME_OPTIONS; ?>[bg_image_disable]" value="enabled" <?php checked($ci['bg_image_disable'], 'enabled'); ?> />
			<label for="bg_image_disable"><?php _e('Disable background image', CI_DOMAIN); ?></label>	
		</fieldset>
	</fieldset>

	<fieldset class="set">
		<p class="guide"><?php _e('You can upload an image to use as custom background for your site. You can also choose whether you want the image to repeat.', CI_DOMAIN); ?></p>
		<label><?php _e('Upload your background image', CI_DOMAIN); ?></label>
		<input type="text" size="60" name="<?php echo THEME_OPTIONS; ?>[bg_image]" value="<?php echo $ci['bg_image']; ?>" class="uploaded"/>      
		<a href="#" class="browse" id="up_bg" ><?php _e('Browse', CI_DOMAIN); ?></a>
		<div class="up-preview"><?php if (isset($ci['bg_image']) ? '<img src="'.$ci['bg_image'].'" />' : '' );  ?></div>
		<fieldset>
			<?php $repeat = $ci['bg_image_repeat']; ?>
			<label><?php _e('Repeat background', CI_DOMAIN); ?></label>
			<select name="<?php echo THEME_OPTIONS; ?>[bg_image_repeat]">
				<option value="no-repeat" <?php selected($repeat,'no-repeat'); ?>><?php _e('No Repeat', CI_DOMAIN); ?></option>
				<option value="repeat" <?php selected($repeat,'repeat'); ?>><?php _e('Repeat', CI_DOMAIN); ?></option>
				<option value="repeat-x" <?php selected($repeat,'repeat-x'); ?>><?php _e('Repeat X', CI_DOMAIN); ?></option>
				<option value="repeat-y" <?php selected($repeat,'repeat-y'); ?>><?php _e('Repeat Y', CI_DOMAIN); ?></option>
			</select>
		</fieldset>
	</fieldset>

	<fieldset class="set">
		<p class="guide"><?php _e('You can select the placement of you image in the background.', CI_DOMAIN); ?></p>
		<fieldset>
			<?php $bg_hor = $ci['bg_image_horizontal']; ?>
			<label><?php _e('Background Horizontal Placement', CI_DOMAIN); ?></label>
			<select name="<?php echo THEME_OPTIONS; ?>[bg_image_horizontal]">
				<option value="left" <?php selected($bg_hor,'left'); ?>><?php _e('Left', CI_DOMAIN); ?></option>
				<option value="center" <?php selected($bg_hor,'center'); ?>><?php _e('Center', CI_DOMAIN); ?></option>
				<option value="right" <?php selected($bg_hor,'right'); ?>><?php _e('Right', CI_DOMAIN); ?></option>
			</select>
		</fieldset>

		<fieldset>
			<?php $bg_ver = $ci['bg_image_vertical']; ?>
			<label><?php _e('Background Vertical Placement', CI_DOMAIN); ?></label>
			<select name="<?php echo THEME_OPTIONS; ?>[bg_image_vertical]">
				<option value="top" <?php selected($bg_ver,'top'); ?>><?php _e('Top', CI_DOMAIN); ?></option>
				<option value="center" <?php selected($bg_ver,'center'); ?>><?php _e('Center', CI_DOMAIN); ?></option>
				<option value="bottom" <?php selected($bg_ver,'bottom'); ?>><?php _e('Bottom', CI_DOMAIN); ?></option>
			</select>
		</fieldset>
	</fieldset>

	<fieldset class="set">
		<p class="guide"><?php _e('When the fixed background option is checked, the background image will not scroll along with the rest of the page.', CI_DOMAIN); ?></p>
		<fieldset>
			<input type="checkbox" class="check" id="bg_image_attachment" name="<?php echo THEME_OPTIONS; ?>[bg_image_attachment]" value="fixed" <?php checked($ci['bg_image_attachment'], 'fixed'); ?> />
			<label for="bg_image_attachment"><?php _e('Fixed background', CI_DOMAIN); ?></label>	
		</fieldset>
	</fieldset>

	</div>

<?php endif; ?>