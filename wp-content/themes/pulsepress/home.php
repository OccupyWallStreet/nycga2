<?php
/**
 * @package WordPress
 * @subpackage PulsePress
 */
?>
<?php get_header(); ?>

<div class="sleeve_main">
	<?php if ( pulse_press_user_can_post() ) : ?>
		<?php locate_template( array( 'post-form.php' ), true ); ?>
	<?php endif; ?>
	<div id="main">
		<h2 class="home-title">
			<?php if ( pulse_press_get_page_number() > 1 ) printf( __( 'Page %s', 'pulse_press' ), pulse_press_get_page_number() ); ?>
			<a class="rss" href="<?php bloginfo( 'rss2_url' ); ?>">RSS</a>

			<span class="controls">
				<a href="#" id="togglecomments"> <?php _e( 'Toggle Comment Threads', 'pulse_press' ); ?></a> | <a href="#directions" id="directions-keyboard"><?php _e( 'Keyboard Shortcuts', 'pulse_press' ); ?></a>
			</span>
		</h2>
		<ul id="postlist">
		<?php 
		$display_regular_posts = true;
		if(isset($_GET['starred'])):
			$paged = pulse_press_get_page_number();	
			
			$starred = pulse_press_get_user_starred_post_meta();
			if( $starred ):
				query_posts( array( 'post__in'=>$starred, 'paged'=>$paged, 'ignore_sticky_posts' => 1 ) );
			else: 
				$display_regular_posts = false;
			?>
				<div class="started-alert"><?php _e( "Sorry, you don't have any starred posts.",'pulse_press'); ?></div>
			<?php
			endif;
		endif; 
		
		
		if($display_regular_posts):
			if ( have_posts() ) : 
				while ( have_posts() ) : the_post(); 
		    		pulse_press_load_entry(); // loads entry.php 
				endwhile;
				
			 else : ?>
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
			<?php
			endif;
		endif;
		?>

		
		
	</div> <!-- main -->
</div> <!-- sleeve -->

<?php get_footer(); ?>