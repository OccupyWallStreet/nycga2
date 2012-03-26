<form action="<?php echo home_url('/'); ?>" id="searchform" method="get" role="search">
	<div>
		<label for="s" class="screen-reader-text"><?php _e('Search for', CI_DOMAIN); ?>:</label>
		<input type="text" id="s" name="s" class="text" value="<?php the_search_query(); ?>">
		<input type="submit" class="button" value="<?php _e('Search', CI_DOMAIN); ?>" id="searchsubmit">
	</div>
</form>