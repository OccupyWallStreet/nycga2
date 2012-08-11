<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package views_base
 */

get_header(); ?>

<div class="wptypes_body">
	<?php get_sidebar('header_sidebar'); ?>
	<div class="wptypes_center">
		<?php get_sidebar('first_sidebar'); ?>
		<div class="wptypes_middle <?php echo $class_base_theme->middle_switch()?>">
			<?php get_sidebar('center_header_sidebar'); ?>
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
				<?php comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->

			<?php get_sidebar('center_foot_sidebar'); ?>
		</div><!-- .wptypes_middle -->
		<?php get_sidebar('second_sidebar'); ?>
	</div><!-- .wptypes_center -->
	<?php get_sidebar('foot_sidebar_1'); ?>
	<?php get_sidebar('foot_sidebar_2'); ?>
	<?php get_sidebar('foot_sidebar_3'); ?>
</div><!-- .wptypes_body -->
<?php get_footer(); ?>