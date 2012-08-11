<?php
/**
 * Template Name: Left-sidebar page
 *
 * @package views_base
 */

get_header(); 
?>

<div class="wptypes_body">
	<div class="wptypes_center">
		<?php get_sidebar('first_sidebar'); ?>
		<div class="wptypes_middle <?php echo $class_base_theme->middle_switch()?>">
		<div id="content" role="main">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
				<?php comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>
		</div><!-- #content -->
		</div><!-- .wptypes_middle -->
	</div><!-- .wptypes_center -->
</div><!-- .wptypes_body -->
<?php get_footer(); ?>