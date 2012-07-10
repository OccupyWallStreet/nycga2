<?php

/**
 * BuddyPress - Forum Single Topic
 *
 * @package BuddyPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bp_before_group_forum_topic' ); ?>

<?php if ( bp_has_forum_topic_posts() ) : ?>

	<form action="<?php bp_forum_topic_action() ?>" method="post" id="forum-topic-form" class="standard-form">

		<div class="item-list-tabs bp-sub-tabs no-ajax" id="subnav" role="navigation">
			<ul>
				<?php if ( is_user_logged_in() ) : ?>

					<li>
						<a href="<?php bp_forum_topic_new_reply_link() ?>" class="new-reply-link"><?php _e( 'New Reply', 'buddypress' ) ?></a>
					</li>

				<?php endif; ?>

				<?php if ( bp_forums_has_directory() ) : ?>

					<li>
						<a href="<?php bp_forums_directory_permalink() ?>"><?php _e( 'Forum Directory', 'buddypress') ?></a>
					</li>

				<?php endif; ?>

			</ul>
		</div><!-- #subnav -->

		<h2 class="entry-title"><?php bp_the_topic_title() ?> (<?php bp_the_topic_total_post_count() ?>)</h2>

		<?php if ( bp_forum_topic_has_tags() ) : ?>

			<div class="entry-meta">

				<span class="category">
					<?php _e( 'Topic tags:', 'buddypress' ) ?> <?php bp_forum_topic_tag_list() ?>
				</span><!-- .category -->

			</div>

		<?php endif; ?>
		
		<?php if ( bp_group_is_admin() || bp_group_is_mod() || bp_get_the_topic_is_mine() ) : ?>

			<div class="entry-meta last admin-links">

				<?php bp_the_topic_admin_links( array( 'seperator' => '' ) ) ?>

			</div>

		<?php endif; ?>

		<?php do_action( 'bp_group_forum_topic_meta' ); ?>

		<div class="pagination bp-pagination no-ajax">

			<div id="post-count-top" class="pag-count">
				<?php bp_the_topic_pagination_count() ?>
			</div>

			<div class="pagination-links" id="topic-pag-top">
				<?php bp_the_topic_pagination() ?>
			</div>

		</div><!-- .bp-pagination -->

		<?php do_action( 'bp_before_group_forum_topic_posts' ) ?>
		
		<div id="comments-template">
			<div id="comments">
				<ol id="topic-post-list" class="comment-list" role="main">
					<?php while ( bp_forum_topic_posts() ) : bp_the_forum_topic_post(); ?>

						<li id="post-<?php bp_the_topic_post_id() ?>" class="<?php bp_the_topic_post_css_class() ?>">
						
							<div class="comment-wrap">
							
								<div class="comment-header">
									<a href="<?php bp_the_topic_post_poster_link() ?>">
										<?php bp_the_topic_post_poster_avatar( 'width=50&height=50' ) ?>
									</a>
									<div class="comment-meta">
									
										<span class="comment-author vcard">
											<cite class="fn">
												<?php	echo sprintf( __( '%1$s said %2$s:', 'buddypress' ), bp_get_the_topic_post_poster_name(), bp_get_the_topic_post_time_since() ) ?>
											</cite>
										</span><!-- .comment-author .vcard -->
										
										<?php if ( bp_group_is_admin() || bp_group_is_mod() || bp_get_the_topic_post_is_mine() ) : ?>
											<?php bp_the_topic_post_admin_links() ?>
										<?php endif; ?>

										<?php do_action( 'bp_group_forum_post_meta' ); ?>

										<a href="#post-<?php bp_the_topic_post_id() ?>" title="<?php _e( 'Permanent link to this post', 'buddypress' ) ?>">Permalink</a>
									</div><!-- .comment-meta -->
								</div><!-- .comment-header -->

								<div class="comment-content comment-text">
									<?php bp_the_topic_post_content() ?>
								</div><!-- .commen-text -->
							
							</div><!-- .comment-wrap -->
							
						</li>

					<?php endwhile; ?>
				</ol><!-- #topic-post-list -->
			</div><!-- #comments -->
		</div><!-- #comments-template -->

		<?php do_action( 'bp_after_group_forum_topic_posts' ) ?>

		<div class="pagination bp-pagination no-ajax">

			<div id="post-count-bottom" class="pag-count">
				<?php bp_the_topic_pagination_count() ?>
			</div>

			<div class="pagination-links" id="topic-pag-bottom">
				<?php bp_the_topic_pagination() ?>
			</div>

		</div><!-- .bp-pagination -->

		<?php if ( ( is_user_logged_in() && 'public' == bp_get_group_status() ) || bp_group_is_member() ) : ?>

			<?php if ( bp_get_the_topic_is_last_page() ) : ?>

				<?php if ( bp_get_the_topic_is_topic_open() && !bp_group_is_user_banned() ) : ?>

					<div id="post-topic-reply">
					
						<div class="entry-content">
						
							<a id="post-reply"></a>

							<?php if ( bp_groups_auto_join() && !bp_group_is_member() ) : ?>
								<p><?php _e( 'You will auto join this group when you reply to this topic.', 'buddypress' ) ?></p>
							<?php endif; ?>

							<?php do_action( 'groups_forum_new_reply_before' ) ?>

							<h3><?php _e( 'Add a reply:', 'buddypress' ) ?></h3>

							<textarea name="reply_text" id="reply_text"></textarea>

							<p class="submit">
								<input type="submit" name="submit_reply" id="submit" value="<?php _e( 'Post Reply', 'buddypress' ) ?>" />
							</p>

							<?php do_action( 'groups_forum_new_reply_after' ) ?>

							<?php wp_nonce_field( 'bp_forums_new_reply' ) ?>
						
						</div><!-- .entry-content -->
						
					</div><!-- #post-topic-reply -->

				<?php elseif ( !bp_group_is_user_banned() ) : ?>

					<div id="message" class="info">
						<p><?php _e( 'This topic is closed, replies are no longer accepted.', 'buddypress' ) ?></p>
					</div>

				<?php endif; ?>

			<?php endif; ?>

		<?php endif; ?>

	</form><!-- #forum-topic-form -->

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There are no posts for this topic.', 'buddypress' ) ?></p>
	</div>

<?php endif;?>

<?php do_action( 'bp_after_group_forum_topic' ) ?>
