<?php /* Template Name: Homepage */
get_header();
?>

<div id="post-entry" class="home-column">

<?php if ( is_active_sidebar( __('top-left-column', TEMPLATE_DOMAIN ) ) ) : ?>
<?php dynamic_sidebar( __('top-left-column', TEMPLATE_DOMAIN ) ); ?>
<?php endif; ?>

<?php
$home_featured_block = get_option('tn_buddycorp_home_featured_block');
$home_featured_block_style = get_option('tn_buddycorp_home_featured_block_style');
?>

<?php if($home_featured_block != 'hide') { ?>

<?php if($home_featured_block_style != 'slideshow') { ?>


<?php locate_template( array( 'lib/templates/wp-template/featured_events.php'), true ); ?>


<?php } else { ?>

<?php locate_template( array( 'lib/templates/wp-template/slideshow.php'), true ); ?>

<?php } ?>

<?php } ?>

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