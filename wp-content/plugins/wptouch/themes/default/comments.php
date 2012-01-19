<?php
// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>
		<p class="nocomments"><?php _e( "This post is password protected. Enter the password to view comments.", "wptouch" ); ?></p>
	<?php
		return;
	}
?>
<!-- You can start editing below here... but make a backup first!  -->

<div id="comment_wrapper">
<?php if ($comments) : ?>
<?php 
	echo '<h3 onclick="bnc_showhide_coms_toggle();" id="com-head"><img id="com-arrow" src="' . compat_get_plugin_url( 'wptouch' ) . '/themes/core/core-images/com_arrow.png" alt="arrow" />';
	$comment_string1 = __( 'No Responses Yet', 'wptouch' );
	$comment_string2 = __( '1 Response', 'wptouch' );
	$comment_string3 = __( '% Responses', 'wptouch' );
	comments_number( $comment_string1, $comment_string2, $comment_string3 );
	echo '</h3>';
?>
<?php endif; ?>

	<ol class="commentlist" id="commentlist">
		<?php if ($comments) : ?>
			<?php foreach ($comments as $comment) : ?>
				<?php if (get_comment_type() == "comment") { ?>
					<li class="<?php  echo $oddcomment; ?>" id="comment-<?php comment_ID(); ?>">
						<div class="comwrap">
								<div class="comtop<?php if ($comment->comment_approved == '0') : echo ' preview'; endif; ?>">		
									<?php if (bnc_is_gravatars_enabled()) { echo get_avatar( $comment, $size = '64', $default = '' . compat_get_plugin_url( 'wptouch' ) . '/themes/core/core-images/blank_gravatar.jpg' ); } ?>
									<div class="com-author"><?php comment_author_link(); ?></div> 	<?php if ($comment->comment_approved == '0') : echo '<span>(moderation preview)</span>'; endif; ?>
										<div class="comdater">
											<?php if (is_single()) { ?>
												<span><?php wptouch_moderate_comment_link(get_comment_ID()); ?></span>
											<?php } ?>
											<?php if (function_exists('wptouch_time_since')) { 
												echo wptouch_time_since(abs(strtotime($comment->comment_date_gmt . " GMT")), time()) . " " . __( 'ago', 'wptouch' ); } else { the_time('F jS, Y'); 
											} ?>	
										</div>									

								</div><!--end comtop-->
								<div class="combody">  
									<?php comment_text(); //delete_comment_link(get_comment_ID()); ?>
								</div>
						</div><!--end comwrap-->
					</li>
		
			<?php
				/* Changes every other comment to a different class */
				if ('alt' == $oddcomment) $oddcomment = '';  else $oddcomment = 'alt'; ?>
				<?php } ?>
				<?php endforeach;
				/* end for each comment */
			?>
	  </ol>

  <?php else : // this is displayed if there are no comments so far  ?>
  
	  <?php if ('open' == $post->comment_status) : ?>
		  <!-- If comments are open, but there are no comments. -->
		  <li id="hidelist" style="display:none"></li>
		  </ol>
	  
	  <?php else : // comments are closed  ?>
		  <!-- If comments are closed. -->
		  <li style="display:none"></li>
		  </ol>
		  <h3 class="result-text coms-closed"><?php _e( 'Comments are closed.', 'wptouch' ); ?></h3>
  
  	<?php endif; ?><!--end comment status-->
	<?php endif; ?>
 
  <div id="textinputwrap">
  	<?php if ('open' == $post->comment_status) : ?>
		<?php if (get_option('comment_registration') && !$user_ID) : ?>
			<center>
			<h1>
				<?php sprintf( __( 'You must %slogin</a> or %sregister</a> to comment', 'wptouch' ), '<a href="' . get_bloginfo('wpurl') . '/wp-login.php">', '<a href="' . get_bloginfo('wpurl') . '"/wp-register.php">') ; ?>
			</h1>
			</center>

	<?php else : ?>

		<div id="refresher" style="display:none;">
			<img src="<?php echo compat_get_plugin_url( 'wptouch' ); ?>/images/good.png" alt="checkmark" />
			<h3><?php _e( "Success! Comment added.", "wptouch" ); ?></h3>
			&rsaquo; <a href="javascript:this.location.reload();"><?php _e( "Refresh the page to see your comment.", "wptouch" ); ?></a><br />
				<?php _e( "(If your comment requires moderation it will be added soon.)", "wptouch" ); ?>
		</div>

		<form action="<?php bloginfo('wpurl'); ?>/wp-comments-post.php" method="post" id="commentform">

	<?php if ($user_ID) : ?>

		<p class="logged"  id="respond"><?php _e( "Logged in as", "wptouch" ); ?> <a href="<?php bloginfo('wpurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>:</p>
	
	<?php else : ?>
	
		<h3 id="respond"><?php _e( "Leave A Comment", "wptouch" ); ?></h3>
		<p>
			<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
			<label for="author"><?php _e( 'Name', 'wptouch' ); ?> <?php if ($req) echo "*"; ?></label>
		</p>

		<p>
			<input name="email" id="email" type="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
			<label for="email"><?php _e( 'Mail (unpublished)', 'wptouch' ); ?> <?php if ($req) echo "*"; ?></label>
		</p>
	
		<p>
			<input name="url" id="url" type="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
			<label for="url"><?php _e( 'Website', 'wptouch' ); ?></label>
		</p>

	<?php endif; ?>
			<div id="errors" style="display:none"><?php _e( "There was an error posting your comment. Maybe it was too short?", "wptouch" ); ?></div>

		<?php do_action('comment_form', $post->ID); ?>
		<p><textarea name="comment" id="comment" tabindex="4"></textarea></p>
		
		<p>
			<input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Publish', 'wptouch'); ?>	" />
			<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />		
			<div id="loading" style="display:none">
				<img src="<?php echo compat_get_plugin_url( 'wptouch' ); ?>/themes/core/core-images/comment-ajax-loader.gif" alt="" /> <p><?php _e( 'Publishing...', 'wptouch' ); ?></p>
			</div>
		</p>
		</form>

	<?php endif; // If registration required and not logged in ?>

  </div><!--textinputwrap div-->
</div><!-- comment_wrapper -->

<?php endif; // if you delete this the sky will fall on your head