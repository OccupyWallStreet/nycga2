<?php get_header() ?>

	<div id="content">
		<div class="padder">

			<?php if ( bp_groupblog_blog_exists( bp_get_groupblog_id() ) ) : ?>
				<?php if ( bp_has_groups( 'type=single-group&slug=' . bp_get_groupblog_slug() ) ) : while ( bp_groups() ) : bp_the_group(); ?>
				
					<?php locate_template( array( 'groupblog/group-header.php' ), true ) ?>
					
					<?php do_action( 'bp_before_blog_page' ) ?>
			
					<div class="page" id="blog-page">

						<?php if ( bp_group_is_visible() ) : ?>
			
							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				
								<h2 class="pagetitle"><?php the_title(); ?></h2>
				
								<div class="post" id="post-<?php the_ID(); ?>">
				
									<div class="entry">
				
										<?php the_content( __( '<p class="serif">Read the rest of this page &rarr;</p>', 'buddypress' ) ); ?>
				
										<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', 'buddypress' ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
										<?php edit_post_link( __( 'Edit this entry.', 'buddypress' ), '<p>', '</p>'); ?>
				
									</div>
				
								</div>
				
							<?php endwhile; endif; ?>

						<?php elseif ( !bp_group_is_visible() ) : ?>
						
							<?php /* The group is not visible, show the status message */ ?>
			
							<?php do_action( 'bp_before_group_status_message' ) ?>
			
							<div id="message" class="info">
								<p><?php bp_group_status_message() ?></p>
							</div>
			
							<?php do_action( 'bp_after_group_status_message' ) ?>
						<?php endif; ?>
									
					</div><!-- .page -->
			
					<?php do_action( 'bp_after_blog_page' ) ?>

				<?php endwhile; endif; ?>
			<?php endif; ?>
			
		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer(); ?>
