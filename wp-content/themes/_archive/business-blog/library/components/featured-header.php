<div id="top-box">
<div id="featured-post">
					<?php
			$featured_category = get_option('dev_businessblog_feature_cat');
					?>
					<?php
					if ($featured_category != ""){
					?>
		<?php 
		// exclude the featured category and the list of latest offset
		query_posts('category_name='. $featured_category . '&showposts=1'); ?>

						  <?php while (have_posts()) : the_post(); ?>
<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
<br /><br />
<span class="featured-author"><?php _e('by', 'business-blog'); ?> <?php the_author_posts_link(); ?> <?php _e('on', 'business-blog'); ?> <?php the_time('F jS Y') ?></span>
	<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
	<?php the_post_thumbnail(); ?></div><?php } } ?>
<p><?php the_excerpt(); ?></p>
<?php endwhile;?>
<?php
}
else {
	?>
	<h1><?php _e('Configure a category under your theme options', 'business-blog'); ?></h1>
	<?php
}
?>
</div>
</div>