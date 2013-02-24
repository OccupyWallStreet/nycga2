<form action="<?php bp_activity_post_form_action() ?>" method="post" id="swa-whats-new-form" name="swa-whats-new-form">

	<?php do_action( 'bp_before_activity_post_form' ) ?>

	
<!--
	<div id="whats-new-avatar">
		<a href="<?php echo bp_loggedin_user_domain() ?>">
			<?php bp_loggedin_user_avatar( 'width=60&height=60' ) ?>
		</a>
	</div> -->

	<h5>
			<?php printf( __( "What's new %s?", 'buddypress' ), bp_get_user_firstname() ) ?>
		
	</h5>

	<div id="swa-whats-new-content">
		<div id="swa-whats-new-textarea">
			<textarea name="whats-new" id="swa-whats-new" cols="30" rows="4"></textarea>
		</div>

		<div id="swa-whats-new-options">
			

			<?php if ( function_exists('bp_has_groups') && !bp_is_my_profile() && !bp_is_group() ) : ?>
				<div id="swa-whats-new-post-in-box">
					<?php _e( 'Post in', 'buddypress' ) ?>:

					<select id="swa-whats-new-post-in" name="swa-whats-new-post-in">
						<option selected="selected" value="0"><?php _e( 'My Profile', 'buddypress' ) ?></option>

						<?php if ( bp_has_groups( 'user_id=' . bp_loggedin_user_id() . '&type=alphabetical&max=100&per_page=100&populate_extras=0' ) ) : while ( bp_groups() ) : bp_the_group(); ?>
							<option value="<?php bp_group_id() ?>"><?php bp_group_name() ?></option>
						<?php endwhile; endif; ?>
					</select>
				</div>
				<input type="hidden" id="swa-whats-new-post-object" name="swa-whats-new-post-object" value="groups" />
			<?php elseif ( bp_is_group_home() ) : ?>
				<input type="hidden" id="swa-whats-new-post-object" name="swa-whats-new-post-object" value="groups" />
				<input type="hidden" id="swa-whats-new-post-in" name="swa-whats-new-post-in" value="<?php bp_group_id() ?>" />
			<?php endif; ?>
                        <div id="whats-new-submit">
				<span class="ajax-loader"></span> &nbsp;
				<input type="submit" name="swa-whats-new-submit" id="swa-whats-new-submit" value="<?php _e( 'Post Update', 'buddypress' ) ?>" />
			</div>
			<?php do_action( 'bp_activity_post_form_options' ) ?>

		</div><!-- #whats-new-options -->
	</div><!-- #whats-new-content -->

	<?php wp_nonce_field( 'swa_post_update', '_wpnonce_swa_post_update' ); ?>
	<?php do_action( 'bp_after_activity_post_form' ) ?>

</form><!-- #whats-new-form -->
