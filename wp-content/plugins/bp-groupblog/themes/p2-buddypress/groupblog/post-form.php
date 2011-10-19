<form action="<?php bp_activity_post_form_action() ?>" method="post" id="whats-new-form" name="whats-new-form">

	<?php do_action( 'bp_before_activity_post_form' ) ?>

	<?php if ( isset( $_GET['r'] ) ) : ?>
		<div id="message" class="info">
			<p><?php printf( __( 'You are mentioning %s in a new update, this user will be sent a notification of your message.', 'buddypress' ), bp_get_mentioned_user_display_name( $_GET['r'] ) ) ?></p>
		</div>
	<?php endif; ?>

	<div id="whats-new-avatar">
		<a href="<?php echo bp_loggedin_user_domain() ?>">
			<?php bp_loggedin_user_avatar( 'width=60&height=60' ) ?>
		</a>
	</div>

	<h5>
		<?php if ( bp_get_groupblog_id() ) : ?>
			<?php printf( __( "What's new in %s, %s?", 'buddypress' ), bp_get_group_name(), bp_get_user_firstname() ) ?>
		<?php endif; ?>
	</h5>

	<div id="whats-new-content">
		<div id="whats-new-textarea">
			<textarea name="whats-new" id="whats-new" value="" /><?php if ( isset( $_GET['r'] ) ) : ?>@<?php echo esc_attr( $_GET['r'] ) ?> <?php endif; ?></textarea>
		</div>

		<div id="whats-new-options">
			<div id="whats-new-submit">
				<span class="ajax-loader"></span> &nbsp;
				<input type="submit" name="aw-whats-new-submit" id="aw-whats-new-submit" value="<?php _e( 'Post Update', 'buddypress' ) ?>" />
			</div>
			
			<?php if ( bp_get_groupblog_id() ) : ?>
				<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
				<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php bp_groupblog_id() ?>" />
			<?php endif; ?>

			<?php do_action( 'bp_activity_post_form_options' ) ?>

		</div><!-- #whats-new-options -->
	</div><!-- #whats-new-content -->

	<?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>
	<?php do_action( 'bp_after_activity_post_form' ) ?>

</form><!-- #whats-new-form -->
