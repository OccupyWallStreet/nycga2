<?php
/**
 * @package WordPress
 * @subpackage P2
 */
?>
<?php get_header(); ?>

<div class="sleeve_main">

	<div id="main">
		<h2><?php printf( __( 'Search Results for: %s', 'p2' ), get_search_query() ); ?></h2>
		
		<?php if ( have_posts() ) : ?>
			
			<ul id="postlist">
			<?php while ( have_posts() ) : the_post(); ?>
				
				<?php p2_load_entry() // loads entry.php ?>
			
			<?php endwhile; ?>
			</ul>
		
		<?php else : ?>

			<div class="no-posts">
			    <h3><?php _e( 'No posts found!', 'p2' ); ?></h3>
				<p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'p2' ); ?></p>
				<?php get_search_form(); ?>
			</div>
			
		<?php endif ?>
		
		<div class="navigation">
			<p class="nav-older"><?php next_posts_link( __( '&larr; Older posts', 'p2' ) ); ?></p>
			<p class="nav-newer"><?php previous_posts_link( __( 'Newer posts &rarr;</span>', 'p2' ) ); ?></p>
		</div>		

	</div> <!-- main -->

</div> <!-- sleeve -->

<?php get_footer(); ?>