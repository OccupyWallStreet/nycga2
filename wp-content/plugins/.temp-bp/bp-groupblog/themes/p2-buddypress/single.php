<?php get_header() ?>

	<div id="content">
		<div class="padder">

			<?php if ( bp_groupblog_blog_exists( bp_get_groupblog_id() ) ) : ?>
				<?php if ( bp_has_groups( 'type=single-group&slug=' . bp_get_groupblog_slug() ) ) : while ( bp_groups() ) : bp_the_group(); ?>
				
					<?php locate_template( array( 'groupblog/group-header.php' ), true ) ?>
					
					<?php do_action( 'bp_before_blog_single_post' ) ?>
			
					<div class="page" id="blog-single">

						<?php if ( bp_group_is_visible() ) : ?>	
								
							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				
								<div class="item-options">
				
									<div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'buddypress' ) ) ?></div>
									<div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'buddypress' ) ) ?></div>
				
								</div>
				
								<div class="post" id="post-<?php the_ID(); ?>">
				
									<div class="author-box">
										<?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
										<p><?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></p>
									</div>
				
									<div class="post-content">
										<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				
										<p class="date"><?php the_time('F j, Y') ?> <em><?php _e( 'in', 'buddypress' ) ?> <?php the_category(', ') ?> <?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></em></p>
				
										<div class="entry">
											<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
				
											<?php wp_link_pages(array('before' => __( '<p><strong>Pages:</strong> ', 'buddypress' ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
										</div>
				
										<?php include( 'groupblog/comments.php' ) ?>
									</div>
				
								</div>
				
							<?php //comments_template(); ?>
				
							<?php endwhile; else: ?>
				
								<p><?php _e( 'Sorry, no posts matched your criteria.', 'buddypress' ) ?></p>
				
							<?php endif; ?>

						<?php elseif ( !bp_group_is_visible() ) : ?>
						
							<?php /* The group is not visible, show the status message */ ?>
			
							<?php do_action( 'bp_before_group_status_message' ) ?>
			
							<div id="message" class="info">
								<p><?php bp_group_status_message() ?></p>
							</div>
			
							<?php do_action( 'bp_after_group_status_message' ) ?>
						<?php endif; ?>
			
					</div>
			
					<?php do_action( 'bp_after_blog_single_post' ) ?>

				<?php endwhile; endif; ?>
			<?php endif; ?>
			
		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>