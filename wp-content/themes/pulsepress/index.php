<?php
/**
 * @package WordPress
 * @subpackage PulsePress
 */
?>
<?php get_header(); ?>

<div class="sleeve_main">
	<?php if ( pulse_press_user_can_post() && !is_archive() ) : ?>
		<?php locate_template( array( 'post-form.php' ), true ); ?>
	<?php endif; ?>
	<div id="main">
		<h2 class="index-title">
			<?php if ( is_author() ) : ?>
				
				<?php printf( _x( 'Updates from %s', 'Author name', 'pulse_press' ), pulse_press_get_archive_author() ); ?>
				<a class="rss" href="<?php pulse_press_author_feed_link(); ?>">RSS</a>
				
			<?php elseif ( is_tax( 'mentions' ) ) : ?>

				<?php printf( _x( 'Posts Mentioning %s', 'Author name', 'pulse_press' ), pulse_press_get_mention_name() ); ?>
				<a class="rss" href="<?php pulse_press_author_feed_link(); ?>">RSS</a>
			<?php elseif ( is_category() ) : ?>
				<?php single_cat_title(); ?> <?php if ( pulse_press_get_page_number() > 1 ) printf( __( 'Page %s', 'pulse_press' ), pulse_press_get_page_number() ); ?>
			<?php elseif(is_day()) : ?>
	
				<?php printf( _x( 'Updates from %s', 'Month name', 'pulse_press' ), get_the_time( 'l jS F , Y' ) ); ?>
		
			<?php endif; ?>
	
			<span class="controls">
				<a href="#" id="togglecomments"> <?php _e( 'Toggle Comment Threads', 'pulse_press' ); ?></a> | <a href="#directions" id="directions-keyboard"><?php _e( 'Keyboard Shortcuts', 'pulse_press' ); ?></a>
			</span>
		</h2>
		
		<?php 
		
		if(isset($_GET['starred'])):
			$paged = pulse_press_get_page_number();	
			query_posts(array('post__in'=>pulse_press_get_user_starred_post_meta(), 'paged'=>$paged));
		endif; 
		
		
		?>
		
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