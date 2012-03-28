<?php
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die (__('Please do not load this page directly. Thanks!', CI_DOMAIN));



	ci_comment_form(array(
		'comment_notes_before' => '',
		'comment_notes_after'  => ''
	));

?>





<?php
// Based on WP 3.0 wp-includes/comment-template.php / comment_form()
// Customized for CSSIgniter, keeping existing functionality, i.e. hooks, etc.
function ci_comment_form( $args = array(), $post_id = null ) {
	global $user_identity, $id;

	if ( null === $post_id )
		$post_id = $id;
	else
		$id = $post_id;

	$commenter = wp_get_current_commenter();

	$req = get_option( 'require_name_email' );
	$req_string = ($req ? '<span class="required">*</span>' : '');
	$aria_req = ( $req ? " aria-required='true'" : '' );
	
	$fields =  array(
		'author' => '<fieldset><label for="author">' . __('Name') . ': '.$req_string.'</label><input type="text" class="text input-text" id="author" name="author" value="' . esc_attr( $commenter['comment_author'] ) . '"' . $aria_req . ' /></fieldset>',
		'email'  => '<fieldset><label for="email">' . __('Email') . ': '.$req_string.'</label><input type="text" class="text input-text" id="email" name="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '"' . $aria_req . ' /></fieldset>',
		'url'    => '<fieldset><label for="url">' . __('Website') . ':</label><input type="text" class="text input-text" id="url" name="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" /></fieldset>',
	);

	$required_text = sprintf( ' ' . __('Required fields are marked %s', CI_DOMAIN), '<span class="required">*</span>' );
	$defaults = array(
		'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
		'comment_field'        => '<fieldset><label for="comment">' . _x( 'Comment', 'noun' ) . ':</label><textarea cols="5" rows="5" id="comment" name="comment" aria-required="true"></textarea></fieldset>',
		'must_log_in'          => '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
		'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
		'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.' ) . ( $req ? $required_text : '' ) . '</p>',
		'comment_notes_after'  => '<p class="form-allowed-tags">' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s' ), ' <code>' . allowed_tags() . '</code>' ) . '</p>',
		'id_form'              => 'commentform',
		'id_submit'            => 'submit',
		'title_reply'          => __( 'Have your say', CI_DOMAIN),
		'title_reply_to'       => __( 'Reply to %s', CI_DOMAIN),
		'cancel_reply_link'    => __( 'Cancel reply', CI_DOMAIN),
		'label_submit'         => __( 'Submit Comment', CI_DOMAIN),
	);

	$args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );

	?>
		<?php if ( comments_open() ) : ?>
			<?php do_action( 'comment_form_before' ); ?>

				<h3 id="reply-title" class="comments"><?php comment_form_title( $args['title_reply'], $args['title_reply_to'] ); ?><small><?php cancel_comment_reply_link( $args['cancel_reply_link'] ); ?></small></h3>
				<div class="post-form innerbox">
					<?php if ( get_option( 'comment_registration' ) && !is_user_logged_in() ) : ?>
						<?php echo $args['must_log_in']; ?>
						<?php do_action( 'comment_form_must_log_in_after' ); ?>
					<?php else : ?>
						<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post" id="<?php echo esc_attr( $args['id_form'] ); ?>">
							<?php do_action( 'comment_form_top' ); ?>
							<?php if ( is_user_logged_in() ) : ?>
								<?php echo apply_filters( 'comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity ); ?>
								<?php do_action( 'comment_form_logged_in_after', $commenter, $user_identity ); ?>
							<?php else : ?>
								<?php echo $args['comment_notes_before']; ?>
								<?php
								do_action( 'comment_form_before_fields' );
								foreach ( (array) $args['fields'] as $name => $field ) {
									echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
								}
								do_action( 'comment_form_after_fields' );
								?>
							<?php endif; ?>
							<?php echo apply_filters( 'comment_form_field_comment', $args['comment_field'] ); ?>
							<?php echo $args['comment_notes_after']; ?>
							
							<fieldset>
								<input type="submit" class="button input-button" name="submit" id="<?php echo esc_attr( $args['id_submit'] ); ?>" value="<?php echo esc_attr( $args['label_submit'] ); ?>" />
								<?php comment_id_fields(); ?>
							</fieldset>
							
							<?php do_action( 'comment_form', $post_id ); ?>
						</form>
					<?php endif; ?>
				</div>
			<?php do_action( 'comment_form_after' ); ?>
		<?php else : ?>
			<?php do_action( 'comment_form_comments_closed' ); ?>
		<?php endif; ?>
	<?php
}

?>