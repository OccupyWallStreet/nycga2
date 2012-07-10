<?php

/**
 * Edit Topic Tag
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php if ( current_user_can( 'edit_topic_tags' ) ) : ?>

	<div id="edit-topic-tag-<?php bbp_topic_tag_id(); ?>" class="bbp-topic-tag-form">

		<div class="bbp-form" id="bbp-edit-topic-tag">

			<div id="tag-rename">

				<h3><?php _e( 'Rename', 'bbpress' ); ?></h3>

				<div class="bbp-template-notice info">
					<p><?php _e( 'Leave the slug empty to have one automatically generated.', 'bbpress' ); ?></p>
				</div>

				<div class="bbp-template-notice">
					<p><?php _e( 'Changing the slug affects its permalink. Any links to the old slug will stop working.', 'bbpress' ); ?></p>
				</div>

				<form id="rename_tag" name="rename_tag" method="post" action="">

					<p>
						<label for="tag-name"><?php _e( 'Name:', 'bbpress' ); ?></label>
						<input type="text" id="tag-name" name="tag-name" size="20" maxlength="40" tabindex="<?php bbp_tab_index(); ?>" value="<?php echo esc_attr( bbp_get_topic_tag_name() ); ?>" />
					</p>

					<p>
						<label for="tag-slug"><?php _e( 'Slug:', 'bbpress' ); ?></label>
						<input type="text" id="tag-slug" name="tag-slug" size="20" maxlength="40" tabindex="<?php bbp_tab_index(); ?>" value="<?php echo esc_attr( apply_filters( 'editable_slug', bbp_get_topic_tag_slug() ) ); ?>" />
					</p>

					<div class="bbp-submit-wrapper">
						<input type="submit" name="submit" tabindex="<?php bbp_tab_index(); ?>" value="<?php esc_attr_e( 'Update', 'bbpress' ); ?>" /><br />

						<input type="hidden" name="tag-id" value="<?php bbp_topic_tag_id(); ?>" />
						<input type="hidden" name="action" value="bbp-update-topic-tag" />

						<?php wp_nonce_field( 'update-tag_' . bbp_get_topic_tag_id() ); ?>

					</div>
				</form>

			</div>

			<div id="tag-merge">

				<h3><?php _e( 'Merge', 'bbpress' ); ?></h3>

				<div class="bbp-template-notice">
					<p><?php _e( 'Merging tags together cannot be undone.', 'bbpress' ); ?></p>
				</div>

				<form id="merge_tag" name="merge_tag" method="post" action="">

					<p>
						<label for="tag-existing-name"><?php _e( 'Existing tag:', 'bbpress' ); ?></label>
						<input type="text" id="tag-existing-name" name="tag-existing-name" size="22" tabindex="<?php bbp_tab_index(); ?>" maxlength="40" />
					</p>

					<div class="bbp-submit-wrapper">
						<input type="submit" name="submit" tabindex="<?php bbp_tab_index(); ?>" value="<?php esc_attr_e( 'Merge', 'bbpress' ); ?>"
							onclick="return confirm('<?php echo esc_js( sprintf( __( 'Are you sure you want to merge the "%s" tag into the tag you specified?', 'bbpress' ), bbp_get_topic_tag_name() ) ); ?>');" />

						<input type="hidden" name="tag-id" value="<?php bbp_topic_tag_id(); ?>" />
						<input type="hidden" name="action" value="bbp-merge-topic-tag" />

						<?php wp_nonce_field( 'merge-tag_' . bbp_get_topic_tag_id() ); ?>
					</div>
				</form>

			</div>

			<?php if ( current_user_can( 'delete_topic_tags' ) ) : ?>

			<div id="delete-tag">

				<h3><?php _e( 'Delete', 'bbpress' ); ?></h3>

				<div class="bbp-template-notice info">
					<p><?php _e( 'This does not delete your topics. Only the tag itself is deleted.', 'bbpress' ); ?></p>
				</div>
				
				<div class="bbp-template-notice">
					<p><?php _e( 'Deleting a tag cannot be undone.', 'bbpress' ); ?></p>
					<p><?php _e( 'Any links to this tag will no longer function.', 'bbpress' ); ?></p>
				</div>

				<form id="delete_tag" name="delete_tag" method="post" action="">

					<div class="bbp-submit-wrapper">
						<input type="submit" name="submit" tabindex="<?php bbp_tab_index(); ?>" value="<?php _e( 'Delete', 'bbpress' ); ?>"
								onclick="return confirm('<?php echo esc_js( sprintf( __( 'Are you sure you want to delete the "%s" tag? This is permanent and cannot be undone.', 'bbpress' ), bbp_get_topic_tag_name() ) ); ?>');" />

						<input type="hidden" name="tag-id" value="<?php bbp_topic_tag_id(); ?>" />
						<input type="hidden" name="action" value="bbp-delete-topic-tag" />

						<?php wp_nonce_field( 'delete-tag_' . bbp_get_topic_tag_id() ); ?>
					</div>
				</form>

			</div>

			<?php endif; ?>

		</div>
	</div>

<?php endif; ?>
