<?php
/*
Template Name: Single Proposal
*/
?>

<?php get_header() ?>
	<div id="content">
		<div class="padder">
		
			<div class="single-post-meta">
				<?php do_action( 'bp_before_blog_single_post' ) ?>
			</div>
			
			<div class="page" id="blog-single-proposal" role="main">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
			<?php
			//grab custom meta data
			$custom_fields = get_post_custom();
			$p_proposer &= $custom_fields['Proposer'];
			$p_date &= $custom_fields['Date'];
			$p_status &= $custom_fields['Status'];
			$p_funding &= $custom_fields['Funding'];
			$p_assembly &= $custom_fields['Assembly'];
			
			?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<div class="post-content">
						
						<span class="post-utility alignright"><?php edit_post_link( __( 'Edit this entry', 'buddypress' ) ); ?></span>
						
						<h1 class="proposal-proposer"><?=$p_proposer ?></h1>
						
						<h2 class="posttitle"><?php the_title(); ?></h2>

						<p class="date">
							<span class="proposal-status"><?=$p_status ?></span>							
							<span class="proposal-assembly"><?=$p_assembly ?></span>							
							<span class="proposal-funding"><?=$p_funding ?></span>
						</p>
						<p class="proposal-date"><?=$p_date ?></p>

						<div class="entry">
							<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>

							<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
						</div>

						<p class="postmetadata"><?php the_tags( '<span class="tags">' . __( 'Tags: ', 'buddypress' ), ', ', '</span>' ); ?>&nbsp;</p>

						<div class="alignleft"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'buddypress' ) . '</span> %title' ); ?></div>
						<div class="alignright"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'buddypress' ) . '</span>' ); ?></div>
					</div>

				</div>

			<?php comments_template(); ?>

			<?php endwhile; else: ?>

				<p><?php _e( 'Sorry, no posts matched your criteria.', 'buddypress' ) ?></p>

			<?php endif; ?>

		</div>

		<?php do_action( 'bp_after_blog_single_post' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar() ?>

<?php get_footer() ?>