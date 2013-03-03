<?php
/**
 * @package WordPress
 * @subpackage PulsePress
 */
?>
<?php get_header(); ?>

<div class="sleeve_main">

	<div id="main">
		<h2 class="archive-title search-title"><?php printf( __( 'Search Results for: %s', 'pulse_press' ), get_search_query() ); ?></h2>
		
		<?php if ( have_posts() ) : ?>
			
			<ul id="postlist">
			<?php while ( have_posts() ) : the_post(); ?>
				
				<?php pulse_press_load_entry() // loads entry.php ?>
			
			<?php endwhile; ?>
			</ul>
		
		<?php else : ?>

			<div class="no-posts">
			    <h3><?php _e( 'No posts found!', 'pulse_press' ); ?></h3>
				<p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'pulse_press' ); ?></p>
				<?php get_search_form(); ?>
			</div>
			
		<?php endif ?>
		
		<div class="navigation">
			<p class="nav-older"><?php next_posts_link( __( '&larr; Older posts', 'pulse_press' ) ); ?></p>
			<p class="nav-newer"><?php previous_posts_link( __( 'Newer posts &rarr;</span>', 'pulse_press' ) ); ?></p>
		</div>		

	</div> <!-- main -->

</div> <!-- sleeve -->

<?php get_footer(); ?>