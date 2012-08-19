<?php get_header(); ?>
<?php
/*
Template Name: Portfolio
*/
?>
	<div id="colFull">
		<?php 
		$current = get_the_category(); 
		$current_id= $current[0] ->cat_ID; 
		$categs_list = get_category_parents($current_id);
		$pieces = explode("/", $categs_list);
		$category_name = strtolower($pieces[0]);
		$categs = get_cat_id($category_name);
		?>

<?php  if(is_category() && in_category($current_id) || post_is_in_descendant_category($current_id)){?>
		<h1><?php single_cat_title(); ?></h1>
		<ul class="portfolioCategs">
			<li><a href="<?php echo get_category_link(get_option('boldy_portfolio'))?>">All projects</a></li>
			<?php	
					$categories = get_categories('hide_empty=1&child_of='.$categs);
					foreach ($categories as $cat) {
					echo ('<li><a href="');
					echo (get_category_link($cat->cat_ID).'">'.$cat->cat_name.'</a></li>');
					}
				?>
		</ul>
	<?php } ?>
	<div style="clear:both"></div>
		<div class="gallery">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<div class="portfolioItem">
						<h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
						<a href="<?php echo get_thumb_urlfull($post) ?>" rel="prettyPhoto" title="<?php the_title();?>"><?php the_post_thumbnail(); ?></a>
						<p><?php the_excerpt() ?></p>
					</div>
			<?php endwhile; ?>
		</div>
		<!--<div class="navigation">
						<div class="alignleft"><?php next_posts_link() ?></div>
						<div class="alignright"><?php previous_posts_link() ?></div>
			</div>-->
			<?php if (function_exists("emm_paginate")) {
				emm_paginate();
			} ?>

	<?php else : ?>

		<p>Sorry, but you are looking for something that isn't here.</p>

	<?php endif; ?>
		</div>
<?php get_footer(); ?>