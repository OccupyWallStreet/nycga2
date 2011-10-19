<div class="page" id="blog-latest">

	<?php query_posts( 'showposts=5' );	?>

	<?php if ( have_posts() ) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<?php $category = get_the_category(); $the_category = $category[0]->category_nicename; ?>
			<?php $posttags = get_the_tags(); ?>

			<?php do_action( 'bp_before_blog_post' ) ?>

			<div class="post" id="post-<?php the_ID(); ?>">

				<div class="author-box">
					<?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
					<p><?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></p>
				</div>

				<div class="post-content">
					<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

					<p class="date"><?php the_time('F j, Y') ?> <em><?php _e( 'in', 'buddypress' ) ?> <?php echo $the_category; ?>
 <?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></em></p>

					<div class="entry">
						<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
					</div>

					<p class="postmetadata"><span class="tags"><?php if ( $posttags ) : ?><?php _e( 'Tags:', 'buddypress' ); ?><?php foreach ( $posttags as $tag ) { echo ' / ' . $tag->name; } ?><?php endif; ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'buddypress' ), __( '1 Comment &#187;', 'buddypress' ), __( '% Comments &#187;', 'buddypress' ) ); ?></span></p>
				</div>

			</div>

			<?php do_action( 'bp_after_blog_post' ) ?>

		<?php endwhile; ?>

	<?php else : ?>

		<h2 class="center"><?php _e( 'Not Found', 'buddypress' ) ?></h2>
		<p class="center"><?php _e( 'Sorry, but you are looking for something that isn\'t here.', 'buddypress' ) ?></p>

		<?php locate_template( array( 'searchform.php' ), true ) ?>

	<?php endif; ?>

</div>