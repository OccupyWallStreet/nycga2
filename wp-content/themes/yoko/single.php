<?php
/**
 * @package WordPress
 * @subpackage Yoko
 */

get_header(); ?>

<div id="wrap">
<div id="main">

	<div id="content">

	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'single' ); ?>
				
			<?php comments_template( '', true ); ?>

			<?php endwhile; // end of the loop. ?>
	
			<nav id="nav-below">
				<div class="nav-previous"><?php previous_post_link( '%link', '' . _x( '&larr;  Previous Post', 'Previous post link', 'yoko' ) . '' ); ?></div>
				<div class="nav-next"><?php next_post_link( '%link', __('') . _x( 'Next Post &rarr;', 'Next post link', 'yoko' ) . '' ); ?></div>
			</nav><!-- end nav-below -->
				
	</div><!-- end content -->
	
<?php get_sidebar(); ?>
<?php get_footer(); ?>