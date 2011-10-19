<div id="new_post_wrapper">

	<div class="generic-button" id="create_post"><a href=""><?php _e('Create Post', 'groupblog'); ?></a></div>

	<form id="new_post" name="new_post" method="post" action="<?php echo site_url(); ?>" style="display:none;" />

		<div id="whats-new-avatar">
			<a href="<?php echo bp_loggedin_user_domain() ?>">
				<?php bp_loggedin_user_avatar( 'width=60&height=60' ) ?>
			</a>
		</div>

		<div id="whats-new-type"
			<ul id="post-types">
				<?php $action = isset( $_GET['p'] ) ? $_GET['p'] : '' ?>
				<li><a id="status"<?php if ( $action == 'status' || !isset($_GET['p']) ) : ?> class="selected"<?php endif; ?> href="<?php echo site_url( '?p=status' ) ?>" title="<?php _e( 'Status Update', 'p2' ) ?>"><?php _e( 'Status Update', 'p2' ) ?></a></li>
				<li><a id="post"<?php if ( $action == 'post' ) : ?> class="selected"<?php endif; ?> href="<?php echo site_url( '?p=post' ) ?>" title="<?php _e( 'Post', 'p2' ) ?>"><?php _e( 'Post', 'p2' ) ?></a></li>
				<li><a id="photo"<?php if ( $action == 'photo' ) : ?> class="selected"<?php endif; ?> href="<?php echo site_url( '?p=photo' ) ?>" title="<?php _e( 'Photo', 'p2' ) ?>"><?php _e( 'Photo', 'p2' ) ?></a></li>
				<li><a id="video"<?php if ( $action == 'video' ) : ?> class="selected"<?php endif; ?> href="<?php echo site_url( '?p=video' ) ?>" title="<?php _e( 'Video', 'p2' ) ?>"><?php _e( 'Video', 'p2' ) ?></a></li>

				<?php if ( groups_is_user_admin( bp_loggedin_user_id(), bp_get_group_id() ) || groups_is_user_mod( bp_loggedin_user_id(), bp_get_group_id() ) ) : ?>
					<li><a id="featured"<?php if ( $action == 'featured' ) : ?> class="selected"<?php endif; ?> href="<?php echo site_url( '?p=featured' ) ?>" title="<?php _e( 'Featured', 'p2' ) ?>"><?php _e( 'Featured', 'p2' ) ?></a></li>
				<?php endif; ?>
			</ul>
		</div>

		<div id="whats-new-content" class="inputarea">

			<div id="whats-new-title">
				<?php if ( 'status' == p2_get_posting_type() || '' == p2_get_posting_type() ) : ?>
					<h5 id="whats-new-status"><?php p2_user_prompt() ?></h5>
				<?php endif; ?>

				<div id="whats-new-post" class="post-input<?php if ( 'post' == p2_get_posting_type() ) echo ' selected'; ?>">
					<input type="text" name="posttitle" id="posttitle" tabindex="1" value="" />
				</div>

				<div id="char-count"><span id='counter'>140</span></div>

				<?php if ( current_user_can( 'upload_files' ) ): ?>
					<div id="media-buttons" class="hide-if-no-js" style="display:none;">
						<?php echo P2::media_buttons(); ?>
					</div>
				<?php endif; ?>
			</div>

			<div id="whats-new-textarea" class="status">
				<textarea class="expand70-200" name="posttext" id="posttext" tabindex="1" rows="3" cols="60"><?php if ( isset( $_GET['r'] ) ) : ?>@<?php echo esc_attr( $_GET['r'] ) ?> <?php endif; ?></textarea>
			</div>

			<div id="whats-new-options">
				<div id="whats-new-tags"class="post-input">
					<input id="tags" name="tags" type="text" tabindex="2" autocomplete="off"
						value="<?php echo esc_attr( __( 'Tag it', 'p2' ) ); ?>"
						onfocus="this.value=(this.value=='<?php echo esc_js( __( 'Tag it', 'p2' ) ); ?>') ? '' : this.value;"
						onblur="this.value=(this.value=='') ? '<?php echo esc_js( __( 'Tag it', 'p2' ) ); ?>' : this.value;" />
				</div>

				<div id="whats-new-submit">
					<span class="progress" id="ajaxActivity">
						<img src="<?php echo str_replace( WP_CONTENT_DIR, content_url(), locate_template( array( 'i/indicator.gif' ) ) ) ?>" />
					</span>
					<input id="submit" type="submit" tabindex="3" value="<?php _e( 'Post Update', 'buddypress' ); ?>" />
				</div>

				<?php if ( bp_get_groupblog_id() ) : ?>
					<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
					<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php bp_groupblog_id() ?>" />
				<?php elseif ( !bp_get_groupblog_id() ) : ?>
					<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="blog" />
					<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php blog_id() ?>" />
				<?php endif; ?>

				<input type="hidden" name="post_cat" id="post_cat" value="<?php echo ( isset( $_GET['p'] ) ) ? esc_attr( $_GET['p'] ) : 'status' ?>" />
				<input type="hidden" name="action" value="post" />

			</div><!-- #whats-new-options -->
		</div><!-- #whats-new-content -->

		<?php wp_nonce_field( 'new-post' ); ?>

		<?php /* Here we stick the nonce from the BuddyPress Activity Post Form. */?>
		<?php /* We renamed it to reflect p2, we also included some other stuff here. */?>
		<?php /* Please compare to the original P2 post form to see the changes. */?>
		<?php wp_nonce_field( 'p2_post_update', '_wpnonce_p2_post_update' ); ?>

	</form>

</div>