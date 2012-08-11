<?php // Do not delete these lines
if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
die (__('Please do not load this page directly. Thanks!',TEMPLATE_DOMAIN));
if (!empty($post->post_password)) { // if there's a password
if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
?>
<h2><?php _e("This post is password protected. Enter the password to view comments.",TEMPLATE_DOMAIN); ?></h2>
<?php
return;
}
}

?>

<div id="comments-template">
<?php if ( have_comments() ) : ?>
<h4 id="comments"><?php comments_number(__('No Comments',TEMPLATE_DOMAIN), __('1 Comment',TEMPLATE_DOMAIN), __('% Comments',TEMPLATE_DOMAIN)); ?> <?php _e("in this post",TEMPLATE_DOMAIN); ?> &raquo;</h4>
<p>
<?php post_comments_feed_link(__('<abbr title="Really Simple Syndication">RSS</abbr> feed for comments on this post.',TEMPLATE_DOMAIN)); ?><?php if ( pings_open() ) : ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php trackback_url() ?>" rel="trackback"><?php _e('TrackBack <abbr title="Universal Resource Locator">URL</abbr>',TEMPLATE_DOMAIN); ?></a><?php endif; ?>
</p>

<?php do_action( 'bp_before_blog_comment_list' ) ?>

<?php if ( ! empty($comments_by_type['comment']) ) : ?>

<div id="post-navigator-single">
<div class="alignright"><?php if(function_exists('paginate_comments_links')) {  paginate_comments_links(); } ?></div>
</div>

<ul id="comments" class="commentlist">
<?php wp_list_comments('type=comment&callback=list_comments'); ?>
</ul>


<div id="post-navigator-single">
<div class="alignright"><?php if(function_exists('paginate_comments_links')) {  paginate_comments_links(); } ?></div>
</div>

<?php endif; ?>

<?php if ( $post->ping_status == "open" ) : ?>
<?php if ( ! empty($comments_by_type['pings']) ) : ?>

<h4 id="comments"><?php _e('Trackbacks/Pingbacks',TEMPLATE_DOMAIN); ?></h4>
<ul id="pingback">
<?php wp_list_comments('type=pings&callback=list_pings'); ?>
</ul>

<?php endif; ?>
<?php endif; ?>

<?php do_action( 'bp_after_blog_comment_list' ) ?>

<?php endif; //end if comments ?>


<?php if ('open' == $post->comment_status) : ?>

<?php if (get_option('comment_registration') && !$user_ID) : ?>

<p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.', TEMPLATE_DOMAIN), get_site_url() ."/wp-login.php?redirect_to=".urlencode(get_permalink()));?></p>

<?php else : ?>

<div id="respond">

<h4 id="respond"><?php _e('Leave a comment',TEMPLATE_DOMAIN); ?></h4>

<?php do_action( 'bp_before_blog_comment_form' ) ?>

<?php if(USE_NEW_COMMENT_FORM == 'yes') { ?>
<?php comment_form(); ?>
<?php } else { ?>

<form action="<?php echo site_url(); ?>/wp-comments-post.php" method="post" id="comment-form">

<div class="cancel-comment-reply"><?php cancel_comment_reply_link(); ?></div>

<?php if ( $user_ID ) : ?>

<p><?php _e('Logged in as',TEMPLATE_DOMAIN); ?> <a href="<?php echo site_url(); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account',TEMPLATE_DOMAIN); ?>"><?php _e('Log out &raquo;',TEMPLATE_DOMAIN); ?></a></p>

<?php else : ?>

<label><?php _e('Name',TEMPLATE_DOMAIN); ?> <span><?php if ($req) _e('(required)',TEMPLATE_DOMAIN); ?></span></label>
<p><input name="author" type="text" class="inbox" value="<?php echo $comment_author; ?>"/></p>

<label><?php _e('Mail (will not be published)',TEMPLATE_DOMAIN);?> <span><?php if ($req) _e('(required)',TEMPLATE_DOMAIN); ?></span></label>
<p><input name="email" type="text" class="inbox" value="<?php echo $comment_author_email; ?>"/></p>

<label><?php _e('Website',TEMPLATE_DOMAIN); ?></label>
<p><input name="url" type="text" class="inbox" value="<?php echo $comment_author_url; ?>"/></p>

<?php endif; ?>

<p><textarea name="comment" cols="50%" id="comment" rows="8" class="inarea"></textarea></p>

<p>
<input name="Submit" type="submit" class="sbutton" value="<?php echo esc_attr(__('Submit Comment',TEMPLATE_DOMAIN)); ?> &raquo;" />
<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
</p>

<?php if(function_exists("comment_id_fields")) { ?>
<?php comment_id_fields(); ?>
<?php } ?>
<?php do_action('comment_form', $post->ID); ?>

</form>
<?php } ?>

<?php do_action( 'bp_after_blog_comment_form' ) ?>

</div>

<?php endif; ?>

<?php endif; ?>

</div>