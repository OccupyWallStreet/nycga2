<?php
/**
 * @package WordPress
 * @subpackage PulsePress
 */
?>
<?php get_header(); ?>

<div class="sleeve_main">
	
	<div id="main">
		
		<?php if ( have_posts() ) : ?>
			
			<?php while ( have_posts() ) : the_post(); ?>
			
				
		
				<ul id="postlist">
		    		<?php pulse_press_load_entry() // loads entry.php ?>
				</ul>
			
			<?php endwhile; ?>
			
		<?php else : ?>
			
			<ul id="postlist">
				<li class="no-posts">
			    	<h3><?php _e( 'No posts yet!', 'pulse_press' ); ?></h3>
				</li>
			</ul>
			
		<?php endif; ?>

		<div class="navigation">
			<p class="nav-older"><?php previous_post_link( '%link', __( '&larr;', 'Previous post link', 'pulse_press' ) . ' %title' ); ?></p>
			<p class="nav-newer"><?php next_post_link( '%link', '%title ' . __( '&rarr;', 'Next post link', 'pulse_press' ) ); ?></p>
		</div>
		
	</div> <!-- main -->

</div> <!-- sleeve -->

<?php get_footer(); ?>