<?php

/**
 * BuddyPress - Forums Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter()
 *
 * @package BuddyPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bp_before_forums_loop' ); ?>

<?php if ( bp_has_forum_topics( bp_ajax_querystring( 'forums' ) ) ) : ?>

	<div id="pag-top" class="pagination bp-pagination">

		<div class="pag-count" id="topic-count-top">
			<?php bp_forum_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="topic-pag-top">
			<?php bp_forum_pagination(); ?>
		</div>

	</div><!-- .bp-pagination -->

	<?php do_action( 'bp_before_directory_forums_list' ); ?>
	
	<ul class="loop-entries bp-topics">

		<?php while ( bp_forum_topics() ) : bp_the_forum_topic(); ?>

		<li class="<?php bp_the_topic_css_class(); ?>">

			<h2 class="entry-title">
				<a class="topic-title" href="<?php bp_the_topic_permalink(); ?>" title="<?php bp_the_topic_title(); ?> - <?php _e( 'Permalink', 'buddypress' ); ?>"><?php bp_the_topic_title(); ?></a>
			</h2><!-- .entry-title -->

			<div class="byline">
			
				<span class="bp-topic-reply-count">
					<?php bp_the_topic_total_posts(); ?> <?php _e( 'Replies', 'fanwood' ); ?>
				</span>
				
				<span class="bp-topic-freshness">
					<?php bp_the_topic_time_since_last_post(); ?>
				</span>
				
				<span class="bp-topic-freshness-author">
					<?php _e( 'Latest by: ', 'buddypress' ); ?><?php bp_the_topic_last_poster_name(); ?>
				</span>

				<?php if ( !bp_is_group_forum() ) : ?>

					<span class="bp-topic-started-in">
						<?php
							$topic_in = '<a href="' . bp_get_the_topic_object_permalink() . '" title="' . bp_get_the_topic_object_name() . '">' . bp_get_the_topic_object_name() .'</a>';

							printf( __( '%1$s', 'buddypress' ), $topic_in );
						?>
					</span>

				<?php endif; ?>
						
				</div><!-- .byline -->

				<?php do_action( 'bp_directory_forums_extra_cell' ); ?>

			<?php do_action( 'bp_directory_forums_extra_row' ); ?>

		</li>
			
		<?php endwhile; ?>
	
	</ul><!-- .loop-entries -->

	<?php do_action( 'bp_after_directory_forums_list' ); ?>

	<div id="pag-bottom" class="pagination bp-pagination">

		<div class="pag-count" id="topic-count-bottom">
			<?php bp_forum_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="topic-pag-bottom">
			<?php bp_forum_pagination(); ?>
		</div>

	</div><!-- .bp-pagination -->

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, there were no forum topics found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_forums_loop' ); ?>
