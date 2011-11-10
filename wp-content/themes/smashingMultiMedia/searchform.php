<form method="get" id="sform" class="clearfix" action="<?php bloginfo('url'); ?>/">
	<label class="hidden" for="stext"><?php _e('Search:','smashingMultiMedia'); ?></label>
	<input type="text" value="<?php the_search_query(); ?>" name="s" id="stext" class="text" />
	<input type="submit" id="searchgo" value="Go" class="formbutton" />
</form>
