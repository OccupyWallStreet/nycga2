<?php
/*
Template Name: Full Width
*/
?>

<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
		<div id="content" class="one-col">
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h1 class="page-title"><?php the_title(); ?></h1>
				<div class="entry-content">
					<?php the_content(''); ?>
					<?php edit_post_link('('.__('Edit', 'themejunkie').')', '<span class="entry-edit">', '</span>'); ?>
				</div><!-- .entry -->
			</div><!-- #post-<?php the_ID(); ?> -->
		</div><!-- #content -->
		
	<?php endwhile; endif; ?>
	
<?php get_footer(); ?>