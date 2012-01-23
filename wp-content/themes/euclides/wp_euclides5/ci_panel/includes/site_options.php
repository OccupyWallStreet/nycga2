<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php 
	$ci_defaults['logotext'] 		= get_bloginfo('name');
	$ci_defaults['slogan']			= get_bloginfo('description');
	$ci_defaults['logo']			= '';
	$ci_defaults['stylesheet']		= 'default';
	$ci_defaults['title_separator']	= '|';
?>
<?php else: ?>
	<fieldset class="set">
		<p class="guide"><?php _e('You can set general blog options in this page. Your original WordPress settings will not be changed.', CI_DOMAIN); ?></p>
		<fieldset>
			<label><?php _e('Logo text', CI_DOMAIN); ?></label>
			<input type="text" size="60" name="<?php echo THEME_OPTIONS; ?>[logotext]" value="<?php echo $ci['logotext']; ?>" />
		</fieldset>
		<fieldset>
			<label><?php _e('Slogan (visible under the logo)', CI_DOMAIN); ?></label>
			<input type="text" size="60" name="<?php echo THEME_OPTIONS; ?>[slogan]" value="<?php echo $ci['slogan']; ?>" />      
		</fieldset>
		<fieldset>
			<label><?php _e('Upload your logo', CI_DOMAIN); ?></label>
			<input type="text" size="60" name="<?php echo THEME_OPTIONS; ?>[logo]" value="<?php echo $ci['logo']; ?>" class="uploaded"/>      
			<a href="#" class="browse" id="up_logo" ><?php _e('Browse', CI_DOMAIN); ?></a>
			<div class="up-preview"><?php if (isset($ci['logo']) ? '<img src="'.$ci['logo'].'" />' : '' );  ?></div>
		</fieldset>
	</fieldset>
	<fieldset class="set">
		<p class="guide"><?php _e('Select your color scheme.', CI_DOMAIN); ?></p>
		<fieldset>
		<?php $stylesheet = $ci['stylesheet']; ?>
			<label><?php _e('Color scheme', CI_DOMAIN); ?></label>
			<select name="<?php echo THEME_OPTIONS; ?>[stylesheet]">
				<option value="default" <?php selected($stylesheet,'default'); ?>><?php _e('Default', CI_DOMAIN); ?></option>
				<option value="grey" <?php selected($stylesheet,'grey'); ?>><?php _e('Grey', CI_DOMAIN); ?></option>
				<option value="green" <?php selected($stylesheet,'green'); ?>><?php _e('Green', CI_DOMAIN); ?></option>
			</select>
		</fieldset>
	</fieldset>
	
	<fieldset class="set">
		<p class="guide"><?php _e('The title separator is inserted between various elements within the title tag of each page. Leading and trailing spaces are automatically inserted where appropriate.', CI_DOMAIN); ?></p>
		<fieldset>
			<label><?php _e('Title separator', CI_DOMAIN); ?></label>
			<input type="text" size="60" name="<?php echo THEME_OPTIONS; ?>[title_separator]" value="<?php echo $ci['title_separator']; ?>" />
		</fieldset>
	</fieldset>

	<fieldset class="set">
		<p class="guide"><?php _e('Here you can upload your favicon. The favicon is a small, 16x16 icon that appears besides your URL in the address bar, in open tabs and/or in bookmarks. We recommend you create your favicon from an existing square image, using appropriate online services such as <a href="http://tools.dynamicdrive.com/favicon/">Dynamic Drive</a> and <a href="http://www.favicon.cc/">favicon.cc</a>', CI_DOMAIN); ?></p>
		<label><?php _e('Upload your favicon', CI_DOMAIN); ?></label>
		<input type="text" size="60" name="<?php echo THEME_OPTIONS; ?>[favicon]" value="<?php echo $ci['favicon']; ?>" class="uploaded"/>      
		<a href="#" class="browse" id="up_favicon" ><?php _e('Browse', CI_DOMAIN); ?></a>
		<div class="up-preview"><?php if (isset($ci['favicon']) ? '<img src="'.$ci['favicon'].'" />' : '' );  ?></div>
	</fieldset>


<?php endif; ?>