<?php
/*
Template Name: Single Column Template
*/
?>
	<?php get_header() ?>

		<div id="content" class="grid_24">
		<?php do_action( 'bp_before_blog_page' ) ?>
			<div class="page" id="blog-page" role="main">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<h2 class="pagetitle"><?php the_title(); ?></h2>
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="entry">
						<?php the_content( __( '<p class="serif">Read the rest of this page &rarr;</p>', 'buddypress' ) ); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
						<?php edit_post_link( __( 'Edit this page.', 'buddypress' ), '<p class="edit-link">', '</p>'); ?>
					</div>
				</div>
			<?php comments_template(); ?>
			<?php endwhile; endif; ?>
			</div><!-- .page -->
			<?php do_action( 'bp_after_blog_page' ) ?>
		</div><!-- #content -->

	<?php get_footer(); ?>
