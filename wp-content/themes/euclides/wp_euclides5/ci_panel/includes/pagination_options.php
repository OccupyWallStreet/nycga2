<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php 
	$ci_defaults['pagination'] 			= 'disabled';
	$ci_defaults['posts_per_page'] 		= '10';
	$ci_defaults['home_pagination'] 	= 'global';
	$ci_defaults['home_posts_per_page']	= '10';
	$ci_defaults['cat_pagination']		= 'global';
	$ci_defaults['cat_posts_per_page'] 	= '10';
	$ci_defaults['tag_pagination'] 		= 'global';
	$ci_defaults['tag_posts_per_page'] 	= '10';
	$ci_defaults['tax_pagination'] 		= 'global';
	$ci_defaults['tax_posts_per_page'] 	= '10';
?>
<?php else: ?>
	<fieldset class="set">
		<p class="guide"><?php _e('Global options are generic and can be overridden by more specific options bellow.', CI_DOMAIN); ?></p>
		<fieldset>
			<label><?php _e('Global Pagination', CI_DOMAIN); ?></label>
			<p>
				<input type="radio" class="radio" id="pagination-e" name="<?php echo THEME_OPTIONS; ?>[pagination]" value="enabled" <?php checked($ci['pagination'], 'enabled'); ?> />
				<label for="pagination-e" class="radio"><?php _e('Enabled', CI_DOMAIN); ?></label>
			</p>
			<p>
				<input type="radio" class="radio" id="pagination-d" name="<?php echo THEME_OPTIONS; ?>[pagination]" value="disabled" <?php checked($ci['pagination'], 'disabled'); ?> />
				<label for="pagination-d" class="radio"><?php _e('Disabled', CI_DOMAIN); ?></label>
			</p>
		</fieldset>
		<fieldset>
			<label><?php _e('Posts per page (global)', CI_DOMAIN); ?></label>
			<input type="text" size="3" name="<?php echo THEME_OPTIONS; ?>[posts_per_page]" value="<?php echo $ci['posts_per_page']; ?>" />
		</fieldset>
	</fieldset>

	<fieldset class="set">
		<p class="guide"><?php _e('The following settings affect only the home page.', CI_DOMAIN); ?></p>
		<fieldset>
			<label><?php _e('Home Page Pagination', CI_DOMAIN); ?></label>
			<p>
				<input type="radio" class="radio" id="home_pagination-e" name="<?php echo THEME_OPTIONS; ?>[home_pagination]" value="enabled" <?php checked($ci['home_pagination'], 'enabled'); ?> />
				<label for="home_pagination-e" class="radio"><?php _e('Enabled', CI_DOMAIN); ?></label>
			</p>
			<p>
				<input type="radio" class="radio" id="home_pagination-d" name="<?php echo THEME_OPTIONS; ?>[home_pagination]" value="disabled" <?php checked($ci['home_pagination'], 'disabled'); ?> />
				<label for="home_pagination-d" class="radio"><?php _e('Disabled', CI_DOMAIN); ?></label>
			</p>
			<p>
				<input type="radio" class="radio" id="home_pagination-g" name="<?php echo THEME_OPTIONS; ?>[home_pagination]" value="global" <?php checked($ci['home_pagination'], 'global'); ?> />
				<label for="home_pagination-g" class="radio"><?php _e('Global', CI_DOMAIN); ?></label>
			</p>
		</fieldset>
		<fieldset>
			<label><?php _e('Home Page - Posts per page', CI_DOMAIN); ?></label>
			<input type="text" size="3" name="<?php echo THEME_OPTIONS; ?>[home_posts_per_page]" value="<?php echo $ci['home_posts_per_page']; ?>" />
		</fieldset>
	</fieldset>

	<fieldset class="set">
		<p class="guide"><?php _e('The following settings affect only the displaying of pages when browsing posts from a specific category.', CI_DOMAIN); ?></p>
		<fieldset>
			<label><?php _e('Category Listing Pages Pagination', CI_DOMAIN); ?></label>
			<p>
				<input type="radio" class="radio" id="cat_pagination-e" name="<?php echo THEME_OPTIONS; ?>[cat_pagination]" value="enabled" <?php checked($ci['cat_pagination'], 'enabled'); ?> />
				<label for="cat_pagination-e" class="radio"><?php _e('Enabled', CI_DOMAIN); ?></label>
			</p>
			<p>
				<input type="radio" class="radio" id="cat_pagination-d" name="<?php echo THEME_OPTIONS; ?>[cat_pagination]" value="disabled" <?php checked($ci['cat_pagination'], 'disabled'); ?> />
				<label for="cat_pagination-d" class="radio"><?php _e('Disabled', CI_DOMAIN); ?></label>
			</p>
			<p>
				<input type="radio" class="radio" id="cat_pagination-g" name="<?php echo THEME_OPTIONS; ?>[cat_pagination]" value="global" <?php checked($ci['cat_pagination'], 'global'); ?> />
				<label for="cat_pagination-g" class="radio"><?php _e('Global', CI_DOMAIN); ?></label>
			</p>
		</fieldset>
		<fieldset>
			<p>
				<label><?php _e('Category Listing Pages', CI_DOMAIN); ?> - <?php _e('Posts per page', CI_DOMAIN); ?></label>
				<input type="text" size="3" name="<?php echo THEME_OPTIONS; ?>[cat_posts_per_page]" value="<?php echo $ci['cat_posts_per_page']; ?>" />
			</p>
		</fieldset>
	</fieldset>

	<fieldset class="set">
		<p class="guide"><?php _e('The following settings affect only the displaying of pages when browsing posts from a specific tag.', CI_DOMAIN); ?></p>
		<fieldset>
			<label><?php _e('Tag Listing Pages Pagination', CI_DOMAIN); ?></label>
			<p>
				<input type="radio" class="radio" id="tag_pagination-e" name="<?php echo THEME_OPTIONS; ?>[tag_pagination]" value="enabled" <?php checked($ci['tag_pagination'], 'enabled'); ?> />
				<label for="tag_pagination-e" class="radio"><?php _e('Enabled', CI_DOMAIN); ?></label>
			</p>
			<p>
				<input type="radio" class="radio" id="tag_pagination-d" name="<?php echo THEME_OPTIONS; ?>[tag_pagination]" value="disabled" <?php checked($ci['tag_pagination'], 'disabled'); ?> />
				<label for="tag_pagination-d" class="radio"><?php _e('Disabled', CI_DOMAIN); ?></label>
			</p>
			<p>
				<input type="radio" class="radio" id="tag_pagination-g" name="<?php echo THEME_OPTIONS; ?>[tag_pagination]" value="global" <?php checked($ci['tag_pagination'], 'global'); ?> />
				<label for="tag_pagination-g" class="radio"><?php _e('Global', CI_DOMAIN); ?></label>
			</p>
		</fieldset>
		<fieldset>
			<p>
				<label><?php _e('Tag Listing Pages', CI_DOMAIN); ?> - <?php _e('Posts per page', CI_DOMAIN); ?></label>
				<input type="text" size="3" name="<?php echo THEME_OPTIONS; ?>[tag_posts_per_page]" value="<?php echo $ci['tag_posts_per_page']; ?>" />
			</p>
		</fieldset>
	</fieldset>
<?php endif; ?>