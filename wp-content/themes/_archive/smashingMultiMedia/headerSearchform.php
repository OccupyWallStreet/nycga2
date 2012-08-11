<form method="get" id="searchform" class="clearfix" action="<?php bloginfo('url'); ?>/">
	<label class="hidden" for="s"><?php _e('Search:','smashingMultiMedia'); ?></label>
	<input type="text" value="<?php the_search_query(); ?>" name="s" id="s" class="text" />
	<input type="image" src="<?php bloginfo('wpurl'); ?>/wp-content/themes/<?php echo get_option('wps_child_theme'); ?>/images/search.png" id="searchsubmit" value="Go"/>
</form>
