<?php
/**
 * The Template for displaying all single posts.
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

				<?php get_template_part( 'content', get_post_format() ); ?>
				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() )
						comments_template( '', true );
				?>

			<?php endwhile; // end of the loop. ?>
		</div><!-- #content -->

			<?php get_sidebar('center_foot_sidebar'); ?>
		</div>
		<?php get_sidebar('second_sidebar'); ?>
	</div>
	<?php get_sidebar('foot_sidebar'); ?>
</div>
<?php get_footer(); ?>