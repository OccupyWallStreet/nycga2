<?php
/**
 * Archive Template
 *
 * The archive template is basically a placeholder for archives that don't have a template file. 
 * Ideally, all archives would be handled by a more appropriate template according to the current
 * page context.
 *
 * @package PulsePress
 * @subpackage Template
 */

get_header(); ?>
<div class="sleeve_main">
	<?php if ( pulse_press_user_can_post() && !is_archive() ) : ?>
		<?php locate_template( array( 'post-form.php' ), true ); ?>
	<?php endif; ?>
	<div id="main">
		<h2 class="archive-title date-archive">
			<?php if(is_day()): ?>
			<?php printf( _x( 'Updates from <strong>%s</strong>', 'Month name', 'pulse_press' ), get_the_time( 'l jS F, Y' ) ); ?>
			<?php elseif(is_month()): ?>
			<?php printf( _x( 'Updates from <strong>%s</strong>', 'Month name', 'pulse_press' ), get_the_time( 'F, Y' ) ); ?>
			<?php elseif(is_year()): ?>
			<?php printf( __('Updates from <strong>%s</strong>','pulse_press'), get_the_time( 'Y' ) ); ?>
			<?php endif; ?>
			<span class="controls">
				<a href="#" id="togglecomments"> <?php _e( 'Toggle Comment Threads', 'pulse_press' ); ?></a> | <a href="#directions" id="directions-keyboard"><?php _e( 'Keyboard Shortcuts', 'pulse_press' ); ?></a>
			</span>
			
		</h2>
		
		<ul id="postlist">
		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>
	    		<?php pulse_press_load_entry() // loads entry.php ?>
			<?php endwhile; ?>

		<?php else : ?>
			
			<li class="no-posts">
		    	<h3><?php _e( 'No posts yet!', 'pulse_press' ); ?></h3>
			</li>
			
		<?php endif; ?>
		</ul>
		
		
		<?php if ( $wp_query->max_num_pages > 1 ) : ?>
		<div class="navigation">
			<p class="nav-older"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'pulse_press' ) ); ?></p>
			<p class="nav-newer"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'pulse_press' ) ); ?></p>
		</div>
		<?php endif; ?>
	
	</div> <!-- main -->

</div> <!-- sleeve -->
<?php get_footer(); ?>