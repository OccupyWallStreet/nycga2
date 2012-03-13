<?php
/*
Template Name: NYCGA Homepage
*/
?>
<!-- index.php -->

	<?php get_header() ?>

	<?php locate_template( array( 'leftsidebar.php' ), true ); ?>	

	<div id="content" class="grid_14">

	<?php do_action( 'bp_before_blog_home' ) ?>
	<?php do_action( 'template_notices' ) ?>

	<div class="page" id="blog-latest" role="main">

	
		<?php
			$wp_query = new WP_Query('category_name=homepage-featured'); // don't show posts from category ID 6, a.k.a Links
	 
		 if ( $wp_query->have_posts() ) : ?>
			<?php bp_dtheme_content_nav( 'nav-above' ); ?>
			<?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
			
			<?if( has_post_thumbnail() ): // if there's an image, lets print the post, otherwise....'?>
				<?php do_action( 'bp_before_blog_post' ) ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?/*	
			// design attribution into the features somehow?
			<div class="author-box">
				<?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
				<p><?php printf( _x( 'by %s', 'Post written by...', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></p>
				<?php if ( is_sticky() ) : ?>
					<span class="activity sticky-post"><?php _ex( 'Featured', 'Sticky post', 'buddypress' ); ?></span>
				<?php endif; ?>
			</div>
*/?>
			<div class="post-content">
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<?php the_post_thumbnail( 'large' ); // check if the post has a Post Thumbnail assigned to it. ?>
				<p class="date"><?php printf( __( '%1$s', 'buddypress' ), get_the_date() ); ?></p>
				<?/*<p class="date"><?php printf( __( '%1$s <span>in %2$s</span>', 'buddypress' ), get_the_date(), get_the_category_list( ', ' ) ); ?></p>*/?>
				<div class="entry">
					<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
				</div>
<?/*
				<p class="postmetadata"><?php the_tags( '<span class="tags">' . __( 'Tags: ', 'buddypress' ), ', ', '</span>' ); ?> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'buddypress' ), __( '1 Comment &#187;', 'buddypress' ), __( '% Comments &#187;', 'buddypress' ) ); ?></span></p>
*/?>
			</div>
		</div>
				<?/* php do_action( 'bp_after_blog_post' ) */?>
			<?endif;?>
			<?php endwhile; ?>
<?/*			<?php bp_dtheme_content_nav( 'nav-below' ); ?>
*/?>
		<?php else : ?>
			<h2 class="center"><?php _e( 'Not Found', 'buddypress' ) ?></h2>
			<p class="center"><?php _e( 'Sorry, but you are looking for something that isn\'t here.', 'buddypress' ) ?></p>
			<?php get_search_form() ?>
		<?php endif; ?>
	</div>

	<?php do_action( 'bp_after_blog_home' ) ?>

	</div><!-- #content -->

	<?php get_sidebar() ?>

<?php get_footer() ?>
