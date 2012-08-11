<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package views_base
 */

get_header(); 
?>

<div class="wptypes_body">
	<?php get_sidebar('header_sidebar'); ?>
	<div class="wptypes_center">
		<?php get_sidebar('first_sidebar'); ?>
		<div class="wptypes_middle <?php esc_attr_e(apply_filters( 'middle_switch', ''));?>">
			<?php get_sidebar('center_header_sidebar'); ?>
		<div id="content" role="main">
		<?php if ( have_posts() ) : ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', get_post_format() ); ?>
			<?php endwhile; ?>

			<?php views_base_content_nav( 'nav-below' ); ?>

		<?php else : ?>

			<article id="post-0" class="post no-results not-found">

			<?php if ( current_user_can( 'edit_posts' ) ) :
				// Show a different message to a logged-in user who can add posts.
			?>
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'No posts to display', 'views_base' ); ?></h1>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<p><?php printf( __( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'views_base' ), admin_url( 'post-new.php' ) ); ?></p>
				</div><!-- .entry-content -->

			<?php else :
				// Show the default message to everyone else.
			?>
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'Nothing found', 'views_base' ); ?></h1>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'views_base' ); ?></p>
					<?php get_search_form(); ?>
				</div><!-- .entry-content -->
			<?php endif; // end current_user_can() check ?>

			</article><!-- #post-0 -->

		<?php endif; // end have_posts() check ?>
		<?php do_action( 'views_base_before_close_content' );?>
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