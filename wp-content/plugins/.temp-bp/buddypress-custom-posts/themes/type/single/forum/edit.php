<?php 
	global $bp;
	$ids = bp_forums_get_topic_id_from_slug( $bp->action_variables[1] );
	if ( bp_has_forum_topic_posts( Array( 'topic_id' => $ids ) ) ) : ?>

	<form action="<?php bp_forum_topic_action() ?>" method="post" id="forum-topic-form" class="standard-form">

		<div id="topic-meta">
			<h3><?php bp_the_topic_title() ?> (<?php bp_the_topic_total_post_count() ?>)</h3>
			<a class="button" href="<?php bp_forum_permalink() ?>/">&larr; <?php _e( 'Forum', 'buddypress' ) ?></a>

			<?php if ( current_user_can( 'edit_post' ) || bp_get_the_topic_is_mine() ) : ?>
				<div class="admin-links"><?php bp_the_topic_admin_links() ?></div>
			<?php endif; ?>
		</div>
			<?php if ( bpcp_is_edit_topic() ) : ?>

				<div id="edit-topic">

					<p><strong><?php _e( 'Edit Topic:', 'buddypress' ) ?></strong></p>

					<label for="topic_title"><?php _e( 'Title:', 'buddypress' ) ?></label>
					<input type="text" name="topic_title" id="topic_title" value="<?php bp_the_topic_title() ?>" />

					<label for="topic_text"><?php _e( 'Content:', 'buddypress' ) ?></label>
					<textarea name="topic_text" id="topic_text"><?php bp_the_topic_text() ?></textarea>

					<p class="submit"><input type="submit" name="save_changes" id="save_changes" value="<?php _e( 'Save Changes', 'buddypress' ) ?>" /></p>

					<?php wp_nonce_field( 'bp_forums_edit_topic' ) ?>

				</div>

			<?php else : ?>

				<div id="edit-post">

					<p><strong><?php _e( 'Edit Post:', 'buddypress' ) ?></strong></p>

					<textarea name="post_text" id="post_text"><?php bp_the_topic_post_edit_text() ?></textarea>

					<p class="submit"><input type="submit" name="save_changes" id="save_changes" value="<?php _e( 'Save Changes', 'buddypress' ) ?>" /></p>

					<?php wp_nonce_field( 'bp_forums_edit_post' ) ?>

				</div>

			<?php endif; ?>

		<?php //endif; ?>

	</form><!-- #forum-topic-form -->

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'This topic does not exist.', 'buddypress' ) ?></p>
	</div>

<?php endif;?>
