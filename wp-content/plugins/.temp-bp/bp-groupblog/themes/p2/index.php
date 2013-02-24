<?php
/**
 * @package WordPress
 * @subpackage P2
 */
?>
<?php get_header(); ?>

<div class="sleeve_main">
	<?php if ( p2_user_can_post() && !is_archive() ) : ?>
		<?php locate_template( array( 'post-form.php' ), true ); ?>
	<?php endif; ?>
	<div id="main">
		<h2>
			<?php if ( is_home() or is_front_page() ) : ?>
		
				<?php _e( 'Recent Updates' , 'p2' ); ?> <?php if ( p2_get_page_number() > 1 ) printf( __( 'Page %s', 'p2' ), p2_get_page_number() ); ?>
				<a class="rss" href="<?php bloginfo( 'rss2_url' ); ?>">RSS</a>

			<?php elseif ( is_author() ) : ?>
				
				<?php printf( _x( 'Updates from %s', 'Author name', 'p2' ), p2_get_archive_author() ); ?>
				<a class="rss" href="<?php p2_author_feed_link(); ?>">RSS</a>
				
			<?php elseif ( is_tax( 'mentions' ) ) : ?>

				<?php printf( _x( 'Posts Mentioning %s', 'Author name', 'p2' ), p2_get_mention_name() ); ?>
				<a class="rss" href="<?php p2_author_feed_link(); ?>">RSS</a>
		
			<?php else : ?>
	
				<?php printf( _x( 'Updates from %s', 'Month name', 'p2' ), get_the_time( 'F, Y' ) ); ?>
		
			<?php endif; ?>
	
			<span class="controls">
				<a href="#" id="togglecomments"> <?php _e( 'Toggle Comment Threads', 'p2' ); ?></a> | <a href="#directions" id="directions-keyboard"><?php _e( 'Keyboard Shortcuts', 'p2' ); ?></a>
			</span>
		</h2>
	
		<ul id="postlist">
		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>
	    		<?php p2_load_entry() // loads entry.php ?>
			<?php endwhile; ?>

		<?php else : ?>
			
			<li class="no-posts">
		    	<h3><?php _e( 'No posts yet!', 'p2' ); ?></h3>
			</li>
			
		<?php endif; ?>
		</ul>
		
		<div class="navigation">
			<p class="nav-older"><?php next_posts_link( __( '&larr; Older posts', 'p2' ) ); ?></p>
			<p class="nav-newer"><?php previous_posts_link( __( 'Newer posts &rarr;</span>', 'p2' ) ); ?></p>
		</div>
	
	</div> <!-- main -->

</div> <!-- sleeve -->

<?php get_footer(); ?>