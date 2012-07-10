<?php

/**
 * Split Topic
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

	<?php if ( is_user_logged_in() && current_user_can( 'edit_topic', bbp_get_topic_id() ) ) : ?>

		<div id="split-topic-<?php bbp_topic_id(); ?>" class="bbp-topic-split">

			<form id="split_topic" name="split_topic" method="post" action="">

				<div class="bbp-form">

					<h3><?php printf( __( 'Split topic "%s"', 'bbpress' ), bbp_get_topic_title() ); ?></h3>

					<div class="bbp-template-notice info">
						<p><?php _e( 'When you split a topic, you are slicing it in half starting with the reply you just selected. Choose to use that reply as a new topic with a new title, or merge those replies into an existing topic.', 'bbpress' ); ?></p>
					</div>

					<div class="bbp-template-notice">
						<p><?php _e( 'If you use the existing topic option, replies within both topics will be merged chronologically. The order of the merged replies is based on the time and date they were posted.', 'bbpress' ); ?></p>
					</div>

					<h3><?php _e( 'Split Method', 'bbpress' ); ?></h3>

					<p>
						<input name="bbp_topic_split_option" id="bbp_topic_split_option_reply" type="radio" checked="checked" value="reply" tabindex="<?php bbp_tab_index(); ?>" /> <label for="bbp_topic_split_option_reply"><?php printf( __( 'New topic in <strong>%s</strong> titled:', 'bbpress' ), bbp_get_forum_title( bbp_get_topic_forum_id( bbp_get_topic_id() ) ) ); ?></label>
						
						<input type="text" id="bbp_topic_split_destination_title" value="<?php printf( __( 'Split: %s', 'bbpress' ), bbp_get_topic_title() ); ?>" tabindex="<?php bbp_tab_index(); ?>" size="35" name="bbp_topic_split_destination_title" />
					</p>

					<?php if ( bbp_has_topics( array( 'show_stickies' => false, 'post_parent' => bbp_get_topic_forum_id( bbp_get_topic_id() ), 'post__not_in' => array( bbp_get_topic_id() ) ) ) ) : ?>

					<p>
						<input name="bbp_topic_split_option" id="bbp_topic_split_option_existing" type="radio" value="existing" tabindex="<?php bbp_tab_index(); ?>" /> <label for="bbp_topic_split_option_existing"><?php _e( 'Use an existing topic in this forum:', 'bbpress' ); ?></label>

						<?php
							bbp_dropdown( array(
								'post_type'   => bbp_get_topic_post_type(),
								'post_parent' => bbp_get_topic_forum_id( bbp_get_topic_id() ),
								'selected'    => -1,
								'exclude'     => bbp_get_topic_id(),
								'select_id'   => 'bbp_destination_topic',
								'none_found'  => __( 'No other topics found!', 'bbpress' )
							) );
						?>

					</p>

					<?php endif; ?>

					<h3><?php _e( 'Topic Extras', 'bbpress' ); ?></h3>

					<p>

						<?php if ( bbp_is_subscriptions_active() ) : ?>

						<input name="bbp_topic_subscribers" id="bbp_topic_subscribers" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" /> <label for="bbp_topic_subscribers"><?php _e( 'Copy subscribers to the new topic', 'bbpress' ); ?></label><br />

						<?php endif; ?>
								
						<input name="bbp_topic_favoriters" id="bbp_topic_favoriters" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" /> <label for="bbp_topic_favoriters"><?php _e( 'Copy favoriters to the new topic', 'bbpress' ); ?></label><br />
							
						<input name="bbp_topic_tags" id="bbp_topic_tags" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" /> <label for="bbp_topic_tags"><?php _e( 'Copy topic tags to the new topic', 'bbpress' ); ?></label><br />

					</p>

					<div class="bbp-template-notice error">
						<p><?php _e( '<strong>WARNING:</strong> This process cannot be undone.', 'bbpress' ); ?></p>
					</div>

					<div class="bbp-submit-wrapper">
						<button type="submit" tabindex="<?php bbp_tab_index(); ?>" id="bbp_merge_topic_submit" name="bbp_merge_topic_submit" class="button submit"><?php _e( 'Submit', 'bbpress' ); ?></button>
					</div>

					<?php bbp_split_topic_form_fields(); ?>

				</div>
				
			</form>
			
		</div>

	<?php else : ?>

		<div id="no-topic-<?php bbp_topic_id(); ?>" class="bbp-no-topic">
			<div class="bbp-template-notice"><p><?php is_user_logged_in() ? _e( 'You do not have the permissions to edit this topic!', 'bbpress' ) : _e( 'You cannot edit this topic.', 'bbpress' ); ?></p></div>
		</div>

	<?php endif; ?>
