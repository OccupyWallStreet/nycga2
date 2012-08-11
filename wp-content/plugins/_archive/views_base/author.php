<?php
/**
 * The template for displaying Author Archive pages.
 *
 *
 * @package views_base
 *
 */

get_header(); ?>

<div class="wptypes_body">
	<?php get_sidebar('header_sidebar'); ?>
	<div class="wptypes_center">
		<?php get_sidebar('first_sidebar'); ?>
		<div class="wptypes_middle <?php echo $class_base_theme->middle_switch()?>">
			<?php get_sidebar('center_header_sidebar'); ?>
		<div id="content" role="main">

		<?php if ( have_posts() ) : the_post(); ?>

			<header class="page-header">
				<h1 class="page-title author"><?php printf( __( 'Author Archives: %s', 'views_base' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( "ID" ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' ); ?></h1>
			</header>

			<?php rewind_posts(); ?>

			<?php views_base_content_nav( 'nav-above' ); ?>

			<?php
			// If a user has filled out their description, show a bio on their entries.
			if ( get_the_author_meta( 'description' ) ) : ?>
			<div id="author-info">
				<div id="author-avatar">
					<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentytwelve_author_bio_avatar_size', 60 ) ); ?>
				</div><!-- #author-avatar -->
				<div id="author-description">
					<h2><?php printf( __( 'About %s', 'views_base' ), get_the_author() ); ?></h2>
					<?php the_author_meta( 'description' ); ?>
				</div><!-- #author-description	-->
			</div><!-- #author-info -->
			<?php endif; ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', get_post_format() ); ?>
			<?php endwhile; ?>

			<?php views_base_content_nav( 'nav-below' ); ?>

		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>

		</div><!-- #content -->

			<?php get_sidebar('center_foot_sidebar'); ?>
		</div>
		<?php get_sidebar('second_sidebar'); ?>
	</div>
	<?php get_sidebar('foot_sidebar'); ?>
</div>
<?php get_footer(); ?>