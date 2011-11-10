<?php get_header(); ?>
	<div id="main">
		<div id="content" role="main">
	<?php
		if ( have_posts() )
			the_post();
	?>
	
	<h1 class="page-title author"><?php printf( __( 'Posts by author: %s', "voidy" ), "<span class='vcard'><a class='url fn n' href='" . get_author_posts_url( get_the_author_meta( 'ID' ) ) . "' title='" . esc_attr( get_the_author() ) . "' rel='me'>" . get_the_author() . "</a></span>" ); ?></h1>
	
	<?php
	// If a user has filled out their description, show a bio on their entries.
	if ( get_the_author_meta( 'description' ) ) : ?>
		<div id="entry-author-info">
			<div id="author-avatar">
				<?php echo get_avatar( get_the_author_meta( 'user_email' )); ?>
			</div><!-- #author-avatar -->
			<div id="author-description">
				<h2><?php printf( __( 'About %s', "voidy" ), get_the_author() ); ?></h2>
				<?php the_author_meta( 'description' ); ?>
			</div><!-- #author-description  -->
		</div><!-- #entry-author-info -->
	<?php endif; ?>
	
	<?php
	        rewind_posts();
	?>
	
	<!-- The Loop -->
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<p>
		<em><?php the_time('d M Y'); ?></em>
		<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a>
	</p>
	<?php endwhile; else: ?>
	<p><?php _e('No posts by this author.', "voidy" ); ?></p>

	<?php endif; ?>
	<!-- End Loop -->
	
	</div><!-- #content -->
	<?php get_sidebar(); ?>
	<?php get_footer(); ?>