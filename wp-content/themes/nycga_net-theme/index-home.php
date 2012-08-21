<?php /* Template Name: Homepage */
get_header();
?>

<div id="post-entry" class="home-column">

<?php if ( is_active_sidebar( __('top-left-column', TEMPLATE_DOMAIN ) ) ) : ?>
<div >
<?php dynamic_sidebar( __('top-left-column', TEMPLATE_DOMAIN ) ); ?>
</div>
<?php endif; ?>

<?php

global $post;

$myquery = new WP_Query('post_type=incsub_event'); 

?>

<?php if ($myquery->have_posts()) : ?> 

<div id="homepage-features" class="featured">
		<h2>Featured Events</h2>
	
		<ul id="featured-items">
				
			<?php foreach( $myquery as $post ) :	setup_postdata($post); ?>
			
			<li>
			<h2 class="title"><?php echo Eab_Template::get_event_link($post); ?>
			<span class="date"><?php echo Eab_Template::get_event_dates($post); ?></span></h2>
			<div class="detail">
				<div class="event-image"><?php the_post_thumbnail(); ?></div>
				<div class="event-summary"><?php the_excerpt(); ?></div>
				<div class="event-date"><?php echo Eab_Template::get_event_dates($post); ?></div>
			</div>
			</li>
			
			<?php endforeach; ?>
		
		</ul>	
			
</div>

<?php else : ?>

<!-- No announcements -->

<?php endif; ?>

<!-- Second query -->
<?php

global $post;

$myquery2 = new WP_Query('post_type=incsub_event'); 

?>

<?php if ($myquery2->have_posts()) : ?>

<div id="homepage-features" class="featured">
		<h2>Featured Events</h2>
	
		<ul id="featured-items">
			<?php while (have_posts()) : the_post(); ?> 
			
			<li>
			<h2 class="title"><?php echo Eab_Template::get_event_link($post); ?>
			<span class="date"><?php echo Eab_Template::get_event_dates($post); ?></span></h2>
			<div class="detail">
				<div class="event-image"><?php the_post_thumbnail(); ?></div>
				<div class="event-summary"><?php the_excerpt(); ?></div>
				<div class="event-date"><?php echo Eab_Template::get_event_dates($post); ?></div>
			</div>
			</li>
			<?php endwhile; ?>
					
		</ul>	
			
</div>

<?php else : ?>

<!-- No announcements -->

<?php endif; ?>

<?php if ( is_active_sidebar( __('left-column', TEMPLATE_DOMAIN ) ) ) : ?>
<?php dynamic_sidebar( __('left-column', TEMPLATE_DOMAIN ) ); ?>
<?php endif; ?>

</div>

<div id="top-right-column">
<?php is_front_page(); ?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
		<?php if($post->post_content=="") : ?>
		
		<!-- Do stuff with empty posts (or leave blank to skip empty posts) -->
		
		<?php else : ?>
	
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
				<?php the_content( __( '<p class="serif">Read the rest of this page &rarr;</p>', 'buddypress' ) ); ?>
	
				<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
				<?php edit_post_link( __( 'Edit this page.', 'buddypress' ), '<p class="edit-link">', '</p>'); ?>
	
		</div>
	
		<?php endif; ?>
	
<?php endwhile; endif; ?>

<?php if ( is_active_sidebar( __('top-right-column', TEMPLATE_DOMAIN ) ) ) : ?>
<?php dynamic_sidebar( __('top-right-column', TEMPLATE_DOMAIN ) ); ?>
<?php endif; ?>

</div>

<?php locate_template( array( 'home-sidebar.php'), true ); ?>

<?php get_footer(); ?>