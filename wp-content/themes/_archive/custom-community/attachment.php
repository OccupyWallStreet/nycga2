<?php get_header(); ?>

	<div id="content">
	<div class="padder">
		<?php do_action( 'bp_before_attachment' ) ?>

		<div class="page" id="attachments-page">

			<h2 class="pagetitle"><?php _e( 'Blog', 'cc' ) ?></h2>

				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<?php do_action( 'bp_before_blog_post' ) ?>

					<?php $attachment_link = wp_get_attachment_link($post->ID, true, array(450, 800)); // This also populates the iconsize for the next line ?>
					<?php $_post = &get_post($post->ID); $classname = ($_post->iconsize[0] <= 128 ? 'small' : '') . 'attachment'; // This lets us style narrow icons specially ?>

					<div class="post" id="post-<?php the_ID(); ?>">

						<h2 class="posttitle"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a> &rarr; <a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>

						<div class="entry">
							<p class="<?php echo $classname; ?>"><?php echo $attachment_link; ?><br /><?php echo basename($post->guid); ?></p>

							<?php the_content( __('<p class="serif">Read the rest of this entry &rarr;</p>', 'cc' ) ); ?>

							<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', 'cc' ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
						</div>

					</div>

					<?php do_action( 'bp_after_blog_post' ) ?>

				<?php comments_template(); ?>

				<?php endwhile; else: ?>

					<p><?php _e( 'Sorry, no attachments matched your criteria.', 'cc' ) ?></p>

				<?php endif; ?>
		</div>

		<?php do_action( 'bp_after_attachment' ) ?>
	</div><!-- .padder -->
	</div>

<?php get_footer(); ?>