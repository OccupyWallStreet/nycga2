	<?php include (get_template_directory() . '/library/options/options.php'); ?>
<?php if($bp_existed == 'true') { ?>
	<?php do_action( 'bp_before_blog_search_form' ) ?>
<?php } ?>
<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
		<div class="search-plugin"><?php _e('Search our site'); ?></div>
	<input type="text" value="<?php the_search_query(); ?>" name="s" id="s" class="search-plug" />
	<input type="submit" id="sbb" value="<?php _e( 'Search', 'business-portfolio' ) ?>" />
	<?php if($bp_existed == 'true') { ?>
		<?php do_action( 'bp_blog_search_form' ) ?>
	<?php } ?>
</form>
<?php if($bp_existed == 'true') { ?>
	<?php do_action( 'bp_after_blog_search_form' ) ?>
<?php } ?>
