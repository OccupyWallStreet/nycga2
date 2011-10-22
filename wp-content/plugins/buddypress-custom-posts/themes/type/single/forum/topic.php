<?php 
	global $bp;
	$ids = bp_forums_get_topic_id_from_slug( $bp->action_variables[1] );
	if ( bp_has_forum_topic_posts( Array( 'topic_id' => $ids ) ) ) : ?>

	<form action="<?php bp_forum_topic_action() ?>" method="post" id="forum-topic-form" class="standard-form">

		<div class="pagination no-ajax">

			<div id="post-count" class="pag-count">
				<?php bp_the_topic_pagination_count() ?>
			</div>

			<div class="pagination-links" id="topic-pag">
				<?php bp_the_topic_pagination() ?>
			</div>

		</div>

		<div id="topic-meta">
			<h3><?php bp_the_topic_title() ?> (<?php bp_the_topic_total_post_count() ?>)</h3>
			<a class="button" href="<?php bp_forum_permalink() ?>/">&larr; <?php _e( 'Forum', 'buddypress' ) ?></a>

			<div class="admin-links">
				<?php if ( current_user_can( 'edit_post' ) || bp_get_the_topic_is_mine() ) : ?>
					<?php bp_the_topic_admin_links() ?>
				<?php endif; ?>
			</div>
		</div>

		<ul id="topic-post-list" class="item-list">
			<?php while ( bp_forum_topic_posts() ) : bp_the_forum_topic_post(); ?>

				<li id="post-<?php bp_the_topic_post_id() ?>" class="<?php bp_the_topic_post_css_class() ?>">
					<div class="poster-meta">
						<a href="<?php bp_the_topic_post_poster_link() ?>">
							<?php bp_the_topic_post_poster_avatar( 'width=40&height=40' ) ?>
						</a>
						<?php echo sprintf( __( '%s said %s ago:', 'buddypress' ), bp_get_the_topic_post_poster_name(), bp_get_the_topic_post_time_since() ) ?>
					</div>

					<div class="post-content">
						<?php bp_the_topic_post_content() ?>
					</div>

					<div class="admin-links">
						<?php if ( current_user_can( 'edit_post' ) ) : ?>
							<?php bp_the_topic_post_admin_links() ?>
						<?php endif; ?>

						<a href="#post-<?php bp_the_topic_post_id() ?>" title="<?php _e( 'Permanent link to this post', 'buddypress' ) ?>">#</a>
					</div>
				</li>

			<?php endwhile; ?>
		</ul><!-- #topic-post-list -->

		<div class="pagination no-ajax">

			<div id="post-count" class="pag-count">
				<?php bp_the_topic_pagination_count() ?>
			</div>

			<div class="pagination-links" id="topic-pag">
				<?php bp_the_topic_pagination() ?>
			</div>

		</div>

		<?php if ( current_user_can( 'read_post' ) ) : ?>

			<?php if ( bp_get_the_topic_is_last_page() ) : ?>

				<?php if ( bp_get_the_topic_is_topic_open() ) : ?>

					<div id="post-topic-reply">
						<p id="post-reply"></p>

						<h4><?php _e( 'Add a reply:', 'buddypress' ) ?></h4>

						<textarea name="reply_text" id="reply_text"></textarea>

						<div class="submit">
							<input type="submit" name="submit_reply" id="submit" value="<?php _e( 'Post Reply', 'buddypress' ) ?>" />
						</div>

						<?php wp_nonce_field( 'bp_forums_new_reply' ) ?>
					</div>

				<?php else : ?>

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
