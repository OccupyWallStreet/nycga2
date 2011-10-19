<?php if ( bpcp_is_forum_topic_edit() ) : ?>
	<?php bpcp_locate_template( Array( 'type/single/forum/edit.php' ), true ) ?>

<?php elseif ( bpcp_is_forum_topic() ) : ?>
	<?php bpcp_locate_template( Array( 'type/single/forum/topic.php' ), true ) ?>

<?php else : ?>

	<div class="forums single-forum">
		<?php bpcp_locate_template( array( 'forums/forums-type-loop.php' ), true ) ?>
	</div><!-- .forums.single-forum -->

<?php endif; ?>

<?php if ( !bpcp_is_forum_topic_edit() && !bpcp_is_forum_topic() ) : ?>

	<?php global $post; if ( current_user_can( 'read_post', $post->ID ) ) : ?>

		<form action="" method="post" id="forum-topic-form" class="standard-form">
			<div id="post-new-topic">

				<p id="post-new"></p>
				<h4><?php _e( 'Post a New Topic:', 'buddypress' ) ?></h4>

				<label><?php _e( 'Title:', 'buddypress' ) ?></label>
				<input type="text" name="topic_title" id="topic_title" value="" />

				<label><?php _e( 'Content:', 'buddypress' ) ?></label>
				<textarea name="topic_text" id="topic_text"></textarea>

				<label><?php _e( 'Tags (comma separated):', 'buddypress' ) ?></label>
				<input type="text" name="topic_tags" id="topic_tags" value="" />

				<div class="submit">
					<input type="submit" name="submit_topic" id="submit" value="<?php _e( 'Post Topic', 'buddypress' ) ?>" />
				</div>

				<?php wp_nonce_field( 'bp_forums_new_topic' ) ?>
			</div><!-- #post-new-topic -->
		</form><!-- #forum-topic-form -->

	<?php endif; ?>

<?php endif; ?>

