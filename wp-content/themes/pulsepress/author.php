<?php
/**
 * Author Template
 *
 * This template is loaded when viewing an author archive.
 * It shows the author's bio, avatar, and latest posts.
 * @link http://codex.wordpress.org/Author_Templates
 *
 * @package PuslePress
 * @subpackage Template
 */
$id = get_query_var( 'author' );

get_header(); ?>
<div class="sleeve_main">
	<div id="main">
		<div id="author-bio">
		<?php echo get_avatar($id,100); ?>
		<strong><?php the_author_meta( 'display_name', $id ); ?></strong>
		<p class="author-bio">
					<?php the_author_meta( 'description', $id ); ?>
				</p><!-- .author-bio -->
		</div>
		<h2 class="archive-title author-title">
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