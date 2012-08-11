<?php
	$featured_category = get_option('dev_businessfeature_feature_cat');
	$featured_number = get_option('dev_businessfeature_feature_number');
	$featured_title = get_option('dev_businessfeature_feature_title');
?>
<?php
	if ($featured_category != ""){
		?>
		<h2 class="main-header"><?php stripslashes($featured_title); ?></h2>
		
	<?php query_posts('category_name='. $featured_category . '&showposts='. $featured_number . ''); ?>
					  <?php while (have_posts()) : the_post(); ?>
<div class="plug-list">
	<div class="plug-screens">
	<div class="plug-wrap"><a href="<?php the_permalink() ?>">
		<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
		<?php the_post_thumbnail(); ?></div><?php } } ?>
	</a></div>
	</div>
	<div class="plug-info">
	<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
<div class="description">
		<?php the_excerpt(); ?>
</div>
	<div class="plugbar">
	<ul>
	<li class="price">	<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php _e( 'Read more', 'business-feature') ?></a></li>
	</ul>
	</div>
	</div>
	</div>
				<?php endwhile; 
			}
				?>