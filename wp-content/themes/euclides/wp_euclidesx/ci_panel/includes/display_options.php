<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php 
	$ci_defaults['preview_content'] 	= 'enabled'; //enabled means content, disabled means excerpt
	$ci_defaults['excerpt_length'] 		= 50;
	$ci_defaults['excerpt_more_link'] 	= 'enabled';
	$ci_defaults['read_more_text'] 		= '[...]';
	
	// The following is not user-definable, so it doesn't need a default.
	// In fact, not being in the panel overwrites it with an empty string when ci_default_options(false); runs.
	$ci['read_more_wrap'] = '<span class="read-more %2$s">%1$s</span>';


	// Handle the excerpt.
	add_filter('excerpt_length', 'ci_excerpt_length');
	add_filter('excerpt_more', 'ci_excerpt_more');
	function ci_excerpt_length($length) {
		return ci_setting('excerpt_length');
	}
	
	function ci_excerpt_more($more) {
		global $post;
		
		$linked_text = '<a href="'. get_permalink($post->ID) . '">' . ci_setting('read_more_text') . '</a>';

		//	If we're getting the excerpt of 'featured' post type, we don't want the "read more text".
		// We are constructing in from within the template, as it may use custom fields.
		if ( get_query_var('post_type') == 'featured' )
			return '';

		
		if(ci_setting('excerpt_more_link')=='enabled')
			return sprintf(ci_setting('read_more_wrap'), $linked_text, 'more-excerpt');
		else
			return ci_setting('read_more_text');
	}
	
	// Handle the content
	add_filter('the_content_more_link', 'ci_content_more_link');
	function ci_content_more_link($link) { 
		$format = ci_setting('read_more_wrap');
		$link = str_replace('class="more-link"', '', $link);
		return sprintf($format, $link, 'more-content');
	}
	
?>
<?php else: ?>
	<fieldset class="set">
		<p class="guide"><?php _e('You can select whether you want the Content or the Excerpt to be displayed on listing pages.', CI_DOMAIN); ?></p>
		<fieldset>
			<label><?php _e('Use the following on listing pages', CI_DOMAIN); ?></label>
			<p>
				<input type="radio" class="radio" id="use_content" name="<?php echo THEME_OPTIONS; ?>[preview_content]" value="enabled" <?php checked($ci['preview_content'], 'enabled'); ?> />
				<label for="use_content" class="radio"><?php _e('Use the Content', CI_DOMAIN); ?></label>
			</p>
			<p>
				<input type="radio" class="radio" id="use_excerpt" name="<?php echo THEME_OPTIONS; ?>[preview_content]" value="disabled" <?php checked($ci['preview_content'], 'disabled'); ?> />
				<label for="use_excerpt" class="radio"><?php _e('Use the Excerpt', CI_DOMAIN); ?></label>
			</p>
		</fieldset>
	</fieldset>
	
	<fieldset class="set">
		<p class="guide"><?php _e('You can set what the Read More text will be. This applies to both the Content and the Excerpt.', CI_DOMAIN); ?></p>
		<fieldset>
			<label><?php _e('Read More text', CI_DOMAIN); ?></label>
			<input type="text" size="60" name="<?php echo THEME_OPTIONS; ?>[read_more_text]" value="<?php echo $ci['read_more_text']; ?>" />
		</fieldset>
	</fieldset>

	
	<fieldset class="set">
		<p class="guide"><?php _e('You can define how long the Excerpt will be (in words). You can also choose whether the text will be linked to the article (to behave more like the Content). These option apply only to the Excerpt.', CI_DOMAIN); ?></p>
		<fieldset>
			<label><?php _e('Excerpt length (in words)', CI_DOMAIN); ?></label>
			<input type="text" size="60" name="<?php echo THEME_OPTIONS; ?>[excerpt_length]" value="<?php echo $ci['excerpt_length']; ?>" />
			<input type="checkbox" class="check" id="readmore-linked" name="<?php echo THEME_OPTIONS; ?>[excerpt_more_link]" value="enabled" <?php checked($ci['excerpt_more_link'], 'enabled'); ?>>
			<label for="readmore-linked"><?php _e('Should the "Read More" text be linked to the article?', CI_DOMAIN); ?></label>
		</fieldset>
	</fieldset>
<?php endif; ?>