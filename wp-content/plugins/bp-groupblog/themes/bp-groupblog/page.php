<?php get_header() ?>

	<div id="content">
		<div class="padder">
			<?php if ( bp_has_groups( 'type=single-group&slug=' . bp_get_groupblog_slug() ) ) : while ( bp_groups() ) : bp_the_group(); ?>
			
			<?php do_action( 'bp_before_blog_page' ) ?>

			<div id="item-header">
				<?php locate_template( array( 'groups/single/group-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_groupblog_options_nav() ?>

						<?php do_action( 'bp_group_options_nav' ) ?>
					</ul>
				</div>
			</div>
				
			<div class="page" id="blog-page">
	
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
	
			</div><!-- .page -->
	
			<?php do_action( 'bp_after_blog_page' ) ?>

			<?php endwhile; endif; ?>
		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer(); ?>
