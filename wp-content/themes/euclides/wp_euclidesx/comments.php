<?php
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die (__('Please do not load this page directly. Thanks!', CI_DOMAIN));
	if ( post_password_required() ) {
		echo '<p class="nocomments">' . _e('This post is password protected. Enter the password to view comments.', CI_DOMAIN) . '</p>';
		return;
	}
?>

<?php if (have_comments()): ?>
	<div class="post-comments shadow">
	<h2><?php comments_number(__('No comments', CI_DOMAIN), __('1 comment', CI_DOMAIN), __('% comments', CI_DOMAIN)); ?></h2>
		<ol id="comment-list">
			<?php wp_list_comments(array(
				'callback' => 'ci_comment'
			)); ?>
		</ol>
	</div><!-- .post-comments -->
<?php else: ?>
	<?php if(!comments_open()): ?>
		<div class="post-comments innerbox">
			<p><?php _e('Comments are closed.', CI_DOMAIN); ?></p>
		</div>
	<?php endif; ?>
<?php endif; ?>

<?php if(comments_open()): ?>
	<div id="respond">
		<div id="form-wrapper">
			<?php get_template_part('comment-form'); ?>
		</div><!-- #form-wrapper -->
	</div>
<?php endif; ?>


